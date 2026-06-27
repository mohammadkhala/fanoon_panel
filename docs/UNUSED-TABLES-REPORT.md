# تقرير الجداول غير المستخدمة في نظام Bait Pait

> تم إعداد هذا التقرير بناءً على تحليل الكود والموديلات والـ Controllers
>
> **تم التنفيذ:** تم حذف الجداول والموديلات المرتبطة (migration: `2026_03_10_000000_drop_unused_tables`)

---

## 1. ملخص سريع

| # | الجدول | السبب | التوصية |
|---|--------|-------|---------|
| 1 | `newsletters` | لا يوجد استخدام في الكود | يمكن حذفه |
| 2 | `track_deliverymen` | لا يوجد استخدام في الكود | يمكن حذفه |
| 3 | `soft_credentials` | لا Model ولا استخدام | يمكن حذفه |
| 4 | `wallet_transactions` | Model موجود لكن غير مستدعى | يمكن حذفه |
| 5 | `user_accounts` | علاقة موجودة لكن غير مستخدمة | يمكن حذفه |

---

## 2. تفاصيل كل جدول

### 2.1 `newsletters`

- **الموديل:** `App\Models\Newsletter` موجود
- **الاستخدام:** لا يوجد أي استدعاء لـ `Newsletter::` في Controllers أو Routes
- **الوظيفة المتوقعة:** اشتراك النشرة البريدية
- **الخلاصة:** الجدول والموديل جاهزان لكن ميزة الاشتراك غير مُنفذة

---

### 2.2 `track_deliverymen`

- **الموديل:** `App\Models\TrackDeliveryman` موجود
- **الاستخدام:** لا يوجد أي استدعاء لـ `TrackDeliveryman::` في الكود
- **الوظيفة المتوقعة:** تتبع موقع مندوب التوصيل (longitude, latitude)
- **الخلاصة:** الجدول والموديل جاهزان لكن ميزة التتبع غير مُنفذة

---

### 2.3 `soft_credentials`

- **الموديل:** لا يوجد
- **الاستخدام:** يُذكر فقط في `DatabaseSettingsController` ضمن قائمة الجداول المستثناة من التصدير
- **الهيكل:** `id`, `key`, `value`, `created_at`, `updated_at`
- **الخلاصة:** جدول قديم/احتياطي، لا يُقرأ ولا يُكتب في منطق التطبيق

---

### 2.4 `wallet_transactions`

- **الموديل:** `App\Models\WalletTransaction` موجود
- **الاستخدام:** لا يوجد أي استدعاء لـ `WalletTransaction::` في الكود
- **الوظيفة المتوقعة:** سجل معاملات المحفظة (ترجمة: `wallet_transaction_history` في ملفات اللغة)
- **الخلاصة:** ميزة المحفظة مُعدّة في قاعدة البيانات لكن غير مُنفذة في التطبيق

---

### 2.5 `user_accounts`

- **الموديل:** `App\Models\UserAccount` موجود
- **العلاقة:** `User` لديه `userAccount()` (morphOne)
- **الاستخدام:** لا يوجد أي استخدام لـ `$user->userAccount` أو `UserAccount::` في Controllers أو Resources
- **الوظيفة المتوقعة:** رصيد المحفظة للمستخدم (`wallet_balance`)
- **الخلاصة:** مرتبط بميزة المحفظة غير المُنفذة

---

## 3. جداول نظامية (لا تُحذف)

هذه الجداول مطلوبة من Laravel أو Passport:

| الجدول | الغرض |
|--------|-------|
| `failed_jobs` | Laravel Queue |
| `password_resets` | إعادة تعيين كلمة المرور |
| `migrations` | سجل الـ migrations |
| `oauth_access_tokens` | Passport API |
| `oauth_auth_codes` | Passport |
| `oauth_clients` | Passport |
| `oauth_personal_access_clients` | Passport |
| `oauth_refresh_tokens` | Passport |

---

## 4. التوصيات

### خيار أ: الحذف (لتبسيط قاعدة البيانات)

إذا كنت متأكداً أنك لن تحتاج لهذه الميزات:

1. إنشاء migration لحذف الجداول الخمسة
2. حذف أو تعطيل الموديلات: `Newsletter`, `TrackDeliveryman`, `WalletTransaction`, `UserAccount`
3. إزالة علاقة `userAccount()` من موديل `User`

### خيار ب: الإبقاء (للتنفيذ لاحقاً)

إذا كنت تخطط لتنفيذ هذه الميزات مستقبلاً:

- أبقِ الجداول والموديلات كما هي
- لا حاجة لأي تعديل

### خيار ج: الإبقاء مع توثيق

- أبقِ الجداول
- أضف تعليقات في الكود توضّح أنها لم تُنفذ بعد
- حدّث `PLAN-FRESH-DATABASE-BAITPAIT.md` لاستثناء هذه الجداول من النسخ المختصرة إن رغبت

---

## 5. ملاحظة بخصوص `product_translations`

- لا يوجد جدول باسم `product_translations` في قاعدة البيانات
- الـ migration `create_product_translations_table` ينشئ جدول `translations` (وليس product_translations)
- جدول `translations` **مُستخدم** عبر موديل `Translation` وعلاقة `Product::translations()`

---

**تاريخ التقرير:** 2025  
**المصدر:** تحليل الكود في `elitevapeDB`
