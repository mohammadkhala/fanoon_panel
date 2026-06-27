@extends('layouts.admin.app')

@section('title', translate('Add new coupon'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
<style>
.category-form-card-header { border-bottom: 2px solid var(--primary-clr, #EC2227); }
.category-form-card-header h6 { font-size: 1.15rem !important; }
.badge-category-count { font-size: 1rem !important; font-weight: 600; padding: 0.4rem 0.75rem; background-color: var(--primary-clr, #EC2227) !important; color: #fff !important; }
.category-filter-btns .category-filter-btn { flex: 1 1 0; min-width: 0; height: 42px !important; min-height: 42px !important; font-size: 1rem !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/coupon.png')}}" alt="{{ translate('coupon') }}">
                {{translate('add_new_coupon')}}
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#couponInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_coupon_add_btn') }}
            </button>
        </div>

        @include('admin-views.partials._help-instructions-modal', ['modalId' => 'couponInstructionsModal', 'titleKey' => 'help_coupon_add_title', 'pageKey' => 'help_coupon_add_page'])

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.coupon.store')}}" method="post">
                    @csrf
                    <div class="bg-light rounded p-3">
                        <div class="row g-3">
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('title')}}</label>
                                <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="{{ translate('New coupon') }}" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('coupon')}} {{translate('type')}}</label>
                                <select name="coupon_type" class="form-control coupon-type">
                                    <option value="default">{{translate('default')}}</option>
                                    <option value="first_order">{{translate('first order')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6" id="limit-for-user">
                            <div class="form-group">
                                <label class="input-label">{{translate('limit')}} {{translate('for')}} {{translate('same')}} {{translate('user')}}</label>
                                <input type="number" name="limit" id="user-limit" value="{{ old('limit') }}" class="form-control" max="100000" placeholder="{{ translate('EX: 10') }}" required min="1">
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <div class="d-flex justify-content-between">
                                    <label class="input-label">{{translate('code')}}</label>
                                    <a href="javascript:void(0)" class="float-right c1 fz-12 generate-code">{{translate('generate_code')}}</a>
                                </div>
                                <input type="text" name="code" class="form-control" maxlength="15" id="code" value="{{ old('code') }}"
                                       placeholder="{{\Illuminate\Support\Str::random(8)}}" required>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('start')}} {{translate('date')}}</label>
                                <input type="text" name="start_date" id="start_date" class="js-flatpickr form-control flatpickr-custom" placeholder="{{ translate('Select date') }}" data-hs-flatpickr-options='{ "dateFormat": "Y/m/d", "minDate": "today" }'>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('expire')}} {{translate('date')}}</label>
                                <input type="text" name="expire_date" id="expire_date" class="js-flatpickr form-control flatpickr-custom" placeholder="{{ translate('Select date') }}" data-hs-flatpickr-options='{ "dateFormat": "Y/m/d", "minDate": "today" }'>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('min')}} {{translate('purchase')}}</label>
                                <input type="number" step="0.01" name="min_purchase" value="{{ old('min_purchase')}}" min="0" max="100000" class="form-control"
                                       placeholder="{{ translate('100') }}">
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('discount')}} {{translate('type')}}</label>
                                <select name="discount_type" id="discount_type" class="form-control">
                                    <option value="percent">{{translate('percent')}}</option>
                                    <option value="amount">{{translate('amount')}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('discount')}}</label>
                                <input type="number" step="0.01" min="1" max="100000" name="discount" value="{{old('discount') }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6" id="max_discount_div">
                            <div class="form-group">
                                <label class="input-label">{{translate('max')}} {{translate('discount')}}</label>
                                <input type="number" step="0.01" min="0" value="{{ old('max_discount') }}" max="100000" name="max_discount" class="form-control">
                            </div>
                        </div>

                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="reset" class="btn btn--reset min-w-120">{{translate('Reset')}}</button>
                            <button type="submit" class="btn btn-primary min-w-120">{{translate('submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-light category-form-card-header">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                            <i class="tio-folder-outlined me-2"></i>{{ translate('Coupon List ') }}
                        </h6>
                        <span class="badge badge-category-count">{{ $coupons->total() }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body p-2 bg-light">
                <form action="{{ request()->url() }}" method="GET" class="category-filter-form">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <div class="row align-items-end g-2">
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label small mb-1">{{ translate('Search by title') }}</label>
                            <input type="search" name="search" class="form-control form-control-sm"
                                   placeholder="{{ translate('Search by title') }}" value="{{ $search ?? '' }}" autocomplete="off">
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <label class="form-label small mb-1">{{ translate('status') }}</label>
                            <select class="form-control form-control-sm" name="status">
                                <option value="" {{ (($status ?? '') === '') ? 'selected' : '' }}>{{ translate('all') }}</option>
                                <option value="1" {{ ($status ?? '') === '1' ? 'selected' : '' }}>{{ translate('active') }}</option>
                                <option value="0" {{ ($status ?? '') === '0' ? 'selected' : '' }}>{{ translate('inactive') }}</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <label class="form-label small mb-1">{{ translate('sort_by') }}</label>
                            <select class="form-control form-control-sm" name="sort_by">
                                <option value="latest" {{ ($sortBy ?? 'latest') === 'latest' ? 'selected' : '' }}>{{ translate('latest') }}</option>
                                <option value="title_az" {{ ($sortBy ?? '') === 'title_az' ? 'selected' : '' }}>{{ translate('name_a_z') }}</option>
                                <option value="title_za" {{ ($sortBy ?? '') === 'title_za' ? 'selected' : '' }}>{{ translate('name_z_a') }}</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 d-flex gap-2 align-items-end category-filter-btns">
                            <button type="submit" class="btn btn-primary category-filter-btn">
                                <i class="tio-checkmark-circle-outlined me-1"></i>{{ translate('Show_Data') }}
                            </button>
                            <a href="{{ route('admin.coupon.add-new') }}" class="btn btn-soft-secondary category-filter-btn d-inline-flex align-items-center justify-content-center">{{ translate('clear') }}</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
            <div class="table-responsive datatable-custom">
                <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{translate('title')}}</th>
                        <th>{{translate('code')}}</th>
                        <th>{{translate('min')}} {{translate('purchase')}}</th>
                        <th>{{translate('max')}} {{translate('discount')}}</th>
                        <th>{{translate('discount')}}</th>
                        <th>{{translate('discount')}} {{translate('type')}}</th>
                        <th>{{translate('start')}} {{translate('date')}}</th>
                        <th>{{translate('expire')}} {{translate('date')}}</th>
                        <th>{{translate('status')}}</th>
                        <th class="text-center">{{translate('action')}}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($coupons as $key=>$coupon)
                        <tr>
                            <td>{{$coupons->firstItem()+$key}}</td>
                            <td><p class="max-w-200 min-w-150 line--limit-2" data-toggle="tooltip" data-placement="top" title="{{$coupon['title']}}">{{$coupon['title']}}</p></td>
                            <td>{{$coupon['code']}}</td>
                            <td>{{ Helpers::set_symbol($coupon['min_purchase']) }}</td>
                            <td>{{ $coupon['discount_type'] == 'percent' ? Helpers::set_symbol($coupon['max_discount']) : '-' }}</td>
                            <td>{{$coupon['discount']}}</td>
                            <td>{{translate($coupon['discount_type'])}}</td>
                            <td>{{date('d-m-Y', strtotime($coupon['start_date']))}}</td>
                            <td>{{date('d-m-Y', strtotime($coupon['expire_date']))}}</td>
                            <td>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input change-status" id="{{ $coupon['id'] }}"
                                           {{ $coupon['status'] == 1 ? 'checked' : '' }}
                                           data-route="{{ route('admin.coupon.status', [$coupon['id'], $coupon['status'] == 1 ? 0 : 1]) }}">
                                    <span class="switcher_control"></span>
                                </label>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-info square-btn"
                                        href="{{route('admin.coupon.update',[$coupon['id']])}}"><i class="tio tio-edit"></i>
                                    </a>
                                    <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                       data-id="coupon-{{$coupon['id']}}"
                                       data-message="{{translate('Want to delete this coupon ?')}}">
                                        <i class="tio tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.coupon.delete',[$coupon['id']])}}"
                                        method="post" id="coupon-{{$coupon['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {!! $coupons->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($coupons)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('Image Description') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('assets/admin/js/coupon.js') }}"></script>
@endpush
