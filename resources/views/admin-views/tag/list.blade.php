@extends('layouts.admin.app')

@section('title', translate('product_tags'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
            <i class="tio-label"></i>
            {{ translate('product_tags') }}
        </h2>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.tag.store') }}" method="post" id="tag_form">
                @csrf
                @php $default_lang = $defaultLang ?? 'en'; @endphp
                @if($language ?? null)
                    <ul class="nav nav-tabs mb-4 max-content">
                        @foreach(json_decode($language) as $lang)
                            <li class="nav-item">
                                <a class="nav-link lang_link {{ $lang == $default_lang ? 'active' : '' }}" href="#" id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) }}({{ strtoupper($lang) }})</a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="row align-items-end g-3 bg-light rounded p-3 mb-2">
                        <div class="col-12 col-lg-6">
                            @foreach(json_decode($language) as $lang)
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
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="{{ translate('help_product_auto_translate') }}">
                                                <i class="tio-globe"></i> {{ translate('Auto translate') }}
                                            </button>
                                        @endif
                                    </label>
                                    <input type="text" name="name[]" id="{{ $lang }}_name" class="form-control" placeholder="{{ translate('product_tags') }}" maxlength="100">
                                </div>
                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                            @endforeach
                        </div>
                        <div class="col-12 col-md-4 col-lg-2">
                            <label class="input-label">{{ translate('sort') ?: 'الترتيب' }}</label>
                            <input type="number" name="sort_order" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-12 col-md-2">
                            <button type="submit" class="btn btn-primary w-100">{{ translate('add') }}</button>
                        </div>
                    </div>
                @else
                    <div class="row align-items-end g-3">
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="input-label">{{ translate('name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name[]" class="form-control" placeholder="{{ translate('product_tags') }}" maxlength="100" required>
                            <input type="hidden" name="lang[]" value="{{ $default_lang }}">
                        </div>
                        <div class="col-12 col-md-4 col-lg-2">
                            <label class="input-label">{{ translate('sort') ?: 'الترتيب' }}</label>
                            <input type="number" name="sort_order" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-12 col-md-2">
                            <button type="submit" class="btn btn-primary w-100">{{ translate('add') }}</button>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card mt-3">
        <div class="p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <h6 class="m-0">{{ translate('product_tags') }} {{ translate('list') }}</h6>
                    <span class="badge badge-soft-dark rounded-50">{{ $tags->total() }}</span>
                </div>
                <form action="{{ request()->url() }}" method="GET">
                    @foreach (request()->except('search','page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <div class="input-group min-h-35">
                        <input type="search" name="search" class="form-control py-1 h-35" placeholder="{{ translate('Search by name') }}" value="{{ $search ?? '' }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary px-2 py-1 min-h-35"><i class="tio-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{ translate('name') }}</th>
                        <th>{{ translate('action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tags as $key => $tag)
                    <tr>
                        <td>{{ $tags->firstItem() + $key }}</td>
                        <td>{{ $tag->name }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#editTagModal-{{ $tag->id }}">
                                    <i class="tio-edit"></i>
                                </button>
                                <a class="btn btn-outline-danger btn-sm form-alert" href="javascript:"
                                   data-id="tag-delete-{{ $tag->id }}"
                                   data-message="{{ translate('Want to delete this tag?') ?: 'هل تريد حذف هذا الوسم؟' }}">
                                    <i class="tio-delete"></i>
                                </a>
                                <form id="tag-delete-{{ $tag->id }}" action="{{ route('admin.tag.delete', $tag->id) }}" method="post" class="d-none">
                                    @csrf @method('delete')
                                </form>
                            </div>
                            @php
                                $tagTranslations = [];
                                if (count($tag->translations ?? [])) {
                                    foreach ($tag->translations as $t) {
                                        if ($t->key === 'name') {
                                            $tagTranslations[$t->locale]['name'] = $t->value;
                                        }
                                    }
                                }
                            @endphp
                            <div class="modal fade" id="editTagModal-{{ $tag->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.tag.update', $tag->id) }}" method="post" class="tag-edit-form">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ translate('update') }} {{ translate('product_tags') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                @if($language ?? null)
                                                    <ul class="nav nav-tabs mb-3 max-content">
                                                        @foreach(json_decode($language) as $lang)
                                                            <li class="nav-item">
                                                                <a class="nav-link lang_link_modal {{ $lang == ($defaultLang ?? 'en') ? 'active' : '' }}" href="#" data-lang="{{ $lang }}" data-modal="editTagModal-{{ $tag->id }}">{{ \App\CentralLogics\Helpers::get_language_name($lang) }}({{ strtoupper($lang) }})</a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                    @foreach(json_decode($language) as $lang)
                                                        <div class="form-group lang_form_modal {{ $lang != ($defaultLang ?? 'en') ? 'd-none' : '' }}" id="{{ $lang }}-form-{{ $tag->id }}">
                                                            <label>
                                                                {{ translate('name') }} ({{ strtoupper($lang) }}) <span class="text-danger">*</span>
                                                                @if($lang != ($defaultLang ?? 'en'))
                                                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2 translate-btn"
                                                                            data-field="name" data-source-lang="{{ $defaultLang ?? 'en' }}"
                                                                            data-target-lang="{{ $lang }}"
                                                                            data-source-id="tag-edit-{{ $tag->id }}-{{ $defaultLang ?? 'en' }}-name"
                                                                            data-target-id="tag-edit-{{ $tag->id }}-{{ $lang }}-name"
                                                                            data-is-html="0"
                                                                            data-toggle="tooltip" data-placement="top"
                                                                            title="{{ translate('help_product_auto_translate') }}">
                                                                        <i class="tio-globe"></i> {{ translate('Auto translate') }}
                                                                    </button>
                                                                @endif
                                                            </label>
                                                            <input type="text" name="name[]" id="tag-edit-{{ $tag->id }}-{{ $lang }}-name" class="form-control" value="{{ $lang == ($defaultLang ?? 'en') ? $tag->getRawOriginal('name') : ($tagTranslations[$lang]['name'] ?? '') }}" maxlength="100" required>
                                                        </div>
                                                        <input type="hidden" name="lang[]" value="{{ $lang }}">
                                                    @endforeach
                                                @else
                                                    <div class="form-group">
                                                        <label>{{ translate('name') }} <span class="text-danger">*</span></label>
                                                        <input type="text" name="name[]" class="form-control" value="{{ $tag->getRawOriginal('name') }}" required>
                                                        <input type="hidden" name="lang[]" value="{{ $defaultLang ?? 'en' }}">
                                                    </div>
                                                @endif
                                                <div class="form-group">
                                                    <label>{{ translate('sort') ?: 'الترتيب' }}</label>
                                                    <input type="number" name="sort_order" class="form-control" value="{{ $tag->sort_order }}" min="0">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('cancel') }}</button>
                                                <button type="submit" class="btn btn-primary">{{ translate('update') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center p-4">{{ translate('No data to show') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tags->hasPages())
        <div class="p-3">
            {!! $tags->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
        </div>
        @endif
    </div>
</div>
@endsection

@push('script_2')
<script>
    'use strict';
    $(".lang_link").click(function(e){
        e.preventDefault();
        $(".lang_link").removeClass('active');
        $(".lang_form").addClass('d-none');
        $(this).addClass('active');
        let form_id = this.id;
        let lang = form_id.split("-")[0];
        $("#"+lang+"-form").removeClass('d-none');
    });

    $(".lang_link_modal").click(function(e){
        e.preventDefault();
        let modalId = $(this).data('modal');
        let lang = $(this).data('lang');
        $("#"+modalId+" .lang_link_modal").removeClass('active');
        $("#"+modalId+" .lang_form_modal").addClass('d-none');
        $(this).addClass('active');
        $("#"+lang+"-form-"+modalId.replace('editTagModal-','')).removeClass('d-none');
    });

    $(document).on('click', '.translate-btn', function() {
        var btn = $(this);
        var sourceId = btn.data('source-id');
        var targetId = btn.data('target-id');
        var isHtml = btn.data('is-html') == 1;
        var sourceLang = btn.data('source-lang');
        var targetLang = btn.data('target-lang');

        var sourceEl = document.getElementById(sourceId);
        if (!sourceEl) return;

        var text = $(sourceEl).val() || '';
        text = $.trim(text);
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
                    if (targetEl) $(targetEl).val(res.translated_text);
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
</script>
@endpush
