# تقرير المشاكل بعد تعديل قاعدة البيانات

**التاريخ:** 2026  
**السياق:** بعد تنفيذ `migrate:fresh --seed` باستخدام `2026_01_01_000000_baitpait_full_schema.php` و `BaitPaitSeeder`

---

## ✅ تم إكمال الترحيل — لا توجد أخطاء متبقية من قاعدة البيانات القديمة

تم التخلص من جميع المشاكل المرتبطة بقاعدة البيانات القديمة:
- لا توجد مراجع لـ `addon_settings` (تم حذفه)
- لا توجد مراجع لصفحات الدفع أو SMS أو الخارطة (تم حذفها)
- قاعدة البيانات الحالية نظيفة ومتوافقة مع الكود

---

## ملخص تنفيذي

المشاكل المتبقية هي **تحسينات في الكود** (null safety، قيم افتراضية) وليست أخطاء من قاعدة البيانات القديمة:

1. **نقص بيانات `business_settings`** — يُنصح بتوسيع الـ seed لإضافة مفاتيح افتراضية (تحسين، ليس خطأ ترحيل).
2. **استخدام `->first()->value` بدون التحقق من null** — يُنصح بإضافة null safety في الكود.
3. **وصول لمفاتيح مصفوفة على قيم null** في ConfigController — يُنصح بإضافة isset/??.

---

## 1. بيانات business_settings الناقصة

### ما يُزرع حالياً في `seedMinimalData`:
- `restaurant_name`
- `currency`
- `delivery_charge`
- `maintenance_duration_setup`
- `maintenance_message_setup`

### مفاتيح مطلوبة وغير موجودة (تسبب أخطاء أو سلوك غير متوقع):

| المفتاح | الاستخدام | التأثير عند الغياب |
|--------|-----------|---------------------|
| `pagination_limit` | Helpers::getPagination(), Controllers | **خطأ:** Trying to get property 'value' of null |
| `push_notification_key` | fcm-index | **خطأ:** Trying to get property 'value' of null |
| `order_pending_message` | fcm-index | **خطأ** |
| `order_confirmation_msg` | fcm-index | **خطأ** |
| `order_processing_message` | fcm-index | **خطأ** |
| `out_for_delivery_message` | fcm-index | **خطأ** |
| `order_delivered_message` | fcm-index | **خطأ** |
| `delivery_boy_assign_message` | fcm-index | **خطأ** |
| `delivery_boy_start_message` | fcm-index | **خطأ** |
| `delivery_boy_delivered_message` | fcm-index | **خطأ** |
| `play_store_config` | app-setting-index ✅, ConfigController API | app-setting: مُصلح. ConfigController: يحتاج فحص |
| `app_store_config` | app-setting-index ✅, ConfigController API | app-setting: مُصلح. ConfigController: يحتاج فحص |
| `cash_on_delivery` | ConfigController API | **خطأ** عند الوصول لـ `['status']` (الدفع نقداً عند الاستلام فقط) |
| `digital_payment` | ConfigController API | معطّل — الدفع الإلكتروني مُزال |
| `cookies` | ConfigController API | **خطأ** |
| `recaptcha` | recaptcha-index | **خطأ:** $config['site_key'] على null |
| `phone` | ecom-setup, invoice, ConfigController | قيمة فارغة |
| `email_address` | ecom-setup, invoice, ConfigController | قيمة فارغة |
| `address` | ecom-setup, invoice, ConfigController | قيمة فارغة |
| `country` | ecom-setup, ConfigController | يُستخدم ?? 'PS' في بعض المواضع |
| `minimum_order_value` | ecom-setup, ConfigController | قيمة فارغة |
| `self_pickup` | ecom-setup, ConfigController | قيمة فارغة |
| `currency_symbol_position` | ecom-setup, ConfigController | يُستخدم ?? 'right' في بعض المواضع |
| `guest_checkout` | ecom-setup, ConfigController | يُستخدم ?? 0 |
| `time_zone` | AppServiceProvider | آمن (يوجد isset) |
| `fav_icon` | layouts, errors | آمن (يُستخدم optional أو ?->) |
| `logo` | sidebar, invoice | قيمة فارغة |
| `app_logo` | ConfigController | قيمة فارغة |
| `language` | category, product, attribute, areas, cities | قد يسبب أخطاء في الصفحات |
| `terms_and_conditions` | ConfigController | null |
| `privacy_policy` | ConfigController | null |
| `about_us` | ConfigController | null |
| `maintenance_mode` | ecom-setup, ConfigController | يُستخدم ?? 0 |
| `mail_config` | ConfigController, BusinessSettings | قد يسبب أخطاء |
| `firebase_otp_verification` | firebase-auth, ConfigController | null — **لا يُستخدم لتسجيل دخول الزبون** (الدخول فقط عبر Google/Facebook/Apple) |
| `google_social_login` | ConfigController | **مطلوب** — تسجيل دخول الزبون |
| `facebook_social_login` | ConfigController | **مطلوب** — تسجيل دخول الزبون |
| `apple_login` | ConfigController | **مطلوب** — تسجيل دخول الزبون |
| `whatsapp` | chat-index, ConfigController | آمن (?->) |
| `telegram` | chat-index, ConfigController | آمن |
| `messenger` | chat-index, ConfigController | آمن |
| `otp_resend_time` | otp-setup, ConfigController | يُستخدم ?? 60 |
| `loyalty_points_enabled` | ecom-setup, ConfigController | يُستخدم ?? 0 |
| `loyalty_amount_for_one_point` | ecom-setup, ConfigController | يُستخدم ?? 10 |
| `loyalty_points_per_amount` | ecom-setup, ConfigController | يُستخدم ?? 1 |
| `loyalty_point_redemption_value` | ecom-setup, ConfigController | يُستخدم ?? 0.5 |
| `minimum_stock_alert` | Product::lowStock | تمت إضافته في migration |

