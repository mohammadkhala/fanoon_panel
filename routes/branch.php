<?php

use App\Http\Controllers\Branch\Auth\LoginController;
use App\Http\Controllers\Branch\OrderController;
use App\Http\Controllers\Branch\SystemController;
use Illuminate\Support\Facades\Route;


Route::group(['as' => 'branch.', 'middleware' => 'maintenance_mode'], function () {
    /*authentication*/
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('/code/captcha/{tmp}', [LoginController::class,'captcha'])->name('default-captcha');
        Route::get('login', [LoginController::class,'login'])->name('login');
        Route::post('login', [LoginController::class,'submit']);
        Route::get('logout', [LoginController::class,'logout'])->name('logout');
    });
    /*authentication*/

    Route::group(['middleware' => ['branch']], function () {
        Route::get('/', [SystemController::class, 'dashboard'])->name('dashboard');

        Route::get('settings', [SystemController::class, 'settings'])->name('settings');
        Route::post('settings', [SystemController::class, 'settingsUpdate']);
        Route::post('settings-password', [SystemController::class, 'settingsPasswordUpdate'])->name('settings-password');
        Route::post('order-stats', [SystemController::class, 'orderStats'])->name('order-stats');
        Route::get('/get-store-data', [SystemController::class, 'storeData'])->name('get-store-data');
        Route::get('/get-restaurant-data', fn () => redirect()->route('branch.get-store-data', [], 301))->name('get-restaurant-data'); // توافق رجعي
        Route::get('dashboard/earning-statistics', [SystemController::class, 'getEarningStatistics'])->name('dashboard.earning-statistics');
        Route::get('ignore-check-order', [SystemController::class, 'ignoreCheckOrder'])->name('ignore-check-order');

        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
            Route::get('list/{status}', [OrderController::class, 'list'])->name('list');
            Route::get('details/{id}', [OrderController::class, 'details'])->name('details');
            Route::get('status', [OrderController::class, 'status'])->name('status');
            Route::get('payment-status', [OrderController::class, 'paymentStatus'])->name('payment-status');
            Route::get('generate-invoice/{id}', [OrderController::class, 'generateInvoice'])->name('generate-invoice');
            Route::post('add-payment-ref-code/{id}', [OrderController::class, 'addPaymentRefCode'])->name('add-payment-ref-code');
            Route::get('export/{status}', [OrderController::class, 'exportOrders'])->name('export');
            Route::get('search-product', [OrderController::class, 'searchProduct'])->name('search-product');
            Route::post('update-product-list/{id}', [OrderController::class, 'updateProductList'])->name('update-product-list');
            Route::post('update-shipping/{id}', [OrderController::class, 'updateShipping'])->name('update-shipping');
            Route::get('quick-view', [OrderController::class, 'quickView'])->name('quick-view');
            Route::get('quick-view-modal-footer', [OrderController::class, 'quickViewModalFooter'])->name('quick-view-modal-footer');
            Route::post('variant_price', [OrderController::class, 'variantPrice'])->name('variant_price');
            Route::post('add-to-cart', [OrderController::class, 'addToCart'])->name('add-to-cart');
            Route::get('pos-invoice/{id}', [OrderController::class, 'generatePosInvoice'])->name('pos-invoice');
        });
    });
});
