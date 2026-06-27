# خطة علاجية — بناءً على تقرير الفحص الشامل

> **المصدر:** [AUDIT-QUALITY-SECURITY-PERFORMANCE.md](AUDIT-QUALITY-SECURITY-PERFORMANCE.md)  
> **التاريخ:** 2025-03

---

## نظرة عامة

| المرحلة | المهام | الجهد الإجمالي |
|---------|--------|----------------|
| **المرحلة 1** — أمان حرج | 4 مهام | ~3 ساعات |
| **المرحلة 2** — أمان وجودة | 4 مهام | ~6 ساعات |
| **المرحلة 3** — أداء وقاعدة بيانات | 2 مهام | ~3 ساعات |
| **المرحلة 4** — تحسينات إضافية | 3 مهام | ~4 ساعات |

---

## المرحلة 1 — أمان حرج (أسبوع 1)

### المهمة 1.1 — ضبط CORS للإنتاج

**الهدف:** منع استخدام `*` في بيئة الإنتاج.

**الملفات:**
- `config/cors.php`
- `.env.example`

**الخطوات:**

1. إضافة في `.env.example`:
```
# CORS — في الإنتاج حدد النطاقات المسموحة (مفصولة بفاصلة)
# مثال: https://app.example.com,https://admin.example.com
CORS_ALLOWED_ORIGINS=
```

2. توثيق في `config/cors.php` (تعليق فوق `allowed_origins`):
```php
// في الإنتاج: حدد CORS_ALLOWED_ORIGINS في .env (مثلاً: https://app.example.com)
// إن ترك فارغاً: يُستخدم * (غير آمن للإنتاج)
```

3. في السيرفر الإنتاجي: إضافة `CORS_ALLOWED_ORIGINS=https://your-domain.com` في `.env`

**التحقق:** طلب من نطاق غير مسموح يُرجع 403 أو لا يتلقى CORS headers.

---

### المهمة 1.2 — إزالة OTP من Logs

**الهدف:** منع تسريب OTP في ملفات اللوج.

**الملفات:**
- `app/Http/Controllers/Api/V1/Auth/PasswordResetController.php`
- `app/Http/Controllers/Api/V1/Auth/CustomerAuthController.php`

**الخطوات:**

1. البحث عن `Log::info` و `Log::debug` التي تحتوي `otp` أو `token` في الـ payload

2. استبدال:
```php
Log::info('...', ['email' => $email, 'otp' => $token]);
```
بـ:
```php
Log::info('...', ['email' => $email, 'event' => 'otp_sent']);
```

3. التحقق من عدم وجود أي `otp` أو `token` في أي `Log::` في هذين الملفين

**التحقق:** إرسال OTP ثم فحص `storage/logs/laravel.log` — لا يجب أن يظهر الرقم.

---

### المهمة 1.3 — إضافة Cache لـ lowStockCount في Header

**الهدف:** تقليل استعلامات DB في كل طلب.

**الملف:** `app/Providers/AppServiceProvider.php`

**الخطوات:**

1. استبدال ViewComposer للـ header:

```php
// قبل (سطر ~85–90):
View::composer('layouts.admin.partials._header', function ($view) {
    $lowStockCount = Product::lowStock()->count();
    $lowStockProducts = Product::lowStock()->take(15)->get(['id', 'name', 'total_stock']);
    $view->with(compact('lowStockCount', 'lowStockProducts'));
});

// بعد:
View::composer('layouts.admin.partials._header', function ($view) {
    $cacheKey = 'admin_header_low_stock';
    $data = Cache::remember($cacheKey, 120, function () {
        $products = Product::lowStock()->take(15)->get(['id', 'name', 'total_stock']);
        return [
            'lowStockCount' => $products->count(),
            'lowStockProducts' => $products,
        ];
    });
    $view->with('lowStockCount', $data['lowStockCount'])
         ->with('lowStockProducts', $data['lowStockProducts']);
});
```

2. ملاحظة: عند إضافة/تعديل منتج أو تغيير المخزون، مسح الكاش:
   - في `ProductController` (store, update) أو في `Product` Observer
   - `Cache::forget('admin_header_low_stock');`

**التحقق:** فتح لوحة التحكم — التحقق من أن العدد صحيح، وتكرار الطلب بدون تغيير في المخزون — يجب أن يُستخدم الكاش.

---

