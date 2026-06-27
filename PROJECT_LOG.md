# PROJECT_LOG — سجل التغييرات

> **التنسيق:** `[YYYY-MM-DD] نوع التغيير — وصف مختصر`  
> **القسم:** الملفات المتأثرة، الغرض، الملاحظات.

---

## [2026-06-25] fix — استعادة المشروع وحلّ تعارضات دمج محفوظة في الكوميت

### الملخص

كان مجلد العمل مفرّغاً من ملفات المشروع (مستعادة من Git)، والأخطر أن الكوميت الأخير على `main` كان يحتوي على **علامات تعارض دمج غير محلولة** (`<<<<<<< HEAD`) مكتوبة فعلياً داخل 19 ملفاً، ما منع `composer install` و`npm install`. حُسمت كل التعارضات لصالح جانب **HEAD** (تطبيق Fanoon الحقيقي: Passport + Modules + CentralLogics + واجهة Laravel-Mix/Vue2)، لأن الجانب الآخر (`234599e`) هيكل Laravel فارغ Breeze/Inertia/Vite غير متوافق مع الكود الموجود. أُعيد توليد ملفات القفل وبُنيت الأصول والمشروع يعمل على `127.0.0.1:8000` (HTTP 200).

### الملفات

| الملف |
|-------|
| `composer.json`, `package.json` — حسم التعارض لصالح HEAD + إعادة توليد `composer.lock`, `package-lock.json` |
| `config/{app,auth,cache,database,filesystems,logging,mail,queue,services,session}.php` — حسم التعارض لصالح HEAD |
| `phpunit.xml`, `README.md`, `database/.gitignore` — حسم التعارض لصالح HEAD |
| `database/factories/UserFactory.php`, `database/seeders/DatabaseSeeder.php` — حسم التعارض لصالح HEAD |
| `public/css/app.css`, `public/js/app.js` — إعادة بناء الأصول (`npm run build`) |

### ملاحظات

- لم تُنفّذ أي هجرات جديدة؛ قاعدة `fanoon` كانت مُحدّثة بالكامل مسبقاً.
- تبقى تحذيرات `npm audit` (حزم الواجهة القديمة Vue2/Bootstrap4) دون تغيير.

---

## [2026-03-24] موقع المتجر في إعدادات العمل + API ‎`config`

### الملخص

تبويب جديد في `business-setup-nav`: **موقع المتجر (خرائط جوجل)** — حقل رابط واحد يُحفظ في `business_settings.store_google_maps_url`. **GET ‎`/api/v1/config`** يعيد `store_google_maps_url` و`google_maps_location_url` (نفس النص) لتسهيل استخدام التطبيق (`url_launcher` إن لم يكن فارغاً).

### الملفات

| الملف |
|-------|
| `routes/admin.php` — `store-location-map`, `update-store-location-map` |
| `BusinessSettingsController` — `storeLocationMap`, `updateStoreLocationMap` |
| `resources/views/admin-views/business-settings/store-map-location.blade.php` |
| `ConfigController` — حقول الـ JSON |
| `tests/Feature/StoreMapLocationConfigApiTest.php` |
| `docs/API-FRONTEND-PROGRAMMER-REPORT.md` |

---

## [2026-03-24] أداء API — إزالة وسيط pass-through

### الملخص

`ApiPerformanceDebugMiddleware` أصبح pass-through فقط (بدون قياس/تسجيل). أُزيل من مجموعة `api` في `bootstrap/app.php` لتقليل طبقة وسيطة على كل طلب API؛ **75 اختباراً** ما زالت تنجح.

---

## [2026-03-24] وضع الصيانة وـ API ‎`/api/v1/config`

### الملخص

- **كاش الـ config:** حتى مع بقاء `api_v1_configuration_payload_v1` ~30 دقيقة، تُحدَّث في كل طلب قيم `maintenance_mode` و `advance_maintenance_mode` من قاعدة البيانات (المدة الزمنية والحالة الفعلية).
- **تبديل الصيانة السريع (AJAX):** كان يمسح كاش `maintenance` فقط؛ أصبح يُعاد بناؤه من الإعدادات عبر `refreshMaintenanceMiddlewareCache()` ليتوافق مع `MaintenanceModeMiddleware` (لوحة الفرع).

### الملفات

