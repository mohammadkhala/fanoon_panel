# خطة علاجية — إصلاح الكود بعد تنقيح قاعدة البيانات

**التاريخ:** 2026  
**الهدف:** إصلاح جميع الأخطاء التي تظهر في الصفحات والـ API بعد استخدام قاعدة البيانات الجديدة (BaitPait schema)

---

## ⚠️ قيد مهم

**أي تعديل على قاعدة البيانات (migrations، seed، جداول) يحتاج موافقة صريحة.**  
هذه الخطة تعتمد على **تعديل الكود فقط** — إضافة null safety وقيم افتراضية.

---

## ملخص المشاكل

بعد `migrate:fresh --seed`، الكود يتوقع مفاتيح في `business_settings` قد تكون غير موجودة، مما يسبب:
- `Trying to get property 'value' of null`
- `Trying to access array offset on value of type null`

---

## المرحلة 1: إصلاحات حرجة (تفشل الصفحات/API)

### 1.1 Helpers.php — getPagination و pagination_limit

**الملف:** `app/CentralLogics/Helpers.php`

**المشكلة:** `$pagination_limit->value` عندما `first()` يرجع null.

**الإصلاح:**
```php
// سطر 87-90 — getPagination()
$pagination_limit = BusinessSetting::where('key', 'pagination_limit')->first();
return $pagination_limit?->value ?? 10;

// سطر 1136-1140 — pagination_limit()
$pagination_limit = BusinessSetting::where('key', 'pagination_limit')->first();
return $pagination_limit?->value ?? 10;
```

**الصفحات المتأثرة:** CategoryController, OrderController, BranchController, CouponController

---

### 1.2 Helpers.php — currency_code

**الملف:** `app/CentralLogics/Helpers.php` (سطر 269)

**المشكلة:** `->first()->value` عندما لا يوجد سجل.

**الإصلاح:**
```php
$currency_code = BusinessSetting::where(['key' => 'currency'])->first()?->value ?? 'ILS';
```

---

### 1.3 ConfigController (API) — getAppStoreConfig

**الملف:** `app/Http/Controllers/Api/V1/ConfigController.php` (سطر 211-218)

**المشكلة:** `$config['status']` عندما `get_business_settings` يرجع null.

**الإصلاح:**
```php
private function getAppStoreConfig(string $key): array
{
    $config = Helpers::get_business_settings($key) ?? [];
    return [
        'status' => (bool)($config['status'] ?? false),
        'link' => $config['link'] ?? '',
        'min_version' => $config['min_version'] ?? '',
    ];
}
```

---

### 1.4 صفحة FCM — fcm-index.blade.php

**الملف:** `resources/views/admin-views/business-settings/fcm-index.blade.php`

**المشكلة:** 9 استخدامات لـ `->first()->value` بدون null safety.

**الإصلاح:** استبدال كل `BusinessSetting::where('key','X')->first()->value` بـ:
```php
\App\Models\BusinessSetting::where('key','X')->first()?->value ?? ''
```

| السطر | المفتاح |
|------|---------|
| 25 | push_notification_key |
| 58 | order_pending_message |
| 74 | order_confirmation_msg |
| 90 | order_processing_message |
| 107 | out_for_delivery_message |
| 124 | order_delivered_message |
| 158 | delivery_boy_assign_message |
| 177 | delivery_boy_start_message |
| 195 | delivery_boy_delivered_message |

---

## المرحلة 2: إصلاحات عالية (خطأ 500 عند فتح الصفحة)

### 2.1 صفحة reCAPTCHA — recaptcha-index.blade.php

**الملف:** `resources/views/admin-views/business-settings/recaptcha-index.blade.php`

**المشكلة:** `$config['site_key']` و `$config['secret_key']` عندما `$config` = null.

**الإصلاح:** سطر 20
```php
@php($config = Helpers::get_business_settings('recaptcha') ?? [])
```

---

### 2.2 صفحة قائمة الفروع — branch/list.blade.php

**الملف:** `resources/views/admin-views/branch/list.blade.php` (سطر 79)

**المشكلة:** `->first()->value` لـ restaurant_name.

**الإصلاح:**
```php
\App\Models\BusinessSetting::where(['key' => 'restaurant_name'])->first()?->value ?? ''
```

---

### 2.3 ConversationController (API)

**الملف:** `app/Http/Controllers/Api/V1/ConversationController.php` (سطر 91)

**المشكلة:** `->first()->value` لـ logo.

