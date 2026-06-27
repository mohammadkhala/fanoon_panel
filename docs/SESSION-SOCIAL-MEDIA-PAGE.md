# توثيق جلسة تطوير صفحة وسائل التواصل الاجتماعي

**المسار:** `/admin/business-settings/social-media`  
**الملف:** `resources/views/admin-views/business-settings/social-media.blade.php`  
**التاريخ:** نهاية الجلسة

---

## ملخص التعديلات

تم تحديث صفحة إعدادات وسائل التواصل الاجتماعي لتتوافق مع الهوية البصرية للمشروع وتحسين تجربة المستخدم.

---

## 1. تحديث الهوية البصرية

### الترويسات (Headers)
- إضافة كلاس `social-media-card-header` للبطاقات
- خط أحمر سفلي بعرض 2px باستخدام `var(--primary-clr, #EC2227)`
- حجم خط العناوين: `1.15rem`

### شارة العدد (Badge)
- كلاس `badge-social-count` لعرض عدد وسائل التواصل
- لون الخلفية: الأحمر الأساسي للمشروع
- حجم الخط: `1rem` مع وزن `600`

---

## 2. تحسين النموذج (Form)

### الحقول
- استخدام `form-control-lg` لجميع حقول الإدخال
- توحيد حجم حقل الاختيار (select) وحقل الرابط (input):
  - **العرض:** `col-md-5` لكلا الحقلين (كان select بـ col-md-4 و link بـ col-md-5)
  - **الارتفاع:** `min-height: 53px` لضمان نفس الارتفاع
- تنسيق التركيز: حدود حمراء وظل عند التركيز على الحقول

### تخطيط الصف
| العنصر | العمود | الوصف |
|--------|--------|-------|
| حقل الاختيار (المنصة) | col-md-5 | Instagram, Facebook, Twitter... |
| حقل الرابط | col-md-5 | https://... |
| أزرار الحفظ/التحديث | col-md-2 | زر إضافة + زر تحديث |

---

## 3. تحسين الجدول (Table)

- إضافة `table-hover` لتأثير التمرير
- روابط قابلة للنقر تفتح في نافذة جديدة
- خلية الرابط `link-cell`: قص النص الطويل مع ellipsis
- رأس الجدول: خلفية رمادية فاتحة ووزن خط 600

---

## 4. حالة فارغة (Empty State)

- عرض رسالة "لم تتم إضافة وسائل تواصل بعد" عند عدم وجود بيانات
- أيقونة `tio-social-media` بحجم كبير
- ترجمة عربية: `No social media added yet`

---

## 5. مفتاح التفعيل/الإيقاف (Status Toggle)

- إصلاح مفتاح تبديل الحالة `status-toggle`
- يعمل مع AJAX لتحديث الحالة دون إعادة تحميل الصفحة

---

## 6. CSS المستخدم

```css
.social-media-card-header { border-bottom: 2px solid var(--primary-clr, #EC2227); }
.social-media-card-header h6 { font-size: 1.15rem !important; }
.badge-social-count { font-size: 1rem !important; font-weight: 600; padding: 0.4rem 0.75rem; background-color: var(--primary-clr, #EC2227) !important; color: #fff !important; }
.social-form-row .form-control { font-size: 1rem; min-height: 53px; }
.social-form-row .form-control:focus { border-color: var(--primary-clr, #EC2227); box-shadow: 0 0 0 0.2rem rgba(236, 34, 39, 0.15); }
.social-table thead th { font-weight: 600; background-color: #f8f9fa; }
.social-table .link-cell { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
```

---

## 7. المسارات والـ Routes المستخدمة

| الوظيفة | Route |
|---------|-------|
| جلب البيانات | `admin.business-settings.fetch` |
| إضافة | `admin.business-settings.social-media-store` |
| تحديث | `admin.business-settings.social-media-update` |
| حذف | `admin.business-settings.social-media-delete` |
| تعديل (تحميل) | `admin.business-settings.social-media-edit` |
| تحديث الحالة | `admin.business-settings.social-media-status-update` |

---

## 8. المنصات المدعومة

Instagram, Facebook, Twitter/X, LinkedIn, Pinterest, YouTube, TikTok, Snapchat, Telegram, WhatsApp, Threads, Discord

---

*تم إنشاء هذا الملف لتوثيق جميع التعديلات في جلسة تطوير صفحة وسائل التواصل الاجتماعي.*
