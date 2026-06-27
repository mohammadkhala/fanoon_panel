# خطة تحديث API الكاملة — Elite Vape

> **ملاحظة:** هذه خطة تحليلية وتخطيطية فقط. لا يتم تنفيذ أي تعديلات حتى يُقال "تم".

---

## 1. الوضع الحالي — ما هو موجود

### 1.1 بنية الـ API

| العنصر | الوصف |
|-------|--------|
| **Base URL** | `/api/v1` |
| **API v2** | فارغ (`routes/api/v2/api.php` — تم إزالة ثغرة RCE) |
| **المصادقة** | Laravel Passport (OAuth2) — `auth:api` |
| **مصادقة الضيف** | `guest-id` header + middleware `guest_user` |
| **اللغة** | `X-localization` header (ar, en, he) |
| **الـ Throttling** | Auth: 20/min، Contact: 10/min |

### 1.2 قائمة الـ Endpoints الحالية (api/v1)

#### Auth (بدون auth:api)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| POST | `auth/registration` | تسجيل عميل |
| POST | `auth/login` | تسجيل دخول |
| POST | `auth/social-customer-login` | تسجيل دخول اجتماعي |
| POST | `auth/check-phone` | التحقق من رقم الهاتف |
| POST | `auth/verify-phone` | التحقق من OTP الهاتف |
| POST | `auth/check-email` | التحقق من البريد |
| POST | `auth/verify-email` | التحقق من البريد |
| POST | `auth/firebase-auth-verify` | التحقق Firebase |
| POST | `auth/verify-otp` | التحقق من OTP |
| POST | `auth/registration-with-otp` | تسجيل مع OTP |
| POST | `auth/existing-account-check` | فحص وجود حساب |
| POST | `auth/registration-with-social-media` | تسجيل عبر وسائل التواصل |
| POST | `auth/forgot-password` | نسيت كلمة المرور |
| POST | `auth/verify-token` | التحقق من رمز إعادة التعيين |
| PUT | `auth/reset-password` | إعادة تعيين كلمة المرور |
| POST | `auth/delivery-man/register` | تسجيل مندوب |
| POST | `auth/delivery-man/login` | دخول مندوب |

#### Config (عام)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `config/` | إعدادات التطبيق الكاملة |
| GET | `config/delivery-fee` | رسوم التوصيل |
| GET | `config/delivery-charge` | رسوم التوصيل حسب المنطقة |

#### Products (عام)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `products/latest` | أحدث المنتجات |
| GET | `products/discounted` | المنتجات المخفضة |
| GET | `products/search` | بحث |
| GET | `products/details/{id}` | تفاصيل منتج |
| GET | `products/related-products/{product_id}` | منتجات ذات صلة |
| GET | `products/reviews/{product_id}` | تقييمات المنتج |
| GET | `products/rating/{product_id}` | تقييم المنتج |
| GET | `products/new-arrival` | وصول جديد |
| POST | `products/reviews/submit` | إرسال تقييم (auth) |

#### Banners, Categories, Pages (عام)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `banners/` | البانرات |
| GET | `categories/` | التصنيفات |
| GET | `categories/childes/{category_id}` | التصنيفات الفرعية |
| GET | `categories/products/{category_id}` | منتجات التصنيف |
| GET | `categories/products/{category_id}/all` | كل المنتجات |
| GET | `categories/featured` | تصنيفات مميزة |
| GET | `categories/popular` | تصنيفات شائعة |
| GET | `pages` | الصفحات |
| GET | `language/` | اللغات |

#### Customer (auth:api)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `customer/info` | معلومات العميل |
| PUT | `customer/update-profile` | تحديث الملف |
| PUT | `customer/cm-firebase-token` | تحديث FCM |
| POST | `customer/verify-profile-info` | التحقق من الملف |
| DELETE | `customer/remove-account` | حذف الحساب |
| GET | `customer/address/list` | عناوين (guest_user) |
| POST | `customer/address/add` | إضافة عنوان |
| PUT | `customer/address/update/{id}` | تحديث عنوان |
| DELETE | `customer/address/delete` | حذف عنوان |
| GET | `customer/order/list` | قائمة الطلبات |
| POST | `customer/order/details` | تفاصيل الطلب |
| POST | `customer/order/place` | إنشاء طلب |
| PUT | `customer/order/cancel` | إلغاء طلب |
| POST | `customer/order/track` | تتبع الطلب |
| PUT | `customer/order/payment-method` | تحديث طريقة الدفع |
| GET | `customer/reorder/products` | منتجات إعادة الطلب |
| POST | `customer/payment-mobile` | دفع (guest) |
| GET | `customer/message/get-admin-message` | رسائل |
| POST | `customer/message/send-admin-message` | إرسال رسالة |
| GET | `customer/message/get-order-message` | رسائل الطلب |
| POST | `customer/message/send/{sender_type}` | إرسال |
| GET | `customer/wish-list/` | المفضلة |
| POST | `customer/wish-list/add` | إضافة للمفضلة |
| DELETE | `customer/wish-list/remove` | حذف من المفضلة |

