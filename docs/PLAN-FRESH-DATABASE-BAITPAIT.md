# خطة إعادة بناء قاعدة البيانات — Bait Pait

> **تم التنفيذ:** 2025-03-08

---

## 1. الجداول الحالية في قاعدة البيانات (من installation + migrations)

من ملف `installation/database_v7.7.sql` والـ migrations الإضافية:

| # | الجدول | المصدر |
|---|--------|--------|
| 1 | addon_settings | installation |
| 2 | admins | installation |
| 3 | attributes | installation |
| 4 | banners | installation |
| 5 | branches | installation |
| 6 | business_settings | installation |
| 7 | categories | installation |
| 8 | conversations | installation |
| 9 | coupons | installation |
| 10 | currencies | installation |
| 11 | customer_addresses | installation |
| 12 | dc_conversations | installation |
| 13 | delivery_charge_by_areas | installation |
| 14 | delivery_charge_setups | installation |
| 15 | delivery_histories | installation |
| 16 | delivery_men | installation |
| 17 | d_m_reviews | installation |
| 18 | email_verifications | installation |
| 19 | failed_jobs | installation |
| 20 | flash_sales | installation |
| 21 | flash_sale_products | installation |
| 22 | guest_users | installation |
| 23 | login_setups | installation |
| 24 | messages | installation |
| 25 | migrations | installation |
| 26 | newsletters | installation |
| 27 | notifications | installation |
| 28 | oauth_* (Passport) | installation |
| 29 | order_areas | installation |
| 30 | order_delivery_histories | installation |
| 31 | order_details | installation |
| 32 | orders | installation |
| 33 | password_resets | installation |
| 34 | payment_requests | installation |
| 35 | phone_verifications | installation |
| 36 | products | installation |
| 37 | reviews | installation |
| 38 | social_medias | installation |
| 39 | soft_credentials | installation |
| 40 | track_deliverymen | installation |
| 41 | translations | installation |
| 42 | user_accounts | installation |
| 43 | users | installation |
| 44 | wallet_transactions | installation |
| 45 | wishlists | installation |
| 46 | areas | migration |
| 47 | cities | migration |
| 48 | contact_us | migration |
| 49 | product_translations | migration |
| 50 | user_types | migration |
| 51 | product_user_type_discounts | migration |
| 52 | product_user_type_prices | migration |
| 53 | loyalty_* | migration |
| 54 | shipping_companies, order_shipments | migration |
| 55 | business_pages (إن وُجد) | migration |

---

## 2. مراحل التنفيذ

### المرحلة 1: حذف Migrations الحالية
- حذف كل الملفات في `database/migrations/` (77 ملف)
- الاحتفاظ بـ `migrations` folder فارغ أو بملف واحد فقط

### المرحلة 2: إنشاء Migration واحد
- إنشاء ملف واحد: `database/migrations/2026_01_01_000000_baitpait_full_schema.php`
- دمج هيكل كل الجداول من:
  - `installation/database_v7.7.sql` (الأساس)
  - الـ migrations التي تضيف جداول جديدة (areas, cities, contact_us, user_types, loyalty, shipping, product_translations, إلخ)
- ترتيب الجداول حسب الاعتماديات (Foreign Keys)
- إزالة بيانات INSERT غير الضرورية — إبقاء فقط:
  - `business_settings` (الحد الأدنى للتشغيل)
  - `login_setups` (الحد الأدنى)
  - `currencies` (الحد الأدنى إن لزم)

### المرحلة 3: إنشاء Seeder
- إنشاء `database/seeders/BaitPaitSeeder.php`:
  - **Admin واحد:** `info@baitpait.com` / `100200300`
  - **فرع واحد (id=1):** `Bait Pait` — بيانات الفرع الافتراضي
  - **إعدادات أساسية:** `business_settings` و `login_setups` للحد الأدنى
  - **لا منتجات، لا تصنيفات، لا عملاء** — العميل يضيف لاحقاً

### المرحلة 4: إلغاء تسجيل دخول الفرع
- **تعطيل مسارات Branch:** إزالة أو تعليق `mapBranchRoutes()` من `RouteServiceProvider`
- أو: إعادة توجيه `branch/*` إلى صفحة 404 أو رسالة "غير متاح"
- **النتيجة:** فقط Admin يمكنه الدخول عبر `admin/auth/login`

### المرحلة 5: إزالة رابط الفرع من القائمة
- إزالة أو إخفاء أي رابط لتسجيل دخول الفرع من الواجهات
- (صفحة إعدادات الفرع تبقى في Admin — لتعديل بيانات الفرع الوحيد)

### المرحلة 6: تحديث DatabaseSeeder
- استدعاء `BaitPaitSeeder` من `DatabaseSeeder.php`

### المرحلة 7: مسح قاعدة البيانات والتشغيل من جديد
- `php artisan migrate:fresh --seed`
- التحقق: تسجيل دخول Admin بـ `info@baitpait.com` / `100200300`

---

## 3. بيانات Bait Pait في الـ Seeder

| الجدول | الحقل | القيمة |
|--------|-------|-------|
| admins | email | info@baitpait.com |
| admins | password | bcrypt('100200300') |
| admins | f_name | Bait Pait |
| admins | l_name | Admin |
| branches | id | 1 |
| branches | name | Bait Pait |
| branches | email | info@baitpait.com (أو branch@baitpait.com) |
| branches | password | bcrypt('100200300') |
| branches | address | (فارغ أو عنوان افتراضي) |
| branches | phone | (فارغ) |
| branches | status | 1 |

---

## 4. ما يُترك فارغاً (كما طلبت)

- اسم المتجر، الشعار، إلخ — فارغ
- المنتجات، التصنيفات، العملاء — فارغ
- الإعدادات المعقدة — الحد الأدنى للتشغيل فقط
- **العميل يضيف كل شيء لاحقاً**

---

## 5. أسئلة للتأكيد قبل التنفيذ

1. **هل تريد الاحتفاظ بجدول `branches`؟**  
   نعم — للفرع الوحيد (id=1) وتعديله من Admin. لكن لا تسجيل دخول للفرع.

2. **API التطبيق:**  
   هل تبقى كما هي؟ (العملاء يسجلون من التطبيق، الطلبات، إلخ) — أفترض نعم.

3. **مندوبو التوصيل:**  
   هل تبقى الميزة؟ (Admin يضيفهم، يربطهم بالطلبات) — أفترض نعم.

4. **ملف installation/database.sql:**  
   هل نحدّثه ليتوافق مع الـ migration الجديد؟ أم نتركه للتوثيق فقط؟

---

## 6. ملخص الملفات المتأثرة

| الملف | الإجراء |
|-------|---------|
| `database/migrations/*` | حذف الكل |
| `database/migrations/2026_01_01_000000_baitpait_full_schema.php` | إنشاء جديد |
| `database/seeders/BaitPaitSeeder.php` | إنشاء جديد |
| `database/seeders/DatabaseSeeder.php` | تعديل — استدعاء BaitPaitSeeder |
| `app/Providers/RouteServiceProvider.php` | تعطيل mapBranchRoutes |
| `routes/branch.php` | تعطيل أو إعادة توجيه |

---

## 7. ترتيب التنفيذ

1. حذف migrations
2. إنشاء migration واحد
3. إنشاء BaitPaitSeeder
4. تحديث DatabaseSeeder
5. تعطيل mapBranchRoutes
6. تشغيل `migrate:fresh --seed`
7. اختبار تسجيل الدخول

---

**إذا كان لديك أي تعديل أو إضافة، حدّث الخطة. عند قولك "تم" نبدأ التنفيذ.**
