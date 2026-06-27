@extends('layouts.admin.app')

@section('title', translate('Product List'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-3 align-items-center mb-3 justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                    <img src="{{asset('assets/admin/img/icons/all_orders.png')}}"
                         alt="{{ translate('product') }}">{{translate('product_list')}}
                </h2>
                <span class="badge badge-soft-dark rounded-50 fs-14">{{$products->total()}}</span>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#productListInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_product_list_btn') }}
            </button>
        </div>

        @include('admin-views.partials._help-instructions-modal', ['modalId' => 'productListInstructionsModal', 'titleKey' => 'help_product_list_title', 'pageKey' => 'help_product_list_page'])

        <div class="card">
            <div class="p-3">
                <form action="{{ request()->url() }}" method="GET" class="filter-form mb-4" id="product-filter-form">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <div class="product-filter-form bg-light rounded p-2 mb-2">
                        {{-- صف البحث --}}
                        <div class="row g-2 mb-2">
                            <div class="col-12">
                                <input id="datatableSearch_" type="search" name="search"
                                       class="form-control"
                                       placeholder="{{ translate('Search by id, name') }}" aria-label="Search"
                                       value="{{ $search }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="row align-items-end g-2">
                            <div class="col-6 col-md-4 col-lg-3">
                                <label class="form-label small text-capitalize mb-1">{{ translate('category') }}</label>
                                <select class="form-control form-control-sm" name="category_id" id="category_id"
                                        data-subcategory-url="{{ route('admin.product.get-categories') }}">
                                    <option value="">{{ translate('all') }} {{ translate('category') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (string)($categoryId ?? '') === (string)$category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <label class="form-label small text-capitalize mb-1">{{ translate('sub_category') }}</label>
                                <select class="form-control form-control-sm" name="sub_category_id" id="sub_category_id">
                                    <option value="">{{ translate('all') }} {{ translate('sub_category') }}</option>
                                    @foreach($subCategories as $sub)
                                        <option value="{{ $sub->id }}" {{ (string)($subCategoryId ?? '') === (string)$sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <label class="form-label small text-capitalize mb-1">{{ translate('status') }}</label>
                                <select class="form-control form-control-sm" name="status" id="status">
                                    <option value="" {{ (($status ?? '') === '') ? 'selected' : '' }}>{{ translate('all') }}</option>
                                    <option value="1" {{ ($status ?? '') === '1' ? 'selected' : '' }}>{{ translate('active') }}</option>
                                    <option value="0" {{ ($status ?? '') === '0' ? 'selected' : '' }}>{{ translate('inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <label class="form-label small text-capitalize mb-1">{{ translate('stock') }}</label>
                                <select class="form-control form-control-sm" name="stock_filter">
                                    <option value="all" {{ ($stockFilter ?? 'all') === 'all' ? 'selected' : '' }}>{{ translate('all') }}</option>
                                    <option value="in_stock" {{ ($stockFilter ?? '') === 'in_stock' ? 'selected' : '' }}>{{ translate('in_stock') }}</option>
                                    <option value="low_stock" {{ ($stockFilter ?? '') === 'low_stock' ? 'selected' : '' }}>{{ translate('low_stock') }}</option>
                                    <option value="out_of_stock" {{ ($stockFilter ?? '') === 'out_of_stock' ? 'selected' : '' }}>{{ translate('out_of_stock') }}</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <label class="form-label small text-capitalize mb-1">{{ translate('price') }} {{ translate('min') }}</label>
                                <input type="number" class="form-control form-control-sm" name="price_min" value="{{ $priceMin ?? '' }}" placeholder="0" min="0" step="0.01">
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <label class="form-label small text-capitalize mb-1">{{ translate('price') }} {{ translate('max') }}</label>
                                <input type="number" class="form-control form-control-sm" name="price_max" value="{{ $priceMax ?? '' }}" placeholder="" min="0" step="0.01">
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <label class="form-label small text-capitalize mb-1">{{ translate('rating') }}</label>
                                <select class="form-control form-control-sm" name="rating_min">
                                    <option value="">{{ translate('all') }}</option>
                                    <option value="4" {{ (string)($ratingMin ?? '') === '4' ? 'selected' : '' }}>4+ ★</option>
                                    <option value="3" {{ (string)($ratingMin ?? '') === '3' ? 'selected' : '' }}>3+ ★</option>
                                    <option value="2" {{ (string)($ratingMin ?? '') === '2' ? 'selected' : '' }}>2+ ★</option>
                                    <option value="1" {{ (string)($ratingMin ?? '') === '1' ? 'selected' : '' }}>1+ ★</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <label class="form-label small text-capitalize mb-1">{{ translate('product_tags') }}</label>
                                <select class="form-control form-control-sm" name="tag_id">
                                    <option value="">{{ translate('all') }} {{ translate('product_tags') }}</option>
                                    @foreach($tags ?? [] as $tag)
                                        <option value="{{ $tag->id }}" {{ (string)($tagId ?? '') === (string)$tag->id ? 'selected' : '' }}>{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <label class="form-label small text-capitalize mb-1">{{ translate('attributes') }}</label>
                                <select class="form-control form-control-sm" name="attribute_id">
                                    <option value="">{{ translate('all') }} {{ translate('attributes') }}</option>
                                    @foreach($attributes ?? [] as $attr)
                                        <option value="{{ $attr->id }}" {{ (string)($attributeId ?? '') === (string)$attr->id ? 'selected' : '' }}>{{ $attr->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <label class="form-label small text-capitalize mb-1">{{ translate('sort_by') }}</label>
                                <select class="form-control form-control-sm" name="sort_by">
                                    <option value="latest" {{ ($sortBy ?? 'latest') === 'latest' ? 'selected' : '' }}>{{ translate('latest') }}</option>
                                    <option value="price_low" {{ ($sortBy ?? '') === 'price_low' ? 'selected' : '' }}>{{ translate('price_low_to_high') }}</option>
                                    <option value="price_high" {{ ($sortBy ?? '') === 'price_high' ? 'selected' : '' }}>{{ translate('price_high_to_low') }}</option>
                                    <option value="name_az" {{ ($sortBy ?? '') === 'name_az' ? 'selected' : '' }}>{{ translate('name_a_z') }}</option>
                                    <option value="name_za" {{ ($sortBy ?? '') === 'name_za' ? 'selected' : '' }}>{{ translate('name_z_a') }}</option>
                                    <option value="stock_low" {{ ($sortBy ?? '') === 'stock_low' ? 'selected' : '' }}>{{ translate('stock_low_first') }}</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3 d-flex gap-2 align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="tio-checkmark-circle-outlined me-1"></i>{{ translate('Show_Data') }}
                                </button>
                                <a href="{{ route('admin.product.list') }}" class="btn btn-soft-secondary btn-sm flex-grow-1 text-center">{{ translate('clear') }}</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="d-flex flex-wrap justify-content-end align-items-center border-top pt-3">
                    <a href="{{ route('admin.product.add-new') }}" class="btn btn-primary gap-1 d-flex font-weight-bold align-items-center min-h-35 py-1 fs-12 cmn-border">
                        <i class="tio-add-circle"></i>
                        {{ translate('add_new_product') }}
                    </a>
                </div>
            </div>


            <div class="table-responsive datatable-custom">
                <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{ translate('product_name') }}</th>
                        <th>{{ translate('product_tags') }}</th>
                        <th>{{ translate('status') }}</th>
                        <th>{{ translate('stock') }}</th>
                        <th class="text-center">{{ translate('action') }}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($products as $key=>$product)
                        @php
                            $defaultAlert = (int) ($defaultStockAlert ?? 5);
                            $isLowStock = ($product['minimum_stock_alert'] !== null && $product['total_stock'] <= $product['minimum_stock_alert'])
                                || ($product['minimum_stock_alert'] === null && $product['total_stock'] <= $defaultAlert && $product['total_stock'] >= 0);
                        @endphp
                        <tr class="{{ $isLowStock ? 'table-row-low-stock' : '' }}">
                            <td>{{$products->firstitem()+$key}}</td>
                            <td class="product-list-name-cell">
                                <div class="media gap-3 align-items-center">
                                    <div class="avatar rounded border">
                                        <img
                                            src="{{$product['image_fullpath'][0]}}"
                                            class="img-fit rounded"
                                            alt="{{ translate('product') }}"
                                            loading="lazy">
                                    </div>
                                    <a href="{{route('admin.product.view',[$product['id']])}}"
                                       class="media-body text-dark text-break">
                                        {{ $product['name'] }}
                                    </a>
                                </div>
                            </td>
                            <td>
                                @if(isset($product->tags) && $product->tags->isNotEmpty())
                                    @foreach($product->tags as $t)
                                        <span class="badge badge-soft-info me-1">{{ $t->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($product['status']==1)
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input change-status" checked
                                               id="{{$product['id']}}"
                                               data-route="{{route('admin.product.status',[$product['id'],0])}}">
                                        <span class="switcher_control"></span>
                                    </label>
                                @else
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input change-status"
                                               id="{{$product['id']}}"
                                               data-route="{{route('admin.product.status',[$product['id'],1])}}">
                                        <span class="switcher_control"></span>
                                    </label>
                                @endif
                            </td>
                            <td>
                                @php
                                    $variantsDecoded = json_decode($product->variations ?? '[]', true);
                                    $hasVariants = is_array($variantsDecoded) && count($variantsDecoded) > 0;
                                @endphp
                                @if(!$hasVariants)
                                    <div class="quick-stock-wrap d-flex flex-wrap align-items-center gap-1"
                                         data-product-id="{{ $product['id'] }}"
                                         data-url="{{ route('admin.product.quick-stock', $product['id']) }}">
                                        <input type="number" min="0" max="100000000" step="1"
                                               class="form-control form-control-sm quick-stock-input"
                                               style="width: 5.5rem; max-width: 100%;"
                                               value="{{ (int) $product['total_stock'] }}"
                                               aria-label="{{ translate('stock') }}">
                                        <button type="button" class="btn btn-sm btn-primary quick-stock-save px-2"
                                                title="{{ translate('save') }}">
                                            <i class="tio-checkmark"></i>
                                        </button>
                                    </div>
                                    @if($isLowStock)
                                        <small class="text-danger fw-medium d-block mt-1">{{ translate('stock_near_end') }}</small>
                                    @endif
                                @elseif($isLowStock)
                                    <div class="d-flex flex-column gap-1">
                                        <label class="badge badge-low-stock-alert fs-14">{{$product['total_stock']}}</label>
                                        <small class="text-danger fw-medium">{{ translate('stock_near_end') }}</small>
                                    </div>
                                @else
                                    <label class="badge badge-soft-info fs-14">{{$product['total_stock']}}</label>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    <a class="btn btn-outline-primary square-btn"
                                       href="{{route('admin.product.edit',[$product['id']])}}"
                                       title="{{ translate('edit') }}">
                                        <i class="tio tio-edit"></i>
                                    </a>
                                    <a class="btn btn-outline-danger square-btn form-alert"
                                       href="javascript:"
                                       data-id="product-{{$product['id']}}"
                                       data-message="{{translate('Want to delete this product ?')}}"
                                       title="{{ translate('delete') }}">
                                        <i class="tio tio-delete"></i>
                                    </a>
                                    <a class="btn btn-outline-success square-btn"
                                       href="{{ route('admin.design-template.add-new') }}?product_id={{ $product['id'] }}"
                                       title="إضافة قالب تصميم">
                                        <i class="tio tio-add"></i>
                                    </a>
                                    <a class="btn btn-outline-info square-btn"
                                       href="{{ route('admin.design-template.by-category') }}?product_id={{ $product['id'] }}"
                                       title="عرض قوالب المنتج">
                                        <i class="tio tio-layers"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.product.delete',[$product['id']])}}"
                                      method="post" id="product-{{$product['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $products->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($products)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem"
                         src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}"
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

        (function () {
            const categorySelect = document.getElementById('category_id');
            const subCategorySelect = document.getElementById('sub_category_id');

            if (!categorySelect || !subCategorySelect) {
                return;
            }

            const url = categorySelect.dataset.subcategoryUrl;
            const defaultSubCategoryText = @json(translate('all') . ' ' . translate('sub_category'));
            const selectedSubCategoryId = @json((string) ($subCategoryId ?? ''));
            let initialized = false;

            const resetSubCategoryOptions = function () {
                subCategorySelect.innerHTML = '';
                const option = document.createElement('option');
                option.value = '';
                option.textContent = defaultSubCategoryText;
                subCategorySelect.appendChild(option);
            };

            const applySubCategoryHtml = function (optionsHtml) {
                const parser = new DOMParser();
                const doc = parser.parseFromString('<select>' + optionsHtml + '</select>', 'text/html');
                const parsedOptions = doc.querySelectorAll('option');

                resetSubCategoryOptions();
                parsedOptions.forEach(function (opt) {
                    if (String(opt.value) === '0') {
                        return;
                    }
                    subCategorySelect.appendChild(opt.cloneNode(true));
                });
            };

            const loadSubCategories = function (parentId) {
                if (!url || !parentId) {
                    resetSubCategoryOptions();
                    return;
                }

                fetch(url + '?parent_id=' + encodeURIComponent(parentId), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        applySubCategoryHtml(data.options ?? '');

                        if (!initialized && selectedSubCategoryId !== '') {
                            subCategorySelect.value = selectedSubCategoryId;
                        }
                        initialized = true;
                    })
                    .catch(function () {
                        resetSubCategoryOptions();
                        initialized = true;
                    });
            };

            categorySelect.addEventListener('change', function () {
                subCategorySelect.value = '';
                loadSubCategories(categorySelect.value);
            });

            if (categorySelect.value) {
                loadSubCategories(categorySelect.value);
            } else {
                resetSubCategoryOptions();
            }
        })();

        const quickStockMessages = {
            invalid: @json(translate('quick_stock_invalid_value')),
            saved: @json(translate('quick_stock_saved')),
            failed: @json(translate('quick_stock_request_failed')),
        };
        document.querySelectorAll('.quick-stock-save').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const wrap = btn.closest('.quick-stock-wrap');
                if (!wrap) {
                    return;
                }
                const url = wrap.getAttribute('data-url');
                const input = wrap.querySelector('.quick-stock-input');
                if (!url || !input) {
                    return;
                }
                const totalStock = parseInt(String(input.value).trim(), 10);
                if (isNaN(totalStock) || totalStock < 0) {
                    toastr.error(quickStockMessages.invalid, {CloseButton: true, ProgressBar: true});
                    return;
                }
                btn.disabled = true;
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({total_stock: totalStock})
                })
                    .then(function (r) {
                        return r.json().then(function (data) {
                            return {ok: r.ok, status: r.status, data: data};
                        });
                    })
                    .then(function (res) {
                        if (res.ok && res.data && res.data.success) {
                            toastr.success(res.data.message || quickStockMessages.saved, {CloseButton: true, ProgressBar: true});
                            input.value = res.data.total_stock != null ? res.data.total_stock : totalStock;
                            const row = wrap.closest('tr');
                            if (row) {
                                row.classList.remove('table-row-low-stock');
                            }
                        } else {
                            const msg = (res.data && res.data.message) ? res.data.message : quickStockMessages.failed;
                            toastr.error(msg, {CloseButton: true, ProgressBar: true});
                        }
                    })
                    .catch(function () {
                        toastr.error(quickStockMessages.failed, {CloseButton: true, ProgressBar: true});
                    })
                    .finally(function () {
                        btn.disabled = false;
                    });
            });
        });
    </script>
@endpush

