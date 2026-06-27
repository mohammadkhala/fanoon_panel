@extends('layouts.admin.app')

@section('title', isset($formAction) ? translate('store_information') : translate('Update Branch'))

@push('css_or_js')
@if($formAction ?? null)
@include('admin-views.partials._help-instructions-css')
@endif
@endpush

@section('content')
    <div class="content container-fluid">
        @if($formAction ?? null)
        <div class="mb-4">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/business-setup.png')}}" alt="{{ translate('business_setup_image') }}">
                {{translate('business_Setup')}}
            </h2>
        </div>
        <div class="inline-page-menu my-4">
            @include('admin-views.business-settings.partial.business-setup-nav')
        </div>
        @else
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/branch.png')}}" alt="">
                {{ translate('update_branch') }}
            </h2>
        </div>
        @endif


        @php($branch_count=\App\Models\Branch::count())
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0 d-flex gap-2 align-items-center">
                    <i class="tio-user"></i>
                    {{ ($formAction ?? null) ? translate('store_details') : translate('Branch_Information') }}
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ $formAction ?? route('admin.branch.update', [$branch['id']]) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('name')}}</label>
                                <input type="text" name="name" value="{{$branch['name']}}" class="form-control" placeholder="{{ translate('New branch') }}" required>
                            </div>

                            <div class="form-group">
                                <label class="input-label" for="">{{translate('address')}}</label>
                                <input type="text" name="address" value="{{$branch['address']}}" class="form-control" placeholder="" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <label class="mb-0">{{ ($formAction ?? null) ? translate('store_image') : translate('Branch_Image') }}</label>
                                    <small class="text-danger">* ( {{ translate('Ratio 1:1') }} )</small>
                                </div>
                                <div class="d-flex justify-content-center mt-4">
                                    <div class="upload-file">
                                        <input type="file" id="customFileEg1" name="image"
                                               accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                               data-maxFileSize="{{ readableUploadMaxFileSize('image') }}"
                                               class="upload-file__input">
                                        <div class="upload-file__img">
                                            <img width="150"
                                                 src="{{$branch['image_fullpath']}}" id="viewer" alt="{{ translate('branch') }}">
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
                                <input type="number" name="number" class="form-control" value="{{ $branch->phone }}"
                                       maxlength="255" placeholder="{{ translate('EX : +88 05454 6446') }}"
                                       required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('email')}}</label>
                                <input type="email" name="email" value="{{$branch['email']}}" class="form-control"
                                       placeholder="{{ translate('EX : example@example.com') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-3">
                        <button type="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn-primary">{{translate('update')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        "use strict";

        function readURL(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });

    </script>

@endpush
