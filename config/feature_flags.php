<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Force cash-only and hide third-party settings
    |--------------------------------------------------------------------------
    | When true:
    | - Admin: Payment methods, reCAPTCHA, Push notification, Firebase message
    |   config, Social media login/chat, Firebase auth are hidden from the 3rd
    |   Party menu and their routes redirect to Business Settings.
    | - API config: Only cash on delivery is exposed; digital payment, social
    |   login, Firebase OTP, and push-related config are disabled for the app.
    | - Admin/Branch login: reCAPTCHA is not shown.
    | - Map/Google Maps: مرفوض — الخارطة غير مستخدمة في النظام.
    | Set to false to re-enable all of the above.
    */

    'force_cash_only_and_hide_third_party' => env('FORCE_CASH_ONLY_AND_HIDE_THIRD_PARTY', false),

    /*
    |--------------------------------------------------------------------------
    | Hide and disable Send Notifications page
    |--------------------------------------------------------------------------
    | When true: the "Send notification" menu item is hidden in the admin
    | sidebar and all notification add/edit/store/update/status/delete routes
    | return 403. Set to false to re-enable the send notifications page.
    */

    'hide_send_notification_page' => env('HIDE_SEND_NOTIFICATION_PAGE', false),

    /*
    |--------------------------------------------------------------------------
    | Show SMS Module in 3rd Party menu
    |--------------------------------------------------------------------------
    | When false: the SMS Module link is hidden from the 3rd Party submenu.
    | The page and routes remain; set to true (or SHOW_SMS_MODULE=true in .env)
    | to show the link again.
    */

    'show_sms_module' => env('SHOW_SMS_MODULE', false),

    /*
    |--------------------------------------------------------------------------
    | Single branch mode
    |--------------------------------------------------------------------------
    | When true: النظام يعمل بفرع واحد فقط (branch_id=1). إدارة الفروع مخفية،
    | وكل الاستعلامات تستخدم الفرع الافتراضي. لا تعديل على جداول الفروع.
    */

    'single_branch_mode' => env('SINGLE_BRANCH_MODE', true),

    /*
    |--------------------------------------------------------------------------
    | Hide branch management
    |--------------------------------------------------------------------------
    | When true: إخفاء قائمة الفروع، إضافة فرع، تعديل فرع من لوحة التحكم.
    */

    'hide_branch_management' => env('HIDE_BRANCH_MANAGEMENT', true),

    /*
    |--------------------------------------------------------------------------
    | Hide Webhooks
    |--------------------------------------------------------------------------
    | When true: رابط Webhooks مخفي من القائمة الجانبية، ومسارات Webhook ترجع 403.
    | ضع HIDE_WEBHOOKS=false في .env لإظهاره عند الحاجة.
    */

    'hide_webhooks' => env('HIDE_WEBHOOKS', true),

];
