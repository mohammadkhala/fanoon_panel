<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    AttributeController,
    BannerController,
    CategoryController,
    TagController,
    ContactUsController,
    ConversationController,
    CouponController,
    CustomerController,
    UserTypeController,
    FlashSaleController,
    LanguageController,
    NotificationController,
    PageController,
    ProductController,
    WishlistController,
    OrderController,
    GuestUserController,
    ConfigController,
    DesignController
};
use App\Http\Controllers\Api\V1\Auth\{
    CustomerAuthController,
    PasswordResetController
};

Route::group(['middleware' => 'localization'], function () {

    Route::post('fcm-subscribe-to-topic', [CustomerController::class, 'fcmSubscribeToTopic']);

    // User types (for registration dropdown; no auth)
    Route::get('user-types', [UserTypeController::class, 'index']);

    // Auth routes (throttle: 30/min - balance between security and UX for registration)
    Route::prefix('auth')->middleware('throttle:30,1')->group(function () {
        Route::controller(CustomerAuthController::class)->group(function () {
            Route::post('send-whatsapp-otp',   'sendWhatsAppOtp');
            Route::post('verify-whatsapp-otp', 'verifyWhatsAppOtp');
            Route::post('registration', 'registration');
            Route::post('login', 'login');
            Route::post('social-customer-login', 'social_customer_login');
            Route::post('check-phone', 'check_phone');
            Route::post('verify-phone', 'verify_phone');
            Route::post('check-email', 'check_email');
            Route::post('verify-email', 'verify_email');
            Route::post('firebase-auth-verify', 'firebaseAuthVerify');
            Route::post('verify-otp', 'verifyOTP');
            Route::post('registration-with-otp', 'registrationWithOTP');
            Route::post('existing-account-check', 'existingAccountCheck');
            Route::post('registration-with-social-media', 'registrationWithSocialMedia');
        });

        Route::controller(PasswordResetController::class)->group(function () {
            Route::post('forgot-password', 'reset_password_request');
            Route::post('verify-token', 'verify_token');
            Route::put('reset-password', 'reset_password_submit');
        });

    });

    // Config
    Route::prefix('config')->controller(ConfigController::class)->group(function () {
        Route::get('/', 'configuration');
        Route::get('delivery-fee', 'deliveryFree');
        Route::get('delivery-charge', 'deliveryChargeByArea');
    });

    // Product routes
    Route::prefix('products')->controller(ProductController::class)->group(function () {
        Route::get('latest', 'getLatestProduct');
        Route::get('discounted', 'getDiscountedProduct');
        Route::get('search', 'getSearchedProduct');
        Route::get('details/{id}', 'getProduct');
        Route::get('related-products/{product_id}', 'getRelatedProduct');
        Route::get('reviews/{product_id}', 'getProductReviews');
        Route::get('rating/{product_id}', 'getProductRating');
        Route::get('new-arrival', 'getNewArrivalProducts');
        Route::post('reviews/submit', 'submitProductReview')->middleware('auth:api');
    });

    // Banners
    Route::prefix('banners')->controller(BannerController::class)->group(function () {
        Route::get('/', 'getBanners');
    });

    // Clients (trusted-by logos shown on storefront homepage)
    Route::prefix('clients')->controller(\App\Http\Controllers\Api\V1\ClientController::class)->group(function () {
        Route::get('/', 'getClients');
    });

    // Design templates (admin-managed Fabric.js templates for the storefront editor)
    Route::prefix('design-templates')->controller(\App\Http\Controllers\Api\V1\DesignTemplateController::class)->group(function () {
        Route::get('/', 'getTemplates');
    });

    // Notifications
    Route::prefix('notifications')->middleware('guest_user')->controller(NotificationController::class)->group(function () {
        Route::get('/', 'getNotifications');
    });

    // Categories (optional auth so user_type filtering works for logged-in users)
    Route::prefix('categories')->controller(CategoryController::class)->middleware('optional_auth_api')->group(function () {
        Route::get('/', 'getCategories');
        Route::get('childes/{category_id}', 'getChildes');
        Route::get('products/{category_id}', 'getProducts');
        Route::get('products/{category_id}/all', 'getAllProducts');
        Route::get('featured', 'getFeaturedCategories');
        Route::get('popular', 'getPopularCategories');
    });

    // Customer related routes
    Route::prefix('customer')->middleware('auth:api')->group(function () {
        Route::controller(CustomerController::class)->group(function () {
            Route::get('info', 'info');
            Route::put('update-profile', 'updateProfile');
            Route::put('cm-firebase-token', 'updateCmFirebaseToken');
            Route::post('verify-profile-info', 'verifyProfileInfo');
            Route::delete('remove-account', 'removeAccount');
            Route::get('loyalty', 'loyalty');
            Route::get('loyalty/history', 'loyaltyHistory');

            Route::prefix('address')->middleware('guest_user')->withoutMiddleware('auth:api')->group(function () {
                Route::get('list', 'addressList');
                Route::post('add', 'addNewAddress');
                Route::put('update/{id}', 'updateAddress');
                Route::delete('delete', 'deleteAddress');
            });
        });
        Route::controller(OrderController::class)->group(function () {
            Route::prefix('order')->middleware(['optional_auth_api', 'guest_user'])->withoutMiddleware('auth:api')->group(function () {
                Route::get('list', 'getOrderList');
                Route::post('details', 'getOrderDetails');
                Route::post('place', 'placeOrder');
                Route::put('cancel', 'cancelOrder');
                Route::post('track', 'trackOrder');
                Route::post('track-by-phone', 'trackByPhone');
                Route::put('payment-method', 'updatePaymentMethod');
            });

            Route::get('reorder/products', 'getReorderProduct');
        });

        Route::prefix('message')->controller(ConversationController::class)->group(function () {
            Route::get('get-admin-message', 'getAdminMessage');
            Route::post('send-admin-message', 'storeAdminMessage');
        });

        Route::prefix('wish-list')->controller(WishlistController::class)->group(function () {
            Route::get('/', 'wishlist');
            Route::post('add', 'addToWishlist');
            Route::delete('remove', 'removeFromWishlist');
        });
    });

    // Coupon
    Route::prefix('coupon')->middleware('guest_user')->controller(CouponController::class)->group(function () {
        Route::get('list', 'list');
        Route::get('apply', 'apply');
    });

    // Language
    Route::prefix('language')->controller(LanguageController::class)->group(function () {
        Route::get('/', 'get');
    });

    // Pages
    Route::get('pages', [PageController::class, 'index']);

    // Contact Us (throttle 10/min per IP to reduce spam)
    Route::post('contact-us', [ContactUsController::class, 'store'])->middleware('throttle:10,1');

    // Tags (for product filter)
    Route::get('tags', [TagController::class, 'apiList']);

    // Attributes (for product filter)
    Route::get('attributes', [AttributeController::class, 'apiList']);

    // Flash Sale
    Route::get('flash-sale', [FlashSaleController::class, 'getFlashSale']);

    // Guest user
    Route::prefix('guest')->controller(GuestUserController::class)->group(function () {
        Route::post('add', 'guestStore');
    });

    // Design editor — رفع صورة التصميم المخصص
    Route::prefix('design')->controller(DesignController::class)->group(function () {
        Route::post('upload', 'upload');
    });

});
