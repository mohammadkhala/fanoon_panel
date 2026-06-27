# دليل المبرمج — Elite Vape API

> دليل شامل لاستخدام API التطبيق

---

## 1. المعلومات الأساسية

| البند | القيمة |
|-------|--------|
| **Base URL** | `https://your-domain.com/api/v1` |
| **Content-Type** | `application/json` |
| **Accept** | `application/json` |
| **المصادقة** | Bearer Token (OAuth2 / Passport) |

---

## 2. الـ Headers المطلوبة

| Header | مطلوب | الوصف |
|--------|-------|-------|
| `Authorization` | للعميل | `Bearer {access_token}` |
| `X-localization` | اختياري | `ar` أو `en` أو `he` — للغة الاستجابة |
| `guest-id` | للضيف | معرف الضيف من `POST guest/add` |
| `Accept` | موصى به | `application/json` |

---

## 3. سير العمل (Flow) للعميل

```
1. إدخال التطبيق
   └── GET config/          ← إعدادات التطبيق (اللغة، الدفع، الولاء، إلخ)

2. إذا ضيف (Guest)
   └── POST guest/add      ← الحصول على guest_id
   └── إرسال guest-id في كل طلب يتطلب ضيف

3. إذا تسجيل
   └── POST auth/registration
   └── أو POST auth/login
   └── حفظ token في التطبيق

4. التصفح
   └── GET products/latest
   └── GET categories/
   └── GET products/details/{id}

5. الطلب
   └── POST customer/address/add
   └── POST customer/order/place
   └── (اختياري) GET coupon/apply?code=XXX
   └── (اختياري) loyalty_points_used

6. التتبع
   └── POST customer/order/track
```

---

## 4. التوثيق (Auth)

### 4.1 عميل مسجل (Bearer)

بعد تسجيل الدخول أو التسجيل، استخدم الـ token في كل طلب:

```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

### 4.2 ضيف (Guest)

1. استدعِ `POST /api/v1/guest/add`
2. احفظ `guest.id` من الاستجابة
3. أرسل في كل طلب: `guest-id: {id}`

### 4.3 مندوب التوصيل (Delivery Man)

استخدم `token` في query أو body:

```
GET /api/v1/delivery-man/profile?token=xxx
```

---

## 5. أمثلة Request/Response

### 5.1 إعدادات التطبيق

```
GET /api/v1/config
```

**Response (200):**
```json
{
  "ecommerce_name": "Elite Vape",
  "ecommerce_logo": "...",
  "minimum_order_value": 10,
  "currency_symbol": "₪",
  "cash_on_delivery": "true",
  "digital_payment": "false",
  "loyalty_points_enabled": 1,
  "loyalty_amount_for_one_point": 10,
  "loyalty_points_per_amount": 1,
  "loyalty_point_redemption_value": 0.5,
  "loyalty_levels": {
    "bronze": {"min_spent": 0, "name": "Bronze"},
    "silver": {"min_spent": 500, "name": "Silver"},
    "gold": {"min_spent": 1500, "name": "Gold"}
  },
  "base_urls": {"product_image_url": "...", "..."},
  "areas": [...],
  "guest_checkout": 1
}
```

---

### 5.2 إضافة ضيف

```
POST /api/v1/guest/add
Content-Type: application/json

{
  "fcm_token": "optional-firebase-token"
}
```

**Response (200):**
```json
{
  "guest": {
    "id": 123,
    "ip_address": "...",
    "fcm_token": "...",
    "language_code": "ar"
  }
}
```

---

### 5.3 تسجيل عميل

```
POST /api/v1/auth/registration
Content-Type: application/json

{
  "f_name": "أحمد",
  "l_name": "محمد",
  "email": "user@example.com",
  "phone": "1234567890",
  "password": "123456",
  "user_type_id": 1
}
```

**Response (200):**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

---

### 5.4 تسجيل دخول

```
POST /api/v1/auth/login
Content-Type: application/json

{
  "email_or_phone": "user@example.com",
  "password": "123456",
  "type": "email"
}
```

**Response (200):**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "status": true
}
```

---

### 5.5 إنشاء طلب (Place Order)

```
POST /api/v1/customer/order/place
Content-Type: application/json
Authorization: Bearer {token}        ← أو guest-id: {id} للضيف

{
  "cart": [
    {
      "product_id": 1,
      "quantity": 2,
      "variation": [{"type": "500ml"}]
    }
  ],
  "payment_method": "cash_on_delivery",
  "payment_platform": "app",
  "callback": "https://yourapp.com/callback",
  "order_type": "delivery",
  "delivery_address_id": 5,
  "coupon_code": "SAVE10",
  "loyalty_points_used": 50,
  "order_note": "ملاحظة اختيارية",
  "bring_change_amount": 0,
  "is_guest": 0
}
```

**حقول مطلوبة:**

| الحقل | النوع | الوصف |
|-------|-------|-------|
| cart | array | عناصر السلة |
| cart[].product_id | int | معرف المنتج |
| cart[].quantity | int | الكمية (≥1) |
| cart[].variation | array | اختياري — للمنتجات ذات المتغيرات |
| payment_method | string | `cash_on_delivery` أو غيرها |
| payment_platform | string | `web` أو `app` |
| callback | url | رابط callback بعد الدفع |
| order_type | string | `delivery` أو `self_pickup` |
| delivery_address_id | int | معرف العنوان (للـ delivery) |
| is_guest | 0\|1 | 1 للضيف |