#### Delivery Man (بدون auth:api)
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `delivery-man/profile` | ملف المندوب |
| GET | `delivery-man/current-orders` | الطلبات الحالية |
| GET | `delivery-man/all-orders` | كل الطلبات |
| GET | `delivery-man/orders-count` | عدد الطلبات |
| POST | `delivery-man/record-location-data` | تسجيل الموقع |
| GET | `delivery-man/order-delivery-history` | سجل التوصيل |
| PUT | `delivery-man/update-order-status` | تحديث حالة الطلب |
| PUT | `delivery-man/update-payment-status` | تحديث حالة الدفع |
| GET | `delivery-man/order-details` | تفاصيل الطلب |
| GET | `delivery-man/last-location` | آخر موقع |
| PUT | `delivery-man/update-fcm-token` | تحديث FCM |
| GET | `delivery-man/order-model` | نموذج الطلب |
| DELETE | `delivery-man/remove-account` | حذف الحساب |
| POST | `delivery-man/message/get-message` | رسائل |
| POST | `delivery-man/message/send/{sender_type}` | إرسال |
| GET | `delivery-man/reviews/{delivery_man_id}` | تقييمات (auth) |
| GET | `delivery-man/reviews/rating/{delivery_man_id}` | تقييم |
| POST | `delivery-man/reviews/submit` | إرسال تقييم (auth) |

#### Coupon, Notifications, Guest, Contact
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `coupon/list` | قائمة الكوبونات (guest) |
| GET | `coupon/apply` | تطبيق كوبون (guest) |
| GET | `notifications/` | الإشعارات (guest) |
| POST | `guest/add` | إضافة ضيف |
| POST | `contact-us` | تواصل معنا |
| GET | `flash-sale` | عرض فلاش |
| POST | `fcm-subscribe-to-topic` | اشتراك FCM |
| GET | `user-types` | أنواع المستخدمين |

---

### 1.3 صيغة الاستجابة الحالية

#### نجاح (Success)
- **غير موحدة:** بعض الـ endpoints ترجع `data` مباشرة، وبعضها `products`، `orders`، إلخ.
- **responseFormatter:** `response_code`, `message`, `total_size`, `limit`, `offset`, `data`, `errors`
- **Config:** يرجع كائن مباشر بدون غلاف.
- **Product:** `products`, `total_size`, `limit`, `offset` حسب الدالة.

#### أخطاء (Errors)
- **Validation:** `{ "errors": [ { "code": "field_name", "message": "..." } ] }` — HTTP 403
- **Business:** `{ "errors": [ { "code": "order", "message": "Order not found!" } ] }` — HTTP 404
- **Auth:** `authentication-failed` route يرجع `errors` — HTTP 401

#### رموز HTTP المستخدمة
- 200: نجاح
- 403: خطأ تحقق (validation)
- 404: غير موجود
- 401: غير مصرح

---

### 1.4 التوثيق الموجود

| الملف | المحتوى |
|-------|---------|
| `docs/API_ORDERS_LIST.md` | توثيق endpoint واحد فقط: `GET /admin/orders/list/{status}` (Admin وليس API) |

---

## 2. ما هو مطلوب — معايير API ذات جودة

### 2.1 معايير REST API

| المعيار | الوصف | الحالة الحالية |
|---------|-------|-----------------|
| **توحيد الاستجابة** | نفس البنية لكل endpoint | ❌ غير موحد |
| **رموز HTTP صحيحة** | 200, 201, 400, 401, 403, 404, 422, 500 | ⚠️ جزئي (403 للتحقق بدل 422) |
| **صيغة JSON موحدة** | `{ success, data, message, errors?, meta? }` | ❌ غير موحد |
| **Pagination موحد** | `meta: { current_page, last_page, per_page, total }` | ⚠️ جزئي |
| **Versioning** | `api/v1` موجود | ✅ |
| **CORS** | للويب | ✅ |
| **Rate Limiting** | Throttle على Auth | ✅ جزئي |

### 2.2 ما يجب أن ينتجه المصمم (مستند API)

1. **توثيق شامل (OpenAPI/Swagger):**
   - كل الـ endpoints
   - Method، Path، Query، Body
   - Headers (Authorization, X-localization, guest-id)
   - أمثلة Request/Response
   - رموز الأخطاء

