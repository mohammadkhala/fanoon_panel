# الخطة البرمجية الكاملة: تنبيه المخزون + API المنتجات العائدة + إعداد الإظهار للعملاء

**قاعدة مهمة:** لا ننتقل للمرحلة التالية إلا بعد إكمال **جميع** خطوات المرحلة الحالية وتشغيل **اختبارات المرحلة** والتأكد من نجاحها. لا نبدأ بالمرحلة التالية حتى تقول أن الاختبارات نجحت أو توافق على الانتقال.

---

## نظرة عامة على المراحل

| المرحلة | المحتوى | الاختبار |
|---------|---------|----------|
| **المرحلة 1** | تنبيه المخزون في الهيدر (أيقونة + badge + قائمة منسدلة) | اختبارات واجهة الهيدر والقائمة |
| **المرحلة 2** | API المنتجات العائدة للمخزون + عمود `back_in_stock_at` | اختبارات Migration و API |
| **المرحلة 3** | إعداد إظهار/إخفاء في إعدادات العمل + ربطه بالـ API | اختبارات الإعداد والـ API |

---

## المرحلة 1: تنبيه المخزون في الهيدر

### الخطوات البرمجية

**1.1 إنشاء View Composer للهيدر**

- الملف: `app/Providers/AppServiceProvider.php` (أو إنشاء `ViewServiceProvider` وتسجيله).
- تسجيل Composer للـ view: `layouts.admin.partials._header` (أو المسار الفعلي للهيدر).
- في الـ Composer:
  - جلب الإعداد: `$defaultStockAlert = (int)(Helpers::get_business_settings('default_minimum_stock_alert') ?? 5);`
  - `$lowStockCount = Product::lowStock($defaultStockAlert)->count();`
  - `$lowStockProducts = Product::lowStock($defaultStockAlert)->take(15)->get();`
  - تمرير المتغيرات: `$view->with(compact('lowStockCount', 'lowStockProducts'));`

**1.2 تعديل الهيدر (القائمة المنسدلة)**

- الملف: `resources/views/layouts/admin/partials/_header.blade.php`.
- إضافة عنصر **بعد** عنصر الطلبات (قبل قائمة الحساب):
  - غلاف: `<div class="hs-unfold">`.
  - زر يفتح dropdown (لا ينتقل لصفحة): استخدام `data-hs-unfold-options='{"target":"#lowStockNavbarDropdown","type":"css-animation"}'` و `href="javascript:;"`.
  - على الزر: أيقونة (مثلاً `tio-package-outlined`) + `<span class="btn-status btn-status-warning">{{ $lowStockCount ?? 0 }}</span>`.
  - عنصر القائمة: `<div id="lowStockNavbarDropdown" class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right ...">`:
    - عنوان: "منتجات مخزون منخفض" (أو ترجمة).
    - حلقة: `@foreach($lowStockProducts ?? [] as $p)` مع رابط إلى `route('admin.product.view', $p->id)` وعرض الاسم + المخزون.
    - حالة فارغة: "لا توجد منتجات مخزون منخفض".
    - في الأسفل: رابط "عرض الكل" إلى `route('admin.product.list', ['stock_filter' => 'low_stock'])`.

**1.3 الترجمات**

- إضافة في `ar/messages.php` و `en/messages.php`: مفتاح لعنوان القائمة، "عرض الكل"، "لا توجد منتجات مخزون منخفض" (إن لزم).

---

### اختبارات المرحلة 1 (يجب تنفيذها قبل الانتقال للمرحلة 2)

