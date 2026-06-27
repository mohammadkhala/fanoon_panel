# تقرير فحص شامل — الجودة، الأمان، السرعة

> **التاريخ:** 2025-03  
> **المشروع:** Elite Vape Backend (Laravel 12)

---

## 1. ملخص تنفيذي

| المجال | الحالة العامة | عدد النقاط |
|--------|---------------|------------|
| **الجودة** | جيدة مع تحسينات | 8 |
| **الأمان** | جيدة — ثغرات حرجة مُصلحة | 10 |
| **السرعة** | متوسطة — يحتاج تحسينات | 6 |
| **قاعدة البيانات** | جيدة — فهارس ناقصة | 4 |

---

## 2. الجودة (Quality)

### 2.1 ✅ ما تم بشكل صحيح

| البند | التفاصيل |
|-------|----------|
| **حد API** | `Helpers::capApiLimit()` و `capApiOffset()` — حد أقصى 50 |
| **استخدام capApiLimit** | ProductController، CustomerController، ConversationController، ProductLogic، FlashSaleController |
| **Parameter binding** | معظم `whereRaw` و `havingRaw` تستخدم `?` و bindings |
| **Validation** | Controllers تستخدم `$request->validate()` |
| **sanitizeHtmlForDisplay** | موجود في Helpers — يستخدم للمحتوى HTML |
| **Cache الشريط الجانبي** | `admin_sidebar_order_counts` — 60 ثانية |

### 2.2 ⚠️ نقاط تحتاج مراجعة

| # | البند | الملف | التوصية |
|---|--------|-------|---------|
| 1 | **orderByRaw مع concatenation** | `ProductLogic.php:83` | `FIELD(id, {$ids})` — `$ids` من DB (pluck) آمن، لكن يُفضّل التأكد أن `get_customers_also_bought` لا يستقبل `limit` من المستخدم دون cap |
| 2 | **AuthenticateSession معطّل** | `bootstrap/app.php:49` | مُعلّق — يقلل حماية session fixation. إعادة تفعيله إن كان مدعوماً |
| 3 | **تصحيح إملائي** | `SystemController.php` | `getEarningStatitics` → `getEarningStatistics` |
| 4 | **كود مكرر OTP** | CustomerAuthController، PasswordResetController | استخراج منطق OTP إلى Service مشترك |
| 5 | **{!! !!} في Blade** | pagination، Toastr | Laravel pagination آمن؛ تحقق من Toastr أنه لا يخرج محتوى مستخدم |

---

## 3. الأمان (Security)

### 3.1 ✅ ما تم إصلاحه

| البند | الحالة |
|-------|--------|
| مسار `/add-currency` | محذوف |
| CSRF exemptions | محدودة لمسارات الدفع فقط |
| OTP في production | `config('app.env') === 'local'` للـ bypass |
| تحميل الملفات | `uploadFile()` مع whitelist امتدادات |
| وصف المنتج XSS | `Helpers::sanitizeHtmlForDisplay()` |
| Payment status null check | موجود في OrderController |

### 3.2 ⚠️ نقاط تحتاج مراجعة

| # | البند | الملف | الخطورة | التوصية |
|---|--------|-------|---------|---------|
| 1 | **CORS `*` في الإنتاج** | `config/cors.php:22-24` | متوسطة | ضبط `CORS_ALLOWED_ORIGINS` في `.env` للإنتاج — لا تستخدم `*` |
| 2 | **API throttle 60/دقيقة** | `bootstrap/app.php:56` | منخفضة | تخفيف حد auth و password-reset (مثلاً 10/دقيقة) |
| 3 | **Path traversal في AddonController** | `AddonController.php` | عالية | التحقق من `path` ضد قائمة معروفة (مثلاً `Modules/*`) |
| 4 | **OTP في Logs** | PasswordResetController، CustomerAuthController | متوسطة | إزالة OTP من الـ log — تسجيل `otp_sent` فقط |
| 5 | **updateOtp بدون validation** | BusinessSettingsController | متوسطة | إضافة `validate` للأرقام (min, max) |
| 6 | **XSS في صفحات Business** | return_page، refund_page، privacy-policy، إلخ | متوسطة | استخدام `sanitizeHtmlForDisplay` لـ `contentByLang` |

### 3.3 SQL Injection — التحقق