**حقول اختيارية:**

| الحقل | النوع | الوصف |
|-------|-------|-------|
| coupon_code | string | كود الكوبون |
| loyalty_points_used | int | نقاط للاستبدال (مسجل فقط) |
| order_note | string | ملاحظة |
| bring_change_amount | float | مبلغ الباقي |
| selected_delivery_area | int | منطقة التوصيل |
| distance | float | المسافة (كم) |
| branch_id | int | الفرع (إذا multi-branch) |

**Response (200):**
```json
{
  "message": "Order placed successfully",
  "order_id": 42
}
```

**Response (422) — validation:**
```json
{
  "errors": [
    {"code": "cart", "message": "cart is empty"},
    {"code": "loyalty_points_used", "message": "Insufficient loyalty points"}
  ]
}
```

---

### 5.6 إضافة عنوان

```
POST /api/v1/customer/address/add
Content-Type: application/json
Authorization: Bearer {token}   أو  guest-id: {id}

{
  "contact_person_name": "أحمد",
  "contact_person_number": "0599123456",
  "city": "غزة",
  "address": "شارع الرمال",
  "address_type": "Home",
  "area_id": 1,
  "longitude": null,
  "latitude": null
}
```

---

### 5.7 رصيد نقاط الولاء

```
GET /api/v1/customer/loyalty
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "loyalty_points_enabled": true,
  "points": 120,
  "level": "silver",
  "total_spent": 650.5,
  "redemption_value_per_point": 0.5,
  "levels": {
    "bronze": {"min_spent": 0, "name": "Bronze"},
    "silver": {"min_spent": 500, "name": "Silver"},
    "gold": {"min_spent": 1500, "name": "Gold"}
  }
}
```

---

### 5.8 سجل نقاط الولاء

```
GET /api/v1/customer/loyalty/history?per_page=15
Authorization: Bearer {token}
```

**Response (200):** Paginated (Laravel paginator)

---

### 5.9 تتبع الطلب

```
POST /api/v1/customer/order/track
Content-Type: application/json

{
  "order_id": 42,
  "phone": "0599123456"
}
```

---

### 5.10 رسوم التوصيل حسب المنطقة

```
GET /api/v1/config/delivery-charge?area_id=3
```

**Response (200):**
```json
{
  "delivery_charge": 15.5
}
```

---

## 6. صيغة الأخطاء

| HTTP | الحالة |
|------|--------|
| 200 | نجاح |
| 401 | غير مصرح (token غير صالح أو منتهي) |
| 403 | مرفوض (مثلاً: محظور) |
| 404 | غير موجود |
| 422 | فشل التحقق (Validation) |
| 429 | تجاوز حد الطلبات (Throttle) |
| 500 | خطأ داخلي |

**صيغة الأخطاء:**
```json
{
  "errors": [
    {"code": "field_name", "message": "رسالة الخطأ"}
  ]
}
```

---

## 7. Rate Limiting

| المسار | الحد |
|--------|------|
| API عام | 60 طلب/دقيقة |
| Auth (login, OTP, إلخ) | 20 طلب/دقيقة |
| Contact Us | 10 طلبات/دقيقة |

عند التجاوز: **429 Too Many Requests**

---

## 8. ملاحظات مهمة للمبرمج

### 8.1 الضيف vs المسجل

- **الضيف:** يحتاج `guest-id` header. لا يمكنه استخدام الكوبونات أو نقاط الولاء.
- **المسجل:** يحتاج `Authorization: Bearer {token}`. يمكنه استخدام الكوبونات ونقاط الولاء.

### 8.2 نقاط الولاء

- **استبدال:** `loyalty_points_used` في placeOrder — مسموح فقط للمسجل.
- **التحقق:** إذا الرصيد أقل من المطلوب → 422.
- **قيمة النقطة:** من `config` → `loyalty_point_redemption_value`.

### 8.3 الطلبات

- **address/order:** تعمل مع Bearer أو guest-id (بدون auth:api).
- **order_type:** `delivery` أو `self_pickup`.
- **payment_method:** `cash_on_delivery` أو غيرها حسب الإعدادات.

### 8.4 المنتجات

- **variation:** للمنتجات ذات المتغيرات (مثل الحجم)، أرسل `variation: [{"type": "500ml"}]`.
- **الصور:** من `config.base_urls.product_image_url` + اسم الملف.

---

## 9. قائمة الملفات المرجعية

| الملف | الوصف |
|-------|--------|
| `docs/API-ENDPOINTS-LIST.md` | قائمة كاملة بالـ endpoints |
| `docs/API-PROGRAMMER-READY-SUMMARY.md` | ملخص التجهيز |
| `docs/API-EXAMINATION-REPORT.md` | تقرير الفحص |
| `docs/API-LOYALTY-POINTS-GAP.md` | تفاصيل نقاط الولاء |

---

## 10. إعداد بيئة التطوير

1. **Passport Keys:** تأكد من وجود مفاتيح OAuth:
   ```bash
   php artisan passport:keys
   ```

2. **Base URL:** استخدم عنوان السيرفر الفعلي (مثلاً `https://api.elitevape.com`).

3. **اللغة:** أرسل `X-localization: ar` للعربية.
