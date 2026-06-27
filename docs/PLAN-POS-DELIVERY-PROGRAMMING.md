# الخطة البرمجية: طلب التوصيل من نقطة البيع (POS)

**مرجع التحليل:** `PLAN-POS-DELIVERY-FULL-DATAFLOW.md`  
**الغرض:** تفصيل خطوات التنفيذ (ملفات، دوال، تعديلات) — **لا تنفيذ كود حتى الموافقة.**

---

## الافتراضات المعتمدة في الخطة

| القرار | الاختيار المعتمد في الخطة |
|--------|----------------------------|
| تمييز نوع الطلب | **`order_type = 'pos_delivery'`** لطلبات التوصيل من POS. طلبات الاستلام تبقى `order_type = 'pos'`. |
| رسوم التوصيل | حسب **المنطقة (Area)** فقط — لا تغيير لجدول المدن أو "حسب المدينة" في هذه المرحلة. |
| عميل عابر + توصيل | **منع** اختيار "توصيل" إلا بعد اختيار عميل مسجّل (لا عنوان يدوي للضيف في المرحلة الأولى). |

---

## المرحلة 1: النموذج والاستعلامات (Backend – Order)

### 1.1 نموذج الطلب والـ Scopes

| الملف | التعديل |
|-------|---------|
| `app/Models/Order.php` | (اختياري) إضافة scope مثل `scopeDeliveryWorkflow($query)` يرجع الطلبات التي تظهر في قائمة الطلبات العامة: `order_type != 'pos'` **أو** `order_type = 'pos_delivery'`. أو ترك المنطق في الـ Controller. |
| لا تغيير في الـ DB | جدول `orders` يحتوي مسبقاً على `order_type`, `delivery_address_id`, `delivery_address`, `delivery_charge`, `order_status`, `payment_status` — لا migration جديد. |

### 1.2 قائمة الطلبات الإدارية

| الملف | التعديل |
|-------|---------|
| `app/Http/Controllers/Admin/OrderController.php` | في الدالة التي تعيد قائمة الطلبات (مثلاً `list` أو المشابهة): استبدال شرط `notPos()` بشرط يجلب الطلبات حيث `(order_type != 'pos') OR (order_type = 'pos_delivery')`. التأكد من الحفاظ على الفلترة حسب الحالة والتاريخ والبحث. |

### 1.3 تفاصيل الطلب وتعيين المندوب

| الملف | التعديل |
|-------|---------|
| `app/Http/Controllers/Admin/OrderController.php` | في دالة تفاصيل الطلب: لا تغيير في جلب الطلب. |
| `resources/views/admin-views/order/order-view.blade.php` | تعديل الشروط التي تتحكم في إظهار **قائمة تغيير الحالة** و**تعيين مندوب التوصيل**: حالياً تظهر عندما `order_type != 'pos'`. تغييرها إلى: تظهر عندما `order_type != 'pos'` **أو** `order_type == 'pos_delivery'` (أي إظهارها أيضاً لطلب التوصيل من POS). مراجعة كل مكان يستخدم `order_type == 'pos'` أو `order_type != 'pos'` في نفس الملف (الدفع، الملاحظة، الفاتورة، الشحن...) وإضافة استثناء لـ `pos_delivery` حيث يلزم (مثلاً عدم إخفاء قسم العنوان أو رسوم التوصيل). |

### 1.4 الفاتورة

| الملف | التعديل |
|-------|---------|
| `resources/views/admin-views/order/invoice.blade.php` أو أي view خاص بفاتورة الطلب | التأكد أن طلبات `order_type = 'pos_delivery'` تعرض **عنوان التوصيل** و**رسوم التوصيل** مثل طلبات التطبيق (إن كان هناك شرط `order_type == 'pos'` يخفيها، تعديله لاستثناء `pos_delivery`). |
| `resources/views/admin-views/pos/order/invoice.blade.php` | إضافة شرط: إذا الطلب `order_type = 'pos_delivery'` عرض عنوان التوصيل ورسوم التوصيل؛ وإلا السلوك الحالي (طلب استلام). |

---

## المرحلة 2: نقطة البيع – إنشاء طلب التوصيل (Backend)

### 2.1 التحقق والحساب

