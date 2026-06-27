<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Branch;
use App\Models\BusinessSetting;
use App\Models\City;
use App\Models\Currency;
use App\Models\SocialMedia;
use App\Models\LoyaltyPoint;
use App\Models\LoginSetup;
use App\Traits\HelperTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class ConfigController extends Controller
{
    use HelperTrait;

    public function __construct(
        private LoginSetup $loginSetup)
    {}

    public function configuration(): \Illuminate\Http\JsonResponse
    {
        $cacheKey = 'api_v1_configuration_payload_v1';
        $cachedConfig = Cache::get($cacheKey);

        if ($cachedConfig !== null) {
            $lang = $this->getRequestLang();
            $cachedConfig['terms_and_conditions'] = Helpers::parsePageContentByLang($cachedConfig['terms_and_conditions'] ?? null, $lang, 'ar');
            $cachedConfig['privacy_policy'] = Helpers::parsePageContentByLang($cachedConfig['privacy_policy'] ?? null, $lang, 'ar');
            $cachedConfig['about_us'] = Helpers::parsePageContentByLang($cachedConfig['about_us'] ?? null, $lang, 'ar');
            $cachedConfig['cookies_management']['text'] = Helpers::parsePageContentByLang($cachedConfig['cookies_management']['text'] ?? null, $lang, 'ar');
            $cachedConfig['whatsapp'] = $this->normalizeWhatsappChat($cachedConfig['whatsapp'] ?? null);
            $cachedConfig['telegram'] = $this->normalizeTelegramChat($cachedConfig['telegram'] ?? null);
            $cachedConfig['messenger'] = $this->normalizeMessengerChat($cachedConfig['messenger'] ?? null);
            $this->refreshMaintenanceFieldsInConfig($cachedConfig);
            $this->addConfigModelCompatibilityKeys($cachedConfig);
            return response()->json($cachedConfig);
        }

        $currencySymbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()?->currency_symbol ?? '$';
        $cod = Helpers::get_business_settings('cash_on_delivery');
        $codStatus = (is_array($cod) && isset($cod['status'])) ? (int)$cod['status'] : 1;

        $cookiesConfig = Helpers::get_business_settings('cookies');
        $cookiesManagement = [
            "status" => (int)($cookiesConfig['status'] ?? 0),
            "text" => $cookiesConfig['text'] ?? '',
        ];

        $advanceMaintenanceMode = $this->checkMaintenanceMode();

        $emailVerification = (int) Helpers::get_login_settings('email_verification');
        $phoneVerification = (int) Helpers::get_login_settings('phone_verification');

        $firebaseOTPVerification = Helpers::get_business_settings('firebase_otp_verification');
        $firebaseOTPVerificationStatus = (integer)($firebaseOTPVerification ? $firebaseOTPVerification['status'] : 0);


        $status = 0;
        if ($emailVerification == 1) {
            $status = 1;
        } elseif ($phoneVerification == 1) {
            $status = 1;
        }

        $customerVerification = [
            'status' => $status,
            'phone'=> $phoneVerification,
            'email'=> $emailVerification,
            'firebase'=> (int) $firebaseOTPVerificationStatus,
        ];

        $loginOptions = Helpers::get_login_settings('login_options');
        $socialMediaLoginOptions = Helpers::get_login_settings('social_media_for_login');

        $customerLogin = [
            'login_option' => $loginOptions,
            'social_media_login_options' => $socialMediaLoginOptions
        ];

        $emailConfig = Helpers::get_business_settings('mail_config');

        $forgotPassword = [
            'firebase' => $firebaseOTPVerificationStatus,
            'phone' => 0,
            'email' => $emailConfig['status'] ?? 0
        ];

        $apple = Helpers::get_business_settings('apple_login');
        $appleLogin = [
            'login_medium' => $apple['login_medium'] ?? '',
            'client_id' => $apple['client_id'] ?? ''
        ];

        $config = [
            'ecommerce_name' => Helpers::get_business_settings('store_name') ?? Helpers::get_business_settings('restaurant_name'),
            'ecommerce_logo' => Helpers::get_business_settings('logo'),
            'app_logo' => Helpers::get_business_settings('app_logo'),
            'ecommerce_address' => Helpers::get_business_settings('address'),
            'ecommerce_phone' => (string) (Helpers::get_business_settings('phone') ?? ''),
            'ecommerce_email' => Helpers::get_business_settings('email_address'),
            'store_google_maps_url' => (string) (Helpers::get_business_settings('store_google_maps_url') ?? ''),
            'ecommerce_location_coverage' => Branch::where(['id' => Helpers::getDefaultBranchId()])->first(['longitude', 'latitude', 'coverage']),
            'minimum_order_value' => (float) Helpers::get_business_settings('minimum_order_value'),
            'self_pickup' => (int) Helpers::get_business_settings('self_pickup'),
            'base_urls' => [
                'product_image_url' => asset('storage/product'),
                'customer_image_url' => asset('storage/profile'),
                'banner_image_url' => asset('storage/banner'),
                'category_image_url' => asset('storage/category'),
                'category_banner_image_url' => asset('storage/category/banner'),
                'review_image_url' => asset('storage/review'),
                'notification_image_url' => asset('storage/notification'),
                'ecommerce_image_url' => asset('storage/ecommerce'),
                'chat_image_url' => asset('storage/conversation'),
                'flash_sale_image_url' => asset('storage/flash-sale'),
                'gateway_image_url' => asset('storage/payment_modules/gateway_image'),
            ],
            'currency_symbol' => $currencySymbol,
            'delivery_charge' => (float) Helpers::get_business_settings('delivery_charge'),
            'cash_on_delivery' => $codStatus == 1 ? 'true' : 'false',
            'digital_payment' => 'false',
            'branches' => Branch::active()->where('id', Helpers::getDefaultBranchId())->take(1)->get(['id', 'name', 'email', 'longitude', 'latitude', 'address', 'coverage', 'status']),
            'terms_and_conditions' => Helpers::get_business_settings('terms_and_conditions'),
            'privacy_policy' => Helpers::get_business_settings('privacy_policy'),
            'about_us' => Helpers::get_business_settings('about_us'),
            'email_verification' => (boolean) $emailVerification,
            'phone_verification' => (boolean) $phoneVerification,
            'currency_symbol_position' => Helpers::get_business_settings('currency_symbol_position') ?? 'right',
            'maintenance_mode' => (boolean)Helpers::get_business_settings('maintenance_mode') ?? 0,
            'country' => Helpers::get_business_settings('country') ?? 'PS',
            'play_store_config' => $this->getAppStoreConfig('play_store_config'),
            'app_store_config' => $this->getAppStoreConfig('app_store_config'),
            'social_media_link' => SocialMedia::orderBy('id', 'desc')->active()->get(),
            'software_version' => (string) (config('app.software_version') ?? ''),
            'otp_resend_time' => Helpers::get_business_settings('otp_resend_time') ?? 60,
            'cookies_management' => $cookiesManagement,
            'social_login' => [
                'google' => (integer) Helpers::get_business_settings('google_social_login'),
                'facebook' => (integer) Helpers::get_business_settings('facebook_social_login'),
            ],
            'whatsapp' => $this->normalizeWhatsappChat(Helpers::get_business_settings('whatsapp')),
            'telegram' => $this->normalizeTelegramChat(Helpers::get_business_settings('telegram')),
            'messenger' => $this->normalizeMessengerChat(Helpers::get_business_settings('messenger')),
            'digital_payment_status' => 0,
            'active_payment_method_list' => [],
            'advance_maintenance_mode' => $advanceMaintenanceMode,
            'google_map_status' => 0,
            'customer_verification' => $customerVerification,
            'customer_login' => $customerLogin,
            'guest_checkout' => (integer) (Helpers::get_business_settings('guest_checkout') ?? 0),
            'loyalty_points_enabled' => (integer) (Helpers::get_business_settings('loyalty_points_enabled') ?? 0),
            'loyalty_amount_for_one_point' => (float) (Helpers::get_business_settings('loyalty_amount_for_one_point') ?? 10),
            'loyalty_points_per_amount' => (float) (Helpers::get_business_settings('loyalty_points_per_amount') ?? 1),
            'loyalty_point_redemption_value' => (float) (Helpers::get_business_settings('loyalty_point_redemption_value') ?? 0.5),
            'loyalty_and_coupon_together' => (integer) (Helpers::get_business_settings('loyalty_and_coupon_together') ?? 1),
            'loyalty_levels' => LoyaltyPoint::LEVELS,
            'firebase_otp_verification_status' => $firebaseOTPVerificationStatus,
            'forgot_password' => $forgotPassword,
            'apple_login' => $appleLogin,
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_image_size' => uploadMaxFileSize('image'),
            'areas' => Schema::hasTable('areas') ? Area::orderBy('branch_id')->orderBy('sort_order')->orderBy('name_en')->get()->map(fn ($a) => [
                'id' => $a->id,
                'branch_id' => $a->branch_id,
                'name_en' => $a->name_en,
                'name_ar' => $a->name_ar,
                'delivery_charge' => (float) $a->delivery_charge,
            ])->values() : [],
            'cities' => Schema::hasTable('cities') ? City::orderBy('sort_order')->orderBy('name')->get()->map(fn ($c) => [
                'id' => $c->id,
                'area_id' => $c->area_id,
                'name_en' => $c->name,
                'name_ar' => $c->name_ar,
            ])->values() : []
        ];

        if (config('feature_flags.force_cash_only_and_hide_third_party', false)) {
            $config['cash_on_delivery'] = 'true';
            $config['digital_payment'] = 'false';
            $config['digital_payment_status'] = 0;
            $config['active_payment_method_list'] = [];
            $config['google_map_status'] = 0;
            $config['social_login'] = ['google' => 0, 'facebook' => 0];
            $config['firebase_otp_verification_status'] = 0;
            $config['customer_verification'] = array_merge($config['customer_verification'], ['firebase' => 0]);
            $config['forgot_password'] = array_merge($config['forgot_password'], ['firebase' => 0]);
            $config['customer_login']['login_option'] = array_merge($config['customer_login']['login_option'] ?? [], ['social_media_login' => 0]);
            $config['customer_login']['social_media_login_options'] = array_merge($config['customer_login']['social_media_login_options'] ?? [], ['google' => 0, 'facebook' => 0, 'apple' => 0]);
        }

        Cache::put($cacheKey, $config, now()->addMinutes(30));

        $lang = $this->getRequestLang();
        $config['terms_and_conditions'] = Helpers::parsePageContentByLang($config['terms_and_conditions'] ?? null, $lang, 'ar');
        $config['privacy_policy'] = Helpers::parsePageContentByLang($config['privacy_policy'] ?? null, $lang, 'ar');
        $config['about_us'] = Helpers::parsePageContentByLang($config['about_us'] ?? null, $lang, 'ar');
        $config['cookies_management']['text'] = Helpers::parsePageContentByLang($config['cookies_management']['text'] ?? null, $lang, 'ar');

        $this->addConfigModelCompatibilityKeys($config);

        return response()->json($config);
    }

    /**
     * maintenance_mode / advance_maintenance_mode depend on DB and clock; the rest of config may be cached ~30min.
     */
    private function refreshMaintenanceFieldsInConfig(array &$config): void
    {
        $config['advance_maintenance_mode'] = $this->checkMaintenanceMode();
        $config['maintenance_mode'] = (bool) (Helpers::get_business_settings('maintenance_mode') ?? 0);
    }

    /**
     * Add compatibility keys for Flutter ConfigModel (StackFood/wecommerce).
     * Ensures all String? fields receive string values (not int).
     */
    private function addConfigModelCompatibilityKeys(array &$config): void
    {
        $config['business_name'] = (string) ($config['ecommerce_name'] ?? '');
        $config['phone'] = (string) ($config['ecommerce_phone'] ?? '');
        $logo = $config['ecommerce_logo'] ?? $config['app_logo'] ?? '';
        $config['logo_full_url'] = $logo ? (string) asset('storage/ecommerce/' . $logo) : '';
        $config['address'] = (string) ($config['ecommerce_address'] ?? '');
        $config['email'] = (string) ($config['ecommerce_email'] ?? '');
        $config['currency_symbol_direction'] = (string) ($config['currency_symbol_position'] ?? 'right');
        $config['order_confirmation_model'] = 'default';
        $config['google_maps_location_url'] = (string) ($config['store_google_maps_url'] ?? '');
    }

    /**
     * Ensure store/app always receives a consistent shape for admin "Social media chat" settings.
     */
    private function normalizeWhatsappChat(mixed $raw): array
    {
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : null;
        }
        if (!is_array($raw)) {
            return ['status' => 0, 'number' => ''];
        }
        $num = $raw['number'] ?? $raw['value'] ?? '';
        if (!is_string($num)) {
            $num = (string) $num;
        }

        return [
            'status' => (int) ($raw['status'] ?? 0),
            'number' => $num,
        ];
    }

    private function normalizeTelegramChat(mixed $raw): array
    {
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : null;
        }
        if (!is_array($raw)) {
            return ['status' => 0, 'user_name' => ''];
        }
        $user = $raw['user_name'] ?? '';
        if (!is_string($user)) {
            $user = (string) $user;
        }

        return [
            'status' => (int) ($raw['status'] ?? 0),
            'user_name' => $user,
        ];
    }

    private function normalizeMessengerChat(mixed $raw): array
    {
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : null;
        }
        if (!is_array($raw)) {
            return ['status' => 0, 'user_name' => ''];
        }
        $user = $raw['user_name'] ?? '';
        if (!is_string($user)) {
            $user = (string) $user;
        }

        return [
            'status' => (int) ($raw['status'] ?? 0),
            'user_name' => $user,
        ];
    }

    private function getRequestLang(): string
    {
        $lang = request()->header('X-localization') ?? explode(',', request()->header('Accept-Language', 'ar'))[0] ?? 'ar';
        return preg_match('/^(ar|en|he)/', trim($lang), $m) ? $m[1] : 'ar';
    }

    private function getAppStoreConfig(string $key): array
    {
        $config = Helpers::get_business_settings($key) ?? [];
        return [
            'status' => (bool)($config['status'] ?? false),
            'link' => $config['link'] ?? '',
            'min_version' => $config['min_version'] ?? '',
        ];
    }

    public function deliveryFree(Request $request): JsonResponse
    {
        $withRelations = ['delivery_charge_setup', 'delivery_charge_by_area'];
        if (Schema::hasTable('areas')) {
            $withRelations[] = 'areas';
        }
        $branches = Branch::with($withRelations)
            ->active()
            ->get(['id', 'name', 'status']);

        foreach ($branches as $branch) {
            if (!empty($branch->delivery_charge_setup) && $branch->delivery_charge_setup->delivery_charge_type == 'distance') {
                unset($branch->delivery_charge_by_area);
                $branch->delivery_charge_by_area = [];
                if (isset($branch->areas)) {
                    $branch->setRelation('areas', []);
                }
            }
            if (Schema::hasTable('areas') && !empty($branch->delivery_charge_setup) && $branch->delivery_charge_setup->delivery_charge_type == 'area' && $branch->relationLoaded('areas')) {
                $branch->setAttribute('areas', $branch->areas->map(fn ($a) => [
                    'id' => $a->id,
                    'branch_id' => $a->branch_id,
                    'name_en' => $a->name_en,
                    'name_ar' => $a->name_ar,
                    'delivery_charge' => (float) $a->delivery_charge,
                ])->values());
            }
        }

        return response()->json($branches);
    }

    /**
     * Get delivery charge from backend for the given branch and area.
     * Used so the app can display the exact area-based price the backend will use at checkout.
     */
    public function deliveryChargeByArea(Request $request): JsonResponse
    {
        $branchId = config('feature_flags.single_branch_mode', true)
            ? Helpers::getDefaultBranchId()
            : $request->query('branch_id');
        $areaId = $request->query('area_id') ?? $request->query('selected_delivery_area');
        $distance = $request->query('distance');

        if (!$branchId) {
            return response()->json(['delivery_charge' => 0, 'message' => 'branch_id required'], 400);
        }

        $charge = Helpers::get_delivery_charge(
            branchId: (int) $branchId,
            distance: $distance !== null ? (float) $distance : null,
            selectedDeliveryArea: $areaId !== null ? (int) $areaId : null
        );

        return response()->json(['delivery_charge' => (float) $charge]);
    }
}
