@extends('layouts.admin.app')

@section('title', translate('webhooks'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
            <i class="tio-link"></i>
            {{ translate('webhooks') }}
        </h2>
        <a href="{{ route('admin.webhook.add') }}" class="btn btn-primary">
            <i class="tio-add"></i> {{ translate('add') }}
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{ translate('name') }}</th>
                        <th>URL</th>
                        <th>{{ translate('events') }}</th>
                        <th>{{ translate('status') }}</th>
                        <th>{{ translate('action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($endpoints as $key => $ep)
                    <tr>
                        <td>{{ $endpoints->firstItem() + $key }}</td>
                        <td>{{ $ep->name ?: '—' }}</td>
                        <td><code class="small">{{ Str::limit($ep->url, 50) }}</code></td>
                        <td>
                            @foreach($ep->events ?? [] as $e)
                                <span class="badge badge-soft-info me-1">{{ $e }}</span>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('admin.webhook.status', $ep) }}" class="badge badge-soft-{{ $ep->is_active ? 'success' : 'secondary' }}">
                                {{ $ep->is_active ? translate('active') : translate('inactive') }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('admin.webhook.edit', $ep) }}" class="btn btn-outline-primary btn-sm"><i class="tio-edit"></i></a>
                            <a href="javascript:" class="btn btn-outline-danger btn-sm form-alert"
                               data-id="webhook-delete-{{ $ep->id }}"
                               data-message="{{ translate('Want to delete this webhook?') }}">
                                <i class="tio-delete"></i>
                            </a>
                            <form id="webhook-delete-{{ $ep->id }}" action="{{ route('admin.webhook.delete', $ep) }}" method="post" class="d-none">
                                @csrf @method('delete')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">{{ translate('No data to show') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $endpoints->links() }}
        </div>
    </div>
</div>
@endsection
