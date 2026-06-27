# نقاط الولاء — الفجوة بين النظام و API

## 1. ما هو موجود في النظام (Admin/Backend)

### 1.1 قاعدة البيانات
| الجدول | الحقول |
|--------|--------|
| `loyalty_points` | user_id, points, level, total_spent |
| `loyalty_point_logs` | user_id, points, type, description, order_id |
| `orders` | loyalty_points_used, loyalty_discount_amount |

### 1.2 إعدادات الأعمال (Business Settings)
| المفتاح | الوصف |
|---------|-------|
| `loyalty_points_enabled` | تفعيل/إلغاء برنامج الولاء |
| `loyalty_amount_for_one_point` | المبلغ لكل نقطة (مثلاً 10 شيكل = 1 نقطة) |
| `loyalty_points_per_amount` | النقاط المكتسبة لكل مبلغ |
| `loyalty_point_redemption_value` | قيمة النقطة عند الاستبدال (مثلاً 0.5 شيكل لكل نقطة) |

### 1.3 المستويات (LoyaltyPoint::LEVELS)
| المستوى | الحد الأدنى للمصروف |
|---------|---------------------|
| bronze | 0 |
| silver | 500 |
| gold | 1500 |

### 1.4 المنطق الموجود
- **LoyaltyService::awardPointsForDeliveredOrder** — عند تسليم الطلب يُمنح العميل نقاطاً
- **LoyaltyPoint::deductPointsForOrder** — استبدال نقاط عند الطلب (كان مستخدماً في POS المحذوف)

---

## 2. ما هو موجود في API حالياً

| المصدر | المحتوى |
|--------|---------|
| **GET /api/v1/config** | `loyalty_points_enabled`, `loyalty_amount_for_one_point` فقط |
| **GET /api/v1/customer/info** | `loyalty_points`, `loyalty_level` (عبر CustomerResource) |

---

## 3. ما هو ناقص من API

### 3.1 Config API
| المفقود | الوصف |
|---------|-------|
| `loyalty_points_per_amount` | النقاط المكتسبة لكل مبلغ |
| `loyalty_point_redemption_value` | قيمة النقطة عند الاستبدال (مهم للتطبيق) |
| `loyalty_levels` | المستويات (bronze, silver, gold) وحدودها |

### 3.2 Place Order API
| المفقود | الوصف |
|---------|-------|
| `loyalty_points_used` | عدد النقاط المستخدمة في الطلب |
| حساب خصم الولاء | `OrderPricing::calculateOrderAmount` لا يحسب خصم الولاء |
| حفظ في الطلب | `placeOrder` لا يحفظ `loyalty_points_used` ولا `loyalty_discount_amount` |
| خصم النقاط | لا يوجد استدعاء لـ `LoyaltyPoint::deductPointsForOrder` |

### 3.3 Endpoints غير موجودة
| Endpoint | الوصف |
|----------|-------|
| `GET /api/v1/customer/loyalty` | رصيد النقاط + المستوى + التاريخ |
| `GET /api/v1/customer/loyalty/history` | سجل النقاط (اكتساب/استبدال) |
| `POST /api/v1/customer/order/calculate-loyalty` | حساب خصم الولاء قبل الطلب |

### 3.4 Order Response
| المفقود | الوصف |
|---------|-------|
| `loyalty_points_used` | موجود في Order لكن قد لا يُرجع في كل استجابات الطلبات |
| `loyalty_discount_amount` | نفس الشيء |

---

## 4. خطة التعديلات المطلوبة على API

### المرحلة 1: إكمال Config
```php
// في ConfigController::configuration إضافة:
'loyalty_points_per_amount' => (float) (Helpers::get_business_settings('loyalty_points_per_amount') ?? 1),
'loyalty_point_redemption_value' => (float) (Helpers::get_business_settings('loyalty_point_redemption_value') ?? 0.5),
'loyalty_levels' => LoyaltyPoint::LEVELS,
```

### المرحلة 2: Place Order — دعم استبدال النقاط
1. إضافة `loyalty_points_used` (اختياري) في `StoreOrder` rules
2. في `OrderPricing::calculateOrderAmount` أو `placeOrder`:
   - إذا أُرسل `loyalty_points_used`:
     - التحقق من رصيد العميل
     - حساب `loyalty_discount_amount` = points × redemption_value
     - خصم المبلغ من الإجمالي
     - استدعاء `LoyaltyPoint::deductPointsForOrder` عند حفظ الطلب
3. حفظ `loyalty_points_used` و `loyalty_discount_amount` في الطلب

### المرحلة 3: Endpoints جديدة
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `customer/loyalty` | رصيد النقاط، المستوى، قيمة الاستبدال |
| GET | `customer/loyalty/history` | سجل النقاط (مع pagination) |

### المرحلة 4: التأكد من Order Response
- التأكد أن `getOrderList` و `getOrderDetails` و `track_order` ترجع `loyalty_points_used` و `loyalty_discount_amount` عند وجودها

---

## 5. ملخص سريع

| الميزة | في النظام | في API |
|--------|----------|--------|
| عرض النقاط في Customer | ✅ | ✅ (customer/info) |
| إعدادات الولاء (config) | ✅ | ⚠️ جزئي (ناقص 2) |
| المستويات | ✅ | ❌ |
| استبدال نقاط عند الطلب | ✅ (كان في POS) | ❌ |
| سجل النقاط | ✅ | ❌ |
| منح نقاط عند التسليم | ✅ | ✅ (تلقائي) |
