# فحص API لوحة الإدارة — الجودة، السرعة، الأمان

> **التاريخ:** 2026  
> **المشروع:** Elite Vape Backend  
> **المسار:** `/admin/*` (نقاط النهاية التي تُرجع JSON)

---

## 1. ملخص تنفيذي

| المجال | الحالة | الملاحظات |
|--------|--------|-----------|
| **الجودة** | جيدة | تحقق، ربط آمن، هيكلة منظمة |
| **السرعة** | متوسطة | استعلامات متكررة، كاش محدود |
| **الأمان** | جيدة | مصادقة، CSRF، جلسات |

---

## 2. نقاط النهاية (Endpoints) التي تُرجع JSON

هذه النقاط تُستدعى عبر AJAX من لوحة الإدارة:

| المسار | الطريقة | الوظيفة | التردد |
|--------|---------|---------|--------|
| `/admin/get-store-data` | GET | عدادات (طلبات جديدة، تواصل، طلبات نوع) | كل 10 ثوانٍ |
| `/admin/order-stats` | POST | إحصائيات الطلبات حسب الحالة | عند تغيير الفلتر |
| `/admin/unified-search` | GET | بحث في طلبات، منتجات، عملاء | عند الكتابة |
| `/admin/dashboard/earning-statistics` | GET | إحصائيات الأرباح | عند تحميل الداشبورد |
| `/admin/orders/quick-view` | GET | عرض سريع لطلب | عند الطلب |
| `/admin/product/autocomplete` | GET | اقتراح منتجات | عند الكتابة |
| `/admin/product/search` | POST | بحث منتجات | عند الطلب |
| `/admin/tag/search` | GET | بحث وسوم | عند الكتابة |

---

## 3. الجودة (Quality)

### 3.1 ✅ نقاط قوة

| البند | التفاصيل |
|-------|----------|
| **المصادقة** | جميع المسارات محمية بـ `admin` middleware |
| **Parameter binding** | `unifiedSearch` يستخدم `whereRaw(..., ["%{$q}%"])` — آمن |
| **حد البحث** | `unifiedSearch` يحد النتائج بـ `limit(5)` لكل نوع |
| **التحقق** | `switchLang` يتحقق من `in_array($locale, ['en','ar'])` |
| **استجابة JSON** | `expectsJson()` في AdminMiddleware — 401 للمطالبات غير المصادقة |

### 3.2 ⚠️ تحسينات مقترحة

| # | البند | الموقع | التوصية |
|---|--------|--------|---------|
| 1 | **تحقق إضافي في unifiedSearch** | `SystemController::unifiedSearch` | إضافة `max_length` لـ `$q` (مثلاً 100 حرف) لتجنب استعلامات طويلة |
| 2 | **تحقق في order-stats** | `orderStats` | `statistics_type` يُخزن في الجلسة — التحقق من القيم المسموحة (`today`, `this_month`, `this_year`) |
| 3 | **FormRequest** | Controllers | استخدام FormRequest بدلاً من `$request->validate()` مباشرة لتحسين إعادة الاستخدام |

---

## 4. السرعة (Performance)

### 4.1 ✅ ما تم

| البند | التفاصيل |
|-------|----------|
| **كاش الشريط الجانبي** | `admin_sidebar_order_counts` — 60 ثانية |
| **كاش المخزون المنخفض** | `admin_header_low_stock` — 120 ثانية |
| **حد النتائج** | `unifiedSearch` — 5 نتائج لكل نوع |
| **Eager loading** | استخدام `with()` في بعض الاستعلامات |

### 4.2 ⚠️ تحسينات مقترحة

| # | البند | الموقع | التأثير | التوصية |
|---|--------|--------|---------|---------|
| 1 | **get-store-data كل 10 ثوانٍ** | `app.blade.php:311` | استعلامات متكررة | إضافة كاش قصير (5–10 ثوانٍ) أو زيادة الفاصل إلى 30 ثانية |
| 2 | **order_stats_data** | `SystemController` | 9 استعلامات COUNT | دمج في استعلام واحد باستخدام `selectRaw` و `CASE WHEN` |
| 3 | **Dashboard** | `dashboard()` | عدة استعلامات ثقيلة | كاش للـ `topSell`, `mostRatedProducts`, `topCustomer` (مثلاً 5 دقائق) |
| 4 | **getEarningStatistics** | `getEarningStatistics` | استعلامات حسب الشهر | كاش حسب `$dateType` (مثلاً 10 دقائق) |

### 4.3 استعلامات get-store-data

```php
// SystemController::storeData()
$newOrder = DB::table('orders')->where(['checked' => 0])->count();
$pendingTypeApproval = User::whereNotNull('requested_user_type_id')->count();
$newContactUs = ContactUs::unread()->count();
```

**ثلاثة استعلامات** تُنفَّذ كل 10 ثوانٍ لكل مشرف متصل. إضافة كاش 10 ثوانٍ يقلل الحمل بشكل كبير.

---

## 5. الأمان (Security)

### 5.1 ✅ ما تم

| البند | التفاصيل |
|-------|----------|
| **مصادقة Admin** | `AdminMiddleware` — تحويل إلى login إن لم يكن مسجلاً |
| **CSRF** | مسارات Admin ضمن `web` — حماية CSRF مفعّلة |
| **الجلسات** | `EncryptCookies`, `StartSession`, `AuthenticateSession` |
| **SQL Injection** | استخدام Query Builder و parameter binding |
| **XSS** | Blade `{{ }}` للهروب التلقائي |
| **activation-check** | صفحات حساسة (إعدادات، عملات، إلخ) |

### 5.2 ⚠️ نقاط للمراجعة

| # | البند | الخطورة | التوصية |
|---|--------|---------|---------|
| 1 | **لا throttle على Admin** | منخفضة | مسارات Admin لا تستخدم rate limiting — إساءة استخدام من حساب مسرب قد تسبب حملًا |
| 2 | **unifiedSearch بدون throttle** | منخفضة | بحث مفتوح — إضافة `throttle:30,1` للحد من عمليات المسح |
| 3 | **CORS** | منخفضة | لوحة Admin تُستخدم من نفس النطاق — لا حاجة لـ CORS عادة |

---

## 6. توصيات تنفيذية (حسب الأولوية)

### أولوية عالية
1. **كاش get-store-data** — تقليل الحمل من الاستعلام كل 10 ثوانٍ.
2. **دمج order_stats_data** — استعلام واحد بدلاً من 9.

### أولوية متوسطة
3. **كاش Dashboard** — للبيانات الثقيلة (topSell، mostRated، topCustomer).
4. **throttle على unified-search** — حماية من إساءة الاستخدام.

### أولوية منخفضة
5. **FormRequest** — تحسين جودة الكود.
6. **التحقق من statistics_type** — قيم مسموحة فقط.

---

## 7. مراجع

- `AUDIT-QUALITY-SECURITY-PERFORMANCE.md` — فحص عام للنظام
- `API-EXAMINATION-REPORT.md` — فحص API التطبيق (`/api/v1`)
- `DEPLOYMENT-REDIS-CACHE.md` — استخدام Redis للكاش في الإنتاج
