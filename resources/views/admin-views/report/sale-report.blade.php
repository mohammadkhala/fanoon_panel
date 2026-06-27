@extends('layouts.admin.app')

@section('title', translate('Sale Report'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/order_report.png')}}" alt="{{ translate('sale-report') }}">
                {{translate('sale_Report')}}
            </h2>
        </div>

        <div class="card card-body mb-3">
            <div class="media gap-3 flex-column flex-sm-row align-items-sm-center">
                <div class="avatar avatar-xl avatar-4by3">
                    <img class="avatar-img" src="{{asset('assets/admin/svg/illustrations/credit-card.svg')}}"
                         alt="Image Description">
                </div>

                <div class="media-body">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                        <div class="text-capitalize">
                            <h2 class="page-header-title">
                                {{translate('sale')}}
                                {{translate('report')}}
                                {{translate('overview')}}
                            </h2>
                            <div>
                                <span>{{translate('admin')}}:</span>
                                <a href="#">{{auth('admin')->user()->f_name.' '.auth('admin')->user()->l_name}}</a>
                            </div>
                        </div>

                        <div class="d-flex">
                            <a class="btn btn-icon btn-primary rounded-circle" href="{{route('admin.dashboard')}}">
                                <i class="tio-home-outlined"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="#" id="form-data" method="GET">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $branchId }}">
                    <div class="bg-light rounded p-3">
                        <div class="row align-items-end g-3">
                            <div class="col-sm-6 col-xl-4">
                                <label class="form-label mb-1" for="from_date">{{ translate('from') }}</label>
                                <input type="date" name="start_date" id="from_date" value="{{ $startDate }}" class="form-control">
                            </div>

                            <div class="col-sm-6 col-xl-4">
                                <label class="form-label mb-1" for="to_date">{{ translate('to') }}</label>
                                <input type="date" name="end_date" id="to_date" value="{{ $endDate }}" class="form-control">
                            </div>

                            <div class="col-sm-12 col-xl-4 d-flex gap-2">
                                <a href="{{ route('admin.report.sale-report') }}" class="btn btn--reset min-w-120 flex-grow-1">{{translate('clear')}}</a>
                                <button type="submit" class="btn btn-primary min-w-120 flex-grow-1">{{translate('show')}}</button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6 pt-2">
                            <strong>{{translate('total')}} {{translate('orders')}} :
                                <span id=""> {{ count($orders) }}</span>
                            </strong><br>
                            <strong>
                                {{translate('total')}} {{translate('item')}} {{translate('qty')}} :
                                <span id="">{{ $totalQuantity }}</span>
                            </strong><br>
                            <strong>{{translate('total')}}  {{translate('amount')}} :
                                <span id="">{{ Helpers::set_symbol($totalSold) }}</span>
                            </strong>
                            </div>
                            <div class="col-6 pt-2">
                                <div class="hs-unfold mr-5 float-right">
                                    <a class="js-hs-unfold-invoker btn btn-sm btn-white"
                                       href="{{route('admin.report.export-sale-report', ['branch_id'=>Request::get('branch_id'), 'start_date'=>Request::get('start_date'), 'end_date'=>Request::get('end_date')])}}">
                                        <i class="tio-download-to mr-1"></i> {{translate('export')}}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="table">
                <div class="row">
                    <div class="col-12 pr-4 pl-4">
                            <div class="table-responsive datatable-custom">
                            <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{translate('#')}} </th>
                                <th>{{translate('order')}}</th>
                                <th>{{translate('date')}}</th>
                                <th>{{translate('qty')}}</th>
                                <th>{{translate('amount')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $key=>$row)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>
                                        <a href="{{route('admin.orders.details',['id'=>$row['order_id']])}}">{{$row['order_id']}}</a>
                                    </td>
                                    <td>{{ date('d/m/Y', strtotime($row['date'])) }}</td>
                                    <td>{{$row['quantity']}}</td>
                                    <td>{{ Helpers::set_symbol($row['price']) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                            </div>

                    </div>
                </div>
                @if(count($data)==0)
                    <div class="text-center p-4">
                        <img class="mb-3 width-7rem" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                        <p class="mb-0">{{ translate('No data to show') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script_2')

    <script>
        "use strict"

        $('#from_date,#to_date').change(function () {
            let from = $('#from_date').val();
            let to = $('#to_date').val();
            if (from != '' && to != '') {
                if (from > to) {
                    $('#from_date').val('');
                    $('#to_date').val('');
                    toastr.error('{{ translate("Invalid date range!") }}', '{{ translate("error") }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }
        });
    </script>

@endpush
