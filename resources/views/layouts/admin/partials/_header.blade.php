@if (config('app.mode')=='demo')
    <div class="__announcement-bar" style="background-image: url({{ asset('assets/website-top-header.png') }})">
        <div class="container">
            <div class="wrapper">
                <div class="txt">
                    This is a demo website - Buy genuine Hexacom using our official link !
                </div>
                <a href="https://codecanyon.net/item/emarket-ecommerce-app-with-laravel-admin-panel-delivery-man-app/31157454?s_rank=20" class="click" target="_blank">Click Now <img src="{{ asset('assets/arrowww.png') }}" alt=""></a>
                <a href="https://codecanyon.net/item/emarket-ecommerce-app-with-laravel-admin-panel-delivery-man-app/31157454?s_rank=20" class="btn btn-sm" style="background-color: #FF7500; color:#ffffff" target="_blank">Buy Now</a>
            </div>
        </div>
    </div>
@endif
<div id="headerMain" class="d-none">
    <header id="header"
            class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-flush navbar-container navbar-bordered">
        <div class="navbar-nav-wrap">
            <div class="navbar-brand-wrapper">
                @php($logo = Helpers::get_business_settings('logo'))
                <a class="navbar-brand" href="{{route('admin.dashboard')}}" aria-label="">
                    <img class="navbar-brand-logo"
                         src="{{Helpers::onErrorImage(
                            $logo,
                            asset('storage/ecommerce').'/' . $logo,
                            asset('assets/admin/img/160x160/img2.jpg') ,
                            'ecommerce/')}}" alt="{{ translate('Logo') }}">
                    <img class="navbar-brand-logo-mini"
                         src="{{Helpers::onErrorImage(
                            $logo,
                            asset('storage/ecommerce').'/' . $logo,
                            asset('assets/admin/img/160x160/img2.jpg') ,
                            'ecommerce/')}}"
                         alt="{{ translate('Logo') }}">
                </a>
            </div>

            <div class="navbar-nav-wrap-content-left d-xl-none">
                <button type="button" class="js-navbar-vertical-aside-toggle-invoker close mr-3">
                    <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip"
                       data-placement="right" title="Collapse"></i>
                    <i class="tio-last-page navbar-vertical-aside-toggle-full-align"
                       data-template='<div class="tooltip d-none d-sm-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                       data-toggle="tooltip" data-placement="right" title="Expand"></i>
                </button>
            </div>

            <div class="navbar-nav-wrap-content-right">
                <ul class="navbar-nav align-items-center flex-row">
                    <li class="nav-item d-none d-md-inline-block mr-md-2 align-self-center">
                        <div id="unified-search-wrap" class="hs-unfold dropdown elite-unified-search-wrap position-relative">
                            <label class="elite-unified-search-inner mb-0" for="unified-search-input">
                                <span class="elite-unified-search-icon" aria-hidden="true"><i class="tio-search"></i></span>
                                <input type="search" id="unified-search-input" class="form-control elite-unified-search-input"
                                       placeholder="{{ translate('unified_search_placeholder') }}" autocomplete="off" role="searchbox">
                            </label>
                            <div id="unified-search-dropdown" class="dropdown-menu dropdown-menu-right elite-unified-search-dropdown p-0" style="display: none;">
                                <div class="elite-unified-search-dropdown-head text-muted">{{ translate('orders') }}</div>
                                <div id="unified-search-orders"></div>
                                <div class="dropdown-divider my-0"></div>
                                <div class="elite-unified-search-dropdown-head text-muted">{{ translate('products') }}</div>
                                <div id="unified-search-products"></div>
                                <div class="dropdown-divider my-0"></div>
                                <div class="elite-unified-search-dropdown-head text-muted">{{ translate('customers') }}</div>
                                <div id="unified-search-customers"></div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle"
                               href="{{route('admin.orders.list',['status'=>'all'])}}">
                                <i class="tio-shopping-cart-outlined"></i>
                                <span class="btn-status btn-status-danger">{{\App\Models\Order::notPos()->where(['checked' => 0])->count()}}</span>
                            </a>
                        </div>
                    </li>

                    <li class="nav-item d-none d-sm-inline-block">
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle" href="javascript:;"
                               data-hs-unfold-options='{"target": "#lowStockNavbarDropdown", "type": "css-animation"}'
                               aria-label="{{ translate('low_stock_products') }}">
                                <i class="tio-warning-outlined"></i>
                                @if(($lowStockCount ?? 0) > 0)
                                    <span class="btn-status btn-status-warning">{{ $lowStockCount > 99 ? '99+' : $lowStockCount }}</span>
                                @endif
                            </a>
                            <div id="lowStockNavbarDropdown"
                                 class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu min-w-280px">
                                <div class="dropdown-item-text text-wrap">
                                    <span class="text-capitalize font-weight-bold">{{ translate('low_stock_products') }}</span>
                                </div>
                                <div class="dropdown-divider"></div>
                                @forelse(($lowStockProducts ?? []) as $p)
                                    <a class="dropdown-item py-2" href="{{ route('admin.product.edit', [$p->id]) }}">
                                        <span class="text-truncate d-block" title="{{ $p->name }}">{{ \Illuminate\Support\Str::limit($p->name, 32) }}</span>
                                        <small class="text-muted">{{ translate('stock') }}: {{ $p->total_stock }}</small>
                                    </a>
                                @empty
                                    <div class="dropdown-item-text text-muted">{{ translate('no_low_stock_products') }}</div>
                                @endforelse
                                @if(($lowStockCount ?? 0) > 0)
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-primary font-weight-bold" href="{{ route('admin.product.list', ['stock_filter' => 'low_stock']) }}">
                                        {{ translate('view_all') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </li>

                    <li class="nav-item ml-md-3">
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker navbar-dropdown-account-wrapper media align-items-center gap-3 bg-transparent dropdown-toggle dropdown-toggle-left-arrow" href="javascript:;"
                               data-hs-unfold-options='{
                                     "target": "#accountNavbarDropdown",
                                     "type": "css-animation"
                                   }'>
                                <div class="d-none d-md-block media-body text-right">
                                    <h5 class="profile-name text-capitalize mb-0">{{auth('admin')->user()->f_name}}</h5>
                                    <span class="fs-12 text-capitalize">{{ translate('Super Admin') }}</span>
                                </div>
                                <div class="avatar avatar-sm avatar-circle">
                                    <img class="avatar-img"
                                         src="{{auth('admin')->user()->image_fullpath}}"
                                         alt="{{ translate('Image') }}">
                                    <span class="avatar-status avatar-sm-status avatar-status-success"></span>
                                </div>
                            </a>

                            <div id="accountNavbarDropdown"
                                 class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account">
                                <div class="dropdown-item-text">
                                    <div class="media gap-3 align-items-center">
                                        <div class="avatar avatar-sm avatar-circle mr-2" style="min-width: 40px">
                                            <img class="avatar-img"
                                                 src="{{auth('admin')->user()->image_fullpath}}"
                                                 alt="{{ translate('Image') }}">
                                        </div>
                                        <div class="media-body">
                                            <span class="card-title h5">{{auth('admin')->user()->f_name}}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="{{route('admin.settings')}}">
                                    <span class="text-truncate pr-2" title="Profile">{{\App\CentralLogics\translate('profile')}}</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="javascript:" onclick="Swal.fire({
                                    title:'{{translate("Do you want to logout?")}}',
                                    showDenyButton: true,
                                    showCancelButton: true,
                                    confirmButtonColor: '#673ab7',
                                    cancelButtonColor: '#363636',
                                    confirmButtonText: '{{translate("Yes")}}',
                                    cancelButtonText: '{{translate("No")}}',
                                    denyButtonText: `{{translate("Don't Logout")}}`,
                                    }).then((result) => {
                                    if (result.value) {
                                    location.href='{{route('admin.auth.logout')}}';
                                    } else{
                                    Swal.fire('{{ translate("Canceled")}}', '', 'info')
                                    }
                                    })">
                                    <span class="text-truncate pr-2" title="Sign out">{{\App\CentralLogics\translate('sign_out')}}</span>
                                </a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>
</div>
<div id="headerFluid" class="d-none"></div>
<div id="headerDouble" class="d-none"></div>
