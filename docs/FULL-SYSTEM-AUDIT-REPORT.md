# تقرير تدقيق النظام الكامل — كود غير مستخدم، فرع واحد، نقاط البيع، رجال التوصيل

> **تاريخ التقرير:** 2026  
> **الهدف:** توثيق كل الميزات غير المستخدمة أو القابلة للاستغلال، وخطة التحويل إلى فرع واحد فقط.

---

## 1. ملخص تنفيذي

| العنصر | الحالة | التوصية |
|-------|--------|---------|
| **LsLibController** | كود ثغرة أمنية (RCE) | **حذف نهائي** |
| **نقاط البيع (POS)** | مدمجة في الطلبات، لا صفحة مستقلة | مراجعة أو إضافة صفحة POS |
| **رجال التوصيل** | ميزة كاملة (API + لوحة) | إبقاء أو إخفاء حسب الحاجة |
| **الفروع** | مخفية، وضع فرع واحد مفعّل | إنشاء صفحة فرع واحد للتعديل |
| **API V2** | فارغ تقريباً | حذف أو توثيق |

---

## 2. الكود غير المستخدم أو القابل للاستغلال

### 2.1 LsLibController — ثغرة أمنية (يُفضّل حذفه)

| المعلومة | التفاصيل |
|----------|----------|
| **الملف** | `app/Http/Controllers/Api/V2/LsLibController.php` |
| **الوظيفة** | يسمح بكتابة ملفات عشوائية على السيرفر عبر `POST` |
| **المسار** | كان `POST /api/v2/ls-lib-update` — **معطّل حالياً** |
| **الخطر** | إذا أُعيد تفعيل المسار بالخطأ، يصبح النظام عرضة لـ RCE |
| **التوصية** | **حذف الملف نهائياً** |

```php
// الكود الحالي (خطير)
$lib = base_path($request['dir']);
$file = fopen($lib,"w");
fwrite($file,$request['script']);
```

---

### 2.2 نقاط البيع (POS)

| العنصر | الوصف |
|--------|-------|
| **الوضع الحالي** | لا يوجد `POSController` منفصل. الوظائف مدمجة في `OrderController` |
| **المسارات** | `add-to-cart`, `pos-invoice`, `variant_price`, `quick-view`, إلخ — داخل `admin/orders` و `branch/orders` |
| **التوثيق** | وثائق كثيرة (`docs/POS-*.md`) تشير إلى صفحة `GET /admin/pos` غير موجودة |
| **الخلاصة** | صفحة POS مستقلة غير مُنفّذة. الطلبات من نوع `pos` و `pos_delivery` تُنشأ عبر قائمة الطلبات أو واجهة مدمجة |

**الملفات ذات الصلة:**
- `app/Http/Controllers/Admin/OrderController.php` — addToCart, generatePosInvoice
- `app/Http/Controllers/Branch/OrderController.php` — نفس الوظائف
- `docs/POS-DATA-FLOW-AND-ANALYSIS.md`, `docs/PLAN-POS-DELIVERY-*.md`

---

### 2.3 رجال التوصيل (Delivery Man)

| العنصر | الوصف |
|--------|-------|
| **النموذج** | `App\Models\DeliveryMan` — مرتبط بـ `branch_id` |
| **لوحة التحكم** | `DeliveryManController` + Views (list, add, edit, pending, denied, reviews) |
| **المسارات** | **غير مسجّلة في `routes/admin.php`** — كانت تأتي من Modules (إضافات) |
| **API** | مسجّل في `routes/api/v1/api.php` — تسجيل، دخول، طلبات، تقييمات |
| **الاستخدام** | تعيين مندوب للطلب من `order-view` (Admin + Branch) |

**ملاحظة:** مسار `admin.orders.add-delivery-man` **موجود** في admin. لكن مسارات إدارة المندوبين (list, add, edit, delete) **غير مسجّلة** لأنها كانت من إضافة (Module) غير موجودة. النتيجة: واجهات المندوبين قد لا تعمل أو تُعرض من القائمة الجانبية.

---

### 2.4 الفروع (Branches)

