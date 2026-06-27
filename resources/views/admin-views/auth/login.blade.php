<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, shrink-to-fit=no, viewport-fit=cover">
    <meta name="robots" content="nofollow, noindex, max-snippet:1, max-video-preview:1, max-image-preview:standard">
    <!-- Title -->
    <title>{{ translate('Admin') }} | {{ translate('Login') }}</title>

    @php
        $icon = Helpers::get_business_settings('fav_icon');
        $iconPath = $icon ? asset('storage/ecommerce/' . $icon) : asset('assets/admin/img/160x160/img2.jpg');
    @endphp
    <link rel="icon" type="image/x-icon" href="{{ $iconPath }}">
    <link rel="shortcut icon" href="#">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/vendor/icon-set/style.css')}}">

    <link rel="stylesheet" href="{{asset('assets/admin/css/theme.minc619.css?v=1.0')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/custom.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/toastr.css')}}">
    <style>body{font-family:'Cairo',sans-serif !important;}</style>
</head>

<body class="login-redesign-rtl">
<main id="content" role="main" class="main">
    <div class="login-split d-flex flex-column flex-md-row min-vh-100">
        {{-- Logo mobile only (above form on small screens) --}}
        <div class="login-logo-mobile d-flex d-md-none justify-content-center align-items-center">
            <div class="login-blob-mobile">
                <img class="login-page-logo" src="{{ $logo }}" alt="{{ translate('Image Description') }}">
            </div>
        </div>
        {{-- Form section (left in RTL) --}}
        <div class="login-form-section flex-grow-1 d-flex justify-content-center align-items-center md-w-50">
            <div class="login-form-inner">
                <form id="form-id" class="login-form" action="{{route('admin.auth.login')}}" method="post">
                    @csrf

                    <h2 class="login-title">{{ translate('login_welcome') }}</h2>
                    <p class="login-subtitle">{{ translate('login_to_continue') }}</p>

                    <div class="js-form-message form-group login-input-wrap">
                        <label class="login-label" for="signinSrEmail">{{ translate('email') }}</label>
                        <div class="login-input-group login-has-icon">
                            <input type="email" class="form-control login-input" name="email" id="signinSrEmail"
                                    tabindex="1" placeholder="{{ translate('type_your_email') }}" required
                                    data-msg="Please enter a valid email address.">
                            <i class="tio-email-outlined login-input-icon login-static-icon"></i>
                        </div>
                    </div>

                    <div class="js-form-message form-group login-input-wrap">
                        <label class="login-label" for="signupSrPassword">{{ translate('password') }}</label>
                        <div class="login-input-group">
                            <input type="password" class="js-toggle-password form-control login-input"
                                    name="password" id="signupSrPassword" placeholder="{{ translate('type_your_password') }}"
                                    required data-msg="{{ translate('Your password is invalid. Please try again.') }}"
                                    data-hs-toggle-password-options='{
                                        "target": "#changePassTarget",
                                        "defaultClass": "tio-hidden-outlined",
                                        "showClass": "tio-visible-outlined",
                                        "classChangeTarget": "#changePassIcon"
                                    }'>
                            <div id="changePassTarget" class="login-input-icon-wrap">
                                <a class="login-input-icon-btn" href="javascript:">
                                    <i id="changePassIcon" class="tio-visible-outlined"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="login-options d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="termsCheckbox" name="remember">
                            <label class="custom-control-label login-remember" for="termsCheckbox">
                                {{ translate('remember me') }}
                            </label>
                        </div>
                        <button type="submit" class="btn login-btn" id="signInBtn">{{ translate('login') }}</button>
                    </div>
                </form>

                @if(config('app.mode')=='demo')
                <div class="login-demo-footer d-flex justify-content-between mt-4 pt-3">
                    <div class="font-weight-medium text-muted small">
                        <div>{{ translate('Email : admin@admin.com') }}</div>
                        <div>{{ translate('Password : 12345678') }}</div>
                    </div>
                    <button type="button" class="btn btn-sm login-copy" id="copyButton">
                        <i class="tio-copy"></i>
                    </button>
                </div>
                @endif

            </div>
        </div>

        {{-- Illustration / Logo section (right in RTL) --}}
        <div class="login-illustration-section d-none d-md-flex justify-content-center align-items-center flex-grow-1 md-w-50">
            <div class="login-blob-wrap">
                <div class="login-blob">
                    <img class="login-page-logo" src="{{ $logo }}" alt="{{ translate('Image Description') }}">
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{asset('assets/admin/js/vendor.min.js')}}"></script>
<script src="{{asset('assets/admin/js/theme.min.js')}}"></script>
<script src="{{asset('assets/admin/js/toastr.js')}}"></script>
{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', '{{ translate("error") }}', {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif

<script>
    "use strict";

    $(document).on('ready', function () {
        $('.js-toggle-password').each(function () {
            new HSTogglePassword(this).init()
        });

        $('.js-validate').each(function () {
            $.HSCore.components.HSValidation.init($(this));
        });

        let $bgImg = $("[data-bg-img]");
        $bgImg.css("background-image", function () {
            return 'url("' + $(this).data("bg-img") + '")';
        }).removeAttr("data-bg-img").addClass("bg-img");
    });

    $('#copyButton').on('click', function() {
        copy_cred();
    });

    @if(config('app.mode')=='demo')
        function copy_cred() {
            $('#signinSrEmail').val('admin@admin.com');
            $('#signupSrPassword').val('12345678');
            toastr.success('Copied successfully!', 'Success!', {
            CloseButton: true,
            ProgressBar: true
        });
        }
   @endif
</script>

<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('assets/admin/vendor/babel-polyfill/polyfill.min.js')}}"><\/script>');
</script>
</body>
</html>
