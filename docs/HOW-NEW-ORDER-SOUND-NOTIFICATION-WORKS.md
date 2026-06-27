# كيف تعمل إشعار "طلب جديد" مع الصوت في لوحة التحكم

## نظرة عامة

عند وصول طلب جديد، لوحة التحكم (Admin أو Branch) تتحقق دورياً من وجود طلبات غير مُراجعة. إذا وُجدت، يُشغَّل صوت وتظهر نافذة منبثقة (Popup) تقول "لديك طلب جديد، تحقق من فضلك".

---

## 1. مصدر البيانات: عمود `checked` في جدول الطلبات

- في جدول **`orders`** يوجد عمود **`checked`** (قيمة 0 أو 1).
- **طلب جديد غير مُراجع:** `checked = 0`
- بعد أن يطّلع المسؤول على الطلبات أو يضغط "تجاهل"، يتم تحديث الطلبات إلى `checked = 1`

عند إنشاء الطلب (من API أو POS) يُحفظ الطلب عادةً بـ `checked = 0` (أو القيمة الافتراضية في الـ migration).

---

## 2. الـ API الذي يُرجع "هل هناك طلب جديد؟"

### لوحة التحكم الرئيسية (Admin)

- **Route:** `GET /admin/get-restaurant-data`
- **الاسم:** `admin.get-restaurant-data`
- **Controller:** `App\Http\Controllers\Admin\SystemController::restaurantData()`

**الكود (ملخص):**

```php
$newOrder = DB::table('orders')->where(['checked' => 0])->count();
$pendingTypeApproval = User::whereNotNull('requested_user_type_id')->count();
return response()->json([
    'success' => 1,
    'data' => [
        'new_order' => $newOrder,
        'pending_type_approval' => $pendingTypeApproval,
    ]
]);
```

- إذا **`data.new_order > 0`** → يُعتبر أن هناك طلب جديد (يُشغَّل الصوت ويُعرض الـ popup).
- **`pending_type_approval`** يُستخدم لإظهار popup آخر خاص بطلبات الموافقة على نوع المستخدم.

### لوحة الفرع (Branch)

- **Route:** `GET /branch/get-restaurant-data`
- **الاسم:** `branch.get-restaurant-data`
- **Controller:** `App\Http\Controllers\Branch\SystemController::restaurantData()`

نفس الفكرة لكن يعد الطلبات للفرع الحالي فقط:

```php
$new_order = DB::table('orders')->where(['branch_id' => auth('branch')->id(), 'checked' => 0])->count();
return response()->json([ 'data' => ['new_order' => $new_order] ]);
```

---

## 3. الواجهة الأمامية (Frontend): الاستعلام الدوري + الصوت + الـ Popup

### الملف

- **Admin:** `resources/views/layouts/admin/app.blade.php`
- **Branch:** `resources/views/layouts/branch/app.blade.php`

### مكوّنات التنفيذ

#### أ) عنصر الصوت (Audio)

```html
<audio id="myAudio">
    <source src="{{ asset('assets/admin/sound/notification.mp3') }}" type="audio/mpeg">
</audio>
```

- الملف المتوقَّع: **`public/assets/admin/sound/notification.mp3`**

#### ب) دوال تشغيل/إيقاف الصوت (JavaScript)

```javascript
let audio = document.getElementById("myAudio");

function playAudio() {
    audio.play();
}

function pauseAudio() {
    audio.pause();
}
```

#### ج) الاستعلام الدوري (Polling) كل 10 ثوانٍ

```javascript
setInterval(function () {
    $.get({
        url: '{{ route('admin.get-restaurant-data') }}',  // أو branch.get-restaurant-data للفرع
        dataType: 'json',
        success: function (response) {
            let data = response.data;
            if (data.new_order > 0) {
                playAudio();
                $('#popup-modal').appendTo("body").modal('show');
            } else if (data.pending_type_approval > 0) {   // في Admin فقط
                playAudio();
                $('#popup-modal-type-approval').appendTo("body").modal('show');
            }
        },
    });
}, 10000);  // كل 10 ثوانٍ (10000 ميلي ثانية)
```

- كل **10 ثوانٍ** يتم طلب `get-restaurant-data`.
- إذا **`new_order > 0`**: تشغيل الصوت + إظهار الـ modal الذي يظهر فيه نص "لديك طلب جديد، تحقق من فضلك".

