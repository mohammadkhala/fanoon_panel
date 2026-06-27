@extends('layouts.admin.app')

@section('title', translate('Contact message'))

@push('css_or_js')
<style>
.contact-us-card-header { border-bottom: 2px solid var(--primary-clr, #EC2227); }
.contact-us-card-header h6 { font-size: 1.15rem !important; }
.contact-us-detail-row { border-bottom: 1px solid #e7eaf3; padding: 0.75rem 0; }
.contact-us-detail-row:last-of-type { border-bottom: none; }
.contact-us-detail-label { font-size: 1rem; color: #6984a0; font-weight: 600; margin-bottom: 0.35rem; }
.contact-us-detail-value { font-size: 1rem; }
.contact-us-message-box { border: 1px solid #e7eaf3; border-radius: 0.5rem; padding: 1rem 1.25rem; background-color: #f8fafc; margin-top: 0.5rem; font-size: 1rem; line-height: 1.6; white-space: pre-wrap; word-break: break-word; }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <a href="{{ route('admin.contact-us.index') }}" class="btn btn-soft-secondary btn-sm">
                <i class="tio-arrow-backward"></i> {{ translate('back') }}
            </a>
        </div>

        <div class="card">
            <div class="card-header bg-light contact-us-card-header">
                <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                    <i class="tio-message-text me-2"></i>{{ translate('contact_message_detail') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 contact-us-detail-row">
                        <div class="contact-us-detail-label">{{ translate('name') }}</div>
                        <div class="contact-us-detail-value fw-medium">{{ $message->name }}</div>
                    </div>
                    <div class="col-md-6 contact-us-detail-row">
                        <div class="contact-us-detail-label">{{ translate('email') }}</div>
                        <div class="contact-us-detail-value"><a href="mailto:{{ $message->email }}" class="text-primary">{{ $message->email }}</a></div>
                    </div>
                    <div class="col-md-6 contact-us-detail-row">
                        <div class="contact-us-detail-label">{{ translate('phone') }}</div>
                        <div class="contact-us-detail-value">@if($message->phone)<a href="tel:{{ $message->phone }}">{{ $message->phone }}</a>@else — @endif</div>
                    </div>
                    <div class="col-md-6 contact-us-detail-row">
                        <div class="contact-us-detail-label">{{ translate('subject') }}</div>
                        <div class="contact-us-detail-value fw-medium">{{ $message->subject ?: '—' }}</div>
                    </div>
                    <div class="col-md-6 contact-us-detail-row">
                        <div class="contact-us-detail-label">{{ translate('sent_at') }}</div>
                        <div class="contact-us-detail-value">{{ $message->created_at->format('Y-m-d H:i:s') }}</div>
                    </div>
                    @if($message->read_at)
                    <div class="col-md-6 contact-us-detail-row">
                        <div class="contact-us-detail-label">{{ translate('read_at') }}</div>
                        <div class="contact-us-detail-value">{{ $message->read_at->format('Y-m-d H:i:s') }}</div>
                    </div>
                    @endif
                </div>

                <div class="contact-us-detail-row mt-3 pt-3 border-top">
                    <div class="contact-us-detail-label">{{ translate('message') }}</div>
                    <div class="contact-us-message-box">{{ $message->message }}</div>
                </div>

                <hr class="my-4">
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.contact-us.index') }}" class="btn btn-soft-secondary">{{ translate('back_to_list') }}</a>
                    <form action="{{ route('admin.contact-us.destroy', $message->id) }}" method="POST" class="d-inline" id="delete-form-show">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger form-alert" data-id="delete-form-show" data-message="{{ translate('Are you sure delete this message') }}?">
                            <i class="tio-delete"></i> {{ translate('delete') }}
                        </button>
                    </form>
                </div>
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