| الملف | التعديل |
|-------|---------|
| `app/Http/Controllers/Admin/POSController.php` | **دالة `placeOrder`:** استقبال معاملات جديدة من الـ Request: مثلاً `order_delivery_type` (قيمة: `pickup` \| `delivery`)، `delivery_address_id`، `selected_delivery_area` (area id). إذا `order_delivery_type === 'delivery'`: (1) التحقق أن `customer_id` موجود وليس عميل عابر. (2) التحقق من وجود `delivery_address_id` و(إن لزم) `selected_delivery_area`. (3) جلب عنوان التوصيل من `CustomerAddress` وجلب رسوم التوصيل من `Helpers::get_delivery_charge(session('branch_id'), null, selected_delivery_area)`. (4) حساب الإجمالي الحالي من السلة (نفس المنطق الحالي: منتجات، كوبون، خصم إضافي، نقاط ولاء، ضريبة) ثم **إضافة** `delivery_charge` للإجمالي النهائي. (5) عند إنشاء سجل الطلب: إذا توصيل: `order_type = 'pos_delivery'`, `order_status = pending` أو `confirmed` حسب طريقة الدفع، `payment_status = unpaid` أو `paid`، `delivery_address_id`، `delivery_address` (كائن العنوان)، `delivery_charge`، `order_amount` يشمل رسوم التوصيل؛ وإدراج سجل في `order_areas` إن وُجدت. إذا استلام: الإبقاء على السلوك الحالي (`order_type = 'pos'`, `order_status = delivered`, إلخ). |

### 2.2 ربط الطلب بالمنطقة

| الملف | التعديل |
|-------|---------|
| `app/Http/Controllers/Admin/POSController.php` | داخل `placeOrder` عند نوع التوصيل: بعد إنشاء الطلب، إدراج سجل في جدول `order_areas` (order_id, branch_id, area_id) باستخدام `selected_delivery_area` كـ area_id. التأكد من وجود Model أو استخدام `DB::table('order_areas')` أو نموذج `OrderArea` إن وُجد. |

### 2.3 إشعار العميل (اختياري)

| الملف | التعديل |
|-------|---------|
| `app/Http/Controllers/Admin/POSController.php` | بعد حفظ طلب التوصيل: إن وُجدت آلية إشعار (FCM أو بريد) مشابهة لـ API place order، استدعاؤها للعميل (نفس رسالة "تم إنشاء الطلب" أو ما يعادلها). |

---

## المرحلة 3: واجهة POS – العرض والنماذج

### 3.1 اختيار نوع الطلب (استلام / توصيل)

| الملف | التعديل |
|-------|---------|
| `resources/views/admin-views/pos/index.blade.php` | في قسم الفوترة (قبل أو بعد اختيار العميل): إضافة عنصر واجهة لاختيار نوع الطلب: "استلام في المتجر" (افتراضي) و"توصيل". مثلاً: زران أو radio buttons بقيمة `pickup` و`delivery`. عند اختيار "توصيل" إظهار الـ block الخاص بالعنوان والمنطقة (انظر 3.2 و 3.3). |
| نفس الملف أو `resources/views/admin-views/pos/_cart.blade.php` | التأكد أن نموذج "إتمام الطلب" (`form#order_place` أو المشابه) يضم حقلاً مخفياً أو name لـ `order_delivery_type`، و`delivery_address_id`، و`selected_delivery_area` (يُملآن من واجهة العنوان/المنطقة). |

### 3.2 قسم عنوان التوصيل

| الملف | التعديل |
|-------|---------|
| `resources/views/admin-views/pos/index.blade.php` أو partial جديد | إضافة قسم (يُعرض فقط عند اختيار "توصيل" وعند وجود عميل مسجّل): قائمة منسدلة أو قائمة بعناوين العميل الحالي من `customer_addresses`. جلب العناوين: إما تمريرها من الـ Controller في `index` (عناوين عميل افتراضي فارغة) وتحديثها عبر AJAX عند تغيير العميل، أو استدعاء route جديد يرجع عناوين عميل معيّن. عند اختيار عنوان: تعبئة `delivery_address_id` و`selected_delivery_area` (من area_id الخاص بالعنوان) وإعادة حساب رسوم التوصيل وعرضها. |
| Route + Controller | إضافة route مثل `GET admin/pos/customer-addresses/{customerId}` أو `POST admin/pos/customer-addresses` يرجع عناوين عميل معيّن (JSON). التنفيذ في `POSController` أو Controller مساعد. |

### 3.3 قسم المنطقة ورسوم التوصيل

