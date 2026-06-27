# إعداد CORS لتطبيق Flutter ويب

> **الغرض:** السماح لتطبيق Flutter ويب (مثلاً على `http://localhost:8090`) بت consuming الـ API من `admin.elitevape.online` بدون أخطاء CORS.

---

## 1. المشكلة

عند فتح التطبيق من المتصفح على `http://localhost:8090`، يظهر:

```
Access to XMLHttpRequest at 'https://admin.elitevape.online/api/v1/pages' from origin 'http://localhost:8090' 
has been blocked by CORS policy: Request header field authorization is not allowed by Access-Control-Allow-Headers in preflight response.
```

---

## 2. الحل (من طرف السيرفر)

### 2.1 إعداد `.env` على السيرفر

على السيرفر (`admin.elitevape.online`)، أضف أو عدّل في `.env`:

```env
# إضافة localhost للتطوير مع Flutter ويب
CORS_ALLOWED_ORIGINS=https://elitevape.online,https://www.elitevape.online,http://localhost:8090,http://127.0.0.1:8090
```

للإنتاج فقط (بدون localhost):

```env
CORS_ALLOWED_ORIGINS=https://elitevape.online,https://www.elitevape.online
```

### 2.2 مسح الكاش بعد التعديل

```bash
php artisan config:clear
php artisan cache:clear
```

### 2.3 ما تم إعداده في الـ Backend

- **`config/cors.php`**:
  - `allowed_headers` يتضمن: `Authorization`, `Content-Type`, `X-localization`, `guest-id`, `Accept`, `Accept-Language`
  - `allowed_methods` يتضمن: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `OPTIONS`
  - `allowed_origins_patterns` يسمح بـ `http://localhost:*` و `http://127.0.0.1:*` (أي منفذ)
  - `max_age` = 86400 لتقليل طلبات preflight

---

## 3. ملاحظات لتطبيق Flutter

### 3.1 هيدر `Authorization: Bearer null`

إذا كان التوكن غير موجود، يُرسل `Bearer null`. في هذه الحالة:
- الـ API قد يعيد 401 أو JSON
- تأكد أن الـ endpoints العامة مثل `/api/v1/config` و `/api/v1/pages` و `/api/v1/config/delivery-fee` **لا تتطلب** توثيقاً
- إذا وُجدت، يمكن إزالة هيدر `Authorization` عند عدم وجود توكن

### 3.2 استجابة HTML بدلاً من JSON

إذا وُصلت استجابة HTML (مثلاً صفحة تسجيل الدخول) بدل JSON، فإن:
- السبب غالباً: إعادة توجيه 302 إلى صفحة login
- أو خطأ 500 يظهر صفحة HTML
- تأكد أن الـ endpoints المذكورة تُرجع دائماً JSON

---

## 4. التحقق

```bash
# من الطرفية (مثال)
curl -H "Origin: http://localhost:8090" \
     -H "Access-Control-Request-Method: GET" \
     -H "Access-Control-Request-Headers: authorization,content-type,x-localization" \
     -X OPTIONS  https://admin.elitevape.online/api/v1/pages -v
```

يجب أن ترى في الاستجابة:
- `Access-Control-Allow-Origin: http://localhost:8090`
- `Access-Control-Allow-Headers` يتضمن `authorization` و `content-type` و `x-localization`

---

## 5. Nginx (إن وُجد)

إذا كان Nginx يضيف هيدرات CORS خاصة، قد يحدث تعارض مع Laravel. تأكد أن:
- لا يتم إضافة `Access-Control-Allow-Origin` أو `Access-Control-Allow-Headers` من Nginx
- أو أن قيمها تتطابق مع إعدادات Laravel

---

## 6. المراجع

| الملف | الغرض |
|-------|-------|
| `config/cors.php` | إعدادات CORS |
| `.env` | `CORS_ALLOWED_ORIGINS` |
| `docs/حل_خطأ_HTML_بدل_JSON_في_التطبيق.md` | إعدادات Base URL و HTML vs JSON |
