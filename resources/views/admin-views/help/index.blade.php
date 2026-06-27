@extends('layouts.admin.app')

@section('title', translate('user_guide'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <i class="tio-book-outlined"></i>
                {{ translate('user_guide') }}
            </h2>
        </div>
        <div class="card">
            <div class="card-body">
                <p class="mb-0">{{ translate('user_guide') }}</p>
            </div>
        </div>
    </div>
@endsection
