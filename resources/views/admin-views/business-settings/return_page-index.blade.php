@extends('layouts.admin.app')

@section('title', translate('Return Policy'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/pages.png')}}" alt="{{ translate('pages') }}">
                {{translate('pages')}}
            </h2>
        </div>

        <div class="inline-page-menu mb-4">
            @include('admin-views.business-settings.partial.page-nav')
        </div>

        <div class="card">
            <div class="card-header bg-light pages-card-header">
                <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                    <i class="tio-document me-2"></i>{{ translate('return_policy') }}
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.business-settings.return_page_update') }}" method="post" id="return-form">
                    @csrf
                    <input type="hidden" name="default_lang" value="{{ $defaultLang ?? 'ar' }}">
                    <div class="pages-editor">
                        <div class="d-flex align-items-center justify-content-between gap-3 mb-4 border rounded px-3 py-2 bg-white">
                            <label for="switcher_input" class="text-dark font-weight-bold mb-0">{{ translate('Check Status') }}</label>
                            <label class="switcher mb-0">
                                <input type="checkbox" id="switcher_input" class="switcher_input" name="status" value="1" {{ ($status ?? 0) == 1 ? 'checked' : '' }}>
                                <span class="switcher_control"></span>
                            </label>
                        </div>

                        @if(!empty($langs))
                        <ul class="nav nav-tabs mb-4 max-content">
                            @foreach($langs as $lang)
                            <li class="nav-item">
                                <a class="nav-link lang_link {{ $lang == ($defaultLang ?? 'ar') ? 'active' : '' }}" href="#"
                                   id="{{ $lang }}-link">
                                    {{ \App\CentralLogics\Helpers::get_language_name($lang) }}({{ strtoupper($lang) }})
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        @endif

                        @foreach($langs ?? ['ar'] as $lang)
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-0 lang_form {{ $lang != ($defaultLang ?? 'ar') ? 'd-none' : '' }}"
                                     id="{{ $lang }}-form">
                                    @if($lang != ($defaultLang ?? 'ar'))
                                    <div class="mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary translate-btn"
                                                data-source-lang="{{ $defaultLang ?? 'ar' }}" data-target-lang="{{ $lang }}"
                                                data-source-id="{{ $defaultLang ?? 'ar' }}_editor" data-target-id="{{ $lang }}_editor" data-is-html="1"
                                                title="{{ translate('Auto translate from') }} {{ strtoupper($defaultLang ?? 'ar') }}">
                                            <i class="tio-globe"></i> {{ translate('Auto translate') }}
                                        </button>
                                    </div>
                                    @endif
                                    <div id="{{ $lang }}_editor" class="min-h-15">{!! \App\CentralLogics\Helpers::sanitizeHtmlForDisplay($contentByLang[$lang] ?? '') !!}</div>
                                    <input type="hidden" name="content[{{ $lang }}]" id="{{ $lang }}_hiddenArea" value="">
                                </div>
                            </div>
                        </div>
                        @endforeach

                        <div class="d-flex flex-wrap justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button type="reset" class="btn btn-secondary px-4 min-w-120">{{ translate('reset') }}</button>
                            <button type="{{ config('app.mode') != 'demo' ? 'submit' : 'button' }}"
                                class="btn btn-primary px-4 min-w-120 demo-form-submit">{{ translate('save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
@endsection


@push('script_2')
    @include('admin-views.business-settings.partial.summernote-editor-scripts')
    <script>
        $(document).ready(function () {
            var langs = @json($langs ?? ['ar']);
            var isRtl = '{{ session("local", "ar") }}' === 'ar';

            langs.forEach(function(lang) {
                var $el = $('#' + lang + '_editor');
                if ($el.length) {
                    $el.summernote({
                        height: 320,
                        toolbar: [
                            ['style', ['style', 'bold', 'italic', 'underline', 'strikethrough', 'clear']],
                            ['font', ['fontname', 'fontsize', 'color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['table', ['table']],
                            ['insert', ['link', 'picture', 'video', 'hr']],
                            ['view', ['fullscreen', 'codeview', 'help']]
                        ],
                        fontNames: ['Arial', 'Cairo', 'Helvetica', 'Times New Roman'],
                        direction: isRtl ? 'rtl' : 'ltr',
                        lang: isRtl ? 'ar-AR' : 'en-US',
                        dialogsInBody: true
                    });
                }
            });

            $(".lang_link").click(function (e) {
                e.preventDefault();
                $(".lang_link").removeClass('active');
                $(".lang_form").addClass('d-none');
                var lang = this.id.split("-")[0];
                $(this).addClass('active');
                $("#" + lang + "-form").removeClass('d-none');
            });

            $('#return-form').on('submit', function () {
                langs.forEach(function(lang) {
                    var $ed = $('#' + lang + '_editor');
                    if ($ed.length) {
                        $('#' + lang + '_hiddenArea').val($ed.summernote('code') || '');
                    }
                });
            });

            $(document).on('click', '.translate-btn', function() {
                var btn = $(this);
                var sourceId = btn.data('source-id');
                var targetId = btn.data('target-id');
                var isHtml = btn.data('is-html') == 1;
                var sourceLang = btn.data('source-lang');
                var targetLang = btn.data('target-lang');
                var text = isHtml ? ($('#' + sourceId).summernote('code') || '') : ($('#' + sourceId).val() || '');
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
                            if (isHtml) {
                                $('#' + targetId).summernote('code', res.translated_text);
                            } else {
                                $('#' + targetId).val(res.translated_text);
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
