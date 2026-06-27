@extends('layouts.admin.app')

@section('title', translate('Customer Details'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-print-none pb-2">
            <div class="mb-3">
                <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                    <img width="20" src="{{asset('assets/admin/img/icons/customer.png')}}" alt="{{ translate('customer') }}">
                    {{translate('Customer Details')}}
                </h2>
            </div>

            <div class="border-top"></div>

            <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center py-3">
                <div>
                    <h3 class="page-header-title d-flex align-items-center gap-2 flex-wrap">
                        {{translate('customer_ID')}} #{{$customer['id']}}
                        @php($orderCount = \App\Models\Order::where('user_id', $customer['id'])->count())
                        @if($orderCount >= 10)
                            <span class="badge badge-soft-success px-2 py-1 fs-12">{{ translate('customer_badge_trusted') }}</span>
                        @elseif($orderCount >= 5)
                            <span class="badge badge-soft-info px-2 py-1 fs-12">{{ translate('customer_badge_confirmed_buyer') }}</span>
                        @endif
                    </h3>
                    <div class="fs-12">
                        <i class="tio-date-range"></i>
                        {{translate('joined_at')}} : {{date('d M Y H:i:s',strtotime($customer['created_at']))}}
                    </div>
                </div>

                <div class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#customerViewInstructionsModal">
                        <i class="tio-book-outlined"></i> {{ translate('help_customer_view_btn') }}
                    </button>
                    <a href="{{route('admin.dashboard')}}" class="btn btn-primary">
                        <i class="tio-home-outlined"></i> {{translate('dashboard')}}
                    </a>
                </div>
            </div>

            @include('admin-views.partials._help-instructions-modal', ['modalId' => 'customerViewInstructionsModal', 'titleKey' => 'help_customer_view_title', 'pageKey' => 'help_customer_view_page'])
        </div>

        <div class="row" id="printableArea">
            <div class="col-lg-8">
                <div class="card mb-3 mb-lg-0">
                    <div class="px-20 py-3">
                        <div class="row gy-2 align-items-center">
                            <div class="col-sm-4">
                                <h5 class="text-capitalize d-flex align-items-center gap-2 mb-0">
                                    {{translate('customer_table')}}
                                    <span class="badge badge-soft-dark rounded-50 fz-12">{{ $orders->total() }}</span>
                                </h5>
                            </div>
                            <div class="col-sm-8">
                                <div class="d-flex flex-wrap justify-content-sm-end gap-2">
                                    <form action="{{url()->current()}}" method="GET">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control"
                                                placeholder="{{translate('Search by Order ID')}}" aria-label="Search"
                                                value="{{$search}}" required autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary">{{translate('search')}}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{translate('order_ID')}}</th>
                                    <th>{{translate('total')}}</th>
                                    <th class="text-center">{{translate('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                            @foreach($orders as $key=>$order)
                                <tr>
                                    <td>{{$orders->firstitem()+$key}}</td>
                                    <td>
                                        <a class="text-dark" href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                    </td>
                                    <td>{{ Helpers::set_symbol($order['order_amount']) }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-info square-btn"
                                                href="{{route('admin.orders.details',['id'=>$order['id']])}}">
                                                <i class="tio-visible"></i>
                                            </a>
                                            <a class="btn btn-outline-primary square-btn" target="_blank"
                                                href="{{route('admin.orders.generate-invoice',[$order['id']])}}">
                                                <i class="tio-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="">
                        {!! $orders->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
                    </div>
                    @if(count($orders)==0)
                        <div class="text-center p-4">
                            <img class="mb-3 width-7rem" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}"
                                 alt="{{ translate('image') }}">
                            <p class="mb-0">{{ translate('No data to show') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                @if($customer)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4 class="mb-0 d-flex align-items-center gap-2">
                                <i class="tio-refresh"></i> {{ translate('Change type') }}
                            </h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.customer.update-type', $customer->id) }}" method="post">
                                @csrf
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <label class="mb-0">{{ translate('Change type') }}:</label>
                                    <select name="user_type_id" class="form-control form-control-sm" style="max-width:220px">
                                        @foreach($userTypes as $ut)
                                            <option value="{{ $ut->id }}" {{ $customer->user_type_id == $ut->id ? 'selected' : '' }}>{{ $ut->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <h4 class="mb-0 d-flex align-items-center gap-2">
                                <i class="tio tio-user"></i> {{translate('customer_information')}}
                            </h4>
                            <span class="badge badge-danger px-3 py-2">
                                {{ translate('User Type') }}: {{ $customer->userType?->name ?? translate('Default') }}
                            </span>
                        </div>
                    </div>
                    @if($customer)
                        <div class="card-body">
                            <div class="text-center">
                                <div class="avatar avatar-xxl rounded-circle mx-auto border">
                                    <img src="{{$customer['image_fullpath']}}"
                                         class="img-fit rounded-circle"
                                         alt="{{ translate('customer') }}">
                                </div>
                                <h5 class="mt-3 mb-1 text-dark">{{$customer['f_name'].' '.$customer['l_name']}}</h5>
                                <p class="text-muted mb-3">
                                    <strong>{{$customer->orders->count()}}</strong> {{translate('orders')}}
                                </p>
                            </div>

                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex align-items-center justify-content-between mb-2 text-dark">
                                    <span class="font-weight-semibold">{{ translate('phone') }}</span>
                                    <a class="text-dark" href="tel:{{$customer['phone']}}"><strong>{{$customer['phone']}}</strong></a>
                                </div>
                                <div class="text-dark">
                                    <span class="font-weight-semibold d-block">{{ translate('email') }}</span>
                                    <a class="text-dark d-block mt-1" href="mailto:{{$customer['email']}}">{{$customer['email']}}</a>
                                </div>
                            </div>

                            @if($customer->requestedUserType)
                                <div class="mt-3 text-center">
                                    <span class="badge badge-soft-warning">{{ translate('Requested') }}: {{ $customer->requestedUserType->name }}</span>
                                    <form action="{{ route('admin.customer.approve-type', $customer->id) }}" method="post" class="d-inline-block ml-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-soft-success">{{ translate('Approve type') }}</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                @php($googleMapStatus = 0)

                <div class="card mt-3">
                    <div class="card-header">
                        <h4 class="mb-0 d-flex align-items-center gap-2"><i class="tio tio-user"></i> {{translate('addresses')}}</h4>
                    </div>

                    @if($customer)
                        <div class="card-body">
                            @forelse($customer->addresses as $key=>$address)
                                <ul class="list-unstyled list-unstyled-py-2 text-dark">
                                    <li>
                                        <i class="tio-tab mr-2"></i>
                                        {{$address['address_type']}}
                                    </li>
                                    <li>
                                        <i class="tio-android-phone-vs mr-2"></i>
                                        {{$address['contact_person_number']}}
                                    </li>
                                    <li>
                                        @if($googleMapStatus && $address['latitude'] && $address['longitude'])
                                            <a class="text-dark" target="_blank" href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$address['latitude']}}+{{$address['longitude']}}">
                                                <i class="tio-map mr-2"></i>
                                                {{$address['address']}}
                                            </a>
                                        @else
                                            <p>{{$address['address']}}</p>
                                        @endif
                                    </li>
                                </ul>
                                @if($key+1 < count($customer->addresses))
                                    <hr>
                                @endif
                            @empty
                                <div class="text-center">
                                    <p>{{ translate('No data to show') }}</p>
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