---

## 2. الصفحات المتأثرة (تفصيل)

### 2.1 صفحة FCM (إعدادات الإشعارات) — `fcm-index.blade.php`

**الملف:** `resources/views/admin-views/business-settings/fcm-index.blade.php`

**المشكلة:** استخدام `->first()->value` بدون التحقق من null.

| السطر | الكود | المفتاح |
|------|-------|---------|
| 25 | `BusinessSetting::where('key','push_notification_key')->first()->value` | push_notification_key |
| 58 | `->first()->value` | order_pending_message |
| 74 | `->first()->value` | order_confirmation_msg |
| 90 | `->first()->value` | order_processing_message |
| 107 | `->first()->value` | out_for_delivery_message |
| 124 | `->first()->value` | order_delivered_message |
| 158 | `->first()->value` | delivery_boy_assign_message |
| 177 | `->first()->value` | delivery_boy_start_message |
| 195 | `->first()->value` | delivery_boy_delivered_message |

**الخطأ المتوقع:** `Trying to get property 'value' of null`

---

### 2.2 صفحة إعدادات التطبيق — `app-setting-index.blade.php`

**الملف:** `resources/views/admin-views/business-settings/app-setting-index.blade.php`

**الحالة:** ✅ تم إصلاحه — استخدام `$config ?? []` لتفادي null.

---

### 2.3 صفحة reCAPTCHA — `recaptcha-index.blade.php`

**الملف:** `resources/views/admin-views/business-settings/recaptcha-index.blade.php`

**المشكلة:** `$config['site_key']` و `$config['secret_key']` عندما `$config` = null (سطور 51، 57).

**الخطأ المتوقع:** `Trying to access array offset on value of type null`

---

### 2.4 صفحة قائمة الفروع — `branch/list.blade.php`

**الملف:** `resources/views/admin-views/branch/list.blade.php`

**السطر 79:**  
`\App\Models\BusinessSetting::where(['key' => 'restaurant_name'])->first()->value`

**الحالة:** `restaurant_name` موجود في seedMinimalData، لذا الصفحة تعمل حالياً. لكن أي تغيير لاحق قد يكسرها إذا أُزيل المفتاح.

---

### ~~2.5 صفحة الدفع~~ — تم حذفها

### ~~2.6 صفحة SMS~~ — تم حذفها

### 2.5 صفحة إعدادات المتجر — `ecom-setup.blade.php`

**الملف:** `resources/views/admin-views/business-settings/ecom-setup.blade.php`

