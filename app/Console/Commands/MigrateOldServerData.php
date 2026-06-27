<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDO;

/**
 * استيراد بيانات قاعدة البيانات القديمة من السيرفر إلى بنية الإصدار الجديد.
 *
 * الاستخدام:
 *   php artisan migrate-old-server-data /path/to/anagmgxt_elitevape.sql
 *
 * يتطلب: MySQL/MariaDB يعمل محلياً، وبيانات الاتصال من .env
 */
class MigrateOldServerData extends Command
{
    protected $signature = 'migrate-old-server-data
                            {sql_file : مسار ملف SQL القديم (مثلاً ~/Downloads/anagmgxt_elitevape.sql)}
                            {--output= : مسار ملف الإخراج (افتراضي: elitevape_database_import.sql)}';

    protected $description = 'استيراد بيانات قاعدة البيانات القديمة من السيرفر إلى بنية الإصدار الجديد';

    private string $oldDb = 'elitevape_old_temp';
    private string $newDb = 'elitevape_new_temp';

    /** ترتيب النسخ حسب التبعيات (بدون FKs) */
    private array $copyOrder = [
        'admins', 'attributes', 'branches', 'business_settings', 'categories',
        'currencies', 'user_types', 'failed_jobs', 'users', 'password_resets',
        'email_verifications', 'phone_verifications', 'guest_users', 'login_setups',
        'conversations', 'coupons', 'flash_sales', 'notifications', 'social_medias',
        'translations', 'shipping_companies',
        'delivery_charge_setups', 'delivery_charge_by_areas', 'areas', 'cities',
        'customer_addresses', 'products', 'orders', 'order_details', 'flash_sale_products',
        'order_areas', 'order_shipments', 'product_user_type_discounts', 'product_user_type_prices',
        'loyalty_points', 'loyalty_point_logs', 'messages', 'reviews', 'wishlists',
        'oauth_auth_codes', 'oauth_access_tokens', 'oauth_refresh_tokens', 'oauth_clients',
        'oauth_personal_access_clients', 'contact_us', 'banners',
    ];

