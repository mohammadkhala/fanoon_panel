@extends('layouts.admin.app')

@section('title', translate('Customer List'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/customer.png')}}" alt="{{ translate('customer') }}">
                {{translate('customers')}}
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#customerListInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_customer_list_btn') }}
            </button>
        </div>

        @include('admin-views.partials._help-instructions-modal', ['modalId' => 'customerListInstructionsModal', 'titleKey' => 'help_customer_list_title', 'pageKey' => 'help_customer_list_page'])

        <div class="card">
            <div class="p-3">
                <form action="{{ request()->url() }}" method="GET" class="filter-form mb-4">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <div class="bg-light rounded p-3 mb-2">
                        <div class="row align-items-end g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label small text-capitalize mb-1">{{ translate('search_customer') }}</label>
                                <input type="search" name="search" class="form-control form-control-sm"
                                       placeholder="{{ translate('Search by name or phone') }}" value="{{ $search ?? '' }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="row align-items-end g-3">
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label small text-capitalize mb-1">{{ translate('Has orders') }}</label>
                                <select class="form-control form-control-sm" name="has_orders" id="has_orders">
                                    <option value="" {{ ($hasOrders ?? '') === '' ? 'selected' : '' }}>{{ translate('all') }}</option>
                                    <option value="1" {{ ($hasOrders ?? '') === '1' ? 'selected' : '' }}>{{ translate('Yes') }}</option>
                                    <option value="0" {{ ($hasOrders ?? '') === '0' ? 'selected' : '' }}>{{ translate('No') }}</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label small text-capitalize mb-1">{{ translate('user_type') }}</label>
                                <select class="form-control form-control-sm" name="user_type_id" id="user_type_id">
                                    <option value="">{{ translate('all') }} {{ translate('user_type') }}</option>
                                    @foreach($userTypes as $type)
                                        <option value="{{ $type->id }}" {{ (string)$userTypeId === (string)$type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label small text-capitalize mb-1">{{ translate('customer_segment') }}</label>
                                <select class="form-control form-control-sm" name="segment" id="segment">
                                    <option value="" {{ ($segment ?? '') === '' ? 'selected' : '' }}>{{ translate('all') }}</option>
                                    <option value="vip" {{ ($segment ?? '') === 'vip' ? 'selected' : '' }}>{{ translate('segment_vip') }}</option>
                                    <option value="frequent" {{ ($segment ?? '') === 'frequent' ? 'selected' : '' }}>{{ translate('segment_frequent') }}</option>
                                    <option value="new" {{ ($segment ?? '') === 'new' ? 'selected' : '' }}>{{ translate('segment_new') }}</option>
                                    <option value="inactive" {{ ($segment ?? '') === 'inactive' ? 'selected' : '' }}>{{ translate('segment_inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 d-flex gap-2 align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="tio-checkmark-circle-outlined mr-1"></i>{{ translate('Show_Data') }}
                                </button>
                                <a href="{{ route('admin.customer.list') }}" class="btn btn-soft-secondary btn-sm flex-grow-1 text-center">{{ translate('clear') }}</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="card-section-header d-flex flex-wrap justify-content-between align-items-center gy-2 border-top pt-3">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <h5 class="card-section-title mb-0 d-flex align-items-center gap-2">
                            <i class="tio-people-outlined text-primary"></i>
                            <span>{{ translate('Customer List') }}</span>
                            <span class="badge badge-soft-primary rounded-pill px-3 py-1">{{ $customers->total() }}</span>
                        </h5>
                    </div>
                    <a href="{{ route('admin.customer.export', request()->only(['search', 'has_orders', 'user_type_id', 'segment'])) }}"
                       class="btn btn-soft-secondary btn-sm d-flex align-items-center gap-1"
                       title="{{ translate('help_order_export') }}">
                        <img width="14" src="{{asset('assets/admin/img/icons/excel.png')}}" alt="{{ translate('excel') }}">
                        {{ translate('excel') }}
                    </a>
                </div>
            </div>
            <div class="table-responsive datatable-custom">
                <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>{{translate('customer_name')}}</th>
                            <th>{{translate('contact_info')}}</th>
                            <th>{{translate('user_type')}}</th>
                            <th>{{translate('total_Order')}}</th>
                            <th>{{translate('Loyalty points')}}</th>
                            <th class="text-center">{{translate('actions')}}</th>
                        </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($customers as $key=>$customer)
                        <tr>
                            <td>
                                {{$customers->firstitem()+$key}}
                            </td>
                            <td>
                                <a class="text-dark media gap-3 align-items-center" href="{{route('admin.customer.view',[$customer['id']])}}">
                                    <div class="avatar rounded-circle">
                                        <img class="img-fit rounded-circle" src="{{$customer['image_fullpath']}}" alt="{{ translate('customer') }}">
                                    </div>
                                    <div class="media-body">{{$customer['f_name']." ".$customer['l_name']}}</div>
                                </a>
                            </td>
                            <td>
                                <div><a class="text-dark" href="tel:{{$customer['phone']}}">{{$customer['phone']}}</a></div>
                            </td>
                            <td>
                                <span class="badge badge-soft-secondary px-2 py-1 fs-14">{{ $customer->userType?->name ?? '—' }}</span>
                                @if($customer->requestedUserType)
                                    <span class="badge badge-soft-warning ml-1 px-2 py-1 fs-14" title="{{ translate('Pending approval') }}">{{ $customer->requestedUserType->name }}</span>
                                @endif
                                @php($orderCount = $customer->orders->count())
                                @if($orderCount >= 10)
                                    <span class="badge badge-soft-success ml-1 px-2 py-1 fs-12" title="{{ translate('customer_badge_trusted') }}">{{ translate('customer_badge_trusted') }}</span>
                                @elseif($orderCount >= 5)
                                    <span class="badge badge-soft-info ml-1 px-2 py-1 fs-12" title="{{ translate('customer_badge_confirmed_buyer') }}">{{ translate('customer_badge_confirmed_buyer') }}</span>
                                @endif
                            </td>
                            <td>
                                <label class="badge badge-soft-info">
                                    {{$customer->orders->count()}}
                                </label>
                            </td>
                            <td>
                                @if($loyaltyEnabled ?? 0)
                                    <span class="badge badge-soft-success">{{ $customer->loyaltyPoint?->points ?? 0 }}</span>
                                @else
                                    <span class="text-muted">{{ translate('Disabled') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-primary btn-sm square-btn" href="{{route('admin.customer.view',[$customer['id']])}}">
                                        <i class="tio-visible"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $customers->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($customers)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('assets/admin//svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection


