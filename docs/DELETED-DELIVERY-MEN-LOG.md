# سجل الحذف — إزالة جداول رجال التوصيل

> **تاريخ التنفيذ:** 2025-03-10  
> **الغرض:** توثيق كل ما تم حذفه قبل التنفيذ

---

## 1. الجداول المحذوفة (5 جداول)

| # | الجدول | الغرض |
|---|--------|-------|
| 1 | `delivery_men` | بيانات مندوبي التوصيل |
| 2 | `d_m_reviews` | تقييمات مندوبي التوصيل |
| 3 | `delivery_histories` | موقع مندوب التوصيل (خريطة) |
| 4 | `dc_conversations` | محادثات العميل مع مندوب التوصيل |
| 5 | `order_delivery_histories` | سجل بداية/نهاية التوصيل |

---

## 2. الأعمدة المحذوفة من جداول أخرى

| الجدول | العمود المحذوف |
|--------|----------------|
| `orders` | `delivery_man_id` |
| `messages` | `deliveryman_id` |

**ملاحظة:** تم حذف رسائل `dc_conversations` من جدول `messages` قبل حذف الجدول.

---

## 3. الموديلات المحذوفة (5 ملفات)

| الملف |
|-------|
| `app/Models/DeliveryMan.php` |
| `app/Models/DMReview.php` |
| `app/Models/DeliveryHistory.php` |
| `app/Models/DcConversation.php` |
| `app/Models/OrderDeliveryHistory.php` |

---

## 4. Controllers المحذوفة (4 ملفات)

| الملف |
|-------|
| `app/Http/Controllers/Admin/DeliveryManController.php` |
| `app/Http/Controllers/Api/V1/DeliverymanController.php` |
| `app/Http/Controllers/Api/V1/DeliveryManReviewController.php` |
| `app/Http/Controllers/Api/V1/Auth/DeliveryManLoginController.php` |

---

## 5. Views المحذوفة (8 ملفات)

| الملف |
|-------|
| `resources/views/admin-views/delivery-man/index.blade.php` |
| `resources/views/admin-views/delivery-man/edit.blade.php` |
| `resources/views/admin-views/delivery-man/list.blade.php` |
| `resources/views/admin-views/delivery-man/pending-list.blade.php` |
| `resources/views/admin-views/delivery-man/denied-list.blade.php` |
| `resources/views/admin-views/delivery-man/view.blade.php` |
| `resources/views/admin-views/delivery-man/reviews-list.blade.php` |
| `resources/views/admin-views/report/deliveryman-report-index.blade.php` |

---

## 6. الملفات المعدّلة (تعديلات وليس حذف)

| الملف | التعديل |
|-------|---------|
| `app/Models/Order.php` | إزالة delivery_man_id وعلاقة delivery_man() |
| `app/Models/Message.php` | إزالة deliveryman_id من casts |
| `app/Http/Resources/MessageResource.php` | إزالة deliveryman_id من toArray |
| `app/Http/Controllers/Admin/OrderController.php` | إزالة مراجع المندوب |
| `app/Http/Controllers/Branch/OrderController.php` | إزالة مراجع المندوب |
| `app/Http/Controllers/Admin/ReportController.php` | إزالة driverReport ومراجع DeliveryMan |
| `app/Http/Controllers/Api/V1/OrderController.php` | إزالة delivery_man و deliveryman_review_count |
| `app/Http/Controllers/Api/V1/ConversationController.php` | إزالة دوال محادثات الطلب مع المندوب |
| `app/Http/Controllers/Api/V1/ConfigController.php` | إزالة delivery_management و delivery_man_image_url |
| `app/CentralLogics/Helpers.php` | إزالة dm_rating_count() |
| `app/CentralLogics/OrderLogic.php` | إزالة delivery_man.rating |
| `resources/views/admin-views/order/order-view.blade.php` | إزالة أقسام المندوب |
| `resources/views/branch-views/order/order-view.blade.php` | إزالة أقسام المندوب |
| `resources/views/layouts/admin/partials/_sidebar.blade.php` | إزالة رابط مندوبو التوصيل |
| `routes/admin.php` | إزالة مسارات delivery-man و add-delivery-man |
| `routes/branch.php` | إزالة add-delivery-man |
| `routes/api/v1/api.php` | إزالة مسارات delivery-man |
| `config/auth.php` | إزالة guard و provider لـ delivery_men |

---

## 7. Migration المُنشأ

| الملف |
|-------|
| `database/migrations/2026_03_10_000001_remove_delivery_men_tables.php` |

---

---

## 8. التنفيذ الفعلي (2025-03-10)

تم تنفيذ جميع التعديلات بنجاح:
- Migration تم تشغيله بنجاح
- الموديلات والـ Controllers والـ Views تم حذفها
- المسارات والكود تم تعديلها
