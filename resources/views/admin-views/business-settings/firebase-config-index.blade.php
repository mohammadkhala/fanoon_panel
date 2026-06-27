@extends('layouts.admin.app')

@section('title', translate('Firebase Settings'))

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

        <div class="card">
            @php($data=Helpers::get_business_settings('firebase_message_config'))
            @php($serviceFileContent = Helpers::get_business_settings('push_notification_service_file_content'))
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="mb-0">{{ translate('Firebase_Message_Config') }}</h5>
                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#firebaseConfigHelpModal" title="{{ translate('help_firebase_config_btn') }}">
                    <i class="tio-book-outlined"></i> {{ translate('help_firebase_config_btn') }}
                </button>
            </div>
            <div class="card-body">
                <form action="{{config('app.mode')!='demo'?route('admin.business-settings.firebase_message_config'):'javascript:'}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-4">
                        <label class="input-label">{{translate('service_file_content')}}
                            <i class="tio-info cursor-pointer" data-toggle="tooltip" data-placement="top"
                               title="{{ translate('select and copy all the service file content and add here') }}">
                            </i>
                        </label>
                        <textarea name="push_notification_service_file_content" class="form-control" rows="10"
                                  placeholder="{{ translate('Paste Firebase service account JSON content here') }}">{{config('app.mode')!='demo'?(is_array($serviceFileContent)?json_encode($serviceFileContent,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE):(string)($serviceFileContent??'')):''}}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group">
                                <label>{{translate('API Key')}}</label>
                                <input type="text" placeholder="" class="form-control" name="apiKey"
                                       value="{{config('app.mode')!='demo'?($data['apiKey']??''):''}}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group">
                                <label>{{translate('Auth Domain')}}</label>
                                <input type="text" class="form-control" name="authDomain" value="{{config('app.mode')!='demo'?($data['authDomain']??''):''}}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group">
                                <label>{{translate('Project ID')}}</label>
                                <input type="text" class="form-control" name="projectId" value="{{config('app.mode')!='demo'?($data['projectId']??''):''}}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group">
                                <label>{{translate('Storage Bucket')}}</label>
                                <input type="text" class="form-control" name="storageBucket" value="{{config('app.mode')!='demo'?($data['storageBucket']??''):''}}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group">
                                <label>{{translate('Messaging Sender ID')}}</label>
                                <input type="text" placeholder="" class="form-control" name="messagingSenderId"
                                       value="{{config('app.mode')!='demo'?($data['messagingSenderId']??''):''}}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group">
                                <label>{{translate('App ID')}}</label>
                                <input type="text" placeholder="" class="form-control" name="appId"
                                       value="{{config('app.mode')!='demo'?($data['appId']??''):''}}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                <button type="{{config('app.mode')!='demo'?'submit':'button'}}"
                                        class="btn btn-primary demo-form-submit">{{translate('save')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'firebaseConfigHelpModal', 'titleKey' => 'help_firebase_config_title', 'pageKey' => 'help_firebase_config_page'])
@endsection