    public function handle(): int
    {
        $sqlFile = $this->argument('sql_file');
        $outputFile = $this->option('output') ?: base_path('elitevape_database_import.sql');

        if (! is_file($sqlFile)) {
            $this->error("الملف غير موجود: {$sqlFile}");
            return 1;
        }

        $host = config('database.connections.mysql.host', 'localhost');
        $user = config('database.connections.mysql.username', 'root');
        $pass = config('database.connections.mysql.password', '');
        $charset = config('database.connections.mysql.charset', 'utf8mb4');

        $dsn = "mysql:host={$host};charset={$charset}";
        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (\Throwable $e) {
            $this->error('فشل الاتصال بـ MySQL: ' . $e->getMessage());
            return 1;
        }

        $this->info('1. إنشاء قاعدة البيانات المؤقتة للقديم...');
        $pdo->exec("DROP DATABASE IF EXISTS `{$this->oldDb}`");
        $pdo->exec("CREATE DATABASE `{$this->oldDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        $this->info('2. استيراد ملف SQL القديم...');
        $sql = file_get_contents($sqlFile);
        $sql = preg_replace('/^CREATE DATABASE.*?;/m', '', $sql);
        $sql = preg_replace('/^USE `[^`]+`;/m', "USE `{$this->oldDb}`;", $sql, 1);
        $pdo->exec("USE `{$this->oldDb}`");
        $pdo->exec($sql);

        $this->info('3. إنشاء قاعدة البيانات الجديدة وتشغيل migrations...');
        $pdo->exec("DROP DATABASE IF EXISTS `{$this->newDb}`");
        $pdo->exec("CREATE DATABASE `{$this->newDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        config(['database.connections.mysql_temp.database' => $this->newDb]);

        $this->call('migrate', [
            '--database' => 'mysql_temp',
            '--force' => true,
        ]);

        $this->info('4. تفريغ الجداول ونسخ البيانات من القديم...');
        $pdo->exec("USE `{$this->newDb}`");
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

        foreach (array_reverse($this->copyOrder) as $table) {
            if ($table === 'migrations') {
                continue;
            }
            try {
                $pdo->exec("USE `{$this->newDb}`");
                $pdo->exec("TRUNCATE TABLE `{$table}`");
            } catch (\Throwable $e) {
                // جدول غير موجود
            }
        }

        foreach ($this->copyOrder as $table) {
            $this->copyTable($pdo, $table);
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

        $this->info('5. تطبيق restaurant_name → store_name');
        $pdo->exec("UPDATE `{$this->newDb}`.business_settings SET `key` = 'store_name' WHERE `key` = 'restaurant_name'");

        $this->info('6. تصدير قاعدة البيانات النهائية...');
        $cmd = sprintf(
            'mysqldump -h %s -u %s %s --no-tablespaces --single-transaction --routines --triggers --set-gtid-purged=OFF %s 2>/dev/null',
            escapeshellarg($host),
            escapeshellarg($user),
            $pass ? '-p' . escapeshellarg($pass) : '',
            escapeshellarg($this->newDb)
        );
        $dump = shell_exec($cmd);
        if (! $dump) {
            $this->error('فشل mysqldump');
            return 1;
        }

        $dump = $this->stripPrivilegeRequiringLines($dump);

        $header = "CREATE DATABASE IF NOT EXISTS `anagmgxt_elitevape` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\nUSE `anagmgxt_elitevape`;\n\n";
        file_put_contents($outputFile, $header . $dump);

        $this->info('7. تنظيف قواعد البيانات المؤقتة...');
        $pdo->exec("DROP DATABASE IF EXISTS `{$this->oldDb}`");
        $pdo->exec("DROP DATABASE IF EXISTS `{$this->newDb}`");

        $this->info("تم استيراد البيانات بنجاح. الملف: {$outputFile}");
        return 0;
    }

    private function copyTable(PDO $pdo, string $table): void
    {
        try {
            $pdo->exec("USE `{$this->oldDb}`");
            $tables = $pdo->query("SHOW TABLES LIKE '{$table}'")->fetchAll();
            if (empty($tables)) {
                return;
            }

            $stmt = $pdo->query("SELECT COUNT(*) FROM `{$table}`");
            $count = (int) $stmt->fetchColumn();
            if ($count === 0) {
                $pdo->exec("USE `{$this->newDb}`");
                return;
            }

            $cols = $pdo->query("SHOW COLUMNS FROM `{$table}`")->fetchAll(PDO::FETCH_COLUMN);

            $pdo->exec("USE `{$this->newDb}`");
            $newTables = $pdo->query("SHOW TABLES LIKE '{$table}'")->fetchAll();
            if (empty($newTables)) {
                return;
            }
            $newCols = $pdo->query("SHOW COLUMNS FROM `{$table}`")->fetchAll(PDO::FETCH_COLUMN);

            $common = array_intersect($cols, $newCols);
            if (empty($common)) {
                return;
            }

            if ($table === 'orders' && in_array('delivery_man_id', $cols) && ! in_array('delivery_man_id', $newCols)) {
                $common = array_values(array_diff($common, ['delivery_man_id']));
            }

            $colList = implode('`, `', $common);
            $pdo->exec("USE `{$this->oldDb}`");
            $rows = $pdo->query("SELECT `{$colList}` FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);

            $pdo->exec("USE `{$this->newDb}`");
            if (empty($rows)) {
                return;
            }

            $placeholders = '(' . implode(', ', array_fill(0, count($common), '?')) . ')';
            $sql = "INSERT IGNORE INTO `{$table}` (`{$colList}`) VALUES {$placeholders}";
            $stmt = $pdo->prepare($sql);

            foreach ($rows as $row) {
                $vals = array_values($row);
                $stmt->execute($vals);
            }

            $this->line("  - {$table}: " . count($rows) . " سجل");
        } catch (\Throwable $e) {
            $this->warn("  تحذير {$table}: " . $e->getMessage());
        }
    }

    /**
     * إزالة أسطر mysqldump التي تتطلب صلاحيات خاصة (BINLOG ADMIN, GTID)
     * لتجنب خطأ #1227 عند الاستيراد في phpMyAdmin / cPanel
     */
    private function stripPrivilegeRequiringLines(string $dump): string
    {
        $lines = explode("\n", $dump);
        $filtered = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (str_contains($trimmed, 'SQL_LOG_BIN') || str_contains($trimmed, 'MYSQLDUMP_TEMP_LOG_BIN')) {
                continue;
            }
            if (preg_match('/^SET @@GLOBAL\.GTID_PURGED/', $trimmed)) {
                continue;
            }
            $filtered[] = $line;
        }
        return implode("\n", $filtered);
    }
}
