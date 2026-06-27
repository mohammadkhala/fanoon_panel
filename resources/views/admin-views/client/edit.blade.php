@extends('layouts.admin.app')

@section('title', translate('update_client') ?: 'تعديل عميل')

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <i class="tio-edit fs-22"></i>
                {{ translate('update_client') ?: 'تعديل عميل' }}
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.client.update',[$client->id])}}" method="post" enctype="multipart/form-data">
                    @csrf @method('put')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="input-label">{{ translate('name') ?: 'اسم العميل / الشركة' }}
                                    <span class="input-label-secondary text-danger">*</span></label>
                                <input type="text" name="name" value="{{ $client->name }}" class="form-control" required maxlength="255">
                            </div>
                            <div class="mb-4">
                                <label class="input-label">{{ translate('link') ?: 'رابط الموقع (اختياري)' }}</label>
                                <input type="url" name="link" value="{{ $client->link }}" class="form-control" placeholder="https://example.com">
                            </div>
                            <div class="mb-0">
                                <label class="input-label">{{ translate('position') ?: 'ترتيب الظهور' }}</label>
                                <input type="number" name="position" value="{{ $client->position }}" class="form-control" min="0">
                                <small class="text-muted">{{ translate('position_hint') ?: 'الأصغر يظهر أولاً' }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" id="primary_banner">
                                <label class="mb-2">{{ translate('logo') ?: 'شعار الشركة' }}</label>
                                <div class="custom_upload_input max-h200px ratio-1">
                                    <input type="file" name="logo" class="custom-upload-input-file meta-img" id="" data-imgpreview="pre_client_logo_viewer"
                                           accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                           data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">

                                    <div class="img_area_with_preview position-absolute z-index-2">
                                        <img id="pre_client_logo_viewer" class="aspect-1 bg-white" src="img" onerror="this.classList.add('d-none')" alt="{{ translate('logo') }}">
                                    </div>
                                    <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center existing-image-div">
                                        <div class="d-flex flex-column justify-content-center align-items-center overflow-hidden">
                                            @if($client->logo_fullpath)
                                                <img src="{{ $client->logo_fullpath }}" class="w-100" alt="{{ translate('logo') }}">
                                            @else
                                                <i class="tio-image text-muted fs-48"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <p class="fs-14 text-muted mb-0 mt-2">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
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
@endpush
