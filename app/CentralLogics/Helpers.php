<?php

namespace App\CentralLogics;

use App\Models\Branch;
use App\Models\BusinessSetting;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Review;
use App\Models\Area;
use App\Models\DeliveryChargeByArea;
use App\Models\LoginSetup;
use App\Models\Product;
use App\Models\ProductUserTypeDiscount;
use App\Models\ProductUserTypePrice;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class Helpers
{
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            $err_keeper[] = ['code' => $index, 'message' => $error[0]];
        }
        return $err_keeper;
    }

    /**
     * Cap API limit to prevent DoS (max 50 per request).
     */
    public static function capApiLimit($limit, int $max = 50): int
    {
        $limit = (int)($limit ?? 10);
        return min(max(1, $limit), $max);
    }

    /**
     * Cap API offset (minimum 1).
     */
    public static function capApiOffset($offset): int
    {
        return max(1, (int)($offset ?? 1));
    }

    /**
     * Generate OTP token (123456 in local env for testing, random otherwise).
     */
    public static function generateOtpToken(): int
    {
        return (config('app.env') === 'local') ? 123456 : rand(100000, 999999);
    }

    /**
     * Sanitize HTML for safe display (XSS prevention).
     * Allows safe formatting tags; strips scripts and dangerous attributes.
     */
    public static function sanitizeHtmlForDisplay(?string $html): string
    {
        if (empty($html)) {
            return '';
        }
        $allowedTags = '<p><br><strong><b><em><i><ul><ol><li><h1><h2><h3><h4><span><div>';
        $html = strip_tags($html, $allowedTags);
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        $html = preg_replace('/javascript:/i', '', $html);
        return $html;
    }

    /**
     * يبسط رابط خرائط جوجل المنسوخ من المتصفح: يزيل ?query و #fragment من روابط
     * الصيغة /@lat,lng,zoom (مثل entry=ttu و g_ep=) دون تغيير الموقع على الخريطة.
     * روابط أخرى (مكان، مشاركة قصيرة، نطاقات غير مدرجة) تُعاد كما هي بعد trim.
     */
    public static function normalizeStoreGoogleMapsUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }
        $parts = parse_url($url);
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return $url;
        }
        $scheme = strtolower((string) $parts['scheme']);
        if ($scheme !== 'http' && $scheme !== 'https') {
            return $url;
        }
        $host = strtolower((string) $parts['host']);
        $allowedHosts = ['www.google.com', 'google.com', 'maps.google.com'];
        if (! in_array($host, $allowedHosts, true)) {
            return $url;
        }
        $path = $parts['path'] ?? '';
        if (! str_contains($path, '/maps/@')) {
            return $url;
        }
        $port = isset($parts['port']) ? ':' . (int) $parts['port'] : '';

        return $scheme . '://' . $host . $port . $path;
    }

    /**
     * رابط iframe لمعاينة الموقع داخل لوحة التحكم من رابط المتجر (@lat,lng,z).
     * لا يُستخدم رابط Google embed: جوجل تعيد 404 وتضع SAMEORIGIN فيتعطل الإطار من نطاق آخر.
     * تُعرض نفس الإحداثيات عبر OpenStreetMap (معاينة فقط؛ رابط العملاء يبقى Google كما حُفظ).
     */
    public static function googleMapsStoreUrlToEmbedSrc(?string $url): ?string
    {
        if ($url === null || trim($url) === '') {
            return null;
        }
        $normalized = self::normalizeStoreGoogleMapsUrl(trim($url));
        if (! preg_match('#@([-0-9.]+),([-0-9.]+),([0-9]+(?:\.[0-9]+)?)z#i', $normalized, $m)) {
            return null;
        }
        $lat = (float) $m[1];
        $lng = (float) $m[2];
        $zoom = (int) round((float) $m[3]);
        if ($lat < -90.0 || $lat > 90.0 || $lng < -180.0 || $lng > 180.0) {
            return null;
        }
        $zoom = max(1, min(21, $zoom));

        return self::openStreetMapPreviewEmbedFromLatLngZoom($lat, $lng, $zoom);
    }

    /**
     * معاينة OSM: bbox حول النقطة (min_lon,min_lat,max_lon,max_lat) + marker.
     */
    public static function openStreetMapPreviewEmbedFromLatLngZoom(float $lat, float $lng, int $zoom): string
    {
        $latRad = deg2rad($lat);
        $halfWidthM = 280.0;
        $halfHeightM = 220.0;
        $dLat = $halfHeightM / 111320.0;
        $cosLat = cos($latRad);
        $dLng = $cosLat > 0.01 ? ($halfWidthM / (111320.0 * $cosLat)) : 0.2;
        $factor = max(0.35, min(2.5, 18 / max(1, $zoom)));
        $dLat *= $factor;
        $dLng *= $factor;
        $minLat = max(-85.0, $lat - $dLat);
        $maxLat = min(85.0, $lat + $dLat);
        $minLng = max(-180.0, $lng - $dLng);
        $maxLng = min(180.0, $lng + $dLng);
        $bbox = rawurlencode($minLng . ',' . $minLat . ',' . $maxLng . ',' . $maxLat);
        $marker = rawurlencode($lat . ',' . $lng);

        return 'https://www.openstreetmap.org/export/embed.html?bbox=' . $bbox . '&layer=mapnik&marker=' . $marker;
    }

    /**
     * الفرع الافتراضي الوحيد في النظام (وضع فرع واحد فقط).
     * لا تعديل على جداول الفروع.
     */
    public static function getDefaultBranchId(): int
    {
        return defined('DEFAULT_BRANCH_ID') ? (int) DEFAULT_BRANCH_ID : 1;
    }

    public static function getPagination()
    {
        $pagination_limit = BusinessSetting::where('key', 'pagination_limit')->first();
        return $pagination_limit?->value ?? 10;
    }

    public static function combinations($arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    public static function variation_price($product, $variation)
    {
        if (empty(json_decode($variation, true))) {
            $result = $product['price'];
        } else {
            $match = json_decode($variation, true)[0];
            $result = 0;
            foreach (json_decode($product['variations'], true) as $property => $value) {
                if ($value['type'] == $match['type']) {
                    $result = $value['price'];
                }
            }
        }
        return $result;
    }

    public static function product_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data) {
            foreach ($data as $item) {
                $variations = [];
                $item['category_ids'] = json_decode($item['category_ids'] ?? '[]') ?? [];
                $item['image'] = json_decode($item['image'] ?? '[]') ?? [];
                $item['attributes'] = json_decode($item['attributes'] ?? '[]') ?? [];
                $item['choice_options'] = json_decode($item['choice_options'] ?? '[]') ?? [];
                foreach ((json_decode($item['variations'] ?? '[]', true) ?? []) as $var) {
                    $variations[] = [
                        'type' => $var['type'],
                        'price' => (double)$var['price'],
                        'stock' => (double)$var['stock'],
                    ];
                }
                $item['variations'] = $variations;

                $translations = $item['translations'] ?? [];
                if (is_countable($translations) && count($translations) > 0) {
                    foreach ($translations as $translation) {
                        if ($translation->key == 'name') {
                            $item['name'] = $translation->value;
                        }
                        if ($translation->key == 'description') {
                            $item['description'] = $translation->value;
                        }
                    }
                }
                unset($item['translations']);
                $storage[] = $item;
            }
            $data = $storage;
        } else {
            $variations = [];
            $data['category_ids'] = json_decode($data['category_ids'] ?? '[]') ?? [];
            $data['image'] = json_decode($data['image'] ?? '[]') ?? [];
            $data['attributes'] = json_decode($data['attributes'] ?? '[]') ?? [];
            $data['choice_options'] = json_decode($data['choice_options'] ?? '[]') ?? [];
            foreach ((json_decode($data['variations'] ?? '[]', true) ?? []) as $var) {
                $variations[] = [
                    'type' => $var['type'],
                    'price' => (double)$var['price'],
                    'stock' => (int)$var['stock'],
                ];
            }

            $data['variations'] = $variations;
            $translations = $data['translations'] ?? [];
            if (is_countable($translations) && count($translations) > 0) {
                foreach ($translations as $translation) {
                    if ($translation->key == 'name') {
                        $data['name'] = $translation->value;
                    }
                    if ($translation->key == 'description') {
                        $data['description'] = $translation->value;
                    }
                }
            }
        }

        return $data;
    }

    public static function order_details_formatter($details)
    {
        if ($details->count() > 0) {
            foreach ($details as $detail) {
                $detail['product_details'] = gettype($detail['product_details']) != 'array' ? (array)json_decode($detail['product_details'], true) : (array)$detail['product_details'];
                $detail['variation'] = (function ($v) {
                    $v = is_string($v) ? json_decode($v, true) : $v;
                    if (!is_array($v)) return [];
                    if ($v === [[]]) return [];
                    return (count($v) === 1 && is_array(reset($v))) ? reset($v) : $v;
                })($detail['variation']);

                $detail['variant'] = gettype($detail['variant']) != 'array' ? (array)json_decode($detail['variant'], true) : (array)$detail['variant'];

                $orderType = Order::find($detail->order_id)->order_type;

                if ($orderType === 'pos') {
                    $detail['variation'] = !empty($detail['variation'])
                        ? implode('-', array_values($detail['variation']))
                        : null;
                } else {
                    $detail['variation'] = (count($detail['variation']) > 0 && in_array(null, $detail['variation'], true))
                        ? null
                        : (!empty($detail['variation']) ? implode('-', array_values($detail['variation'])) : null);
                }

                $detail['product_details'] = Helpers::product_formatter($detail['product_details']);
            }
        }

        return $details;
    }

    public static function product_formatter($product)
    {
        $product['image'] = gettype($product['image']) != 'array' ? (array)json_decode($product['image'], true) : (array)$product['image'];
        $product['variations'] = gettype($product['variations']) != 'array' ? (array)json_decode($product['variations'], true) : (array)$product['variations'];
        $product['attributes'] = gettype($product['attributes']) != 'array' ? (array)json_decode($product['attributes'], true) : (array)$product['attributes'];
        $product['category_ids'] = gettype($product['category_ids']) != 'array' ? (array)json_decode($product['category_ids'], true) : (array)$product['category_ids'];
        $product['choice_options'] = gettype($product['choice_options']) != 'array' ? (array)json_decode($product['choice_options'], true) : (array)$product['choice_options'];

        return $product;
    }

    public static function get_business_settings($name)
    {
        $config = null;
        $settings = Cache::rememberForever(CACHE_BUSINESS_SETTINGS_TABLE, function () {
            return BusinessSetting::all();
        });

        $data = $settings?->firstWhere('key', $name);
        if (isset($data)) {
            $config = json_decode($data['value'], true);
            // Use original value when: null (invalid JSON), or scalar (e.g. '970597177755' decodes to int but should stay string for phone)
            if ($config === null || !is_array($config)) {
                $config = $data['value'];
            }
        }
        return $config;
    }

    /**
     * Host part for auto-generated customer emails (AUTO_EMAIL_DOMAIN or APP_URL host).
     */
    public static function autoEmailHost(): string
    {
        $domain = config('app.auto_email_domain');
        if (is_string($domain) && $domain !== '') {
            $domain = strtolower(trim($domain));
            $domain = preg_replace('#^https?://#i', '', $domain);
            $domain = explode('/', $domain, 2)[0];
            $domain = explode(':', $domain, 2)[0];

            return preg_replace('/^www\./i', '', $domain) ?: 'localhost';
        }

        $url = (string) config('app.url', 'http://localhost');
        $host = parse_url($url, PHP_URL_HOST);
        if (empty($host)) {
            $host = 'localhost';
        }

        return preg_replace('/^www\./i', '', strtolower($host));
    }

    /**
     * Build a deterministic auto email from phone (digits only in local part).
     */
    public static function generateAutoCustomerEmail(string $phone, int $suffix = 0): string
    {
        $host = self::autoEmailHost();
        $digits = preg_replace('/\D/', '', $phone) ?: bin2hex(random_bytes(8));
        $local = 'u' . $digits . ($suffix > 0 ? 'x' . $suffix : '');
        $local = substr(preg_replace('/[^a-z0-9]/i', '', strtolower($local)), 0, 64);

        return $local . '@' . $host;
    }

    /**
     * Unique auto email for users table (handles rare collisions).
     */
    public static function generateUniqueAutoCustomerEmail(string $phone): string
    {
        $suffix = 0;
        do {
            $email = self::generateAutoCustomerEmail($phone, $suffix);
            $suffix++;
        } while (User::where('email', $email)->exists());

        return $email;
    }

    public static function get_login_settings($name)
    {
        $config = null;
        $settings = Cache::rememberForever(CACHE_LOGIN_SETUP_TABLE, function () {
            return LoginSetup::all();
        });

        $data = $settings?->firstWhere('key', $name);
        if (isset($data)) {
            $config = json_decode($data['value'], true);
            if (is_null($config)) {
                $config = $data['value'];
            }
        }
        return $config;
    }

    public static function currency_code()
    {
        $currency_code = BusinessSetting::where(['key' => 'currency'])->first()?->value ?? 'ILS';
        return $currency_code;
    }

    public static function currency_symbol()
    {
        $currency_symbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()?->currency_symbol ?? '$';
        return $currency_symbol;
    }

    public static function set_symbol($amount)
    {
        $position = Helpers::get_business_settings('currency_symbol_position');
        if (!is_null($position) && $position == 'left') {
            $string = self::currency_symbol() . '' . number_format($amount, 2);
        } else {
            $string = number_format($amount, 2) . '' . self::currency_symbol();
        }
        return $string;
    }

    /**
     * @param array|null $data
     * @return false|void
     */
    public static function sendNotificationToHttp(array|null $data)
    {
        $config = self::get_business_settings('push_notification_service_file_content');
        $key = (array)$config;
        $url = 'https://fcm.googleapis.com/v1/projects/'.$key['project_id'].'/messages:send';
        $headers = [
            'Authorization' => 'Bearer ' . self::getAccessToken($key),
            'Content-Type' => 'application/json',
        ];
        try {
            Http::withHeaders($headers)->post($url, $data);
        }catch (\Exception $exception){
            return false;
        }
    }

    public static function getAccessToken($key):String
    {
        $jwtToken = [
            'iss' => $key['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => time() + 3600,
            'iat' => time(),
        ];
        $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $jwtPayload = base64_encode(json_encode($jwtToken));
        $unsignedJwt = $jwtHeader . '.' . $jwtPayload;
        openssl_sign($unsignedJwt, $signature, $key['private_key'], OPENSSL_ALGO_SHA256);
        $jwt = $unsignedJwt . '.' . base64_encode($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);
        return $response->json('access_token');
    }

    public static function send_push_notif_to_device($fcm_token, $data)
    {
        $postData = [
            'message' => [
                "token" => $fcm_token,
                "data" => [
                    "title" => (string)$data['title'],
                    "body" => (string)$data['description'],
                    "image" => (string)$data['image'],
                    "order_id" => (string)$data['order_id'],
                    "type" => (string)$data['type'],
                    "user_name" => (string)($data['user_name'] ?? ''),
                    "user_image" => (string)($data['user_image'] ?? ''),
                ],
                "notification" => [
                    'title' => (string)$data['title'],
                    'body' => (string)$data['description'],
                    "image" => (string)$data['image'],
                ],
                "android" => [
                    'priority' => 'high',
                    "notification" => [
                        "channel_id" => "hexacom",
                        "sound" => "notification.wav",
                        "icon" => "notification_icon",
                    ]
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "sound" => "notification.wav"
                        ]
                    ],
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                ],
            ]
        ];

        return self::sendNotificationToHttp($postData);
    }

    public static function send_push_notif_to_topic($data, $type)
    {
        $image = asset('storage/notification') . '/' . $data['image'];

        $postData = [
            'message' => [
                "topic" => 'market',
                "data" => [
                    "title" => (string)$data['title'],
                    "body" => (string)$data['description'],
                    "order_id" => (string)$data['order_id'],
                    "type" => (string)$data['type'],
                    "image" => (string)$image
                ],
                "notification" => [
                    "title" => (string)$data['title'],
                    "body" => (string)$data['description'],
                    "image" => (string)$image,
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "sound" => "notification.wav"
                        ]
                    ]
                ],
            ]
        ];

        return self::sendNotificationToHttp($postData);
    }

    public static function sendPushNotifToTopicForMaintenanceMode($data, $topic)
    {
        $postData = [
            'message' => [
                "topic" => $topic,
                "data" => [
                    "title" => (string)$data['title'],
                    "body" => (string)$data['description'],
                    "type" => (string)$data['type'],
                ],
            ]
        ];

        return self::sendNotificationToHttp($postData);
    }

    public static function rating_count($product_id, $rating)
    {
        return Review::where(['product_id' => $product_id, 'rating' => $rating])->count();
    }

    public static function tax_calculate($product, $price)
    {
        if ($product['tax_type'] == 'percent') {
            $price_tax = ($price / 100) * $product['tax'];
        } else {
            $price_tax = $product['tax'];
        }
        return $price_tax;
    }

    public static function discount_calculate($product, $price)
    {
        if ($product['discount_type'] == 'percent') {
            $price_discount = ($price / 100) * $product['discount'];
        } else {
            $price_discount = $product['discount'];
        }
        return $price_discount;
    }

    /**
     * Get product price for a user based on their effective user type.
     * Uses custom price per product per user type if set; otherwise base or variation price.
     *
     * @param Product $product
     * @param User|null $user
     * @param array|string|null $variation For products with variations, pass the variation to get variation price when no custom price.
     * @return float
     */
    public static function product_price_for_user(Product $product, ?User $user, $variation = null): float
    {
        $userType = $user?->effectiveUserType();
        if (!$userType) {
            if ($variation && !empty(json_decode($product->variations, true))) {
                return (float) self::variation_price($product, is_string($variation) ? $variation : json_encode($variation));
            }
            return (float) $product->price;
        }

        $customPrice = ProductUserTypePrice::where('product_id', $product->id)
            ->where('user_type_id', $userType->id)
            ->value('price');

        if ($customPrice !== null) {
            return (float) $customPrice;
        }

        if ($variation && !empty(json_decode($product->variations, true))) {
            return (float) self::variation_price($product, is_string($variation) ? $variation : json_encode($variation));
        }
        return (float) $product->price;
    }

    /**
     * Get product discount for a user based on their effective user type.
     * Uses custom discount per product per user type if set; otherwise product's default discount.
     *
     * @param Product $product
     * @param User|null $user
     * @return array{ discount: float, discount_type: string }
     */
    public static function product_discount_for_user(Product $product, ?User $user): array
    {
        $userType = $user?->effectiveUserType();
        if ($userType) {
            $custom = ProductUserTypeDiscount::where('product_id', $product->id)
                ->where('user_type_id', $userType->id)
                ->first();
            if ($custom !== null) {
                return [
                    'discount' => (float) $custom->discount,
                    'discount_type' => $custom->discount_type ?? 'percent',
                ];
            }
        }
        return [
            'discount' => (float) ($product->discount ?? 0),
            'discount_type' => $product->discount_type ?? 'percent',
        ];
    }

    /**
     * Skip Passport JWT parsing when the client sends placeholder or malformed Bearer values (e.g. "null").
     */
    private static function passportBearerTokenIsParsable(?string $token): bool
    {
        if ($token === null) {
            return false;
        }
        $token = trim($token);
        if ($token === '') {
            return false;
        }
        if (strcasecmp($token, 'null') === 0 || strcasecmp($token, 'undefined') === 0) {
            return false;
        }
        // Laravel Passport access tokens are JWT (header.payload.sig → exactly two dots)
        return substr_count($token, '.') === 2;
    }

    /**
     * Filter out products that are not visible to the current API caller's user type.
     * Call BEFORE apply_user_type_prices_to_products on any API response that returns products.
     *
     * Product visibility rules (visible_to_user_types column):
     *   NULL        → visible to everyone
     *   []          → hidden from everyone
     *   [0, ...]    → 0 = guest; any positive integer = user_type_id
     *
     * @param mixed $data Single product (array|object) or collection/array of products
     * @param bool $multi True if $data is a list of products
     * @return mixed Filtered data; for $multi=false returns null if the product is hidden
     */
    public static function filter_visible_products(mixed $data, bool $multi): mixed
    {
        $isGuest = true;
        $userTypeId = null;
        try {
            $user = auth('api')->user();
            if ($user) {
                $isGuest = false;
                $userTypeId = $user->user_type_id;
            }
        } catch (\Throwable) {
            // treat unparsable/missing token as guest
        }

        $isVisible = function ($item) use ($isGuest, $userTypeId) {
            $id = is_array($item) ? ($item['id'] ?? null) : ($item->id ?? null);
            if (!$id) {
                return true;
            }
            $product = Product::withoutGlobalScopes()->find($id);
            if (!$product) {
                return true;
            }
            $allowed = $product->visible_to_user_types; // already cast to array|null

            if ($allowed === null) {
                return true; // visible to everyone
            }
            if (empty($allowed)) {
                return false; // hidden from everyone
            }
            if ($isGuest) {
                return in_array(0, $allowed);
            }
            if ($userTypeId === null) {
                return true; // logged-in user with no assigned type → no restriction
            }
            return in_array((int) $userTypeId, $allowed);
        };

        if ($multi) {
            $filtered = [];
            foreach ($data as $item) {
                if ($isVisible($item)) {
                    $filtered[] = $item;
                }
            }
            if ($data instanceof \Illuminate\Support\Collection) {
                return collect($filtered)->values();
            }
            return array_values($filtered);
        }

        return $isVisible($data) ? $data : null;
    }

    /**
     * Apply user-type-specific prices to product(s) for the current API user.
     * Use after product_data_formatting on any API response that returns products.
     *
     * @param mixed $data Single product (array|object) or collection/array of products
     * @param bool $multi True if $data is a list of products
     * @return mixed Same structure with price(s) overwritten for logged-in user's type
     */
    public static function apply_user_type_prices_to_products(mixed $data, bool $multi): mixed
    {
        $bearer = request()->bearerToken();
        if (! self::passportBearerTokenIsParsable($bearer)) {
            return $data;
        }

        try {
            $user = auth('api')->user();
        } catch (\Throwable) {
            return $data;
        }
        if (!$user) {
            return $data;
        }
        if ($multi) {
            foreach ($data as $i => $item) {
                $id = is_array($item) ? ($item['id'] ?? null) : $item->id ?? null;
                $p = $id ? Product::withoutGlobalScopes()->find($id) : null;
                if ($p) {
                    $price = self::product_price_for_user($p, $user, null);
                    $discountData = self::product_discount_for_user($p, $user);
                    if (is_array($data[$i])) {
                        $data[$i]['price'] = $price;
                        $data[$i]['discount'] = $discountData['discount'];
                        $data[$i]['discount_type'] = $discountData['discount_type'];
                    } else {
                        $data[$i]->price = $price;
                        $data[$i]->discount = $discountData['discount'];
                        $data[$i]->discount_type = $discountData['discount_type'];
                    }
                }
            }
        } else {
            $id = is_array($data) ? ($data['id'] ?? null) : $data->id ?? null;
            $p = $id ? Product::withoutGlobalScopes()->find($id) : null;
            if ($p) {
                $price = self::product_price_for_user($p, $user, null);
                $discountData = self::product_discount_for_user($p, $user);
                if (is_array($data)) {
                    $data['price'] = $price;
                    $data['discount'] = $discountData['discount'];
                    $data['discount_type'] = $discountData['discount_type'];
                } else {
                    $data->price = $price;
                    $data->discount = $discountData['discount'];
                    $data->discount_type = $discountData['discount_type'];
                }
            }
        }
        return $data;
    }

    public static function max_earning()
    {
        $data = Order::notPos()->where(['order_status' => 'delivered'])->select('id', 'created_at', 'order_amount')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += $order['order_amount'];
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function max_orders()
    {
        $data = Order::notPos()->select('id', 'created_at')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += 1;
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function order_status_update_message($status)
    {
        if ($status == 'pending') {
            $data = self::get_business_settings('order_pending_message');
        } elseif ($status == 'confirmed') {
            $data = self::get_business_settings('order_confirmation_msg');
        } elseif ($status == 'processing') {
            $data = self::get_business_settings('order_processing_message');
        } elseif ($status == 'out_for_delivery') {
            $data = self::get_business_settings('out_for_delivery_message');
        } elseif ($status == 'delivered') {
            $data = self::get_business_settings('order_delivered_message');
        } elseif ($status == 'delivery_boy_delivered') {
            $data = self::get_business_settings('delivery_boy_delivered_message');
        } elseif ($status == 'del_assign') {
            $data = self::get_business_settings('delivery_boy_assign_message');
        } elseif ($status == 'ord_start') {
            $data = self::get_business_settings('delivery_boy_start_message');
        } elseif ($status == 'returned') {
            $data = self::get_business_settings('returned_message');
        }  elseif ($status == 'failed') {
            $data = self::get_business_settings('failed_message');
        }  elseif ($status == 'canceled') {
            $data = self::get_business_settings('canceled_message');
        } elseif ($status == 'customer_notify_message') {
            $data = self::get_business_settings('customer_notify_message');
        } else {
            $data = '{"status":"0","message":""}';
        }

        if ($data == null || $data['status'] == 0) {
            return 0;
        }
        return $data['message'];
    }

    public static function day_part()
    {
        $part = "";
        $morning_start = date("h:i:s", strtotime("5:00:00"));
        $afternoon_start = date("h:i:s", strtotime("12:01:00"));
        $evening_start = date("h:i:s", strtotime("17:01:00"));
        $evening_end = date("h:i:s", strtotime("21:00:00"));

        if (time() >= $morning_start && time() < $afternoon_start) {
            $part = "morning";
        } elseif (time() >= $afternoon_start && time() < $evening_start) {
            $part = "afternoon";
        } elseif (time() >= $evening_start && time() <= $evening_end) {
            $part = "evening";
        } else {
            $part = "night";
        }

        return $part;
    }

    public static function remove_dir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") Helpers::remove_dir($dir . "/" . $object); else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function get_language_name($key)
    {
        $languages = array(
            "af" => "Afrikaans",
            "sq" => "Albanian - shqip",
            "am" => "Amharic - አማርኛ",
            "ar" => "Arabic - العربية",
            "an" => "Aragonese - aragonés",
            "hy" => "Armenian - հայերեն",
            "ast" => "Asturian - asturianu",
            "az" => "Azerbaijani - azərbaycan dili",
            "eu" => "Basque - euskara",
            "be" => "Belarusian - беларуская",
            "bn" => "Bengali - বাংলা",
            "bs" => "Bosnian - bosanski",
            "br" => "Breton - brezhoneg",
            "bg" => "Bulgarian - български",
            "ca" => "Catalan - català",
            "ckb" => "Central Kurdish - کوردی (دەستنوسی عەرەبی)",
            "zh" => "Chinese - 中文",
            "zh-HK" => "Chinese (Hong Kong) - 中文（香港）",
            "zh-CN" => "Chinese (Simplified) - 中文（简体）",
            "zh-TW" => "Chinese (Traditional) - 中文（繁體）",
            "co" => "Corsican",
            "hr" => "Croatian - hrvatski",
            "cs" => "Czech - čeština",
            "da" => "Danish - dansk",
            "nl" => "Dutch - Nederlands",
            "en" => "English",
            "en-AU" => "English (Australia)",
            "en-CA" => "English (Canada)",
            "en-IN" => "English (India)",
            "en-NZ" => "English (New Zealand)",
            "en-ZA" => "English (South Africa)",
            "en-GB" => "English (United Kingdom)",
            "en-US" => "English (United States)",
            "eo" => "Esperanto - esperanto",
            "et" => "Estonian - eesti",
            "fo" => "Faroese - føroyskt",
            "fil" => "Filipino",
            "fi" => "Finnish - suomi",
            "fr" => "French - français",
            "fr-CA" => "French (Canada) - français (Canada)",
            "fr-FR" => "French (France) - français (France)",
            "fr-CH" => "French (Switzerland) - français (Suisse)",
            "gl" => "Galician - galego",
            "ka" => "Georgian - ქართული",
            "de" => "German - Deutsch",
            "de-AT" => "German (Austria) - Deutsch (Österreich)",
            "de-DE" => "German (Germany) - Deutsch (Deutschland)",
            "de-LI" => "German (Liechtenstein) - Deutsch (Liechtenstein)",
            "de-CH" => "German (Switzerland) - Deutsch (Schweiz)",
            "el" => "Greek - Ελληνικά",
            "gn" => "Guarani",
            "gu" => "Gujarati - ગુજરાતી",
            "ha" => "Hausa",
            "haw" => "Hawaiian - ʻŌlelo Hawaiʻi",
            "he" => "Hebrew - עברית",
            "hi" => "Hindi - हिन्दी",
            "hu" => "Hungarian - magyar",
            "is" => "Icelandic - íslenska",
            "id" => "Indonesian - Indonesia",
            "ia" => "Interlingua",
            "ga" => "Irish - Gaeilge",
            "it" => "Italian - italiano",
            "it-IT" => "Italian (Italy) - italiano (Italia)",
            "it-CH" => "Italian (Switzerland) - italiano (Svizzera)",
            "ja" => "Japanese - 日本語",
            "kn" => "Kannada - ಕನ್ನಡ",
            "kk" => "Kazakh - қазақ тілі",
            "km" => "Khmer - ខ្មែរ",
            "ko" => "Korean - 한국어",
            "ku" => "Kurdish - Kurdî",
            "ky" => "Kyrgyz - кыргызча",
            "lo" => "Lao - ລາວ",
            "la" => "Latin",
            "lv" => "Latvian - latviešu",
            "ln" => "Lingala - lingála",
            "lt" => "Lithuanian - lietuvių",
            "mk" => "Macedonian - македонски",
            "ms" => "Malay - Bahasa Melayu",
            "ml" => "Malayalam - മലയാളം",
            "mt" => "Maltese - Malti",
            "mr" => "Marathi - मराठी",
            "mn" => "Mongolian - монгол",
            "ne" => "Nepali - नेपाली",
            "no" => "Norwegian - norsk",
            "nb" => "Norwegian Bokmål - norsk bokmål",
            "nn" => "Norwegian Nynorsk - nynorsk",
            "oc" => "Occitan",
            "or" => "Oriya - ଓଡ଼ିଆ",
            "om" => "Oromo - Oromoo",
            "ps" => "Pashto - پښتو",
            "fa" => "Persian - فارسی",
            "pl" => "Polish - polski",
            "pt" => "Portuguese - português",
            "pt-BR" => "Portuguese (Brazil) - português (Brasil)",
            "pt-PT" => "Portuguese (Portugal) - português (Portugal)",
            "pa" => "Punjabi - ਪੰਜਾਬੀ",
            "qu" => "Quechua",
            "ro" => "Romanian - română",
            "mo" => "Romanian (Moldova) - română (Moldova)",
            "rm" => "Romansh - rumantsch",
            "ru" => "Russian - русский",
            "gd" => "Scottish Gaelic",
            "sr" => "Serbian - српски",
            "sh" => "Serbo-Croatian - Srpskohrvatski",
            "sn" => "Shona - chiShona",
            "sd" => "Sindhi",
            "si" => "Sinhala - සිංහල",
            "sk" => "Slovak - slovenčina",
            "sl" => "Slovenian - slovenščina",
            "so" => "Somali - Soomaali",
            "st" => "Southern Sotho",
            "es" => "Spanish - español",
            "es-AR" => "Spanish (Argentina) - español (Argentina)",
            "esLA" => "Spanish (Latin America) - español (Latinoamérica)",
            "es-MX" => "Spanish (Mexico) - español (México)",
            "es-ES" => "Spanish (Spain) - español (España)",
            "es-US" => "Spanish (United States) - español (Estados Unidos)",
            "su" => "Sundanese",
            "sw" => "Swahili - Kiswahili",
            "sv" => "Swedish - svenska",
            "tg" => "Tajik - тоҷикӣ",
            "ta" => "Tamil - தமிழ்",
            "tt" => "Tatar",
            "te" => "Telugu - తెలుగు",
            "th" => "Thai - ไทย",
            "ti" => "Tigrinya - ትግርኛ",
            "to" => "Tongan - lea fakatonga",
            "tr" => "Turkish - Türkçe",
            "tk" => "Turkmen",
            "tw" => "Twi",
            "uk" => "Ukrainian - українська",
            "ur" => "Urdu - اردو",
            "ug" => "Uyghur",
            "uz" => "Uzbek - o‘zbek",
            "vi" => "Vietnamese - Tiếng Việt",
            "wa" => "Walloon - wa",
            "cy" => "Welsh - Cymraeg",
            "fy" => "Western Frisian",
            "xh" => "Xhosa",
            "yi" => "Yiddish",
            "yo" => "Yoruba - Èdè Yorùbá",
            "zu" => "Zulu - isiZulu",
        );
        return array_key_exists($key, $languages) ? $languages[$key] : $key;
    }

    /**
     * Parse simple page content (about_us, terms, privacy) for multi-language.
     * Legacy: plain string. New: {"ar":"...","en":"...","he":"..."}.
     * Accepts string (raw JSON) or array (already decoded).
     */
    public static function parsePageContentByLang(mixed $value, string $lang, string $defaultLang = 'ar'): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        if (is_array($value)) {
            return (string) ($value[$lang] ?? $value[$defaultLang] ?? '');
        }
        $decoded = json_decode($value, true);
        if (is_array($decoded) && isset($decoded[$lang])) {
            return (string) $decoded[$lang];
        }
        if (is_array($decoded) && isset($decoded[$defaultLang])) {
            return (string) $decoded[$defaultLang];
        }
        return (string) $value;
    }

    /**
     * Parse status page content (refund, cancellation, return) for multi-language.
     * Legacy: {"status":0,"content":"..."}. New: {"status":0,"content":{"ar":"...","en":"..."}}.
     */
    public static function parsePageContentWithStatus(?string $value, string $lang, string $defaultLang = 'ar'): array
    {
        $default = ['status' => 0, 'content' => ''];
        if ($value === null || $value === '') {
            return $default;
        }
        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            return $default;
        }
        $status = (int) ($decoded['status'] ?? 0);
        $contentRaw = $decoded['content'] ?? null;
        if (is_array($contentRaw) && (isset($contentRaw[$lang]) || isset($contentRaw[$defaultLang]))) {
            $content = (string) ($contentRaw[$lang] ?? $contentRaw[$defaultLang] ?? '');
        } else {
            $content = is_string($contentRaw) ? $contentRaw : '';
        }
        return ['status' => $status, 'content' => $content];
    }

    /**
     * Get all language content for a simple page as array [lang => content].
     */
    public static function getPageContentByLangs(?string $value, array $langs): array
    {
        $result = array_fill_keys($langs, '');
        if ($value === null || $value === '') {
            return $result;
        }
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            foreach ($langs as $l) {
                $result[$l] = (string) ($decoded[$l] ?? '');
            }
        } else {
            $result[$langs[0] ?? 'ar'] = $value;
        }
        return $result;
    }

    /**
     * Get all language content for a status page as array [lang => content], plus status.
     */
    public static function getPageContentWithStatusByLangs(?string $value, array $langs): array
    {
        $contentByLang = array_fill_keys($langs, '');
        $status = 0;
        if ($value !== null && $value !== '') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $status = (int) ($decoded['status'] ?? 0);
                $contentRaw = $decoded['content'] ?? null;
                if (is_array($contentRaw)) {
                    foreach ($langs as $l) {
                        $contentByLang[$l] = (string) ($contentRaw[$l] ?? '');
                    }
                } elseif (is_string($contentRaw)) {
                    $contentByLang[$langs[0] ?? 'ar'] = $contentRaw;
                }
            }
        }
        return ['status' => $status, 'content' => $contentByLang];
    }

    public static function upload(string $dir, string $format = APPLICATION_IMAGE_FORMAT, array|object|null $image = null) {
        if (!$image) {
            return null;
        }

        set_time_limit(300);

        $dir = rtrim($dir, '/') . '/';
        $sourcePath = $image instanceof UploadedFile
            ? $image->getRealPath()
            : $image;

        $info = @getimagesize($sourcePath);
        if (!$info || empty($info['mime'])) {
            return false;
        }

        $mime = strtolower($info['mime']);

        // Detect format safely
        $format = match ($mime) {
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
            default      => $format,
        };

        $imageName = Carbon::now()->format('Y-m-d') . '-' . uniqid() . '.' . $format;

        // Ensure directory exists
        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        $savePath = storage_path("app/public/{$dir}{$imageName}");

        /**
         * 🚨 IMPORTANT
         * Never process GIF with GD (animation will break)
         */
        if ($mime === 'image/gif') {
            return copy($sourcePath, $savePath) ? $imageName : false;
        }

        /**
         * WEBP copy-only if already webp
         */
        if ($mime === 'image/webp' && $format === 'webp') {
            return copy($sourcePath, $savePath) ? $imageName : false;
        }

        /**
         * Create GD image
         */
        $gdImage = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($sourcePath),
            'image/png'  => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            default      => false,
        };

        if (!$gdImage) {
            return false;
        }

        /**
         * Convert palette images to truecolor (required for WebP output)
         */
        if (!imageistruecolor($gdImage)) {
            $w = imagesx($gdImage);
            $h = imagesy($gdImage);
            $truecolor = imagecreatetruecolor($w, $h);
            imagealphablending($truecolor, false);
            imagesavealpha($truecolor, true);
            $transparent = imagecolorallocatealpha($truecolor, 0, 0, 0, 127);
            imagefill($truecolor, 0, 0, $transparent);
            imagecopy($truecolor, $gdImage, 0, 0, 0, 0, $w, $h);
            imagedestroy($gdImage);
            $gdImage = $truecolor;
        }

        /**
         * Preserve transparency
         */
        if (in_array($mime, ['image/png', 'image/webp'])) {
            imagealphablending($gdImage, false);
            imagesavealpha($gdImage, true);
        }

        /**
         * Resize logic
         */
        $maxSize = 2500;
        $width   = imagesx($gdImage);
        $height  = imagesy($gdImage);

        if ($width > $maxSize || $height > $maxSize) {
            $ratio = min($maxSize / $width, $maxSize / $height);
            $newW  = (int)($width * $ratio);
            $newH  = (int)($height * $ratio);

            $temp = imagecreatetruecolor($newW, $newH);

            if (in_array($mime, ['image/png', 'image/webp'])) {
                imagealphablending($temp, false);
                imagesavealpha($temp, true);
            }

            imagecopyresampled(
                $temp,
                $gdImage,
                0,
                0,
                0,
                0,
                $newW,
                $newH,
                $width,
                $height
            );

            imagedestroy($gdImage);
            $gdImage = $temp;
        }

        /**
         * Save image
         */
        $saved = match ($format) {
            'jpg', 'jpeg' => imagejpeg($gdImage, $savePath, 85),
            'png'         => imagepng($gdImage, $savePath, -1),
            'webp'        => imagewebp($gdImage, $savePath, 78),
            default       => false,
        };

        imagedestroy($gdImage);

        return $saved ? $imageName : false;
    }

    public static function uploadFile(string $dir, UploadedFile $file, array $allowedExtensions = null)
    {
        $defaultAllowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'p8', 'p12', 'pem', 'json', 'txt'];
        $allowed = $allowedExtensions ?? $defaultAllowed;
        $ext = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, $allowed, true)) {
            throw new \InvalidArgumentException('File extension "' . $ext . '" is not allowed. Allowed: ' . implode(', ', $allowed));
        }

        $dir = rtrim($dir, '/') . '/';
        $fileName = date('Y-m-d') . '-' . uniqid() . '.' . $ext;

        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        $file->storeAs("public/{$dir}", $fileName);

        return $fileName;
    }

    public static function update(string $dir, $old_image, string $format, array|object|null $image = null)
    {
        if (Storage::disk('public')->exists($dir . $old_image)) {
            Storage::disk('public')->delete($dir . $old_image);
        }
        $imageName = Helpers::upload($dir, $format, $image);
        return $imageName;
    }

    public static function delete($full_path)
    {
        if (Storage::disk('public')->exists($full_path)) {
            Storage::disk('public')->delete($full_path);
        }
        return [
            'success' => 1,
            'message' => 'Removed successfully !'
        ];
    }

    public static function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        if (is_bool(env($envKey))) {
            $oldValue = var_export(env($envKey), true);
        } else {
            $oldValue = env($envKey);
        }

        if (strpos($str, $envKey) !== false) {
            $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}", $str);

        } else {
            $str .= "{$envKey}={$envValue}\n";
        }
        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
        return $envValue;
    }

    public static function requestSender()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt_array($curl, array(
            CURLOPT_URL => route(base64_decode('YWN0aXZhdGlvbi1jaGVjaw==')),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $data = json_decode($response, true);
        return $data;
    }

    public static function pagination_limit()
    {
        $pagination_limit = BusinessSetting::where('key', 'pagination_limit')->first();
        return $pagination_limit?->value ?? 10;
    }

    public static function remove_invalid_charcaters($str)
    {
        return str_ireplace(['\'', '"', ',', ';', '<', '>', '?'], ' ', $str);
    }

    public static function file_remover(string $dir, $image)
    {
        if (!isset($image)) return true;

        if (Storage::disk('public')->exists($dir . $image)) Storage::disk('public')->delete($dir . $image);

        return true;
    }

    public static function onErrorImage($data, $src, $error_src ,$path)
    {
        if(isset($data) && strlen($data) >1 && Storage::disk('public')->exists($path.$data)){
            return $src;
        }
        return $error_src;
    }

    public static function get_delivery_charge($branchId, int|float|string|null $distance = null, int|string|null $selectedDeliveryArea = null)
    {
        $branch = Branch::with(['delivery_charge_setup', 'delivery_charge_by_area'])
            ->where(['id' => $branchId])
            ->first(['id', 'name', 'status']);

        if (!$branch || !$branch->delivery_charge_setup) {
            return 0;
        }

        // When an area is selected, use that area's charge first (so admin/API show correct value even if branch type is fixed)
        if ($selectedDeliveryArea !== null && $selectedDeliveryArea !== '') {
            if (Schema::hasTable('areas')) {
                $area = Area::find($selectedDeliveryArea);
                if ($area !== null && $area->delivery_charge !== null) {
                    return (float) $area->delivery_charge;
                }
            }
            $areaRow = DeliveryChargeByArea::where('id', $selectedDeliveryArea)->where('branch_id', $branchId)->first();
            if ($areaRow !== null && $areaRow->delivery_charge !== null) {
                return (float) $areaRow->delivery_charge;
            }
        }

        $deliveryType = $branch->delivery_charge_setup->delivery_charge_type ?? 'fixed';
        $deliveryType = $deliveryType === 'area' ? 'area' : ($deliveryType === 'distance' ? 'distance' : 'fixed');

        if($deliveryType == 'area' && $selectedDeliveryArea){
            if (Schema::hasTable('areas')) {
                $area = Area::find($selectedDeliveryArea);
                $deliveryCharge = $area->delivery_charge ?? 0;
            } else {
                $area = DeliveryChargeByArea::find($selectedDeliveryArea);
                $deliveryCharge = $area->delivery_charge ?? 0;
            }
        }elseif($deliveryType == 'distance') {
            $minDeliveryCharge = $branch->delivery_charge_setup->minimum_delivery_charge;
            $shippingChargePerKM = $branch->delivery_charge_setup->delivery_charge_per_kilometer;
            $minDistanceForFreeDelivery = $branch->delivery_charge_setup->minimum_distance_for_free_delivery;

            if ($distance < $minDistanceForFreeDelivery) {
                $deliveryCharge = 0;
            } else {
                $distanceDeliveryCharge = $shippingChargePerKM * $distance;
                $deliveryCharge = max($distanceDeliveryCharge, $minDeliveryCharge);
            }
        }else{
            $deliveryCharge = $branch->delivery_charge_setup->fixed_delivery_charge ?? 0;
        }
        return $deliveryCharge;
    }

    public static function trimWords($text, $limit = 50)
    {
        $words = explode(' ', strip_tags($text));
        $wordCount = count($words);
        if ($wordCount <= $limit || $limit == 0) {
            return [
                'text' => implode(' ', $words),
                'isTruncated' => false
            ];
        }
        return [
            'text' => implode(' ', array_slice($words, 0, $limit)) . '...',
            'isTruncated' => true
        ];
    }


    public static function paginateValueNumberOptions(?int $custom = null): array
    {
        $allowedNumberOptions = [5, 10, 20, 30, 40, 50, 100, (int) Helpers::getPagination()];

        if ($custom) {
            $allowedNumberOptions[] = (int) $custom;
        }

        $uniqueAllowedNumberOptions = array_unique($allowedNumberOptions);
        sort($uniqueAllowedNumberOptions);

        return $uniqueAllowedNumberOptions;
    }


}

function translate($key)
{
    $local = session()->has('local') ? session('local') : 'ar';
    App::setLocale($local);
    $lang_array = include(base_path('resources/lang/' . $local . '/messages.php'));
    $processed_key = ucfirst(str_replace('_', ' ', Helpers::remove_invalid_charcaters($key)));
    if (!array_key_exists($key, $lang_array)) {
        $lang_array[$key] = $processed_key;
        $str = "<?php return " . var_export($lang_array, true) . ";";
        file_put_contents(base_path('resources/lang/' . $local . '/messages.php'), $str);
        $result = $processed_key;
    } else {
        $result = __('messages.' . $key);
    }
    return $result;
}

