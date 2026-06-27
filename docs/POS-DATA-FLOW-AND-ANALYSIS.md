# تحليل نقطة البيع (POS) — تدفق البيانات والمشاكل والحلول

**الهدف:** طلب الطلبات مباشرة من لوحة التحكم بدلاً من التطبيق أو الموقع.

---

## 1. نظرة عامة

| العنصر | الوصف |
|--------|--------|
| **الرابط** | `GET /admin/pos` |
| **الغرض** | إنشاء طلبات للعملاء من لوحة التحكم (استلام من المتجر أو توصيل) |
| **نوع الطلب** | `pos` (استلام) أو `pos_delivery` (توصيل) |

---

## 2. تدفق البيانات (Data Flow)

### 2.1 تحميل الصفحة الرئيسية

```
GET /admin/pos
    ↓
POSController::index()
    ↓
- جلب التصنيفات (categories)
- جلب المنتجات (total_stock > 0، مع فلتر category_id و keyword)
- Session::put('branch_id', 1)
- جلب العملاء (users)
- جلب الكوبونات للعميل المحدد
- جلب المناطق (areas) للفرع
    ↓
view('admin-views.pos.index')
```

### 2.2 الضغط على صنف (منتج)

```
click .pos-single-product-card
    ↓
quickView(productId)
    ↓
GET /admin/pos/quick-view?product_id=X
    ↓
POSController::quickView()
- جلب المنتج
- قراءة السلة من Session
- حساب الكمية والسعر والمخزون (مع/بدون متغيرات)
    ↓
JSON { view: HTML }
    ↓
$('#quick-view-modal').html(data.view)
$('#quick-view').modal('show')
```

### 2.3 إضافة للسلة (من نافذة Quick View)

```
click .add-to-shopping-cart
    ↓
addToCart()
    ↓
POST /admin/pos/add-to-cart
  { id, quantity, [choice_name]: value }
    ↓
POSController::addToCart()
- جلب المنتج
- بناء variant من choice_options
- إيجاد السعر والمخزون (من variations أو المنتج)
- إضافة/تحديث في Session['cart']
- calculatePOSCouponAndExtraDiscount()
    ↓
updateUI() → POST /admin/pos/cart_items → تحديث #cart
```

### 2.4 تغيير الكمية أو الخيارات في Quick View

```
change #add-to-cart-form input[radio] أو input[name=quantity]
    ↓
getVariantPrice(initial)
    ↓
GET quick-view-modal-footer أو POST variant_price
    ↓
تحديث السعر والمخزون في الـ modal
```

### 2.5 اختيار العميل

```
change #customer
    ↓
store_key('customer_id', value)
    ↓
POST /admin/pos/store-keys
    ↓
Session::put('customer_id', value)
```

### 2.6 اختيار نوع الطلب (استلام / توصيل)

```
change pos_order_delivery_type (pickup/delivery)
    ↓
applyPosDeliveryAndRefreshCart()
    ↓
POST /admin/pos/update-pos-delivery
  { order_delivery_type, delivery_address_id, selected_delivery_area }
    ↓
Session: pos_order_delivery_type, pos_delivery_address_id, pos_selected_delivery_area, pos_delivery_charge
    ↓
POST /admin/pos/cart_items → تحديث #cart (مع رسوم التوصيل)
```

### 2.7 إتمام الطلب

```
submit #order_place
    ↓
POST /admin/pos/order
  { order_delivery_type, delivery_address_id, selected_delivery_area,
    type (cash/card), paid_amount, order_note, ... }
    ↓
POSController::placeOrder()
- التحقق من السلة
- التحقق من العنوان والمنطقة (للتوصيل)
- إنشاء Order
- إنشاء OrderDetail لكل صنف
- خصم المخزون
- تطبيق نقاط الولاء
- إرسال إشعار/بريد (إن وُجد)
- Session::forget(cart, customer_id, ...)
    ↓
redirect back + عرض modal طباعة الفاتورة
```

---

## 3. هيكل البيانات

### 3.1 Session

