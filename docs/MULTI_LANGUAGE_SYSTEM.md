# نظام تعدد اللغات — Multi-Language System

## نظرة عامة

النظام يستخدم **BusinessSetting** لتخزين اللغات المفعّلة في جدول `business_settings` بالمفتاح `language` كـ JSON، مثال: `["ar","en","he"]`.

---

## 1. مصدر اللغات

```php
// جلب اللغات المفعّلة
$language = \App\Models\BusinessSetting::where('key', 'language')->first();
$language = $language->value ?? null;  // JSON: ["ar","en","he"]
$default_lang = json_decode($language)[0] ?? 'ar';  // اللغة الافتراضية
```

---

## 2. بنية HTML للتبويبات

```blade
@if($language)
    <ul class="nav nav-tabs mb-4 max-content">
        @foreach(json_decode($language) as $lang)
            <li class="nav-item">
                <a class="nav-link lang_link {{ $lang == $default_lang ? 'active' : '' }}" href="#"
                   id="{{ $lang }}-link">
                    {{ \App\CentralLogics\Helpers::get_language_name($lang) }}({{ strtoupper($lang) }})
                </a>
            </li>
        @endforeach
    </ul>
@endif
```

**ملاحظات:**
- `lang_link` — كلاس للروابط
- `id="{{ $lang }}-link"` — مثل `ar-link`, `en-link`
- `Helpers::get_language_name($lang)` — اسم اللغة (مثلاً: Arabic - العربية)

---

## 3. بنية حقول النموذج لكل لغة

```blade
@foreach(json_decode($language) as $lang)
    <div class="card mb-3 card-body {{ $lang != $default_lang ? 'd-none' : '' }} lang_form"
         id="{{ $lang }}-form">
        <div class="form-group">
            <label for="{{ $lang }}_name">{{ translate('name') }}({{ strtoupper($lang) }})</label>
            <input type="text" name="name[{{ $lang }}]" id="{{ $lang }}_name" class="form-control">
        </div>
        <input type="hidden" name="lang[]" value="{{ $lang }}">
    </div>
@endforeach
```

**ملاحظات:**
- `lang_form` — كلاس للحاوية
- `id="{{ $lang }}-form"` — مثل `ar-form`, `en-form`
- `d-none` — إخفاء اللغات غير الافتراضية
- `name="name[{{ $lang }}]"` أو `name="name[]"` مع `lang[]` حسب الـ Controller

---

## 4. JavaScript لتبديل اللغات

```javascript
$(".lang_link").click(function (e) {
    e.preventDefault();
    $(".lang_link").removeClass('active');
    $(".lang_form").addClass('d-none');
    $(this).addClass('active');

    let form_id = this.id;           // مثل "ar-link"
    let lang = form_id.split("-")[0]; // مثل "ar"
    $("#" + lang + "-form").removeClass('d-none');

    // إخفاء/إظهار جزء مشترك (اختياري)
    if (lang == '{{ $default_lang }}') {
        $("#from_part_2").removeClass('d-none');
    } else {
        $("#from_part_2").addClass('d-none');
    }
});
```

---

## 5. أسماء اللغات — Helpers::get_language_name()

الملف: `app/CentralLogics/Helpers.php`

```php
Helpers::get_language_name('ar')  // "Arabic - العربية"
Helpers::get_language_name('en')  // "English"
Helpers::get_language_name('he')  // "Hebrew - עברית"
```

---

## 6. تطبيق النظام في صفحة جديدة

### الخطوة 1: في الـ Controller

```php
$languageSetting = \App\Models\BusinessSetting::where('key', 'language')->first();
$language = $languageSetting->value ?? null;
$default_lang = $language ? json_decode($language)[0] : 'ar';

return view('admin-views.your-view', compact('language', 'default_lang'));
```

### الخطوة 2: في الـ Blade

```blade
@php($language = $language ?? null)
@php($default_lang = $default_lang ?? 'ar')

@if($language)
    <ul class="nav nav-tabs mb-4 max-content">
        @foreach(json_decode($language) as $lang)
            <li class="nav-item">
                <a class="nav-link lang_link {{ $lang == $default_lang ? 'active' : '' }}" href="#"
                   id="{{ $lang }}-link">
                    {{ \App\CentralLogics\Helpers::get_language_name($lang) }}({{ strtoupper($lang) }})
                </a>
            </li>
        @endforeach
    </ul>

    @foreach(json_decode($language) as $lang)
        <div class="lang_form {{ $lang != $default_lang ? 'd-none' : '' }}" id="{{ $lang }}-form">
            {{-- حقول النموذج هنا --}}
        </div>
    @endforeach
@endif
```

### الخطوة 3: إضافة الـ JavaScript

انسخ كتلة `$(".lang_link").click(...)` أو ضعها في ملف JS مشترك إذا كانت مكررة.

---

## 7. الصفحات التي تستخدم النظام حالياً

| الصفحة | الملف |
|--------|-------|
| المنتجات (إضافة/تعديل) | `admin-views/product/index.blade.php`, `edit.blade.php` |
| التصنيفات | `admin-views/category/index.blade.php`, `edit.blade.php` |
| السمات | `admin-views/attribute/index.blade.php`, `edit.blade.php` |
| المناطق | `admin-views/business-settings/areas-index.blade.php` |
| المدن | `admin-views/business-settings/cities-index.blade.php` |
| أنواع المستخدمين | `admin-views/user-type/index.blade.php` |

---

## 8. إعداد اللغات

يتم من: **إعدادات الأعمال → إعداد التجارة الإلكترونية** في حقل "ترجمة المنتج والتصنيف" (language select).