- [ ] **1.1** وجود منتج واحد على الأقل بمخزون ≤ حد التنبيه (مثلاً total_stock=3 و default_minimum_stock_alert=5، أو minimum_stock_alert=2).
- [ ] **1.2** فتح أي صفحة في لوحة التحكم (Admin): الظهور في الهيدر لأيقونة المخزون وشارة العدد (العدد > 0).
- [ ] **1.3** النقر على الأيقونة: تفتح قائمة منسدلة (dropdown) وليس انتقالاً لصفحة أخرى.
- [ ] **1.4** القائمة تحتوي أسماء المنتجات ذات المخزون المنخفض (وربما المخزون الحالي)، وكل عنصر يوجّه لصفحة عرض المنتج عند النقر.
- [ ] **1.5** رابط "عرض الكل" يوجّه إلى قائمة المنتجات مع فلتر `stock_filter=low_stock` وتظهر نفس المنتجات.
- [ ] **1.6** عندما لا يوجد أي منتج مخزون منخفض: الشارة تظهر 0 والقائمة تعرض رسالة "لا توجد منتجات مخزون منخفض" (أو ما يعادلها).

**بعد نجاح كل الاختبارات أعلاه فقط ننتقل للمرحلة 2.**

---

## المرحلة 2: API المنتجات العائدة للمخزون

### الخطوات البرمجية

**2.1 Migration: إضافة عمود `back_in_stock_at`**

- إنشاء migration: إضافة إلى جدول `products` عمود `back_in_stock_at` (timestamp, nullable).
- تشغيل: `php artisan migrate`.

**2.2 تحديث Model Product**

- في `app/Models/Product.php`: إضافة `back_in_stock_at` إلى `$casts` كـ `datetime` (إن لزم)، وعدم وضعه في `$fillable` إذا التحديث يتم برمجياً فقط.

**2.3 تحديث كل أماكن تعديل `total_stock`**

- قبل أي عملية تحديث لـ `total_stock` نحفظ القيمة القديمة؛ بعد التحديث إذا (القديم <= 0 والجديد > 0) نحدّث `back_in_stock_at = now()`.
- الأماكن المتوقعة:
  - `App\Http\Controllers\Api\V1\OrderController` (عند إنشاء الطلب وخصم المخزون — هنا نخفض فقط، لكن عند إلغاء طلب أو إرجاع قد نزيد المخزون).
  - `App\Http\Controllers\Admin\OrderController` (تغيير حالة الطلب، تعديل الطلب، إلخ).
  - `App\Http\Controllers\Branch\OrderController` (نفس المنطق).
  - `App\Http\Controllers\Admin\POSController` و `Branch\POSController` (عند إضافة طلب وخفض المخزون؛ إن وُجد منطق يزيد المخزون نضيف التحديث).
  - `App\Http\Controllers\Admin\ProductController`: عند حفظ/تحديث منتج (حقل المخزون يدوياً): إذا `total_stock` أصبح > 0 وكان سابقاً <= 0 نحدّث `back_in_stock_at`.

**2.4 إنشاء API المنتجات العائدة**

- Route: `GET /api/v1/products/back-in-stock` (ضمن مجموعة api/v1 الموجودة).
- Controller (مثلاً في `Api\V1`): دالة ترجع منتجات حيث `total_stock > 0` و `back_in_stock_at` غير null، مرتبة حسب `back_in_stock_at` تنازلياً. الاستجابة بصيغة JSON (قائمة منتجات مع الحقول المناسبة: id, name, image, price, total_stock, إلخ).
- في هذه المرحلة لا نتحقق بعد من إعداد "إظهار للعملاء" (يُضاف في المرحلة 3).

---

### اختبارات المرحلة 2 (يجب تنفيذها قبل الانتقال للمرحلة 3)

- [ ] **2.1** تشغيل Migration بنجاح والتحقق من وجود عمود `back_in_stock_at` في جدول `products`.
- [ ] **2.2** تحديث منتج يدوياً من لوحة التحكم: تخفيض مخزونه إلى 0 ثم رفعه إلى قيمة > 0؛ التحقق من أن `back_in_stock_at` يُملأ في قاعدة البيانات.
- [ ] **2.3** طلب `GET /api/v1/products/back-in-stock`: يرجع قائمة تحتوي المنتج الذي عدّلت مخزونه في 2.2 (وعلى الأقل الحقول المطلوبة للتطبيق).
- [ ] **2.4** منتج لم يُحدَّث مخزونه من 0 إلى موجب: لا يظهر في استجابة الـ API.
- [ ] **2.5** منتج `total_stock > 0` لكن `back_in_stock_at` null: لا يظهر في استجابة الـ API (الـ API تعتمد على back_in_stock_at).

