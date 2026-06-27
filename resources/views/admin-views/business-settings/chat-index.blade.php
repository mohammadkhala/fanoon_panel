@extends('layouts.admin.app')

@section('title', translate('Social Media chat'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/business-setup.png')}}" alt="{{ translate('business_setup_image') }}">
                {{translate('business_Setup')}}
            </h2>
        </div>

        <div class="inline-page-menu my-4">
            @include('admin-views.business-settings.partial.business-setup-nav')
        </div>

        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.business-settings.update-social-media-chat')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="bg-light rounded p-3">
                                <div class="row g-3">
                                    @php($whatsapp=\App\Models\BusinessSetting::where('key','whatsapp')->first()?->value)
                                    @php($whatsapp_data=json_decode($whatsapp,true) ?? [])
                                    @php($whatsapp_data=array_merge(['status'=>0,'number'=>''],$whatsapp_data))
                                    @php($whatsapp_data['number']=$whatsapp_data['number']??$whatsapp_data['value']??'')
                                    <div class="col-12">
                                        <div class="card h-100 mb-0">
                                            <div class="card-body form-group mb-0">
                                                <label class="toggle-switch d-flex align-items-center mb-3" for="whatsapp_status">
                                                <span class="toggle-switch-content ml-0">
                                                    <span class="d-block font-weight-bold">{{translate('whatsapp')}}</span>
                                                </span>
                                                    <input type="checkbox" name="whatsapp_status" class="toggle-switch-input"
                                                           value="1" id="whatsapp_status" {{($whatsapp_data['status']??0)==1?'checked':''}}>
                                                    <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                </label>
                                                <label class="text-capitalize form-label d-flex align-items-center gap-2">
                                                    {{translate('Number')}}
                                                    <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" data-toggle="modal" data-target="#whatsappChatHelpModal" title="{{ translate('help_whatsapp_chat_btn') }}">
                                                        <i class="tio-book-outlined"></i>
                                                    </button>
                                                </label>
                                                <input type="tel" id="whatsapp_number_input" class="form-control" placeholder="{{ translate('whatsapp_number_example') }}" value="{{$whatsapp_data['number'] ?? ''}}" autocomplete="tel">
                                                <small class="text-muted d-block mt-1">{{ translate('whatsapp_number_hint') }}</small>
                                                <input type="hidden" name="whatsapp_number" id="whatsapp_number">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- إخفاء تيليجرام/ماسنجر من الواجهة مع الحفاظ على القيم عند الحفظ --}}
                                    @php($telegram=\App\Models\BusinessSetting::where('key','telegram')->first()?->value)
                                    @php($telegram_data=json_decode($telegram,true) ?? [])
                                    @php($telegram_data=array_merge(['status'=>0,'user_name'=>''],$telegram_data))
                                    @if(($telegram_data['status'] ?? 0) == 1)
                                        <input type="hidden" name="telegram_status" value="1">
                                    @endif
                                    <input type="hidden" name="telegram_user_name" value="{{ $telegram_data['user_name'] ?? '' }}">

                                    @php($messenger=\App\Models\BusinessSetting::where('key','messenger')->first()?->value)
                                    @php($messenger_data=json_decode($messenger,true) ?? [])
                                    @php($messenger_data=array_merge(['status'=>0,'user_name'=>''],$messenger_data))
                                    @if(($messenger_data['status'] ?? 0) == 1)
                                        <input type="hidden" name="messenger_status" value="1">
                                    @endif
                                    <input type="hidden" name="messenger_user_name" value="{{ $messenger_data['user_name'] ?? '' }}">
                                </div>
                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <button type="reset" class="btn btn--reset min-w-120">{{translate('reset')}}</button>
                                    <button type="{{config('app.mode')!='demo'?'submit':'button'}}"
                                            class="btn btn-primary min-w-120 demo-form-submit">{{translate('update')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'whatsappChatHelpModal', 'titleKey' => 'help_whatsapp_chat_title', 'pageKey' => 'help_whatsapp_chat_page'])
@endsection

@push('script_2')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var input = document.getElementById('whatsapp_number_input');
        var hidden = document.getElementById('whatsapp_number');
        if (!input || !window.intlTelInput) return;

        var iti = window.intlTelInput(input, {
            initialCountry: "ps",
            showSelectedDialCode: true,
            separateDialCode: true,
            utilsScript: "{{ asset('assets/admin/intltelinput/js/utils.js') }}"
        });

        var stored = input.value.replace(/[^0-9]/g, '');
        if (stored) {
            iti.setNumber('+' + stored);
            hidden.value = stored;
        }

        function syncToHidden() {
            var full = iti.getNumber();
            hidden.value = full ? full.replace(/^\+/, '') : '';
        }

        input.addEventListener('blur', syncToHidden);
        input.addEventListener('change', syncToHidden);
        input.closest('form').addEventListener('submit', function() {
            syncToHidden();
        });
        input.closest('form').addEventListener('reset', function() {
            setTimeout(function() {
                iti.setNumber('');
                hidden.value = '';
            }, 0);
        });
    });
</script>
@endpush

