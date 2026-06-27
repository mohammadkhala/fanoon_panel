# خطة: نافذة التعليمات في الصفحات (Page Instructions Modal)

> **الغرض:** توثيق التنسيق والتصميم والمحتوى لنافذة التعليمات في صفحة إضافة المنتج، لاستخدامها كنموذج عند نقل نفس الفكرة إلى صفحات أخرى.
>
> **ملاحظة:** لا تنقل إلى المرحلة التالية إلا بموافقة المستخدم.

---

## سجل التغييرات (Changelog)

| التاريخ | التغيير |
|---------|---------|
| — | إضافة نافذة التعليمات في صفحة إضافة المنتج |
| — | تغيير نص الزر من "التعليمات" إلى "كيف أضيف منتج؟" |
| — | إزالة modal-footer — الإغلاق عبر زر × فقط |
| — | تنسيق الهيدر بلون تركواز (#0d9488) |
| — | إضافة أيقونة واتساب للدعم الفني بجانب زر الإغلاق |
| — | رقم واتساب: **970599814758** |
| — | تنسيق زر واتساب: إطار أبيض، عند hover يتحول لأخضر واتساب |
| — | تنسيق زر الإغلاق: خلفية شبه شفافة، حجم 38×38px |
| — | ألوان الخطوات: عنوان تركواز، شرح رمادي داكن |
| — | الخطوة 4: توضيح إمكانية إضافة أكثر من صورة |

---

## 1. الملفات المتأثرة

| الملف | الوظيفة |
|------|---------|
| `resources/views/admin-views/product/index.blade.php` | صفحة إضافة منتج — النموذج الحالي |
| `resources/lang/ar/messages.php` | ترجمات عربية |
| `resources/lang/en/messages.php` | ترجمات إنجليزية |

---

## 2. هيكل التصميم (HTML)

### 2.1 زر التعليمات في رأس الصفحة

```html
<div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
        {{-- عنوان الصفحة --}}
    </h2>
    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#productAddInstructionsModal">
        <i class="tio-book-outlined"></i> {{ translate('help_products_add_btn') }}
    </button>
</div>
```

**مفتاح الزر:** `help_products_add_btn`  
- عربي: كيف أضيف منتج؟  
- English: How do I add a product?

### 2.2 النافذة المنبثقة (Modal)

```html
<div class="modal fade" id="productAddInstructionsModal" tabindex="-1" role="dialog" aria-labelledby="productAddInstructionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header help-instructions-modal-header">
                <h5 class="modal-title" id="productAddInstructionsModalLabel">
                    <i class="tio-book-outlined me-1"></i> {{ translate('help_products_add_title') }}
                </h5>
                <div class="d-flex align-items-center" style="gap: 0.5rem;">
                    <a href="https://wa.me/970599814758" target="_blank" rel="noopener" class="help-whatsapp-icon" title="{{ translate('contact us on WhatsApp') }}" aria-label="WhatsApp">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body help-instructions-body">
                {!! translate('help_products_add_page') !!}
            </div>
            {{-- بدون modal-footer — إغلاق عبر زر × فقط --}}
        </div>
    </div>
</div>
```

**ملاحظات:**
- الهيدر يستخدم class `help-instructions-modal-header`
- أيقونة واتساب وزر الإغلاق داخل `div.d-flex` بجانب بعض
- رقم واتساب: **970599814758**
- لا يوجد modal-footer

---

## 3. تنسيق المحتوى (بنية الخطوات)

كل خطوة في المحتوى لها هذا الهيكل:

```html
<div class="help-step">
    <div class="help-step-title">الخطوة 1</div>
    <div class="help-step-desc">الشرح هنا...</div>
</div>
```

---

## 4. CSS (التنسيق الكامل)

```css
/* الهيدر */
.help-instructions-modal-header { background: #0d9488; color: #fff; border-bottom: none; padding: 1rem 1.25rem; }
.help-instructions-modal-header .modal-title + .d-flex { margin-left: auto; }
.help-instructions-modal-header .modal-title { color: #fff; font-weight: 600; font-size: 1.15rem; }

/* زر واتساب */
.help-instructions-modal-header .help-whatsapp-icon { color: #fff; display: flex; align-items: center; justify-content: center; width: 38px; height: 38px; padding: 0; border-radius: 6px; background: rgba(255,255,255,0.15); border: 2px solid #fff; transition: all 0.2s; }
.help-instructions-modal-header .help-whatsapp-icon:hover { color: #fff; background: rgba(37,211,102,0.9); border-color: #25D366; }

/* زر الإغلاق */
.help-instructions-modal-header .close { color: #fff !important; opacity: 1; font-size: 1.5rem; line-height: 1; padding: 0; margin: 0; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 6px; background: rgba(255,255,255,0.25); border: none; }
.help-instructions-modal-header .close:hover { color: #fff !important; background: rgba(255,255,255,0.4); }
.help-instructions-modal-header .close span { font-size: 1.5rem; line-height: 1; }

/* المحتوى */
.help-instructions-body { line-height: 1.8; }
.help-step { margin-bottom: 1.25rem; }
.help-step:last-child { margin-bottom: 0; }
.help-step-title { font-weight: 600; color: #0d9488; font-size: 1rem; margin-bottom: 0.35rem; }
.help-step-title::after { content: ''; display: block; height: 1px; background: #99f6e4; margin-top: 0.5rem; }
.help-step-desc { color: #475569; font-size: 0.9375rem; padding-top: 0.25rem; }
```

**جدول الألوان:**
| العنصر | اللون | الكود |
|--------|-------|-------|
| خلفية الهيدر | تركواز | `#0d9488` |
| عنوان الخطوة | تركواز | `#0d9488` |
| خط الفاصل | تركواز فاتح | `#99f6e4` |
| نص الشرح | رمادي داكن | `#475569` |
| زر واتساب (hover) | أخضر واتساب | `#25D366` |

---

## 5. مفاتيح الترجمة

| المفتاح | العربي | English |
|---------|--------|---------|
| `help_products_add_btn` | كيف أضيف منتج؟ | How do I add a product? |
| `help_products_add_title` | إضافة منتج جديد | Add a new product |
| `help_products_add_page` | (HTML — انظر أدناه) | (HTML) |
| `contact us on WhatsApp` | تواصل معنا على واتساب | contact us on WhatsApp |

**محتوى `help_products_add_page` (عربي):**
```
<div class="help-step"><div class="help-step-title">الخطوة 1</div><div class="help-step-desc">املأ اسم المنتج والوصف المختصر — يظهران للعميل في المتجر.</div></div>
<div class="help-step"><div class="help-step-title">الخطوة 2</div><div class="help-step-desc">اختر التصنيف والتصنيف الفرعي — لتنظيم المنتجات وتسهيل البحث عليها.</div></div>
<div class="help-step"><div class="help-step-title">الخطوة 3</div><div class="help-step-desc">حدّد السعر الأساسي وكمية المخزون — يمكنك أيضاً تحديد تنبيه عند انخفاض المخزون.</div></div>
<div class="help-step"><div class="help-step-title">الخطوة 4</div><div class="help-step-desc">أضف صورة المنتج — صورة واحدة على الأقل (نسبة 1:1)، ويمكنك إضافة أكثر من صورة.</div></div>
<div class="help-step"><div class="help-step-title">الخطوة 5 (اختياري)</div><div class="help-step-desc">إذا كان المنتج بألوان أو أحجام مختلفة، اختر الخصائص ثم أدخل القيم (مثل: أحمر، أزرق، صغير، كبير). سيتم إنشاء المتغيرات تلقائياً مع إمكانية تحديد سعر ومخزون لكل منها.</div></div>
<div class="help-step"><div class="help-step-title">الخطوة 6 (اختياري)</div><div class="help-step-desc">إذا كان الموقع يدعم أكثر من لغة، اكتب الاسم والوصف باللغة الأساسية أولاً، ثم اضغط زر «ترجمة تلقائية» بجانب الحقل لترجمة المحتوى.</div></div>
<div class="help-step"><div class="help-step-title">الخطوة 7 (اختياري)</div><div class="help-step-desc">إذا كان لديك أنواع عملاء (مثل: جملة، تجزئة)، يمكنك تحديد سعر أو خصم خاص لكل نوع في قسم «أسعار حسب نوع العميل». إن تركت الحقل فارغاً، يُستخدم السعر الأساسي.</div></div>
```

---

## 6. خطوات نقل النموذج إلى صفحة أخرى

### الخطوة أ: إضافة زر التعليمات
- ضع الزر بجانب عنوان الصفحة في نفس الـ `div` مع `d-flex justify-content-between`
- استخدم مفتاح ترجمة للزر (مثل: `help_bulk_import_btn`)
- غيّر `data-target` إلى `#{pageId}InstructionsModal`

### الخطوة ب: إضافة الـ Modal
- انسخ هيكل الـ Modal كاملاً (بما فيه الهيدر وواتساب وزر الإغلاق)
- غيّر `id` إلى `{pageId}InstructionsModal`
- غيّر مفتاح العنوان والمحتوى
- رقم واتساب يبقى: **970599814758** (أو يُخزّن في إعدادات النظام)

### الخطوة ج: إضافة CSS
- انسخ كامل الـ CSS أعلاه داخل `@push('css_or_js')`

### الخطوة د: إضافة الترجمات
- مفتاح الزر: `help_{page}_btn`
- مفتاح العنوان: `help_{page}_title`
- مفتاح المحتوى: `help_{page}_page` (HTML بنفس بنية `help-step`)

### الخطوة هـ: كتابة المحتوى
- اكتب الخطوات ببنية: عنوان → سطر → شرح
- استخدم لغة بسيطة للمستخدم المتوسط
- حدّد الخطوات الاختيارية بـ `(اختياري)`

---

## 7. الصفحات المرشحة للنقل (للموافقة لاحقاً)

| الصفحة | المسار | مفتاح الترجمة المقترح |
|--------|--------|------------------------|
| استيراد جماعي | `product/bulk-import` | `help_bulk_import_page` |
| إضافة تصنيف | `category/add` | `help_category_add_page` |
| تعديل منتج | `product/edit` | `help_products_edit_page` |
| إعدادات الأعمال | `business-settings/ecom-setup` | `help_ecom_setup_page` |
| قائمة الطلبات | `order/list` | `help_orders_list_page` |

---

## 8. ملخص التنسيق

| العنصر | القيمة |
|--------|--------|
| زر التعليمات | `btn btn-outline-primary btn-sm` + أيقونة `tio-book-outlined` |
| مفتاح الزر | `help_products_add_btn` (أو حسب الصفحة) |
| Modal | `modal fade` + `modal-dialog-centered` |
| الهيدر | `help-instructions-modal-header` — خلفية تركواز |
| واتساب | أيقونة SVG، بجانب زر الإغلاق، رابط `https://wa.me/970599814758` |
| بدون Footer | نعم — إغلاق عبر زر × فقط |
| بنية الخطوة | `help-step` + `help-step-title` + `help-step-desc` |
| عرض المحتوى | `{!! translate('key') !!}` (لسماح HTML) |

---

*آخر تحديث: توثيق كامل لجميع التغييرات*