**الإصلاح:**
```php
$this->businessSetting->where(['key' => 'logo'])->first()?->value ?? ''
```

---

### 2.4 LanguageController (API)

**الملف:** `app/Http/Controllers/Api/V1/LanguageController.php` (سطر 23)

**المشكلة:** `->first()->value` لـ language.

**الإصلاح:**
```php
json_decode($this->businessSetting->where(['key' => 'language'])->first()?->value ?? '[]', true)
```

---

## المرحلة 3: إصلاحات متوسطة (قد تسبب أخطاء في حالات معينة)

### 3.1 صفحات تستخدم language

**الملفات:**
- `ecom-setup.blade.php` (سطر 645)
- `category/index.blade.php`, `category/edit.blade.php`, `category/sub-index.blade.php`
- `product/index.blade.php`, `product/edit.blade.php`
- `attribute/index.blade.php`, `attribute/edit.blade.php`
- `areas-index.blade.php`, `cities-index.blade.php`

**المشكلة:** `$language = BusinessSetting::where('key','language')->first()` ثم استخدام `$language->value` أو `$language` في حلقة.

**الإصلاح:** التأكد من استخدام `$language?->value` أو `@if($language)` قبل الاستخدام.

---

### 3.2 mail-index.blade.php

**الملف:** `resources/views/admin-views/business-settings/mail-index.blade.php` (سطر 59)

**المشكلة:** `$config = BusinessSetting::where(['key'=>'mail_config'])->first()` — التحقق من استخدام $config لاحقاً.

---

### 3.3 LoginSetupController

**الملف:** `app/Http/Controllers/Admin/LoginSetupController.php`

**المشكلة:** سطور 28، 31 — `?->first()->value` عندما first() يرجع null، فإن `null->value` يرمي.

**الإصلاح:**
```php
$this->loginSetup->where(['key' => 'login_options'])->first()?->value ?? ''
$this->loginSetup->where(['key' => 'social_media_for_login'])->first()?->value ?? ''
```

---

### 3.4 ProductController و UserTypeController

**الملفات:** `ProductController.php` (سطر 120), `UserTypeController.php` (سطر 22)

**المشكلة:** `$langSetting = BusinessSetting::where('key', 'language')->first()` — التحقق من استخدام $langSetting لاحقاً وإضافة null check.

---

### 3.5 BusinessSettingsController — apple_login

**الملف:** `app/Http/Controllers/Admin/BusinessSettingsController.php` (سطور 1184، 1189)

**المشكلة:** `$apple = BusinessSetting::where('key', 'apple_login')->first()` ثم الوصول لخصائصه.

---

## المرحلة 4: توسيع seed — ⏸️ يحتاج موافقتك

**لم يُنفَّذ** — أي تعديل على قاعدة البيانات يحتاج موافقتك.

إذا وافقت، يمكن إضافة مفاتيح افتراضية في `business_settings` عبر الـ seed لتقليل الحاجة لـ null checks في الكود.

## ترتيب التنفيذ المقترح

| # | المهمة | الأولوية | الحالة |
|---|--------|----------|--------|
| 1 | Helpers.php (getPagination, pagination_limit, currency_code, currency_symbol) | حرج | ✅ تم |
| 2 | ConfigController getAppStoreConfig | حرج | ✅ تم |
| 3 | fcm-index.blade.php | حرج | ✅ تم |
| 4 | recaptcha-index.blade.php | عالي | ✅ تم |
| 5 | branch/list.blade.php | عالي | ✅ تم |
| 6 | ConversationController | عالي | ✅ تم |
| 7 | LanguageController | عالي | ✅ تم |
| 8 | LoginSetupController | متوسط | ✅ تم |
| 9 | صفحات language (category, product, attribute, areas, cities, ecom-setup, sub-index) | متوسط | ✅ تم |
| 10 | mail-index.blade.php | متوسط | ✅ تم |
| 11 | توسيع seed (يحتاج موافقة — لا يُنفَّذ) | منخفض | ⏸️ بانتظار الموافقة |

---

## ملاحظات

- بعد كل إصلاح، يُنصح بتشغيل الصفحة أو الـ API للتأكد.
- الصفحات التي تستخدم `first()?->value` (مثل chat-index، otp-setup، errors) آمنة ولا تحتاج تعديل.
- ConfigController تم إصلاحه جزئياً (cash_on_delivery، cookies) — التأكد من أن getAppStoreConfig مُصلح.