| العنصر | الوصف |
|--------|-------|
| **النموذج** | `App\Models\Branch` — جدول `branches` |
| **المسارات** | `admin/branch/*` — **مخفية** عند `hide_branch_management = true` |
| **وضع الفرع الواحد** | `single_branch_mode = true`, `hide_branch_management = true` |
| **الفرع الافتراضي** | `DEFAULT_BRANCH_ID = 1` في `config/constant.php` |
| **واجهة الفرع** | `branch/*` — لوحة تحكم منفصلة للفرع (تسجيل دخول بـ Branch) |

---

## 3. هيكل الفروع — ما يجب تعديله لفرع واحد فقط

### 3.1 الجداول والعلاقات

| الجدول/النموذج | العلاقة مع الفرع |
|-----------------|------------------|
| `branches` | جدول الفروع |
| `orders` | `branch_id` |
| `delivery_men` | `branch_id` |
| `areas` | `branch_id` |
| `delivery_charge_setups` | `branch_id` |
| `delivery_charge_by_areas` | `branch_id` |
| `order_areas` | `branch_id` |

### 3.2 الملفات التي تستخدم `branch_id`

| الملف | الاستخدام |
|-------|-----------|
| `app/Models/Order.php` | علاقة `branch()` |
| `app/Models/DeliveryMan.php` | علاقة `branch()` |
| `app/Models/Branch.php` | النموذج الرئيسي |
| `app/Models/Area.php` | `branch_id` |
| `app/Http/Controllers/Admin/OrderController.php` | فلتر deliverymen، branchFilter |
| `app/Http/Controllers/Branch/OrderController.php` | كل الاستعلامات بـ `branch_id = auth('branch')->id()` |
| `app/Http/Controllers/Api/V1/OrderController.php` | `branch_id` من الطلب أو الافتراضي |
| `app/Http/Controllers/Api/V1/ConfigController.php` | `getDefaultBranchId()` |
| `app/Http/Controllers/Admin/AreaController.php` | `getDefaultBranchId()` |
| `app/Http/Controllers/Admin/DeliveryManController.php` | `getDefaultBranchId()` |
| `app/Http/Controllers/Admin/ReportController.php` | `getDefaultBranchId()` |
| `app/CentralLogics/Helpers.php` | `getDefaultBranchId()` |
| `resources/views/layouts/branch/partials/_sidebar.blade.php` | `auth('branch')->id()` |
| `resources/views/branch-views/order/order-view.blade.php` | DeliveryMan حسب الفرع |
| `resources/views/admin-views/order/partials/invoice-print.blade.php` | عنوان الفرع |
| `resources/views/branch-views/order/partials/invoice-print.blade.php` | عنوان الفرع |

### 3.3 المسارات والواجهات

| المسار | الوصف | الحالة |
|--------|-------|--------|
| `admin/branch/list` | قائمة الفروع | مخفي (redirect → dashboard) |
| `admin/branch/add` | إضافة فرع | مخفي |
| `admin/branch/edit/{id}` | تعديل فرع | مخفي |
| `branch/auth/login` | تسجيل دخول الفرع | يعمل |
| `branch/` | لوحة تحكم الفرع | يعمل |

---

## 4. خطة التحويل إلى فرع واحد فقط

### 4.1 الهدف

- **صفحة واحدة** لإدارة الفرع الوحيد (تعديل الاسم، العنوان، الهاتف، إلخ)
- **عدم حذف** جدول الفروع أو العلاقات — فقط تبسيط الواجهة
- **تخزين فرع واحد** (id=1) واستخدامه في كل الاستعلامات

### 4.2 التعديلات المطلوبة

#### أ) إنشاء صفحة "إعدادات الفرع" (بديل لقائمة الفروع)

| الإجراء | التفاصيل |
|---------|----------|
| مسار جديد | `admin/branch/settings` أو `admin/settings/branch` |
| الوظيفة | عرض وتعديل بيانات الفرع id=1 فقط |
| إخفاء | إزالة أي رابط لـ "قائمة الفروع" أو "إضافة فرع" |

#### ب) تعديل الشريط الجانبي (Admin)

| الحالي | المقترح |
|--------|---------|
| لا يوجد رابط للفروع (مخفي) | إضافة رابط "إعدادات الفرع" أو "الفرع" → صفحة التعديل |

