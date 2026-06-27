@extends('layouts.admin.app')

@section('title', translate('Mail Settings'))

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

        <div class="col-xl-8 p-0">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="bg-light rounded p-3">
                        <div class="position-relative">
                            <button class="btn btn--reset min-w-120" type="button" data-toggle="collapse"
                                    data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                <i class="tio-email-outlined"></i>
                                {{translate('test_your_email_integration')}}
                            </button>
                        </div>

                        <div class="collapse" id="collapseExample">
                            <form class="pt-3" action="javascript:">
                                <div class="row g-2 align-items-end">
                                    <div class="col-sm-8">
                                        <div class="form-group mb-0">
                                            <label for="inputPassword2" class="sr-only">{{translate('mail')}}</label>
                                            <input type="email" id="test-email" class="form-control" placeholder="Ex : jhon@email.com">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <button type="button" id="send-mail"
                                                class="btn btn-primary min-w-120 h-100 btn-block">
                                            <i class="tio-telegram"></i>
                                            {{translate('send_mail')}}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            @php($config=\App\Models\BusinessSetting::where(['key'=>'mail_config'])->first())
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="mb-0">{{ translate('Mail_Config') }}</h5>
                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#mailConfigHelpModal" title="{{ translate('help_mail_config_btn') }}">
                    <i class="tio-book-outlined"></i> {{ translate('help_mail_config_btn') }}
                </button>
            </div>
            @php($data=json_decode($config?->value ?? '{}', true) ?? [])
            @php($status=($data['status']??0)== 1 ? 0 : 1)
            <div class="card-body">
                <div class="bg-light rounded p-3">
                <div class="d-flex flex-wrap mb-3 align-items-center justify-content-between border rounded px-3 py-2 bg-white">
                    <label class="control-label h5 text-capitalize mb-0">{{translate('mail configuration status')}}</label>
                    <div class="custom--switch">
                        <input type="checkbox" name="status" value="" id="toggle-mail-status" switch="primary"
                               data-route="{{route('admin.business-settings.mail-config.status',[$status])}}"
                               class="toggle-switch-input" {{ ($data['status']??0) ==  1 ? 'checked' : '' }}>
                        <label for="toggle-mail-status" data-on-label="on" data-off-label="off"></label>
                    </div>
                </div>
                <form action="{{route('admin.business-settings.mail-config')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group mb-0">
                                <label>{{translate('mailer')}} {{translate('name')}} <span class="text-danger">*</span></label>
                                <input type="text" placeholder="{{ translate('ex : Alex') }}" class="form-control" name="name"
                                    value="{{config('app.mode')=='demo'?'':($data['name']??'')}}" required>
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group mb-0">
                                <label>{{translate('host')}} <span class="text-danger">*</span></label>
                                <input type="text" placeholder="Ex : smtp.gmail.com" class="form-control" name="host"
                                    value="{{config('app.mode')=='demo'?'':($data['host']??'')}}" required>
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group mb-0">
                                <label>{{translate('driver')}} <span class="text-danger">*</span></label>
                                <input type="text" placeholder="Ex : smtp" class="form-control" name="driver"
                                    value="{{config('app.mode')=='demo'?'':($data['driver']??'smtp')}}" required>
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group mb-0">
                                <label>{{translate('port')}} <span class="text-danger">*</span></label>
                                <input type="text" placeholder="Ex : 587" class="form-control" name="port"
                                    value="{{config('app.mode')=='demo'?'':($data['port']??'')}}" required>
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group mb-0">
                                <label>{{translate('username')}} <span class="text-danger">*</span></label>
                                <input type="text" placeholder="{{ translate('ex : ex@yahoo.com') }}" class="form-control" name="username"
                                    value="{{config('app.mode')=='demo'?'':($data['username']??'')}}" required>
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group mb-0">
                                <label>{{translate('email')}} {{translate('id')}} <span class="text-danger">*</span></label>
                                <input type="email" placeholder="{{ translate('ex : ex@yahoo.com') }}" class="form-control" name="email"
                                    value="{{config('app.mode')=='demo'?'':($data['email_id']??'')}}" required>
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group mb-0">
                                <label>{{translate('encryption')}} <span class="text-danger">*</span></label>
                                <input type="text" placeholder="{{ translate('ex : tls') }}" class="form-control" name="encryption"
                                    value="{{config('app.mode')=='demo'?'':($data['encryption']??'')}}" required>
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group mb-0">
                                <label>{{translate('password')}} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="password"
                                    value="{{config('app.mode')=='demo'?'':($data['password']??'')}}" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="reset" class="btn btn--reset min-w-120">{{translate('reset')}}</button>
                        <button type="{{config('app.mode')!='demo'?'submit':'button'}}"
                                class="btn btn-primary min-w-120 demo-form-submit">{{translate('save')}}
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>

    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'mailConfigHelpModal', 'titleKey' => 'help_mail_config_title', 'pageKey' => 'help_mail_config_page'])
@endsection

@push('script_2')
    <script>
        "use strict"

        $('#toggle-mail-status').on('click', function (){
            let route = $(this).data('route');
            $.get({
                url: route,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    toastr.success(data.message);
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });

        function ValidateEmail(inputText) {
            var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            if (inputText.match(mailformat)) {
                return true;
            } else {
                return false;
            }
        }

        $('#send-mail').on('click', function (){
            if (ValidateEmail($('#test-email').val())) {
                Swal.fire({
                    title: '{{translate('Are you sure?')}}?',
                    text: "{{translate('a_test_mail_will_be_sent_to_your_email')}}!",
                    showCancelButton: true,
                    confirmButtonColor: '#673ab7',
                    cancelButtonColor: 'secondary',
                    confirmButtonText: '{{translate('Yes')}}!'
                }).then((result) => {
                    if (result.value) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: "{{route('admin.business-settings.mail-send')}}",
                            method: 'POST',
                            data: {
                                "email": $('#test-email').val()
                            },
                            beforeSend: function () {
                                $('#loading').show();
                            },
                            success: function (data) {
                                if (data.success === 2) {
                                    toastr.error('{{translate('email_configuration_error')}} !!');
                                } else if (data.success === 1) {
                                    toastr.success('{{translate('email_configured_perfectly!')}}!');
                                } else {
                                    toastr.info('{{translate('email_status_is_not_active')}}!');
                                }
                            },
                            complete: function () {
                                $('#loading').hide();

                            }
                        });
                    }
                })
            } else {
                toastr.error('{{translate('invalid_email_address')}} !!');
            }
        });

    </script>
@endpush