2. **Postman Collection:**
   - جاهز للاستيراد
   - متغيرات بيئة (base_url, token, guest_id)

3. **مستندات منفصلة:**
   - Authentication Flow
   - Error Codes
   - Pagination
   - Localization

---

## 3. خطة التنفيذ المقترحة

### المرحلة 1: توحيد صيغة الاستجابة (Backend)

| # | المهمة | الوصف |
|---|--------|-------|
| 1.1 | إنشاء `ApiResponse` Trait/Helper | دالة موحدة: `success($data, $message, $code)`, `error($errors, $message, $code)` |
| 1.2 | توحيد صيغة النجاح | `{ "success": true, "data": {...}, "message": "..." }` |
| 1.3 | توحيد صيغة الخطأ | `{ "success": false, "errors": [...], "message": "..." }` |
| 1.4 | توحيد Pagination | `meta: { current_page, last_page, per_page, total }` |
| 1.5 | تصحيح رموز HTTP | 422 للتحقق، 401 للـ auth، 404 للـ not found |

### المرحلة 2: تحديث API Controllers

| # | المهمة | الوصف |
|---|--------|-------|
| 2.1 | تحديث Auth Controllers | استخدام ApiResponse |
| 2.2 | تحديث ProductController | توحيد الـ products |
| 2.3 | تحديث OrderController | توحيد الطلبات |
| 2.4 | تحديث CustomerController | توحيد |
| 2.5 | تحديث ConfigController | إضافة غلاف success |
| 2.6 | تحديث باقي الـ Controllers | Category, Banner, Coupon, إلخ |

### المرحلة 3: التوثيق

| # | المهمة | الوصف |
|---|--------|-------|
| 3.1 | إنشاء `openapi.yaml` أو `swagger.json` | مواصفات OpenAPI 3.0 |
| 3.2 | إعداد Swagger UI | `/api/docs` للعرض التفاعلي |
| 3.3 | Postman Collection | تصدير JSON |
| 3.4 | مستندات Markdown | لكل مجموعة endpoints |

### المرحلة 4: تحسينات إضافية

| # | المهمة | الوصف |
|---|--------|-------|
| 4.1 | API Resources | استخدام Laravel API Resources لتوحيد التحويل |
| 4.2 | Form Requests | توحيد التحقق في كل endpoint |
| 4.3 | إضافة API للويب | Admin/Branch endpoints التي تدعم JSON (مثل orders list) — توثيقها |
| 4.4 | إضافة Endpoints ناقصة | إذا وجدت حاجة من التطبيق |

---

## 4. Endpoints إضافية قد يحتاجها المصمم

| Endpoint | الوصف | الحالة |
|----------|-------|--------|
| `GET /api/v1/products` | قائمة منتجات مع فلترة | موجود جزئياً (search) |
| `GET /api/v1/orders/{id}` | تفاصيل طلب واحد | موجود كـ `order/details` (POST) |
| `GET /api/v1/customer/addresses` | عناوين العميل | موجود كـ `address/list` |
| Health check | `GET /api/v1/health` | غير موجود |
| إصدار API | في header أو response | غير موجود |

---

## 5. Admin/Branch Web APIs (غير api/v1)

هذه endpoints تستخدم جلسة Admin/Branch وليست OAuth:

| Endpoint | الوصف | التوثيق |
|----------|-------|---------|
| `GET /admin/orders/list/{status}` | قائمة الطلبات | ✅ موثق في API_ORDERS_LIST.md |
| غيرها | product, category, customer, إلخ | ❌ غير موثق |

**إذا كان المصمم الويب يحتاجها:** يجب توثيقها في مستند منفصل أو إضافة دعم JSON مع توثيق.

---

## 6. ملخص الأولويات

| الأولوية | المهمة | الجهد |
|----------|--------|-------|
| 1 | إنشاء ApiResponse وتوحيد صيغة الاستجابة | متوسط |
| 2 | توثيق OpenAPI/Swagger لكل api/v1 | كبير |
| 3 | Postman Collection | صغير |
| 4 | تحديث Controllers لاستخدام ApiResponse | كبير |
| 5 | توثيق Admin/Branch JSON endpoints | متوسط |

---

## 7. الملفات المهمة للمراجعة

```
routes/api/v1/api.php
app/Http/Controllers/Api/V1/*.php
app/CentralLogics/Helpers.php (error_processor)
app/Library/Helpers.php (responseFormatter)
app/Http/Resources/*.php
docs/API_ORDERS_LIST.md
```

---

## 8. الخطوة التالية

**بانتظار موافقتك:** عند قول "تم"، يمكن البدء بتنفيذ المرحلة 1 (توحيد صيغة الاستجابة) ثم التوثيق.
