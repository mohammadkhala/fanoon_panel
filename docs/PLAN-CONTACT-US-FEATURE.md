# خطة تنفيذ ميزة "سجل تواصل معنا"

**الهدف:** من التطبيق/الموقع يُرسل المستخدم نموذج "اتصل بنا" → تُحفظ البيانات في قاعدة البيانات → تُعرض في لوحة التحكم.

**الحالة الحالية:** لا يوجد حالياً جدول ولا نموذج ولا route خاص بـ "تواصل معنا" في المشروع. الصفحات الأخرى (من نحن، الشروط، الاسترداد...) موجودة كصفحات ثابتة فقط.

---

## المرحلة 1: قاعدة البيانات

### 1.1 إنشاء جدول `contact_us` (Migration)

- **اسم الملف:** `database/migrations/YYYY_MM_DD_HHMMSS_create_contact_us_table.php`
- **الأعمدة المقترحة:**

| العمود        | النوع           | ملاحظات                          |
|---------------|-----------------|-----------------------------------|
| `id`          | bigIncrements   | مفتاح أساسي                      |
| `name`        | string(191)     | اسم المرسل                       |
| `email`       | string(191)     | البريد الإلكتروني                |
| `phone`       | string(50)      | رقم الهاتف (اختياري إن أردت)    |
| `subject`     | string(255)     | موضوع الرسالة (اختياري)          |
| `message`     | text            | نص الرسالة                       |
| `read_at`     | timestamp nullable | وقت القراءة (لتمييز المقروء) |
| `created_at`  | timestamps      | وقت الإرسال                      |
| `updated_at`  | timestamps      |                                  |

- تشغيل: `php artisan migrate` بعد إنشاء الملف.

---

## المرحلة 2: النموذج (Model) والـ Controller

### 2.1 Model

- **المسار:** `app/Models/ContactUs.php`
- **الحقول القابلة للتعبئة (fillable):** `name`, `email`, `phone`, `subject`, `message`
- **Casts:** `read_at` => `datetime`
- دالة مساعدة مثل: `scopeUnread()` للرسائل غير المقروءة (اختياري).

### 2.2 API: استلام الرسالة من التطبيق/الموقع

- **Controller:** إنشاء `App\Http\Controllers\Api\V1\ContactUsController` (أو إضافة دوال في controller موجود).
- **الدالة:** `store(Request $request)`
  - التحقق (validation): `name`, `email`, `message` مطلوبة؛ `phone`, `subject` اختيارية.
  - إنشاء سجل في `contact_us`.
  - إرجاع JSON (نجاح/فشل).
- **Route (API):**  
  `POST /api/v1/contact-us` (بدون مصادقة حتى يتمكن الزائر من الإرسال).

### 2.3 لوحة التحكم (Admin): عرض وإدارة الرسائل

- **Controller:** إنشاء `App\Http\Controllers\Admin\ContactUsController`.
  - `index()`: قائمة الرسائل (مع فلتر: الكل / مقروء / غير مقروء، وترتيب حسب الأحدث).
  - `show($id)`: عرض رسالة واحدة وتحديث `read_at` عند الفتح.
  - `destroy($id)`: حذف رسالة (اختياري).
- **Routes (Admin):** داخل مجموعة `admin` و middleware `admin`:
  - `GET  /admin/contact-us`           → قائمة
  - `GET  /admin/contact-us/{id}`      → عرض
  - `DELETE /admin/contact-us/{id}`    → حذف (اختياري)

---

## المرحلة 3: واجهة لوحة التحكم (Admin Views)

### 3.1 قائمة الرسائل

