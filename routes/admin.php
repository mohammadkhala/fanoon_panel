<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DesignTemplateController;
use App\Http\Controllers\Admin\BusinessSettingsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewsController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\UserTypeController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\LoginSetupController;
use App\Http\Controllers\Admin\DatabaseSettingsController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\ContactUsController;
use App\Http\Controllers\Admin\HelpController;
use App\Http\Controllers\Admin\ShippingCompanyController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\WebhookController;
use App\Http\Controllers\Admin\DeliveryChargeSetupController;
use App\Http\Controllers\Admin\TemplateController;
Route::group(['as' => 'admin.'], function () {
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('/code/captcha/{tmp}', [LoginController::class, 'captcha'])->name('default-captcha');
        Route::get('login', [LoginController::class, 'login'])->name('login');
        Route::post('login', [LoginController::class, 'submit'])->middleware('activation-check');
        Route::get('logout', [LoginController::class, 'logout'])->name('logout');
    });

    Route::group(['middleware' => ['admin']], function () {
        // إعدادات المتجر/الفرع الافتراضي — يجب أن تبقى مسجّلة دائماً (قائمة Business Setup تربط بها).
        // عند hide_branch_management: باقي مسارات الفروع تعيد التوجيه إلى هنا.
        Route::group(['prefix' => 'branch', 'as' => 'branch.'], function () {
            Route::get('settings', [BranchController::class, 'settings'])->name('settings');
            Route::put('settings', [BranchController::class, 'settingsUpdate'])->name('settings-update');

            if (config('feature_flags.hide_branch_management', true)) {
                $redirect = fn () => redirect()->route('admin.branch.settings');
                Route::any('list', $redirect)->name('list');
                Route::any('add', $redirect)->name('add');
                Route::any('edit/{id}', $redirect)->name('edit');
                Route::any('store', $redirect)->name('store');
                Route::any('update/{id}', $redirect)->name('update');
                Route::any('delete/{id}', $redirect)->name('delete');
                Route::any('status/{id}/{status}', $redirect)->name('status');
            } else {
                Route::get('list', [BranchController::class, 'list'])->name('list');
                Route::get('add', [BranchController::class, 'index'])->name('add');
                Route::post('add', [BranchController::class, 'store'])->name('store');
                Route::get('edit/{id}', [BranchController::class, 'edit'])->name('edit');
                Route::put('edit/{id}', [BranchController::class, 'update'])->name('update');
                Route::delete('delete/{id}', [BranchController::class, 'delete'])->name('delete');
                Route::get('status/{id}/{status}', [BranchController::class, 'status'])->name('status');
            }
        });

        Route::get('/', [SystemController::class, 'dashboard'])->name('dashboard');

        Route::post('order-stats', [SystemController::class, 'orderStats'])->name('order-stats');
        Route::get('settings', [SystemController::class, 'settings'])->name('settings');
        Route::post('settings', [SystemController::class, 'settingsUpdate']);
        Route::post('settings-password', [SystemController::class, 'settingsPasswordUpdate'])->name('settings-password');
        Route::get('/get-store-data', [SystemController::class, 'storeData'])->name('get-store-data');
        Route::get('/get-restaurant-data', fn () => redirect()->route('admin.get-store-data', [], 301))->name('get-restaurant-data'); // توافق رجعي
        Route::get('dashboard/earning-statistics', [SystemController::class, 'getEarningStatistics'])->name('dashboard.earning-statistics');
        Route::get('unified-search', [SystemController::class, 'unifiedSearch'])->name('unified-search')->middleware('throttle:30,1');
        Route::get('ignore-check-order', [SystemController::class, 'ignoreCheckOrder'])->name('ignore-check-order');
        Route::get('ignore-check-contact', [SystemController::class, 'ignoreCheckContact'])->name('ignore-check-contact');
        Route::get('lang/{locale}', [SystemController::class, 'switchLang'])->name('lang.switch')->where('locale', 'en|ar');

        Route::get('help', [HelpController::class, 'index'])->name('help.index');

        Route::group(['prefix' => 'banner', 'as' => 'banner.'], function () {
            Route::get('add-new', [BannerController::class, 'index'])->name('add-new');
            Route::post('store', [BannerController::class, 'store'])->name('store');
            Route::get('edit/{id}', [BannerController::class, 'edit'])->name('edit');
            Route::put('update/{id}', [BannerController::class, 'update'])->name('update');
            Route::get('list', [BannerController::class, 'list'])->name('list');
            Route::get('status/{id}/{status}', [BannerController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [BannerController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'client', 'as' => 'client.'], function () {
            Route::get('add-new', [ClientController::class, 'index'])->name('add-new');
            Route::post('store', [ClientController::class, 'store'])->name('store');
            Route::get('edit/{id}', [ClientController::class, 'edit'])->name('edit');
            Route::put('update/{id}', [ClientController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [ClientController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [ClientController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'design-template', 'as' => 'design-template.'], function () {
            Route::get('add-new', [DesignTemplateController::class, 'index'])->name('add-new');
            Route::get('from-order/{detail}', [DesignTemplateController::class, 'createFromOrderDetail'])->name('from-order');
            Route::get('by-category', [DesignTemplateController::class, 'byCategory'])->name('by-category');
            Route::post('store', [DesignTemplateController::class, 'store'])->name('store');
            Route::get('edit/{id}', [DesignTemplateController::class, 'edit'])->name('edit');
            Route::put('update/{id}', [DesignTemplateController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [DesignTemplateController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [DesignTemplateController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'templates', 'as' => 'templates.'], function () {
            Route::get('/', [TemplateController::class, 'index'])->name('index');
            Route::get('create', [TemplateController::class, 'create'])->name('create');
            Route::post('/', [TemplateController::class, 'store'])->name('store');
            Route::get('{productTemplate}/edit', [TemplateController::class, 'edit'])->name('edit');
            Route::put('{productTemplate}', [TemplateController::class, 'update'])->name('update');
            Route::patch('{productTemplate}/toggle', [TemplateController::class, 'toggle'])->name('toggle');
            Route::delete('{productTemplate}', [TemplateController::class, 'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'attribute', 'as' => 'attribute.'], function () {
            Route::get('add-new', [AttributeController::class, 'index'])->name('add-new');
            Route::post('store', [AttributeController::class, 'store'])->name('store');
            Route::get('edit/{id}', [AttributeController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [AttributeController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [AttributeController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'tag', 'as' => 'tag.'], function () {
            Route::get('list', [TagController::class, 'list'])->name('list');
            Route::post('store', [TagController::class, 'store'])->name('store');
            Route::post('update/{id}', [TagController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [TagController::class, 'delete'])->name('delete');
            Route::get('search', [TagController::class, 'search'])->name('search');
        });

        Route::group(['prefix' => 'notification', 'as' => 'notification.'], function () {
            Route::get('add-new', [NotificationController::class, 'index'])->name('add-new');
            Route::post('store', [NotificationController::class, 'store'])->name('store');
            Route::get('edit/{id}', [NotificationController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [NotificationController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [NotificationController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [NotificationController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'contact-us', 'as' => 'contact-us.'], function () {
            Route::get('/', [ContactUsController::class, 'index'])->name('index');
            Route::get('{id}', [ContactUsController::class, 'show'])->name('show');
            Route::delete('{id}', [ContactUsController::class, 'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
            Route::get('add-new', [ProductController::class, 'index'])->name('add-new');
            Route::post('translate', [ProductController::class, 'translate'])->name('translate');
            Route::post('variant-combination', [ProductController::class, 'variantCombination'])->name('variant-combination');
            Route::post('store', [ProductController::class, 'store'])->name('store');
            Route::get('edit/{id}', [ProductController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [ProductController::class, 'update'])->name('update');
            Route::get('list', [ProductController::class, 'list'])->name('list');
            Route::delete('delete/{id}', [ProductController::class, 'delete'])->name('delete');
            Route::get('status/{id}/{status}', [ProductController::class, 'status'])->name('status');
            Route::post('quick-stock/{id}', [ProductController::class, 'quickStockUpdate'])->name('quick-stock');
            Route::post('search', [ProductController::class, 'search'])->name('search');
            Route::get('autocomplete', [ProductController::class, 'autocomplete'])->name('autocomplete');
            Route::get('bulk-import', [ProductController::class, 'bulkImportIndex'])->name('bulk-import');
            Route::post('bulk-import', [ProductController::class, 'bulkImportProduct']);
            Route::get('bulk-export', [ProductController::class, 'bulkExportProduct'])->name('bulk-export');
            Route::get('view/{id}', [ProductController::class, 'view'])->name('view');
            Route::get('get-categories', [ProductController::class, 'getCategories'])->name('get-categories');
            Route::get('remove-image/{id}/{name}', [ProductController::class, 'removeImage'])->name('remove-image');
        });

        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
            Route::get('create', [OrderController::class, 'create'])->name('create');
            Route::get('list/{status}', [OrderController::class, 'list'])->name('list');
            Route::get('details/{id}', [OrderController::class, 'details'])->name('details');
            Route::get('status', [OrderController::class, 'status'])->name('status');
            Route::get('payment-status', [OrderController::class, 'paymentStatus'])->name('payment-status');
            Route::get('generate-invoice/{id}', [OrderController::class, 'generateInvoice'])->name('generate-invoice');
            Route::post('add-payment-ref-code/{id}', [OrderController::class, 'addPaymentReferenceCode'])->name('add-payment-ref-code');
            Route::get('branch-filter/{branch_id}', [OrderController::class, 'branchFilter'])->name('branch-filter');
            Route::get('export/{status}', [OrderController::class, 'exportOrders'])->name('export');
            Route::get('search-product', [OrderController::class, 'searchProduct'])->name('search-product');
            Route::post('update-product-list/{id}', [OrderController::class, 'updateProductList'])->name('update-product-list');
            Route::post('update-shipping/{id}', [OrderController::class, 'updateShipping'])->name('update-shipping');
            Route::post('{id}/add-shipment', [ShippingCompanyController::class, 'addShipment'])->name('add-shipment');
            Route::post('shipment/{shipmentId}/update', [ShippingCompanyController::class, 'updateShipment'])->name('update-shipment');
            Route::get('quick-view', [OrderController::class, 'quickView'])->name('quick-view');
            Route::get('quick-view-modal-footer', [OrderController::class, 'quickViewModalFooter'])->name('quick-view-modal-footer');
            Route::post('variant_price', [OrderController::class, 'variantPrice'])->name('variant_price');
            Route::post('add-to-cart', [OrderController::class, 'addToCart'])->name('add-to-cart');
            Route::get('pos-invoice/{id}', [OrderController::class, 'generatePosInvoice'])->name('pos-invoice');
            Route::post('{id}/add-update', [OrderController::class, 'addCustomStatus'])->name('add-update');
        });

        Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
            Route::get('add', [CategoryController::class, 'index'])->name('add');
            Route::get('add-sub-category', [CategoryController::class, 'subIndex'])->name('add-sub-category');
            Route::post('store', [CategoryController::class, 'store'])->name('store');
            Route::get('edit/{id}', [CategoryController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [CategoryController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [CategoryController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [CategoryController::class, 'delete'])->name('delete');
            Route::post('search', [CategoryController::class, 'search'])->name('search');
            Route::get('featured/{id}/{featured}', [CategoryController::class, 'featured'])->name('featured');
        });

        Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
            Route::get('list', [ReviewsController::class, 'list'])->name('list');
            Route::delete('delete/{id}', [ReviewsController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'coupon', 'as' => 'coupon.'], function () {
            Route::get('add-new', [CouponController::class, 'index'])->name('add-new');
            Route::post('store', [CouponController::class, 'store'])->name('store');
            Route::get('update/{id}', [CouponController::class, 'edit'])->name('update');
            Route::post('update/{id}', [CouponController::class, 'update']);
            Route::get('status/{id}/{status}', [CouponController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [CouponController::class, 'delete'])->name('delete');
            Route::get('details', [CouponController::class, 'details'])->name('details');
        });

        Route::group(['prefix' => 'flash-sale', 'as' => 'flash-sale.'], function () {
            Route::get('index', [FlashSaleController::class, 'index'])->name('index');
            Route::post('store', [FlashSaleController::class, 'store'])->name('store');
            Route::get('edit/{id}', [FlashSaleController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [FlashSaleController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [FlashSaleController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [FlashSaleController::class, 'delete'])->name('delete');

            Route::get('add-product/{flash_sale_id}', [FlashSaleController::class, 'addProduct'])->name('add-product');
            Route::get('add-product-to-session/{flash_sale_id}/{product_id}', [FlashSaleController::class, 'addProductToSession'])->name('add-product-to-session');
            Route::get('delete-product-from-session/{flash_sale_id}/{product_id}', [FlashSaleController::class, 'deleteProductFromSession'])->name('delete-product-from-session');
            Route::get('delete-all-products-from-session/{flash_sale_id}', [FlashSaleController::class, 'deleteAllProductsFromSession'])->name('delete-all-products-from-session');
            Route::post('add-flash-sale-product/{flash_sale_id}', [FlashSaleController::class, 'flashSaleProductStore'])->name('add_flash_sale_product');
            Route::delete('product/delete/{flash_sale_id}/{product_id}', [FlashSaleController::class, 'deleteFlashProduct'])->name('product.delete');
        });

        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.','middleware'=>['activation-check']], function () {
            Route::get('ecom-setup', [BusinessSettingsController::class, 'BusinessSetup'])->name('ecom-setup');
            Route::post('update-setup', [BusinessSettingsController::class, 'BusinessSetupUpdate'])->name('update-setup');

            Route::get('fcm-index', [BusinessSettingsController::class, 'fcmIndex'])->name('fcm-index');
            Route::post('update-fcm', [BusinessSettingsController::class, 'updateFcm'])->name('update-fcm');

            Route::post('update-fcm-messages', [BusinessSettingsController::class, 'updateFcmMessages'])->name('update-fcm-messages');

            Route::get('mail-config', [BusinessSettingsController::class, 'mailIndex'])->name('mail-config');
            Route::post('mail-send',  [BusinessSettingsController::class, 'mailSend'])->name('mail-send');
            Route::post('mail-config', [BusinessSettingsController::class, 'mailConfig']);
            Route::get('mail-config/status/{status}', [BusinessSettingsController::class, 'mailConfigStatus'])->name('mail-config.status');

            Route::get('currency-add', [BusinessSettingsController::class, 'currency_index'])->name('currency-add');
            Route::post('currency-add', [BusinessSettingsController::class, 'currencyStore']);
            Route::get('currency-update/{id}', [BusinessSettingsController::class, 'currencyEdit'])->name('currency-update');
            Route::put('currency-update/{id}', [BusinessSettingsController::class, 'currencyUpdate']);
            Route::delete('currency-delete/{id}', [BusinessSettingsController::class, 'currencyDelete'])->name('currency-delete');

            Route::get('terms-and-conditions', [BusinessSettingsController::class, 'termsAndConditions'])->name('terms-and-conditions');
            Route::post('terms-and-conditions', [BusinessSettingsController::class, 'termsAndConditionsUpdate']);

            Route::get('privacy-policy', [BusinessSettingsController::class, 'privacyPolicy'])->name('privacy-policy');
            Route::post('privacy-policy',  [BusinessSettingsController::class, 'privacyPolicyUpdate']);

            Route::get('about-us', [BusinessSettingsController::class, 'aboutUs'])->name('about-us');
            Route::post('about-us', [BusinessSettingsController::class, 'aboutUsUpdate']);

            Route::get('firebase-message-config', [BusinessSettingsController::class, 'firebaseMessageConfigIndex'])->name('firebase_message_config_index');
            Route::post('firebase-message-config', [BusinessSettingsController::class, 'firebaseMessageConfig'])->name('firebase_message_config');

            Route::get('recaptcha', [BusinessSettingsController::class, 'recaptchaIndex'])->name('recaptcha_index');
            Route::post('recaptcha-update', [BusinessSettingsController::class, 'recaptchaUpdate'])->name('recaptcha_update');

            Route::get('return-page', [BusinessSettingsController::class, 'returnPageIndex'])->name('return_page_index');
            Route::post('return-page-update', [BusinessSettingsController::class, 'returnPageUpdate'])->name('return_page_update');

            Route::get('refund-page', [BusinessSettingsController::class, 'refundPageIndex'])->name('refund_page_index');
            Route::post('refund-page-update', [BusinessSettingsController::class, 'refundPageUpdate'])->name('refund_page_update');

            Route::get('cancellation-page', [BusinessSettingsController::class, 'cancellationPageIndex'])->name('cancellation_page_index');
            Route::post('cancellation-page-update', [BusinessSettingsController::class, 'cancellationPageUpdate'])->name('cancellation_page_update');

            Route::get('currency-position/{position}', [BusinessSettingsController::class, 'currencySymbolPosition'])->name('currency-position');
            Route::get('maintenance-mode', [BusinessSettingsController::class, 'maintenanceMode'])->name('maintenance-mode');

            Route::get('social-media', [BusinessSettingsController::class, 'socialMedia'])->name('social-media');
            Route::get('fetch', [BusinessSettingsController::class, 'fetch'])->name('fetch');
            Route::post('social-media-store', [BusinessSettingsController::class, 'socialMediaStore'])->name('social-media-store');
            Route::post('social-media-edit', [BusinessSettingsController::class, 'socialMediaEdit'])->name('social-media-edit');
            Route::post('social-media-update', [BusinessSettingsController::class, 'socialMediaUpdate'])->name('social-media-update');
            Route::post('social-media-delete', [BusinessSettingsController::class, 'socialMediaDelete'])->name('social-media-delete');
            Route::post('social-media-status-update', [BusinessSettingsController::class, 'socialMediaStatusUpdate'])->name('social-media-status-update');

            Route::get('otp-setup', [BusinessSettingsController::class, 'otpIndex'])->name('otp-setup');
            Route::post('update-otp', [BusinessSettingsController::class ,'updateOtp'])->name('update-otp');

            Route::get('cookies-setup', [BusinessSettingsController::class, 'cookiesSetup'])->name('cookies-setup');
            Route::post('update-cookies', [BusinessSettingsController::class, 'cookiesSetupUpdate'])->name('update-cookies');

            Route::get('social-media-login', [BusinessSettingsController::class, 'socialMediaLogin'])->name('social-media-login');
            Route::get('social_login_status/{medium}/{status}', [BusinessSettingsController::class, 'changeSocialLoginStatus'])->name('social_login_status');
            Route::post('update-apple-login-setup', [BusinessSettingsController::class, 'updateAppleLogin'])->name('update-apple-login-setup');

            Route::get('social-media-chat', [BusinessSettingsController::class, 'socialMediaChat'])->name('social-media-chat');
            Route::post('update-social-media-chat', [BusinessSettingsController::class, 'updateSocialMediaChat'])->name('update-social-media-chat');

            Route::get('store-location-map', [BusinessSettingsController::class, 'storeLocationMap'])->name('store-location-map');
            Route::post('update-store-location-map', [BusinessSettingsController::class, 'updateStoreLocationMap'])->name('update-store-location-map');

            Route::get('areas', [\App\Http\Controllers\Admin\AreaController::class, 'index'])->name('areas');
            Route::post('areas', [\App\Http\Controllers\Admin\AreaController::class, 'store'])->name('areas.store');
            Route::put('areas/{id}', [\App\Http\Controllers\Admin\AreaController::class, 'update'])->name('areas.update');
            Route::delete('areas/{id}', [\App\Http\Controllers\Admin\AreaController::class, 'destroy'])->name('areas.destroy');
            Route::get('cities', [\App\Http\Controllers\Admin\CityController::class, 'index'])->name('cities');
            Route::post('cities', [\App\Http\Controllers\Admin\CityController::class, 'store'])->name('cities.store');
            Route::put('cities/{id}', [\App\Http\Controllers\Admin\CityController::class, 'update'])->name('cities.update');
            Route::delete('cities/{id}', [\App\Http\Controllers\Admin\CityController::class, 'destroy'])->name('cities.destroy');

            Route::get('login-setup', [LoginSetupController::class, 'loginSetup'])->name('login-setup');
            Route::post('login-setup-update', [LoginSetupController::class, 'loginSetupUpdate'])->name('login-setup-update');
            Route::get('check-active-social-media', [LoginSetupController::class, 'checkActiveSocialMedia'])->name('check-active-social-media');

            Route::post('maintenance-mode-setup', [BusinessSettingsController::class, 'maintenanceModeSetup'])->name('maintenance-mode-setup')->middleware('activation-check');

            Route::get('firebase-auth', [BusinessSettingsController::class, 'firebaseAuth'])->name('firebase-auth');
            Route::post('update-firebase-auth', [BusinessSettingsController::class, 'updateFirebaseAuth'])->name('update-firebase-auth');

            Route::get('app-setting', [BusinessSettingsController::class, 'appSettingIndex'])->name('app_setting');
            Route::post('app-setting', [BusinessSettingsController::class, 'appSettingUpdate'])->name('app_setting.update');

            // مناطق التوصيل (area-wise delivery charges)
            Route::get('delivery-fee', [DeliveryChargeSetupController::class, 'deliveryFeeSetup'])->name('delivery-fee');
            Route::post('delivery-charge-type', [DeliveryChargeSetupController::class, 'changeDeliveryChargeType'])->name('delivery-charge-type');
            Route::post('area-wise-charge', [DeliveryChargeSetupController::class, 'StoreAreaWiseDeliveryCharge'])->name('area-wise-charge');
            Route::put('area-wise-charge/{id}', [DeliveryChargeSetupController::class, 'updateAreaWiseDeliveryCharge'])->name('area-wise-charge.update');
            Route::delete('area-wise-charge/{id}', [DeliveryChargeSetupController::class, 'deleteAreaWiseDeliveryCharge'])->name('area-wise-charge.delete');
            Route::post('fixed-charge', [DeliveryChargeSetupController::class, 'storeFixedDeliveryCharge'])->name('fixed-charge');
            Route::post('km-charge', [DeliveryChargeSetupController::class, 'storeKilometerWiseDeliveryCharge'])->name('km-charge');

        });

        Route::group(['prefix' => 'shipping-company', 'as' => 'shipping-company.', 'middleware' => ['activation-check']], function () {
            Route::get('/', [ShippingCompanyController::class, 'index'])->name('index');
            Route::get('create', [ShippingCompanyController::class, 'create'])->name('create');
            Route::post('/', [ShippingCompanyController::class, 'store'])->name('store');
            Route::get('{id}/edit', [ShippingCompanyController::class, 'edit'])->name('edit');
            Route::put('{id}', [ShippingCompanyController::class, 'update'])->name('update');
            Route::delete('{id}', [ShippingCompanyController::class, 'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'webhook', 'as' => 'webhook.'], function () {
            Route::get('list', [WebhookController::class, 'list'])->name('list');
            Route::get('add', [WebhookController::class, 'create'])->name('add');
            Route::post('store', [WebhookController::class, 'store'])->name('store');
            Route::get('edit/{webhook}', [WebhookController::class, 'edit'])->name('edit');
            Route::put('update/{webhook}', [WebhookController::class, 'update'])->name('update');
            Route::delete('delete/{webhook}', [WebhookController::class, 'delete'])->name('delete');
            Route::get('status/{webhook}', [WebhookController::class, 'status'])->name('status');
        });

        Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
            Route::get('order', [ReportController::class, 'orderIndex'])->name('order');
            Route::get('earning', [ReportController::class, 'earningIndex'])->name('earning');
            Route::post('set-date', [ReportController::class, 'setDate'])->name('set-date');
            Route::get('product-report', [ReportController::class, 'productReport'])->name('product-report');
            Route::get('export-product-report', [ReportController::class, 'exportProductReport'])->name('export-product-report');
            Route::get('sale-report', [ReportController::class, 'saleReport'])->name('sale-report');
            Route::get('export-sale-report', [ReportController::class, 'exportSaleReport'])->name('export-sale-report');
            Route::get('best-selling-products', [ReportController::class, 'bestSellingProducts'])->name('best-selling-products');
            Route::get('export-best-selling-products', [ReportController::class, 'exportBestSellingProducts'])->name('export-best-selling-products');
            Route::get('top-customers', [ReportController::class, 'topCustomers'])->name('top-customers');
            Route::get('export-top-customers', [ReportController::class, 'exportTopCustomers'])->name('export-top-customers');
        });

        Route::group(['prefix' => 'user-types', 'as' => 'user-type.'], function () {
            Route::get('/', [UserTypeController::class, 'index'])->name('index');
            Route::post('/', [UserTypeController::class, 'store'])->name('store');
            Route::put('{id}', [UserTypeController::class, 'update'])->name('update');
            Route::delete('{id}', [UserTypeController::class, 'destroy'])->name('destroy');
            Route::post('{id}/set-default', [UserTypeController::class, 'setDefault'])->name('set-default');
        });

        Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
            Route::get('list', [CustomerController::class, 'customerList'])->name('list');
            Route::get('export', [CustomerController::class, 'exportCustomers'])->name('export');
            Route::get('view/{user_id}', [CustomerController::class, 'view'])->name('view');
            Route::post('view/{id}/update-type', [CustomerController::class, 'updateType'])->name('update-type');
            Route::post('view/{id}/approve-type', [CustomerController::class, 'approveType'])->name('approve-type');
        });

        Route::get('database/download', [DatabaseSettingsController::class, 'download'])->name('database.download');

    });
});
