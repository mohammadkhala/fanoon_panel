@extends('layouts.admin.app')

@section('title', translate('Firebase Auth'))

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
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="mb-0">{{ translate('firebase_auth') }}</h5>
                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#firebaseAuthHelpModal" title="{{ translate('help_firebase_auth_btn') }}">
                    <i class="tio-book-outlined"></i> {{ translate('help_firebase_auth_btn') }}
                </button>
            </div>
            <div class="card-body">
                <form action="{{route('admin.business-settings.update-firebase-auth')}}" method="post">
                    @csrf
                    <div class="row">
                        <?php
                            $firebaseOtp = \App\CentralLogics\Helpers::get_business_settings('firebase_otp_verification') ?? [];
                        ?>
                        <div class="col-md-6 mt-5">
                            <div class="d-flex justify-content-between align-items-center border rounded mb-2 px-3 py-2">
                                <h5 class="mb-0">{{translate('Firebase Auth Verification Status')}}</h5>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input" name="status" id="firebase_auth_status" {{ ($firebaseOtp['status'] ?? 0) == 1 ? 'checked' : '' }}>
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label" for="web_api_key">{{translate('web_api_key')}}</label>
                                <input type="text" value="{{config('app.mode')!='demo' ? ($firebaseOtp['web_api_key'] ?? '') : ''}}" name="web_api_key" id="web_api_key"
                                       class="form-control" placeholder="">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="{{config('app.mode')!='demo'?'submit':'button'}}"
                                class="btn btn-primary demo-form-submit">{{translate('update')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'firebaseAuthHelpModal', 'titleKey' => 'help_firebase_auth_title', 'pageKey' => 'help_firebase_auth_page'])
@endsection

@push('script_2')
<script>
    $(document).ready(function () {
        const $firebaseAuthStatus = $('#firebase_auth_status');
        const $webApiKeyInput = $('#web_api_key');

        // Function to toggle the readonly state of the input field
        function toggleWebApiKey() {
            const isChecked = $firebaseAuthStatus.is(':checked');
            if (isChecked) {
                $webApiKeyInput.prop('readonly', false);  // Make editable
            } else {
                $webApiKeyInput.prop('readonly', true);   // Make readonly but keep value
            }
        }

        // Initial call to set the correct state on page load
        toggleWebApiKey();

        // Add event listener to handle checkbox changes
        $firebaseAuthStatus.on('change', toggleWebApiKey);
    });
</script>
@endpush