### المهمة 1.4 — التحقق من path في AddonController

**الهدف:** منع Path Traversal.

**الملف:** `app/Http/Controllers/Admin/System/AddonController.php`

**الخطوات:**

1. إنشاء دالة مساعدة للتحقق:

```php
private function resolveAddonPath(string $path): ?string
{
    $path = trim($path, '/');
    if (empty($path) || str_contains($path, '..')) {
        return null;
    }
    $fullPath = base_path('Modules/' . $path);
    $real = realpath($fullPath);
    $modulesBase = realpath(base_path('Modules'));
    if ($real === false || $modulesBase === false || !str_starts_with($real, $modulesBase)) {
        return null;
    }
    return $path;
}
```

2. في كل استخدام لـ `$request['path']` أو `$request->path`:
   - استبدال بـ `$path = $this->resolveAddonPath($request['path'] ?? '');`
   - إن `$path === null` → `return back()->withErrors(['Invalid addon path']);`

3. تطبيق في: `publish`, `activation`, `deleteAddon`, وأي دالة تستخدم `path`

**التحقق:** محاولة `path=../../../etc` — يجب أن يُرفض.

---

## المرحلة 2 — أمان وجودة (أسبوع 2)

### المهمة 2.1 — إضافة فهارس للجداول الرئيسية

**الهدف:** تحسين استعلامات الطلبات والمنتجات.

**الملف:** إنشاء `database/migrations/2026_03_XX_add_performance_indexes.php`

**الخطوات:**

