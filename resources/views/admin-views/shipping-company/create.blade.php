@extends('layouts.admin.app')

@section('title', translate('add') . ' ' . translate('shipping_company'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="text-capitalize mb-0">
            <i class="tio-truck"></i> {{ translate('add') }} {{ translate('shipping_company') }}
        </h2>
    </div>

    <form action="{{ route('admin.shipping-company.store') }}" method="post">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label">{{ translate('name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required maxlength="100">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label">{{ translate('api_url') }}</label>
                            <input type="url" name="api_url" class="form-control" value="{{ old('api_url') }}" placeholder="https://...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label">{{ translate('api_key') }}</label>
                            <input type="text" name="api_key" class="form-control" value="{{ old('api_key') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label">{{ translate('api_secret') }}</label>
                            <input type="text" name="api_secret" class="form-control" value="{{ old('api_secret') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label">{{ translate('status') }}</label>
                            <select name="is_active" class="form-control">
                                <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>{{ translate('active') }}</option>
                                <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>{{ translate('inactive') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ translate('save') }}</button>
                <a href="{{ route('admin.shipping-company.index') }}" class="btn btn-secondary">{{ translate('cancel') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection
