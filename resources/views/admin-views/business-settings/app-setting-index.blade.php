@extends('layouts.admin.app')

@section('title', translate('App Settings'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/system-setting.png')}}" alt="{{ translate('image') }}">
                {{translate('business_setup')}}
            </h2>
        </div>

        <div class="inline-page-menu mb-4">
            @include('admin-views.business-settings.partial.business-setup-nav')
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <a href="https://wa.me/970599814758" target="_blank" rel="noopener" class="alert alert-danger d-flex align-items-center gap-2 mb-0 flex-grow-1 text-decoration-none" role="alert" style="color: #fff !important;">
                <i class="tio-whatsapp" style="font-size: 1.2em;"></i>
                <span>{{ translate('support_sidebar_app_message') }}</span>
                <span class="font-weight-bold ml-2">— {{ translate('contact us on WhatsApp') }}</span>
            </a>
            <button type="button" class="btn btn-outline-primary btn-sm flex-shrink-0" data-toggle="modal" data-target="#appSettingsHelpModal" title="{{ translate('help_app_settings_btn') }}">
                <i class="tio-book-outlined"></i> {{ translate('help_app_settings_btn') }}
            </button>
        </div>
        <div class="row gy-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{translate('Android')}}</h5>
                    </div>
                    <div class="card-body">
                        @php($config = Helpers::get_business_settings('play_store_config') ?? [])
                        <form
                            action="{{config('app.mode')!='demo'?route('admin.business-settings.app_setting',['platform' => 'android']):'javascript:'}}" method="post">
                            @csrf
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <label class="text-dark font-weight-bold mb-0" for="play_store_status">{{ translate('Enable download link for web footer') }}</label>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input" id="play_store_status" name="play_store_status"
                                            value="1" {{($config['status']??0)==1?'checked':''}}>
                                    <span class="switcher_control"></span>
                                </label>
                            </div>

                            <div class="mb-4">
                                <label class="text-dark" for="app_store_link">{{ translate('Download link') }}</label>
                                <input type="text" id="play_store_link" name="play_store_link"
                                        value="{{$config['link']??''}}" class="form-control">
                            </div>

                            <div class="mb-4">
                                <label class="text-dark" for="android_min_version">{{ translate('Minimum version for force update') }}
                                    <i class="tio-info text-danger" data-toggle="tooltip" data-placement="right"
                                        title="{{ translate("If there is any update available in the admin panel and for that, the previous user app will not work, you can force the customer from here by providing the minimum version for force update. That means if a customer has an app below this version the customers must need to update the app first. If you don't need a force update just insert here zero (0) and ignore it.") }}"></i>
                                </label>
                                <input type="number" min="0" step=".1" id="android_min_version" name="android_min_version"
                                        value="{{$config['min_version']??''}}" class="form-control"
                                        placeholder="{{ translate('EX: 4.0') }}">
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="{{config('app.mode')!='demo'?'submit':'button'}}"
                                    class="btn btn-primary demo-form-submit">{{translate('save')}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{translate('IOS')}}</h5>
                    </div>
                    <div class="card-body">
                        @php($config = Helpers::get_business_settings('app_store_config') ?? [])
                        <form action="{{config('app.mode')!='demo'?route('admin.business-settings.app_setting',['platform' => 'ios']):'javascript:'}}" method="post">
                            @csrf
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <label class="text-dark font-weight-bold mb-0" for="app_store_status2">{{ translate('Enable download link for web footer') }}</label>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input" id="app_store_status2" name="app_store_status"
                                            value="1" {{($config['status']??0)==1?'checked':''}}>
                                    <span class="switcher_control"></span>
                                </label>
                            </div>

                            <div class="mb-4">
                                <label class="text-dark"
                                        for="app_store_link">{{ translate('Download link') }}
                                </label>
                                <input type="text" id="app_store_link" name="app_store_link"
                                        value="{{$config['link']??''}}" class="form-control" placeholder="">
                            </div>

                            <div class="mb-4">
                                <label class="text-dark"
                                        for="ios_min_version">{{ translate('Minimum version for force update') }}
                                    <i class="tio-info text-danger" data-toggle="tooltip" data-placement="right"
                                        title="{{ translate("If there is any update available in the admin panel and for that, the previous user app will not work, you can force the customer from here by providing the minimum version for force update. That means if a customer has an app below this version the customers must need to update the app first. If you don't need a force update just insert here zero (0) and ignore it.") }}"></i>
                                </label>
                                <input type="number" min="0" step=".1" id="ios_min_version" name="ios_min_version"
                                        value="{{$config['min_version']??''}}" class="form-control"
                                        placeholder="{{ translate('EX: 4.0') }}">
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="{{config('app.mode')!='demo'?'submit':'button'}}"
                                    class="btn btn-primary demo-form-submit">{{translate('save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'appSettingsHelpModal', 'titleKey' => 'help_app_settings_title', 'pageKey' => 'help_app_settings_page'])
@endsection