| الملف | التعديل |
|-------|---------|
| نفس الـ view أو partial | عند عدم وجود عنوان محدّد أو عند رغبة المستخدم بتغيير المنطقة: عرض قائمة منسدلة بالمناطق (من جدول `areas` للفرع الحالي). مصدر البيانات: إما من الـ Controller في تحميل الصفحة (قائمة مناطق الفرع) أو route مثل `GET admin/pos/areas` يرجع المناطق. عند اختيار منطقة: استدعاء route لحساب رسوم التوصيل (انظر 3.5) وعرض المبلغ في الواجهة. |
| Route + Controller | إضافة route مثل `GET admin/pos/delivery-charge` مع query params: `area_id` (أو selected_delivery_area). الاستجابة: `delivery_charge` من `Helpers::get_delivery_charge(branch_id, null, area_id)`. |

### 3.4 ملخص الفاتورة (السلة) ورسوم التوصيل

| الملف | التعديل |
|-------|---------|
| `resources/views/admin-views/pos/_cart.blade.php` | في قسم الملخص (قبل الإجمالي): عند كون الطلب من نوع "توصيل" إضافة صف: "رسوم التوصيل" وقيمة (يُمرّر من الـ session أو من متغير يُحدَّث عند اختيار المنطقة). الإجمالي النهائي = (المبلغ الحالي بعد خصم وكوبون ونقاط ولاء وضريبة) + رسوم التوصيل. يمكن أن تُحسب رسوم التوصيل في الـ Controller وتُخزّن في session مؤقتاً عند اختيار المنطقة، أو تُمرّر من الـ front عبر حقل في النموذج. |
| تحديث السلة عبر AJAX | إن كان جزء الملخص يُحدَّث عبر استدعاء `cart_items` أو ما شابه: توسيع الاستجابة لتضمين `delivery_charge` و`is_delivery_order` عندما يكون النوع توصيل، حتى يعكس الـ view المبلغ الصحيح دون إعادة تحميل الصفحة. |

### 3.5 Routes جديدة (POS)

| النوع | المسار | الاستخدام |
|-------|--------|-----------|
| GET | `admin/pos/areas` أو ضمن صفحة واحدة | جلب مناطق الفرع (للقائمة المنسدلة). |
| GET | `admin/pos/delivery-charge?area_id={id}` | جلب رسوم التوصيل لمنطقة معيّنة. |
| GET أو POST | `admin/pos/customer-addresses` أو `admin/pos/customer/{id}/addresses` | جلب عناوين عميل معيّن. |

---

## المرحلة 4: JavaScript وربط الواجهة

### 4.1 أحداث الواجهة

| الملف | التعديل |
|-------|---------|
| `resources/views/admin-views/pos/index.blade.php` (قسم `@push('script_2')` أو ملف JS منفصل) | (1) عند تغيير نوع الطلب إلى "توصيل": إظهار قسم العنوان والمنطقة؛ تعطيل زر "إتمام الطلب" حتى يتم اختيار عنوان (أو منطقة). (2) عند تغيير العميل: إذا "توصيل" — جلب عناوين العميل الجديد وتحديث القائمة؛ إذا عميل عابر — إخفاء قسم التوصيل أو تعطيل "توصيل" وإظهار رسالة. (3) عند اختيار عنوان: تعبئة area_id واستدعاء delivery-charge وعرض الرسوم وتحديث الإجمالي في الواجهة. (4) عند اختيار منطقة يدوياً: نفس الاستدعاء وتحديث الإجمالي. (5) عند الإرسال: التأكد أن حقول `order_delivery_type`, `delivery_address_id`, `selected_delivery_area` مضمنة في الـ form. |

### 4.2 تحديث الإجمالي

| الملف | التعديل |
|-------|---------|
| نفس الـ script | دالة تُحدَّث عند: تغيير السلة، تطبيق كوبون، تطبيق خصم، تطبيق ولاء، **تغيير رسوم التوصيل**. الإجمالي المعروض = مجموع السلة بعد الخصومات + الضريبة + رسوم التوصيل (إن كان توصيل). |

---

## المرحلة 5: قائمة طلبات POS والتمييز