- `pagination_limit`, `minimum_order_value`, `self_pickup`, `currency_symbol_position`, `guest_checkout` إلخ: تُستخدم مع `get_business_settings` التي ترجع null.
- معظم الحقول تستخدم `??` أو مقارنات آمنة، لكن حقول مثل `pagination_limit` و `minimum_order_value` قد تظهر فارغة مع `required`، مما يمنع حفظ النموذج بدون إدخال يدوي.

---

## 3. Controllers و Helpers المتأثرة

### 3.1 Helpers::getPagination() و Helpers::pagination_limit()

**الملف:** `app/CentralLogics/Helpers.php`

```php
// سطر 87-90
$pagination_limit = BusinessSetting::where('key', 'pagination_limit')->first();
return $pagination_limit->value;  // يرمي إذا first() = null

// سطر 1136-1140
$pagination_limit = BusinessSetting::where('key', 'pagination_limit')->first();
return $pagination_limit->value;  // نفس المشكلة
```

**الصفحات المتأثرة (موثقة في laravel.log):**
- CategoryController (سطر 96)
- OrderController (سطر 50)
- BranchController
- CouponController

**الخطأ:** `Trying to get property 'value' of null`

---

### 3.2 Helpers::currency_code()

**الملف:** `app/CentralLogics/Helpers.php` (سطر 269)

```php
$currency_code = BusinessSetting::where(['key' => 'currency'])->first()->value;
```

**الحالة:** `currency` موجود في seedMinimalData، لذا يعمل حالياً.

---

### 3.3 ConfigController (API) — حرج

**الملف:** `app/Http/Controllers/Api/V1/ConfigController.php`

هذا الـ endpoint يُستدعى من التطبيق عند بدء التشغيل. عند غياب الإعدادات، يرمي أخطاء متعددة:

| السطر | الكود | المفتاح | الخطأ |
|------|-------|---------|-------|
| 54 | `$cod = get_business_settings('cash_on_delivery')` | cash_on_delivery | |
| 90 | `$cod['status']` | | Trying to access array offset on null |
| 55 | `$dp = get_business_settings('digital_payment')` | digital_payment | |
| 91 | `$dp['status']` | | Trying to access array offset on null |
| 57-59 | `$cookiesConfig['status']`, `['text']` | cookies | Trying to access array offset on null |
| 110-112 | `$apple['login_medium']`, `['client_id']` | apple_login | Trying to access array offset on null |
| 153-155 | `play_store_config['status']`, `['link']`, `['min_version']` | play_store_config | Trying to access array offset on null |
| 157-159 | `app_store_config[...]` | app_store_config | Trying to access array offset on null |
| 173 | `$digitalPaymentStatusValue['status']` | digital_payment | Trying to access array offset on null |
| 106 | `$emailConfig['status']` | mail_config | قد يرمي إذا null |

**التأثير:** فشل API الإعدادات، وعدم قدرة التطبيق على البدء أو عرض الإعدادات بشكل صحيح.

---

## 4. ~~جدول addon_settings~~ — تم حذفه (لا أخطاء من قاعدة قديمة)

تم إزالة الدفع الإلكتروني و SMS بالكامل. لا يوجد addon_settings. لا توجد مراجع متبقية.

---

## 5. صفحات آمنة نسبياً

- **errors/404.blade.php, errors/500.blade.php:** استخدام `optional($iconSetting)->value`.
- **layouts/admin/app.blade.php:** `first()?->value ?? ''`.
- **chat-index.blade.php:** `first()?->value`.
- **otp-setup.blade.php:** `first()?->value` (آمن) — **لا يُستخدم لتسجيل دخول الزبون** (الدخول فقط عبر Google/Facebook/Apple).
- **firebase-auth.blade.php:** استخدام `isset($firebaseOtp)` و `$firebaseOtp &&`.
- **firebase-config-index.blade.php:** `@if(isset($data))`.
- **AppServiceProvider (timezone):** `if (isset($timezone))`.

---

## 8. توثيق: النظام الهجين وتسجيل دخول الزبون

### 8.1 لوحة التحكم هجينة (ويب + موبايل)

**النظام يعمل بنمط هجين (Hybrid):**
- **لوحة التحكم (Admin Dashboard):** مشتركة بين الويب والموبايل — نفس الواجهة تُستخدم من المتصفح أو من تطبيق الموبايل.
- **تطبيق الزبون:** ويب (PWA) و/أو موبايل (Native/Hybrid).
- **كلا الواجهتين** تستخدمان نفس الـ API.

