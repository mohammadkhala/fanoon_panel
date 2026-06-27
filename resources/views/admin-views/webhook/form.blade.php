@extends('layouts.admin.app')

@section('title', isset($webhook) ? translate('update') . ' ' . translate('webhooks') : translate('add') . ' ' . translate('webhooks'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <a href="{{ route('admin.webhook.list') }}" class="btn btn-soft-secondary"><i class="tio-arrow-back"></i></a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($webhook) ? route('admin.webhook.update', $webhook) : route('admin.webhook.store') }}" method="post">
                @csrf
                @if(isset($webhook)) @method('put') @endif

                <div class="form-group">
                    <label>{{ translate('name') }}</label>
                    <input type="text" name="name" class="form-control" value="{{ $webhook->name ?? old('name') }}" placeholder="{{ translate('optional') }}">
                </div>

                <div class="form-group">
                    <label>URL <span class="text-danger">*</span></label>
                    <input type="url" name="url" class="form-control" value="{{ $webhook->url ?? old('url') }}" placeholder="https://example.com/webhook" required>
                </div>

                <div class="form-group">
                    <label>{{ translate('events') }} <span class="text-danger">*</span></label>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach($events as $key => $label)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="events[]" value="{{ $key }}" id="ev-{{ $key }}"
                                    {{ in_array($key, $webhook->events ?? old('events', [])) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="ev-{{ $key }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label>{{ translate('secret') }} ({{ translate('optional') }})</label>
                    <input type="text" name="secret" class="form-control" value="{{ $webhook->secret ?? old('secret') }}" placeholder="{{ translate('optional') }}">
                    <small class="text-muted">{{ translate('webhook_secret_hint') }}</small>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="is_active" value="1" id="is_active" {{ (isset($webhook) ? $webhook->is_active : true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">{{ translate('active') }}</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">{{ isset($webhook) ? translate('update') : translate('add') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
