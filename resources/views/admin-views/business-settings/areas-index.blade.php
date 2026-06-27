@extends('layouts.admin.app')

@section('title', translate('delivery_fee'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <i class="tio-truck"></i> {{ translate('delivery_fee') }}
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#areasInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_areas_btn') }}
            </button>
        </div>

        @include('admin-views.partials._help-instructions-modal', ['modalId' => 'areasInstructionsModal', 'titleKey' => 'help_areas_title', 'pageKey' => 'help_areas_page'])

        <div class="card">
            <div class="card-header border-0">
                <h5 class="card-title">{{ translate('delivery_fee') }}</h5>
            </div>
            <div class="card-body">
                @php($language = \App\Models\BusinessSetting::where('key', 'language')->first()?->value ?? null)
                @php($default_lang = 'ar')
                @if($language)
                    @php($default_lang = json_decode($language)[0] ?? 'ar')
                @endif
                <form action="{{ route('admin.business-settings.areas.store') }}" method="post" class="mb-4">
                    @csrf
                    @if($language)
                    <ul class="nav nav-tabs mb-3 max-content">
                        @foreach(json_decode($language) as $lang)
                            <li class="nav-item">
                                <a class="nav-link lang_link {{ $lang == $default_lang ? 'active' : '' }}" href="#" id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) }} ({{ strtoupper($lang) }})</a>
                            </li>
                        @endforeach
                    </ul>
                    @endif
                    <div class="row align-items-end g-3 bg-light rounded p-3 mb-2">
                        @if($language)
                            @foreach(json_decode($language) as $lang)
                                <div class="col-12 col-md-6 col-lg-5 lang_form {{ $lang != $default_lang ? 'd-none' : '' }}" id="{{ $lang }}-form">
                                    <label class="form-label">
                                        {{ translate('name') }} ({{ strtoupper($lang) }}) @if($lang == $default_lang)<span class="text-danger">*</span>@else
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2 translate-btn"
                                                data-source-lang="{{ $default_lang }}" data-target-lang="{{ $lang }}"
                                                data-source-id="{{ $default_lang }}_name" data-target-id="{{ $lang }}_name" data-is-html="0"
                                                title="{{ translate('Auto translate from') }} {{ strtoupper($default_lang) }}">
                                            <i class="tio-globe"></i> {{ translate('Auto translate') }}
                                        </button>
                                        @endif
                                    </label>
                                    <input type="text" name="name[{{ $lang }}]" id="{{ $lang }}_name" class="form-control" placeholder="{{ translate('area_name_en') }}" maxlength="100" value="{{ old('name.'.$lang) }}"
                                           {{ $lang == $default_lang ? 'required' : '' }}>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12 col-md-6 col-lg-5">
                                <label class="form-label">{{ translate('name') }} ({{ strtoupper($default_lang) }}) <span class="text-danger">*</span></label>
                                <input type="text" name="name[{{ $default_lang }}]" class="form-control" placeholder="{{ translate('area_name_en') }}" required maxlength="100" value="{{ old('name.'.$default_lang) }}">
                            </div>
                        @endif
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label">{{ translate('delivery_charge') }}</label>
                            <input type="number" name="delivery_charge" class="form-control" step="0.01" min="0" required value="{{ old('delivery_charge', 0) }}">
                        </div>
                        <div class="col-12 col-lg-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="tio-add"></i> {{ translate('add') }}
                            </button>
                        </div>
                    </div>
                    @error('name_en')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                    @if($language)
                        @foreach(json_decode($language) as $lang)
                            @error('name.'.$lang)
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        @endforeach
                    @endif
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>{{ translate('name') }} ({{ strtoupper($default_lang) }})</th>
                                <th>{{ translate('delivery_charge') }}</th>
                                <th class="text-center">{{ translate('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($areas as $key => $area)
                                <tr>
                                    <td>{{ $areas->firstItem() + $key }}</td>
                                    <td>{{ $area->getNameByLang($default_lang) ?? '-' }}</td>
                                    <td>{{ Helpers::set_symbol($area->delivery_charge) }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-soft-primary" data-toggle="modal" data-target="#editAreaModal-{{ $area->id }}"><i class="tio-edit"></i></button>
                                        <form action="{{ route('admin.business-settings.areas.destroy', $area->id) }}" method="post" class="d-inline" onsubmit="return confirm('{{ translate('Are you sure') }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger"><i class="tio-delete"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">{{ translate('no_areas_added') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end">
                    {!! $areas->links() !!}
                </div>
            </div>
        </div>

        @foreach($areas as $area)
        <div class="modal fade" id="editAreaModal-{{ $area->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.business-settings.areas.update', $area->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">{{ translate('edit_area') }}</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            @if($language)
                                @foreach(json_decode($language) as $lang)
                                    <div class="mb-3" id="edit-{{ $lang }}-form-{{ $area->id }}">
                                        <label class="form-label">
                                            {{ translate('name') }} ({{ strtoupper($lang) }}) @if($lang == $default_lang)<span class="text-danger">*</span>@else
                                            <button type="button" class="btn btn-sm btn-outline-primary ms-2 translate-btn"
                                                    data-source-lang="{{ $default_lang }}" data-target-lang="{{ $lang }}"
                                                    data-source-id="area_edit_{{ $area->id }}_{{ $default_lang }}_name"
                                                    data-target-id="area_edit_{{ $area->id }}_{{ $lang }}_name" data-is-html="0"
                                                    title="{{ translate('Auto translate from') }} {{ strtoupper($default_lang) }}">
                                                <i class="tio-globe"></i> {{ translate('Auto translate') }}
                                            </button>
                                            @endif
                                        </label>
                                        <input type="text" name="name[{{ $lang }}]" id="area_edit_{{ $area->id }}_{{ $lang }}_name" class="form-control" value="{{ $area->getNameByLang($lang) ?? '' }}" maxlength="100" {{ $lang == $default_lang ? 'required' : '' }} >
                                    </div>
                                @endforeach
                            @else
                                <div class="mb-3">
                                    <label class="form-label">{{ translate('name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name[{{ $default_lang }}]" class="form-control" value="{{ $area->getNameByLang($default_lang) ?? '' }}" maxlength="100" required>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label">{{ translate('delivery_charge') }}</label>
                                <input type="number" name="delivery_charge" class="form-control" step="0.01" min="0" value="{{ $area->delivery_charge }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ translate('save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($language)
    @push('script_2')
    <script>
        $(function() {
            $(".lang_link").on("click", function(e) {
                e.preventDefault();
                $(".lang_link").removeClass("active");
                $(this).addClass("active");
                var form_id = $(this).attr("id");
                var lang = form_id.split("-")[0];
                $(".lang_form").addClass("d-none");
                $("#" + lang + "-form").removeClass("d-none");
            });
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
    </script>
    @endpush
    @endif
@endsection