| المفتاح | الوصف |
|---------|--------|
| `cart` | مصفوفة: عناصر السلة + coupon_discount, extra_discount, loyalty_points_used, ... |
| `customer_id` | معرف العميل (0 = عميل عابر) — **يجب اختيار العميل** لظهوره في السلة |
| `branch_id` | معرف الفرع |
| `pos_order_delivery_type` | pickup / delivery |
| `pos_delivery_address_id` | عنوان التوصيل |
| `pos_selected_delivery_area` | المنطقة |
| `pos_delivery_charge` | رسوم التوصيل |

### 3.2 عنصر السلة (cart item)

```php
[
    'id' => product_id,
    'variant' => 'Red-L',           // أو '' للمنتجات بدون متغيرات
    'variations' => ['Color'=>'Red','Size'=>'L'],
    'quantity' => 2,
    'price' => 100,
    'name' => '...',
    'discount' => 10,
    'image' => [...],
    'total_stock' => 50,
]
```

---

## 4. المشاكل والحلول

### 4.1 تم إصلاحها سابقاً

| المشكلة | الحل |
|---------|------|
| معالجات Quick View لا تعمل (Add to Cart) | نقل الأحداث إلى index.blade.php بـ $(document).on() |
| المنتجات بدون choice_options تسبب خطأ | إضافة تحقق `choice_options ?? '[]'` في Controller و View |
| تحديد أزرار الراديو في addToCart | تحديد النطاق بـ `#add-to-cart-form [type=radio]` |
| **العميل لا يظهر في السلة عند الاختيار** | 1) storeKeys يرجع دائماً كائن عميل (حتى لعميل عابر) 2) إضافة خيار "عميل عابر" صريح 3) عرض اسم العميل في قسم السلة (.pos-cart-customer-name) |

### 4.2 مشاكل واجهة المستخدم

| المشكلة | الأولوية | الحل |
|---------|----------|------|
| زر "Add to cart" بالإنجليزية فقط في بطاقة المنتج | متوسطة | استخدام `{{ translate('Add to cart') }}` |
| لا رابط لطلبات POS من صفحة POS | متوسطة | إضافة رابط/زر "طلبات نقطة البيع" |
| عدم وضوح حالة Walk In Customer | منخفضة | رسالة توضيحية عند عدم اختيار عميل |

### 4.3 مشاكل تقنية محتملة

| المشكلة | الوصف |
|---------|--------|
| branch_id ثابت | `Session::put('branch_id', 1)` في index — قد يحتاج اختيار فرع ديناميكي |
| قائمة العملاء كاملة | `$users = $this->user->all()` — قد تكون بطيئة مع عدد كبير |
| تعدد التابات | السلة في Session — تابان يشتركان نفس السلة |

### 4.4 مسارات POS

| المسار | الوظيفة |
|--------|---------|
| GET /admin/pos | الصفحة الرئيسية |
| GET /admin/pos/quick-view | معاينة سريعة |
| GET /admin/pos/quick-view-modal-footer | تحديث سعر/كمية في الـ modal |
| POST /admin/pos/variant_price | سعر ومخزون حسب المتغير |
| POST /admin/pos/add-to-cart | إضافة للسلة |
| POST /admin/pos/remove-from-cart | حذف من السلة |
| POST /admin/pos/emptyCart | إفراغ السلة |
| POST /admin/pos/cart_items | HTML السلة (لتحديث الواجهة) |
| POST /admin/pos/updateQuantity | تحديث كمية |
| POST /admin/pos/order | إتمام الطلب |
| POST /admin/pos/store-keys | حفظ customer_id في الجلسة |
| POST /admin/pos/update-pos-delivery | حفظ نوع الطلب والعنوان والمنطقة |
| GET /admin/pos/orders | قائمة طلبات POS |
| GET /admin/pos/order-details/{id} | تفاصيل (إعادة توجيه لـ admin.orders.details) |
| GET /admin/pos/invoice/{id} | فاتورة للطباعة |

---

## 5. ملخص التوصيات

1. **فوري:** إضافة رابط "طلبات نقطة البيع" في صفحة POS أو الشريط الجانبي.
2. **فوري:** ترجمة زر "Add to cart" في بطاقة المنتج.
3. **لاحقاً:** دعم اختيار الفرع عند فتح POS.
4. **لاحقاً:** تحميل العملاء عبر AJAX عند الحاجة (إن كان العدد كبيراً).
