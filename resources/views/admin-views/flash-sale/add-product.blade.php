@extends('layouts.admin.app')

@section('title', translate('Flash sale'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
<style>
.flash-sale-card-header { border-bottom: 2px solid var(--primary-clr, #EC2227); }
.flash-sale-card-header h6 { font-size: 1.15rem !important; }
.badge-flash-count { font-size: 1rem !important; font-weight: 600; padding: 0.4rem 0.75rem; background-color: var(--primary-clr, #EC2227) !important; color: #fff !important; }
.flash-sale-card .input-label { font-size: 1.05rem !important; }
.product-search-result-wrap .form-control { font-size: 1rem; min-height: 44px; }
.selected-product-item { transition: all 0.2s; padding: 24px !important; min-height: 140px; min-width: 340px; width: 100%; }
.selected-product-item:hover { background-color: #f8f9fa !important; }
.selected-product-item .selected-product-img { width: 90px !important; height: 90px !important; object-fit: contain; flex-shrink: 0; }
.selected-product-item h6 { font-size: 1.2rem !important; margin-bottom: 12px !important; line-height: 1.4; word-break: break-word; }
.selected-product-item .media-body { min-width: 0; flex: 1; }
.selected-product-item .media-body .info-row { display: flex; flex-direction: column; gap: 8px; font-size: 1.05rem !important; }
.selected-product-item .media-body .info-row span { white-space: nowrap; }
.selected-products { gap: 2rem !important; }
.remove-item-btn { position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px; color: #dc3545; background: #fff; border: 1px solid #dee2e6; font-size: 1.1rem; }
.remove-item-btn:hover { background: #dc3545; color: #fff; }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('assets/admin/img/icons/flash-sale.png') }}" alt="{{ translate('flash-sale') }}">
                {{ translate('Flash sale Setup') }}
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#flashSaleAddProductInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_flash_sale_add_product_btn') }}
            </button>
        </div>

        @include('admin-views.partials._help-instructions-modal', ['modalId' => 'flashSaleAddProductInstructionsModal', 'titleKey' => 'help_flash_sale_add_product_title', 'pageKey' => 'help_flash_sale_add_product_page'])

        {{-- بطاقة إضافة المنتجات --}}
        <div class="card mb-4">
            <div class="card-header bg-light flash-sale-card-header">
                <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                    <i class="tio-add-circle me-2"></i>{{ translate('Add Product') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-4 flash-sale-card product-search-result-wrap">
                    <label class="input-label mb-2">{{ translate('Product') }}</label>
                    <input type="text" name="product_search" id="product-search" class="form-control form-control-lg"
                           placeholder="{{ translate('search product') }}">

                    <div class="product-search-result bg-white shadow-soft rounded mt-2 px-3 border">
                        @foreach($products as $product)
                            <div class="border-bottom py-3 result">
                                <a class="media gap-3 d-flex align-items-center text-decoration-none text-body" href="{{ route('admin.flash-sale.add-product-to-session', [$flash_sale_id, $product['id']]) }}">
                                    <img class="selected-product-img rounded border p-1" width="55"
                                         src="{{ $product['image_fullpath'][0] ?? asset('assets/admin/img/400x400/img2.jpg') }}" alt="{{ translate('image') }}">
                                    <div class="media-body flex-grow-1">
                                        <h6 class="mb-1 fw-semibold">{{ $product->name }}</h6>
                                        <div class="d-flex flex-wrap gap-3 fs-14">
                                            <span>{{ translate('price') }}: {{ Helpers::set_symbol($product->price) }}</span>
                                            <span>{{ translate('total_stock') }}: {{ $product->total_stock }}</span>
                                        </div>
                                    </div>
                                    <i class="tio-add-circle text-primary fs-20"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                @php $selected_products = session()->get('selected_products', []); @endphp
                @if(count(array_filter($selected_products, fn($p) => $p['flash_sale_id'] == $flash_sale_id)) > 0)
                <div class="mb-4">
                    <label class="input-label mb-2">{{ translate('Selected Products') }}</label>
                    <div class="row g-0 selected-products">
                        @foreach($selected_products as $selected_product)
                            @if($selected_product['flash_sale_id'] == $flash_sale_id)
                                <div class="col-12 col-lg-6 col-xl-4">
                                    <div class="bg-light rounded selected-product-item p-3 position-relative h-100 d-flex">
                                        <a class="remove-item-btn" href="{{ route('admin.flash-sale.delete-product-from-session', [$flash_sale_id, $selected_product['product_id']]) }}" title="{{ translate('remove') }}">
                                            <i class="tio-clear"></i>
                                        </a>
                                        <div class="media gap-3 d-flex align-items-center flex-grow-1">
                                            <img class="selected-product-img rounded border p-1 flex-shrink-0"
                                                 src="{{ $selected_product['image'] }}" alt="{{ translate('image') }}">
                                            <div class="media-body">
                                                <h6 class="mb-2 fw-semibold">{{ $selected_product['name'] }}</h6>
                                                <div class="info-row">
                                                    <span>{{ translate('price') }}: <strong>{{ Helpers::set_symbol($selected_product['price']) }}</strong></span>
                                                    <span>{{ translate('Current Stock') }}: <strong>{{ $selected_product['total_stock'] }}</strong></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="d-flex flex-wrap justify-content-end gap-2 pt-2">
                    <a class="btn btn-secondary px-4 min-w-120" href="{{ route('admin.flash-sale.delete-all-products-from-session', [$flash_sale_id]) }}">{{ translate('reset') }}</a>
                    <a class="btn btn-primary px-4 min-w-120" id="flash-sale-product-store" href="javascript:">{{ translate('add') }}</a>
                    <form action="{{ route('admin.flash-sale.add_flash_sale_product', [$flash_sale_id]) }}" method="post" id="product_store" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>

        {{-- قائمة المنتجات في العرض --}}
        <div class="card">
            <div class="card-header bg-light flash-sale-card-header">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                            <i class="tio-folder-outlined me-2"></i>{{ translate('Flash Sale Product List ') }}
                        </h6>
                        <span class="badge badge-flash-count">{{ $flashSaleProducts->total() }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body p-2 bg-light">
                <form action="{{ request()->url() }}" method="GET">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <div class="row align-items-end g-2">
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label small mb-1">{{ translate('Search by name') }}</label>
                            <input type="search" name="search" class="form-control form-control-sm"
                                   placeholder="{{ translate('Search by name') }}" value="{{ $search ?? '' }}" autocomplete="off">
                        </div>
                        <div class="col-6 col-md-4 col-lg-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="tio-search me-1"></i>{{ translate('Show_Data') }}
                            </button>
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
                                <th>{{ translate('Name') }}</th>
                                <th>{{ translate('Price') }}</th>
                                <th class="text-center">{{ translate('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($flashSaleProducts as $key => $product)
                                <tr>
                                    <td>{{ $flashSaleProducts->firstItem() + $key }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ Helpers::set_symbol($product->price) }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                               data-id="flash-product-delete-{{ $product->id }}"
                                               data-message="{{ translate('Want to delete this product ?') }}">
                                                <i class="tio tio-delete"></i>
                                            </a>
                                        </div>
                                        <form action="{{ route('admin.flash-sale.product.delete', [$flash_sale_id, $product->id]) }}" method="post" id="flash-product-delete-{{ $product->id }}">
                                            @csrf
                                            @method('delete')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3">
                    {!! $flashSaleProducts->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
                </div>
                @if(count($flashSaleProducts) == 0)
                    <div class="text-center p-4">
                        <img class="mb-3 width-7rem" src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}" alt="{{ translate('Image Description') }}">
                        <p class="mb-0">{{ translate('No data to show') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{ asset('assets/admin/js/flash-sale.js') }}"></script>
@endpush
