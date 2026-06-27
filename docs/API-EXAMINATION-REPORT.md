# تقرير فحص API — Elite Vape

**التاريخ:** 2026-03-07  
**المسار الأساسي:** `/api/v1`

---

## 1. ملخص سريع

| البند | الحالة |
|-------|--------|
| عدد الـ endpoints | 90 route |
| التوثيق | ✅ `API-ENDPOINTS-LIST.md` |
| نقاط الولاء | ✅ مكتملة |
| **مشكلة حرجة** | ⚠️ مفاتيح Passport غير صالحة — endpoints تستخدم `auth:api` ترجع 500 |

---

## 2. نتائج الاختبار الفعلي

| Endpoint | HTTP | الملاحظة |
|----------|------|----------|
| `GET /api/v1/config` | 200 | ✅ يعمل |
| `GET /api/v1/categories` | 200 | ✅ يعمل |
| `GET /api/v1/products/latest` | 500 | ❌ LogicException: Invalid key (Passport) |
| `GET /api/v1/customer/info` | 500 | ❌ نفس الخطأ — يتطلب auth |
| `GET /api/v1/banners` | — | لم يُختبر |
| `GET /api/v1/flash-sale` | — | لم يُختبر |

---

## 3. المشكلة الحرجة: Passport Keys

**الخطأ:**
```
LogicException: Invalid key supplied
vendor/league/oauth2-server/src/CryptKey.php
```

**السبب:** أي طلب يستدعي `auth('api')` (Passport guard) يفشل لأن مفاتيح OAuth غير موجودة أو غير صالحة.

**الحل:**
```bash
php artisan passport:keys
```

إذا لم تُنشأ المفاتيح مسبقاً، سيتم إنشاؤها في `storage/` (oauth-private.key, oauth-public.key).

---

## 4. بنية الـ API

### 4.1 المجموعات الرئيسية

| المجموعة | عدد الـ routes | التوثيق |
|----------|---------------|---------|
| Auth | 18 | تسجيل، دخول، OTP، إعادة تعيين |
| Config | 3 | إعدادات، رسوم توصيل |
| Products | 10 | منتجات، بحث، تقييمات |
| Categories | 6 | تصنيفات |
| Customer | 26 | ملف، عناوين، طلبات، ولاء، مفضلة |
| Delivery-man | 18 | طلبات، موقع، حالة |
| Coupon | 2 | قائمة، تطبيق |
| أخرى | 7 | banners, pages, flash-sale, guest, إلخ |

### 4.2 التوثيق (Auth)

| النوع | الآلية |
|-------|--------|
| Bearer | `Authorization: Bearer {token}` — للعميل |
| token | `?token=xxx` أو body — للمندوب |
| guest-id | `guest-id: {id}` — للضيف |
| X-localization | `ar|en|he` |

### 4.3 Rate Limiting

| المسار | الحد |
|--------|------|
| API عام | 60 طلب/دقيقة |
| Auth | 20 طلب/دقيقة |
| Contact Us | 10 طلبات/دقيقة |

---

## 5. Endpoints تتأثر بمشكلة Passport

كل endpoint يستخدم `auth:api` أو يستدعي `auth('api')` داخلياً سيفشل حتى تُصلح المفاتيح:

- `customer/*` (باستثناء address/order مع guest_user)
- `products/reviews/submit`
- `delivery-man/reviews/*`
- `products/latest` — يستدعي `Helpers::apply_user_type_prices_to_products` التي تستدعي `auth('api')`

---

## 6. التوصيات

1. **فوري:** تشغيل `php artisan passport:keys` لتوليد مفاتيح Passport.
2. **اختياري:** التأكد من أن `storage/oauth-*.key` لا تُرفع إلى Git (يجب أن تكون في .gitignore).
3. **للتحقق:** بعد إصلاح المفاتيح، إعادة اختبار `products/latest` و `customer/info`.

---

## 7. الملفات المرجعية

| الملف | الوصف |
|-------|--------|
| `docs/API-ENDPOINTS-LIST.md` | قائمة كاملة بالـ endpoints |
| `docs/API-PROGRAMMER-READY-SUMMARY.md` | ملخص التجهيز ونقاط الولاء |
| `docs/API-LOYALTY-POINTS-GAP.md` | فجوات نقاط الولاء |
| `routes/api/v1/api.php` | تعريف الـ routes |
