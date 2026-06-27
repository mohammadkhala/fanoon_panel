@extends('layouts.admin.app')

@section('title', translate('Order List'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-3 align-items-center mb-3 justify-content-between" dir="ltr">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#ordersListInstructionsModal">
                    <i class="tio-book-outlined"></i> {{ translate('help_orders_list_btn') }}
                </button>
            </div>
            <div class="d-flex align-items-center gap-3">
                <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                    <img src="{{asset('assets/admin/img/icons/all_orders.png')}}"
                         alt="{{ translate('orders') }}">{{translate('all_orders')}}
                </h2>
                <span class="badge badge-soft-dark rounded-50 fs-14">{{$orders->total()}}</span>
            </div>
        </div>

        @include('admin-views.partials._help-instructions-modal', ['modalId' => 'ordersListInstructionsModal', 'titleKey' => 'help_orders_list_title', 'pageKey' => 'help_orders_list_page'])

        <div class="card order-list-card">
            <div class="p-3">
                <form action="{{ request()->url() }}" id="form-data" class="filter-form mb-4" method="GET">
                    <div class="row align-items-end g-3 bg-light rounded p-3 mb-2">
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label small text-capitalize mb-1">
                                {{ translate('Start_Date') }}
                                <i class="tio-info-outlined text-muted fs-14 ms-1" data-toggle="tooltip" data-placement="top" title="{{ translate('help_order_filter_dates') }}"></i>
                            </label>
                            <input type="date" id="start_date" name="start_date" value="{{ $startDate }}"
                                   class="form-control form-control-sm"
                                   placeholder="yyyy-mm-dd">
                        </div>
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label small text-capitalize mb-1">{{ translate('End_date') }}</label>
                            <input type="date" id="end_date" name="end_date" value="{{ $endDate }}"
                                   class="form-control form-control-sm"
                                   placeholder="yyyy-mm-dd">
                        </div>
                        <div class="col-12 col-lg-4 d-flex gap-2 align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                <i class="tio-checkmark-circle-outlined mr-1"></i>{{ translate('Show_Data') }}
                            </button>
                            <a href="{{ route('admin.orders.list', [$status]) }}" class="btn btn-soft-secondary btn-sm flex-grow-1 text-center">
                                {{ translate('clear') }}
                            </a>
                        </div>
                    </div>
                </form>

                <div class="card-section-header d-flex flex-wrap justify-content-between align-items-center gy-2 border-top pt-3">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h5 class="card-section-title mb-0 d-flex align-items-center gap-2">
                            <i class="tio-shopping-cart-outlined text-primary"></i>
                            <span>{{ translate('Order List') }}</span>
                            <span class="badge badge-soft-primary rounded-pill px-3 py-1">{{ $orders->total() }}</span>
                            <i class="tio-info-outlined text-muted fs-16" data-toggle="tooltip" data-placement="top" title="{{ translate('help_order_statuses_title') }}"></i>
                        </h5>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <form action="{{ request()->url() }}" method="GET">
                            @foreach (request()->except('search','page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <div class="input-group min-h-35">
                                <input id="datatableSearch_" type="search" name="search"
                                       class="form-control py-1 h-35 fs-12"
                                       placeholder="{{ translate('Search by order ID') }}" aria-label="Search"
                                       value="{{ $search }}" autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary px-2 py-1 min-h-35">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" id="btn-print-selected" class="btn btn-outline-secondary gap-1 d-flex font-weight-bold align-items-center min-h-35 py-1 fs-12 cmn-border"
                                    title="{{ translate('bulk_print_invoices') }}" disabled>
                                <i class="tio-print"></i>{{ translate('print_selected') }}
                            </button>
                            <div class="dropdown">
                                <button type="button"
                                        class="btn btn-outline-primary gap-1 d-flex font-weight-bold align-items-center min-h-35 py-1 fs-12 cmn-border"
                                        data-toggle="dropdown" aria-expanded="false">
                                    <i class="tio-download-to mt-1"></i>{{ translate('Export') }}<i
                                        class="tio-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right w-auto">
                                <li>
                                    <a type="submit" class="dropdown-item d-flex align-items-center gap-2"
                                       href="{{ route('admin.orders.export', [$status]) }}?{{ http_build_query(request()->only(['start_date', 'end_date', 'search'])) }}">
                                        <img width="14" src="{{asset('assets/admin/img/icons/excel.png')}}"
                                             alt="{{ translate('excel') }}">
                                        {{ translate('excel') }}
                                    </a>
                                </li>
                                </ul>
                            </div>
                            <i class="tio-info-outlined text-muted fs-16" data-toggle="tooltip" data-placement="top" title="{{ translate('help_order_export') }}"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive datatable-custom">
                <table
                    class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                    <tr>
                        <th class="text-center" style="width: 40px;">
                            <input type="checkbox" id="select-all-orders" class="form-check-input" title="{{ translate('select_all') ?: 'تحديد الكل' }}">
                        </th>
                        <th>#</th>
                        <th>{{ translate('order_ID') }}</th>
                        <th>{{ translate('order_date') }}</th>
                        <th>{{ translate('customer_info') }}</th>
                        <th>{{ translate('total_amount') }}</th>
                        <th>
                            {{ translate('order_status') }}
                            <i class="tio-info-outlined text-muted fs-14 ms-1" data-toggle="tooltip" data-placement="top" title="{{ translate('help_order_statuses_title') }}"></i>
                        </th>
                        <th>{{ translate('order_type') }}</th>
                        <th class="text-center">{{ translate('actions') }}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($orders as $key=>$order)

                        <tr class="status-{{$order['order_status']}} class-all">
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input order-checkbox" value="{{ $order['id'] }}" data-invoice-url="{{ route('admin.orders.generate-invoice', [$order['id']]) }}">
                            </td>
                            <td>
                                {{$orders->firstitem()+$key}}
                            </td>
                            <td>
                                <a class="text-dark"
                                   href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                            </td>
                            <td>
                                <div>{{date('d M Y',strtotime($order['created_at']))}}</div>
                                <div class="fs-12">{{date("h:i A",strtotime($order['created_at']))}}</div>
                            </td>
                            <td>
                                @if($order->is_guest == 0)
                                    @if($order->customer)
                                        <a class="text-dark text-capitalize"
                                           href="{{route('admin.customer.view',[$order['user_id']])}}">
                                            <h6 class="mb-0">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</h6>
                                        </a>
                                        <a class="text-dark fs-12"
                                           href="tel:{{ $order->customer['phone'] }}">{{ $order->customer['phone'] }}</a>
                                    @else
                                        <h6 class="text-muted text-capitalize">{{translate('customer')}} {{translate('deleted')}}</h6>
                                    @endif
                                @else
                                    <h6 class="text-success">{{translate('Guest Customer')}}</h6>
                                @endif

                            </td>
                            <td>
                                <div class="text-dark">{{ Helpers::set_symbol($order['order_amount']) }}</div>
                                @if($order->payment_status=='paid')
                                    <span class="text-success">
                                        {{translate('paid')}}
                                    </span>
                                @else
                                    <span class="text-danger">
                                        {{translate('unpaid')}}
                                    </span>
                                @endif
                            </td>
                            <td class="text-capitalize">
                                @if($order['order_status']=='pending')
                                    <span class="badge badge-soft-info">{{translate('pending')}}</span>
                                @elseif($order['order_status']=='confirmed')
                                    <span class="badge badge-soft-info">{{translate('confirmed')}}</span>
                                @elseif($order['order_status']=='processing')
                                    <span class="badge badge-soft-warning">{{translate('processing')}}</span>
                                @elseif($order['order_status']=='out_for_delivery')
                                    <span class="badge badge-soft-warning">{{translate('out_for_delivery')}}</span>
                                @elseif($order['order_status']=='delivered')
                                    <span class="badge badge-soft-success">{{translate('delivered')}}</span>
                                @else
                                    <span
                                        class="badge badge-soft-danger">{{ translate($order['order_status']) }}</span>
                                @endif
                            </td>
                            <td class="text-capitalize">
                                @if($order['order_type']=='self_pickup')
                                    <span class="badge badge-soft-primary">{{translate('self_pickup')}}</span>
                                @else
                                    <span class="badge badge-soft-success">{{translate($order['order_type'])}}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a class="btn btn-outline-primary square-btn"
                                       href="{{route('admin.orders.details',['id'=>$order['id']])}}">
                                        <i class="tio-visible"></i>
                                    </a>
                                    <a class="btn btn-outline-info square-btn" target="_blank"
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

@endsection

@push('script_2')
    <script type="text/javascript" src="{{asset('assets/admin/js/filter-form-validation.js')}}"></script>
    <script>
        (function() {
            var selectAll = document.getElementById('select-all-orders');
            var checkboxes = document.querySelectorAll('.order-checkbox');
            var btnPrint = document.getElementById('btn-print-selected');

            function updatePrintButton() {
                var checked = document.querySelectorAll('.order-checkbox:checked');
                btnPrint.disabled = checked.length === 0;
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(function(cb) { cb.checked = selectAll.checked; });
                    updatePrintButton();
                });
            }

            checkboxes.forEach(function(cb) {
                cb.addEventListener('change', updatePrintButton);
            });

            if (btnPrint) {
                btnPrint.addEventListener('click', function() {
                    if (btnPrint.disabled) return;
                    var checked = document.querySelectorAll('.order-checkbox:checked');
                    checked.forEach(function(cb) {
                        window.open(cb.dataset.invoiceUrl, '_blank', 'noopener');
                    });
                });
            }
        })();
    </script>
@endpush
