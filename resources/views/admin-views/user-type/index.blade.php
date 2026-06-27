@extends('layouts.admin.app')

@section('title', translate('User Types'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('assets/admin/img/icons/customer.png') }}" alt="{{ translate('user types') }}">
                {{ translate('User Types') }}
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#userTypeInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_user_type_btn') }}
            </button>
        </div>

        @include('admin-views.partials._help-instructions-modal', ['modalId' => 'userTypeInstructionsModal', 'titleKey' => 'help_user_type_title', 'pageKey' => 'help_user_type_page'])

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="mb-3">{{ translate('Add User Type') }}</h5>
                <form action="{{ route('admin.user-type.store') }}" method="post">
                    @csrf
                    <ul class="nav nav-tabs mb-3 max-content">
                        @foreach($languages as $lang)
                            <li class="nav-item">
                                <a class="nav-link lang_link {{ $lang === $defaultLang ? 'active' : '' }}" href="#"
                                   id="create-{{ $lang }}-link">
                                    {{ \App\CentralLogics\Helpers::get_language_name($lang) }}({{ strtoupper($lang) }})
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="row align-items-end g-3 bg-light rounded p-3 mb-2">
                        @foreach($languages as $index => $lang)
                            <div class="col-12 col-md-8 col-lg-9 lang_form {{ $lang !== $defaultLang ? 'd-none' : '' }}"
                                 id="create-{{ $lang }}-form">
                                <label class="input-label">
                                    {{ translate('Name') }} ({{ strtoupper($lang) }})
                                    @if($lang === $defaultLang)
                                        <span class="text-danger">*</span>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2 translate-btn"
                                                data-source-id="create-{{ $defaultLang }}-name"
                                                data-target-id="create-{{ $lang }}-name"
                                                data-source-lang="{{ $defaultLang }}"
                                                data-target-lang="{{ $lang }}"
                                                data-is-html="0"
                                                title="{{ translate('Auto translate from') }} {{ strtoupper($defaultLang) }}">
                                            <i class="tio-globe"></i> {{ translate('Auto translate') }}
                                        </button>
                                    @endif
                                </label>
                                <input type="text"
                                       name="name[]"
                                       id="create-{{ $lang }}-name"
                                       class="form-control"
                                       placeholder="{{ translate('e.g. زبون مميز') }}"
                                       maxlength="255"
                                       value="{{ old('name.' . $index) }}"
                                       {{ $lang === $defaultLang ? 'required' : '' }}>
                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                            </div>
                        @endforeach
                        <div class="col-12 col-md-4 col-lg-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 mt-0">{{ translate('Add') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ translate('User Types List') }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>{{ translate('Name') }}</th>
                            <th>{{ translate('Default') }}</th>
                            <th class="text-center">{{ translate('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($userTypes as $key => $type)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $type->name }}</td>
                                <td>
                                    @if($type->is_default)
                                        <span class="badge badge-soft-success">{{ translate('Default') }}</span>
                                    @else
                                        <form action="{{ route('admin.user-type.set-default', $type->id) }}" method="post" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-soft-primary">{{ translate('Set default') }}</button>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @php($nameMap = collect($type->translations)->pluck('value','locale')->toArray())
                                        @php($nameMap[$defaultLang] = $type->getRawOriginal('name'))
                                        <button type="button"
                                                class="btn btn-outline-primary btn-sm square-btn edit-type"
                                                data-id="{{ $type->id }}"
                                                data-names='@json($nameMap, JSON_UNESCAPED_UNICODE)'>
                                            <i class="tio-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.user-type.destroy', $type->id) }}" method="post" class="d-inline" onsubmit="return confirm('{{ translate('Are you sure') }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm square-btn"><i class="tio-delete"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">{{ translate('No user types yet. Add one above.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editTypeForm" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">{{ translate('Edit User Type') }}</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs mb-3 max-content">
                            @foreach($languages as $lang)
                                <li class="nav-item">
                                    <a class="nav-link edit-lang-link {{ $lang === $defaultLang ? 'active' : '' }}" href="#"
                                       id="edit-{{ $lang }}-link">
                                        {{ \App\CentralLogics\Helpers::get_language_name($lang) }}({{ strtoupper($lang) }})
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        @foreach($languages as $lang)
                            <div class="form-group edit-lang-form {{ $lang !== $defaultLang ? 'd-none' : '' }}"
                                 id="edit-{{ $lang }}-form">
                                <label class="input-label">
                                    {{ translate('Name') }} ({{ strtoupper($lang) }})
                                    @if($lang === $defaultLang)
                                        <span class="text-danger">*</span>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2 translate-btn"
                                                data-source-id="editTypeName-{{ $defaultLang }}"
                                                data-target-id="editTypeName-{{ $lang }}"
                                                data-source-lang="{{ $defaultLang }}"
                                                data-target-lang="{{ $lang }}"
                                                data-is-html="0"
                                                title="{{ translate('Auto translate from') }} {{ strtoupper($defaultLang) }}">
                                            <i class="tio-globe"></i> {{ translate('Auto translate') }}
                                        </button>
                                    @endif
                                </label>
                                <input type="text"
                                       name="name[]"
                                       id="editTypeName-{{ $lang }}"
                                       class="form-control"
                                       {{ $lang === $defaultLang ? 'required' : '' }}
                                       maxlength="255">
                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
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

        $(function () {
            $('.lang_link').on('click', function (e) {
                e.preventDefault();
                $('.lang_link').removeClass('active');
                $('.lang_form').addClass('d-none');
                $(this).addClass('active');
                var lang = this.id.replace('create-', '').replace('-link', '');
                $('#create-' + lang + '-form').removeClass('d-none');
            });

            $('.edit-lang-link').on('click', function (e) {
                e.preventDefault();
                $('.edit-lang-link').removeClass('active');
                $('.edit-lang-form').addClass('d-none');
                $(this).addClass('active');
                var lang = this.id.replace('edit-', '').replace('-link', '');
                $('#edit-' + lang + '-form').removeClass('d-none');
            });

            $('.edit-type').on('click', function () {
                var id = $(this).data('id');
                var names = $(this).data('names') || {};
                $('#editTypeForm').attr('action', '{{ url("admin/user-types") }}/' + id);

                @foreach($languages as $lang)
                    $('#editTypeName-{{ $lang }}').val(names['{{ $lang }}'] || '');
                @endforeach

                $('.edit-lang-link').removeClass('active');
                $('.edit-lang-form').addClass('d-none');
                $('#edit-{{ $defaultLang }}-link').addClass('active');
                $('#edit-{{ $defaultLang }}-form').removeClass('d-none');
                $('#editTypeModal').modal('show');
            });
        });
    </script>
@endpush
