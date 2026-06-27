# خطة إزالة جداول رجال التوصيل — تحليل كامل قبل التنفيذ

> **لا تنفيذ حتى يُقال "تم" أو "وافق"**

---

## 1. الجداول المراد حذفها (5 جداول)

| # | الجدول | الغرض |
|---|--------|-------|
| 1 | `delivery_men` | بيانات مندوبي التوصيل |
| 2 | `d_m_reviews` | تقييمات مندوبي التوصيل |
| 3 | `delivery_histories` | موقع مندوب التوصيل (خريطة) |
| 4 | `dc_conversations` | محادثات العميل مع مندوب التوصيل (حسب الطلب) |
| 5 | `order_delivery_histories` | سجل بداية/نهاية التوصيل (غير مستخدم أصلاً) |

---

## 2. الحقول المرتبطة في جداول أخرى

### 2.1 جدول `orders`

| الحقل | النوع | الإجراء المقترح |
|-------|-------|-----------------|
| `delivery_man_id` | bigint, nullable | **حذف العمود** — أو تركه كـ nullable (سيبقى null دائماً) |

### 2.2 جدول `messages`

| الحقل | النوع | الإجراء المقترح |
|-------|-------|-----------------|
| `deliveryman_id` | bigint, nullable | **حذف العمود** — بعد حذف رسائل dc_conversations |

**ملاحظة:** رسائل `dc_conversations` تُخزّن في جدول `messages` مع `conversation_id` يشير إلى `dc_conversations.id`. يجب حذف هذه الرسائل قبل حذف جدول `dc_conversations`.

---

## 3. ترتيب الحذف (لتجنب أخطاء الاعتماديات)

```
1. حذف رسائل dc_conversations من messages
   DELETE FROM messages WHERE conversation_id IN (SELECT id FROM dc_conversations)

2. حذف جدول dc_conversations

3. حذف الجداول (بالترتيب):
   - d_m_reviews        (مرتبط بـ delivery_men)
   - delivery_histories (مرتبط بـ delivery_men)
   - order_delivery_histories
   - delivery_men

4. إزالة العمود delivery_man_id من orders

5. إزالة العمود deliveryman_id من messages
```

---

## 4. الملفات المتأثرة — حذف أو تعديل

### 4.1 موديلات للحذف

| الملف | الإجراء |
|-------|---------|
| `app/Models/DeliveryMan.php` | حذف |
| `app/Models/DMReview.php` | حذف |
| `app/Models/DeliveryHistory.php` | حذف |
| `app/Models/DcConversation.php` | حذف |
| `app/Models/OrderDeliveryHistory.php` | حذف |

### 4.2 Controllers للحذف أو التعطيل

| الملف | الإجراء |
|-------|---------|
| `app/Http/Controllers/Admin/DeliveryManController.php` | حذف |
| `app/Http/Controllers/Api/V1/DeliverymanController.php` | حذف |
| `app/Http/Controllers/Api/V1/DeliveryManReviewController.php` | حذف |
| `app/Http/Controllers/Api/V1/Auth/DeliveryManLoginController.php` | حذف |

### 4.3 Controllers للتعديل (إزالة مراجع المندوب)

| الملف | التعديل المطلوب |
|-------|-----------------|
| `app/Http/Controllers/Admin/OrderController.php` | إزالة `delivery_man` من with، إزالة `addDeliveryman()`، إزالة `$deliverymen`، إزالة `DeliveryHistory`، إزالة خريطة المندوب |
| `app/Http/Controllers/Branch/OrderController.php` | نفس التعديلات |
| `app/Http/Controllers/Admin/ReportController.php` | إزالة أو تعطيل `deliverymanReport()` وتقرير المندوب |
| `app/Http/Controllers/Api/V1/OrderController.php` | إزالة `delivery_man.rating` و `deliveryman_review_count` |
| `app/Http/Controllers/Api/V1/ConversationController.php` | إزالة/تعطيل دوال محادثات الطلب مع المندوب (`getOrderMessageForDm`, `storeMessageByOrder`) |
| `app/Http/Controllers/Api/V1/ConfigController.php` | إزالة `delivery_management` و `delivery_man_image_url` من الاستجابة |

### 4.4 موديل Order

| الملف | التعديل |
|-------|---------|
| `app/Models/Order.php` | إزالة `delivery_man_id` من fillable و casts، إزالة علاقة `delivery_man()` |

### 4.5 موديل Message

| الملف | التعديل |
|-------|---------|
| `app/Models/Message.php` | إزالة `deliveryman_id` من casts |

### 4.6 MessageResource

| الملف | التعديل |
|-------|---------|
| `app/Http/Resources/MessageResource.php` | إزالة `deliveryman_id` من toArray |

### 4.7 Helpers

| الملف | التعديل |
|-------|---------|
| `app/CentralLogics/Helpers.php` | إزالة أو تعطيل `dm_rating_count()` |

### 4.8 OrderLogic

| الملف | التعديل |
|-------|---------|
| `app/CentralLogics/OrderLogic.php` | إزالة `delivery_man.rating` من with |

---

## 5. المسارات (Routes)