**بعد نجاح كل الاختبارات أعلاه فقط ننتقل للمرحلة 3.**

---

## المرحلة 3: إعداد إظهار/إخفاء للعملاء في إعدادات العمل

### الخطوات البرمجية

**3.1 إضافة الخيار في واجهة إعدادات العمل**

- الملف: `resources/views/admin-views/business-settings/ecom-setup.blade.php`.
- إضافة قسم جديد (بطاقة أو صف): عنوان مثل "إظهار المنتجات العائدة للمخزون للعملاء".
- عنصر تحكم: مفتاح تبديل (switcher) مربوط بقيمة من `Helpers::get_business_settings('show_back_in_stock_to_customers')` (1 = مفعّل، 0 أو غير موجود = معطّل).

**3.2 حفظ الإعداد**

- في الـ Controller الذي يتولى حفظ صفحة ecom-setup (غالباً نفس الصفحة عبر POST أو AJAX): عند الحفظ إدراج أو تحديث المفتاح `show_back_in_stock_to_customers` في جدول `business_settings` حسب حالة المفتاح (0 أو 1).

**3.3 ربط الـ API بالإعداد**

- في Controller الـ API للمنتجات العائدة: في بداية الدالة التحقق من `Helpers::get_business_settings('show_back_in_stock_to_customers')`. إذا المعيار غير مفعّل (0 أو null): إرجاع استجابة بقائمة فارغة (مثلاً `{ "products": [], "message": "..." }`) أو رسالة مناسبة دون كشف بيانات المنتجات.

**3.4 الترجمات**

- إضافة مفاتيح للعربية والإنجليزية: عنوان الخيار ووصف قصير (إن وُجد).

---

### اختبارات المرحلة 3 (ختامية)

- [ ] **3.1** فتح صفحة إعدادات العمل (ecom-setup): يظهر القسم الجديد والمفتاح (مفعّل/معطّل).
- [ ] **3.2** تفعيل المفتاح وحفظ الصفحة: في قاعدة البيانات (جدول business_settings) يوجد سجل للمفتاح `show_back_in_stock_to_customers` بالقيمة 1.
- [ ] **3.3** تعطيل المفتاح وحفظ الصفحة: القيمة تصبح 0 (أو يُحذف السجل حسب تصميمك).
- [ ] **3.4** مع تفعيل الإعداد: طلب `GET /api/v1/products/back-in-stock` يرجع قائمة المنتجات العائدة كما في المرحلة 2.
- [ ] **3.5** مع تعطيل الإعداد: نفس الطلب يرجع قائمة فارغة (أو رسالة فقط) دون بيانات منتجات.

**بعد نجاح كل الاختبارات أعلاه تعتبر الميزة مكتملة من ناحية الخطة.**

---

## ملخص الملفات المتأثرة (للمرجع)

| المرحلة | الملفات |
|---------|---------|
| 1 | `AppServiceProvider.php` (أو ViewServiceProvider)، `_header.blade.php`، `ar/messages.php`، `en/messages.php` |
| 2 | Migration جديد، `Product.php`، OrderController (API/Admin/Branch)، POSController (Admin/Branch)، ProductController (Admin)، Controller + Route للـ API back-in-stock |
| 3 | `ecom-setup.blade.php`، Controller حفظ إعدادات ecom، Controller API back-in-stock، الترجمات |

---

## ترتيب التنفيذ والاختبار

1. تنفيذ **جميع** خطوات المرحلة 1 ثم تشغيل **اختبارات المرحلة 1** فقط.
2. بعد التأكد من نجاحها، تنفيذ **جميع** خطوات المرحلة 2 ثم تشغيل **اختبارات المرحلة 2** فقط.
3. بعد التأكد من نجاحها، تنفيذ **جميع** خطوات المرحلة 3 ثم تشغيل **اختبارات المرحلة 3**.

**لا يُنفذ أي كود حتى تقول "ابدأ". ولا ننتقل لمرحلة تالية إلا بعد إكمال اختبارات المرحلة الحالية.**
