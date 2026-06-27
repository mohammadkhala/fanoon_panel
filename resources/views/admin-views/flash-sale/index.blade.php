@extends('layouts.admin.app')

@section('title', translate('Flash sale'))

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
                <img width="16" src="{{asset('assets/admin/img/icons/flash-sale.png')}}" alt="{{ translate('flash-sale') }}">
                {{translate('Flash sale')}}
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#flashSaleInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_flash_sale_btn') }}
            </button>
        </div>

        @include('admin-views.partials._help-instructions-modal', ['modalId' => 'flashSaleInstructionsModal', 'titleKey' => 'help_flash_sale_title', 'pageKey' => 'help_flash_sale_page'])

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.flash-sale.store')}}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row align-items-end g-3 bg-light rounded p-3 mb-2">
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="mb-4">
                                <label class="input-label">{{translate('Title')}}</label>
                                <input type="text" name="title" class="form-control" placeholder="{{ translate('Ex : LUX') }}" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="mb-4">
                                <label class="input-label">{{ translate('Start Date')}}</label>
                                <input type="datetime-local" name="start_date" id="start_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="mb-4">
                                <label class="input-label">{{translate('End Date')}}</label>
                                <input type="datetime-local" name="end_date" id="end_date" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-end gap-2">
                        <button type="reset" class="btn btn-secondary px-4 min-w-120">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn-primary px-4 min-w-120">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-light category-form-card-header">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                            <i class="tio-folder-outlined me-2"></i>{{ translate('Flash Sale List ') }}
                        </h6>
                        <span class="badge badge-category-count">{{ $flashSales->total() }}</span>
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
                            <a href="{{ route('admin.flash-sale.index') }}" class="btn btn-soft-secondary category-filter-btn d-inline-flex align-items-center justify-content-center">{{ translate('clear') }}</a>
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
                            <th>{{translate('Title')}}</th>
                            <th>{{translate('Duration')}}</th>
                            <th>{{translate('status')}}</th>
                            <th>{{translate('Active Products')}}</th>
                            <th>{{translate('Publish')}}</th>
                            <th class="text-center">{{translate('action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($flashSales as $key => $flash_sale)
                        <tr>
                            <td> {{$flashSales->firstitem()+$key}}</td>
                            <td>
                                <span class="d-block font-size-sm text-body text-trim-25">
                                    {{$flash_sale['title']}}
                                </span>
                            </td>
                            <td>{{date('Y-m-d H:i A',strtotime($flash_sale['start_date']))}} - {{date('Y-m-d H:i A',strtotime($flash_sale['end_date']))}}</td>
                            <td>
                                @if(\Carbon\Carbon::parse($flash_sale['end_date'])->endOfDay()->isPast())
                                    <span class="badge badge-soft-danger">{{ translate('expired')}} </span>
                                @else
                                    <span class="badge badge-soft-success"> {{ translate('active')}} </span>
                                @endif
                            </td>
                            <td>{{  $flash_sale->products_count }}</td>
                            <td>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input route-alert"
                                           data-route="{{ route('admin.flash-sale.status', [$flash_sale->id, $flash_sale->status ? 0 : 1]) }}"
                                           data-message="{{ $flash_sale->status? translate('you_want_to_disable_this'): translate('you_want_to_active_this') }}"
                                        {{ $flash_sale->status ? 'checked' : '' }} >
                                    <span class="switcher_control"></span>
                                </label>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-soft-primary btn-sm py-1" href="{{route('admin.flash-sale.add-product', [$flash_sale['id']])}}">
                                        <i class="tio tio-add"></i> {{ translate('Add Product') }}
                                    </a>
                                    <a class="btn btn-outline-info square-btn" href="{{route('admin.flash-sale.edit',[$flash_sale['id']])}}">
                                        <i class="tio tio-edit"></i>
                                    </a>
                                    <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                       data-id="deal-{{$flash_sale['id']}}"
                                       data-message="{{ translate("Want to delete this") }}">
                                        <i class="tio tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.flash-sale.delete',[$flash_sale['id']])}}"
                                      method="post" id="deal-{{$flash_sale['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {!! $flashSales->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($flashSales)==0)
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
    <script src="{{ asset('assets/admin/js/image-upload.js') }}"></script>

    <script>
        "use strict"

        $('#start_date, #end_date').change(function () {
            let from = $('#start_date').val();
            let to = $('#end_date').val();
            if (from != '' && to != '') {
                if (from > to) {
                    $('#start_date').val('');
                    $('#end_date').val('');
                    toastr.error('{{ translate("Invalid date range!") }}', '{{ translate("error") }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }
        });

    </script>

@endpush
