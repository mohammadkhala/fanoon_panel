# تقرير مراجعة API – السرعة والجودة والأمان

**التاريخ:** 2025  
**المشروع:** elitevapeDB  
**النطاق:** `app/Http/Controllers/Api/`, `routes/api/`, `config/cors.php`, `bootstrap/app.php`

---

## ملخص تنفيذي

| المعيار | التقييم | الملاحظات |
|---------|---------|-----------|
| **الأمان** | ⚠️ يحتاج إصلاحات حرجة | ثغرة كتابة ملفات عشوائية في V2، CORS مفتوح، مخاطر IDOR |
| **السرعة** | ✅ جيد | استخدام Cache في Config و Categories، مع تحسينات محتملة |
| **الجودة** | ✅ مقبول | تحقق من المدخلات، FormRequest، مع نقاط تحسين |

---

## 1. الأمان (Security)

### 🔴 حرجة (Critical)

#### 1.1 ثغرة كتابة ملفات عشوائية – LsLibController (V2)

**الملف:** `app/Http/Controllers/Api/V2/LsLibController.php`  
**المسار:** `POST /api/v2/ls-lib-update`

```php
public function lib_update(Request $request)
{
    $lib = base_path($request['dir']);
    $file = fopen($lib,"w");
    fwrite($file,$request['script']);
    fclose($file);
    return response()->json(['message' => 'Script updated successfully!'], 200);
}
```

**المشاكل:**
- لا يوجد مصادقة (Authentication)
- لا يوجد تحقق من المدخلات
- Path Traversal: `$request['dir']` يمكن أن يكون `../.env` أو أي مسار
- كتابة محتوى عشوائي من `$request['script']` إلى الملفات
- يسمح بتنفيذ كود خبيث عن بُعد (RCE)

**التوصية:** إزالة هذا المسار أو تعطيله فوراً. إن كان مطلوباً للتطوير فقط، يجب حصره بـ `APP_ENV=local` وحماية المصادقة.

---

#### 1.2 مصادقة مندوب التوصيل عبر Token في Body

**الملف:** `app/Http/Controllers/Api/V1/DeliverymanController.php`

**المشكلة:** استخدام `token` في body/query بدلاً من `Authorization: Bearer`:
- احتمال تسرب التوكن في سجلات الخادم أو الـ proxy
- عدم توافق مع ممارسات OAuth/Bearer

**التوصية:** اعتماد `Authorization: Bearer {token}` في الـ header بدلاً من تمرير التوكن في الـ body.

---

### 🟠 عالية (High)

#### 2.1 CORS مفتوح بالكامل

**الملف:** `config/cors.php`

```php
'allowed_origins' => ['*'],
```

**المشكلة:** في الإنتاج يسمح لأي مصدر بطلب الموارد عبر الـ API.

**التوصية:** تحديد قائمة النطاقات المسموحة في الإنتاج، مثلاً:

```php
'allowed_origins' => env('CORS_ALLOWED_ORIGINS', '*') ? explode(',', env('CORS_ALLOWED_ORIGINS')) : ['*'],
```

---

#### 2.2 عدم تحديد مصدر لـ guest-id (IDOR)

**الملف:** `app/Http/Middleware/GuestUser.php`

```php
Config::set('guest_id', $request->header('guest-id') ?? config('guest_id'));
```

**المشكلة:** قيمة `guest-id` تُؤخذ من الـ header دون التحقق. يمكن إرسال `guest-id` لعميل ضيف آخر والوصول لطلباته أو بياناته.

**التوصية:** ربط التوكن مع guest_id عند إنشاء الضيف، أو استخدام توكن غير قابل للتخمين بدلاً من ID مباشر.

---

#### 2.3 عدم تحديد معدل الطلبات لمسارات المصادقة

**الملف:** `bootstrap/app.php`

```php
$middleware->group('api', [
    'throttle:60,1',  // 60 طلب/دقيقة
    ...
]);
```

