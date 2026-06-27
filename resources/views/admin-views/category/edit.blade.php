@extends('layouts.admin.app')

@section('title', translate('Update category'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@include('admin-views.category.partials._parent-tree-picker-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/brand-setup.png')}}"
                     alt="{{ translate('image') }}">
                @if($category->parent_id == 0)
                    {{translate('category_update')}}
                @else
                    {{translate('sub_category_update')}}
                @endif
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#categoryEditInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_category_edit_btn') }}
            </button>
        </div>

        @include('admin-views.partials._help-instructions-modal', ['modalId' => 'categoryEditInstructionsModal', 'titleKey' => 'help_category_edit_title', 'pageKey' => 'help_category_edit_page'])

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form action="{{route('admin.category.update',[$category['id']])}}" method="post"
                              enctype="multipart/form-data" id="category_form">
                            @php
                                $language     = \App\Models\BusinessSetting::where('key','language')->first()?->value ?? null;
                                $default_lang = 'ar';
                                // Read raw DB value (bypasses 'array' cast edge-cases with null)
                                $rawVtu          = $category->getAttributes()['visible_to_user_types'] ?? null;
                                $everyoneChecked = ($rawVtu === null || $rawVtu === '');
                                $savedIds        = $everyoneChecked ? [] : (json_decode($rawVtu, true) ?? []);
                            @endphp
                            @if($language)
                                @php $default_lang = json_decode($language)[0] ?? 'ar'; @endphp
                                <ul class="nav nav-tabs mb-4 max-content">
                                    @foreach(json_decode($language) as $lang)
                                        <li class="nav-item">
                                            <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}"
                                               href="#"
                                               id="{{$lang}}-link">{{Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        @foreach(json_decode($language) as $lang)
                                                <?php
                                                if (count($category['translations'])) {
                                                    $translate = [];
                                                    foreach ($category['translations'] as $t) {
                                                        if ($t->locale == $lang && $t->key == "name") {
                                                            $translate[$lang]['name'] = $t->value;
                                                        }
                                                    }
                                                }
                                                ?>
                                            <div class="form-group {{$lang != $default_lang ? 'd-none':''}} lang_form"
                                                 id="{{$lang}}-form">
                                                <label class="input-label"
                                                       for="exampleFormControlInput1">
                                                    {{translate('name')}}({{strtoupper($lang)}})
                                                    @if($lang == $default_lang)
                                                        <span class="input-label-secondary text-danger">*</span>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2 translate-btn"
                                                                data-field="name" data-source-lang="{{ $default_lang }}"
                                                                data-target-lang="{{ $lang }}" data-source-id="{{ $default_lang }}_name"
                                                                data-target-id="{{ $lang }}_name" data-is-html="0"
                                                                title="{{ translate('Auto translate from') }} {{ strtoupper($default_lang) }}">
                                                            <i class="tio-globe"></i> {{ translate('Auto translate') }}
                                                        </button>
                                                    @endif
                                                </label>
                                                <input type="text" name="name[]" id="{{ $lang }}_name" maxlength="255"
                                                       value="{{$lang==$default_lang?$category['name']:($translate[$lang]['name']??'')}}"
                                                       class="form-control"
                                                       oninvalid="document.getElementById('{{ $default_lang }}-link').click()"
                                                       placeholder="{{ translate('New Category') }}"
                                                >
                                                @if($lang == $default_lang)
                                                    <span class="error-text" data-error="name.0"></span>
                                                @endif
                                            </div>
                                            <input type="hidden" name="lang[]" value="{{$lang}}">
                                        @endforeach
                                        @else
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group lang_form" id="{{$default_lang}}-form">
                                                        <label class="input-label"
                                                               for="exampleFormControlInput1">
                                                            {{translate('name')}}({{strtoupper($default_lang)}})
                                                            <span class="input-label-secondary text-danger">*</span>
                                                        </label>
                                                        <input type="text" name="name[]" value="{{$category['name']}}"
                                                               class="form-control"
                                                               oninvalid="document.getElementById('{{ $default_lang }}-link').click()"
                                                               placeholder="{{ translate('New Category') }}">
                                                        <span class="error-text" data-error="name.0"></span>
                                                    </div>
                                                    <input type="hidden" name="lang[]" value="{{$default_lang}}">
                                                    @endif
                                                    <input name="position" value="0" class="d-none">
                                                </div>
                                                @if($category->parent_id == 0)
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="mb-2">{{translate('Image')}}</label>
                                                            <div class="custom_upload_input ratio-1 max-w-200">
                                                                <input type="file" name="image"
                                                                       class="custom-upload-input-file meta-img h-100"
                                                                       id="" data-imgpreview="pre_meta_image_viewer"
                                                                       accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                                                       data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">

                                                                <div
                                                                    class="img_area_with_preview position-absolute z-index-2">
                                                                    <img id="pre_meta_image_viewer"
                                                                         class="h-auto aspect-1 bg-white ratio-1"
                                                                         src=""
                                                                         onerror="this.classList.add('d-none')" alt="">
                                                                </div>
                                                                <div
                                                                    class="img_area_with_preview position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                                                    <div
                                                                        class="d-flex flex-column justify-content-center align-items-center">
                                                                        <img src="{{$category['image_fullpath']}}"
                                                                             class="w-100" alt="">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <span class="error-text justify-content-start"
                                                                  data-error="image"></span>
                                                            <p class="fs-16 mb-2 text-dark mt-2">{{ translate('Images Ratio') }}
                                                                1:1</p>
                                                            <p class="fs-14 text-muted mb-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>

                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="input-label">
                                                                {{ translate('category_parent_tree_label') ?: 'وضع التصنيف تحت' }}
                                                                <span class="input-label-secondary text-danger">*</span>
                                                            </label>
                                                            @include('admin-views.category.partials._parent-tree-picker', [
                                                                'parentCategoryTree' => $parentCategoryTree ?? [],
                                                                'selectedParentId' => $category->parent_id,
                                                                'inputName' => 'parent_id',
                                                            ])
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                            {{-- ── Category Visibility by User Type ─────────────── --}}
                                            <div class="card border mb-3">
                                                <div class="card-header py-2">
                                                    <h6 class="card-header-title mb-0">
                                                        <i class="tio tio-user-switch"></i>
                                                        {{ translate('category_visibility') ?: 'من يرى هذا التصنيف' }}
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <p class="text-muted small mb-3">
                                                        اختر من يستطيع رؤية هذا التصنيف في المتجر. اترك «كل الزوار» محدداً ليظهر للجميع.
                                                    </p>

                                                    {{-- Everyone toggle --}}
                                                    <div class="custom-control custom-checkbox mb-3">
                                                        <input type="checkbox"
                                                               class="custom-control-input"
                                                               id="vis-everyone"
                                                               name="visible_everyone"
                                                               value="1"
                                                               {{ $everyoneChecked ? 'checked' : '' }}
                                                               onchange="toggleVisibilityPanel(this)">
                                                        <label class="custom-control-label font-weight-bold" for="vis-everyone">
                                                            كل الزوار (بدون قيود)
                                                        </label>
                                                    </div>

                                                    {{-- Specific types panel --}}
                                                    <div id="vis-panel" style="{{ $everyoneChecked ? 'display:none' : '' }}">
                                                        <div class="custom-control custom-checkbox mb-2">
                                                            <input type="checkbox"
                                                                   class="custom-control-input"
                                                                   id="vis-guest"
                                                                   name="visible_to_user_types[]"
                                                                   value="0"
                                                                   {{ in_array(0, $savedIds) ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="vis-guest">
                                                                <i class="tio tio-user-add"></i> الزوار غير المسجلين
                                                            </label>
                                                        </div>
                                                        @if(isset($userTypes) && $userTypes->count())
                                                            @foreach($userTypes as $ut)
                                                            <div class="custom-control custom-checkbox mb-2">
                                                                <input type="checkbox"
                                                                       class="custom-control-input"
                                                                       id="vis-ut-{{ $ut->id }}"
                                                                       name="visible_to_user_types[]"
                                                                       value="{{ $ut->id }}"
                                                                       {{ in_array($ut->id, $savedIds) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="vis-ut-{{ $ut->id }}">
                                                                    {{ $ut->name }}
                                                                    @if($ut->is_default)
                                                                        <span class="badge badge-soft-success badge-sm">{{ translate('default') }}</span>
                                                                    @endif
                                                                </label>
                                                            </div>
                                                            @endforeach
                                                        @else
                                                            <p class="text-muted small">لا توجد أنواع عملاء بعد. اذهب إلى <strong>العملاء ← أنواع العملاء</strong> لإنشائها.</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end gap-3">
                                                <button type="reset"
                                                        class="btn btn-secondary">{{translate('reset')}}</button>
                                                <button type="submit"
                                                        class="btn btn-primary">{{translate('update')}}</button>
                                            </div>

                                            <script>
                                            function toggleVisibilityPanel(cb){
                                                document.getElementById('vis-panel').style.display = cb.checked ? 'none' : '';
                                            }
                                            </script>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('assets/admin/js/image-upload.js') }}"></script>
    <script src="{{ asset('assets/admin/js/category.js') }}"></script>
    <script>
        "use strict";

        @include('admin-views.category.partials._parent-tree-picker-js')

        $(document).on('click', '.translate-btn', function() {
            var btn = $(this);
            var sourceId = btn.data('source-id');
            var targetId = btn.data('target-id');
            var isHtml = btn.data('is-html') == 1;
            var sourceLang = btn.data('source-lang');
            var targetLang = btn.data('target-lang');

            var sourceEl = document.getElementById(sourceId);
            if (!sourceEl) return;

            var text = isHtml ? (($('#'+sourceId).length && typeof $('#'+sourceId).summernote==='function' ? $('#'+sourceId).summernote('code') : (sourceEl.querySelector('.ql-editor') ? sourceEl.querySelector('.ql-editor').innerHTML : '')) || '') : ($(sourceEl).val() || '');
            text = $.trim(text);
            if (isHtml && (text === '' || text === '<p><br></p>' || text === '<p></p>')) text = '';
            if (!text) {
                toastr.warning('{{ translate("Please fill the source field first") }}', { CloseButton: true, ProgressBar: true });
                return;
            }

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> {{ translate("Translating...") }}');

            $.ajax({
                url: '{{ route("admin.product.translate") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    text: text,
                    source_lang: sourceLang,
                    target_lang: targetLang,
                    is_html: isHtml ? 1 : 0
                },
                success: function(res) {
                    if (res.success && res.translated_text) {
                        var targetEl = document.getElementById(targetId);
                        if (isHtml) {
                            var $tgt = $('#' + targetId);
                            if ($tgt.length && typeof $tgt.summernote === 'function') {
                                $tgt.summernote('code', res.translated_text);
                            } else {
                                var qlTarget = targetEl ? targetEl.querySelector('.ql-editor') : document.querySelector('#' + targetId + ' .ql-editor');
                                if (qlTarget) qlTarget.innerHTML = res.translated_text;
                            }
                        } else {
                            $(targetEl).val(res.translated_text);
                        }
                        toastr.success('{{ translate("Translation applied. You can edit before saving.") }}', { CloseButton: true, ProgressBar: true });
                    } else {
                        toastr.error(res.message || '{{ translate("Translation failed") }}', { CloseButton: true, ProgressBar: true });
                    }
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ translate("Translation failed") }}';
                    toastr.error(msg, { CloseButton: true, ProgressBar: true });
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="tio-globe"></i> {{ translate("Auto translate") }}');
                }
            });
        });

        submitByAjax('#category_form', {
            hasEditors: false,
            languages: @json(json_decode($language) ?? []),
            successMessage: '{{ $category->parent_id == 0 ? translate("Category updated successfully!") : translate("Sub category updated successfully!") }}',
            redirectUrl: '{{ $category->parent_id == 0 ? route('admin.category.add') : route('admin.category.add-sub-category') }}'
        });
    </script>
@endpush
