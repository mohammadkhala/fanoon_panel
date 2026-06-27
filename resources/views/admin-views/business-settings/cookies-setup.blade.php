@extends('layouts.admin.app')

@section('title', translate('Cookies Setup'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/business-setup.png')}}" alt="{{ translate('settings-image') }}">
                {{translate('business_Setup')}}
            </h2>
        </div>

        <div class="inline-page-menu mb-4">
            @include('admin-views.business-settings.partial.business-setup-nav')
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="mb-0">{{ translate('cookies_setup') }}</h5>
                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#cookiesSetupHelpModal" title="{{ translate('help_cookies_setup_btn') }}">
                    <i class="tio-book-outlined"></i> {{ translate('help_cookies_setup_btn') }}
                </button>
            </div>
            <div class="card-body">
                <form action="{{route('admin.business-settings.update-cookies')}}" method="post">
                    @csrf
                    <input type="hidden" name="default_lang" value="{{ $defaultLang ?? 'ar' }}">
                    <div class="bg-light rounded p-3">
                        <div class="d-flex align-items-center justify-content-between gap-3 mb-3 border rounded px-3 py-2 bg-white">
                            <span class="text-dark font-weight-bold">{{ translate('cookies_setup') }}</span>
                            <label class="switch-custom-label toggle-switch toggle-switch-sm d-inline-flex mb-0">
                                <input type="checkbox" name="status" value="1" class="toggle-switch-input" {{ ($cookies['status'] ?? 0) == 1 ? 'checked' : '' }}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>

                        @if(!empty($langs) && count($langs) > 1)
                        <ul class="nav nav-tabs mb-3 max-content">
                            @foreach($langs as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link {{ $lang == ($defaultLang ?? 'ar') ? 'active' : '' }}" href="#" id="{{ $lang }}-link">
                                        {{ \App\CentralLogics\Helpers::get_language_name($lang) }} ({{ strtoupper($lang) }})
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        @endif

                        <div class="row g-3">
                            @foreach($langs ?? ['ar'] as $lang)
                            <div class="col-md-12 lang_form {{ $lang != ($defaultLang ?? 'ar') ? 'd-none' : '' }}" id="{{ $lang }}-form">
                                <label class="form-label">
                                    {{ translate('cookies_text') }} ({{ strtoupper($lang) }})
                                    @if($lang != ($defaultLang ?? 'ar'))
                                    <button type="button" class="btn btn-sm btn-outline-primary ms-2 translate-btn"
                                            data-source-lang="{{ $defaultLang ?? 'ar' }}" data-target-lang="{{ $lang }}"
                                            data-source-id="{{ $defaultLang ?? 'ar' }}_cookies_text"
                                            data-target-id="{{ $lang }}_cookies_text" data-is-html="0"
                                            title="{{ translate('Auto translate from') }} {{ strtoupper($defaultLang ?? 'ar') }}">
                                        <i class="tio-globe"></i> {{ translate('Auto translate') }}
                                    </button>
                                    @endif
                                </label>
                                <textarea name="text[{{ $lang }}]" id="{{ $lang }}_cookies_text" class="form-control" rows="6"
                                          placeholder="{{ translate('Cookies text') }}" {{ $lang == ($defaultLang ?? 'ar') ? 'required' : '' }}>{{ $contentByLang[$lang] ?? '' }}</textarea>
                            </div>
                            @endforeach

                            <div class="col-md-12 d-flex justify-content-end gap-2">
                                <button type="reset" class="btn btn--reset min-w-120">{{translate('reset')}}</button>
                                <button type="{{ config('app.mode') != 'demo' ? 'submit' : 'button' }}"
                                        class="btn btn-primary min-w-120 demo-form-submit">{{translate('update')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'cookiesSetupHelpModal', 'titleKey' => 'help_cookies_setup_title', 'pageKey' => 'help_cookies_setup_page'])
@endsection

@if(!empty($langs) && count($langs) > 1)
@push('script_2')
<script>
    $(function() {
        $(".lang_link").on("click", function(e) {
            e.preventDefault();
            $(".lang_link").removeClass("active");
            $(this).addClass("active");
            var lang = $(this).attr("id").split("-")[0];
            $(".lang_form").addClass("d-none");
            $("#" + lang + "-form").removeClass("d-none");
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
                data: { _token: '{{ csrf_token() }}', text: text, source_lang: sourceLang, target_lang: targetLang, is_html: isHtml ? 1 : 0 },
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
                    toastr.error((xhr.responseJSON && xhr.responseJSON.message) || '{{ translate("Translation failed") }}', { CloseButton: true, ProgressBar: true });
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="tio-globe"></i> {{ translate("Auto translate") }}');
                }
            });
        });
    });
</script>
@endpush
@endif
