# خطة تجهيز API للمبرمج — Elite Vape

> **لا يُنفَّذ أي تعديل حتى يُقال "تم".**

---

## الأهداف

| المعيار | الوصف |
|---------|-------|
| **سرعة** | استجابة سريعة، cache حيث يلزم، تقليل استعلامات غير ضرورية |
| **أمان** | تحقق صارم، منع IDOR، rate limiting، حماية بيانات حساسة |
| **جودة** | صيغة موحدة، رموز HTTP صحيحة، توثيق واضح |

---

## المرحلة 0: التحضير (بدون تعديل كود)

| # | المهمة | المخرجات |
|---|--------|-----------|
| 0.1 | تجميع قائمة endpoints كاملة | `docs/API-ENDPOINTS-LIST.md` |
| 0.2 | تحديد endpoints بحاجة صيانة | جدول: المشكلة + الحل |
| 0.3 | تحديد endpoints جديدة مطلوبة | جدول: الـ endpoint + السبب |

---

## المرحلة 1: الصيانة السريعة (أولوية عالية)

### 1.1 أمان

| # | المهمة | الملف | الوصف |
|---|--------|-------|-------|
| 1.1.1 | مراجعة auth على delivery-man | `routes/api/v1/api.php` | التأكد أن كل endpoint محمي بـ auth:api أو token صحيح |
| 1.1.2 | مراجعة guest_user | `GuestUser` middleware | التأكد من التحقق من guest-id وعدم السماح بـ IDOR |
| 1.1.3 | إضافة throttle عام | `routes/api/v1/api.php` | throttle:60,1 للـ API العام (اختياري) |
| 1.1.4 | منع تسريب أعمدة حساسة | Controllers | التأكد أن response لا يحتوي password, token, إلخ |

### 1.2 إصلاحات سريعة

| # | المهمة | الوصف |
|---|--------|-------|
| 1.2.1 | رمز HTTP للتحقق | استبدال 403 بـ 422 عند فشل validation |
| 1.2.2 | Cache Config | Config مُخزَّن — التأكد من invalidate عند تغيير الإعدادات |
| 1.2.3 | معالجة استثناءات placeOrder | try/catch مع رسالة آمنة (لا تكشف تفاصيل داخلية) |

---

## المرحلة 2: نقاط الولاء (ميزة ناقصة)

| # | المهمة | الجهد | الملفات |
|---|--------|-------|---------|
| 2.1 | إكمال Config | صغير | `ConfigController.php` |
| 2.2 | دعم loyalty في placeOrder | متوسط | `OrderController`, `OrderPricing`, `StoreOrder` |
| 2.3 | endpoint `customer/loyalty` | صغير | `LoyaltyController` أو `CustomerController` |
| 2.4 | endpoint `customer/loyalty/history` | صغير | نفس الـ controller |

**التفاصيل:** راجع `docs/API-LOYALTY-POINTS-GAP.md`

---

## المرحلة 3: توحيد الاستجابة (للسرعة والجودة)

| # | المهمة | الوصف |
|---|--------|-------|
| 3.1 | إنشاء `ApiResponse` helper | `success()`, `error()`, `paginated()` |
| 3.2 | تطبيق على Controllers الجديدة فقط | عدم تعديل كل الـ API دفعة واحدة (لتقليل المخاطر) |
| 3.3 | توثيق الصيغة الموحدة | للمبرمج: `{ success, data?, errors?, message?, meta? }` |

---

## المرحلة 4: التوثيق للمبرمج

| # | المهمة | المخرجات |
|---|--------|-----------|
| 4.1 | مستند endpoints | Markdown: Method, Path, Auth, Params, Response |
| 4.2 | Postman Collection | JSON جاهز للاستيراد |
| 4.3 | أمثلة Request/Response | لكل endpoint رئيسي |
| 4.4 | رموز الأخطاء | جدول: code, message, HTTP |

---

## ترتيب التنفيذ المقترح (للسرعة)

```
الأسبوع 1:
├── 0.1–0.3 (التحضير)
├── 1.1.1–1.1.4 (أمان)
└── 1.2.1–1.2.3 (إصلاحات)

الأسبوع 2:
├── 2.1–2.4 (نقاط الولاء)
└── 3.1 (ApiResponse)

الأسبوع 3:
├── 4.1–4.4 (التوثيق)
└── 3.2–3.3 (تطبيق ApiResponse على endpoints جديدة فقط)
```

---

## قائمة الملفات المتوقعة للتعديل

| الملف | نوع التعديل |
|-------|-------------|
| `routes/api/v1/api.php` | إضافة routes، throttle |
| `app/Http/Controllers/Api/V1/ConfigController.php` | إكمال loyalty |
| `app/Http/Controllers/Api/V1/OrderController.php` | دعم loyalty في placeOrder |
| `app/Http/Requests/StoreOrder.php` | loyalty_points_used |
| `app/Traits/OrderPricing.php` | حساب loyalty discount |
| `app/Http/Controllers/Api/V1/CustomerController.php` | أو LoyaltyController جديد |
| `app/Helpers/ApiResponse.php` (جديد) | helper موحد |
| `docs/*.md` | توثيق |

---

## نقاط لا تُعدَّل (للسرعة)

- عدم إعادة كتابة كل الـ Controllers دفعة واحدة
- عدم تغيير بنية الـ database
- عدم تعديل الـ API v2 (فارغ)
- عدم إضافة OpenAPI/Swagger في المرحلة الأولى (يمكن لاحقاً)

---

## معايير القبول

| المعيار | كيف نتحقق |
|---------|------------|
| أمان | مراجعة auth و guest وعدم تسريب بيانات |
| سرعة | عدم إضافة استعلامات ثقيلة، استخدام cache للـ config |
| جودة | صيغة أخطاء موحدة، رموز HTTP صحيحة |
| توثيق | المبرمج يستطيع فهم كل endpoint من المستند |

---

## الخطوة التالية

**بانتظار "تم"** للبدء بالتنفيذ.
