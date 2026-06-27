# خطة برمجية تفصيلية — المرحلة 3 (وسوم، سجل الطلبات، منتجات ذات صلة)

> **الهدف:** تنفيذ كامل لوسوم المنتجات، سجل تغييرات الطلبات، ومنتجات ذات صلة — مع قاعدة بيانات، واجهات، واختبارات.

---

## 1. قاعدة البيانات (مكتمل ✅)

| الجدول | الأعمدة | الحالة |
|--------|---------|--------|
| `tags` | id, name, slug, sort_order, timestamps | ✅ |
| `product_tag` | id, product_id, tag_id, timestamps | ✅ |
| `order_status_logs` | id, order_id, old_status, new_status, changed_by_type, changed_by_id, note, timestamps | ✅ |
| `product_relations` | id, product_id, related_product_id, sort_order, timestamps | ✅ |

---

## 2. Models (النماذج)

| الملف | العلاقات | الوصف |
|-------|----------|-------|
| `app/Models/Tag.php` | products() | BelongsToMany Product |
| `app/Models/OrderStatusLog.php` | order(), changedBy() | BelongsTo Order, morphTo changedBy |
| `app/Models/ProductRelation.php` | product(), relatedProduct() | BelongsTo Product |
| `app/Models/Product.php` | tags(), relatedProducts(), productRelations() | إضافة |
| `app/Models/Order.php` | statusLogs() | إضافة |

---

## 3. Controllers (المتحكمات)

### 3.1 TagController (Admin)

| الـ Action | Method | Route | الوصف |
|------------|--------|-------|-------|
| index | GET | admin.tag.list | قائمة الوسوم |
| store | POST | admin.tag.store | إضافة وسم |
| update | POST | admin.tag.update/{id} | تعديل وسم |
| delete | DELETE | admin.tag.delete/{id} | حذف وسم |
| search | GET | admin.tag.search | بحث للـ autocomplete |

### 3.2 ProductController (تعديل)

| التعديل | الحقل | الوصف |
|---------|-------|-------|
| store() | tag_ids[] | إرفاق وسوم عند الحفظ |
| update() | tag_ids[] | تحديث الوسوم |
| store() | related_product_ids[] | ربط منتجات ذات صلة |
| update() | related_product_ids[] | تحديث المنتجات ذات الصلة |
| list() | tag_id filter | فلتر حسب الوسم |

### 3.3 OrderController (Admin + Branch)

| التعديل | الموقع | الوصف |
|---------|--------|-------|
| status() | بعد حفظ order_status | استدعاء OrderStatusLogService::log() |

---

## 4. الواجهات (Views)

### 4.1 الوسوم

| الملف | الوصف |
|-------|-------|
| `admin-views/tag/list.blade.php` | قائمة الوسوم مع إضافة/تعديل/حذف |
| `admin-views/tag/partials/_form.blade.php` | نموذج الوسم |
| `admin-views/product/partials/_tags-input.blade.php` | حقل اختيار الوسوم في المنتج |
| `admin-views/product/partials/_related-products.blade.php` | اختيار منتجات ذات صلة |

### 4.2 المنتجات

| التعديل | الملف | الوصف |
|---------|-------|-------|
| إضافة | index.blade.php | قسم الوسوم (tags-input) |
| إضافة | edit.blade.php | قسم الوسوم + منتجات ذات صلة |
| إضافة | list.blade.php | عمود الوسوم + فلتر tag_id |

### 4.3 الطلبات

| التعديل | الملف | الوصف |
|---------|-------|-------|
| إضافة | order-view.blade.php (admin + branch) | قسم "سجل التغييرات" مع جدول |

---

## 5. الخدمات (Services)

| الملف | الوظيفة |
|-------|---------|
| `app/Services/OrderStatusLogService.php` | logOrderStatusChange($order, $oldStatus, $newStatus, $note = null) |

---

## 6. API

| Endpoint | Method | الوصف |
|----------|--------|-------|
| `api/v1/products/{id}` | GET | إضافة tags في الاستجابة |
| `api/v1/products/{id}` | GET | إضافة related_products في الاستجابة |
| `api/v1/products` | GET | فلتر tag_id |
| `api/v1/tags` | GET | قائمة الوسوم (للاختيار) |

---

## 7. الاختبارات (Tests)

| الملف | الاختبارات |
|-------|------------|
| `tests/Feature/TagTest.php` | CRUD، البحث، الحذف |
| `tests/Feature/ProductTagsTest.php` | إرفاق وسوم، فلتر |
| `tests/Feature/OrderStatusLogTest.php` | تسجيل التغيير عند Admin و Branch |
| `tests/Feature/ProductRelationsTest.php` | ربط منتجات ذات صلة |
| `tests/Unit/OrderStatusLogServiceTest.php` | منطق الخدمة |

---

## 8. الترجمات

| المفتاح | ar | en |
|---------|----|----|
| product_tags | وسوم المنتجات | Product Tags |
| product_tags_hint | اختر الوسوم للتصنيف والبحث | Select tags for categorization and search |
| related_products | منتجات ذات صلة | Related Products |
| order_status_log | سجل تغييرات الطلب | Order Status Log |
| changed_by | غيّر بواسطة | Changed by |
| changed_at | تاريخ التغيير | Changed at |

---

## 9. ترتيب التنفيذ

1. ✅ قاعدة البيانات
2. ✅ Models + العلاقات
3. ✅ OrderStatusLogService
4. ✅ دمج سجل الطلبات في OrderController (Admin + Branch)
5. ✅ TagController + واجهات الوسوم
6. ✅ دمج الوسوم في Product add/edit
7. ✅ منتجات ذات صلة (واجهة + منطق)
8. ⏸ API (مؤجل)
9. ✅ الاختبارات (Feature + Unit)
