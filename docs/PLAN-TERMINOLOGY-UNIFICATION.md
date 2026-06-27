# خطة توحيد المصطلحات — restaurant → store

> **المرجع:** PLAN-SYSTEM-IMPROVEMENTS.md — القسم 1.2  
> **الهدف:** استبدال مصطلحات المطاعم بمصطلحات المتجر/التجارة الإلكترونية دون كسر النظام.

---

## 1. نطاق التغيير

### 1.1 المصطلحات المستهدفة

| الحالي | المقترح | النوع |
|--------|----------|-------|
| `get-restaurant-data` (route) | `get-store-data` | Route + اسم الدالة |
| `restaurantData()` (method) | `storeData()` | Controller method |
| `restaurant_name` (DB key) | `store_name` | مفتاح في `business_settings` |
| `restaurant_name` (form input) | `store_name` | حقل النموذج |
| `restaurant_id` (جدول) | يُترك أو يُستبدل لاحقاً | عمود في جدول (إن وُجد) |

### 1.2 ما يُستثنى (لا تغيير الآن)

| العنصر | السبب |
|--------|-------|
| `restaurant_id` في migrations/seeders | قد يكون عموداً في جدول موجود — يحتاج تحليل منفصل |
| ملفات `installation/*.sql` | مرجعية قديمة — لا تُستخدم في التشغيل |
| `restaurant` في ملفات الترجمة (نصوص وصفية) | يمكن تحديثها لاحقاً كنص واجهة |
| API response key `ecommerce_name` | الـ API يعيد `ecommerce_name` بالفعل — لا تغيير |

---

## 2. خريطة الملفات المتأثرة

### 2.1 Route و Controller — `get-restaurant-data` → `get-store-data`

| الملف | التغيير |
|-------|---------|
| `routes/admin.php` | `get-restaurant-data` → `get-store-data`، `restaurantData` → `storeData` |
| `routes/branch.php` | نفس التغيير |
| `app/Http/Controllers/Admin/SystemController.php` | `restaurantData()` → `storeData()` |
| `app/Http/Controllers/Branch/SystemController.php` | `restaurantData()` → `storeData()` |
| `resources/views/layouts/admin/app.blade.php` | `route('admin.get-restaurant-data')` → `route('admin.get-store-data')` |
| `resources/views/layouts/branch/app.blade.php` | `route('branch.get-restaurant-data')` → `route('branch.get-store-data')` |

### 2.2 `restaurant_name` → `store_name` (قاعدة البيانات + الكود)

| الملف | التغيير |
|-------|---------|
| **Migration جديدة** | إضافة مفتاح `store_name` أو تحديث `restaurant_name` → `store_name` في `business_settings` |
| `app/Http/Controllers/Admin/BusinessSettingsController.php` | `restaurant_name` → `store_name` في InsertOrUpdateBusinessData و `$request->restaurant_name` |
| `resources/views/admin-views/business-settings/ecom-setup.blade.php` | `restaurant_name` → `store_name` في get_business_settings و name الحقل |
| `app/Http/Controllers/InstallController.php` | `restaurant_name` → `store_name` في where/update |
| `app/Http/Controllers/Api/V1/ConfigController.php` | `get_business_settings('restaurant_name')` → `get_business_settings('store_name')` |
| `resources/views/admin-views/branch/list.blade.php` | `restaurant_name` → `store_name` |
| `resources/views/admin-views/order/partials/invoice-print.blade.php` | `restaurant_name` → `store_name` |
| `resources/views/branch-views/order/partials/invoice-print.blade.php` | `restaurant_name` → `store_name` |
| `resources/views/admin-views/order/invoice.blade.php` | `restaurant_name` → `store_name` |
| `resources/views/branch-views/order/invoice.blade.php` | `restaurant_name` → `store_name` |
| `resources/views/errors/404.blade.php` | `restaurant_name` → `store_name` |
| `resources/views/errors/500.blade.php` | `restaurant_name` → `store_name` |
| `database/migrations/2026_01_01_000000_baitpait_full_schema.php` | في seeder: `restaurant_name` → `store_name` (للـ fresh install) |
| `database/seeders/BaitPaitSeeder.php` | إن كان يذكر `restaurant_name` — مراجعة |

---

## 3. استراتيجية قاعدة البيانات

### الخيار أ: Migration لتحديث المفتاح (موصى به)

```php
// Migration: rename restaurant_name to store_name in business_settings
DB::table('business_settings')
    ->where('key', 'restaurant_name')
    ->update(['key' => 'store_name']);
```

**مميزات:** توحيد كامل.  
**عيوب:** يحتاج تشغيل migration على كل بيئة.

### الخيار ب: دعم الاثنين (Backward Compatible)

في Helper أو عند القراءة:

```php
Helpers::get_business_settings('store_name') 
    ?? Helpers::get_business_settings('restaurant_name')
```

**مميزات:** يعمل مع قواعد قديمة وجديدة.  
**عيوب:** كود إضافي، وتبقى القيم القديمة في DB.

### التوصية

- **للنظام الجديد (fresh install):** استخدام `store_name` في schema/seeders.
- **للنظام الموجود:** Migration لتحديث المفتاح من `restaurant_name` إلى `store_name`.

---

## 4. ترتيب التنفيذ المقترح

| المرحلة | المهمة | الملفات |
|---------|--------|---------|
| **1** | إنشاء Migration لتحديث `restaurant_name` → `store_name` | `database/migrations/` |
| **2** | تحديث Controllers (BusinessSettings، Install، Config) | 3 ملفات |
| **3** | تحديث Views (ecom-setup، invoice، errors، branch/list) | 7 ملفات |
| **4** | تحديث Routes و SystemController (get-store-data) | 4 ملفات |
| **5** | تحديث Schema و Seeders للـ fresh install | 2 ملفات |
| **6** | تشغيل Migration واختبار | — |

---

## 5. التحقق بعد التنفيذ — **تم**

- [x] صفحة إعدادات المتجر (ecom-setup) تعرض وتُحفظ الاسم بشكل صحيح
- [x] الفواتير تعرض اسم المتجر
- [x] صفحات الأخطاء (404، 500) تعرض اسم المتجر
- [x] الـ API يعيد `ecommerce_name` بشكل صحيح (يقرأ من `store_name`)
- [x] لوحة التحكم (admin/branch) — عدّاد الطلبات يعمل عبر `get-store-data`
- [x] تطبيق الفرع (branch) — نفس العدّاد يعمل

**قائمة التحقق اليدوية:** راجع `docs/TERMINOLOGY-TEST-CHECKLIST.md`

---

## 6. مخاطر وتخفيفها

| المخاطرة | التخفيف |
|-----------|---------|
| تطبيق جوال يستدعي route قديم | إضافة redirect من `get-restaurant-data` إلى `get-store-data` (اختياري) |
| قواعد بيانات قديمة بدون migration | استخدام الخيار ب (دعم الاثنين) في القراءة |
| ملفات compiled (storage/framework/views) | تشغيل `php artisan view:clear` بعد التعديلات |

---

## 7. ملخص

| العنصر | العدد التقريبي |
|--------|----------------|
| Routes | 2 |
| Controllers | 4 |
| Views | 7 |
| Migrations | 1 (جديدة) |
| Seeders/Schema | 2 |

**الوقت المتوقع:** 1–2 ساعات تنفيذ + اختبار.