#### ج) التأكد من استخدام الفرع الافتراضي

- كل الاستعلامات التي تحتاج `branch_id` يجب أن تستخدم `Helpers::getDefaultBranchId()` عند عدم التحديد
- التحقق من: API، التقارير، المناطق، رجال التوصيل

#### د) واجهة الفرع (Branch Panel)

- الإبقاء على `branch/auth/login` و `branch/*` للفرع الوحيد
- المستخدم يسجّل دخولاً بحساب الفرع id=1
- لا حاجة لاختيار فرع — الفرع معروف من الجلسة

### 4.3 الملفات التي تحتاج تعديلاً

| الملف | التعديل |
|-------|---------|
| `routes/admin.php` | إضافة route لصفحة إعدادات الفرع (بدون حذف المسارات الحالية) |
| `resources/views/layouts/admin/partials/_sidebar.blade.php` | إضافة رابط "الفرع" أو "إعدادات الفرع" |
| `app/Http/Controllers/Admin/BranchController.php` | إضافة method `settings()` و `settingsUpdate()` للفرع الوحيد |
| `resources/views/admin-views/branch/edit.blade.php` | استخدامه كصفحة الإعدادات (أو إنشاء `settings.blade.php`) |
| `config/feature_flags.php` | التأكد من `single_branch_mode` و `hide_branch_management` |

---

## 5. قائمة الملفات/الكود المقترح حذفه أو تنظيفه

| العنصر | الإجراء | الأولوية |
|--------|---------|----------|
| `app/Http/Controllers/Api/V2/LsLibController.php` | **حذف** | عالية |
| `routes/api/v2/api.php` | مراجعة — إن بقي فارغاً، دمج مع v1 أو حذف | متوسطة |
| وثائق POS التي تشير لـ `POSController` غير موجود | تحديث أو حذف | منخفضة |
| مسارات Install/Update المعطّلة في RouteServiceProvider | توثيق أو إزالة | منخفضة |

---

## 6. ملخص التوصيات

1. **حذف LsLibController** فوراً — ثغرة أمنية.
2. **إنشاء صفحة إعدادات الفرع** في لوحة التحكم — تعديل الفرع الوحيد فقط.
3. **إبقاء واجهة الفرع** (`branch/*`) — تعمل مع الفرع الوحيد.
4. **مراجعة مسارات رجال التوصيل** — إما تسجيلها في `admin.php` أو إخفاء القائمة إن لم تُستخدم.
5. **عدم حذف** جداول أو علاقات الفروع — فقط تبسيط الواجهة.

---

## 7. ملحق — مسارات الفروع الكاملة

```
Admin:
  admin/branch/list      → مخفي (redirect)
  admin/branch/add       → مخفي
  admin/branch/edit/{id} → مخفي
  admin/branch/store     → مخفي
  admin/branch/update/{id} → مخفي
  admin/branch/delete/{id} → مخفي
  admin/branch/status/{id}/{status} → مخفي

Branch:
  branch/auth/login
  branch/auth/logout
  branch/ (dashboard)
  branch/settings
  branch/orders/list/{status}
  branch/orders/details/{id}
  ... (كل مسارات الطلبات)
```

---

*تم إعداد هذا التقرير بناءً على فحص المشروع. يُنصح بمراجعته قبل البدء بأي تنفيذ.*

---

## 8. تنفيذ 2026 — ما تم إنجازه

| المهمة | الحالة |
|--------|--------|
| حذف LsLibController | ✅ تم |
| صفحة إعدادات الفرع | ✅ تم — `admin/branch/settings` |
| رابط الفرع في الشريط الجانبي | ✅ تم |
| مسارات رجال التوصيل | ✅ تم — مسجّلة في admin.php |
| HelpController | ✅ تم — إنشاء الملف والصفحة |
| Migration SQLite (change_product_image) | ✅ تم — تخطي على SQLite |
| Migration create_products | ✅ تم — للاختبارات |
| اختبارات Unit | ✅ تعمل |
| اختبارات AdminOrdersListApiTest | ⚠️ تحتاج MySQL — الجداول من installation SQL |
