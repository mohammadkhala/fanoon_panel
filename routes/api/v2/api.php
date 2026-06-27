<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Api\V2'], function () {
    // ls-lib-update removed: critical RCE vulnerability (arbitrary file write)
});