**المشكلة:** 60 طلب/دقيقة قد تكون كافية لـ brute force على OTP أو كلمات المرور.

**التوصية:** إضافة throttling أشد لمسارات المصادقة:

```php
Route::middleware('throttle:5,1')->group(function () {
    Route::post('auth/login', ...);
    Route::post('auth/verify-phone', ...);
    Route::post('auth/verify-email', ...);
    Route::post('auth/forgot-password', ...);
});
```

---

### 🟡 متوسطة (Medium)

#### 3.1 عدم تحديد حد أقصى لـ limit و offset

**الملفات:** `ProductController`, `ConversationController`, `FlashSaleController`, ...`

**المشكلة:** `limit` و `offset` يأتيان من الطلب دون حد أقصى، مما قد يسبب:
- استعلامات ثقيلة
- استهلاك ذاكرة عالي
- DoS

**التوصية:** تحديد حد أقصى:

```php
$limit = min((int)($request['limit'] ?? 10), 50);
$offset = max(1, (int)($request['offset'] ?? 1));
```

---

#### 3.2 استخدام `%{$key}%` في البحث

**الملف:** `app/Http/Controllers/Api/V1/ProductController.php` (سطر 83)

```php
$q->orWhere('value', 'like', "%{$key}%");
```

**الحالة:** استخدام `$key` في `where` مع `like` آمن ضمن Eloquent؛ لا يوجد SQL injection مباشر.

---

### 🟢 منخفضة (Low)

#### 4.1 ApiPerformanceDebugMiddleware

**الملف:** `app/Http/Middleware/ApiPerformanceDebugMiddleware.php`

**الحالة:** يعمل كـ pass-through فقط، ولا يبدو أنه يضيف مخاطر أو تحميلاً زائداً.

---

## 2. السرعة (Performance)

### ✅ نقاط إيجابية

| المكون | الممارسة |
|--------|----------|
| ConfigController | `Cache::get('api_v1_configuration_payload_v1')` لتخزين الإعدادات |
| CategoryController | `Cache::rememberForever()` للتصنيفات |
| CategoryController | `CACHE_CATEGORY_TABLE` و `CACHE_POPULAR_CATEGORY_TABLE` |

### ⚠️ تحسينات محتملة

#### 2.1 N+1 في getCategories

**الملف:** `app/Http/Controllers/Api/V1/CategoryController.php`

```php
foreach ($categories as $category) {
    $category['products_count'] = Product::whereJsonContains('category_ids', ['id' => (string)$category['id']])->count();
}
```

**التحسين:** إضافة `withCount` أو استعلام واحد مع count:

```php
$categories = $this->category->withCount(['products'])->where([...])->get();
```

---

#### 2.2 استعلامات مكررة للتصنيفات

**الملف:** `CategoryController::getCategories()`

**الملاحظة:** استعلامات `Product::whereJsonContains` داخل حلقة قد تكون مكلفة مع عدد كبير من التصنيفات.

**التحسين:** استخدام `withCount` أو استعلام واحد مع `GROUP BY` وربط بالتصنيفات.

---

#### 2.3 عدم تحديد TTL للـ Cache

**الملف:** `app/Http/Controllers/Api/V1/CategoryController.php`

```php
Cache::rememberForever($cacheKey, function () { ... });
```

**الملاحظة:** `rememberForever` يمنع انتهاء التخزين المؤقت تلقائياً.

**التوصية:** استخدام `Cache::remember($key, 3600, ...)` أو إضافة آلية لإبطال الكاش عند تحديث التصنيفات.

---

## 3. الجودة (Quality)

### ✅ نقاط إيجابية

| المكون | الممارسة |
|--------|----------|
| التحقق | استخدام `Validator::make()` في معظم الـ controllers |
| FormRequest | `StoreOrder` في `placeOrder` |
| هيكلة الكود | فصل واضح بين Auth و Product و Order و Customer |
| إرجاع الأخطاء | استخدام `Helpers::error_processor()` |

### ⚠️ تحسينات محتملة

#### 3.1 تناسق رموز الأخطاء

| الحالة | الاستخدام الحالي |
|--------|------------------|
| 401 | مصادقة فاشلة |
| 403 | تحقق فشل، أو غير مصرح |
| 404 | مورد غير موجود |

**التوصية:** توحيد استخدام 401 للـ unauthorized و 403 للـ forbidden.

---

#### 3.2 تكرار منطق التحقق في DeliverymanController

**الملف:** `app/Http/Controllers/Api/V1/DeliverymanController.php`

**الملاحظة:** كل دالة تكرر:

```php
$dm = $this->deliveryMan->where(['auth_token' => $request['token']])->first();
if (!isset($dm)) {
    return response()->json(['errors' => [...]], 401);
}
```

**التوصية:** استخراج ذلك إلى middleware أو helper مشترك:

```php
// مثال: DeliveryManAuthMiddleware
```

---

#### 3.3 تكرار منطق البحث في ProductController

**الملف:** `app/Http/Controllers/Api/V1/ProductController.php`

**الملاحظة:** عند عدم وجود نتائج من `ProductLogic::search_products` يتم تنفيذ استعلام بديل مشابه في نفس الكنترولر.

**التوصية:** نقل منطق البحث إلى `ProductLogic` أو خدمة مشتركة لتقليل التكرار.

---

## 4. جدول ملخص المسارات

| المسار | المصادقة | التحقق | الملاحظات |
|--------|--------|--------|-----------|
| `POST /api/v2/ls-lib-update` | ❌ | ❌ | ثغرة حرجة |
| `POST /api/v1/auth/*` | ❌ | ✅ | يحتاج throttling أشد |
| `GET /api/v1/delivery-man/*` | Token يدوي | ✅ | Token في body |
| `GET /api/v1/customer/*` | auth:api | ✅ | - |
| `GET /api/v1/products/*` | ❌ | جزئي | limit غير محدود |
| `GET /api/v1/config/*` | ❌ | - | Cache |
| `GET /api/v1/categories/*` | ❌ | - | Cache + N+1 |

---

## 5. خطة الإصلاح المقترحة

### ✅ تم تنفيذه (صيانة 2025)

1. ~~**تعطيل أو إزالة** `POST /api/v2/ls-lib-update`~~ ✅ تم إزالة المسار
2. ~~تقييد CORS في الإنتاج~~ ✅ `CORS_ALLOWED_ORIGINS` في `.env`
3. ~~إضافة throttling أشد لمسارات المصادقة~~ ✅ `throttle:20,1` على `/auth/*`
4. ~~تحديد حد أقصى لـ `limit` و `offset`~~ ✅ `Helpers::capApiLimit()` و `capApiOffset()` (حد أقصى 50)
5. ~~إصلاح مخاطر IDOR لـ guest-id~~ ✅ التحقق من وجود الضيف ومطابقة IP (`GUEST_VALIDATE_IP`)

### أولوية منخفضة (متبقية)

6. إصلاح N+1 في `getCategories`  
7. إضافة TTL للـ Cache بدلاً من `rememberForever`  
8. استخدام Bearer token لمندوبي التوصيل  

---

## 6. ملخص الملفات

| الملف | المسارات | الحالة |
|------|----------|--------|
| `routes/api/v1/api.php` | 50+ | ✅ |
| `routes/api/v2/api.php` | 1 | 🔴 ثغرة |
| `ConfigController` | 3 | ✅ |
| `ProductController` | 10 | ⚠️ |
| `OrderController` | 8 | ⚠️ guest_id |
| `CustomerAuthController` | 12 | ⚠️ throttle |
| `DeliverymanController` | 12 | ⚠️ token |
| `CustomerController` | 8 | ⚠️ guest_id |
| `CategoryController` | 6 | ⚠️ N+1 |
| `LsLibController` | 1 | 🔴 ثغرة |

---

*نهاية التقرير*