| الملف |
|-------|
| `app/Http/Controllers/Api/V1/ConfigController.php` |
| `app/Http/Controllers/Admin/BusinessSettingsController.php` |
| `tests/Feature/MaintenanceModeConfigApiTest.php` |

---

## [2026-03-24] تبويب «معلومات المتجر» ضمن إعدادات العمل

### الملخص

إضافة زر تنقّل في `business-setup-nav` يفتح `admin/branch/settings` (نص: `store_information`)، ويظهر فقط عند `hide_branch_management = true`. صفحة إعدادات المتجر تعرض نفس عنوان «إعدادات العمل» وشريط التبويبات مثل باقي صفحات `business-settings`. **عنصر القائمة الجانبية** لـ «معلومات المتجر» أُزيل؛ الوصول عبر تبويب إعدادات العمل فقط. رابط **إعدادات العمل** في الشريط الجانبي يبقى `active` عند `admin/branch/settings`.

### الملفات

| الملف |
|-------|
| `resources/views/admin-views/business-settings/partial/business-setup-nav.blade.php` |
| `resources/views/admin-views/branch/edit.blade.php` |
| (نسخ `elitevape_full_upload/` المماثلة) |

---

## [2026-03-24] أيقونة «معلومات المتجر» في الشريط الجانبي

### الملخص

رابط `admin/branch/settings` كان يستخدم `tio-store` بينما **اسم الصنف غير موجود** في `public/assets/admin/vendor/icon-set/style.css`، فلا يظهر أي رمز. استُبدل بـ **`tio-shop-outlined`** (موجود، وبنفس أسلوب أيقونات مثل `tio-pages` / `-outlined`).

### الملفات

| الملف |
|-------|
| `resources/views/layouts/admin/partials/_sidebar.blade.php` |
| `elitevape_full_upload/resources/views/layouts/admin/partials/_sidebar.blade.php` |

---

## [2026-03-24] إشعارات لوحة التحكم — طلب جديد / تواصل / أولوية المودال

### الملخص

إصلاح وتثبيت تدفق إشعار **طلب جديد** و**تواصل معنا** في `/admin`: عدّاد `get-store-data`، المودال، الصوت، الكاش، التعارض مع Toastr، وعدم مسح `checked` عند فتح قائمة الطلبات فقط.

### الملفات الرئيسية

| الملف | التغيير |
|-------|---------|
| `app/Http/Controllers/Admin/SystemController.php` | رؤوس `Cache-Control: no-store` على `storeData()` |
| `app/Http/Controllers/Branch/SystemController.php` | نفس الرؤوس على `storeData()` |
| `app/Http/Controllers/Admin/OrderController.php` | إزالة `where(checked,0)->update(1)` من `list()`؛ تعيين `checked=1` في `details()` فقط للطلب المعروض |
| `app/Http/Controllers/Branch/OrderController.php` | نفس منطق القائمة/التفاصيل |
| `resources/views/layouts/admin/app.blade.php` | استطلاع `get-store-data` مع `cache: false`؛ مودالات BS3 + `blockOtherNotifyModals` (طلب → نوع مستخدم → تواصل) مع مرور عند تأجيل الطلب؛ توستر نجاح مرة/تبويب عند `new_order===0`؛ أخطاء مُقيّدة؛ مستمع النقر للمودالات داخل `#popup-modal*` فقط |
| `resources/views/layouts/branch/app.blade.php` | `cache: false` للاستطلاع |
| `public/assets/admin/css/custom.css` | `z-index` مودال/خلفية فوق `#toast-container` (999999) — قيمة ~1000010 |
| `app/Console/Commands/InsertTestOrderForAdminNotificationCommand.php` | أمر `dev:admin-notification-test-order` + إخراج تشخيصي |
| `app/Console/Commands/InsertTestContactUsForAdminNotificationCommand.php` | أمر `dev:admin-notification-test-contact-us` |
| `resources/lang/ar/messages.php` / `en/messages.php` | مفاتيح نصوص الإشعارات والتوستر |

### Session Storage (المتصفح)

