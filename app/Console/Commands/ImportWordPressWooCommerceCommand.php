<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

/**
 * Imports essential WooCommerce data (categories, products, customers, orders) from a WordPress dump
 * into this Laravel store. Does not import plugin/CMS tables — only reads them from a temp MySQL database.
 */
class ImportWordPressWooCommerceCommand extends Command
{
    protected $signature = 'import:wordpress-woocommerce
                            {--sql= : Absolute path to WordPress SQL dump (e.g. baitpait_anagheemdb.sql)}
                            {--uploads= : Absolute path to wp-content/uploads folder}
                            {--wipe : Remove existing categories, products, orders, and customer data before import}
                            {--import-sql : Create/replace WP_IMPORT_DATABASE and load --sql into it via mysql CLI}';

    protected $description = 'Import WooCommerce categories, products, images, customers, and orders from WordPress SQL + uploads folder';

    private string $pfx = 'wpuj_';

    public function handle(): int
    {
        $sqlPath = $this->option('sql');
        $uploadsPath = $this->option('uploads');
        if (!$sqlPath || !is_readable($sqlPath)) {
            $this->error('Provide readable --sql= path to the WordPress dump.');
            return self::FAILURE;
        }
        if (!$uploadsPath || !is_dir($uploadsPath)) {
            $this->error('Provide existing --uploads= path (WordPress uploads directory).');
            return self::FAILURE;
        }
        $uploadsPath = rtrim($uploadsPath, '/');

        if ($this->option('import-sql')) {
            $this->importSqlFile($sqlPath);
        }

        try {
            DB::connection('wordpress_import')->getPdo();
        } catch (\Throwable $e) {
            $this->error('Cannot connect to wordpress_import. Set WP_IMPORT_DATABASE in .env and import the SQL (or use --import-sql).');
            $this->line($e->getMessage());
            return self::FAILURE;
        }

        $wp = DB::connection('wordpress_import');

        if (!$this->option('wipe')) {
            if (DB::table('products')->exists() || DB::table('categories')->where('id', '>', 0)->exists()) {
                $this->error('Target DB already has products/categories. Re-run with --wipe to replace, or empty manually.');
                return self::FAILURE;
            }
        } else {
            $this->wipeCommerceData();
        }

        $this->ensureUserTypes();

        $branchId = (int) (Branch::query()->value('id') ?? 1);

        $this->info('Importing categories…');
        $termToCategory = $this->importCategories($wp, $uploadsPath);

        $this->info('Importing products…');
        $wpProductToLocal = $this->importProducts($wp, $uploadsPath, $termToCategory);

        $this->info('Importing customers (from order billing)…');
        $emailToUserId = $this->importCustomersFromOrders($wp);

        $this->info('Importing orders…');
        $this->importOrders($wp, $wpProductToLocal, $emailToUserId, $branchId);

        $this->info('Done. Admin and branch rows were kept; only store/catalog/order data was imported.');

        return self::SUCCESS;
    }

    private function importSqlFile(string $sqlPath): void
    {
        $db = config('database.connections.wordpress_import.database');
        $host = config('database.connections.wordpress_import.host');
        $port = (string) config('database.connections.wordpress_import.port');
        $user = config('database.connections.wordpress_import.username');
        $pass = (string) config('database.connections.wordpress_import.password');

        $this->info("Creating database `{$db}` if needed…");
        $create = new Process(
            array_values(array_filter([
                'mysql',
                '-h' . $host,
                '-P' . $port,
                '-u' . $user,
                $pass !== '' ? '-p' . $pass : null,
                '-e',
                'CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', $db) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;',
            ]))
        );
        $create->setTimeout(300);
        $create->run();
        if (!$create->isSuccessful()) {
            $this->error($create->getErrorOutput() . $create->getOutput());
            throw new \RuntimeException('mysql create database failed');
        }

