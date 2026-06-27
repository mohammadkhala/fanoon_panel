# خطة علاجية: زر تفاصيل الكوبون

## تحليل المشكلة

الزر لا يعمل عند النقر — لا يظهر المودال ولا تُحمّل التفاصيل.

---

## الأسباب المحتملة (من تحليل الكود)

| # | السبب | الملف | الوصف |
|---|-------|-------|-------|
| 1 | تعارض معرف المودال | coupon/index | كان `data-target="#exampleModalCenter"` بينما المودال `id="quick-view"` |
| 2 | تعارض مع صفحة الطلبات | order/order-view | نفس `id="quick-view"` — قد يحدث تعارض إن فُتحت الصفحتان |
| 3 | عدم التحقق من null | CouponController | `find($id)` يرجع null إن لم يُوجد الكوبون — يسبب خطأ في الـ view |
| 4 | هيكل المودال | coupon/details | المحتوى بدون `modal-header`/`modal-body` — قد يؤثر على العرض |
| 5 | ترتيب تحميل السكربت | coupon.js | قد يُحمّل قبل jQuery أو Bootstrap |
| 6 | #loading | layouts | `d--none` قد يمنع ظهور المؤشر إن استُخدمت أنماط أخرى |

---

## خطة العلاج

### المرحلة 1: إصلاح الـ Controller

- [x] التحقق من وجود الكوبون قبل عرض الـ view
- [x] إرجاع JSON خطأ إن لم يُوجد الكوبون

### المرحلة 2: إصلاح المودال والهيكل

- [x] استخدام معرف فريد للمودال: `coupon-details-modal`
- [x] هيكلة محتوى التفاصيل داخل `modal-header` و `modal-body`

### المرحلة 3: إصلاح السكربت

- [x] استخدام معرف المودال الجديد
- [x] إضافة `complete` في AJAX لضمان إخفاء #loading
- [x] استخدام `show()` و `hide()` للـ loading (مثل باقي الصفحات)
- [x] إلحاق المودال بـ `body` عند الفتح لتجنب مشاكل z-index

### المرحلة 4: التحقق

- [ ] اختبار النقر على الزر
- [ ] التحقق من طلب AJAX في Network
- [ ] التحقق من عدم وجود أخطاء في Console

---

## الملفات المتأثرة

- `resources/views/admin-views/coupon/index.blade.php`
- `resources/views/admin-views/coupon/details.blade.php`
- `app/Http/Controllers/Admin/CouponController.php`
