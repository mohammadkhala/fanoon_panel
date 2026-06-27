<?php

namespace App\Http\Controllers\Admin;

use App\Models\Currency;
use App\Models\SocialMedia;
use App\Traits\UploadSizeHelperTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class BusinessSettingsController extends Controller
{
    use UploadSizeHelperTrait;
    public function __construct(
        private BusinessSetting $business_setting,
        private Currency        $currency,
        private SocialMedia     $social_media
    )
    {
    }

    /**
     * @return Application|Factory|View
     */
    public function BusinessSetup(): View|Factory|Application
    {
        if (!$this->business_setting->where(['key' => 'minimum_order_value'])->first()) {
            $this->InsertOrUpdateBusinessData(['key' => 'minimum_order_value'], [
                'value' => 1
            ]);
        }

        if (!$this->business_setting->where(['key' => 'fav_icon'])->first()) {
            $this->InsertOrUpdateBusinessData(['key' => 'fav_icon'], [
                'value' => ''
            ]);
        }

        $logoRaw = Helpers::get_business_settings('logo');
        $logo = Helpers::onErrorImage($logoRaw, asset('storage/ecommerce') . '/' . $logoRaw, asset('assets/admin/img/160x160/img2.jpg'), 'ecommerce/');
        $logo = $this->appendCacheBust($logo);

        $appLogoRaw = Helpers::get_business_settings('app_logo');
        $app_logo = Helpers::onErrorImage($appLogoRaw, asset('storage/ecommerce') . '/' . $appLogoRaw, asset('assets/admin/img/160x160/img2.jpg'), 'ecommerce/');
        $app_logo = $this->appendCacheBust($app_logo);

        $favIconRaw = Helpers::get_business_settings('fav_icon');
        $fav_icon = Helpers::onErrorImage($favIconRaw, asset('storage/ecommerce') . '/' . $favIconRaw, asset('assets/admin/img/160x160/img2.jpg'), 'ecommerce/');
        $fav_icon = $this->appendCacheBust($fav_icon);

        $currencies = \App\Models\Currency::where('currency_code', 'ILS')->get();
        if ($currencies->isEmpty()) {
            $currencies = collect([(object)['currency_code' => 'ILS', 'currency_symbol' => '₪']]);
        }
        $currency_code = Helpers::get_business_settings('currency') ?? 'ILS';

        return view('admin-views.business-settings.ecom-setup', compact('logo', 'app_logo', 'fav_icon', 'currencies', 'currency_code'));
    }

    /**
     * @param $side
     * @return JsonResponse
     */
    public function currencySymbolPosition($side): JsonResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'currency_symbol_position'], [
            'value' => $side
        ]);
        return response()->json(['message' => translate("Symbol position is ") . $side]);
    }

    /**
     * @return JsonResponse
     */
    public function maintenanceMode(): JsonResponse
    {
        $mode = Helpers::get_business_settings('maintenance_mode');
        $this->InsertOrUpdateBusinessData(['key' => 'maintenance_mode'], [
            'value' => isset($mode) ? !$mode : 1
        ]);

        $this->sendMaintenanceModeNotification();
        $this->refreshMaintenanceMiddlewareCache();

        if (!$mode) {
            return response()->json(['message' => translate("Maintenance Mode is On.")]);
        }
        return response()->json(['message' => translate("Maintenance Mode is Off.")]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function BusinessSetupUpdate(Request $request): RedirectResponse
    {
        $this->initUploadLimits();
        $check = $this->validateUploadedFile($request, ['logo', 'app_logo', 'fav_icon']);
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'store_name' => 'required|string|max:255',
            'logo' => 'sometimes|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'app_logo' => 'sometimes|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'fav_icon' => 'sometimes|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ], [
                'logo.mimes' => 'Logo must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
                'logo.max' => translate('Logo size must be below ' . $this->maxImageSizeReadable),
                'app_logo.mimes' => 'App logo must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
                'app_logo.max' => translate('App logo size must be below ' . $this->maxImageSizeReadable),
                'fav_icon.mimes' => 'Fav icon must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
                'fav_icon.max' => translate('Fav icon image size must be below ' . $this->maxImageSizeReadable),
            ]
        );

        if ($request['email_verification'] == 1) {
            $request['phone_verification'] = 0;
        } elseif ($request['phone_verification'] == 1) {
            $request['email_verification'] = 0;
        }

        $this->InsertOrUpdateBusinessData(['key' => 'store_name'], [
            'value' => $request->store_name
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'country'], [
            'value' => strtoupper((string) $request->input('country', 'PS'))
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'phone_verification'], [
            'value' => $request['phone_verification']
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'email_verification'], [
            'value' => $request['email_verification']
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'self_pickup'], [
            'value' => $request['self_pickup']
        ]);

        $currency = 'ILS';
        $this->InsertOrUpdateBusinessData(['key' => 'currency'], [
            'value' => $currency
        ]);

        $curr_logo = $this->business_setting->where(['key' => 'logo'])->first();
        $this->InsertOrUpdateBusinessData(['key' => 'logo'], [
            'value' => $request->has('logo') ? Helpers::update('ecommerce/', $curr_logo['value'], APPLICATION_IMAGE_FORMAT, $request->file('logo')) : $curr_logo['value']
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'phone'], [
            'value' => $request['phone']
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'email_address'], [
            'value' => $request['email']
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'address'], [
            'value' => $request['address']
        ]);


        $this->InsertOrUpdateBusinessData(['key' => 'minimum_order_value'], [
            'value' => $request['minimum_order_value']
        ]);

        $languages = $request['language'];

        // Default language first (Arabic) for product/category translation
        if (in_array('ar', $languages)) {
            unset($languages[array_search('ar', $languages)]);
        }
        array_unshift($languages, 'ar');
        $languages = array_values($languages);

        $this->InsertOrUpdateBusinessData(['key' => 'language'], [
            'value' => json_encode($languages),
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'point_per_currency'], [
            'value' => $request['point_per_currency'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'time_zone'], [
            'value' => 'Asia/Jerusalem',
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'pagination_limit'], [
            'value' => $request['pagination_limit'],
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'loyalty_points_enabled'], [
            'value' => $request->has('loyalty_points_enabled') ? 1 : 0,
        ]);
        if ($request->has('loyalty_amount_for_one_point')) {
            $this->InsertOrUpdateBusinessData(['key' => 'loyalty_amount_for_one_point'], [
                'value' => max(1, (float) $request['loyalty_amount_for_one_point']),
            ]);
        }
        if ($request->has('loyalty_points_per_amount')) {
            $this->InsertOrUpdateBusinessData(['key' => 'loyalty_points_per_amount'], [
                'value' => max(0.5, (float) $request['loyalty_points_per_amount']),
            ]);
        }
        if ($request->has('loyalty_point_redemption_value')) {
            $this->InsertOrUpdateBusinessData(['key' => 'loyalty_point_redemption_value'], [
                'value' => max(0.01, (float) $request['loyalty_point_redemption_value']),
            ]);
        }
        $this->InsertOrUpdateBusinessData(['key' => 'loyalty_and_coupon_together'], [
            'value' => $request->has('loyalty_and_coupon_together') ? 1 : 0,
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'dm_self_registration'], [
            'value' => 0
        ]);

        $curr_fav_icon = $this->business_setting->where(['key' => 'fav_icon'])->first();

        $this->InsertOrUpdateBusinessData(['key' => 'fav_icon'], [
            'value' => $request->has('fav_icon') ? Helpers::update('ecommerce/', $curr_fav_icon['value'], APPLICATION_IMAGE_FORMAT, $request->file('fav_icon')) : $curr_fav_icon['value']
        ]);

        $curr_app_logo = $this->business_setting->where(['key' => 'app_logo'])->first();
        $this->InsertOrUpdateBusinessData(['key' => 'app_logo'], [
            'value' => $request->has('app_logo') ? Helpers::update('ecommerce/', $curr_app_logo['value'], APPLICATION_IMAGE_FORMAT, $request->file('app_logo')) : $curr_app_logo['value']
        ]);

        $guestCheckout = $request->has('guest_checkout') ? 1 : 0;
        $this->InsertOrUpdateBusinessData(['key' => 'guest_checkout'], [
            'value' => $guestCheckout
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function mailIndex(): View|Factory|Application
    {
        return view('admin-views.business-settings.mail-index');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function mailSend(Request $request): JsonResponse
    {
        $response_flag = 0;
        try {
            $emailServices = Helpers::get_business_settings('mail_config');

            if (isset($emailServices['status']) && $emailServices['status'] == 1) {
                Mail::to($request->email)->send(new \App\Mail\TestEmailSender());
                $response_flag = 1;
            }
        } catch (\Exception $exception) {
            $response_flag = 2;
        }

        return response()->json(['success' => $response_flag]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function mailConfig(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'mail_config'],
            [
                'value' => json_encode([
                    "status" => 1,
                    "name" => $request['name'],
                    "host" => $request['host'],
                    "driver" => $request['driver'],
                    "port" => $request['port'],
                    "username" => $request['username'],
                    "email_id" => $request['email'],
                    "encryption" => $request['encryption'],
                    "password" => $request['password']
                ])
            ]);
        Toastr::success(translate('Configuration updated successfully!'));
        return back();
    }

    /**
     * @param $status
     * @return JsonResponse
     */
    public function mailConfigStatus($status): JsonResponse
    {
        $data = Helpers::get_business_settings('mail_config') ?? [];
        $data['status'] = $status == '1' ? 1 : 0;
        $data = array_merge([
            'name' => '', 'host' => '', 'driver' => 'smtp', 'port' => '', 'username' => '',
            'email_id' => '', 'encryption' => '', 'password' => ''
        ], $data);

        $this->InsertOrUpdateBusinessData(['key' => 'mail_config'], [
            'value' => json_encode($data),
        ]);

        return response()->json(['message' => 'Mail config status updated']);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function currencyStore(Request $request): RedirectResponse
    {
        $request->validate([
            'currency_code' => 'required|unique:currencies',
        ]);

        $this->currency->create([
            "country" => $request['country'],
            "currency_code" => $request['currency_code'],
            "currency_symbol" => $request['symbol'],
            "exchange_rate" => $request['exchange_rate'],
        ]);
        Toastr::success(translate('Currency added successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function currencyEdit($id): View|Factory|Application
    {
        $currency = $this->currency->find($id);
        return view('admin-views.business-settings.currency-update', compact('currency'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function currencyUpdate(Request $request, $id): Redirector|RedirectResponse|Application
    {
        $this->currency->where(['id' => $id])->update([
            "country" => $request['country'],
            "currency_code" => $request['currency_code'],
            "currency_symbol" => $request['symbol'],
            "exchange_rate" => $request['exchange_rate'],
        ]);
        Toastr::success(translate('Currency updated successfully!'));
        return redirect('admin/business-settings/currency-add');
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function currencyDelete($id): RedirectResponse
    {
        $this->currency->where(['id' => $id])->delete();
        Toastr::success(translate('Currency removed successfully!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function termsAndConditions(): View|Factory|Application
    {
        $languageSetting = $this->business_setting->where(['key' => 'language'])->first();
        $language = $languageSetting->value ?? null;
        $defaultLang = $language ? json_decode($language)[0] ?? 'ar' : 'ar';
        $langs = $language ? json_decode($language) : ['ar'];

        $tnc = $this->business_setting->where(['key' => 'terms_and_conditions'])->first();
        if (!$tnc) {
            $this->business_setting->insert(['key' => 'terms_and_conditions', 'value' => '']);
            $tnc = (object)['key' => 'terms_and_conditions', 'value' => ''];
        }
        $contentByLang = Helpers::getPageContentByLangs($tnc->value ?? null, $langs);

        return view('admin-views.business-settings.terms-and-conditions', compact('tnc', 'language', 'defaultLang', 'langs', 'contentByLang'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function termsAndConditionsUpdate(Request $request): RedirectResponse
    {
        $content = is_array($request->tnc) ? $request->tnc : [$request->get('default_lang', 'ar') => $request->tnc];
        $this->InsertOrUpdateBusinessData(['key' => 'terms_and_conditions'], [
            'value' => json_encode($content),
        ]);

        Toastr::success(translate('Terms and Conditions updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function privacyPolicy(): View|Factory|Application
    {
        $languageSetting = $this->business_setting->where(['key' => 'language'])->first();
        $language = $languageSetting->value ?? null;
        $defaultLang = $language ? json_decode($language)[0] ?? 'ar' : 'ar';
        $langs = $language ? json_decode($language) : ['ar'];

        $data = $this->business_setting->where(['key' => 'privacy_policy'])->first();
        if (!$data) {
            $this->business_setting->insert(['key' => 'privacy_policy', 'value' => '']);
            $data = (object)['key' => 'privacy_policy', 'value' => ''];
        }
        $contentByLang = Helpers::getPageContentByLangs($data->value ?? null, $langs);

        return view('admin-views.business-settings.privacy-policy', compact('data', 'language', 'defaultLang', 'langs', 'contentByLang'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function privacyPolicyUpdate(Request $request): RedirectResponse
    {
        $content = is_array($request->privacy_policy) ? $request->privacy_policy : [$request->get('default_lang', 'ar') => $request->privacy_policy];
        $this->InsertOrUpdateBusinessData(['key' => 'privacy_policy'], [
            'value' => json_encode($content),
        ]);

        Toastr::success(translate('Privacy policy updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function aboutUs(): View|Factory|Application
    {
        $languageSetting = $this->business_setting->where(['key' => 'language'])->first();
        $language = $languageSetting->value ?? null;
        $defaultLang = $language ? json_decode($language)[0] ?? 'ar' : 'ar';
        $langs = $language ? json_decode($language) : ['ar'];

        $data = $this->business_setting->where(['key' => 'about_us'])->first();
        if (!$data) {
            $this->business_setting->insert(['key' => 'about_us', 'value' => '']);
            $data = (object)['key' => 'about_us', 'value' => ''];
        }
        $contentByLang = Helpers::getPageContentByLangs($data->value ?? null, $langs);

        return view('admin-views.business-settings.about-us', compact('data', 'language', 'defaultLang', 'langs', 'contentByLang'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function aboutUsUpdate(Request $request): RedirectResponse
    {
        $content = is_array($request->about_us) ? $request->about_us : [$request->get('default_lang', 'ar') => $request->about_us];
        $this->InsertOrUpdateBusinessData(['key' => 'about_us'], [
            'value' => json_encode($content),
        ]);

        Toastr::success(translate('About us updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function fcmIndex(): View|Factory|Application|RedirectResponse
    {
        if (config('feature_flags.force_cash_only_and_hide_third_party', false)) {
            Toastr::info(translate('This feature is disabled.'));
            return redirect()->route(config('feature_flags.show_sms_module', false) ? 'admin.business-settings.sms-module' : 'admin.business-settings.mail-config');
        }
        if (!$this->business_setting->where(['key' => 'fcm_topic'])->first()) {
            $this->business_setting->insert([
                'key' => 'fcm_topic',
                'value' => ''
            ]);
        }
        if (!$this->business_setting->where(['key' => 'fcm_project_id'])->first()) {
            $this->business_setting->insert([
                'key' => 'fcm_project_id',
                'value' => ''
            ]);
        }
        if (!$this->business_setting->where(['key' => 'push_notification_key'])->first()) {
            $this->business_setting->insert([
                'key' => 'push_notification_key',
                'value' => ''
            ]);
        }

        $fcmDefaults = [
            'order_pending_message' => 'شكراً لثقتك! استلمنا طلبك ونجهّزه خصيصاً لك — سنؤكد لك فور الجاهزية',
            'order_confirmation_msg' => 'خبر سار! طلبك مؤكد ونحضّره بعناية. سنصل إليك في الموعد — ننتظر رأيك',
            'order_processing_message' => 'طلبك بين أيدينا الآن ونعطيه كل الاهتمام. سنخبرك عند خروجه للتوصيل',
            'out_for_delivery_message' => 'طلبك في الطريق إليك! شكراً لصبرك — سنصل قريباً ونتمنى تجربة رائعة',
            'order_delivered_message' => 'تم التوصيل بنجاح! شكراً لاختيارك لنا. رأيك يهمنا — شاركنا تجربتك',
            'returned_message' => 'شكراً لتواصلك. استلمنا طلب الإرجاع وفريقنا سيتواصل معك خلال 24 ساعة',
            'failed_message' => 'نعتذر عن الإزعاج. واجهنا صعوبة — سنتواصل معك فوراً لترتيب أفضل حل. ثقتك تهمنا',
            'canceled_message' => 'تم تنفيذ طلب الإلغاء. نأمل خدمتك مجدداً — نحن هنا لخدمتك عند حاجتك',
        ];

        foreach ($fcmDefaults as $key => $defaultMessage) {
            if (!$this->business_setting->where(['key' => $key])->first()) {
                $this->business_setting->insert([
                    'key' => $key,
                    'value' => json_encode([
                        'status' => 0,
                        'message' => $defaultMessage
                    ])
                ]);
            }
        }

        return view('admin-views.business-settings.fcm-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateFcm(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'fcm_project_id'], [
            'value' => $request['fcm_project_id']
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'push_notification_key'], [
            'value' => $request['push_notification_key']
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'push_notification_service_file_content'], [
            'value' => $request['push_notification_service_file_content'],
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateFcmMessages(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'order_pending_message'], [
            'value' => json_encode([
                'status' => $request['pending_status'] == 1 ? 1 : 0,
                'message' => $request['pending_message']
            ])
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'order_confirmation_msg'], [
            'value' => json_encode([
                'status' => $request['confirm_status'] == 1 ? 1 : 0,
                'message' => $request['confirm_message']
            ])
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'order_processing_message'], [
            'value' => json_encode([
                'status' => $request['processing_status'] == 1 ? 1 : 0,
                'message' => $request['processing_message']
            ])
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'out_for_delivery_message'], [
            'value' => json_encode([
                'status' => $request['out_for_delivery_status'] == 1 ? 1 : 0,
                'message' => $request['out_for_delivery_message']
            ])
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'order_delivered_message'], [
            'value' => json_encode([
                'status' => $request['delivered_status'] == 1 ? 1 : 0,
                'message' => $request['delivered_message']
            ])
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'returned_message'], [
            'value' => json_encode([
                'status' => $request['returned_status'] == 1 ? 1 : 0,
                'message' => $request['returned_message'],
            ]),
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'failed_message'], [
            'value' => json_encode([
                'status' => $request['failed_status'] == 1 ? 1 : 0,
                'message' => $request['failed_message'],
            ]),
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'canceled_message'], [
            'value' => json_encode([
                'status' => $request['canceled_status'] == 1 ? 1 : 0,
                'message' => $request['canceled_message'],
            ]),
        ]);

        Toastr::success(translate('Message updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function recaptchaIndex(Request $request): RedirectResponse
    {
        Toastr::info(translate('Recaptcha has been removed.'));
        return redirect()->route('admin.business-settings.login-setup');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function recaptchaUpdate(Request $request): RedirectResponse
    {
        Toastr::info(translate('Recaptcha has been removed.'));
        return redirect()->route('admin.business-settings.login-setup');
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function returnPageIndex(Request $request): View|Factory|Application
    {
        $languageSetting = $this->business_setting->where(['key' => 'language'])->first();
        $langs = $languageSetting && $languageSetting->value ? json_decode($languageSetting->value) : ['ar'];
        $defaultLang = $langs[0] ?? 'ar';

        $data = $this->business_setting->where(['key' => 'return_page'])->first();
        if (!$data) {
            $this->business_setting->insert([
                'key' => 'return_page',
                'value' => json_encode(['status' => 0, 'content' => array_fill_keys($langs, '')]),
            ]);
            $data = (object)['key' => 'return_page', 'value' => json_encode(['status' => 0, 'content' => array_fill_keys($langs, '')])];
        }
        $parsed = Helpers::getPageContentWithStatusByLangs($data->value ?? null, $langs);
        $contentByLang = $parsed['content'] ?? array_fill_keys($langs, '');
        $status = $parsed['status'] ?? 0;

        return view('admin-views.business-settings.return_page-index', compact('data', 'langs', 'defaultLang', 'contentByLang', 'status'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function returnPageUpdate(Request $request): RedirectResponse
    {
        $content = is_array($request->content) ? $request->content : [$request->get('default_lang', 'ar') => $request->content ?? ''];
        $this->InsertOrUpdateBusinessData(['key' => 'return_page'], [
            'value' => json_encode([
                'status' => $request['status'] == 1 ? 1 : 0,
                'content' => $content,
            ]),
        ]);

        Toastr::success(translate('Updated Successfully'));
        return back();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function refundPageIndex(Request $request): View|Factory|Application
    {
        $languageSetting = $this->business_setting->where(['key' => 'language'])->first();
        $langs = $languageSetting && $languageSetting->value ? json_decode($languageSetting->value) : ['ar'];
        $defaultLang = $langs[0] ?? 'ar';

        $data = $this->business_setting->where(['key' => 'refund_page'])->first();
        if (!$data) {
            $this->business_setting->insert([
                'key' => 'refund_page',
                'value' => json_encode(['status' => 0, 'content' => array_fill_keys($langs, '')]),
            ]);
            $data = (object)['key' => 'refund_page', 'value' => json_encode(['status' => 0, 'content' => array_fill_keys($langs, '')])];
        }
        $parsed = Helpers::getPageContentWithStatusByLangs($data->value ?? null, $langs);
        $contentByLang = $parsed['content'] ?? array_fill_keys($langs, '');
        $status = $parsed['status'] ?? 0;

        return view('admin-views.business-settings.refund_page-index', compact('data', 'langs', 'defaultLang', 'contentByLang', 'status'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function refundPageUpdate(Request $request): RedirectResponse
    {
        $content = is_array($request->content) ? $request->content : [$request->get('default_lang', 'ar') => $request->content ?? ''];
        $this->InsertOrUpdateBusinessData(['key' => 'refund_page'], [
            'value' => json_encode([
                'status' => $request['status'] == 1 ? 1 : 0,
                'content' => $content,
            ]),
        ]);

        Toastr::success(translate('Updated Successfully'));
        return back();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function cancellationPageIndex(Request $request): View|Factory|Application
    {
        $languageSetting = $this->business_setting->where(['key' => 'language'])->first();
        $langs = $languageSetting && $languageSetting->value ? json_decode($languageSetting->value) : ['ar'];
        $defaultLang = $langs[0] ?? 'ar';

        $data = $this->business_setting->where(['key' => 'cancellation_page'])->first();
        if (!$data) {
            $this->business_setting->insert([
                'key' => 'cancellation_page',
                'value' => json_encode(['status' => 0, 'content' => array_fill_keys($langs, '')]),
            ]);
            $data = (object)['key' => 'cancellation_page', 'value' => json_encode(['status' => 0, 'content' => array_fill_keys($langs, '')])];
        }
        $parsed = Helpers::getPageContentWithStatusByLangs($data->value ?? null, $langs);
        $contentByLang = $parsed['content'] ?? array_fill_keys($langs, '');
        $status = $parsed['status'] ?? 0;

        return view('admin-views.business-settings.cancellation_page-index', compact('data', 'langs', 'defaultLang', 'contentByLang', 'status'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cancellationPageUpdate(Request $request): RedirectResponse
    {
        $content = is_array($request->content) ? $request->content : [$request->get('default_lang', 'ar') => $request->content ?? ''];
        $this->InsertOrUpdateBusinessData(['key' => 'cancellation_page'], [
            'value' => json_encode([
                'status' => $request['status'] == 1 ? 1 : 0,
                'content' => $content,
            ]),
        ]);

        Toastr::success(translate('Updated Successfully'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function appSettingIndex(): View|Factory|Application
    {
        return View('admin-views.business-settings.app-setting-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function appSettingUpdate(Request $request): RedirectResponse
    {
        if ($request->platform == 'android') {
            $this->InsertOrUpdateBusinessData(['key' => 'play_store_config'], [
                'value' => json_encode([
                    'status' => $request['play_store_status'],
                    'link' => $request['play_store_link'],
                    'min_version' => $request['android_min_version'],
                ]),
            ]);

            Toastr::success(translate('Updated Successfully for Android'));
            return back();
        }

        if ($request->platform == 'ios') {
            $this->InsertOrUpdateBusinessData(['key' => 'app_store_config'], [
                'value' => json_encode([
                    'status' => $request['app_store_status'],
                    'link' => $request['app_store_link'],
                    'min_version' => $request['ios_min_version'],
                ]),
            ]);

            Toastr::success(translate('Updated Successfully for IOS'));
            return back();
        }


        Toastr::error(translate('Updated failed'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function firebaseMessageConfigIndex(): View|Factory|Application|RedirectResponse
    {
        if (config('feature_flags.force_cash_only_and_hide_third_party', false)) {
            Toastr::info(translate('This feature is disabled.'));
            return redirect()->route(config('feature_flags.show_sms_module', false) ? 'admin.business-settings.sms-module' : 'admin.business-settings.mail-config');
        }
        return View('admin-views.business-settings.firebase-config-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function firebaseMessageConfig(Request $request): RedirectResponse
    {
        if ($request->has('push_notification_service_file_content')) {
            $this->InsertOrUpdateBusinessData(['key' => 'push_notification_service_file_content'], [
                'value' => $request['push_notification_service_file_content'],
            ]);
        }

        $this->InsertOrUpdateBusinessData(['key' => 'firebase_message_config'], [
            'value' => json_encode([
                'apiKey' => $request['apiKey'] ?? '',
                'authDomain' => $request['authDomain'] ?? '',
                'projectId' => $request['projectId'] ?? '',
                'storageBucket' => $request['storageBucket'] ?? '',
                'messagingSenderId' => $request['messagingSenderId'] ?? '',
                'appId' => $request['appId'] ?? '',
            ]),
        ]);

        self::firebaseMessageConfigFileGenerate();

        Toastr::success(translate('Config Updated Successfully'));
        return back();
    }

    /**
     * @return void
     */
    function firebaseMessageConfigFileGenerate(): void
    {
        $config = \App\CentralLogics\Helpers::get_business_settings('firebase_message_config');
        $apiKey = $config['apiKey'] ?? '';
        $authDomain = $config['authDomain'] ?? '';
        $projectId = $config['projectId'] ?? '';
        $storageBucket = $config['storageBucket'] ?? '';
        $messagingSenderId = $config['messagingSenderId'] ?? '';
        $appId = $config['appId'] ?? '';

        try {
            $old_file = fopen("firebase-messaging-sw.js", "w") or die("Unable to open file!");

            $new_text = "importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');\n";
            $new_text .= "importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');\n";
            $new_text .= 'firebase.initializeApp({apiKey: "' . $apiKey . '",authDomain: "' . $authDomain . '",projectId: "' . $projectId . '",storageBucket: "' . $storageBucket . '", messagingSenderId: "' . $messagingSenderId . '", appId: "' . $appId . '"});';
            $new_text .= "\nconst messaging = firebase.messaging();\n";
            $new_text .= "messaging.setBackgroundMessageHandler(function (payload) { return self.registration.showNotification(payload.data.title, { body: payload.data.body ? payload.data.body : '', icon: payload.data.icon ? payload.data.icon : '' }); });";
            $new_text .= "\n";

            fwrite($old_file, $new_text);
            fclose($old_file);

        } catch (\Exception $exception) {
        }

    }

    /**
     * @return Application|Factory|View
     */
    public function socialMedia(): View|Factory|Application
    {
        return view('admin-views.business-settings.social-media');
    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function fetch(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->social_media->orderBy('id', 'desc')->get();
            return response()->json($data);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function socialMediaStore(Request $request): JsonResponse
    {
        try {
            $this->social_media->updateOrInsert([
                'name' => $request->get('name'),
            ], [
                'name' => $request->get('name'),
                'link' => $request->get('link'),
            ]);

            return response()->json([
                'success' => 1,
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'error' => 1,
            ]);
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function socialMediaEdit(Request $request): JsonResponse
    {
        $data = $this->social_media->where('id', $request->id)->first();
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function socialMediaUpdate(Request $request): JsonResponse
    {
        $socialMedia = $this->social_media->find($request->id);
        $socialMedia->name = $request->name;
        $socialMedia->link = $request->link;
        $socialMedia->save();
        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function socialMediaDelete(Request $request): JsonResponse
    {
        $socialMedia = $this->social_media->find($request->id);
        $socialMedia->delete();
        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function socialMediaStatusUpdate(Request $request): JsonResponse
    {
        $socialMedia = $this->social_media->find($request->id);
        $socialMedia->status = $socialMedia->status == 1 ? 0 : 1;
        $socialMedia->save();

        return response()->json([
            'success' => 1,
        ], 200);
    }

    /**
     * @return Application|Factory|View
     */
    public function otpIndex(): Factory|View|Application
    {
        return view('admin-views.business-settings.otp-setup');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'maximum_otp_hit' => 'required|integer|min:1|max:20',
            'otp_resend_time' => 'required|integer|min:30|max:600',
            'temporary_block_time' => 'required|integer|min:60|max:3600',
            'maximum_login_hit' => 'required|integer|min:1|max:20',
            'temporary_login_block_time' => 'required|integer|min:60|max:3600',
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'maximum_otp_hit'], [
            'value' => $request['maximum_otp_hit'],
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'otp_resend_time'], [
            'value' => $request['otp_resend_time'],
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'temporary_block_time'], [
            'value' => $request['temporary_block_time'],
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'maximum_login_hit'], [
            'value' => $request['maximum_login_hit'],
        ]);
        $this->InsertOrUpdateBusinessData(['key' => 'temporary_login_block_time'], [
            'value' => $request['temporary_login_block_time'],
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function cookiesSetup(): Factory|View|Application
    {
        $languageSetting = $this->business_setting->where(['key' => 'language'])->first();
        $language = $languageSetting->value ?? null;
        $defaultLang = $language ? json_decode($language)[0] ?? 'ar' : 'ar';
        $langs = $language ? json_decode($language) : ['ar'];

        $cookies = Helpers::get_business_settings('cookies');
        $textRaw = $cookies['text'] ?? null;
        if (is_array($textRaw)) {
            $contentByLang = array_fill_keys($langs, '');
            foreach ($langs as $l) {
                $contentByLang[$l] = (string) ($textRaw[$l] ?? '');
            }
        } else {
            $contentByLang = Helpers::getPageContentByLangs($textRaw, $langs);
        }

        $cookiesDefaults = [
            'ar' => 'نستخدم ملفات تعريف الارتباط لتحسين تجربتك على الموقع وتذكر تفضيلاتك. يمكنك تعطيلها من إعدادات المتصفح. بمتابعة التصفح، فإنك توافق على استخدامنا لملفات تعريف الارتباط.',
            'en' => 'We use cookies to improve your site experience and remember your preferences. You can disable them from your browser settings. By continuing to browse, you agree to our use of cookies.',
        ];
        foreach ($langs as $l) {
            if (trim($contentByLang[$l] ?? '') === '') {
                $contentByLang[$l] = $cookiesDefaults[$l] ?? $cookiesDefaults['ar'];
            }
        }

        return view('admin-views.business-settings.cookies-setup', compact('cookies', 'langs', 'defaultLang', 'contentByLang'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cookiesSetupUpdate(Request $request): RedirectResponse
    {
        $text = is_array($request->text) ? $request->text : [$request->get('default_lang', 'ar') => $request->text];
        $this->InsertOrUpdateBusinessData(['key' => 'cookies'], [
            'value' => json_encode([
                'status' => $request['status'] ?? 0,
                'text' => $text,
            ])
        ]);

        \Illuminate\Support\Facades\Cache::forget('api_v1_configuration_payload_v1');

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function socialMediaLogin(): RedirectResponse
    {
        Toastr::info(translate('Apple login is disabled. Use Customer Login setup for Google.'));
        return redirect()->route('admin.business-settings.login-setup');
    }

    public function updateAppleLogin(Request $request): RedirectResponse
    {
        $this->initUploadLimits('files');
        $check = $this->validateUploadedFile($request, ['service_file']);
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'service_file' => 'sometimes|max:'. $this->maxImageSizeKB .'|extensions:p8',
        ],[
            'service_file.extensions' => 'Service file must be a file of type: p8',
            'service_file.max' => translate('Service file size must be below ' . $this->maxImageSizeReadable),
        ]);

        $appleLogin = Helpers::get_business_settings('apple_login');

        if ($request->hasFile('service_file')) {
            $fileName = Helpers::uploadFile('apple-login/', $request->file('service_file'));
        }

        $data = [
            'value' => json_encode([
                'login_medium' => 'apple',
                'client_id' => $request['client_id'],
                'client_secret' => '',
                'team_id' => $request['team_id'],
                'key_id' => $request['key_id'],
                'service_file' => $fileName ?? $appleLogin['service_file'],
                'redirect_url' => '',
                'status' => $request->has('status') ? 1 : 0,
            ]),
        ];

        $this->InsertOrUpdateBusinessData(['key' => 'apple_login'], $data);

        Toastr::success(translate('settings updated!'));
        return back();
    }

    /**
     * @param $medium
     * @param $status
     * @return JsonResponse
     */
    public function changeSocialLoginStatus($medium, $status): JsonResponse
    {
        if ($medium == 'google') {
            $this->InsertOrUpdateBusinessData(['key' => 'google_social_login'], [
                'value' => $status
            ]);
        } elseif ($medium == 'facebook') {
            $this->InsertOrUpdateBusinessData(['key' => 'facebook_social_login'], [
                'value' => $status
            ]);
        }
        return response()->json(['message' => 'Status updated']);
    }

    /**
     * @return Application|Factory|View
     */
    public function socialMediaChat(): Factory|View|Application
    {
        if (!$this->business_setting->where(['key' => 'whatsapp'])->first()) {
            $this->business_setting->insert([
                'key' => 'whatsapp',
                'value' => json_encode([
                    'status' => 0,
                    'number' => '',
                ]),
            ]);
        }

        if (!$this->business_setting->where(['key' => 'telegram'])->first()) {
            $this->business_setting->insert([
                'key' => 'telegram',
                'value' => json_encode([
                    'status' => 0,
                    'user_name' => '',
                ]),
            ]);
        }

        if (!$this->business_setting->where(['key' => 'messenger'])->first()) {
            $this->business_setting->insert([
                'key' => 'messenger',
                'value' => json_encode([
                    'status' => 0,
                    'user_name' => '',
                ]),
            ]);
        }
        return view('admin-views.business-settings.chat-index');
    }

    public function updateSocialMediaChat(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'whatsapp_number' => 'required_if:whatsapp_status,1|nullable|regex:/^[0-9]{9,15}$/',
            'telegram_user_name' => 'required_if:telegram_status,1',
            'messenger_user_name' => 'required_if:messenger_status,1',
        ], [
            'whatsapp_number.required_if' => 'The WhatsApp number is required when WhatsApp status is set to active.',
            'whatsapp_number.regex' => 'The WhatsApp number must be 9-15 digits (country code + number, without +).',
            'telegram_user_name.required_if' => 'The Telegram username is required when Telegram status is set to active.',
            'messenger_user_name.required_if' => 'The Messenger username is required when Messenger status is set to active.',
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'whatsapp'], [
            'value' => json_encode([
                'status' => $request->has('whatsapp_status') ? 1 : 0,
                'number' => $request->input('whatsapp_number', ''),
            ]),
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'telegram'], [
            'value' => json_encode([
                'status' => $request->has('telegram_status') ? 1 : 0,
                'user_name' => $request->input('telegram_user_name', ''),
            ]),
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'messenger'], [
            'value' => json_encode([
                'status' => $request->has('messenger_status') ? 1 : 0,
                'user_name' => $request->input('messenger_user_name', ''),
            ]),
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    public function storeLocationMap(): Factory|View|Application
    {
        if (! $this->business_setting->where(['key' => 'store_google_maps_url'])->first()) {
            $this->InsertOrUpdateBusinessData(['key' => 'store_google_maps_url'], ['value' => '']);
        }
        $raw = Helpers::get_business_settings('store_google_maps_url');
        $storeGoogleMapsUrl = is_string($raw) ? $raw : '';
        $mapPreviewEmbedSrc = Helpers::googleMapsStoreUrlToEmbedSrc(old('store_google_maps_url', $storeGoogleMapsUrl));

        return view('admin-views.business-settings.store-map-location', compact('storeGoogleMapsUrl', 'mapPreviewEmbedSrc'));
    }

    public function updateStoreLocationMap(Request $request): RedirectResponse
    {
        $request->validate([
            'store_google_maps_url' => [
                'nullable',
                'string',
                'max:2048',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    if (! preg_match('#^https?://#i', trim((string) $value))) {
                        $fail(translate('store_google_maps_url_invalid'));
                    }
                },
            ],
        ]);

        $raw = trim((string) $request->input('store_google_maps_url', ''));
        $normalized = $raw === '' ? '' : Helpers::normalizeStoreGoogleMapsUrl($raw);

        $this->InsertOrUpdateBusinessData(['key' => 'store_google_maps_url'], [
            'value' => $normalized,
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    public function maintenanceModeSetup(Request $request): RedirectResponse
    {
        $this->InsertOrUpdateBusinessData(['key' => 'maintenance_mode'], [
            'value' => $request->has('maintenance_mode') ? 1 : 0
        ]);

        $allSystems = ['branch_panel', 'customer_app', 'web_app', 'deliveryman_app'];

        $this->InsertOrUpdateBusinessData(['key' => 'maintenance_system_setup'], [
            'value' => json_encode($allSystems),
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'maintenance_duration_setup'], [
            'value' => json_encode([
                'maintenance_duration' => $request['maintenance_duration'],
                'start_date' => $request['start_date'] ?? null,
                'end_date' => $request['end_date'] ?? null,
            ]),
        ]);

        $this->InsertOrUpdateBusinessData(['key' => 'maintenance_message_setup'], [
            'value' => json_encode([
                'business_number' => $request->has('business_number') ? 1 : 0,
                'business_email' => $request->has('business_email') ? 1 : 0,
                'maintenance_message' => $request['maintenance_message'],
                'message_body' => $request['message_body']
            ]),
        ]);

        $this->refreshMaintenanceMiddlewareCache();

        $this->sendMaintenanceModeNotification();

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    private function sendMaintenanceModeNotification(): void
    {
        $data = [
            'title' => translate('Maintenance Mode Settings Updated'),
            'description' => translate('Maintenance Mode Settings Updated'),
            'type' => 'maintenance',
        ];

        try {
            Helpers::sendPushNotifToTopicForMaintenanceMode($data, 'market');
            Helpers::sendPushNotifToTopicForMaintenanceMode($data, "market-deliveryman");
        } catch (\Exception $e) {
            //
        }
    }

    /**
     * Syncs {@see MaintenanceModeMiddleware} cache from DB (used by branch web routes).
     */
    private function refreshMaintenanceMiddlewareCache(): void
    {
        $maintenanceStatus = (int) (Helpers::get_business_settings('maintenance_mode') ?? 0);
        $selectedMaintenanceDuration = Helpers::get_business_settings('maintenance_duration_setup') ?? [];
        if (! is_array($selectedMaintenanceDuration)) {
            $selectedMaintenanceDuration = [];
        }
        $selectedMaintenanceSystem = Helpers::get_business_settings('maintenance_system_setup') ?? [];
        if (! is_array($selectedMaintenanceSystem)) {
            $selectedMaintenanceSystem = [];
        }
        $isBranch = in_array('branch_panel', $selectedMaintenanceSystem, true) ? 1 : 0;

        $messages = Helpers::get_business_settings('maintenance_message_setup') ?? [];
        if (! is_array($messages)) {
            $messages = [];
        }

        $maintenance = [
            'status' => $maintenanceStatus,
            'start_date' => $selectedMaintenanceDuration['start_date'] ?? null,
            'end_date' => $selectedMaintenanceDuration['end_date'] ?? null,
            'branch_panel' => $isBranch,
            'maintenance_duration' => $selectedMaintenanceDuration['maintenance_duration'] ?? null,
            'maintenance_messages' => $messages,
        ];

        Cache::put('maintenance', $maintenance, now()->addDays(30));
    }

    public function firebaseAuth(): View|Factory|Application|RedirectResponse
    {
        if (config('feature_flags.force_cash_only_and_hide_third_party', false)) {
            Toastr::info(translate('This feature is disabled.'));
            return redirect()->route(config('feature_flags.show_sms_module', false) ? 'admin.business-settings.sms-module' : 'admin.business-settings.mail-config');
        }
        return view('admin-views.business-settings.firebase-auth');
    }

    public function updateFirebaseAuth(Request $request)
    {
        $this->InsertOrUpdateBusinessData(['key' => 'firebase_otp_verification'], [
            'value' => json_encode([
                'status' => $request->has('status') ? 1 : 0,
                'web_api_key' => $request->input('web_api_key', ''),
            ]),
        ]);

        Toastr::success(translate('updated_successfully'));
        return back();
    }

    /**
     * @param $key
     * @param $value
     * @return void
     */
    /**
     * Append cache-bust query string to image URL so browser loads fresh after upload.
     */
    private function appendCacheBust(string $url): string
    {
        $sep = str_contains($url, '?') ? '&' : '?';
        return $url . $sep . 'v=' . time();
    }

    private function InsertOrUpdateBusinessData($key, $value): void
    {
        $businessSetting = $this->business_setting->where(['key' => $key['key']])->first();
        if ($businessSetting) {
            $businessSetting->value = $value['value'];
            $businessSetting->save();
        } else {
            $data = [
                'key' => $key['key'],
                'value' => $value['value'],
            ];
            $this->business_setting->create($data);
        }
    }

}