        $this->info('Loading SQL (this may take a while)…');
        $import = new Process(
            array_values(array_filter([
                'mysql',
                '-h' . $host,
                '-P' . $port,
                '-u' . $user,
                $pass !== '' ? '-p' . $pass : null,
                $db,
            ]))
        );
        $import->setTimeout(3600);
        $import->setInput(fopen($sqlPath, 'r'));
        $import->run();
        if (!$import->isSuccessful()) {
            $this->error($import->getErrorOutput() . $import->getOutput());
            throw new \RuntimeException('mysql import failed');
        }
        $this->info('SQL loaded.');
    }

    private function wipeCommerceData(): void
    {
        Schema::disableForeignKeyConstraints();
        try {
            $tables = [
                'order_status_logs',
                'order_shipments',
                'order_areas',
                'order_details',
                'orders',
                'reviews',
                'wishlists',
                'product_tag',
                'product_relations',
                'flash_sale_products',
                'product_user_type_prices',
                'product_user_type_discounts',
                'loyalty_point_logs',
                'loyalty_points',
                'customer_addresses',
                'messages',
                'conversations',
                'products',
                'categories',
            ];
            foreach ($tables as $t) {
                if (Schema::hasTable($t)) {
                    DB::table($t)->truncate();
                }
            }
            DB::table('translations')->whereIn('translationable_type', [
                Category::class,
                Product::class,
            ])->delete();
            DB::table('users')->truncate();
            DB::table('guest_users')->truncate();
        } finally {
            Schema::enableForeignKeyConstraints();
        }
        $this->warn('Wiped products, categories, orders, customers, and related rows.');
    }

    private function ensureUserTypes(): void
    {
        if (DB::table('user_types')->exists()) {
            return;
        }
        $now = now();
        DB::table('user_types')->insert([
            ['name' => 'عميل عادي', 'is_default' => true, 'position' => 0, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    /**
     * @return array<int,int> WordPress term_id => Laravel category id
     */
    private function importCategories($wp, string $uploadsRoot): array
    {
        $rows = $wp->table($this->pfx . 'terms as t')
            ->join($this->pfx . 'term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
            ->where('tt.taxonomy', 'product_cat')
            ->select(['t.term_id', 't.name', 't.slug', 'tt.parent'])
            ->get();

        $byId = [];
        foreach ($rows as $row) {
            $byId[(int) $row->term_id] = $row;
        }
        $map = [];
        $position = 0;
        $safety = count($byId) + 5;
        while ($byId !== [] && $safety-- > 0) {
            $insertedThisRound = false;
            foreach ($byId as $tid => $row) {
                $parentWp = (int) $row->parent;
                if ($parentWp !== 0 && !isset($map[$parentWp])) {
                    continue;
                }
                $parentLaravel = $parentWp === 0 ? 0 : $map[$parentWp];

                $thumbId = $wp->table($this->pfx . 'termmeta')
                    ->where('term_id', $row->term_id)
                    ->where('meta_key', 'thumbnail_id')
                    ->value('meta_value');
                $imageName = 'def.png';
                if ($thumbId) {
                    $copied = $this->copyAttachmentToDisk($wp, (int) $thumbId, $uploadsRoot, 'category');
                    if ($copied) {
                        $imageName = $copied;
                    }
                }

                $id = DB::table('categories')->insertGetId([
                    'name' => $this->clip($row->name, 255),
                    'parent_id' => $parentLaravel,
                    'position' => $position++,
                    'status' => 1,
                    'image' => $imageName,
                    'banner_image' => null,
                    'is_featured' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $map[$tid] = $id;
                unset($byId[$tid]);
                $insertedThisRound = true;
            }
            if (!$insertedThisRound) {
                $this->warn('Some product categories were skipped (broken parent chain).');
                break;
            }
        }

        return $map;
    }

    /**
     * @param array<int,int> $termToCategory
     * @return array<int,int> WordPress product post ID => Laravel product id
     */
    private function importProducts($wp, string $uploadsRoot, array $termToCategory): array
    {
        $posts = $wp->table($this->pfx . 'posts')
            ->where('post_type', 'product')
            ->whereIn('post_status', ['publish', 'private'])
            ->orderBy('ID')
            ->get(['ID', 'post_title', 'post_content', 'post_date']);

        $ids = $posts->pluck('ID')->all();
        if ($ids === []) {
            return [];
        }

        $metaRows = $wp->table($this->pfx . 'postmeta')->whereIn('post_id', $ids)->get(['post_id', 'meta_key', 'meta_value']);
        $metaByPost = [];
        foreach ($metaRows as $m) {
            $metaByPost[(int) $m->post_id][$m->meta_key] = $m->meta_value;
        }

        $relRows = $wp->table($this->pfx . 'term_relationships as tr')
            ->join($this->pfx . 'term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
            ->where('tt.taxonomy', 'product_cat')
            ->whereIn('tr.object_id', $ids)
            ->get(['tr.object_id', 'tt.term_id']);

        $catsByProduct = [];
        foreach ($relRows as $r) {
            $catsByProduct[(int) $r->object_id][] = (int) $r->term_id;
        }

        $map = [];
        foreach ($posts as $post) {
            $pid = (int) $post->ID;
            $meta = $metaByPost[$pid] ?? [];
            $type = $meta['_product_type'] ?? 'simple';
            if ($type !== 'simple') {
                $this->line("Skip non-simple product #{$pid} (type={$type})");
                continue;
            }

            $regular = isset($meta['_regular_price']) ? (float) $meta['_regular_price'] : 0.0;
            $sale = isset($meta['_sale_price']) && $meta['_sale_price'] !== '' ? (float) $meta['_sale_price'] : null;
            $price = $sale !== null && $sale > 0 ? $sale : ($regular > 0 ? $regular : (float) ($meta['_price'] ?? 0));

            $discount = 0.0;
            $discountType = 'percent';
            if ($sale !== null && $regular > 0 && $sale < $regular) {
                $discount = round((($regular - $sale) / $regular) * 100, 2);
                $discountType = 'percent';
            }

            $manageStock = ($meta['_manage_stock'] ?? 'no') === 'yes';
            $stockQty = isset($meta['_stock']) && $meta['_stock'] !== '' ? (int) $meta['_stock'] : 0;
            $stockStatus = $meta['_stock_status'] ?? 'instock';
            if (!$manageStock) {
                $totalStock = $stockStatus === 'outofstock' ? 0 : 9999;
            } else {
                $totalStock = max(0, $stockQty);
            }

            $images = [];
            $thumbId = isset($meta['_thumbnail_id']) ? (int) $meta['_thumbnail_id'] : 0;
            if ($thumbId) {
                $fn = $this->copyAttachmentToDisk($wp, $thumbId, $uploadsRoot, 'product');
                if ($fn) {
                    $images[] = $fn;
                }
            }
            if (!empty($meta['_product_image_gallery'])) {
                foreach (array_filter(array_map('intval', explode(',', $meta['_product_image_gallery']))) as $aid) {
                    if ($aid === $thumbId) {
                        continue;
                    }
                    $fn = $this->copyAttachmentToDisk($wp, $aid, $uploadsRoot, 'product');
                    if ($fn) {
                        $images[] = $fn;
                    }
                }
            }

            $categoryIdsJson = '[]';
            if (!empty($catsByProduct[$pid])) {
                $pairs = [];
                $pos = 1;
                foreach ($catsByProduct[$pid] as $termId) {
                    if (!isset($termToCategory[$termId])) {
                        continue;
                    }
                    $pairs[] = ['id' => (string) $termToCategory[$termId], 'position' => $pos++];
                }
                if ($pairs !== []) {
                    $categoryIdsJson = json_encode($pairs, JSON_UNESCAPED_UNICODE);
                }
            }

            $name = $this->clip($post->post_title, 255) ?: 'Product ' . $pid;
            $desc = (string) $post->post_content;

            $newId = DB::table('products')->insertGetId([
                'name' => $name,
                'description' => $desc,
                'image' => json_encode($images, JSON_UNESCAPED_UNICODE),
                'price' => $price,
                'variations' => '[]',
                'tax' => 0,
                'status' => 1,
                'attributes' => '[]',
                'category_ids' => $categoryIdsJson,
                'choice_options' => '[]',
                'discount' => $discount,
                'discount_type' => $discountType,
                'tax_type' => 'percent',
                'unit' => 'pc',
                'total_stock' => $totalStock,
                'min_order_qty' => 1,
                'minimum_stock_alert' => null,
                'created_at' => $post->post_date ?: now(),
                'updated_at' => now(),
            ]);
            $map[$pid] = $newId;
        }

        return $map;
    }

    /**
     * @return array<string,int>
     */
    private function importCustomersFromOrders($wp): array
    {
        $defaultType = DB::table('user_types')->where('is_default', true)->value('id')
            ?? DB::table('user_types')->value('id');

        $emails = $wp->table($this->pfx . 'wc_order_addresses')
            ->where('address_type', 'billing')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->distinct()
            ->pluck('email');

        $map = [];
        $placeholder = bcrypt(Str::random(40));
        foreach ($emails as $email) {
            $email = strtolower(trim((string) $email));
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $billing = $wp->table($this->pfx . 'wc_order_addresses')
                ->where('address_type', 'billing')
                ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
                ->orderByDesc('id')
                ->first();

            $fn = $this->clip($billing->first_name ?? '', 100);
            $ln = $this->clip($billing->last_name ?? '', 100);
            if ($fn === '' && $ln === '') {
                $fn = strstr($email, '@', true) ?: 'Customer';
            }

            $uid = DB::table('users')->insertGetId([
                'f_name' => $fn,
                'l_name' => $ln,
                'email' => $this->clip($email, 100),
                'image' => null,
                'is_phone_verified' => false,
                'email_verified_at' => null,
                'password' => $placeholder,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'email_verification_token' => null,
                'phone' => $this->clip(preg_replace('/\s+/', '', (string) ($billing->phone ?? '')), 255),
                'cm_firebase_token' => null,
                'temporary_token' => null,
                'login_hit_count' => 0,
                'is_temp_blocked' => false,
                'temp_block_time' => null,
                'login_medium' => 'general',
                'user_type_id' => $defaultType,
                'requested_user_type_id' => null,
            ]);
            $map[$email] = $uid;
        }

        return $map;
    }

    /**
     * @param array<int,int> $wpProductToLocal
     * @param array<string,int> $emailToUserId
     */
    private function importOrders($wp, array $wpProductToLocal, array $emailToUserId, int $branchId): void
    {
        $orders = $wp->table($this->pfx . 'wc_orders')
            ->where('type', 'shop_order')
            ->orderBy('id')
            ->get();

        foreach ($orders as $o) {
            $oid = (int) $o->id;
            $billing = $wp->table($this->pfx . 'wc_order_addresses')
                ->where('order_id', $oid)
                ->where('address_type', 'billing')
                ->first();

            $email = $billing && $billing->email
                ? strtolower(trim((string) $billing->email))
                : '';
            $userId = ($email !== '' && isset($emailToUserId[$email])) ? $emailToUserId[$email] : null;
            $isGuest = $userId === null ? 1 : 0;

            $laravelStatus = $this->mapWcOrderStatus((string) $o->status);
            $paymentStatus = in_array($laravelStatus, ['delivered', 'canceled'], true)
                ? ($laravelStatus === 'canceled' ? 'unpaid' : 'paid')
                : 'unpaid';
            if ($laravelStatus === 'delivered' && ($o->payment_method ?? '') === 'cod') {
                $paymentStatus = 'paid';
            }

            $deliveryAddress = $this->buildDeliveryAddressPayload($billing);

            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $userId,
                'is_guest' => $isGuest,
                'order_amount' => (float) $o->total_amount,
                'coupon_discount_amount' => 0,
                'coupon_discount_title' => null,
                'payment_status' => $paymentStatus,
                'order_status' => $laravelStatus,
                'total_tax_amount' => (float) ($o->tax_amount ?? 0),
                'payment_method' => $this->mapPaymentMethod((string) ($o->payment_method ?? '')),
                'transaction_reference' => 'wc-' . $oid,
                'delivery_address_id' => null,
                'created_at' => $o->date_created_gmt ?? now(),
                'updated_at' => $o->date_updated_gmt ?? now(),
                'checked' => 0,
                'delivery_charge' => 0,
                'order_note' => $o->customer_note ?? null,
                'coupon_code' => null,
                'order_type' => 'delivery',
                'branch_id' => $branchId,
                'callback' => null,
                'extra_discount' => 0,
                'delivery_address' => $deliveryAddress ? json_encode($deliveryAddress, JSON_UNESCAPED_UNICODE) : null,
                'bring_change_amount' => 0,
                'paid_amount' => $paymentStatus === 'paid' ? (float) $o->total_amount : 0,
                'loyalty_points_used' => 0,
                'loyalty_discount_amount' => 0,
                'additional_payment_method' => null,
            ]);

            $items = $wp->table($this->pfx . 'woocommerce_order_items')
                ->where('order_id', $oid)
                ->where('order_item_type', 'line_item')
                ->get();

            foreach ($items as $item) {
                $itemId = (int) $item->order_item_id;
                $im = $wp->table($this->pfx . 'woocommerce_order_itemmeta')
                    ->where('order_item_id', $itemId)
                    ->pluck('meta_value', 'meta_key');

                $wpPid = isset($im['_product_id']) ? (int) $im['_product_id'] : 0;
                $qty = isset($im['_qty']) ? max(1, (int) $im['_qty']) : 1;
                $lineTotal = isset($im['_line_total']) ? (float) $im['_line_total'] : 0;
                $unitPrice = $qty > 0 ? round($lineTotal / $qty, 2) : $lineTotal;

                $localPid = $wpProductToLocal[$wpPid] ?? null;
                if ($localPid === null) {
                    $this->line("Order wc-{$oid}: skip line item (unknown product wp #{$wpPid})");
                    continue;
                }

                $productRow = Product::withoutGlobalScopes()->find($localPid);
                $snapshot = $productRow ? $productRow->getAttributes() : [];
                if ($snapshot !== []) {
                    unset($snapshot['translations']);
                }

                DB::table('order_details')->insert([
                    'product_id' => $localPid,
                    'order_id' => $orderId,
                    'price' => $unitPrice,
                    'product_details' => json_encode($snapshot, JSON_UNESCAPED_UNICODE),
                    'variation' => '[]',
                    'discount_on_product' => 0,
                    'discount_type' => 'amount',
                    'quantity' => $qty,
                    'tax_amount' => 0,
                    'variant' => '',
                    'unit' => 'pc',
                    'is_stock_decreased' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function mapWcOrderStatus(string $wc): string
    {
        return match ($wc) {
            'wc-pending' => 'pending',
            'wc-processing' => 'processing',
            'wc-on-hold' => 'confirmed',
            'wc-completed' => 'delivered',
            'wc-cancelled' => 'canceled',
            'wc-refunded' => 'returned',
            'wc-failed' => 'failed',
            default => 'pending',
        };
    }

    private function mapPaymentMethod(string $wc): string
    {
        if ($wc === 'cod') {
            return 'cash_on_delivery';
        }
        return $wc !== '' ? $this->clip($wc, 30) : 'cash_on_delivery';
    }

    private function buildDeliveryAddressPayload(?object $billing): ?array
    {
        if (!$billing) {
            return null;
        }
        $name = trim(($billing->first_name ?? '') . ' ' . ($billing->last_name ?? ''));
        $addr = trim((string) ($billing->address_1 ?? ''));
        if ($name === '' && $addr === '' && empty($billing->phone)) {
            return null;
        }
        return [
            'contact_person_name' => $this->clip($name, 100),
            'address' => $this->clip($addr, 250),
            'floor' => $this->clip((string) ($billing->address_2 ?? ''), 10),
            'road' => '',
            'house' => '',
            'phone' => $this->clip(preg_replace('/\s+/', '', (string) ($billing->phone ?? '')), 20),
            'city' => $this->clip((string) ($billing->city ?? ''), 100),
        ];
    }

    private function copyAttachmentToDisk($wp, int $attachmentId, string $uploadsRoot, string $diskSubdir): ?string
    {
        $post = $wp->table($this->pfx . 'posts')
            ->where('ID', $attachmentId)
            ->where('post_type', 'attachment')
            ->first();
        if (!$post) {
            return null;
        }
        $rel = $wp->table($this->pfx . 'postmeta')
            ->where('post_id', $attachmentId)
            ->where('meta_key', '_wp_attached_file')
            ->value('meta_value');
        if (!$rel) {
            return null;
        }
        $rel = ltrim(str_replace('\\', '/', (string) $rel), '/');
        $src = $uploadsRoot . '/' . $rel;
        if (!is_readable($src) && !empty($post->guid)) {
            $path = parse_url((string) $post->guid, PHP_URL_PATH);
            if (is_string($path) && preg_match('#/(?:wp-content/)?uploads/(.+)$#i', $path, $m)) {
                $try = $uploadsRoot . '/' . ltrim(str_replace('\\', '/', $m[1]), '/');
                if (is_readable($try)) {
                    $src = $try;
                }
            }
        }
        if (!is_readable($src)) {
            return null;
        }
        $ext = pathinfo($rel, PATHINFO_EXTENSION) ?: 'jpg';
        $destName = date('Y-m-d') . '-' . Str::lower(Str::random(10)) . '.' . $ext;
        $destPath = $diskSubdir . '/' . $destName;
        try {
            Storage::disk('public')->put($destPath, file_get_contents($src));
        } catch (\Throwable) {
            return null;
        }
        return $destName;
    }

    private function clip(?string $s, int $max): string
    {
        $s = (string) $s;
        if (function_exists('mb_substr')) {
            return mb_substr($s, 0, $max);
        }
        return substr($s, 0, $max);
    }
}
