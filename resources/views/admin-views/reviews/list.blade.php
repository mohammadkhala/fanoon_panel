@extends('layouts.admin.app')

@section('title', translate('Review List'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/product-review.png')}}"
                     alt="{{ translate('product-review') }}">
                {{translate('review_list')}}
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#reviewsListInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_reviews_list_btn') }}
            </button>
        </div>

        @include('admin-views.partials._help-instructions-modal', ['modalId' => 'reviewsListInstructionsModal', 'titleKey' => 'help_reviews_list_title', 'pageKey' => 'help_reviews_list_page'])

        <div class="card">

            <div class="p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="m-0">{{translate('Review List ')}}</h6>
                        <span class="badge badge-soft-dark rounded-50 fz-10">{{$reviews->total()}}</span>
                    </div>
                </div>
            </div>
            <div class="px-3 pb-3">
                <form action="{{ request()->url() }}" method="GET" id="review-filter-form">
                    <div class="bg-light rounded p-3">
                        <div class="row align-items-end g-3">
                            <div class="col-sm-12 col-md-8 col-xl-9">
                                <label class="form-label mb-1" for="datatableSearch_">{{ translate('search') }}</label>
                                <input id="datatableSearch_" type="search" name="search"
                                       class="form-control"
                                       placeholder="{{translate('Search by product name')}}"
                                       aria-label="Search"
                                       value="{{$search}}"
                                       autocomplete="off">
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-3">
                                <label class="form-label mb-1" for="rating">{{ translate('rating') }}</label>
                                <select class="form-control" name="rating" id="rating">
                                    <option value="">{{ translate('all') }}</option>
                                    @for($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}" {{ (string)$rating === (string)$i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="row align-items-end g-3 mt-1">
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label mb-1" for="start_date">{{ translate('from') }}</label>
                                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-control">
                            </div>
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label mb-1" for="end_date">{{ translate('to') }}</label>
                                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-control">
                            </div>
                            <div class="col-sm-12 col-md-4 col-xl-6 d-flex gap-2">
                                <a href="{{ route('admin.reviews.list') }}" class="btn btn--reset min-w-120 flex-grow-1 order-2">{{ translate('clear') }}</a>
                                <button type="submit" class="btn btn-primary min-w-120 flex-grow-1 order-1">{{ translate('show') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive datatable-custom">
                <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{translate('product')}}</th>
                        <th>{{translate('customer')}}</th>
                        <th>{{translate('review')}}</th>
                        <th class="text-center">{{translate('rating')}}</th>
                        <th class="text-center">{{translate('action')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($reviews as $key=>$review)
                        <tr>
                            <td>{{$reviews->firstitem()+$key}}</td>
                            <td>
                                @if($review->product)
                                    <a class="text-dark" href="{{route('admin.product.view',[$review['product_id']])}}">
                                        {{ $review->product['name'] }}
                                    </a>
                                @else
                                    <span class="text-muted">
                                                    {{translate('Product unavailable')}}
                                                </span>
                                @endif
                            </td>
                            <td>
                                @if(isset($review->customer))
                                    <a class="text-dark" href="{{route('admin.customer.view',[$review->user_id])}}">
                                        {{$review->customer->f_name." ".$review->customer->l_name}}
                                    </a>
                                @else
                                    <span class="text-muted">
                                                    {{translate('customer_unavailable')}}
                                                </span>
                                @endif
                            </td>
                            <td>
                                <div class="mx-w300 mn-w200 text-wrap pragraph-description" data-limit="120">
                                    <p class="mb-0">
                                        {{$review->comment}}
                                    </p>
                                    <a href="#0" class="text-primary d-inline-block cursor-pointer font-semibold text-underline see-more">see_more</a>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <label
                                        class="badge badge-soft-info d-flex gap-1 align-items-center justify-content-center">
                                        {{$review->rating}} <i class="tio-star"></i>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <a class="btn btn-outline-danger square-btn form-alert"
                                       href="javascript:"
                                       data-id="review-{{$review['id']}}"
                                       data-message="{{translate('Want to delete this review ?')}}">
                                        <i class="tio tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.reviews.delete',[$review['id']])}}"
                                      method="post" id="review-{{$review['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $reviews->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($reviews)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('assets/admin//svg/illustrations/sorry.svg')}}"
                         alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>

    </div>

@endsection

@push('script_2')
    <script>
        "use strict";

        $('#start_date,#end_date').change(function () {
            let from = $('#start_date').val();
            let to = $('#end_date').val();

            if (from !== '' && to !== '' && from > to) {
                $('#start_date').val('');
                $('#end_date').val('');
                toastr.error("{{ translate('Invalid date range!') }}");
            }
        });

        $(document).ready(function () {
            $('.pragraph-description').each(function () {
                var $container = $(this);
                var limit = parseInt($container.data('limit')) || 350;
                var $desc = $container.find('p');
                var fullText = $desc.text().trim();

                if (fullText.length > limit) {
                    var shortText = fullText.substring(0, limit) + '...';
                    $desc.data('full-text', fullText).text(shortText);
                    $container.find('.see-more').show().text('See More');
                } else {
                    $container.find('.see-more').remove();
                }
            });

            $(document).on('click', '.see-more', function (e) {
                e.preventDefault();

                var $link = $(this);
                var $container = $link.closest('.pragraph-description');
                var $desc = $container.find('p');
                var fullText = $desc.data('full-text');
                var limit = parseInt($container.data('limit')) || 350;

                if ($link.text().trim().toLowerCase() === 'see more') {
                    $desc.text(fullText);
                    $link.text('See Less');
                } else {
                    $desc.text(fullText.substring(0, limit) + '...');
                    $link.text('See More');
                }
            });
        });
    </script>
@endpush

