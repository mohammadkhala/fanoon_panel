<!DOCTYPE html>
@php
    $adminLocale = session('local', 'ar');
    $isRtl = ($adminLocale === 'ar');
@endphp
<html lang="{{ str_replace('_', '-', $adminLocale) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>
    @php($icon = \App\Models\BusinessSetting::where(['key' => 'fav_icon'])->first()?->value ?? '')
    <link rel="icon" type="image/x-icon" href="{{ $icon ? asset('storage/ecommerce/' . $icon) : '' }}">
    <link rel="shortcut icon" href="">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/vendor/icon-set/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/intltelinput/css/intlTelInput.css')}}">


    <link rel="stylesheet" href="{{asset('assets/admin/css/theme.minc619.css?v=1.0')}}">
    <link href="{{asset('assets/admin/css/dropzone.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/admin/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/custom.css')}}?v=2.7">
    <link rel="stylesheet" href="{{asset('css/demo.css')}}">

    @stack('css_or_js')

    <script src="{{asset('assets/admin/js/jquery.js')}}"></script>
    <script>
        (function () {
            window.lastAdminStoreData = window.lastAdminStoreData || null;

            /**
             * يزيل بقايا فتح المودال من Inline style حتى يعمل modal('show') لاحقاً
             * (تجنب display:none !important الذي كان يتعارض مع .modal.in و BS3).
             */
            window.adminPrepareNotifyModalForShow = function (modalId) {
                var el = document.getElementById(modalId);
                if (!el) {
                    return;
                }
                el.classList.remove('in', 'show');
                el.style.removeProperty('display');
                el.style.removeProperty('opacity');
            };

            window.adminStripNotifyModalsFromDom = function () {
                ['popup-modal', 'popup-modal-contact', 'popup-modal-type-approval'].forEach(function (id) {
                    var el = document.getElementById(id);
                    if (!el) {
                        return;
                    }
                    el.classList.remove('in', 'show');
                    el.style.removeProperty('display');
                    el.style.removeProperty('opacity');
                    el.setAttribute('aria-hidden', 'true');
                });
                document.querySelectorAll('.modal-backdrop').forEach(function (b) {
                    b.remove();
                });
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
            };

            window.adminGoAfterNotifyClick = function (url) {
                try {
                    if (typeof jQuery !== 'undefined') {
                        try {
                            jQuery('#popup-modal, #popup-modal-contact, #popup-modal-type-approval').modal('hide');
                        } catch (e) {}
                    }
                    window.adminStripNotifyModalsFromDom();
                } finally {
                    if (url) {
                        window.location.assign(url);
                    }
                }
            };

            function adminApplyNotifySnoozeFromButton(el) {
                var d = window.lastAdminStoreData;
                if (!d || !el) {
                    return;
                }
                if (el.closest('#popup-modal') && d.new_order != null) {
                    sessionStorage.setItem('elite_admin_snooze_order', String(d.new_order));
                }
                if (el.closest('#popup-modal-contact') && d.new_contact_us != null) {
                    sessionStorage.setItem('elite_admin_snooze_contact_us', String(d.new_contact_us));
                }
                if (el.closest('#popup-modal-type-approval') && d.pending_type_approval != null) {
                    sessionStorage.setItem('elite_admin_snooze_type_approval', String(d.pending_type_approval));
                }
            }

            function adminNotifyClickCapture(e) {
                /* فقط داخل مودالات الإشعار — لا نلتقط Swal / Toastr / بقية الصفحة */
                if (!e.target.closest('#popup-modal, #popup-modal-contact, #popup-modal-type-approval')) {
                    return;
                }
                var stripBtn = e.target.closest('[data-admin-notify-strip]');
                if (stripBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    if (stripBtn.closest('#popup-modal-type-approval')) {
                        adminApplyNotifySnoozeFromButton(stripBtn);
                    }
                    window.adminStripNotifyModalsFromDom();
                    return;
                }
                var goBtn = e.target.closest('[data-admin-notify-url]');
                if (!goBtn) {
                    return;
                }
                var url = goBtn.getAttribute('data-admin-notify-url');
                if (!url) {
                    return;
                }
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                adminApplyNotifySnoozeFromButton(goBtn);
                window.adminGoAfterNotifyClick(url);
            }

            document.addEventListener('click', adminNotifyClickCapture, true);
        })();
    </script>
    <script src="{{asset('assets/admin/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets/admin/css/toastr.css')}}">