| المفتاح | الغرض |
|---------|--------|
| `elite_admin_snooze_order` | يطابق `String(new_order)` → لا يُعرض مودال الطلب حتى يتغير العدد |
| `elite_admin_snooze_type_approval` | مثلًا لطلبات موافقة نوع المستخدم |
| `elite_admin_snooze_contact_us` | مثلًا لعدد رسائل التواصل غير المقروءة |
| `elite_admin_notify_poll_verified` | مرة واحدة/تبويب: عرض توستر «الاستطلاع يعمل» عند عدم وجود طلبات جديدة |

### أوامر الاختبار اليدوي

```bash
php artisan dev:admin-notification-test-order --user=1 --branch=1 --amount=99
php artisan dev:admin-notification-test-contact-us --count=2
php artisan optimize:clear
```

### الاختبارات (PHPUnit)

- `tests/Feature/NotificationFlowTest.php` — منها `opening_order_list_does_not_mark_all_orders_checked` و `get_store_data_reports_new_order_and_new_contact_counts_together`
- `tests/Feature/InsertTestOrderForAdminNotificationCommandTest.php`
- `tests/Feature/InsertTestContactUsForAdminNotificationCommandTest.php`
- `tests/Unit/AdminNewOrderModalVisibilityCssTest.php`

### ملاحظات سلوكية

- **أولوية المودال:** طلب غير مُراجع أولاً؛ إن وُضع تأجيل للطلب يُفحص نوع المستخدم ثم التواصل.
- **قائمة الطلبات** لا تعيد تعيين `checked` لجميع الطلبات (كان يُصفّر عداد الإشعار فور فتح القائمة).
- **التوستر الأخضر** (تأكيد الاستطلاع) يظهر فقط عندما `new_order === 0` ولمرة واحدة لكل تبويب؛ **لا** توستر تحذير عند تأجيل الطلب.

---

## [2025-01-XX] توثيق الجلسة — ملخص التغييرات

### 1. API قائمة الطلبات (JSON)

| الملف | التغيير |
|-------|---------|
| `app/Http/Controllers/Admin/OrderController.php` | إرجاع JSON عند `Accept: application/json` أو `wantsJson()` |
| `app/Http/Middleware/AdminMiddleware.php` | إرجاع 401 JSON بدلاً من redirect عند طلبات JSON غير المصادقة |

**الغرض:** ربط تطبيق فلتر الويب بقائمة الطلبات.

**المسار:** `GET /admin/orders/list/{status}`  
**الفلاتر:** `search`, `start_date`, `end_date`, `per_page`, `page`

---

### 2. OrderSeeder

| الملف | التغيير |
|-------|---------|
| `database/seeders/OrderSeeder.php` | إنشاء 15 طلباً تجريبياً |
| `database/seeders/DatabaseSeeder.php` | إضافة `OrderSeeder::class` |

**الغرض:** بيانات تجريبية للطلبات. يعمل فقط عند وجود `Branch` و `User` في قاعدة البيانات.

---

### 3. اختبارات API الطلبات

| الملف | التغيير |
|-------|---------|
| `tests/Feature/AdminOrdersListApiTest.php` | اختبارات: 401 غير مصادق، بنية JSON، فلاتر الحالة/البحث/التاريخ، التصفح |

---

### 4. توثيق API

| الملف | التغيير |
|-------|---------|
| `docs/API_ORDERS_LIST.md` | توثيق كامل لـ API قائمة الطلبات لتطبيق فلتر الويب |

---

### 5. ترجمات عربية — الطلب دون تسجيل

| الملف | التغيير |
|-------|---------|
| `resources/lang/ar/messages.php` | `Guest Checkout` / `guest_checkout` → **الطلب دون تسجيل** |
| `resources/lang/ar/messages.php` | Tooltip → **عند التفعيل، يمكن للعملاء إتمام الطلب دون تسجيل الدخول أو إنشاء حساب.** |

**الغرض:** نصوص عربية أوضح في إعدادات الأعمال (E-commerce Setup).

---

### أوامر التشغيل

```bash
# تشغيل السيدر
php artisan db:seed --class=OrderSeeder

# تشغيل الاختبارات
php artisan test --filter=AdminOrdersListApiTest

# مسح الكاش
php artisan optimize:clear
```

---

### ملاحظات بيئة

- المشروع يتطلب **PHP 8.3+** (Composer platform_check).
- `.cursorignore` يُفضّل تضمين `vendor/`, `storage/` لتقليل الضوضاء.
