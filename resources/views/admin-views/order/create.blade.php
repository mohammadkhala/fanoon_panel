@extends('layouts.admin.app')

@section('title', translate('add_new_order'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <i class="tio-add-circle-outlined text-primary"></i>
                {{ translate('add_new_order') }}
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-4">{{ translate('add_new_order_description') }}</p>

                <div class="d-flex flex-wrap gap-2 mb-4">
                    @if($playStoreLink !== '')
                        <a href="{{ $playStoreLink }}" target="_blank" rel="noopener" class="btn btn-outline-dark btn-sm">
                            <i class="tio-android"></i> {{ translate('add_new_order_play_store') }}
                        </a>
                    @endif
                    @if($appStoreLink !== '')
                        <a href="{{ $appStoreLink }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm">
                            <i class="tio-apple-outlined"></i> {{ translate('add_new_order_app_store') }}
                        </a>
                    @endif
                </div>

                <a href="{{ route('admin.orders.list', ['status' => 'all']) }}" class="btn btn-primary">
                    <i class="tio-chevron-left"></i> {{ translate('add_new_order_back_to_list') }}
                </a>
            </div>
        </div>
    </div>
@endsection
