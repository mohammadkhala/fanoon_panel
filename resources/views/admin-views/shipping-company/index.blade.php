@extends('layouts.admin.app')

@section('title', translate('shipping_companies'))

@section('content')
<div class="content container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h2 class="text-capitalize mb-0">
            <i class="tio-truck"></i> {{ translate('shipping_companies') }}
        </h2>
        <a href="{{ route('admin.shipping-company.create') }}" class="btn btn-primary">
            <i class="tio-add"></i> {{ translate('add') }}
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>{{ translate('name') }}</th>
                            <th>{{ translate('status') }}</th>
                            <th class="text-center">{{ translate('action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $key => $company)
                            <tr>
                                <td>{{ $companies->firstItem() + $key }}</td>
                                <td>{{ $company->name }}</td>
                                <td>
                                    <span class="badge badge-soft-{{ $company->is_active ? 'success' : 'secondary' }}">
                                        {{ $company->is_active ? translate('active') : translate('inactive') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.shipping-company.edit', $company->id) }}" class="btn btn-sm btn-soft-primary">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.shipping-company.destroy', $company->id) }}" method="post" class="d-inline" onsubmit="return confirm('{{ translate('Are you sure') }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger"><i class="tio-delete"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">{{ translate('no_data_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                {!! $companies->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
