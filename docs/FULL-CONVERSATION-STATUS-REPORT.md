# التقرير الشامل — المحادثة والخطط وما لم يُنجز

**التاريخ:** 2026  
**النطاق:** مراجعة كاملة للمحادثة، الخطط، قاعدة البيانات، والصفحات غير المرتبطة

---

## الجزء 1: ما تم إنجازه (من المحادثة)

| المحور | التفاصيل |
|--------|----------|
| **ملفات تعريف الارتباط** | قيمة افتراضية، migration، تعليمات مفصّلة، إصلاح مفتاح مكرر |
| **ترتيب إعدادات الأعمال** | تبويبات مجمّعة منطقياً |
| **رسالة واتساب في إعدادات التطبيق** | تنبيه أحمر، نص أبيض، رابط واتساب |
| **قسم الدعم في الشريط الجانبي** | صورة مع رابط واتساب |
| **إزالة كلمة مرور الفرع** | الفرع لا يدخل للنظام — المسؤول فقط |
| **المهمة 8.2 دليل المستخدم** | مراجعة واختبار، إزالة fcm_alert_need_help |
| **الخطة العلاجية** | LsLibController، حد API، whereRaw، كاش الطلبات، ILS |

---

## الجزء 2: ما لم يُنجز من الخطط

### 2.1 خطة تحسين النظام (PLAN-SYSTEM-IMPROVEMENTS)

| المرحلة | المهام المعلّقة |
|---------|-----------------|
| **المرحلة 1** | توحيد المصطلحات (restaurant → store)، مراجعة API V2، Modules |
| **المرحلة 2** | Redis للكاش، تحميل كسول للصور، ضغط الاستجابة |
| **المرحلة 3 — UX** | وسوم المنتجات، فلاتر متقدمة، سجل تغييرات الطلبات، بحث موحّد |
| **المرحلة 4** | توثيق API (OpenAPI/Swagger)، Webhooks، تقارير متقدمة، اشتراكات |

### 2.2 خطة دليل المستخدم (USER-GUIDE)

| المهمة | الحالة |
|--------|--------|
| إثراء صفحة مركز المساعدة | المحتوى (روابط سريعة، خطوات البدء) غير معروض |
| رابط دليل الاستخدام في الشريط الجانبي | غير موجود (موجود في الـ Header فقط) |
| المرحلة 7 — الجولة التفاعلية (Tour) | لم تُنفّذ (اختياري) |

### 2.3 خطة إصلاح الكود (PLAN-FIX-CODE-AFTER-DB-MIGRATION)

معظم الإصلاحات الحرجة مُنفّذة. قد تبقى تحسينات null safety في مواضع نادرة.

---

## الجزء 3: ما تم إزالته من قاعدة البيانات

### 3.1 Migrations المنفّذة

| Migration | الإجراء |
|-----------|---------|
| `2026_03_08_000003_remove_recaptcha_from_business_settings` | حذف مفتاح `recaptcha` من `business_settings` |
| `2026_01_01_000000_baitpait_full_schema` | Schema جديد — **لا يتضمن** الجداول التالية من النظام القديم |

### 3.2 جداول مُستثناة من Schema BaitPait (لم تُنشأ)

حسب تعليق الـ migration:

| الجدول | السبب |
|--------|-------|
| `delivery_men` | ميزة مندوبي التوصيل مُزالة |
| `d_m_reviews` | مراجعات المندوبين |
| `delivery_histories` | سجل التوصيل |
| `dc_conversations` | محادثات المندوبين |
| `order_delivery_histories` | سجل توصيل الطلبات |
| `newsletters` | النشرة البريدية غير مُنفذة |
| `track_deliverymen` | تتبع موقع المندوب غير مُنفذ |
| `soft_credentials` | جدول قديم غير مستخدم |
| `wallet_transactions` | معاملات المحفظة غير مُنفذة |
| `user_accounts` | حسابات المحفظة غير مُنفذة |

### 3.3 تعديلات على الجداول

| الجدول | التعديل |
|--------|---------|
| `orders` | لا يوجد `delivery_man_id` |
| `messages` | لا يوجد `deliveryman_id` |

---

## الجزء 4: الصفحات غير المرتبطة أو غير المستخدمة

### 4.1 صفحات يتعرّض الوصول إليها لخطأ (ملفات مفقودة)

| الصفحة | Controller | الحالة |
|--------|------------|--------|
| `currency-update` | BusinessSettingsController::currencyEdit | الملف `currency-update.blade.php` **غير موجود** — يسبب 500 عند التعديل |
| `db-index` | DatabaseSettingsController::databaseIndex | الملف `db-index.blade.php` **غير موجود** — لا route له |
| `location-index` | LocationSettingsController::locationIndex | الملف `location-index.blade.php` **غير موجود** — لا route له |

