<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'storage/*', 'images/*', 'image-proxy'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    /*
    | في الإنتاج: حدد CORS_ALLOWED_ORIGINS في .env
    | مثال: https://elitevape.online,https://www.elitevape.online,http://localhost:8090
    | لإضافة localhost للتطوير: أضف http://localhost:8090 و http://127.0.0.1:8090
    */
    'allowed_origins' => env('CORS_ALLOWED_ORIGINS')
        ? array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS')))
        : (config('app.env') === 'production' ? array_filter([config('app.url')]) : ['*']),

    /*
    | أنماط للنطاقات — يسمح بـ localhost بأي منفذ للتطوير
    | مثال: http://localhost:54481, http://localhost:8090, http://127.0.0.1:*
    */
    'allowed_origins_patterns' => [
        '#^https?://localhost(:\d+)?$#',
        '#^https?://127\.0\.0\.1(:\d+)?$#',
        // أناغيم: الموقع الرئيسي، لوحة الإدارة، وتطبيقات ويب/Flutter على نفس النطاق
        '#^https://([a-z0-9-]+\.)?anagheemhome\.com$#',
        '#^https://([a-z0-9-]+\.)?divabloom\.store$#',
    ],

    /*
    | هيدرات مسموحة صراحة — بعض المتصفحات لا تقبل * في preflight
    | مطلوب لـ Flutter/Dio: Authorization, Content-Type, X-localization, guest-id
    */
    'allowed_headers' => [
        'Authorization',
        'Content-Type',
        'Accept',
        'X-Requested-With',
        'X-localization',
        'Origin',
        'Accept-Language',
        'guest-id',
    ],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => false,

];
