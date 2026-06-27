# خطة علاجية — فحص API لوحة الإدارة + تحميل الصور في المتجر

> **التاريخ:** 2026  
> **المصادر:** ADMIN-PANEL-API-AUDIT.md، تحليل صفحات المتجر (wecommerce)

---

## 1. ملخص تنفيذي

| المجال | الأولوية | المهام |
|--------|----------|--------|
| **لوحة الإدارة — API** | عالية | كاش get-store-data، دمج order_stats |
| **لوحة الإدارة — أمان** | متوسطة | throttle على unified-search |
| **المتجر — تحميل الصور** | عالية | حجم الصور، lazy loading، memCache |
| **المتجر — Backend** | متوسطة | صور مصغرة / resize اختياري |

---

## 2. لوحة الإدارة — API

### 2.1 كاش get-store-data (أولوية عالية)

**المشكلة:** ثلاثة استعلامات تُنفَّذ كل 10 ثوانٍ لكل مشرف متصل.

**الحل:**
```php
// SystemController::storeData()
$cacheKey = 'admin_store_data_' . auth('admin')->id();
return Cache::remember($cacheKey, 10, function () {
    return [
        'new_order' => DB::table('orders')->where(['checked' => 0])->count(),
        'pending_type_approval' => User::whereNotNull('requested_user_type_id')->count(),
        'new_contact_us' => ContactUs::unread()->count(),
    ];
});
```

**ملاحظة:** عند تحديث `checked` أو `read_at` يجب إبطال الكاش (أو استخدام مفتاح عام بدون admin_id).

**الملف:** `app/Http/Controllers/Admin/SystemController.php`

---

### 2.2 دمج order_stats_data (أولوية عالية)

**المشكلة:** 9 استعلامات COUNT منفصلة.

**الحل:** استعلام واحد باستخدام `selectRaw` و `CASE WHEN`:
```php
$counts = Order::selectRaw("
    SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN order_status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
    ...
")->when($today, ...)->when($this_month, ...)->first();
```

**الملف:** `app/Http/Controllers/Admin/SystemController.php`

---

### 2.3 throttle على unified-search (أولوية متوسطة)

**المشكلة:** بحث مفتوح بدون حد — إساءة استخدام محتملة.

**الحل:** إضافة `throttle:30,1` على مسار unified-search.

**الملف:** `routes/admin.php`

---

### 2.4 كاش Dashboard (أولوية متوسطة)

**المشكلة:** `topSell`, `mostRatedProducts`, `topCustomer` — استعلامات ثقيلة عند كل تحميل.

**الحل:** كاش 5 دقائق لهذه البيانات.

**الملف:** `app/Http/Controllers/Admin/SystemController.php`

---

## 3. المتجر — تحميل الصور (تحليل)

### 3.1 الوضع الحالي

| المكون | الوضع | التأثير |
|--------|--------|---------|
| **CustomImageWidget** | CachedNetworkImage + memCacheWidth/Height | ✅ كاش قرص وذاكرة — لكن عند عدم تمرير width/height يكون decode بالحجم الكامل |
| **ProductCardWidget** | لا يمرّر width/height لـ CustomImageWidget | ❌ الصور تُحمّل وتُفكّ بأحجامها الأصلية |
| **StoreScreen** | GridView/MasonryGridView مع shrinkWrap + NeverScrollableScrollPhysics | ❌ جميع المنتجات تُبنى دفعة واحدة — لا lazy loading |
| **Backend** | صور كاملة الحجم فقط | ❌ لا thumbnails ولا resize |
| **Web** | image-proxy يمرّر الطلب عبر السيرفر | ⚠️ تأخير إضافي، لا تحسين حجم |

### 3.2 نقاط الضعف الرئيسية

1. **جميع الصور تُحمّل دفعة واحدة** — عند فتح صفحة المتجر بفلتر (مثلاً 50 منتج) تُحمّل 50 صورة كاملة الحجم.
2. **لا تحجيم في الذاكرة** — بدون `memCacheWidth`/`memCacheHeight` تُفكّ الصور بالحجم الأصلي (مثلاً 800×800) حتى لو كانت معروضة في بطاقة 150×150.
3. **لا صور مصغرة من الخادم** — لا يوجد endpoint مثل `?w=200&h=200` لتقليل حجم النقل.

---

## 4. خطة علاج تحميل الصور

### 4.1 تمرير أبعاد الصورة في ProductCardWidget (أولوية عالية)

**المشكلة:** `CustomImageWidget` بدون width/height → لا يُستخدم memCacheWidth/Height.