### 4.2 صفحات يتيمة (Orphan) — لا route أو لا رابط — **تم حذفها**

| الصفحة | السبب | الحالة |
|--------|-------|--------|
| `component.blade.php` | لا Controller ولا Route — قالب تطوير/اختبار | ✅ **مُحذوف** |
| `messages/index.blade.php` + partials | ConversationController::list — لا route في admin | ✅ **مُحذوف** |
| `business-settings/delivery-fee.blade.php` + `partial/delivery_charge_form.blade.php` | DeliveryChargeSetupController — لا route في admin | ✅ **مُحذوف** |
| `system/addon/index.blade.php` + partials | AddonController — لا route في admin | ✅ **مُحذوف** |

### 4.3 صفحات مُزالة أو مُحوّلة

| الصفحة | الحالة |
|--------|--------|
| `recaptcha-index.blade.php` | **مُزالة** — recaptchaIndex يعيد التوجيه إلى login-setup |
| روابط recaptcha في business-setup-nav | **مُزالة** — غير موجودة في القائمة الحالية |

### 4.4 صفحات مرتبطة عبر التبويبات فقط (ليست في الشريط الجانبي)

هذه الصفحات تُفتح من `business-setup-nav` أو روابط داخلية:

- `social-media-login` — من chat-index أو روابط أخرى
- `firebase-auth`، `firebase-config-index` — من business-setup-nav
- `cancellation_page-index`، `return_page-index`، `refund_page-index` — من page-nav
- `about-us`، `terms-and-conditions`، `privacy-policy` — من page-nav أو sidebar (قسم Pages)

---

## الجزء 5: خطة الخطوة الكاملة — ما يجب تنفيذه

### 5.1 أولوية عالية — إصلاح أخطاء

| الإجراء | التفاصيل |
|---------|----------|
| إصلاح أو إزالة `currencyEdit` | إما إنشاء `currency-update.blade.php` أو إزالة Route و Controller method |
| إزالة أو تعطيل `databaseIndex` | الملف غير موجود — إزالة الاستدعاء أو إنشاء view بسيط |
| إزالة أو تعطيل `locationIndex` | الملف غير موجود — إزالة Route أو إنشاء view |

### 5.2 أولوية متوسطة — تنظيف الصفحات اليتيمة — **تم**

| الإجراء | الملفات | الحالة |
|---------|---------|--------|
| حذف | `component.blade.php` | ✅ تم |
| حذف | `messages/index.blade.php` + partials (_list، _conversations) | ✅ تم |
| حذف | `delivery-fee.blade.php` + `partial/delivery_charge_form.blade.php` | ✅ تم |
| حذف | `system/addon/index.blade.php` + partials (activation-modal، activation-modal-data) | ✅ تم |

### 5.3 أولوية منخفضة — تحسينات الخطط

| الخطة | الإجراءات |
|-------|-----------|
| PLAN-SYSTEM-IMPROVEMENTS | تنفيذ المرحلة 3 (UX) والمرحلة 4 |
| USER-GUIDE | إثراء صفحة المساعدة، إضافة رابط في الشريط الجانبي |
| USER-GUIDE | المرحلة 7 (Tour) — اختياري |

---

## الجزء 6: ملخص تنفيذي

| المحور | الحالة |
|--------|--------|
| **ما تم في المحادثة** | cookies، ترتيب التبويبات، واتساب، إزالة كلمة مرور الفرع، المهمة 8.2 |
| **قاعدة البيانات** | recaptcha مُزال؛ جداول delivery_men، newsletters، إلخ غير موجودة في Schema |
| **صفحات مفقودة** | currency-update، db-index، location-index |
| **صفحات يتيمة** | تم حذفها: component، messages، delivery-fee، addon |
| **خطط معلّقة** | UX، توثيق API، Webhooks، إثراء مركز المساعدة، Tour |

---

## الملفات المرجعية

| الملف | الوصف |
|-------|-------|
| `docs/PLAN-SYSTEM-IMPROVEMENTS.md` | خطة التحسينات |
| `docs/USER-GUIDE-IMPLEMENTATION-PLAN.md` | خطة دليل المستخدم |
| `docs/USER-GUIDE-STATUS-REPORT.md` | تقرير حالة دليل المستخدم |
| `docs/UNUSED-TABLES-REPORT.md` | جداول غير مستخدمة |
| `docs/POST-DB-MIGRATION-ISSUES-REPORT.md` | مشاكل ما بعد الهجرة |
| `docs/CONVERSATION-STATUS-REPORT.md` | تقرير حالة المحادثة |
