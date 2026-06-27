<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WaSender WhatsApp API
    | Docs: https://wasenderapi.com/api-docs
    |--------------------------------------------------------------------------
    |
    | api_key  → Bearer token from WaSender dashboard (Session Management)
    | session  → Session name configured in WaSender (informational only)
    | store_phone → The WhatsApp number connected to this session
    |
    */

    'api_key'     => env('WASENDER_API_KEY', ''),
    'session'     => env('WASENDER_SESSION', 'baitpait'),
    'store_phone' => env('WASENDER_STORE_PHONE', '+970599814754'),
    'enabled'     => env('WASENDER_ENABLED', true),
];