#### د) الـ Modal (نافذة منبثقة)

- **id الـ modal:** `popup-modal`
- النص المعروض: **"You have new order, Check Please."** (وترجمته: "لديك طلب جديد، تحقق من فضلك.")
- أزرار:
  - **"Ignore for now" (تجاهل الآن):** يوجّه إلى `admin.ignore-check-order` أو `branch.ignore-check-order`
  - **"Ok, let me check" (تحقق):** يوجّه إلى صفحة قائمة الطلبات

---

## 4. تحديث "تمت المراجعة" (إزالة إشعار الطلب الجديد)

### زر "تجاهل الآن" (Ignore for now)

- **Route (Admin):** `GET /admin/ignore-check-order` → `admin.ignore-check-order`
- **Route (Branch):** `GET /branch/ignore-check-order` → `branch.ignore-check-order`
- **Controller (Admin):** `App\Http\Controllers\Admin\SystemController::ignoreCheckOrder()`

**الكود:**

```php
public function ignoreCheckOrder()
{
    $this->order->where(['checked' => 0])->update(['checked' => 1]);
    return redirect()->back();
}
```

- يضع **جميع** الطلبات ذات `checked = 0` إلى `checked = 1`، فيتوقف إشعار "طلب جديد" حتى يأتي طلب جديد آخر.

كما يتم تحديث `checked` إلى 1 عند دخول صفحة قائمة الطلبات (مثلاً في `OrderController::list()`).

---

## 5. ملخص تدفق العمل (Flow)

1. **عميل يضع طلباً** → يُدرج في `orders` مع `checked = 0` (أو الافتراضي).
2. **لوحة التحكم مفتوحة** → كل 10 ثوانٍ يرسل المتصفح طلباً إلى `get-restaurant-data`.
3. **الـ Backend** يحسب عدد الطلبات التي `checked = 0` ويرجعها في `data.new_order`.
4. إذا **`new_order > 0`**:
   - المتصفح يشغّل **notification.mp3**.
   - يظهر **modal** "لديك طلب جديد، تحقق من فضلك".
5. عند النقر **"تجاهل"** أو فتح قائمة الطلبات → تحديث الطلبات إلى `checked = 1` → في الدورة التالية لا يُعاد إشعار الطلب نفسه.

---

## 6. استخدام نفس الآلية لميزة أخرى (مثل "تواصل معنا")

لإشعار بصوت عند وصول **رسالة تواصل معنا** جديدة يمكن:

- إضافة في **`get-restaurant-data`** (أو endpoint مشابه) حقل مثل `new_contact_us` = عدد رسائل تواصل معنا غير المقروءة.
- في **layout لوحة التحكم** (مثلاً `app.blade.php`): في دالة `success` للـ `$.get`، إذا `data.new_contact_us > 0` تشغيل نفس الصوت (أو صوت آخر) وإظهار modal مناسب لرسائل "تواصل معنا".
- استخدام عمود **`read_at`** في جدول `contact_us` لتمييز المقروء من غير المقروء (مثل استخدام `checked` في الطلبات).

---

## 7. الملفات والـ Routes المرجعية

| المكون | الملف أو الـ Route |
|--------|---------------------|
| عرض + صوت + polling (Admin) | `resources/views/layouts/admin/app.blade.php` |
| عرض + صوت + polling (Branch) | `resources/views/layouts/branch/app.blade.php` |
| إرجاع عدد الطلبات الجديدة (Admin) | `App\Http\Controllers\Admin\SystemController::restaurantData()` |
| إرجاع عدد الطلبات الجديدة (Branch) | `App\Http\Controllers\Branch\SystemController::restaurantData()` |
| Route بيانات الطلبات (Admin) | `admin.get-restaurant-data` في `routes/admin.php` |
| Route تجاهل الطلبات (Admin) | `admin.ignore-check-order` في `routes/admin.php` |
| ترجمة النص | `resources/lang/ar/messages.php` و `en/messages.php`: "You have new order, Check Please." |
| ملف الصوت | `public/assets/admin/sound/notification.mp3` |

---

*تم توثيق آلية إشعار "طلب جديد" مع الصوت لاستخدامها كمرجع أو لتطبيق نفس الأسلوب على ميزات أخرى مثل تواصل معنا.*