| الملف | التعديل |
|-------|---------|
| `app/Http/Controllers/Admin/POSController.php` | في دالة `orderList` (قائمة طلبات POS): تغيير الاستعلام من `order_type = 'pos'` إلى `(order_type = 'pos' OR order_type = 'pos_delivery')` ليعرض طلبات الاستلام والتوصيل معاً، أو الإبقاء على `order_type = 'pos'` فقط وعرض طلبات التوصيل في القائمة العامة فقط — حسب قرار المنتج. |
| `resources/views/admin-views/pos/order/list.blade.php` | إضافة عمود أو badge يوضح "استلام" أو "توصيل" حسب `order_type` (pos vs pos_delivery). |

---

## المرحلة 6: الترجمات والتحقق

| الملف | التعديل |
|-------|---------|
| `resources/lang/ar/messages.php` و `resources/lang/en/messages.php` | إضافة مفاتيح مثل: "استلام في المتجر"، "توصيل"، "عنوان التوصيل"، "اختر المنطقة"، "رسوم التوصيل"، "يجب اختيار عميل مسجّل لطلب التوصيل"، "يجب اختيار عنوان توصيل". |
| `app/Http/Controllers/Admin/POSController.php` | في `placeOrder`: التحقق (validation) من أن نوع التوصيل يتطلب عميلاً وعنواناً ومنطقة؛ إرجاع رسالة خطأ واضحة عند النقص. |

---

## المرحلة 7: الفروع (Branch) إن وُجدت

| الملف | التعديل |
|-------|---------|
| `app/Http/Controllers/Admin/POSController.php` | عند جلب المناطق ورسوم التوصيل استخدام `session('branch_id')` أو الفرع الافتراضي (مثل Helpers::getDefaultBranchId()) بشكل متسق. |

---

## ترتيب التنفيذ المقترح (بدون تنفيذ فعلي)

1. **المرحلة 1:** تعديل قائمة الطلبات وتفاصيل الطلب والفاتورة (حتى يظهر طلب `pos_delivery` ويعامل معاملة طلب التطبيق).
2. **المرحلة 2:** تعديل `POSController::placeOrder` لدعم نوع التوصيل وإنشاء الطلب بـ `pos_delivery` وdelivery_address وdelivery_charge وorder_areas.
3. **المرحلة 3 و 3.5:** إضافة الـ routes والـ views (اختيار النوع، عنوان، منطقة، ملخص رسوم التوصيل).
4. **المرحلة 4:** ربط الواجهة بـ JavaScript (أحداث، تحديث إجمالي، إرسال الحقول الصحيحة).
5. **المرحلة 5 و 6 و 7:** قائمة طلبات POS، الترجمات، والتحقق، ومراعاة الفرع.

---

## ملخص الملفات المتأثرة

| الملف / المكون | نوع التعديل |
|----------------|-------------|
| `app/Http/Controllers/Admin/OrderController.php` | تعديل شرط القائمة (إظهار pos_delivery). |
| `resources/views/admin-views/order/order-view.blade.php` | إظهار الحالة والمندوب لـ pos_delivery؛ عرض العنوان ورسوم التوصيل. |
| `resources/views/admin-views/order/invoice.blade.php` أو ما يكافئ | عرض العنوان ورسوم التوصيل لـ pos_delivery. |
| `app/Http/Controllers/Admin/POSController.php` | placeOrder: استقبال نوع الطلب وعنوان ومنطقة؛ إنشاء طلب pos_delivery؛ routes جديدة: areas، delivery-charge، customer-addresses. |
| `resources/views/admin-views/pos/index.blade.php` | اختيار استلام/توصيل؛ قسم العنوان والمنطقة؛ تضمين الحقول في النموذج. |
| `resources/views/admin-views/pos/_cart.blade.php` | صف رسوم التوصيل في الملخص عند النوع توصيل. |
| `resources/views/admin-views/pos/order/invoice.blade.php` | دعم عرض التوصيل لـ pos_delivery. |
| `resources/views/admin-views/pos/order/list.blade.php` | تمييز استلام vs توصيل. |
| `routes/admin.php` | إضافة routes: pos/areas، pos/delivery-charge، pos/customer-addresses (أو customer/{id}/addresses). |
| `resources/lang/ar/messages.php`, `resources/lang/en/messages.php` | مفاتيح ترجمة جديدة. |
| (اختياري) `app/Models/Order.php` | scope DeliveryWorkflow إن رُغب بمركزية الشرط. |

---

*هذه خطة برمجية للتنفيذ لاحقاً بعد الموافقة. لا تنفيذ كود حتى تُعطى الموافقة صراحة.*
