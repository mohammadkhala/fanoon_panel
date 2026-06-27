# خطة المهمة 8.2 — مراجعة واختبار دليل المستخدم

**التاريخ:** 2026  
**المصدر:** USER-GUIDE-IMPLEMENTATION-PLAN.md — المرحلة 8

---

## نطاق المهمة

| البند | الوصف |
|-------|-------|
| 8.2.1 | اختبار RTL — التأكد من أن واجهة مركز المساعدة والصفحات تعمل بشكل صحيح مع العربية |
| 8.2.2 | اختبار الترجمات — البحث عن مفاتيح ناقصة أو مكررة أو غير مستخدمة |
| 8.2.3 | مراجعة المحتوى — التحقق من اتساق محتوى التعليمات والمساعدة |

---

## خطوات التنفيذ

### 1. تنظيف مفاتيح الترجمة غير المستخدمة
- [x] إزالة `fcm_alert_need_help` من `ar/messages.php` (والمفتاح المكرر)
- [x] إزالة `fcm_alert_need_help` من `en/messages.php`

### 2. اختبار RTL
- [x] مراجعة `dir="rtl"` و `lang="ar"` في layouts — مُطبّق في `layouts/admin/app.blade.php` عبر `$isRtl` و `direction-rtl`
- [x] التحقق من أن مركز المساعدة يعرض بشكل صحيح — يرث من layout الرئيسي
- [x] مراجعة نوافذ التعليمات — تستخدم نفس الـ layout

### 3. اختبار الترجمات
- [x] إزالة مفتاح `fcm_alert_need_help` غير المستخدم
- [x] دالة `translate()` تضيف المفاتيح الناقصة تلقائياً عند أول استخدام
- [x] التحقق من عدم وجود مفاتيح مكررة — تم إزالة المكرر في ar

### 4. مراجعة المحتوى
- [x] نصوص التعليمات موجودة في الصفحات (cookies، FCM، Firebase، إلخ)
- [x] اتساق المصطلحات — الترجمات في ar و en
- [x] تحديث خطة دليل المستخدم

---

## الملفات المتأثرة

| الملف | الإجراء |
|-------|---------|
| `resources/lang/ar/messages.php` | إزالة fcm_alert_need_help |
| `resources/lang/en/messages.php` | إزالة fcm_alert_need_help |
| `resources/views/admin-views/help/index.blade.php` | مراجعة RTL |
| `resources/views/layouts/admin/*` | مراجعة dir/lang |
| `docs/USER-GUIDE-IMPLEMENTATION-PLAN.md` | تحديث الحالة |
