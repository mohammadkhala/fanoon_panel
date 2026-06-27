@extends('layouts.admin.app')

@section('title', translate('Update banner'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/banner.png')}}" alt="{{ translate('banner') }}">
                {{translate('update_banner')}}
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#bannerEditInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_banner_edit_btn') }}
            </button>
        </div>

        @include('admin-views.partials._help-instructions-modal', ['modalId' => 'bannerEditInstructionsModal', 'titleKey' => 'help_banner_edit_title', 'pageKey' => 'help_banner_edit_page'])

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.banner.update',[$banner['id']])}}" method="post" enctype="multipart/form-data">
                    @csrf @method('put')

                    <div class="row">
                        <div class="col-md-6">
                            <input type="hidden" name="banner_type" value="primary">
                            <div class="mb-5">
                                <label class="input-label">{{translate('title')}}</label>
                                <input type="text" name="title" value="{{$banner['title']}}" class="form-control"
                                       placeholder="{{ translate('New banner') }}" required>
                            </div>
                            <div class="mb-5">
                                <label class="input-label d-flex align-items-center gap-1">
                                    {{ translate('banner_placement') ?: 'موضع البانر في المتجر' }}
                                    <i class="tio-info-outlined text-muted fs-14" data-toggle="tooltip" data-placement="top"
                                       title="حدد أين يظهر هذا البانر في الموقع"></i>
                                </label>
                                <select name="placement" class="form-control form-control-lg">
                                    <option value="main" {{ ($banner['placement'] ?? 'main') === 'main' ? 'selected' : '' }}>{{ translate('main_banner') ?: 'البانر الرئيسي (السلايدر)' }}</option>
                                    <option value="hero_grid" {{ ($banner['placement'] ?? '') === 'hero_grid' ? 'selected' : '' }}>{{ translate('hero_grid_banner') ?: 'شبكة الواجهة (3 بطاقات أسفل العنوان)' }}</option>
                                </select>
                            </div>
                            <div class="mb-5">
                                <label class="input-label">{{translate('Redirection')}} {{translate('type')}}<span
                                        class="input-label-secondary">*</span></label>
                                <select name="item_type" class="form-control form-control-lg" id="redirection_type">
                                    <option value="product" {{ $banner['product_id'] != null ? 'selected' : '' }}>{{ translate('product') }}</option>
                                    <option value="category" {{ $banner['product_id'] == null ? 'selected' : '' }}>{{ translate('category') }}</option>
                                </select>
                            </div>

                            <div class="mb-5 type-product {{$banner['product_id'] == null ? 'd--none':'d-block'}}" id="type-product">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('product')}}
                                    <span class="input-label-secondary">*</span>
                                </label>
                                <select name="product_id" class="form-control js-select2-custom">
                                    @foreach($products as $product)
                                        <option
                                            value="{{$product['id']}}" {{$banner['product_id']==$product['id']?'selected':''}}>
                                            {{$product['name']}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-5 type-category {{$banner['category_id']==null?'d--none':'d-block'}}" id="type-category">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('category')}}
                                    <span class="input-label-secondary">*</span>
                                </label>
                                <select name="category_id" class="form-control js-select2-custom">
                                    @foreach($categories as $category)
                                        <option value="{{$category['id']}}" {{$banner['category_id']==$category['id']?'selected':''}}>{{$category['name']}}</option>
                                    @endforeach
                                </select>
                            </div>


                        </div>
                        <div class="col-md-6">
                            <div class="form-group" id="primary_banner">
                                <label class="mb-2">{{translate('Image')}}</label>
                                <div class="custom_upload_input max-h200px ratio-2">
                                    <input type="file" name="primary_image" class="custom-upload-input-file meta-img" id="" data-imgpreview="pre_meta_image_viewer"
                                           accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                           data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">

                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        <img id="pre_meta_image_viewer" class="aspect-1 bg-white" src="img" onerror="this.classList.add('d-none')" alt="{{ translate('img') }}">
                                    </div>
                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center existing-image-div">
                                        <div class="d-flex flex-column justify-content-center align-items-center overflow-hidden">
                                            <img
                                                  src="{{Helpers::onErrorImage(
                                                            $banner['image'],
                                                            asset('storage/banner').'/' . $banner['image'],
                                                            asset('assets/admin/img/ratio/2_1.png') ,
                                                            'banner/')}}"
                                                  class="w-100" alt="{{ translate('banner') }}">
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
                    <div class="d-flex justify-content-end gap-3">
                        <button type="reset" class="btn btn-secondary px-5">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn-primary px-5">{{translate('update')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('assets/admin/js/image-upload.js') }}"></script>
    <script src="{{ asset('assets/admin/js/banner.js') }}"></script>
@endpush
