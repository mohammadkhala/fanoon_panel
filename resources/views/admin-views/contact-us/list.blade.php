@extends('layouts.admin.app')

@section('title', translate('Contact Us'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
<style>
.contact-us-card-header { border-bottom: 2px solid var(--primary-clr, #EC2227); }
.contact-us-card-header h6 { font-size: 1.15rem !important; }
.badge-contact-count { font-size: 1rem !important; font-weight: 600; padding: 0.4rem 0.75rem; background-color: var(--primary-clr, #EC2227) !important; color: #fff !important; }
.contact-table thead th { font-weight: 600; background-color: #f8f9fa; }
.contact-table .message-cell { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <i class="tio-message-text-outlined"></i>
                {{ translate('Contact Us') }}
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#contactUsListInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_contact_us_list_btn') }}
            </button>
        </div>

        @include('admin-views.partials._help-instructions-modal', ['modalId' => 'contactUsListInstructionsModal', 'titleKey' => 'help_contact_us_list_title', 'pageKey' => 'help_contact_us_list_page'])

        <div class="card">
            <div class="card-header bg-light contact-us-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                    <i class="tio-list me-2"></i>{{ translate('contact_us_messages') }}
                </h6>
                <span class="badge badge-contact-count" id="contact-unread-count">{{ $unreadCount }}</span>
            </div>
            <div class="card-body p-0">
                <div class="p-3 border-bottom bg-light">
                    <form action="{{ route('admin.contact-us.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                        <label class="mb-0">{{ translate('filter') }}:</label>
                        <select name="filter" class="form-control form-control-sm w-auto">
                            <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>{{ translate('all') }}</option>
                            <option value="unread" {{ $filter === 'unread' ? 'selected' : '' }}>{{ translate('unread') }}</option>
                            <option value="read" {{ $filter === 'read' ? 'selected' : '' }}>{{ translate('read') }}</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="tio-filter-list mr-1"></i>{{ translate('Show_Data') }}</button>
                    </form>
                </div>
                <div class="table-responsive datatable-custom">
                    <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table contact-table mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col" class="w-60px">#</th>
                                <th scope="col">{{ translate('name') }}</th>
                                <th scope="col">{{ translate('email') }}</th>
                                <th scope="col">{{ translate('phone') }}</th>
                                <th scope="col">{{ translate('subject') }}</th>
                                <th scope="col" class="contact-table message-cell">{{ translate('message') }}</th>
                                <th scope="col">{{ translate('date') }}</th>
                                <th scope="col" class="text-center w-100px">{{ translate('status') }}</th>
                                <th scope="col" class="text-center w-120px">{{ translate('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($messages as $key => $msg)
                            <tr class="{{ is_null($msg->read_at) ? 'table-warning' : '' }}">
                                <td>{{ $messages->firstItem() + $key }}</td>
                                <td>{{ $msg->name }}</td>
                                <td><a href="mailto:{{ $msg->email }}" class="text-primary">{{ $msg->email }}</a></td>
                                <td>@if($msg->phone)<a href="tel:{{ $msg->phone }}">{{ $msg->phone }}</a>@else — @endif</td>
                                <td>{{ $msg->subject ?: '—' }}</td>
                                <td class="message-cell" title="{{ $msg->message }}">{{ Str::limit($msg->message, 40) }}</td>
                                <td>{{ $msg->created_at->format('Y-m-d H:i') }}</td>
                                <td class="text-center">
                                    @if($msg->read_at)
                                        <span class="badge badge-soft-success">{{ translate('read') }}</span>
                                    @else
                                        <span class="badge badge-soft-warning">{{ translate('unread') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a class="btn btn-outline-primary btn-sm square-btn" href="{{ route('admin.contact-us.show', $msg->id) }}" title="{{ translate('view') }}">
                                            <i class="tio-visible"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm square-btn form-alert" data-id="delete-form-{{ $msg->id }}" data-message="{{ translate('Are you sure delete this message') }}?" title="{{ translate('delete') }}">
                                            <i class="tio-delete"></i>
                                        </button>
                                        <form id="delete-form-{{ $msg->id }}" action="{{ route('admin.contact-us.destroy', $msg->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="tio-message-text-outlined" style="font-size: 3rem;"></i>
                                    <p class="mt-2 mb-0">{{ translate('No contact messages yet') }}</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($messages->hasPages())
                    <div class="p-3 border-top">
                        {!! $messages->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script_2')
<script>
    $(function () {
        $('.form-alert').on('click', function () {
            let id = $(this).data('id');
            let message = $(this).data('message');
            form_alert(id, message);
        });
    });
    function form_alert(id, message) {
        Swal.fire({
            title: '{{ translate("Are you sure?") }}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#EC2227',
            cancelButtonText: '{{ translate("No") }}',
            confirmButtonText: '{{ translate("Yes") }}',
            reverseButtons: true
        }).then(function (result) {
            if (result.value) {
                $('#' + id).submit();
            }
        });
    }
</script>
@endpush
