@extends('layouts.admin.app')

@section('title', translate('Add new sub category'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    @include('admin-views.category.partials._parent-tree-picker-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/brand-setup.png')}}" alt="{{ translate('image') }}">
                {{ translate('sub_category_Setup') }}
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#subCategoryAddInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_sub_category_add_btn') }}
            </button>
        </div>

        @include('admin-views.partials._help-instructions-modal', ['modalId' => 'subCategoryAddInstructionsModal', 'titleKey' => 'help_sub_category_add_title', 'pageKey' => 'help_sub_category_add_page'])

        <form action="{{route('admin.category.store')}}" method="post" id="category_form">
            @php($language = \App\Models\BusinessSetting::where('key', 'language')->first()?->value ?? null)
            @php($default_lang = 'ar')
            <input name="position" value="1" type="hidden">

            {{-- القسم 1: اسم التصنيف الفرعي والتصنيف الرئيسي --}}
            <div class="card mb-3 category-form-card">
                <div class="card-header bg-light category-form-card-header">
                    <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                        <i class="tio-label me-2"></i>{{ translate('sub_category') }} {{ translate('name') }}
                    </h6>
                </div>
                <div class="card-body">
                    {{-- 1. اسم التصنيف الفرعي (مع اللغات) --}}
                    <div class="row g-4 mb-4">
                        <div class="col-12">
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
                                    <div class="form-group m-0 {{ $lang != $default_lang ? 'd-none' : '' }} lang_form" id="{{ $lang }}-form">
                                        <label class="input-label">
                                            {{ translate('sub_category') }} {{ translate('name') }} ({{ strtoupper($lang) }})
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
                                        <input type="text" name="name[]" id="{{ $lang }}_name" class="form-control" maxlength="255" placeholder="{{ translate('New Sub Category') }}">
                                        @if($lang == $default_lang)
                                            <span class="error-text" data-error="name.0"></span>
                                        @endif
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{ $lang }}">
                                @endforeach
                            @else
                                <div class="form-group m-0 lang_form" id="{{ $default_lang }}-form">
                                    <label class="input-label">
                                        {{ translate('sub_category') }} {{ translate('name') }} ({{ strtoupper($default_lang) }})
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name[]" class="form-control" placeholder="{{ translate('New Sub Category') }}">
                                    <span class="error-text" data-error="name.0"></span>
                                </div>
                                <input type="hidden" name="lang[]" value="{{ $default_lang }}">
                            @endif
                        </div>
                    </div>
                    {{-- 2. التصنيف الأب — شجرة قابلة للنقر --}}
                    <div class="row g-4">
                        <div class="col-12 col-lg-6">
                            <div class="form-group m-0">
                                <label class="input-label">
                                    {{ translate('category_parent_tree_label') ?: 'وضع التصنيف الجديد تحت' }}
                                    <span class="text-danger">*</span>
                                </label>
                                @include('admin-views.category.partials._parent-tree-picker', [
                                    'parentCategoryTree' => $parentCategoryTree ?? [],
                                    'selectedParentId' => null,
                                    'inputName' => 'parent_id',
                                ])
                            </div>
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
                            <i class="tio-folder-outlined me-2"></i>{{ translate('Sub Category List') }}
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
                                   placeholder="{{ translate('Search by name') }}" value="{{ $search ?? '' }}" autocomplete="off">
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
                            <a href="{{ route('admin.category.add-sub-category') }}" class="btn btn-soft-secondary category-filter-btn d-inline-flex align-items-center justify-content-center">{{ translate('clear') }}</a>
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
                                <th>{{ translate('main') }} {{ translate('category') }}</th>
                                <th>{{ translate('sub_category') }}</th>
                                <th>{{ translate('status') }}</th>
                                <th class="text-center">{{ translate('action') }}</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">
                            @foreach($categories as $key => $category)
                                <tr>
                                    <td>{{ $categories->firstItem() + $key }}</td>
                                    <td class="text-title">{{ $category->parent['name'] ?? '-' }}</td>
                                    <td>{{ $category['name'] }}</td>
                                    <td>
                                        <label class="switcher">
                                            <input type="checkbox" class="switcher_input change-status"
                                                   {{ $category['status'] == 1 ? 'checked' : '' }} id="{{ $category['id'] }}"
                                                   data-route="{{ route('admin.category.status', [$category['id'], $category['status'] == 1 ? 0 : 1]) }}">
                                            <span class="switcher_control"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-info square-btn"
                                               href="{{ route('admin.category.edit', [$category['id']]) }}">
                                                <i class="tio tio-edit"></i>
                                            </a>
                                            <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                               data-id="category-{{ $category['id'] }}"
                                               data-message="{{ translate('Want to delete this ?') }}">
                                                <i class="tio tio-delete"></i>
                                            </a>
                                        </div>
                                        <form action="{{ route('admin.category.delete', [$category['id']]) }}"
                                              method="post" id="category-{{ $category['id'] }}">
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
                @if(count($categories) == 0)
                    <div class="text-center p-4">
                        <img class="mb-3 width-7rem" src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}" alt="{{ translate('Image Description') }}">
                        <p class="mb-0">{{ translate('No data to show') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script_2')
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
            successMessage: '{{ translate("Sub category added successfully!") }}',
            redirectUrl: '{{ route('admin.category.add-sub-category') }}'
        });
    </script>
@endpush