- **المسار:** `resources/views/admin-views/contact-us/list.blade.php`
- **المحتوى:**
  - جدول: (#، الاسم، البريد، الهاتف، الموضوع، تاريخ الإرسال، مقروء/غير مقروء، إجراءات).
  - استخدام معيار الجداول الموثق في `DB/توثيق_معيار_الجداول.md`.
  - فلتر أو تبويبات: الكل / غير مقروء / مقروء.
  - ترقيم الصفحات (pagination).
  - زر "عرض" يفتح صفحة التفاصيل.

### 3.2 صفحة عرض رسالة واحدة

- **المسار:** `resources/views/admin-views/contact-us/show.blade.php`
- **المحتوى:** عرض الاسم، البريد، الهاتف، الموضوع، الرسالة، التاريخ. تحديث `read_at` عند أول فتح.

### 3.3 القائمة الجانبية (Sidebar)

- إضافة عنصر "تواصل معنا" أو "سجل اتصل بنا" في القائمة الجانبية للإدارة.
- **الملف:** `resources/views/layouts/admin/partials/_sidebar.blade.php`
- وضع الرابط تحت "الصفحات والتواصل" أو تحت "إعدادات الأعمال" مع route مثل: `admin.contact-us.index`.
- إظهار شارة بعدد الرسائل غير المقروءة (اختياري).

---

## المرحلة 4: التطبيق/الموقع (مصدر البيانات)

### 4.1 تطبيق موبايل (API فقط)

- في التطبيق: نموذج (اسم، بريد، هاتف، موضوع، رسالة) يرسل طلب **POST** إلى:
  - `POST /api/v1/contact-us`
- الـ API يستقبل، يتحقق، ويحفظ في `contact_us` ثم يرد بالنتيجة.

### 4.2 موقع ويب (إن وُجد)

- إن كان هناك موقع ويب بلارافيل:
  - إنشاء route في `web.php`: مثلاً `GET /contact-us` (صفحة النموذج)، `POST /contact-us` (إرسال).
  - صفحة blade تحتوي على form يرسل إلى نفس الـ backend (نفس منطق الحفظ في `contact_us`)، أو يستدعي نفس الـ API عبر AJAX إلى `POST /api/v1/contact-us`.

---

## المرحلة 5: الترجمات والأمان

### 5.1 الترجمات

- إضافة مفاتيح في `resources/lang/ar/messages.php` و `resources/lang/en/messages.php`:
  - عناوين الصفحات، أعمدة الجدول، أزرار، رسائل نجاح/خطأ (مثل: "تم استلام رسالتك"، "الاسم مطلوب"، "سجل تواصل معنا").

### 5.2 الأمان

- **Rate limiting:** وضع throttle على `POST /api/v1/contact-us` (مثلاً 10 طلبات/دقيقة لكل IP) لتقليل السبام.
- **Validation:** منع حقول طويلة جداً وتنظيف المدخلات.
- **CSRF:** طلبات الـ web تستخدم CSRF؛ الـ API بدون CSRF لكن مع throttle.

---

## ترتيب التنفيذ المقترح

1. Migration → تشغيل `migrate`
2. Model `ContactUs`
3. Admin: Controller + Routes + List view + Show view + Sidebar
4. API: Controller + Route + Validation + حفظ في DB
5. ترجمات (عربي/إنجليزي)
6. (اختياري) تحسينات: شارة غير مقروء، حذف، فلتر، throttle

---

## الملفات التي ستُنشأ أو تُعدّل (ملخص)

| الإجراء   | الملف/المسار |
|-----------|----------------|
| إنشاء     | `database/migrations/..._create_contact_us_table.php` |
| إنشاء     | `app/Models/ContactUs.php` |
| إنشاء     | `app/Http/Controllers/Api/V1/ContactUsController.php` |
| إنشاء     | `app/Http/Controllers/Admin/ContactUsController.php` |
| إنشاء     | `resources/views/admin-views/contact-us/list.blade.php` |
| إنشاء     | `resources/views/admin-views/contact-us/show.blade.php` |
| تعديل     | `routes/api/v1/api.php` (إضافة POST contact-us) |
| تعديل     | `routes/admin.php` (إضافة مجموعة contact-us) |
| تعديل     | `resources/views/layouts/admin/partials/_sidebar.blade.php` (رابط القائمة) |
| تعديل     | `resources/lang/ar/messages.php` و `resources/lang/en/messages.php` |

---

## تنفيذ مكتمل + إشعار صوت (مثل طلب جديد)

تم تنفيذ الميزة مع إشعار عند وصول رسالة جديدة:

- **جدول:** `contact_us` (Migration: `2026_03_05_000001_create_contact_us_table.php`)
- **Model:** `App\Models\ContactUs` مع `scopeUnread()`
- **Admin:** `ContactUsController` (index, show, destroy) + Routes + قائمة + عرض + رابط في السايدبار (تحت "الصفحات والتواصل")
- **API:** `POST /api/v1/contact-us` مع throttle 10/دقيقة، تحقق: name, email, message مطلوبة؛ phone, subject اختيارية
- **إشعار:** في `get-restaurant-data` تم إضافة `new_contact_us`. في لوحة التحكم كل 10 ثوانٍ إذا `new_contact_us > 0` يُشغَّل نفس الصوت (notification.mp3) ويظهر modal "لديك رسالة تواصل جديدة. تحقق من فضلك." مع زرّي "تجاهل" و"تحقق". Route "تجاهل": `admin.ignore-check-contact` يضع `read_at` لجميع غير المقروءة.
- **الترجمات:** مضافة في ar و en.

---

## خطوات الاختبار (كل خطوة قبل الانتقال للتالية)

### 1. اختبار Migration وقاعدة البيانات
- [ ] تشغيل: `php artisan migrate`
- [ ] التأكد من وجود جدول `contact_us` في قاعدة البيانات (الأعمدة: id, name, email, phone, subject, message, read_at, created_at, updated_at)

### 2. اختبار API إرسال رسالة
- [ ] إرسال طلب POST إلى `/api/v1/contact-us` مع Body (JSON):  
  `{"name":"Test User","email":"test@test.com","message":"Hello"}`
- [ ] التأكد من الاستجابة 201 ورسالة نجاح
- [ ] التحقق من ظهور سجل جديد في جدول `contact_us`
- [ ] إرسال طلب ناقص (بدون name أو email أو message) والتأكد من 422 مع أخطاء التحقق
- [ ] إرسال أكثر من 10 طلبات من نفس IP خلال دقيقة والتأكد من throttle (429)

### 3. اختبار لوحة التحكم – القائمة
- [ ] تسجيل الدخول كـ Admin ثم فتح `/admin/contact-us`
- [ ] التأكد من ظهور الصفحة بعنوان "تواصل معنا" وشارة العدد (غير مقروء)
- [ ] التأكد من ظهور الجدول بالهوية البصرية (ترويسة حمراء، جدول قياسي)
- [ ] تغيير الفلتر (الكل / غير مقروء / مقروء) والتأكد من تصفية النتائج
- [ ] النقر على "عرض" لرسالة والتأكد من الانتقال لصفحة التفاصيل

### 4. اختبار لوحة التحكم – عرض رسالة وحذف
- [ ] فتح رسالة غير مقروءة والتأكد من تعبئة `read_at` بعد الفتح
- [ ] التأكد من عرض الاسم، البريد، الهاتف، الموضوع، الرسالة، التواريخ
- [ ] النقر على "حذف" والتأكد من ظهور تأكيد ثم حذف الرسالة والعودة للقائمة

### 5. اختبار الإشعار (صوت + Popup)
- [ ] إنشاء رسالة جديدة عبر API (أو إدخال سجل يدوي في DB مع `read_at = NULL`)
- [ ] إبقاء لوحة التحكم مفتوحة (أي صفحة admin) وانتظار مدة لا تزيد عن 10 ثوانٍ
- [ ] التأكد من تشغيل الصوت وظهور نافذة "لديك رسالة تواصل جديدة. تحقق من فضلك."
- [ ] النقر "تجاهل الآن" والتأكد من التوجيه وتوقف الإشعار في الدورة التالية
- [ ] إنشاء رسالة غير مقروءة أخرى والنقر "تحقق" والتأكد من التوجيه إلى `/admin/contact-us`

### 6. اختبار الترجمات والهوية البصرية
- [ ] تبديل لغة لوحة التحكم إلى العربية والتأكد من ظهور النصوص العربية في صفحة تواصل معنا والقائمة الجانبية
- [ ] التأكد من أن ترويسات البطاقات والجدول تتبع الهوية البصرية (خط أحمر، شارة العدد، نفس أسلوب صفحة وسائل التواصل)

---

*تم تنفيذ الميزة وإضافة خطوات الاختبار أعلاه.*
