@extends('layouts.admin.app')

@section('title', translate('business_setup'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/business-setup.png')}}" alt="{{ translate('business-setup') }}">
                {{translate('business_Setup')}}
            </h2>
        </div>

        <div class="inline-page-menu mb-4">
            @include('admin-views.business-settings.partial.business-setup-nav')
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="tio-notifications-alert mr-1"></i>
                    {{translate('Maintenance_Mode')}}
                    <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" data-toggle="modal" data-target="#ecomMaintenanceHelpModal" title="{{ translate('help_ecom_maintenance_btn') }}">
                        <i class="tio-book-outlined"></i>
                    </button>
                </h5>
            </div>
            <?php
                $config = \App\CentralLogics\Helpers::get_business_settings('maintenance_mode');
                $selectedMaintenanceDuration = \App\CentralLogics\Helpers::get_business_settings('maintenance_duration_setup') ?? [];
                if (!is_array($selectedMaintenanceDuration)) {
                    $selectedMaintenanceDuration = [];
                }
                $startDate = !empty($selectedMaintenanceDuration['start_date']) ? new DateTime($selectedMaintenanceDuration['start_date']) : null;
                $endDate = !empty($selectedMaintenanceDuration['end_date']) ? new DateTime($selectedMaintenanceDuration['end_date']) : null;
            ?>
            <div class="card-body">
                <div class="bg-light rounded p-3">
                    <div class="row">
                        <div class="col-md-8">
                            @if($config)
                                <div class="mb-3">
                                    <p class="mb-0">
                                        @if(isset($selectedMaintenanceDuration['maintenance_duration']) && $selectedMaintenanceDuration['maintenance_duration'] == 'until_change')
                                            {{ translate('Your maintenance mode is activated until I change') }}
                                        @elseif($startDate && $endDate)
                                            {{ translate('Your maintenance mode is activated from') }}<strong class="pl-1">{{ $startDate->format('m/d/Y, h:i A') }}</strong> {{ translate('to') }} <strong>{{ $endDate->format('m/d/Y, h:i A') }}</strong>.
                                        @else
                                            {{ translate('Your maintenance mode is activated until I change') }}
                                        @endif
                                        <a class="btn btn-outline-primary btn-sm py-1 px-2 edit square-btn maintenance-mode-show d-inline-block" href="#"><i class="tio-edit"></i></a>
                                    </p>
                                </div>
                            @else
                                <p>*{{ translate('By turning on maintenance mode Control your all system & function') }}</p>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center border rounded mb-2 px-3 py-2">
                                <h5 class="mb-0">{{translate('Maintenance Mode')}}</h5>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input @if(!$config) maintenance-mode-show @else maintenance-mode-off @endif"
                                           id="maintenance-mode-input"
                                        {{isset($config) && $config?'checked':''}}>
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            @if($config)
                                <div class="d-flex flex-wrap gap-3 align-items-center">
                                    <h6 class="mb-0">{{ translate('All System') }}</h6>
                                    <span class="bg-soft-dark px-3 py-1 rounded">{{ translate('Maintenance applies to all systems') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="tio-download-to"></i>
                    {{ translate('Download DB') }}
                    <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" data-toggle="modal" data-target="#ecomDownloadDbHelpModal" title="{{ translate('help_ecom_download_db_btn') }}">
                        <i class="tio-book-outlined"></i>
                    </button>
                </h5>
            </div>
            <div class="card-body">
                <div class="bg-light rounded p-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center border rounded px-3 py-2 bg-white gap-2">
                        <span class="text-dark mb-0">{{ translate('Download DB') }}</span>
                        <a href="{{ route('admin.database.download') }}" class="btn btn-primary min-w-120">
                            {{ translate('Download DB') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="tio-star"></i>
                    {{ translate('loyalty_program') }}
                    <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" data-toggle="modal" data-target="#ecomLoyaltyHelpModal" title="{{ translate('help_ecom_loyalty_btn') }}">
                        <i class="tio-book-outlined"></i>
                    </button>
                </h5>
            </div>
            <div class="card-body">
                <div class="bg-light rounded p-3">
                    <div class="row g-3">
                        <div class="col-lg-4 col-sm-6">
                            @php($loyaltyEnabled = \App\CentralLogics\Helpers::get_business_settings('loyalty_points_enabled'))
                            <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2 bg-white">
                                <h5 class="mb-0">{{ translate('loyalty_points_enabled') }}</h5>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input" name="loyalty_points_enabled" form="ecomSetupForm" {{ ($loyaltyEnabled ?? 0) == 1 ? 'checked' : '' }}>
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            @php($amountForPoint = \App\CentralLogics\Helpers::get_business_settings('loyalty_amount_for_one_point') ?? 10)
                            <div class="form-group">
                                <label class="input-label">{{ translate('loyalty_amount_for_one_point') }}</label>
                                <input type="number" name="loyalty_amount_for_one_point" form="ecomSetupForm" class="form-control" value="{{ $amountForPoint }}" min="1" step="0.01" placeholder="10">
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            @php($pointsPerAmount = \App\CentralLogics\Helpers::get_business_settings('loyalty_points_per_amount') ?? 1)
                            <div class="form-group">
                                <label class="input-label">{{ translate('loyalty_points_per_amount') }}</label>
                                <input type="number" name="loyalty_points_per_amount" form="ecomSetupForm" class="form-control" value="{{ $pointsPerAmount }}" min="0.5" step="0.5" placeholder="1">
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            @php($redemptionValue = \App\CentralLogics\Helpers::get_business_settings('loyalty_point_redemption_value') ?? 0.5)
                            <div class="form-group">
                                <label class="input-label">{{ translate('loyalty_point_redemption_value') }}</label>
                                <input type="number" name="loyalty_point_redemption_value" form="ecomSetupForm" class="form-control" value="{{ $redemptionValue }}" min="0.01" step="0.01" placeholder="0.5">
                                <small class="text-muted">{{ translate('Cash value per point at POS redemption') }} ({{ \App\CentralLogics\Helpers::currency_symbol() }})</small>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            @php($loyaltyCouponTogether = \App\CentralLogics\Helpers::get_business_settings('loyalty_and_coupon_together') ?? 1)
                            <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2 bg-white">
                                <h5 class="mb-0">{{ translate('loyalty_and_coupon_together') }}</h5>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input" name="loyalty_and_coupon_together" form="ecomSetupForm" {{ ($loyaltyCouponTogether ?? 1) == 1 ? 'checked' : '' }}>
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">{{ translate('loyalty_and_coupon_together_help') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center gap-2">
                    <i class="tio-image"></i>
                    {{ translate('logos') }}
                    <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" data-toggle="modal" data-target="#ecomLogosHelpModal" title="{{ translate('help_ecom_logos_btn') }}">
                        <i class="tio-book-outlined"></i>
                    </button>
                </h5>
            </div>
            <div class="card-body">
                <div class="bg-light rounded p-3">
                    <div class="row g-3">
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="mb-0">{{translate('Admin Logo')}}</label>
                                <small class="text-danger"> * ( {{translate('ratio')}} 3:1 )</small>
                                <p class="fs-14 text-muted mb-2 mt-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                                <div class="custom-file">
                                    <input type="file" name="logo" id="customFileEg1" form="ecomSetupForm" class="custom-file-input"
                                           accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                           data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">
                                    <label class="custom-file-label"
                                        for="customFileEg1">{{translate('choose')}} {{translate('file')}}</label>
                                </div>

                                <div class="text-center mt-4">
                                    <img class="upload-img-view h-auto max-w-200" id="viewer"
                                        src="{{ $logo }}" alt="{{ translate('logo_image') }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="mb-0">{{translate('Web App Logo')}}</label>
                                <small class="text-danger"> * ( {{translate('ratio')}} 1:1 )</small>
                                <p class="fs-14 text-muted mb-2 mt-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                                <div class="custom-file">
                                    <input type="file" name="app_logo" id="customFileEg3" form="ecomSetupForm" class="custom-file-input"
                                           accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                           data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">
                                    <label class="custom-file-label"
                                        for="customFileEg3">{{translate('choose')}} {{translate('file')}}</label>
                                </div>

                                <div class="text-center mt-4">
                                    <img class="upload-img-view h-auto max-w-200" id="viewer_3"
                                        src="{{ $app_logo }}" alt="{{ translate('app_logo_image') }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="mb-0">{{translate('fav_icon')}}</label>
                                <small class="text-danger"> * ( {{translate('ratio')}} 1:1 )</small>
                                <p class="fs-14 text-muted mb-2 mt-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                                <div class="custom-file">
                                    <input type="file" name="fav_icon" id="customFileEg2" form="ecomSetupForm" class="custom-file-input"
                                           accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                           data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">
                                    <label class="custom-file-label"
                                        for="customFileEg2">{{translate('choose')}} {{translate('file')}}</label>
                                </div>

                                <div class="text-center mt-4">
                                    <img class="upload-img-view h-auto max-w-145" id="viewer_2"
                                        src="{{ $fav_icon}}" alt="{{ translate('fav_icon_image') }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h4 class="d-flex align-items-center gap-2 mb-0">
                    <i class="tio-settings"></i>
                    {{ translate('General settings form') }}
                    <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" data-toggle="modal" data-target="#ecomGeneralHelpModal" title="{{ translate('help_ecom_general_btn') }}">
                        <i class="tio-book-outlined"></i>
                    </button>
                </h4>
            </div>
            <div class="card-body">
                <form id="ecomSetupForm" action="{{route('admin.business-settings.update-setup')}}" method="post"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="bg-light rounded p-3">
                    <div class="row g-3">
                        @php($name=Helpers::get_business_settings('store_name') ?? Helpers::get_business_settings('restaurant_name'))
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('Shop Name')}}</label>
                                <input type="text" name="store_name" value="{{$name}}" class="form-control"
                                       placeholder="{{ translate('ABC Company') }}" required>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('currency')}}</label>
                                <select name="currency" class="form-control js-select2-custom">
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->currency_code }}" {{ $currency_code == $currency->currency_code ? 'selected' : '' }}>
                                            {{ $currency->currency_code }} ( {{ $currency->currency_symbol }} )
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @php($phone=Helpers::get_business_settings('phone'))
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('phone')}}</label>
                                <input type="text" value="{{$phone}}" name="phone" class="form-control"
                                       placeholder="" required>
                            </div>
                        </div>
                        @php($email=Helpers::get_business_settings('email_address'))
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('email')}}</label>
                                <input type="email" value="{{$email}}"
                                       name="email" class="form-control" placeholder="" required>
                            </div>
                        </div>
                        @php($address=Helpers::get_business_settings('address'))
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{translate('address')}}</label>
                                <input type="text" value="{{$address}}"
                                       name="address" class="form-control" placeholder=""
                                       required>
                            </div>
                        </div>
                        @php($country = Helpers::get_business_settings('country') ?? 'PS')
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label" for="country">{{translate('country')}}</label>
                                <select name="country" id="country" class="form-control js-select2-custom">
                                    @foreach(COUNTRY_CODE as $c)
                                        <option value="{{ $c['code'] }}" {{ strtoupper($country) == $c['code'] ? 'selected' : '' }}>{{ $c['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @php($pagination_limit=Helpers::get_business_settings('pagination_limit'))
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{translate('pagination')}} {{translate('settings')}}</label>
                                <input type="text" value="{{$pagination_limit}}"
                                       name="pagination_limit" class="form-control" placeholder=""
                                       required>
                            </div>
                        </div>
                        @php($mov=Helpers::get_business_settings('minimum_order_value'))
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{translate('min')}} {{translate('order')}} {{translate('value')}}
                                    ( {{Helpers::currency_symbol()}} )</label>
                                <input type="number" min="1" value="{{$mov}}"
                                       name="minimum_order_value" class="form-control" placeholder=""
                                       required>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            @php($sp=Helpers::get_business_settings('self_pickup'))
                            <div class="form-group">
                                <label>{{translate('self_pickup')}}</label>
                                <small class="text-danger"> *</small>
                                <div class="input-group input-group-md-down-break">
                                    <div class="form-control">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" value="1"
                                                   name="self_pickup"
                                                   id="sp1" {{$sp==1?'checked':''}}>
                                            <label class="custom-control-label"
                                                   for="sp1">{{translate('on')}}</label>
                                        </div>
                                    </div>

                                    <div class="form-control">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" value="0"
                                                   name="self_pickup"
                                                   id="sp2" {{$sp==0?'checked':''}}>
                                            <label class="custom-control-label"
                                                   for="sp2">{{translate('off')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            @php($config=Helpers::get_business_settings('currency_symbol_position'))
                            <div class="form-group">
                                <label class="d-flex justify-content-between align-items-center"> {{ translate('Currency Symbol Position') }}</i> </label>

                                <div class="input-group input-group-md-down-break">
                                    <div class="form-control">
                                        <div class="custom-control custom-radio custom-radio-reverse">
                                            <input type="radio" class="custom-control-input currency-symbol-position"
                                                   name="projectViewNewProjectTypeRadio"
                                                   data-route="{{ route('admin.business-settings.currency-position',['left']) }}"
                                                   id="projectViewNewProjectTypeRadio1" {{(isset($config) && $config=='left')?'checked':''}}>
                                            <label class="custom-control-label media align-items-center" for="projectViewNewProjectTypeRadio1">
                                                <i class="tio-agenda-view-outlined text-muted mr-2"></i>
                                                <span class="media-body">{{Helpers::currency_symbol()}} {{ translate('Left') }}</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-control">
                                        <div class="custom-control custom-radio custom-radio-reverse">
                                            <input type="radio" class="custom-control-input currency-symbol-position"
                                                   name="projectViewNewProjectTypeRadio"
                                                   data-route="{{ route('admin.business-settings.currency-position',['right']) }}"
                                                   id="projectViewNewProjectTypeRadio2" {{(isset($config) && $config=='right')?'checked':''}}>
                                            <label class="custom-control-label media align-items-center"
                                                   for="projectViewNewProjectTypeRadio2">
                                                <i class="tio-table text-muted mr-2"></i>
                                                <span class="media-body">
                                                    {{ translate('Right') }} {{Helpers::currency_symbol()}}
                                                    </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            @php($guestCheckout=\App\CentralLogics\Helpers::get_business_settings('guest_checkout'))
                            <div class="d-flex justify-content-between align-items-center border rounded mb-2 px-3 py-2">
                                <h5 class="mb-0 d-flex align-items-center gap-2">{{translate('Guest Checkout')}}
                                    <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" data-toggle="modal" data-target="#ecomGuestCheckoutHelpModal" title="{{ translate('help_ecom_guest_checkout_btn') }}">
                                        <i class="tio-book-outlined"></i>
                                    </button>
                                </h5>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input" name="guest_checkout" id="guest_checkout" {{ $guestCheckout == 1 ? 'checked' : '' }}>
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-12">
                            <div class="form-group ecom-translation-language-wrap">
                                <label class="input-label d-block mb-2" for="language">
                                    {{ translate('ecom_translation_languages_label') }}
                                    <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2 ms-1" data-toggle="modal" data-target="#ecomLanguagesHelpModal" title="{{ translate('help_ecom_languages_btn') }}">
                                        <i class="tio-book-outlined"></i>
                                    </button>
                                </label>
                                <select name="language[]" id="language"
                                        class="form-control js-select2-custom ecom-translation-select" required multiple>
                                    @foreach(LANGUAGE_CODE as $language)
                                        <option value="{{ $language['code'] }}">{{ $language['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-start align-items-center gap-2 mt-4 pt-4 border-top ecom-setup-actions">
                        <button type="reset" class="btn btn--reset min-w-120">{{translate('reset')}}</button>
                        <button type="{{config('app.mode')!='demo'?'submit':'button'}}"
                                class="btn btn-primary min-w-120 demo-form-submit">{{translate('submit')}}
                        </button>
                    </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="maintenance-mode-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">
                        <i class="tio-notifications-alert mr-1"></i>
                        {{translate('System Maintenance')}}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{route('admin.business-settings.maintenance-mode-setup')}}" id="maintenanceModeForm">
                    <?php
                    $selectedMaintenanceDuration = \App\CentralLogics\Helpers::get_business_settings('maintenance_duration_setup') ?? [];
                    if (!is_array($selectedMaintenanceDuration)) {
                        $selectedMaintenanceDuration = [];
                    }
                    $selectedMaintenanceMessage = \App\CentralLogics\Helpers::get_business_settings('maintenance_message_setup') ?? [];
                    if (!is_array($selectedMaintenanceMessage)) {
                        $selectedMaintenanceMessage = [];
                    }
                    $maintenanceMode = \App\CentralLogics\Helpers::get_business_settings('maintenance_mode') ?? 0;
                    ?>
                    <div class="modal-body">
                        @csrf
                        <div class="d-flex flex-column g-2">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <p>*{{ translate('By turning on maintenance mode Control your all system & function') }}</p>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-between align-items-center border rounded mb-2 px-3 py-2">
                                        <h5 class="mb-0">{{translate('Maintenance Mode')}}</h5>
                                        <label class="toggle-switch toggle-switch-sm">
                                            <input type="checkbox" class="toggle-switch-input" name="maintenance_mode" id="maintenance-mode-checkbox"
                                                {{ $maintenanceMode ?'checked':''}}>
                                            <span class="toggle-switch-label text mb-0">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-4">
                                    <h3>{{ translate('Maintenance Date') }} & {{ translate('Time') }}</h3>
                                    <p>{{ translate('Choose the maintenance mode duration.') }}</p>
                                </div>
                                <div class="col-xl-8">
                                    <div class="border p-3">
                                        <div class="d-flex flex-wrap gap-3 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="maintenance_duration"
                                                       {{ isset($selectedMaintenanceDuration['maintenance_duration']) && $selectedMaintenanceDuration['maintenance_duration'] == 'one_day' ? 'checked' : '' }}
                                                       value="one_day" id="one_day">
                                                <label class="form-check-label" for="one_day">{{ translate('For 24 Hours') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="maintenance_duration"
                                                       {{ isset($selectedMaintenanceDuration['maintenance_duration']) && $selectedMaintenanceDuration['maintenance_duration'] == 'one_week' ? 'checked' : '' }}
                                                       value="one_week" id="one_week">
                                                <label class="form-check-label" for="one_week">{{ translate('For 1 Week') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="maintenance_duration"
                                                       {{ isset($selectedMaintenanceDuration['maintenance_duration']) && $selectedMaintenanceDuration['maintenance_duration'] == 'until_change' ? 'checked' : '' }}
                                                       value="until_change" id="until_change">
                                                <label class="form-check-label" for="until_change">{{ translate('Until I change') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="maintenance_duration"
                                                       {{ isset($selectedMaintenanceDuration['maintenance_duration']) && $selectedMaintenanceDuration['maintenance_duration'] == 'customize' ? 'checked' : '' }}
                                                       value="customize" id="customize">
                                                <label class="form-check-label" for="customize">{{ translate('Customize') }}</label>
                                            </div>
                                        </div>
                                        <div class="row start-and-end-date">
                                            <div class="col-md-6">
                                                <label>{{ translate('Start Date') }}</label>
                                                <input type="datetime-local" class="form-control" name="start_date" id="startDate"
                                                       value="{{ old('start_date', $selectedMaintenanceDuration['start_date'] ?? '') }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>{{ translate('End Date') }}</label>
                                                <input type="datetime-local" class="form-control" name="end_date" id="endDate"
                                                       value="{{ old('end_date', $selectedMaintenanceDuration['end_date'] ?? '') }}" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <small id="dateError" class="form-text text-danger" style="display: none;">{{ translate('Start date cannot be greater than end date.') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="advanceFeatureButtonDiv">
                            <div class="d-flex justify-content-center mt-3">
                                <a href="#" id="advanceFeatureToggle" class="d-block mb-3 maintenance-advance-feature-button">{{ translate('Advance Feature') }}</a>
                            </div>
                        </div>

                        <div class="row mt-4" id="advanceFeatureSection" style="display: none;">
                            <div class="col-xl-4">
                                <h3>{{ translate('Maintenance Massage') }}</h3>
                                <p>{{ translate('Select & type what massage you want to see when maintenance mode is active.') }}</p>
                            </div>
                            <div class="col-xl-8">
                                <div class="border p-3">
                                    <div class="form-group">
                                        <label>{{ translate('Show Contact Info') }}</label>
                                        <div class="d-flex flex-wrap">
                                            <div class="form-check mr-4">
                                                <input class="form-check-input" type="checkbox" name="business_number"
                                                       {{ ($selectedMaintenanceMessage['business_number'] ?? 0) == 1 ? 'checked' : '' }}
                                                       id="businessNumber">
                                                <label class="form-check-label ml-1" for="businessNumber">{{ translate('Business Number') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="business_email"
                                                       {{ ($selectedMaintenanceMessage['business_email'] ?? 0) == 1 ? 'checked' : '' }}
                                                       id="businessEmail">
                                                <label class="form-check-label ml-1" for="businessEmail">{{ translate('Business Email') }}</label>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-group">
                                        <label>{{ translate('Maintenance Message') }}
                                            <i class="tio-info-outined"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="{{ translate('The maximum character limit is 100') }}">
                                            </i>
                                        </label>
                                        <input type="text" class="form-control" name="maintenance_message" placeholder="We're Working On Something Special!"
                                               maxlength="100" value="{{ $selectedMaintenanceMessage['maintenance_message'] ?? '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ translate('Message Body') }}
                                            <i class="tio-info-outined"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="{{ translate('The maximum character limit is 255') }}">
                                            </i>
                                        </label>
                                        <textarea class="form-control" name="message_body" maxlength="255" rows="3" placeholder="{{ translate('Our system is currently undergoing maintenance to bring you an even tastier experience. Hang tight while we make the dishes.') }}">{{ $selectedMaintenanceMessage['message_body'] ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="#" id="seeLessToggle" class="d-block mb-3 maintenance-advance-feature-button">{{ translate('See Less') }}</a>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-end gap-3">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelButton">{{ translate('Cancel') }}</button>
                            <button type="button" class="btn btn-primary demo-form-submit" @if(config('app.mode') != 'demo')
                                onclick="validateMaintenanceMode()"
                                @endif>{{ translate('Save') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'ecomMaintenanceHelpModal', 'titleKey' => 'help_ecom_maintenance_title', 'pageKey' => 'help_ecom_maintenance_page'])
    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'ecomDownloadDbHelpModal', 'titleKey' => 'help_ecom_download_db_title', 'pageKey' => 'help_ecom_download_db_page'])
    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'ecomLoyaltyHelpModal', 'titleKey' => 'help_ecom_loyalty_title', 'pageKey' => 'help_ecom_loyalty_page'])
    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'ecomLogosHelpModal', 'titleKey' => 'help_ecom_logos_title', 'pageKey' => 'help_ecom_logos_page'])
    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'ecomGuestCheckoutHelpModal', 'titleKey' => 'help_ecom_guest_checkout_title', 'pageKey' => 'help_ecom_guest_checkout_page'])
    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'ecomLanguagesHelpModal', 'titleKey' => 'help_ecom_languages_title', 'pageKey' => 'help_ecom_languages_page'])
    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'ecomGeneralHelpModal', 'titleKey' => 'help_ecom_general_title', 'pageKey' => 'help_ecom_general_page'])

    <!-- Modal for Checking -->
@endsection

@push('script_2')
    <script src="{{ asset('assets/admin/js/business-settings.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var runId = 'biz-ui-' + Date.now();
            var nodes = document.querySelectorAll('.text-center.mt-4');
            var logoImg = document.getElementById('viewer');
            var appLogoImg = document.getElementById('viewer_3');
            var favIconImg = document.getElementById('viewer_2');

        });
    </script>

    <script>
        "use strict";

        @php($language=\App\Models\BusinessSetting::where('key','language')->first()?->value ?? '["ar"]')
        let language = <?php echo($language); ?>;
        $('[id=language]').val(language);

        $('.maintenance-mode-off').on('click', function (){
            @if(config('app.mode')=='demo'){
                toastr.info('Disabled for demo version!')
            }@else{
                Swal.fire({
                    title: '{{translate("Are you sure?")}}',
                    text:  '{{translate("Be careful before you turn on/off maintenance mode")}}',
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#377dff',
                    cancelButtonText: '{{translate("No")}}',
                    confirmButtonText: '{{translate("Yes")}}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $.get({
                            url: '{{route('admin.business-settings.maintenance-mode')}}',
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
                    } else {
                        location.reload();
                    }
                })
            }
            @endif
        })
    </script>
    <script>
        $('.maintenance-mode-show').click(function (){
            $('#maintenance-mode-modal').modal('show');
        });

        $(document).ready(function() {
            var initialMaintenanceMode = $('#maintenance-mode-input').is(':checked');

            $('#maintenance-mode-modal').on('show.bs.modal', function () {
                var initialMaintenanceModeModel = $('#maintenance-mode-input').is(':checked');
                $('#maintenance-mode-checkbox').prop('checked', initialMaintenanceModeModel);
            });

            $('#maintenance-mode-modal').on('hidden.bs.modal', function () {
                $('#maintenance-mode-input').prop('checked', initialMaintenanceMode);
            });

            $('#cancelButton').click(function() {
                $('#maintenance-mode-input').prop('checked', initialMaintenanceMode);
                $('#maintenance-mode-modal').modal('hide');
            });

            $('#maintenance-mode-checkbox').change(function() {
                $('#maintenance-mode-input').prop('checked', $(this).is(':checked'));
            });

            $('#advanceFeatureToggle').click(function(event) {
                event.preventDefault();
                $('#advanceFeatureSection').show();
                $('#advanceFeatureButtonDiv').hide();
            });

            $('#seeLessToggle').click(function(event) {
                event.preventDefault();
                $('#advanceFeatureSection').hide();
                $('#advanceFeatureButtonDiv').show();
            });

            var startDate = $('#startDate');
            var endDate = $('#endDate');
            var dateError = $('#dateError');

            function updateDatesBasedOnDuration(selectedOption) {
                if (selectedOption === 'one_day' || selectedOption === 'one_week') {
                    var now = new Date();
                    var timezoneOffset = now.getTimezoneOffset() * 60000;
                    var formattedNow = new Date(now.getTime() - timezoneOffset).toISOString().slice(0, 16);

                    if (selectedOption === 'one_day') {
                        var end = new Date(now);
                        end.setDate(end.getDate() + 1);
                    } else if (selectedOption === 'one_week') {
                        var end = new Date(now);
                        end.setDate(end.getDate() + 7);
                    }

                    var formattedEnd = new Date(end.getTime() - timezoneOffset).toISOString().slice(0, 16);

                    startDate.val(formattedNow).prop('readonly', false).prop('required', true);
                    endDate.val(formattedEnd).prop('readonly', false).prop('required', true);
                    $('.start-and-end-date').removeClass('opacity');
                    dateError.hide();
                } else if (selectedOption === 'until_change') {
                    startDate.val('').prop('readonly', true).prop('required', false);
                    endDate.val('').prop('readonly', true).prop('required', false);
                    $('.start-and-end-date').addClass('opacity');
                    dateError.hide();
                } else if (selectedOption === 'customize') {
                    startDate.prop('readonly', false).prop('required', true);
                    endDate.prop('readonly', false).prop('required', true);
                    $('.start-and-end-date').removeClass('opacity');
                    dateError.hide();
                }
            }

            function validateDates() {
                var start = new Date(startDate.val());
                var end = new Date(endDate.val());
                if (start > end) {
                    dateError.show();
                    startDate.val('');
                    endDate.val('');
                } else {
                    dateError.hide();
                }
            }

            // Initial load
            var selectedOption = $('input[name="maintenance_duration"]:checked').val();
            updateDatesBasedOnDuration(selectedOption);

            // When maintenance duration changes
            $('input[name="maintenance_duration"]').change(function() {
                var selectedOption = $(this).val();
                updateDatesBasedOnDuration(selectedOption);
            });

            // When start date or end date changes
            $('#startDate, #endDate').change(function() {
                $('input[name="maintenance_duration"][value="customize"]').prop('checked', true);
                startDate.prop('readonly', false).prop('required', true);
                endDate.prop('readonly', false).prop('required', true);
                validateDates();
            });

            // // Form validation before submission
            $('#maintenanceModeForm').on('submit', function(e) {
                let selectedOption = $('input[name="maintenance_duration"]:checked').val();

                if (selectedOption === 'customize') {
                    let startDateValue = $('#startDate').val();
                    let endDateValue = $('#endDate').val();

                    if (!startDateValue || !endDateValue) {
                        e.preventDefault();
                        dateError.text('Please provide both start and end dates.').show();
                        return false;
                    }
                }
                dateError.hide();
            });
        });

        function validateMaintenanceMode() {
            $('#maintenanceModeForm').submit();
        }
    </script>
@endpush
