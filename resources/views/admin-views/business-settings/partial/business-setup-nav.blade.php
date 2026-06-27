<ul class="list-unstyled business-setup-nav-btns d-flex flex-wrap gap-2">
    {{-- إعدادات أساسية --}}
    <li class="{{Request::is('admin/business-settings/ecom-setup')?'active':''}}"><a href="{{route('admin.business-settings.ecom-setup')}}" class="btn-nav">{{translate('business_setup')}}</a></li>
    @if(config('feature_flags.hide_branch_management', true))
    <li class="{{Request::is('admin/branch/settings')?'active':''}}"><a href="{{route('admin.branch.settings')}}" class="btn-nav">{{translate('store_information')}}</a></li>
    @endif
    {{-- مخفي من القائمة فقط؛ المسار ما زال يعمل عند الحاجة --}}
    <li class="d-none {{Request::is('admin/business-settings/app-setting')?'active':''}}"><a href="{{route('admin.business-settings.app_setting')}}" class="btn-nav">{{translate('app_settings')}}</a></li>
    {{-- البريد والتواصل --}}
    <li class="d-none {{Request::is('admin/business-settings/mail-config')?'active':''}}"><a href="{{route('admin.business-settings.mail-config')}}" class="btn-nav">{{translate('Mail_Config')}}</a></li>
    <li class="{{Request::is('admin/business-settings/social-media-chat')?'active':''}}"><a href="{{route('admin.business-settings.social-media-chat')}}" class="btn-nav">{{translate('social_media_chat')}}</a></li>
    <li class="{{Request::is('admin/business-settings/store-location-map')?'active':''}}"><a href="{{route('admin.business-settings.store-location-map')}}" class="btn-nav">{{translate('store_location_map')}}</a></li>
    {{-- الخصوصية --}}
    <li class="{{Request::is('admin/business-settings/cookies-setup')?'active':''}}"><a href="{{route('admin.business-settings.cookies-setup')}}" class="btn-nav">{{translate('cookies_setup')}}</a></li>
    {{-- التسجيل والمصادقة --}}
    <li class="{{Request::is('admin/business-settings/login-setup')?'active':''}}"><a href="{{route('admin.business-settings.login-setup')}}" class="btn-nav">{{translate('Customer Login')}}</a></li>
    <li class="{{Request::is('admin/business-settings/otp-setup')?'active':''}}"><a href="{{route('admin.business-settings.otp-setup')}}" class="btn-nav">{{translate('OTP_setup')}}</a></li>
    <li class="d-none {{Request::is('admin/business-settings/firebase-auth')?'active':''}}"><a href="{{route('admin.business-settings.firebase-auth')}}" class="btn-nav">{{translate('firebase_auth')}}</a></li>
    {{-- الإشعارات و Firebase --}}
    <li class="{{Request::is('admin/business-settings/firebase-message-config')?'active':''}}"><a href="{{route('admin.business-settings.firebase_message_config_index')}}" class="btn-nav">{{translate('Firebase_Message_Config')}}</a></li>
    <li class="{{Request::is('admin/business-settings/fcm-index')?'active':''}}"><a href="{{route('admin.business-settings.fcm-index')}}" class="btn-nav">{{translate('Push_Notification')}}</a></li>
</ul>