</head>

<body class="footer-offset {{ config('app.mode')=='demo'?'demo':'' }} {{ $isRtl ? 'direction-rtl' : '' }}" id="{{ config('app.mode') == 'demo' ? 'demo' : '' }}">


<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="loading" class="d--none">
                <div class="loader-wrap">
                    <img width="200" src="{{asset('assets/admin/img/loader.gif')}}">
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.admin.partials._front-settings')

@include('layouts.admin.partials._header')
@include('layouts.admin.partials._sidebar')

<main id="content" role="main" class="main pointer-event">
@yield('content')

@include('layouts.admin.partials._footer')

    <div class="modal fade" id="popup-modal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center">
                                <h2>
                                    <i class="tio-shopping-cart-outlined"></i> {{translate('You have new order, Check Please.')}}
                                </h2>
                                <hr>
                                <button type="button" class="btn btn-warning mr-3 ignore-order" data-admin-notify-url="{{ route('admin.ignore-check-order') }}">{{translate('Ignore for now')}}</button>
                                <button type="button" class="btn btn-primary check-order" data-admin-notify-url="{{ route('admin.orders.list', ['status' => 'all']) }}">{{translate('Ok, let me check')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="popup-modal-type-approval">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center">
                                <h2>
                                    <i class="tio-user-outlined"></i> {{ translate('New user type approval request(s). Check customers.') }}
                                </h2>
                                <hr>
                                <button type="button" class="btn btn-warning mr-3" data-admin-notify-strip="1">{{ translate('Ignore for now') }}</button>
                                <button type="button" class="btn btn-primary check-type-approval" data-admin-notify-url="{{ route('admin.customer.list') }}">{{ translate('Ok, let me check') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="popup-modal-contact">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center">
                                <h2>
                                    <i class="tio-message-text-outlined"></i> {{ translate('You have new contact message(s). Check Please.') }}
                                </h2>
                                <hr>
                                <button type="button" class="btn btn-warning mr-3 ignore-contact" data-admin-notify-url="{{ route('admin.ignore-check-contact') }}">{{ translate('Ignore for now') }}</button>
                                <button type="button" class="btn btn-primary check-contact" data-admin-notify-url="{{ route('admin.contact-us.index') }}">{{ translate('Ok, let me check') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<span id="message-send-successfully" data-text="{{ translate('Okay') }}"></span>
<script src="{{asset('assets/admin/js/custom.js')}}"></script>
<span class="system-default-country-code" data-value="{{ \App\CentralLogics\Helpers::get_business_settings('country') ?? 'ps' }}"></span>
<span class="image-file-size-data-to-js"
      data-max-upload-size-for-image="{{ readableUploadMaxFileSize('image') }}"
      data-max-upload-size-for-file="{{ readableUploadMaxFileSize('file') }}"
      data-post-max-size="{{ convertToReadableSize(convertToBytes(ini_get('post_max_size'))) }}"
      data-allowed-extensions=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}"
></span>
@stack('script')
@include('admin-views.partials._unified-search-js')
<script src="{{asset('assets/admin/js/vendor.min.js')}}"></script>
<script src="{{asset('assets/admin/js/bootstrap.js')}}"></script>
<script src="{{asset('assets/admin/js/theme.min.js')}}"></script>
<script src="{{asset('assets/admin/js/sweet_alert.js')}}"></script>
<script src="{{asset('assets/admin/js/toastr.js')}}"></script>
<script src="{{asset('assets/admin/intltelinput/js/intlTelInput.min.js')}}"></script>
<script src="{{ asset('assets/admin/js/country-picker-init.js') }}"></script>
<script src="{{ asset('assets/admin/js/file-size-type-validation-with-compress.js') }}"></script>

<script>
    $(function () {
        $(document).on('click', '.toggle-password_custom .eye-on, .toggle-password_custom .eye-off', function () {
            let $wrap = $(this).closest('.toggle-password_custom');
            let $input = $wrap.find('.toggle-password_input');

            let isPassword = $input.attr('type') === 'password';
            $input.attr('type', isPassword ? 'text' : 'password');

            $wrap.find('.eye-on').toggle(!isPassword);
            $wrap.find('.eye-off').toggle(isPassword);
        });
    });
</script>
<script>



</script>

<script>
// $('#add-customer').on('show.bs.modal', function () {
//     setTimeout(function () {
//         $('.select2-dropdown').hide();
//         $('.select2').select2('close');
//     }, 0);
// });



</script>
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
    $(document).on('ready', function () {

        if (window.localStorage.getItem('hs-builder-popover') === null) {
            $('#builderPopover').popover('show')
                .on('shown.bs.popover', function () {
                    $('.popover').last().addClass('popover-dark')
                });

            $(document).on('click', '#closeBuilderPopover', function () {
                window.localStorage.setItem('hs-builder-popover', true);
                $('#builderPopover').popover('dispose');
            });
        } else {
            $('#builderPopover').on('show.bs.popover', function () {
                return false
            });
        }

        $('.js-navbar-vertical-aside-toggle-invoker').click(function () {
            $('.js-navbar-vertical-aside-toggle-invoker i').tooltip('hide');
        });

        let sidebar = $('.js-navbar-vertical-aside').hsSideNav();

        $('.js-nav-tooltip-link').tooltip({boundary: 'window'})

        $(".js-nav-tooltip-link").on("show.bs.tooltip", function (e) {
            if (!$("body").hasClass("navbar-vertical-aside-mini-mode")) {
                return false;
            }
        });

        $('.js-hs-unfold-invoker').each(function () {
            var unfold = new HSUnfold($(this)).init();
        });

        $('.js-form-search').each(function () {
            new HSFormSearch($(this)).init()
        });

        $('.js-select2-custom').each(function () {
            let $el = $(this);

            let select2 = $.HSCore.components.HSSelect2.init($el, {
                sorter: function(data) {
                let term = $('.select2-search__field').val() || '';

                if (!term.trim()) {
                    return data;
                }

                let termLower = term.toLowerCase();

                return data.sort(function(a, b) {
                    let aText = a.text.toLowerCase();
                    let bText = b.text.toLowerCase();

                    let aStarts = aText.startsWith(termLower) ? -1 : 0;
                    let bStarts = bText.startsWith(termLower) ? -1 : 0;

                    if (aStarts !== bStarts) {
                    return aStarts - bStarts;
                    }

                    return aText.localeCompare(bText);
                });
                }
            });
        });


        $('.js-daterangepicker').daterangepicker();

        $('.js-daterangepicker-times').daterangepicker({
            timePicker: true,
            startDate: moment().startOf('hour'),
            endDate: moment().startOf('hour').add(32, 'hour'),
            locale: {
                format: 'M/DD hh:mm A'
            }
        });

        let start = moment();
        let end = moment();

        $('.js-clipboard').each(function () {
            let clipboard = $.HSCore.components.HSClipboard.init(this);
        });
    });
</script>
<script>
    $(document).on('ready', function () {
        $('.js-toggle-password').each(function () {
            new HSTogglePassword(this).init()
        });

        $('.js-validate').each(function () {
            $.HSCore.components.HSValidation.init($(this));
        });
    });
</script>

@stack('script_2')
<audio id="myAudio" preload="auto">
    <source src="{{asset('assets/admin/sound/notification.mp3')}}" type="audio/mpeg">
</audio>

<script>
    (function () {
        var audio = document.getElementById('myAudio');

        function playAudio() {
            if (!audio) {
                return;
            }
            audio.play().catch(function () {});
        }

        window.pauseAdminNotifyAudio = function () {
            if (audio) {
                audio.pause();
            }
        };

        function unlockAudioOnFirstGesture() {
            if (!audio) {
                return;
            }
            function once() {
                audio.muted = true;
                var p = audio.play();
                if (p && typeof p.then === 'function') {
                    p.then(function () {
                        audio.pause();
                        audio.currentTime = 0;
                        audio.muted = false;
                    }).catch(function () {
                        audio.muted = false;
                    });
                } else {
                    audio.muted = false;
                }
                document.removeEventListener('click', once);
                document.removeEventListener('keydown', once);
            }
            document.addEventListener('click', once);
            document.addEventListener('keydown', once);
        }
        unlockAudioOnFirstGesture();

        var adminNotifyT = {
            title: @json(translate('Notifications')),
            pollFailed: @json(translate('Could not load store notification data. Check login or network.')),
            pollOkVerify: @json(translate('New order notification system is active (polling OK).')),
        };

        function adminStoreNotifyToast(kind, message) {
            if (typeof toastr === 'undefined') {
                return;
            }
            var o = { timeOut: 9000, closeButton: true, progressBar: true };
            if (kind === 'error') {
                toastr.error(message, adminNotifyT.title, o);
            } else if (kind === 'success') {
                toastr.success(message, adminNotifyT.title, { timeOut: 6500, closeButton: true, progressBar: true });
            } else {
                toastr.info(message, adminNotifyT.title, o);
            }
        }

        function adminStoreNotifyThrottleToast(ms) {
            var k = '__adminPollErrToastAt';
            var now = Date.now();
            if (window[k] && (now - window[k]) < ms) {
                return false;
            }
            window[k] = now;
            return true;
        }

        function pollStoreNotifications() {
            $.ajax({
                url: '{{route('admin.get-store-data')}}',
                type: 'GET',
                cache: false,
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                success: function (response) {
                    if (!response || response.success != 1 || !response.data) {
                        if (adminStoreNotifyThrottleToast(60000)) {
                            adminStoreNotifyToast('error', adminNotifyT.pollFailed);
                        }
                        return;
                    }
                    var data = response.data;
                    window.lastAdminStoreData = data;

                    if (!sessionStorage.getItem('elite_admin_notify_poll_verified')) {
                        sessionStorage.setItem('elite_admin_notify_poll_verified', '1');
                        if (data.new_order === 0) {
                            adminStoreNotifyToast('success', adminNotifyT.pollOkVerify);
                        }
                    }

                    /*
                     * أولوية العرض: طلب → موافقة نوع → تواصل.
                     * إذا طلب مؤجل (snooze) أو لا يُعرض هذا الدورة، نفحص ما بعده — لا نستخدم else if
                     * حتى يظهر مودال التواصل رغم وجود طلبات إن كان إشعار الطلب مؤجلاً.
                     */
                    var blockOtherNotifyModals = false;

                    if (data.new_order > 0) {
                        if (sessionStorage.getItem('elite_admin_snooze_order') === String(data.new_order)) {
                            /* مؤجل: نتابع لفحص النوع والتواصل */
                        } else if ($('#popup-modal').hasClass('in')) {
                            blockOtherNotifyModals = true;
                        } else {
                            playAudio();
                            window.adminPrepareNotifyModalForShow('popup-modal');
                            $('#popup-modal').appendTo('body').modal('show');
                            blockOtherNotifyModals = true;
                        }
                    }

                    if (!blockOtherNotifyModals && data.pending_type_approval > 0) {
                        if (sessionStorage.getItem('elite_admin_snooze_type_approval') === String(data.pending_type_approval)) {
                            /* مؤجل */
                        } else if ($('#popup-modal-type-approval').hasClass('in')) {
                            blockOtherNotifyModals = true;
                        } else {
                            playAudio();
                            window.adminPrepareNotifyModalForShow('popup-modal-type-approval');
                            $('#popup-modal-type-approval').appendTo('body').modal('show');
                            blockOtherNotifyModals = true;
                        }
                    }

                    if (!blockOtherNotifyModals && data.new_contact_us > 0) {
                        if (sessionStorage.getItem('elite_admin_snooze_contact_us') !== String(data.new_contact_us)
                            && !$('#popup-modal-contact').hasClass('in')) {
                            playAudio();
                            window.adminPrepareNotifyModalForShow('popup-modal-contact');
                            $('#popup-modal-contact').appendTo('body').modal('show');
                        }
                    }
                },
                error: function () {
                    if (adminStoreNotifyThrottleToast(60000)) {
                        adminStoreNotifyToast('error', adminNotifyT.pollFailed);
                    }
                },
            });
        }
        pollStoreNotifications();
        setInterval(pollStoreNotifications, 10000);
    })();
</script>
<script>
    {{-- أزرار الإشعار تُدار من &lt;head&gt; (تقاطع + DOM خالص) عبر data-admin-notify-url / data-admin-notify-strip --}}

    $('.route-alert').on('click', function (){
        let route = $(this).data('route');
        let message = $(this).data('message');
        route_alert(route, message)
    });

    function route_alert(route, message) {
        Swal.fire({
            title: '{{translate("Are you sure?")}}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#673ab7',
            cancelButtonText: '{{translate("No")}}',
            confirmButtonText:'{{translate("Yes")}}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                location.href = route;
            }
        })
    }

    $('.form-alert').on('click', function (){
        let id = $(this).data('id');
        let message = $(this).data('message');
        form_alert(id, message)
    });

    function form_alert(id, message) {
        Swal.fire({
            title:'{{translate("Are you sure?")}}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#673ab7',
            cancelButtonText: '{{translate("No")}}',
            confirmButtonText: '{{translate("Yes")}}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $('#'+id).submit()
            }
        })
    }

    function call_demo(){
        toastr.info('Disabled for demo version!')
    }

    $('.change-status').on('click', function (){
        location.href = $(this).data('route');
    });

    let initialImages = [];
    $(window).on('load', function() {
        $("form").find('img').each(function (index, value) {
            initialImages.push(value.src);
        })
    })

    $(document).ready(function() {
        $('form').on('reset', function(e) {
            $("form").find('img').each(function (index, value) {
                $(value).attr('src', initialImages[index]);
            })
        });
    });

    $('.demo-form-submit').click(function() {
        if ('{{ config('app.mode') }}' === 'demo') {
            call_demo();
        }
    });

</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        replaceEmailsAndPhoneNumbersWithAsterisks(document.getElementById('demo'));

        // Disable mailto links
        document.querySelectorAll('a[href^="mailto:"]').forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
            });
        });
        // Disable tel links
        document.querySelectorAll('a[href^="tel:"]').forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
            });
        });
    });

    function replaceEmailsAndPhoneNumbersWithAsterisks(element) {
        if (!element) return;
        if (element.nodeType === 3) {
            element.nodeValue = element.nodeValue
                .replace(/\b([A-Za-z0-9._%+-])[^@]*@([A-Za-z0-9.-]+)\.([A-Z|a-z]{2,})\b/g, function (match, firstLetter, domain, tld) {
                    var remainingAsterisks = '*'.repeat(Math.min(10, match.length - 1));
                    return firstLetter + remainingAsterisks + '@' + domain + '.' + tld;
                })
                .replace(/\b(?:\+\d{1,3}\s?)?\d{1,4}[-.\s]?\d{5,}\b/g, function (match) {
                    if (match.length >= 8) {
                        var firstChar = match.charAt(0) === '+' ? '+' : match.charAt(0);
                        var remainingAsterisks = '*'.repeat(Math.min(10, match.length - (firstChar === '+' ? 2 : 1)));
                        return firstChar + remainingAsterisks;
                    } else {
                        return match;
                    }
                });
        } else if (element.nodeType === 1) {
            for (var i = 0; i < element.childNodes.length; i++) {
                replaceEmailsAndPhoneNumbersWithAsterisks(element.childNodes[i]);
            }
        }
    }
</script>

{{--termalprint--}}
<script>
    $('.invoice-printing').on('click', function (){
        let orderId = $(this).data('id');
        print_invoice(orderId);
    })

    function print_invoice(order_id) {
        $.get({
            url: '{{url('/')}}/admin/orders/pos-invoice/'+order_id,
            dataType: 'json',
            beforeSend: function () {
                $('#loading').show();
            },
            success: function (data) {
                $('#print-invoice').modal('show');
                $('#printableArea').empty().html(data.view);
            },
            complete: function () {
                $('#loading').hide();
            },
        });
    }

    function round(value, decimals) {
        return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
    }


    // $('.print-div-button').on('click', function (){
    //     let name = $(this).data('name');
    //     printDiv(name);
    // })
    //
    // function printDiv(divName) {
    //     let printContents = document.getElementById(divName).innerHTML;
    //     document.body.innerHTML = printContents;
    //     window.print();
    //     location.reload();
    // }
</script>

<!-- IE Support -->
<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>
