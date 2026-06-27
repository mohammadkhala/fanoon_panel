# صيانة كاملة قبل الرفع — قائمة التعديلات

**التاريخ:** مراجعة شاملة قبل رفع نسخة جديدة للاستضافة.

---

## 1. السمات (Attributes)

| الملف | التعديل |
|-------|---------|
| `app/Http/Controllers/Admin/AttributeController.php` | حفظ اسم السمة من أول لغة غير فارغة (عربي/إنجليزي) في store و update |
| `app/Models/Attribute.php` | عرض الترجمة عند كون العمود name فارغاً (للقائمة واختيار السمات في المنتج) |
| `resources/views/admin-views/attribute/index.blade.php` | عرض الاسم مع احتياطي من الترجمة في الجدول |

---

## 2. المنتج (إضافة منتج جديد)

| الملف | التعديل |
|-------|---------|
| `resources/views/admin-views/product/index.blade.php` | السعر الأساسي والمخزون يبدآن فارغين (value="")؛ min=0؛ تصحيح oninput لعدم فرض 1 عند الحقل الفارغ |

---

## 3. jQuery و Bootstrap (أخطاء $ is not defined و tooltip)

| الملف | التعديل |
|-------|---------|
| `resources/views/layouts/admin/app.blade.php` | jQuery في الـ head؛ تحميل bootstrap.js قبل theme.min.js |
| `resources/views/layouts/branch/app.blade.php` | نفس التعديل |
| `resources/views/errors/404.blade.php` | jQuery + bootstrap.js قبل theme.min.js |
| `resources/views/errors/500.blade.php` | نفس التعديل |
| `resources/views/admin-views/order/invoice.blade.php` | jQuery + bootstrap.js قبل theme.min.js |
| `resources/views/branch-views/order/invoice.blade.php` | نفس التعديل |

---

## 4. نقطة البيع (POS) والإعدادات

| الملف | التعديل |
|-------|---------|
| `config/app.php` | إضافة `show_pos_menu` (من .env، افتراضي false) |
| `resources/views/layouts/admin/partials/_sidebar.blade.php` | إخفاء رابط "نقطة البيع" إلا إذا show_pos_menu = true |
| `routes/admin.php` | مسار get-cart-payment-section كـ GET (ليس POST) |
| `routes/branch.php` | نفس التعديل |
| `app/Http/Controllers/Admin/POSController.php` | التحقق من وجود المنتج في placeOrder؛ تهيئة couponDiscount و extra_discount؛ order_note من الطلب؛ getCustomerAddresses مع is_guest؛ orderList/export يشملان pos و pos_delivery؛ orderDetails يوجّه إلى admin.orders.details |
| `resources/views/admin-views/pos/index.blade.php` | استخدام url() لعناوين العملاء؛ رسائل عند عدم وجود عناوين أو خطأ تحميل |
| `resources/views/admin-views/pos/_cart.blade.php` | حقل ملاحظات الطلب (order_note) |
| `resources/views/admin-views/order/order-view.blade.php` | عرض الملاحظة لجميع أنواع الطلبات عند وجودها |
| `resources/views/branch-views/order/order-view.blade.php` | نفس التعديل (عرض الملاحظة لجميع الأنواع) |

---

## 5. الملفات المطلوب رفعها (للصيانة الكاملة)

يُنصح برفع الملفات التالية فقط (أو فك ملف maintenance-full.zip في جذر المشروع):

```
app/Http/Controllers/Admin/AttributeController.php
app/Http/Controllers/Admin/POSController.php
app/Models/Attribute.php
config/app.php
routes/admin.php
routes/branch.php
resources/views/admin-views/attribute/index.blade.php
resources/views/admin-views/product/index.blade.php
resources/views/admin-views/pos/index.blade.php
resources/views/admin-views/pos/_cart.blade.php
resources/views/layouts/admin/app.blade.php
resources/views/layouts/branch/app.blade.php
resources/views/layouts/admin/partials/_sidebar.blade.php
resources/views/errors/404.blade.php
resources/views/errors/500.blade.php
resources/views/admin-views/order/invoice.blade.php
resources/views/branch-views/order/invoice.blade.php
resources/views/admin-views/order/order-view.blade.php
resources/views/branch-views/order/order-view.blade.php
```

---

## 6. أوامر بعد الرفع على السيرفر

```bash
cd مسار_المشروع
php artisan view:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan optimize
```

---

## 7. التأكد على السيرفر

- وجود الملف: `public/assets/admin/js/jquery.js`
- وجود الملف: `public/assets/admin/js/bootstrap.js`
- إظهار نقطة البيع (اختياري): إضافة `APP_SHOW_POS_MENU=true` في `.env`

---

*تم إنشاء هذا الملف كجزء من الصيانة الكاملة قبل الرفع.*
