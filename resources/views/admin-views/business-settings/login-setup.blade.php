@extends('layouts.admin.app')

@section('title', translate('Customer Login Setup'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/business-setup.png')}}" alt="{{ translate('business_setup_image') }}">
                {{translate('business_Setup')}}
            </h2>
        </div>

        <div class="inline-page-menu mb-4">
            @include('admin-views.business-settings.partial.business-setup-nav')
        </div>

        <div class="card">
            <div class="p-3 border-bottom d-flex flex-wrap justify-content-between align-items-start gap-2">
                <div>
                    <h4 class="mb-0">{{translate('Setup Login Option')}}</h4>
                    <p class="mb-0">{{translate('The option you select customer will have the option to login')}}</p>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#loginSetupHelpModal" title="{{ translate('help_login_setup_btn') }}">
                    <i class="tio-book-outlined"></i> {{ translate('help_login_setup_btn') }}
                </button>
            </div>

            <div class="card-body">
                <form id="login-setup-form" action="{{route('admin.business-settings.login-setup-update')}}" method="post">
                    @csrf
                    <div class="login-option mt-2 mb-4">
                        <div class="bg-light rounded p-3">
                        <div class="row g-3">
                            <div class="col-lg-4 col-sm-6 mb-2">
                                <div class="form-control d-flex justify-content-between align-items-center gap-3">
                                    <label class="text-dark mb-0">{{translate('Manual Login')}}
                                        <i class="tio-info-outlined" data-toggle="tooltip" data-placement="top"
                                           title="{{ translate('By enabling manual login, customers will get the option to create account and log in using necessary credentials & password in the app & website.') }}">
                                        </i>
                                    </label>
                                    <input name="manual_login" type="checkbox" {{ $loginOptions->manual_login == 1 ? 'checked' : '' }} id="otp-manual_login">
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 mb-2">
                                <div class="form-control d-flex justify-content-between align-items-center gap-3">
                                    <label class="text-dark mb-0">{{translate('OTP Login')}}
                                        <i class="tio-info-outlined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="{{ translate('With OTP Login, customers can log in using their phone number. while new customers can create accounts instantly.') }}">
                                        </i>
                                    </label>
                                    <input name="otp_login" type="checkbox" {{ $loginOptions->otp_login == 1 ? 'checked' : '' }} id="otp-login">
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-6 mb-2">
                                <div class="form-control d-flex justify-content-between align-items-center gap-3">
                                    <label class="text-dark mb-0">{{translate('Social Media Login')}}
                                        <i class="tio-info-outlined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="{{ translate('With Social Login, customers can log in using social media credentials. while new customers can create accounts instantly.') }}">
                                        </i>
                                    </label>
                                    <input name="social_media_login" type="checkbox" {{ $loginOptions->social_media_login == 1 ? 'checked' : '' }} id="social-media-login">
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>

                    <div class="social-media-setup my-4" id="social-media-setup" style="display: none;">
                        <div class="mb-2 social-media-setup-header">
                            <h4 class="">{{translate('Social Media Login Setup')}}</h4>
                        </div>
                        <div class="bg-light rounded p-3">
                            <h4>{{ translate('Choose Social Media') }}</h4>
                            <div class="row g-3">
                                <div class="col-lg-4 col-sm-6 mb-2">
                                    <div class="form-control d-flex justify-content-between align-items-center gap-3">
                                        <label class="text-dark mb-0">{{translate('Google')}}
                                            <i class="tio-info-outlined"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="{{ translate('Enabling Google Login, customers can log in to the site using their existing Gmail credentials.') }}">
                                            </i>
                                        </label>
                                        <input name="google" type="checkbox" {{ $socialMediaLoginOptions->google == 1 ? 'checked' : '' }} id="google">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="otp-verification my-4">
                        <div class="mb-2">
                            <h4 class="mb-0">{{translate('OTP Verification')}}</h4>
                            <a>{{translate('The option you select will need to be verified by the customer')}}</a>
                        </div>
                        <div class="bg-light rounded p-3">
                            <div class="row g-3">
                                <div class="col-lg-4 col-sm-6 mb-2">
                                    <div class="form-control d-flex justify-content-between align-items-center gap-3">
                                        <label class="text-dark mb-0">{{translate('Email Verification')}}
                                            <i class="tio-info-outlined"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="{{ translate('If Email verification is on, Customers must verify their email address with an OTP to complete the process.') }}">
                                            </i>
                                        </label>
                                        <input name="email_verification" type="checkbox" {{ $emailVerification == 1 ? 'checked' : '' }} id="email-verification">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 mb-2">
                                    <div class="form-control d-flex justify-content-between align-items-center gap-3">
                                        <label class="text-dark mb-0">{{translate('Phone Number Verification')}}
                                            <i class="tio-info-outlined"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="{{ translate('If Phone Number verification is on, Customers must verify their Phone Number with an OTP to complete the process.') }}">
                                            </i>
                                        </label>
                                        <input class="" name="phone_verification" type="checkbox" {{ $phoneVerification == 1 ? 'checked' : '' }} id="phone-verification">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mb-3">
                        <button type="reset" class="btn btn--reset min-w-120" id="reset">{{translate('reset')}}</button>
                        <button type="{{config('app.mode')!='demo'?'submit':'button'}}"
                                class="btn btn-primary min-w-120 demo-form-submit">{{translate('save')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'loginSetupHelpModal', 'titleKey' => 'help_login_setup_title', 'pageKey' => 'help_login_setup_page'])

    <!-- Modal -->
    <div class="modal fade" id="smsConfigModal" tabindex="-1" role="dialog" aria-labelledby="smsConfigModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div>
                        <img class="w-100px" src="{{ asset('assets/admin/img/OTP-Verification.png') }}" alt="{{ translate('image') }}">
                    </div>
                    <div class="py-4">
                        <h5 class="modal-title" id="smsConfigModalLabel">{{ translate('Set Up SMS Configuration/Firebase Auth First') }}</h5>
                    </div>
                    <p>{{ translate('It looks like your SMS configuration is not set up yet. To enable the OTP system, please set up the SMS configuration/Firebase Auth first.') }}</p>
                </div>
                <div class="text text-center mb-5">
                    <a href="{{route('admin.business-settings.firebase-auth')}}" target="_blank" class="btn btn-primary min-w-120">{{ translate('Go to Firebase Auth') }}</a>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>

        $(document).ready(function() {
            toggleSocialMediaSetup();

            $('#social-media-login').change(function() {
                toggleSocialMediaSetup();
            });

            $('#login-setup-form').on('reset', function() {
                setTimeout(function() {
                    toggleSocialMediaSetup();
                }, 10);
            });

            function toggleSocialMediaSetup() {
                if ($('#social-media-login').is(':checked')) {
                    $('#social-media-setup').show();
                } else {
                    $('#social-media-setup').hide();
                }
            }

            $('#otp-login').change(function() {
                if ($(this).is(':checked')) {
                    $('#phone-verification').prop('checked', true);
                }
            });

        });


        $('#login-setup-form').submit(function(event) {
            let manualLogin = $('#otp-manual_login').prop('checked');
            let otpLogin = $('#otp-login').prop('checked');
            let socialMediaLogin = $('#social-media-login').prop('checked');

            if (!manualLogin && !otpLogin && !socialMediaLogin) {
                event.preventDefault();
                Swal.fire({
                    type: 'warning',
                    title: '{{ translate("No Login Option Selected") }}!',
                    text: '{{ translate("Please select at least one login option.") }}',
                    confirmButtonText: '{{ translate("OK") }}',
                    confirmButtonColor: '#673ab7',
                });
            }
        });

    </script>
@endpush