### 8.2 تسجيل دخول الزبون — فقط Google، Facebook، Apple

| المطلوب | الحالة |
|---------|--------|
| **Google Social Login** | ✅ مطلوب |
| **Facebook Social Login** | ✅ مطلوب |
| **Apple Login** | ✅ مطلوب |

| غير مطلوب (لا نريده) | الحالة |
|----------------------|--------|
| تسجيل دخول بالبريد الإلكتروني/كلمة السر | ❌ لا نريد |
| تسجيل دخول بالهاتف/OTP | ❌ لا نريد |
| Firebase OTP للزبون | ❌ لا نريد |

**ملاحظة:** صفحات `otp-setup` و `firebase-auth` قد تبقى في لوحة التحكم لأغراض أخرى (مثل إدارة الأدمن)، لكن **تسجيل دخول الزبون** يكون حصرياً عبر Google و Facebook و Apple فقط.

### 8.3 الإشعارات (مطلوبة)

- **Firebase Cloud Messaging (FCM)** — لإرسال الإشعارات للويب والموبايل
- **Firebase Message Config** — إعدادات رسائل الإشعارات (حالة الطلب، إلخ)
- **Push Notification** — صفحة FCM في إعدادات الأعمال

**لا تُزال هذه المكونات** — النظام يحتاجها لـ:
- إشعارات الطلبات (تأكيد، معالجة، توصيل)
- إشعارات التطبيق للمستخدمين

---

## 6. توصيات (بدون تنفيذ — كما طُلِب)

1. **توسيع seedMinimalData أو BaitPaitSeeder** لإضافة كل المفاتيح المطلوبة في `business_settings` مع قيم افتراضية.
2. ~~إضافة seed لـ addon_settings~~ — تم إزالة الدفع الإلكتروني و SMS.
3. **استبدال `->first()->value` بـ `->first()?->value ?? 'default'`** في fcm-index و branch/list.
4. **استخدام `isset($config)` و `$config['key'] ?? default`** في recaptcha-index (app-setting-index مُصلح).
5. **إضافة فحوصات null/isset في ConfigController** لكل الإعدادات التي تُستخدم كمصفوفات قبل الوصول لمفاتيحها.
6. **تعديل Helpers::getPagination() و pagination_limit()** للتعامل مع null (مثلاً إرجاع قيمة افتراضية مثل 10).
7. **تسجيل دخول الزبون:** التأكد من أن التطبيق يدعم فقط Google/Facebook/Apple — إخفاء أو تعطيل خيارات البريد/كلمة السر والهاتف/OTP في واجهة الزبون.

---

## 7. ملخص أولوية الإصلاح

| الأولوية | العنصر | التأثير |
|----------|--------|---------|
| عالية جداً | ConfigController API | فشل التطبيق عند جلب الإعدادات |
| عالية جداً | Helpers::getPagination() | فشل صفحات الفئات، الطلبات، الفروع، الكوبونات |
| عالية | fcm-index | خطأ 500 عند فتح صفحة FCM |
| ~~عالية~~ | ~~app-setting-index~~ | ✅ مُصلح |
| متوسطة | recaptcha-index | خطأ 500 عند فتح reCAPTCHA |
| منخفضة | ecom-setup حقول فارغة | تجربة مستخدم أسوأ، لا أخطاء |

---

**تحديثات لاحقة:**
- تم إزالة الدفع الإلكتروني و SMS بالكامل.
- تم إزالة الخارطة (Map): `map-api.blade.php`, `MapApiController`, إعدادات `map_api_key` و `google_map_status`. رسوم التوصيل تعتمد على المناطق (Area/Zip) فقط.
- تم إزالة db-index و location-setup. إعدادات التطبيق (app-setting) نُقلت إلى إعدادات الأعمال.
- **النظام الهجين:** لوحة التحكم مشتركة بين الويب والموبايل.
- **تسجيل دخول الزبون:** فقط عبر Google و Facebook و Apple — لا نريد البريد/كلمة السر ولا الهاتف/OTP.
- **لا توجد أخطاء متبقية من قاعدة البيانات القديمة** — الترحيل مكتمل.