**الحل:** حساب حجم الصورة من الـ aspect ratio (1.1) وعرض البطاقة:
```dart
// في _VerticalCard — AspectRatio 1.1
final width = constraints.maxWidth;
final height = width / 1.1;
CustomImageWidget(
  image: url,
  width: width,
  height: height,
  fit: BoxFit.cover,
);
```

**الملف:** `wecommerce/lib/common/widgets/product_card_widget.dart`

---

### 4.2 Lazy loading في صفحة المتجر (أولوية عالية)

**المشكلة:** `shrinkWrap: true` + `NeverScrollableScrollPhysics` يبني كل العناصر دفعة واحدة.

**الحل:**
- استبدال `GridView.builder` بـ `SliverGrid` أو `SliverMasonryGrid` داخل `CustomScrollView` (الذي يستخدم `_scrollController` بالفعل).
- إزالة `shrinkWrap` و `NeverScrollableScrollPhysics` للسماح بالتمرير والـ lazy loading.
- التأكد أن `CustomScrollView` يتحكم في التمرير — حاليًا الـ scroll موجود على `CustomScrollView`، لكن الـ GridView الداخلي له `NeverScrollableScrollPhysics` مما يجعل كل العناصر تُبنى.

**البديل الأبسط:** استخدام `SliverGrid` أو `SliverMasonryGrid` داخل `CustomScrollView` بدلاً من `GridView` داخل `SliverToBoxAdapter` — بحيث تُبنى فقط العناصر المرئية.

**الملف:** `wecommerce/lib/features/store/screens/store_screen.dart`

---

### 4.3 صور مصغرة من الخادم (أولوية متوسطة)

**المشكلة:** الصور تُحمّل بالحجم الكامل (مثلاً 800×800 بكسل).

**الحلول:**

1. **خيار أ — إضافة صور مصغرة عند الرفع:**  
   عند رفع صورة منتج، إنشاء نسخة مصغرة (مثلاً 300×300) وحفظها. الـ API يُرجع `thumbnail_url` أو `image_thumb` للقوائم.

2. **خيار ب — endpoint resize ديناميكي:**  
   إضافة route مثل `/storage/product/thumb/{w}x{h}/{filename}` يستخدم Intervention Image أو غيره لإنشاء thumbnail عند الطلب (مع كاش).

3. **خيار ج — CDN/Imgproxy:**  
   استخدام خدمة خارجية لتحويل الصور حسب الحجم.

**التوصية:** البدء بخيار أ (أبسط وأقل تكلفة على الخادم).

---

### 4.4 تحسين Web (image-proxy)

**المشكلة:** على الويب، كل صورة تمر عبر `/image-proxy?url=...` — طلب إضافي للسيرفر.

**الحل:**  
- إن كان `productImageUrl` من نفس النطاق، تجنب image-proxy عند عدم الحاجة لـ CORS.
- أو إضافة دعم للـ resize في image-proxy: `?url=...&w=200&h=200` لتقليل حجم النقل.

---

## 5. جدول المهام (حسب الأولوية)

| # | المهمة | الموقع | الأولوية | الجهد |
|---|--------|--------|----------|-------|
| 1 | كاش get-store-data | SystemController | عالية | 1 |
| 2 | تمرير width/height لصور المنتجات | ProductCardWidget | عالية | 1 |
| 3 | Lazy loading في StoreScreen | store_screen.dart | عالية | 2 |
| 4 | دمج order_stats_data | SystemController | عالية | 2 |
| 5 | throttle unified-search | admin.php | متوسطة | 0.5 |
| 6 | كاش Dashboard | SystemController | متوسطة | 1 |
| 7 | صور مصغرة عند الرفع | ProductController + migration | متوسطة | 3 |
| 8 | تحسين image-proxy (اختياري) | web.php | منخفضة | 2 |

---

## 6. ترتيب التنفيذ المقترح

1. **المرحلة 1 (سريعة):**  
   - كاش get-store-data  
   - تمرير width/height في ProductCardWidget  
   - throttle unified-search  

2. **المرحلة 2 (متوسطة):**  
   - Lazy loading في StoreScreen  
   - دمج order_stats_data  
   - كاش Dashboard  

3. **المرحلة 3 (أطول):**  
   - صور مصغرة عند الرفع  
   - تحسين image-proxy  

---

## 7. مراجع

- `ADMIN-PANEL-API-AUDIT.md` — فحص API لوحة الإدارة
- `AUDIT-QUALITY-SECURITY-PERFORMANCE.md` — فحص عام
- `DEPLOYMENT-REDIS-CACHE.md` — Redis للكاش في الإنتاج