1. إنشاء migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('order_status');
            $table->index('branch_id');
            $table->index('created_at');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['order_status']);
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['created_at']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['product_id']);
        });
    }
};
```

2. تشغيل: `php artisan migrate`

**التحقق:** `EXPLAIN` على استعلامات الطلبات — يجب أن تظهر استخدام الفهارس.

---

### المهمة 2.2 — validation لـ updateOtp

**الهدف:** منع قيم غير صالحة في إعدادات OTP.

**الملف:** `app/Http/Controllers/Admin/BusinessSettingsController.php`

**الخطوات:**

1. في دالة `updateOtp` (أو ما يقابلها)، إضافة قبل `InsertOrUpdateBusinessData`:

```php
$request->validate([
    'maximum_otp_hit' => 'required|integer|min:1|max:20',
    'otp_resend_time' => 'required|integer|min:30|max:600',
    'temporary_block_time' => 'required|integer|min:60|max:3600',
], [
    'maximum_otp_hit.required' => 'Maximum OTP hit is required',
    'maximum_otp_hit.min' => 'Minimum 1',
    'maximum_otp_hit.max' => 'Maximum 20',
    // ... ترجمات حسب الحاجة
]);
```

2. التأكد من أن أسماء الحقول تطابق النموذج في الواجهة.

**التحقق:** إرسال قيم غير صالحة (مثل -1 أو 9999) — يجب أن يُرجع validation error.

---

### المهمة 2.3 — XSS في صفحات Business (contentByLang)

**الهدف:** منع XSS في محتوى الصفحات (سياسة الخصوصية، الشروط، إلخ).

**الملفات:**
- `resources/views/admin-views/business-settings/return_page-index.blade.php`
- `resources/views/admin-views/business-settings/refund_page-index.blade.php`
- `resources/views/admin-views/business-settings/cancellation_page-index.blade.php`
- `resources/views/admin-views/business-settings/privacy-policy.blade.php`
- `resources/views/admin-views/business-settings/terms-and-conditions.blade.php`
- `resources/views/admin-views/business-settings/about-us.blade.php`

**الخطوات:**

1. البحث عن `{!! $contentByLang[$lang] ?? '' !!}`

2. استبدال بـ:
```php
{!! \App\CentralLogics\Helpers::sanitizeHtmlForDisplay($contentByLang[$lang] ?? '') !!}
```

3. أو إن كان العرض للقراءة فقط: `{{ $contentByLang[$lang] ?? '' }}` (مع تفقد فقدان التنسيق)

4. الخيار الأفضل: `sanitizeHtmlForDisplay` لأنه يحافظ على التنسيق الآمن.

**التحقق:** إدخال `<script>alert(1)</script>` في المحتوى — يجب ألا يُنفذ.

---

### المهمة 2.4 — تخفيف throttle لـ auth

**الهدف:** تقليل هجمات brute force على تسجيل الدخول و reset password.

**الملف:** `bootstrap/app.php` أو `routes/api/v1/api.php`

**الخطوات:**

1. إنشاء throttle group خاص:
```php
// في RouteServiceProvider أو في bootstrap/app.php
RateLimiter::for('auth', function (Request $request) {
    return Limit::perMinute(10)->by($request->ip());
});
```

2. تطبيق على مسارات auth:
```php
Route::middleware(['throttle:auth'])->group(function () {
    // مسارات auth، password-reset، verify-otp
});
```

3. أو استخدام `throttle:10,1` بدلاً من `throttle:60,1` لمسارات auth فقط.

**التحقق:** إرسال أكثر من 10 طلبات auth في دقيقة من نفس IP — يجب أن يُرجع 429.

---

## المرحلة 3 — أداء وقاعدة بيانات

### المهمة 3.1 — cap لـ get_customers_also_bought

**الهدف:** منع استعلامات ثقيلة إن تم استدعاء الدالة مع limit كبير.

**الملف:** `app/CentralLogics/ProductLogic.php`

**الخطوات:**

1. في `get_customers_also_bought`:
```php
$limit = Helpers::capApiLimit($limit ?? 10);
```

2. التأكد من أن أي استدعاء من API يمرر limit من المستخدم يستخدم cap.

**التحقق:** استدعاء API مع `limit=1000` — يجب أن يُحد إلى 50.

---

### المهمة 3.2 — Redis للإنتاج

**الهدف:** تحسين أداء الكاش والجلسات.

**المرجع:** `docs/DEPLOYMENT-REDIS-CACHE.md`

**الخطوات:**

1. تثبيت Redis على السيرفر
2. في `.env` للإنتاج:
```
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```
3. تنظيف الكاش بعد التبديل: `php artisan config:clear && php artisan cache:clear`

**التحقق:** `php artisan tinker` → `Cache::put('test', 1);` → `Cache::get('test')` — يجب أن يرجع 1.

---

## المرحلة 4 — تحسينات إضافية

### المهمة 4.1 — تصحيح getEarningStatitics

**الملف:** `app/Http/Controllers/Admin/SystemController.php`

**الخطوة:** إعادة تسمية `getEarningStatitics` → `getEarningStatistics` في كل مكان (الدالة، route، أي استدعاء).

---

### المهمة 4.2 — استخراج OTP logic إلى Service

**الهدف:** تقليل التكرار بين CustomerAuthController و PasswordResetController.

**الخطوات:**

1. إنشاء `app/Services/OtpService.php`

2. نقل منطق: rate limit، block time، hit count، إرسال OTP

3. استدعاء الـ Service من كلا الـ Controllers

---

### المهمة 4.3 — AuthenticateSession

**الملف:** `bootstrap/app.php`

**الخطوة:** إعادة تفعيل `AuthenticateSession::class` في مجموعة `web` إن كان مدعوماً في إصدار Laravel الحالي. (قد يكون الاسم قد تغيّر في Laravel 11+ — راجع الـ docs)

---

## سجل التنفيذ

| # | المهمة | الحالة | التاريخ |
|---|--------|--------|---------|
| 1.1 | CORS للإنتاج | ✅ | 2025-03 |
| 1.2 | إزالة OTP من Logs | ✅ (كانت مُصلحة) | — |
| 1.3 | Cache lowStockCount | ✅ | 2025-03 |
| 1.4 | AddonController path | ✅ (كانت مُصلحة) | — |
| 2.1 | فهارس DB | ✅ | 2025-03 |
| 2.2 | validation updateOtp | ✅ (كانت مُصلحة) | — |
| 2.3 | XSS contentByLang | ✅ (كانت مُصلحة) | — |
| 2.4 | throttle auth | ✅ | 2025-03 |
| 3.1 | cap get_customers_also_bought | ✅ | 2025-03 |
| 3.2 | Redis للإنتاج | ✅ (توثيق) | 2025-03 |
| 4.1 | getEarningStatistics | ✅ (كانت مُصلحة) | — |
| 4.2 | OtpService | ⬜ (اختياري) | |
| 4.3 | AuthenticateSession | ✅ | 2025-03 |

---

*استخدم هذا الملف لتتبع التقدم. عند إكمال كل مهمة، حدّث الحالة إلى ✅ وأضف التاريخ.*