### 5.1 Admin routes — إزالة أو تعطيل

| المسار | الملف |
|--------|-------|
| `admin/delivery-man/*` (كل مسارات المندوب) | `routes/admin.php` |
| `admin/orders/add-delivery-man/{order_id}/{delivery_man_id}` | `routes/admin.php` |
| `admin/report/deliveryman-report` (إن وُجد) | `routes/admin.php` |

### 5.2 Branch routes — إزالة أو تعطيل

| المسار | الملف |
|--------|-------|
| `branch/orders/add-delivery-man/*` | `routes/branch.php` |

### 5.3 API routes — إزالة أو تعطيل

| المسار | الملف |
|--------|-------|
| `api/v1/delivery-man/register` | `routes/api/v1/api.php` |
| `api/v1/delivery-man/login` | `routes/api/v1/api.php` |
| `api/v1/delivery-man/*` (كل مسارات المندوب) | `routes/api/v1/api.php` |
| `api/v1/delivery-man/message/*` | `routes/api/v1/api.php` |
| `api/v1/delivery-man/reviews/*` | `routes/api/v1/api.php` |
| `api/v1/customer/message/get-order-message` (محادثات الطلب مع المندوب) | `routes/api/v1/api.php` |
| `api/v1/customer/message/send/{sender_type}` | `routes/api/v1/api.php` |

---

## 6. الواجهات (Views)

### 6.1 حذف كامل

| الملف | الوصف |
|-------|-------|
| `resources/views/admin-views/delivery-man/index.blade.php` | إضافة مندوب |
| `resources/views/admin-views/delivery-man/edit.blade.php` | تعديل مندوب |
| `resources/views/admin-views/delivery-man/list.blade.php` | قائمة المندوبين |
| `resources/views/admin-views/delivery-man/pending-list.blade.php` | قائمة الانتظار |
| `resources/views/admin-views/delivery-man/denied-list.blade.php` | قائمة المرفوضين |
| `resources/views/admin-views/delivery-man/view.blade.php` | عرض مندوب |
| `resources/views/admin-views/delivery-man/reviews-list.blade.php` | تقييمات المندوبين |
| `resources/views/admin-views/report/deliveryman-report-index.blade.php` | تقرير المندوبين |

### 6.2 تعديل (إزالة أقسام المندوب)

| الملف | التعديل |
|-------|---------|
| `resources/views/admin-views/order/order-view.blade.php` | إزالة قائمة تعيين المندوب، إزالة خريطة المندوب، إزالة `addDeliveryMan` JS |
| `resources/views/branch-views/order/order-view.blade.php` | نفس التعديلات |

---

## 7. الإعدادات (Config)

| الملف | التعديل |
|-------|---------|
| `config/auth.php` | إزالة guard `delivery_men` و provider `delivery_men` |

---

## 8. إعدادات الأعمال (Business Settings)

قد توجد مفاتيح في `business_settings`:
- `delivery_management`
- `delivery_boy_assign_message`

**التوصية:** تركها أو حذفها من واجهة FCM — لا تؤثر على الحذف.

---

## 9. Migration المقترح

```php
// 2026_03_XX_000000_remove_delivery_men_tables.php

public function up(): void
{
    // 1. حذف رسائل dc_conversations (خطوتان لتجنب أخطاء MySQL)
    $ids = DB::table('dc_conversations')->pluck('id');
    DB::table('messages')->whereIn('conversation_id', $ids)->delete();
    
    // 2. حذف الجداول
    Schema::dropIfExists('dc_conversations');
    Schema::dropIfExists('d_m_reviews');
    Schema::dropIfExists('delivery_histories');
    Schema::dropIfExists('order_delivery_histories');
    Schema::dropIfExists('delivery_men');
    
    // 3. إزالة الأعمدة
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn('delivery_man_id');
    });
    Schema::table('messages', function (Blueprint $table) {
        $table->dropColumn('deliveryman_id');
    });
}
```

---

## 10. ملخص التأثير

| البند | العدد |
|-------|-------|
| جداول للحذف | 5 |
| أعمدة للحذف | 2 (orders.delivery_man_id, messages.deliveryman_id) |
| موديلات للحذف | 5 |
| Controllers للحذف | 4 |
| Controllers للتعديل | 6 |
| Views للحذف | 8 |
| Views للتعديل | 2 |
| مسارات للإزالة | ~20 |
| ملفات config | 1 |

---

## 11. التحقق بعد التنفيذ

1. `php artisan migrate` — يعمل بدون أخطاء
2. تسجيل دخول Admin — يعمل
3. عرض الطلبات وتفاصيلها — يعمل بدون أخطاء
4. API الطلبات — يعمل
5. عدم ظهور أي مرجع لـ `delivery_man` أو `DeliveryMan` في الـ logs

---

## 12. الخلاصة

هذه الخطة تحذف كل ما يتعلق برجال التوصيل من قاعدة البيانات والكود. التنفيذ يتطلب تعديلات في أكثر من 30 ملفاً.

**عند الموافقة، اكتب "تم" أو "وافق" لبدء التنفيذ.**
