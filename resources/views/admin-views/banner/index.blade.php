@extends('layouts.admin.app')

@section('title', translate('Add new banner'))

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
                <img width="24" src="{{asset('assets/admin/img/icons/banner.png')}}" alt="{{ translate('banner') }}">
                {{ translate('لوحة الإعلانات') }}
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#bannerInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_banner_add_btn') }}
            </button>
        </div>

        @include('admin-views.partials._help-instructions-modal', ['modalId' => 'bannerInstructionsModal', 'titleKey' => 'help_banner_add_title', 'pageKey' => 'help_banner_add_page'])

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.banner.store')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="banner_type" value="primary">
                    <div class="bg-light rounded p-3">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="input-label">{{translate('title')}}</label>
                                    <input type="text" name="title" class="form-control" placeholder="{{ translate('New banner') }}" required maxlength="255">
                                </div>
                                <div class="mb-3">
                                    <label class="input-label d-flex align-items-center gap-1">
                                        {{ translate('banner_placement') ?: 'موضع البانر في المتجر' }}
                                        <i class="tio-info-outlined text-muted fs-14" data-toggle="tooltip" data-placement="top"
                                           title="حدد أين يظهر هذا البانر في الموقع"></i>
                                    </label>
                                    <select name="placement" class="form-control form-control-lg">
                                        <option value="main">{{ translate('main_banner') ?: 'البانر الرئيسي (السلايدر)' }}</option>
                                        <option value="hero_grid">{{ translate('hero_grid_banner') ?: 'شبكة الواجهة (3 بطاقات أسفل العنوان)' }}</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="input-label">{{translate('Redirection')}} {{translate('type')}}<span
                                            class="input-label-secondary text-danger">*</span></label>
                                    <select name="item_type" class="form-control form-control-lg" id="redirection_type">
                                        <option value="product">{{ translate('product') }}</option>
                                        <option value="category">{{ translate('category') }}</option>
                                    </select>
                                </div>
                                <div class="mb-3 type-product" id="type-product">
                                    <label class="input-label">{{translate('product')}}
                                        <span class="input-label-secondary text-danger">*</span>
                                    </label>
                                    <select name="product_id" class="form-control js-select2-custom">
                                        @foreach($products as $product)
                                            <option value="{{$product['id']}}">{{$product['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-0 d--none type-category" id="type-category">
                                    <label class="input-label">
                                        {{translate('category')}}
                                        <span class="input-label-secondary text-danger">*</span>
                                    </label>
                                    <select name="category_id" class="form-control js-select2-custom">
                                        @foreach($categories as $category)
                                            <option value="{{$category['id']}}">{{$category['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group mb-0" id="primary_banner">
                                    <label class="mb-2">{{translate('Image')}}</label>
                                    <div class="custom_upload_input max-h200px ratio-2">
                                        <input type="file" name="primary_image" class="custom-upload-input-file meta-img" id="" data-imgpreview="pre_meta_image_viewer"
                                               accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                               data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">

                                        <span class="delete_file_input btn btn-outline-danger btn-sm square-btn" style="display: none">
                                                    <i class="tio-delete"></i>
                                                </span>

                                        <div class="img_area_with_preview position-absolute z-index-2">
                                            <img id="pre_meta_image_viewer" class="aspect-1 bg-white" src="img" onerror="this.classList.add('d-none')" alt="{{ translate('image') }}">
                                        </div>
                                        <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                            <div class="d-flex flex-column justify-content-center align-items-center overflow-hidden">
                                                <h3 class="text-muted">{{ translate('Drag & Drop here') }}</h3>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="fs-16 mb-2 text-dark mt-2">
                                        <i class="tio-info-outlined cursor-pointer" data-toggle="tooltip"
                                           title="{{ translate('When do not have secondary banner than the primary banner ration will be 3:1') }}">
                                        </i>
                                        {{ translate('Images Ratio') }} 2:1
                                    </p>
                                    <p class="fs-14 text-muted mb-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="reset" class="btn btn--reset min-w-120">{{translate('reset')}}</button>
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
                            <i class="tio-folder-outlined me-2"></i>{{ translate('لوحة الإعلانات') }}
                        </h6>
                        <span class="badge badge-category-count">{{ $banners->total() }}</span>
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
                            <a href="{{ route('admin.banner.add-new') }}" class="btn btn-soft-secondary category-filter-btn d-inline-flex align-items-center justify-content-center">{{ translate('clear') }}</a>
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
                            <th>{{translate('banner_image')}}</th>
                            <th>{{translate('title')}}</th>
                            <th>{{ translate('banner_placement') ?: 'الموضع' }}</th>
                            <th>{{translate('status')}}</th>
                            <th class="text-center">{{translate('action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($banners as $key=>$banner)
                        <tr>
                            <td>{{$banners->firstitem()+$key}}</td>
                            <td>
                                <div class="banner-img-wrap rounded border">
                                    <img class="img-fit" src="{{$banner['image_fullpath']}}"
                                         alt="{{ translate('banner') }}">
                                </div>
                            </td>
                            <td>{{$banner['title']}}</td>
                            <td>
                                @if(($banner['placement'] ?? 'main') === 'hero_grid')
                                    <span class="badge badge-soft-info">شبكة الواجهة</span>
                                @else
                                    <span class="badge badge-soft-secondary">البانر الرئيسي</span>
                                @endif
                            </td>
                            <td>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input change-status"
                                           {{ $banner['status'] == 1 ? 'checked' : '' }}
                                           data-route="{{ route('admin.banner.status', [$banner['id'], $banner['status'] == 1 ? 0 : 1]) }}">
                                    <span class="switcher_control"></span>
                                </label>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-info square-btn"
                                        href="{{route('admin.banner.edit',[$banner['id']])}}"><i class="tio tio-edit"></i></a>
                                    <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                       data-id="banner-{{$banner['id']}}"
                                       data-message="{{translate('Want to delete this banner ?')}}">
                                        <i class="tio tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.banner.delete',[$banner['id']])}}"
                                        method="post" id="banner-{{$banner['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {!! $banners->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($banners)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('assets/admin//svg/illustrations/sorry.svg')}}" alt="{{ translate('Image Description') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('assets/admin/js/image-upload.js') }}"></script>
    <script src="{{ asset('assets/admin/js/banner.js') }}"></script>

@endpush
