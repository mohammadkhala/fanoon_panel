# Fanoon — دليل المبرمج الكامل

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-Proprietary-blue.svg)](LICENSE)

> **للمبرمج الجديد:** هذا الملف يحتوي كل ما تحتاجه لبدء التطوير — التثبيت، الإعداد، تسجيل الدخول، بنية المشروع، الـ API، والأوامر اليومية.

**مستودع GitHub:** [github.com/baitpait/Fanoon](https://github.com/baitpait/Fanoon)

---

## 1. ما هو هذا المشروع؟

**Fanoon** منصة إدارة متجر إلكتروني (Backend) تتكون من:

| المكون | الوصف |
|--------|--------|
| **لوحة تحكم (Admin)** | إدارة المنتجات، الطلبات، العملاء، التقارير، الإعدادات، المدن والمناطق |
| **REST API** | `api/v1/*` — للتطبيقات (Flutter / Web) عبر **Laravel Passport** |
| **واجهة الفرع (Branch)** | واجهة محدودة للفروع — مخفية/معطّلة افتراضياً |

### ما تتضمنه هذه النسخة

- الكود الكامل للوحة التحكم والـ API
- `database/migrations/` — هيكل قاعدة البيانات
- `database/seeders/` — بيانات أولية للتطوير
- التوثيق في مجلد `docs/`

### ما لا تتضمنه هذه النسخة

| المستبعد | السبب |
|----------|--------|
| ملفات `.sql` (نسخ إنتاج) | ابدأ بقاعدة فارغة عبر `migrate` |
| صور/وسائط `storage/app/public` | تُرفع محلياً بعد `storage:link` |
| ملف `.env` | كل مبرمج ينشئ نسخته من `.env.example` |
| مفاتيح Passport `storage/*.key` | تُنشأ عبر `passport:keys` |
| مجلد `vendor/` | يُثبَّت عبر `composer install` |

---

## 2. المتطلبات

| الأداة | الإصدار |
|--------|---------|
| **PHP** | 8.2+ |
| **Composer** | 2.x |
| **MySQL** | 8+ أو MariaDB 10.6+ |
| **Node.js** | اختياري — لبناء الـ assets الأمامية |

**امتدادات PHP المطلوبة:** `pdo_mysql`, `openssl`, `mbstring`, `json`, `curl`, `gd`, `fileinfo`, `tokenizer`, `xml`, `ctype`, `bcmath`

---

## 3. التثبيت خطوة بخطوة

### 3.1 استنساخ المشروع

```bash
git clone https://github.com/baitpait/Fanoon.git
cd Fanoon
```

### 3.2 تثبيت الاعتماديات

```bash
composer install
```

### 3.3 إعداد البيئة

```bash
cp .env.example .env
php artisan key:generate
```

### 3.4 إنشاء قاعدة البيانات

أنشئ قاعدة بيانات فارغة في MySQL، مثلاً:

```sql
CREATE DATABASE fanoon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

ثم عدّل في `.env`:

```env
APP_NAME="Fanoon"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fanoon
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3.5 تشغيل الهجرات والبذور

```bash
php artisan migrate --force
php artisan db:seed --class=BaitPaitSeeder
php artisan passport:keys --force
php artisan storage:link
```

### 3.6 تشغيل السيرفر

```bash
php artisan serve
```

**التحقق السريع:**

```bash
curl http://127.0.0.1:8000/api/v1/config
```

---

## 4. تسجيل الدخول — بيانات المشرف الافتراضية

بعد تشغيل `BaitPaitSeeder`:

| الحقل | القيمة |
|-------|--------|
| **رابط الدخول** | `http://127.0.0.1:8000/admin/auth/login` |
| **البريد** | `info@baitpait.com` |
| **كلمة المرور** | `100200300` |

> **مهم:** غيّر كلمة المرور فوراً في بيئة مشتركة أو إنتاج.

### بيانات الفرع الافتراضي (للطلبات فقط — تسجيل الدخول معطّل)

| الحقل | القيمة |
|-------|--------|
| **البريد** | `branch@baitpait.com` |
| **كلمة المرور** | `100200300` |
| **الاسم** | Bait Pait |

---

## 5. البذور (Seeders)

### 5.1 البذور الأساسية — ابدأ بها دائماً

```bash
php artisan db:seed --class=BaitPaitSeeder
```

| ما يُنشئه | التفاصيل |
|-----------|----------|
| مشرف لوحة التحكم | `info@baitpait.com` |
| فرع افتراضي (id=1) | Bait Pait |
| عملاء Passport | Personal Access + Password Grant |

### 5.2 بذور المدن والمناطق

```bash
php artisan db:seed --class=WestBankPalestineCitiesSeeder
php artisan db:seed --class=InsideArabTownsCitiesSeeder
php artisan db:seed --class=JerusalemDeliveryCitySeeder
```

| الأمر | الغرض |
|--------|--------|
| `WestBankPalestineCitiesSeeder` | مدن الضفة الغربية |
| `InsideArabTownsCitiesSeeder` | بلدات عرب الداخل |
| `JerusalemDeliveryCitySeeder` | القدس ضمن منطقة القدس |

### 5.3 بيانات تجريبية كاملة (اختياري — للاختبار)

```bash
php artisan db:seed --class=FullTestDataSeeder
```

ينشئ: منتجات، تصنيفات، عملاء، طلبات، كوبونات، تقييمات، نقاط ولاء، محادثات، وغيرها.

> يتطلب تشغيل `BaitPaitSeeder` أولاً.

### 5.4 بذور أخرى

| الأمر | الغرض |
|--------|--------|
| `php artisan db:seed --class=BusinessPagesSeeder` | صفحات السياسات (خصوصية، شروط، إلخ) |
| `php artisan db:seed --class=CouponSeeder` | كوبونات تجريبية |
| `php artisan db:seed --class=OrderSeeder` | طلبات للاختبار |
| `php artisan db:seed --class=TestDataSeeder` | بيانات اختبار خفيفة |
| `php artisan db:seed` | يشغّل `BaitPaitSeeder` + `FullTestDataSeeder معاً |

---

## 6. إعدادات `.env` المهمة

| المتغير | الوصف | مثال محلي |
|---------|--------|-----------|
| `APP_URL` | عنوان اللوحة والـ API | `http://127.0.0.1:8000` |
| `APP_DEBUG` | وضع التصحيح | `true` (محلي) / `false` (إنتاج) |
| `DB_*` | اتصال قاعدة البيانات | انظر القسم 3.4 |
| `CORS_ALLOWED_ORIGINS` | نطاقات الواجهات المسموحة | `http://localhost:8090,http://127.0.0.1:8090` |
| `AUTO_EMAIL_DOMAIN` | نطاق بريد تلقائي للعملاء بدون إيميل | `yourstore.com` |
| `FORCE_HTTPS` | إجبار HTTPS | `false` محلياً / `true` إنتاج |
| `QUEUE_CONNECTION` | طابور المهام | `sync` محلياً / `redis` أو `database` إنتاج |

### CORS لتطبيق Flutter Web

عند ربط تطبيق Flutter ويب (مثلاً على المنفذ `8090`):

```env
CORS_ALLOWED_ORIGINS=http://localhost:8090,http://127.0.0.1:8090
```

ثم:

```bash
php artisan config:clear
php artisan cache:clear
```

> في البيئة المحلية، `config/cors.php` يسمح تلقائياً بـ `localhost` و`127.0.0.1` على أي منفذ.

---

## 7. بنية المشروع — أين أجد ماذا؟

```
Fanoon/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/              ← لوحة التحكم
│   │   │   ├── OrderController.php
│   │   │   ├── ProductController.php
│   │   │   ├── CustomerController.php
│   │   │   ├── ReportController.php
│   │   │   ├── TagController.php
│   │   │   └── WebhookController.php
│   │   ├── Api/V1/             ← API للتطبيق
│   │   └── Branch/             ← واجهة الفرع
│   ├── Models/                 ← Order, Product, Customer, Tag, ...
│   ├── Services/               ← WebhookService, OrderStatusLogService
│   └── Jobs/                   ← DispatchWebhookJob
├── config/
│   ├── cors.php                ← إعدادات CORS
│   └── feature_flags.php       ← إخفاء/إظهار ميزات
├── database/
│   ├── migrations/             ← هيكل قاعدة البيانات
│   └── seeders/                ← البذور
├── resources/views/
│   └── admin-views/            ← صفحات لوحة التحكم (Blade)
├── routes/
│   ├── admin.php               ← مسارات لوحة التحكم
│   └── api/v1/api.php          ← مسارات API
├── public/
│   └── api-docs/               ← توثيق Swagger (openapi.yaml)
├── storage/
│   └── app/public/             ← ملفات الرفع (فارغ — يُملأ محلياً)
└── docs/                       ← توثيق تفصيلي إضافي
```

---

## 8. مسارات لوحة التحكم

| الصفحة | المسار |
|--------|--------|
| تسجيل الدخول | `/admin/auth/login` |
| لوحة التحكم الرئيسية | `/admin` |
| المنتجات | `/admin/product/list` |
| الطلبات | `/admin/order/list` |
| العملاء | `/admin/customer/list` |
| وسوم المنتجات | `/admin/tag/list` |
| التقارير — أفضل المنتجات | `/admin/report/best-selling-products` |
| التقارير — أفضل العملاء | `/admin/report/top-customers` |
| Webhooks | `/admin/webhook/list` (مخفية افتراضياً) |

---

## 9. مراحل حياة الطلب

| الحالة | الوصف |
|--------|-------|
| `pending` | بانتظار التأكيد |
| `confirmed` | مؤكد |
| `processing` | قيد التحضير |
| `out_for_delivery` | في الطريق |
| `delivered` | تم التوصيل (يُمنح نقاط الولاء) |
| `canceled` | ملغى |
| `failed` | فشل التوصيل |
| `returned` | مرتجع |

---

## 10. REST API — للمبرمج التطبيق

### 10.1 المعلومات الأساسية

| البند | القيمة |
|-------|--------|
| **Base URL (محلي)** | `http://127.0.0.1:8000/api/v1` |
| **Content-Type** | `application/json` |
| **المصادقة** | Bearer Token (OAuth2 / Passport) |

### 10.2 Headers المهمة

| Header | مطلوب | الوصف |
|--------|-------|-------|
| `Authorization` | للعميل المسجّل | `Bearer {access_token}` |
| `X-localization` | اختياري | `ar` أو `en` أو `he` |
| `guest-id` | للضيف | معرف الضيف من `POST guest/add` |
| `Accept` | موصى به | `application/json` |

### 10.3 سير العمل (Flow) للتطبيق

```
1. إدخال التطبيق
   └── GET /api/v1/config          ← إعدادات المتجر

2. إذا ضيف (Guest)
   └── POST /api/v1/guest/add      ← الحصول على guest_id
   └── إرسال guest-id في كل طلب

3. إذا تسجيل
   └── POST /api/v1/auth/registration
   └── أو POST /api/v1/auth/login
   └── حفظ token

4. التصفح
   └── GET /api/v1/products/latest
   └── GET /api/v1/categories/
   └── GET /api/v1/products/details/{id}

5. الطلب
   └── POST /api/v1/customer/address/add
   └── POST /api/v1/customer/order/place

6. التتبع
   └── POST /api/v1/customer/order/track
```

### 10.4 أهم Endpoints

| Method | Endpoint | Auth | الوصف |
|--------|----------|------|-------|
| GET | `config/` | — | إعدادات التطبيق |
| POST | `guest/add` | — | إضافة ضيف |
| POST | `auth/login` | — | تسجيل دخول |
| POST | `auth/registration` | — | تسجيل عميل |
| GET | `products/latest` | — | أحدث المنتجات |
| GET | `products/details/{id}` | — | تفاصيل منتج |
| GET | `categories/` | — | التصنيفات |
| GET | `banners/` | — | البانرات |
| POST | `customer/order/place` | Bearer/guest | إنشاء طلب |
| GET | `customer/order/list` | Bearer/guest | قائمة الطلبات |
| GET | `customer/loyalty` | Bearer | نقاط الولاء |
| GET | `coupon/apply` | guest-id | تطبيق كوبون |

### 10.5 توثيق API التفصيلي

| المصدر | الموقع |
|--------|--------|
| Swagger UI | `http://127.0.0.1:8000/api-docs/` |
| ملف OpenAPI | `public/api-docs/openapi.yaml` |
| دليل المبرمج | [docs/API-DEVELOPER-GUIDE.md](docs/API-DEVELOPER-GUIDE.md) |
| قائمة Endpoints | [docs/API-ENDPOINTS-LIST.md](docs/API-ENDPOINTS-LIST.md) |

---

## 11. Feature Flags — إخفاء/إظهار الميزات

في `config/feature_flags.php` (أو عبر `.env`):

| المفتاح | الافتراضي | الوصف |
|---------|-----------|-------|
| `SINGLE_BRANCH_MODE` | `true` | فرع واحد فقط (branch_id=1) |
| `HIDE_BRANCH_MANAGEMENT` | `true` | إخفاء إدارة الفروع |
| `HIDE_WEBHOOKS` | `true` | إخفاء Webhooks |
| `HIDE_SEND_NOTIFICATION_PAGE` | `true` | إخفاء صفحة الإشعارات |
| `FORCE_CASH_ONLY_AND_HIDE_THIRD_PARTY` | `false` | الدفع نقداً فقط + إخفاء إعدادات الطرف الثالث |

**أمثلة في `.env`:**

```env
HIDE_WEBHOOKS=false
SINGLE_BRANCH_MODE=false
```

---

## 12. الملفات والمجلدات المهمة

### Controllers رئيسية

| Controller | المسؤولية |
|------------|-----------|
| `Admin/OrderController` | الطلبات، تغيير الحالة، سجل التغييرات |
| `Admin/ProductController` | المنتجات، الترجمة، التصدير |
| `Admin/CustomerController` | العملاء، التقسيم، التصدير Excel |
| `Admin/ReportController` | التقارير، أفضل المنتجات/العملاء |
| `Admin/TagController` | وسوم المنتجات |
| `Api/V1/OrderController` | API الطلبات للتطبيق |

### Services

| Service | الوظيفة |
|---------|---------|
| `WebhookService` | إرسال `order.created`, `order.status_changed` |
| `OrderStatusLogService` | تسجيل تغييرات حالة الطلب |

---

## 13. أوامر يومية مفيدة

```bash
# تشغيل السيرفر
php artisan serve

# مسح الكاش (بعد تعديل .env أو config)
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# إعادة بناء قاعدة البيانات من الصفر
php artisan migrate:fresh --seed

# فقط BaitPaitSeeder بعد fresh
php artisan migrate:fresh
php artisan db:seed --class=BaitPaitSeeder

# مفاتيح Passport (مرة واحدة لكل بيئة)
php artisan passport:keys --force

# رابط التخزين (مرة واحدة)
php artisan storage:link

# Queue — مطلوب لـ Webhooks في الإنتاج
php artisan queue:work

# اختبار API سريع
curl http://127.0.0.1:8000/api/v1/config
```

---

## 14. التخزين (Storage)

```bash
php artisan storage:link
```

- الملفات المرفوعة تُحفظ في: `storage/app/public/`
- تُعرض عبر: `public/storage/` (symlink)
- المجلد **فارغ** في المستودع — ارفع الصور من لوحة التحكم بعد التثبيت

**صلاحيات السيرفر (إنتاج):**

```bash
chmod -R 775 storage bootstrap/cache
```

---

## 15. الميزات المفعّلة في النظام

### لوحة التحكم

| الميزة | الحالة |
|--------|--------|
| إدارة المنتجات (وسوم، منتجات ذات صلة، تنبيه مخزون) | ✅ |
| إدارة التصنيفات والبنرات والكوبونات | ✅ |
| إدارة الطلبات وسجل التغييرات | ✅ |
| تصدير الطلبات والعملاء Excel | ✅ |
| إدارة المناطق والمدن | ✅ |
| أنواع المستخدمين، شركات الشحن | ✅ |
| Contact Us، التقارير، Webhooks | ✅ |
| بحث موحّد، فلتر التقييم | ✅ |

### API

| الميزة | الحالة |
|--------|--------|
| تصفح، سلة، طلب، دفع عند الاستلام | ✅ |
| تسجيل ضيف، تسجيل دخول (يدوي + Google) | ✅ |
| كوبون، إعادة طلب، منتجات ذات صلة | ✅ |
| نقاط الولاء | ✅ |
| الدردشة مع الإدارة | ✅ |

### معطّل افتراضياً

| الميزة | السبب |
|--------|-------|
| الدفع الإلكتروني | cash only |
| خريطة Google | غير مستخدمة |
| مندوب التوصيل | محذوف |
| Facebook/Apple Login | Google فقط |

---

## 16. حل المشاكل الشائعة

### خطأ اتصال قاعدة البيانات

- تأكد من إنشاء قاعدة `fanoon` وتطابق `DB_*` في `.env`
- شغّل: `php artisan config:clear`

### 500 بعد التثبيت

```bash
php artisan key:generate
php artisan passport:keys --force
chmod -R 775 storage bootstrap/cache
```

### الصور لا تظهر

```bash
php artisan storage:link
```

### CORS مع Flutter Web

- أضف `http://localhost:8090` في `CORS_ALLOWED_ORIGINS`
- شغّل `php artisan config:clear`
- راجع [docs/CORS-SETUP-FOR-FLUTTER-WEB.md](docs/CORS-SETUP-FOR-FLUTTER-WEB.md)

### 401 على API مع Bearer null

- لا ترسل `Authorization: Bearer null` — أزل الهيدر إذا لا يوجد token
- Endpoints العامة مثل `config/` لا تتطلب مصادقة

---

## 17. الأمان — قواعد مهمة

- **لا** ترفع `.env` أو `storage/*.key` إلى Git
- **لا** تضمّن نسخ SQL إنتاجية في المستودع
- غيّر كلمة مرور المشرف الافتراضية في أي بيئة غير محلية
- في الإنتاج: `APP_DEBUG=false`, `APP_ENV=production`
- Document root على السيرفر = مجلد **`public/`** فقط

---

## 18. النشر على الإنتاج (ملخص)

1. ارفع الكود (بدون `vendor/` — شغّل `composer install --no-dev` على السيرفر)
2. أنشئ `.env` من `.env.example` وعدّل القيم
3. `php artisan migrate --force`
4. `php artisan passport:keys --force`
5. `php artisan storage:link`
6. `php artisan config:cache`
7. وجّه Document root إلى `public/`

**دليل تفصيلي:** [docs/DEPLOY-ANAGHEEMHOME.md](docs/DEPLOY-ANAGHEEMHOME.md)

---

## 19. توثيق إضافي

| الموضوع | الملف |
|---------|-------|
| دليل المبرمج التفصيلي | [docs/DEVELOPER-ONBOARDING.md](docs/DEVELOPER-ONBOARDING.md) |
| دليل API | [docs/API-DEVELOPER-GUIDE.md](docs/API-DEVELOPER-GUIDE.md) |
| قائمة Endpoints | [docs/API-ENDPOINTS-LIST.md](docs/API-ENDPOINTS-LIST.md) |
| CORS لـ Flutter | [docs/CORS-SETUP-FOR-FLUTTER-WEB.md](docs/CORS-SETUP-FOR-FLUTTER-WEB.md) |
| نشر الإنتاج | [docs/DEPLOY-ANAGHEEMHOME.md](docs/DEPLOY-ANAGHEEMHOME.md) |
| Redis والكاش | [docs/DEPLOYMENT-REDIS-CACHE.md](docs/DEPLOYMENT-REDIS-CACHE.md) |
| فهرس كل التوثيق | [docs/README.md](docs/README.md) |

---

## 20. سير العمل الموصى به للمبرمج الجديد

```
1. git clone + composer install
2. cp .env.example .env + php artisan key:generate
3. إنشاء DB + تعديل DB_* في .env
4. php artisan migrate --force
5. php artisan db:seed --class=BaitPaitSeeder
6. php artisan passport:keys --force
7. php artisan storage:link
8. php artisan serve
9. ادخل /admin/auth/login → info@baitpait.com / 100200300
10. (اختياري) php artisan db:seed --class=FullTestDataSeeder للبيانات التجريبية
11. اربط تطبيق Flutter بـ http://127.0.0.1:8000/api/v1/
```

---

© Bait Pait — مشروع خاص. الاستخدام والنشر بإذن صاحب المشروع.

*آخر تحديث: يونيو 2026*