| الموقع | الطريقة | الحالة |
|--------|---------|--------|
| ProductController whereRaw | `[(string) $categoryId]` | ✅ آمن |
| ProductController havingRaw | `[$ratingVal]` | ✅ آمن |
| SystemController whereRaw | `["%{$q}%"]` binding | ✅ آمن |
| ProductLogic havingRaw | `[$rating]` | ✅ آمن |
| CustomerController havingRaw | `[500]`, `[$cutoff]` | ✅ آمن |
| ReportController DB::raw | تجميع فقط (SUM, COUNT) | ✅ آمن |
| ProductLogic orderByRaw | `$ids` من pluck (أرقام) | ✅ آمن — مصدر DB |

---

## 4. السرعة (Performance)

### 4.1 ✅ ما تم

| البند | التفاصيل |
|-------|----------|
| كاش الشريط الجانبي | 60 ثانية لعدد الطلبات |
| كاش API configuration | `api_v1_configuration_payload_v1` |
| Eager loading | `with()`, `withCount()` في المنتجات |
| حد limit API | منع استعلامات ثقيلة |

### 4.2 ⚠️ تحسينات مقترحة

| # | البند | الملف/الموقع | التوصية |
|---|--------|---------------|---------|
| 1 | **Header lowStockCount** | AppServiceProvider:86 | استعلامان لكل طلب — إضافة Cache (مثلاً 2 دقيقة) |
| 2 | **Redis في الإنتاج** | config/cache | استخدام Redis بدلاً من File — راجع `DEPLOYMENT-REDIS-CACHE.md` |
| 3 | **N+1 محتمل** | صفحات قوائم | مراجعة `with()` في Order list، Customer list |
| 4 | **Lazy loading للصور** | قوائم المنتجات | إضافة `loading="lazy"` — تم في API حسب التقارير |
| 5 | **Queue للـ Webhooks** | DispatchWebhookJob | التأكد من تشغيل `php artisan queue:work` في الإنتاج |

---

## 5. قاعدة البيانات

### 5.1 الفهارس الموجودة

| الجدول | الفهارس |
|--------|---------|
| orders | — (لا فهارس صريحة على user_id, order_status, created_at) |
| products | — (لا فهارس على status, category_ids) |
| order_details | — |
| order_areas | order_id, branch_id, area_id |
| translations | translationable_id, locale |
| delivery_charge_setups | branch_id |
| areas | branch_id |

### 5.2 فهارس مقترحة لتحسين الأداء

```php
// Migration مقترحة
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
```

---

## 6. خطة تنفيذ مقترحة

### أولوية عالية (أسبوع 1)

| # | المهمة | الجهد |
|---|--------|-------|
| 1 | ضبط CORS للإنتاج | 30 دقيقة |
| 2 | إزالة OTP من Logs | 30 دقيقة |
| 3 | إضافة Cache لـ lowStockCount في Header | 1 ساعة |
| 4 | التحقق من path في AddonController | 1 ساعة |

### أولوية متوسطة (أسبوع 2)

| # | المهمة | الجهد |
|---|--------|-------|
| 5 | إضافة فهارس للجداول الرئيسية | 2 ساعة |
| 6 | validation لـ updateOtp | 1 ساعة |
| 7 | XSS لصفحات Business (contentByLang) | 2 ساعة |
| 8 | تخفيف throttle لـ auth | 30 دقيقة |

### أولوية منخفضة

| # | المهمة |
|---|--------|
| 9 | تصحيح getEarningStatitics |
| 10 | استخراج OTP logic إلى Service |
| 11 | Redis للإنتاج |

---

## 7. خطة العلاج

| الملف | الوصف |
|-------|--------|
| **[PLAN-AUDIT-TREATMENT.md](PLAN-AUDIT-TREATMENT.md)** | خطة علاجية تفصيلية — خطوات تنفيذ كل توصية |

---

## 8. مراجع

| الملف | الوصف |
|-------|--------|
| [SECURITY_AUDIT_REPORT.md](../SECURITY_AUDIT_REPORT.md) | تقرير أمان سابق |
| [CODE_QUALITY_SECURITY_AUDIT_REPORT.md](../CODE_QUALITY_SECURITY_AUDIT_REPORT.md) | جودة وأمان |
| [DEPLOYMENT-REDIS-CACHE.md](DEPLOYMENT-REDIS-CACHE.md) | إعداد Redis |
| [PLAN-FIRST-RELEASE-PRIORITIES.md](PLAN-FIRST-RELEASE-PRIORITIES.md) | أولويات الإصدار |

---

*تم إعداد هذا التقرير بناءً على فحص الكود والتقارير السابقة.*
