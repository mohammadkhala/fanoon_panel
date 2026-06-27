<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container text-capitalize">
            <div class="navbar-vertical-footer-offset">
                <div class="d-flex align-items-center gap-3 py-2 px-3 justify-content-between">
                    @php
                        $logo = Helpers::get_business_settings('logo') ?? '';
                    @endphp
                    <a class="navbar-brand w-75" href="{{ route('admin.dashboard') }}" aria-label="Front">
                        <img class="navbar-brand-logo"
                             alt="{{ translate('logo') }}"
                             src="{{ Helpers::onErrorImage($logo, asset('storage/ecommerce').'/'.$logo, asset('assets/admin/img/160x160/img2.jpg'), 'ecommerce/') }}"
                        >
                        <img class="navbar-brand-logo-mini"
                             alt="{{ translate('logo') }}"
                             src="{{ Helpers::onErrorImage($logo, asset('storage/ecommerce').'/'.$logo, asset('assets/admin/img/160x160/img2.jpg'), 'ecommerce/') }}"
                        >
                    </a>

                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker close mt-1">
                        <i class="tio-first-page navbar-vertical-aside-toggle-short-align"></i>
                        <i class="tio-last-page navbar-vertical-aside-toggle-full-align"
                           title="{{ translate('Expand') }}"></i>
                    </button>
                </div>

                <div class="navbar-vertical-content">
                    <div class="sidebar--search-form py-3">
                        <div class="search--form-group">
                            <button type="button" class="btn"><i class="tio-search"></i></button>
                            <input type="text" class="js-form-search form-control form--control" id="search-bar-input"
                                   placeholder="{{ translate('Search Menu...') }}">
                        </div>
                    </div>

                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin')?'show':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.dashboard')}}" title="{{translate('Dashboards')}}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('dashboard')}}
                                </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle">{{translate('orders_management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/orders*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping-cart nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('orders')}}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/order*')?'block':'none'}}">
                                @php($oc = $sidebarOrderCounts ?? ['all'=>0,'pending'=>0,'confirmed'=>0,'processing'=>0,'out_for_delivery'=>0,'delivered'=>0,'returned'=>0,'failed'=>0,'canceled'=>0])
                                <li class="nav-item {{Request::is('admin/orders/list/all')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.orders.list',['all'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('all')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{$oc['all']}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/pending')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['pending'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('pending')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{$oc['pending']}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/confirmed')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['confirmed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('confirmed')}}
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                {{$oc['confirmed']}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/processing')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['processing'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('processing')}}
                                                <span class="badge badge-soft-warning badge-pill ml-1">
                                                {{$oc['processing']}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/out_for_delivery')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['out_for_delivery'])}}"
                                       title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('out_for_delivery')}}
                                                <span class="badge badge-soft-warning badge-pill ml-1">
                                                {{$oc['out_for_delivery']}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/delivered')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['delivered'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('delivered')}}
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                {{$oc['delivered']}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/returned')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['returned'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('returned')}}
                                                <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{$oc['returned']}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/failed')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['failed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('failed')}}
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{$oc['failed']}}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/orders/list/canceled')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['canceled'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('canceled')}}
                                                <span class="badge badge-soft-dark badge-pill ml-1">
                                                {{$oc['canceled']}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle"
                                   title="{{translate('User Management')}}">{{translate('user_management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/customer*') || Request::is('admin/user-types*') || Request::is('admin/reviews*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('customers')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/customer*') || Request::is('admin/user-types*') || Request::is('admin/reviews*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/customer/list') || Request::is('admin/customer/view*')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.customer.list')}}"
                                       title="{{translate('Customer List')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('Customer List')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/user-types*')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.user-type.index')}}"
                                       title="{{translate('User Types')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('User Types')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/reviews*')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.reviews.list')}}"
                                       title="{{translate('Product reviews')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('Product reviews')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle">{{translate('products_management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/product*') || Request::is('admin/attribute*') || Request::is('admin/tag*') || Request::is('admin/design-template*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                            >
                                <i class="tio-premium-outlined nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('products')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/product*') || Request::is('admin/attribute*') || Request::is('admin/tag*') || Request::is('admin/design-template*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/product/add-new')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.product.add-new')}}"
                                       title="{{translate('add new product')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('add')}} {{translate('new')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/product/list')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.product.list')}}"
                                       title="{{translate('product list')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('list')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/attribute*')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.attribute.add-new')}}"
                                       title="{{translate('attributes')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('attributes')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/tag*')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.tag.list')}}"
                                       title="{{translate('product_tags')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('product_tags')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/product/bulk-import')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.product.bulk-import')}}"
                                       title="{{translate('bulk import')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('bulk_import')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/product/bulk-export')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.product.bulk-export')}}"
                                       title="{{translate('bulk export')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('bulk_export')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/design-template/add-new')?'active':''}}">
                                    <a class="nav-link" href="{{ route('admin.design-template.add-new') }}"
                                       title="{{ translate('design_templates') ?: 'قوالب التصميم' }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">إضافة قالب</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/design-template/by-category')?'active':''}}">
                                    <a class="nav-link" href="{{ route('admin.design-template.by-category') }}"
                                       title="القوالب حسب التصنيف">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">القوالب حسب التصنيف</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/category*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-category nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('product_categories')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/category*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/category/add')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.category.add')}}"
                                       title="{{translate('main_categories')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('main_categories')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/category/add-sub-category')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.category.add-sub-category')}}"
                                       title="{{translate('sub_categories')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('sub_categories')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle">{{translate('cities_and_delivery')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/areas*') || Request::is('admin/business-settings/cities*') || Request::is('admin/shipping-company*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-truck nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('cities_and_delivery')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/business-settings/areas*') || Request::is('admin/business-settings/cities*') || Request::is('admin/shipping-company*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/business-settings/cities*')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.business-settings.cities')}}" title="{{translate('cities')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('cities')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/business-settings/areas*')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.business-settings.areas')}}" title="{{translate('delivery_fee')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('delivery_fee')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/shipping-company*')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.shipping-company.index')}}" title="{{translate('shipping_companies')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('shipping_companies')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle"
                                   title="{{translate('Promotion Management')}}">{{translate('Promotion Management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/banner*') || Request::is('admin/flash-sale*') || Request::is('admin/coupon*') || Request::is('admin/client*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping-basket-add nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('marketing')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/banner*') || Request::is('admin/flash-sale*') || Request::is('admin/coupon*') || Request::is('admin/client*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/banner*')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.banner.add-new')}}"
                                       title="{{translate('banner')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('banner')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/flash-sale*')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.flash-sale.index')}}"
                                       title="{{translate('Flash Sale')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('Flash Sale')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/coupon*')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.coupon.add-new')}}"
                                       title="{{translate('coupon')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('coupon')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/client*')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.client.add-new')}}"
                                       title="{{ translate('our_clients') ?: 'عملاؤنا يثقون بنا' }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('our_clients') ?: 'عملاؤنا يثقون بنا' }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle" title="{{ translate('Contact Us') }}">{{ translate('Contact Us') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/contact-us*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-message-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Contact Us') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/contact-us*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/contact-us*') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.contact-us.index') }}" title="{{ translate('contact_us_messages') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('contact_us_messages') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        @if(!config('feature_flags.hide_send_notification_page', true))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/notification*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.notification.add-new')}}"
                            >
                                <i class="tio-notifications nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('send')}} {{translate('notification')}}
                                </span>
                            </a>
                        </li>
                        @endif

                        <li class="nav-item">
                            <small class="nav-subtitle"
                                   title="{{translate('report_and_analytics')}}">{{translate('report_and_analytics')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/report*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-chart-pie-1 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('report_and_analytics') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/report*') ? 'block' : 'none' }}">
                                <li class="nav-item {{Request::is('admin/report/earning')?'active':''}}">
                                    <a class="nav-link" href="{{ route('admin.report.earning') }}" title="{{ translate('earning_report') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('earning_report') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/report/order')?'active':''}}">
                                    <a class="nav-link" href="{{ route('admin.report.order') }}" title="{{ translate('order_report') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('order_report') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/report/product-report')?'active':''}}">
                                    <a class="nav-link" href="{{ route('admin.report.product-report') }}" title="{{ translate('product_report') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('product_report') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/report/sale-report')?'active':''}}">
                                    <a class="nav-link" href="{{ route('admin.report.sale-report') }}" title="{{ translate('sale_report') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('sale_report') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/report/best-selling-products')?'active':''}}">
                                    <a class="nav-link" href="{{ route('admin.report.best-selling-products') }}" title="{{ translate('best_selling_products') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('best_selling_products') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/report/top-customers')?'active':''}}">
                                    <a class="nav-link" href="{{ route('admin.report.top-customers') }}" title="{{ translate('top_customers') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('top_customers') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle"
                                   title="{{translate('pages_section')}}">{{translate('pages_section')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/social-media') || Request::is('admin/business-settings/return-page') || Request::is('admin/business-settings/about-us') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/terms-and-conditions') ||
                            Request::is('admin/business-settings/cancellation-page') || Request::is('admin/business-settings/refund-page') ?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-pages nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('pages_section')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/business-settings/social-media') || Request::is('admin/business-settings/return-page') || Request::is('admin/business-settings/about-us') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/terms-and-conditions') ||
                                    Request::is('admin/business-settings/cancellation-page') || Request::is('admin/business-settings/refund-page')?'block':'none'}}">

                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/return-page') || Request::is('admin/business-settings/about-us') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/terms-and-conditions') ||
                                     Request::is('admin/business-settings/cancellation-page') || Request::is('admin/business-settings/refund-page')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.business-settings.about-us')}}"
                                       title="{{translate('pages')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('pages')}}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/business-settings/social-media')?'active':''}}">
                                    <a class="nav-link "
                                       href="{{route('admin.business-settings.social-media')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('Social Media')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle"
                                   title="{{translate('Business Section')}}">{{translate('system_settings')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        @if(!config('feature_flags.hide_webhooks', true))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/webhook*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.webhook.list')}}"
                               title="{{ translate('webhooks') }}">
                                <i class="tio-link nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('webhooks') }}</span>
                            </a>
                        </li>
                        @endif

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/*') || Request::is('admin/database/download') || Request::is('admin/branch/settings')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.business-settings.ecom-setup')}}"
                            >
                                <i class="tio-settings nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('business_Setup')}}</span>
                            </a>
                        </li>

                        @if(count(config('addon_admin_routes'))>0)
                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/payment/configuration/*') || Request::is('admin/sms/configuration/*')?'active':''}} mb-5">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                   href="javascript:">
                                    <i class="tio-puzzle nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('Addon Menus')}}</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('admin/payment/configuration/*') || Request::is('admin/sms/configuration/*')?'block':'none'}}">
                                    @foreach(config('addon_admin_routes') as $routes)
                                        @foreach($routes as $route)
                                            <li class="navbar-vertical-aside-has-menu {{Request::is($route['path'])  ? 'active' :''}}">
                                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                                   href="{{ $route['url'] }}" title="{{ translate($route['name']) }}">
                                                    <span class="tio-circle nav-indicator-icon"></span>
                                                    <span class="text-truncate">{{ translate($route['name']) }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    @endforeach
                                </ul>
                            </li>
                        @endif

                        <li class="nav-item p-top-100px support-image-box">
                            <a class="d-flex align-items-center justify-content-center py-3 px-2" href="https://wa.me/970599814758" target="_blank" rel="noopener" title="{{ translate('contact us on WhatsApp') }}">
                                <img src="{{ asset('assets/admin/img/call-support.png') }}" alt="{{ translate('contact us on WhatsApp') }}" class="support-image">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </aside>
</div>

<div id="sidebarCompact" class="d-none">

</div>

@push('script_2')
    <script>
        "use script";
        // Active Item scrollTop show
        $(window).on('load', function () {
            if ($(".navbar-vertical-content li.active").length) {
                $('.navbar-vertical-content').animate({
                    scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
                }, 10);
            }
        });

        // Search
        var $items = $('.navbar-vertical-content .navbar-nav > li');
        $('#search-bar-input').on('keyup', function () {
            var keyword = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

            $items.hide(); // hide all initially

            if (keyword === '') {
                $items.show(); // show all if input is empty
                return;
            }

            var showItems = $();

            $items.each(function (i, el) {
                var $el = $(el);
                var text = $el.text().replace(/\s+/g, ' ').toLowerCase();

                if ($el.hasClass('nav-item') && text.includes(keyword)) {
                    // Matched .nav-item — show it and everything until next .nav-item
                    showItems = showItems.add($el);
                    $el.nextAll().each(function () {
                        var $next = $(this);
                        if ($next.hasClass('nav-item')) {
                            return false; // stop at next nav-item
                        }
                        showItems = showItems.add($next);
                    });
                } else if (!$el.hasClass('nav-item') && text.includes(keyword)) {
                    // Matched non-nav-item — show just this
                    showItems = showItems.add($el);
                }
            });

            showItems.show();
        });
    </script>
@endpush



