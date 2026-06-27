@extends('layouts.admin.app')

@section('title', translate('Add new category'))

@push('css_or_js')
<style>
.category-form-card-header {
    border-bottom: 2px solid var(--primary-clr, #EC2227);
}
.category-form-card-header h6 {
    font-size: 1.15rem !important;
}
.category-form-card .input-label {
    font-size: 1.05rem !important;
}
.category-form-card .category-form-hint,
.category-form-card p.text-muted {
    font-size: 1rem !important;
}
.category-filter-btns {
    flex-wrap: nowrap;
}
.badge-category-count {
    font-size: 1rem !important;
    font-weight: 600;
    padding: 0.4rem 0.75rem;
    background-color: var(--primary-clr, #EC2227) !important;
    color: #fff !important;
}
.help-instructions-modal-header { background: #0d9488; color: #fff; border-bottom: none; padding: 1rem 1.25rem; }
.help-instructions-modal-header .modal-title { order: 1; color: #fff; font-weight: 600; font-size: 1.15rem; }
.help-instructions-modal-header .d-flex.align-items-center { order: 2; margin-inline-start: auto; }
.help-instructions-modal-header .help-whatsapp-icon { color: #fff; display: flex; align-items: center; justify-content: center; width: 38px; height: 38px; padding: 0; border-radius: 6px; background: rgba(255,255,255,0.15); border: 2px solid #fff; transition: all 0.2s; }
.help-instructions-modal-header .help-whatsapp-icon:hover { color: #fff; background: rgba(37,211,102,0.9); border-color: #25D366; }
.help-instructions-modal-header .close { color: #fff !important; opacity: 1; font-size: 1.5rem; line-height: 1; padding: 0; margin: 0; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 6px; background: rgba(255,255,255,0.25); border: none; }
.help-instructions-modal-header .close:hover { color: #fff !important; background: rgba(255,255,255,0.4); }
.help-instructions-modal-header .close span { font-size: 1.5rem; line-height: 1; }
.help-instructions-body { line-height: 1.8; }
.help-step { margin-bottom: 1.25rem; }
.help-step:last-child { margin-bottom: 0; }
.help-step-title { font-weight: 600; color: #0d9488; font-size: 1rem; margin-bottom: 0.35rem; }
.help-step-title::after { content: ''; display: block; height: 1px; background: #99f6e4; margin-top: 0.5rem; }
.help-step-desc { color: #475569; font-size: 0.9375rem; padding-top: 0.25rem; }
.category-filter-btns .category-filter-btn {
    flex: 1 1 0;
    min-width: 0;
    height: 42px !important;
    min-height: 42px !important;
    font-size: 1rem !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/brand-setup.png')}}" alt="{{ translate('image') }}">
                {{ translate('add new category') }}
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#categoryAddInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_category_add_btn') }}
            </button>
        </div>

        {{-- Modal تعليمات إضافة تصنيف --}}
        <div class="modal fade" id="categoryAddInstructionsModal" tabindex="-1" role="dialog" aria-labelledby="categoryAddInstructionsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header help-instructions-modal-header">
                        <div class="d-flex align-items-center" style="gap: 0.5rem;">
                            <a href="https://wa.me/970599814758" target="_blank" rel="noopener" class="help-whatsapp-icon" title="{{ translate('contact us on WhatsApp') }}" aria-label="WhatsApp">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <h5 class="modal-title" id="categoryAddInstructionsModalLabel">
                            <i class="tio-book-outlined me-1"></i> {{ translate('help_category_add_title') }}
                        </h5>
                    </div>
                    <div class="modal-body help-instructions-body">
                        {!! translate('help_category_add_page') !!}
                    </div>
                </div>
            </div>
        </div>

        <form action="{{route('admin.category.store')}}" method="post" enctype="multipart/form-data" id="category_form">
            @php($language = \App\Models\BusinessSetting::where('key', 'language')->first()?->value ?? null)
            @php($default_lang = 'ar')
            <input name="position" value="0" type="hidden">

            {{-- القسم 1: الاسم --}}
            <div class="card mb-3 category-form-card">
                <div class="card-header bg-light category-form-card-header">
                    <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                        <i class="tio-label me-2"></i>{{ translate('section_category_name') }}
                    </h6>
                </div>
                <div class="card-body">
                    @if ($language)
                        @php($default_lang = json_decode($language)[0] ?? 'ar')
                        <ul class="nav nav-tabs mb-3 max-content">
                            @foreach (json_decode($language) as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link {{ $lang == $default_lang ? 'active' : '' }}" href="#" id="{{ $lang }}-link">
                                        {{ Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        @foreach (json_decode($language) as $lang)
                            <div class="form-group {{ $lang != $default_lang ? 'd-none' : '' }} lang_form" id="{{ $lang }}-form">
                                <label class="input-label">
                                    {{ translate('name') }} ({{ strtoupper($lang) }})
                                    @if($lang == $default_lang)
                                        <span class="text-danger">*</span>
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
                                <input type="text" name="name[]" id="{{ $lang }}_name" class="form-control" placeholder="{{ translate('New Category') }}" maxlength="255">
                                @if($lang == $default_lang)
                                    <span class="error-text" data-error="name.0"></span>
                                @endif
                            </div>
                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                        @endforeach
                    @else
                        <div class="form-group lang_form" id="{{ $default_lang }}-form">
                            <label class="input-label">{{ translate('name') }} ({{ strtoupper($default_lang) }}) <span class="text-danger">*</span></label>
                            <input type="text" name="name[]" class="form-control" maxlength="255" placeholder="{{ translate('New Category') }}">
                            <span class="error-text" data-error="name.0"></span>
                        </div>
                        <input type="hidden" name="lang[]" value="{{ $default_lang }}">
                    @endif
                </div>
            </div>

            {{-- القسم 2: الصور --}}
            <div class="card mb-3 category-form-card">
                <div class="card-header bg-light category-form-card-header">
                    <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                        <i class="tio-photo me-2"></i>{{ translate('section_category_images') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="input-label mb-2">{{ translate('Image') }}</label>
                            <div class="custom_upload_input ratio-1 max-w-200">
                                <input type="file" name="image" class="custom-upload-input-file meta-img h-100" data-imgpreview="pre_category_image"
                                       accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                       data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">
                                <span class="delete_file_input btn btn-outline-danger btn-sm square-btn d-none"><i class="tio-delete"></i></span>
                                <div class="img_area_with_preview position-absolute z-index-2">
                                    <img id="pre_category_image" class="h-auto aspect-1 bg-white ratio-1" src="img" onerror="this.classList.add('d-none')">
                                </div>
                                <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                    <div class="d-flex flex-column justify-content-center align-items-center">
                                        <span class="text-muted">{{ translate('Drag & Drop here') }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="error-text justify-content-start" data-error="image"></span>
                            <p class="text-muted mt-1 mb-0 category-form-hint">{{ translate('Image Ratio') }} 1:1 | {{ translate('Image format') }}: {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- أزرار الحفظ --}}
            <div class="card mb-3">
                <div class="card-body d-flex flex-wrap justify-content-end gap-2">
                    <button type="reset" class="btn btn-secondary px-4 min-w-120">{{ translate('reset') }}</button>
                    <button type="submit" class="btn btn-primary btn--primary px-4 min-w-120">{{ translate('submit') }}</button>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-header bg-light category-form-card-header">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                            <i class="tio-folder-outlined me-2"></i>{{ translate('Category List') }}
                        </h6>
                        <span class="badge badge-category-count">{{ $categories->total() }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body p-2 bg-light">
                <form action="{{ request()->url() }}" method="GET" class="category-filter-form">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <div class="row align-items-end g-2">
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label small mb-1">{{ translate('Search by name') }}</label>
                            <input type="search" name="search" class="form-control form-control-sm"
                                   placeholder="{{ translate('Search by name') }}" value="{{ $search }}" autocomplete="off">
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <label class="form-label small mb-1">{{ translate('status') }}</label>
                            <select class="form-control form-control-sm" name="status">
                                <option value="" {{ (($status ?? '') === '') ? 'selected' : '' }}>{{ translate('all') }}</option>
                                <option value="1" {{ ($status ?? '') === '1' ? 'selected' : '' }}>{{ translate('active') }}</option>
                                <option value="0" {{ ($status ?? '') === '0' ? 'selected' : '' }}>{{ translate('inactive') }}</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <label class="form-label small mb-1">{{ translate('Is Featured') }}</label>
                            <select class="form-control form-control-sm" name="featured">
                                <option value="" {{ (($featured ?? '') === '') ? 'selected' : '' }}>{{ translate('all') }}</option>
                                <option value="1" {{ ($featured ?? '') === '1' ? 'selected' : '' }}>{{ translate('yes') }}</option>
                                <option value="0" {{ ($featured ?? '') === '0' ? 'selected' : '' }}>{{ translate('no') }}</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <label class="form-label small mb-1">{{ translate('sort_by') }}</label>
                            <select class="form-control form-control-sm" name="sort_by">
                                <option value="latest" {{ ($sortBy ?? 'latest') === 'latest' ? 'selected' : '' }}>{{ translate('latest') }}</option>
                                <option value="name_az" {{ ($sortBy ?? '') === 'name_az' ? 'selected' : '' }}>{{ translate('name_a_z') }}</option>
                                <option value="name_za" {{ ($sortBy ?? '') === 'name_za' ? 'selected' : '' }}>{{ translate('name_z_a') }}</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 d-flex gap-2 align-items-end category-filter-btns">
                            <button type="submit" class="btn btn-primary category-filter-btn">
                                <i class="tio-checkmark-circle-outlined me-1"></i>{{ translate('Show_Data') }}
                            </button>
                            <a href="{{ route('admin.category.add') }}" class="btn btn-soft-secondary category-filter-btn d-inline-flex align-items-center justify-content-center">{{ translate('clear') }}</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
            <div class="table-responsive datatable-custom">
                <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>{{translate('Category_Image')}}</th>
                            <th>{{translate('name')}}</th>
                            <th>{{ translate('Is Featured') }} <i class="tio-info-outined cursor-pointer" data-toggle="tooltip" title="{{ translate('If enable, the category will show in featured category') }}"></i></th>
                            <th>{{translate('status')}}</th>
                            <th class="text-center">{{translate('action')}}</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($categories as $key=>$category)
                        <tr>
                            <td>{{$categories->firstItem()+$key}}</td>
                            <td>
                                <div class="avatar-lg rounded border">
                                    <img class="img-fit rounded"
                                         src="{{$category['image_fullpath']}}"
                                         alt="{{ translate('image') }}">
                                </div>
                            </td>
                            <td>{{$category['name']}}</td>
                            <td>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input change-status"
                                        {{ $category['is_featured'] == 1 ? 'checked' : '' }}
                                        data-route="{{ route('admin.category.featured', [$category['id'], $category->is_featured == 1 ? 0 : 1]) }}">
                                    <span class="switcher_control"></span>
                                </label>
                            </td>
                            <td>
                                @if($category['status']==1)
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input change-status" {{$category['status']==1? 'checked' : ''}}
                                                id="{{$category['id']}}"
                                               data-route="{{route('admin.category.status',[$category['id'],0])}}">
                                        <span class="switcher_control"></span>
                                    </label>
                                @else
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input change-status" {{$category['status']==1? 'checked' : ''}}
                                                id="{{$category['id']}}"
                                               data-route="{{route('admin.category.status',[$category['id'],1])}}">
                                        <span class="switcher_control"></span>
                                    </label>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-info square-btn" href="{{route('admin.category.edit',[$category['id']])}}">
                                        <i class="tio tio-edit"></i>
                                    </a>
                                    <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                       data-id="category-{{$category['id']}}"
                                       data-message="{{translate('Want to delete this ?')}}">
                                        <i class="tio tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.category.delete',[$category['id']])}}"
                                        method="post" id="category-{{$category['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {!! $categories->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($categories)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('Image Description') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('assets/admin/js/image-upload.js') }}"></script>
    <script src="{{ asset('assets/admin/js/category.js') }}"></script>
    <script>
        "use strict";

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
            successMessage: '{{ translate("Category added successfully!") }}',
            redirectUrl: '{{ route('admin.category.add') }}'
        });
    </script>
@endpush
