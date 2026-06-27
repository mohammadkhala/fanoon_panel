@extends('layouts.admin.app')

@section('title', translate('FCM Settings'))

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

        <div class="alert alert-info d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="tio-info-outlined"></i>
            <span>{{ translate('Firebase configuration and Service File Content are configured in') }} <a href="{{ route('admin.business-settings.firebase_message_config_index') }}" class="alert-link">{{ translate('Firebase_Message_Config') }}</a>.</span>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="mb-0">{{ translate('push_messages') }}</h5>
                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#fcmHelpModal" title="{{ translate('help_fcm_btn') }}">
                    <i class="tio-book-outlined"></i> {{ translate('help_fcm_btn') }}
                </button>
            </div>
            <div class="card-body">
                <form action="{{route('admin.business-settings.update-fcm-messages')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        @php($orderPendingMessage=\App\Models\BusinessSetting::where('key','order_pending_message')->first()?->value ?? '{"status":0,"message":""}')
                        @php($data=json_decode($orderPendingMessage,true) ?? [])
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="pending_status">
                                        <input type="checkbox" name="pending_status" class="switcher_input"
                                                value="1" id="pending_status" {{($data['status']??0)==1?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="pending_status" class="text-dark mb-0 cursor-pointer">{{translate('order')}} {{translate('pending')}} {{translate('message')}}</label>
                                </div>
                                <textarea name="pending_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($orderConfirmationMessage=\App\Models\BusinessSetting::where('key','order_confirmation_msg')->first()?->value ?? '{"status":0,"message":""}')
                        @php($data=json_decode($orderConfirmationMessage,true) ?? [])
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="confirm_status">
                                        <input type="checkbox" name="confirm_status" class="switcher_input"
                                                value="1" id="confirm_status" {{($data['status']??0)==1?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="confirm_status" class="text-dark mb-0 cursor-pointer">{{translate('order')}} {{translate('confirmation')}} {{translate('message')}}</label>
                                </div>
                                <textarea name="confirm_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($orderProcessingMessage=\App\Models\BusinessSetting::where('key','order_processing_message')->first()?->value ?? '{"status":0,"message":""}')
                        @php($data=json_decode($orderProcessingMessage,true) ?? [])
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="processing_status">
                                        <input type="checkbox" name="processing_status"
                                                class="switcher_input"
                                                value="1" id="processing_status" {{($data['status']??0)==1?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="processing_status" class="text-dark mb-0 cursor-pointer">{{translate('order')}} {{translate('processing')}} {{translate('message')}}</label>
                                </div>
                                <textarea name="processing_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($orderOutForDeliveryMessage=\App\Models\BusinessSetting::where('key','out_for_delivery_message')->first()?->value ?? '{"status":0,"message":""}')
                        @php($data=json_decode($orderOutForDeliveryMessage,true) ?? [])
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="out_for_delivery">
                                        <input type="checkbox" name="out_for_delivery_status"
                                                class="switcher_input"
                                                value="1" id="out_for_delivery" {{($data['status']??0)==1?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="out_for_delivery" class="text-dark mb-0 cursor-pointer">{{translate('Order_Out_for_delivery_Message')}}</label>
                                </div>
                                <textarea name="out_for_delivery_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($orderDeliveredMessage=\App\Models\BusinessSetting::where('key','order_delivered_message')->first()?->value ?? '{"status":0,"message":""}')
                        @php($data=json_decode($orderDeliveredMessage,true) ?? [])
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="delivered_status">
                                        <input type="checkbox" name="delivered_status" class="switcher_input"
                                                value="1" id="delivered_status" {{($data['status']??0)==1?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="delivered_status" class="text-dark mb-0 cursor-pointer">{{translate('Order_Delivered_Message')}}</label>
                                </div>
                                <textarea name="delivered_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($data=Helpers::get_business_settings('returned_message'))
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher"
                                            for="returned_status">
                                        <input type="checkbox" name="returned_status"
                                                class="switcher_input"
                                                value="1"
                                                id="returned_status" {{(isset($data['status']) && $data['status']==1)?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="returned_status" class="text-dark mb-0 cursor-pointer">{{translate('Order_returned_message')}}</label>
                                </div>
                                <textarea name="returned_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($data=Helpers::get_business_settings('failed_message'))
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="failed_status">
                                        <input type="checkbox" name="failed_status"
                                                class="switcher_input"
                                                value="1"
                                                id="failed_status" {{(isset($data['status']) && $data['status']==1)?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="failed_status" class="text-dark mb-0 cursor-pointer">{{translate('Order_failed_message')}}</label>
                                </div>
                                <textarea name="failed_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($data=Helpers::get_business_settings('canceled_message'))
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="canceled_status">
                                        <input type="checkbox" name="canceled_status"
                                                class="switcher_input"
                                                value="1"
                                                id="canceled_status" {{(isset($data['status']) && $data['status']==1)?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="canceled_status" class="text-dark mb-0 cursor-pointer">{{translate('Order_canceled_message')}}</label>
                                </div>

                                <textarea name="canceled_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

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

    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'fcmHelpModal', 'titleKey' => 'help_fcm_title', 'pageKey' => 'help_fcm_page'])
@endsection
