@extends('layouts.admin.app')

@section('title', translate('Add new branch'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/branch.png')}}" alt="{{ translate('branch') }}">
                {{translate('add_new_branch')}}
            </h2>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h4 class="mb-0 d-flex gap-2 align-items-center">
                    <i class="tio-user"></i>
                    {{translate('Branch_Information')}}
                </h4>
            </div>
            <div class="card-body">
                <form action="{{route('admin.branch.store')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('name')}}</label>
                                <input type="text" name="name" class="form-control" placeholder="{{ translate('New branch') }}" value="{{ old('name') }}" maxlength="255" required>
                            </div>

                            <div class="form-group">
                                <label class="input-label" for="">{{translate('address')}}</label>
                                <input type="text" name="address" class="form-control" placeholder="" value="{{ old('address') }}" required>
                            </div>
                        </div>

                        <div class="col-lg-6">

                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <label class="mb-0">{{translate('Branch_Image')}}</label>
                                    <small class="text-danger">* ( {{ translate('Ratio 1:1') }} )</small>
                                </div>
                                <div class="d-flex justify-content-center mt-4">
                                    <div class="upload-file">
                                        <input type="file" name="image" id="customFileEg1"
                                               accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                               data-maxFileSize="{{ readableUploadMaxFileSize('image') }}"
                                               class="upload-file__input">
                                        <div class="upload-file__img">
                                            <img width="150" id="viewer" src="{{asset('assets/admin/img/icons/upload_img.png')}}" alt="{{ translate('image') }}">
                                        </div>
                                    </div>
                                </div>
                                <p class="fs-14 text-muted mb-2 mt-0 text-center">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('number')}}</label>
                                <input type="number" name="number" class="form-control" value="{{ old('number') }}"
                                       maxlength="255" placeholder="{{ translate('EX : +88 05454 6446') }}"
                                       required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('email')}}</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                       maxlength="255" placeholder="{{ translate('EX : example@example.com') }}"
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-3">
                        <button type="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection

@push('script_2')
    <script src="{{ asset('assets/admin/js/image-upload.js') }}"></script>
@endpush
