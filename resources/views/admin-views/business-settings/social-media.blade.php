@extends('layouts.admin.app')

@section('title', translate('Social Media Settings'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
<style>
.social-media-card-header { border-bottom: 2px solid var(--primary-clr, #EC2227); }
.social-media-card-header h6 { font-size: 1.15rem !important; }
.badge-social-count { font-size: 1rem !important; font-weight: 600; padding: 0.4rem 0.75rem; background-color: var(--primary-clr, #EC2227) !important; color: #fff !important; }
.social-form-row .form-control { font-size: 1rem; min-height: 53px; }
.social-form-row .form-control:focus { border-color: var(--primary-clr, #EC2227); box-shadow: 0 0 0 0.2rem rgba(236, 34, 39, 0.15); }
.social-table thead th { font-weight: 600; background-color: #f8f9fa; }
.social-table .link-cell { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/social_media.png')}}" alt="{{ translate('social_media') }}">
                {{ translate('social_media') }}
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#socialMediaInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_social_media_btn') }}
            </button>
        </div>

        @include('admin-views.partials._help-instructions-modal', ['modalId' => 'socialMediaInstructionsModal', 'titleKey' => 'help_social_media_title', 'pageKey' => 'help_social_media_page'])

        <div class="card mb-4">
            <div class="card-header bg-light social-media-card-header">
                <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                    <i class="tio-add-circle me-2"></i>{{ translate('social_media_form') }}
                </h6>
            </div>
            <div class="card-body">
                <form>
                    @csrf
                    <div class="border rounded p-4 bg-light">
                        <div class="row g-3 align-items-end social-form-row">
                            <div class="col-md-5">
                                <label for="name" class="form-label fw-medium">{{ translate('name') }}</label>
                                <select class="form-control form-control-lg" name="name" id="name">
                                    <option value="">--- {{ translate('select') }} ---</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="twitter">Twitter / X</option>
                                    <option value="linkedin">LinkedIn</option>
                                    <option value="pinterest">Pinterest</option>
                                    <option value="youtube">YouTube</option>
                                    <option value="tiktok">TikTok</option>
                                    <option value="snapchat">Snapchat</option>
                                    <option value="telegram">Telegram</option>
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="threads">Threads</option>
                                    <option value="discord">Discord</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="hidden" id="id">
                                <label for="link" class="form-label fw-medium">{{ translate('social_media_link') }}</label>
                                <input type="url" name="link" class="form-control form-control-lg" id="link"
                                       placeholder="https://..." maxlength="255" dir="ltr">
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex gap-2">
                                    <button type="button" id="add" class="btn btn-primary px-4 flex-grow-1">
                                        <i class="tio-add me-1"></i>{{ translate('save') }}
                                    </button>
                                    <button type="button" id="update" class="btn btn-primary px-4 flex-grow-1 d-none">
                                        <i class="tio-save me-1"></i>{{ translate('update') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light social-media-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                    <i class="tio-list me-2"></i>{{ translate('social_media_table') }}
                </h6>
                <span class="badge badge-social-count" id="social-count">0</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover social-table mb-0">
                        <thead>
                            <tr>
                                <th scope="col" class="w-60px">#</th>
                                <th scope="col">{{ translate('name') }}</th>
                                <th scope="col">{{ translate('link') }}</th>
                                <th scope="col" class="w-100px text-center">{{ translate('status') }}</th>
                                <th scope="col" class="w-120px text-center">{{ translate('action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div id="social-empty" class="text-center py-5 text-muted d-none">
                    <i class="tio-social-media" style="font-size: 3rem;"></i>
                    <p class="mt-2 mb-0">{{ translate('No social media added yet') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        "use strict";

        fetch_social_media();

        function fetch_social_media() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.business-settings.fetch') }}",
                method: 'GET',
                success: function (data) {
                    $('#social-count').text(data.length || 0);
                    if (data.length != 0) {
                        $('#social-empty').addClass('d-none');
                        let html = '';
                        for (let count = 0; count < data.length; count++) {
                            html += '<tr>';
                            html += '<td class="align-middle">' + (count + 1) + '</td>';
                            html += '<td class="align-middle text-capitalize">' + data[count].name + '</td>';
                            var link = data[count].link || '';
                            var linkEsc = (link + '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                            html += '<td class="align-middle link-cell" title="' + linkEsc + '"><a href="' + linkEsc + '" target="_blank" rel="noopener" class="text-primary">' + linkEsc + '</a></td>';
                            html += `<td class="align-middle text-center">
                                <label class="switcher mb-0">
                                    <input type="checkbox" class="switcher_input status-toggle" data-id="${data[count].id}" ${data[count].status == 1 ? "checked" : ""}>
                                    <span class="switcher_control"></span>
                                </label>
                            </td>`;
                            html += '<td class="align-middle"><div class="d-flex gap-2 justify-content-center">'
                                + '<button type="button" class="btn btn-outline-primary btn-sm square-btn edit" data-id="' + data[count].id + '"><i class="tio-edit"></i></button>'
                                + '<button type="button" class="btn btn-outline-danger btn-sm square-btn delete" data-id="' + data[count].id + '"><i class="tio-delete"></i></button>'
                                + '</div></td></tr>';
                        }
                        $('tbody').html(html);
                    } else {
                        $('tbody').html('');
                        $('#social-empty').removeClass('d-none');
                    }
                }
            });
        }

        $('#add').on('click', function () {
            let name = $('#name').val();
            let link = $('#link').val();
            if (name == "") {
                toastr.error('{{translate('Social Name Is Requeired')}}.');
                return false;
            }
            if (link == "") {
                toastr.error('{{translate('Social Link Is Requeired')}}.');
                return false;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.business-settings.social-media-store')}}",
                method: 'POST',
                data: {
                    name: name,
                    link: link
                },
                success: function (response) {
                    if (response.error == 1) {
                        toastr.error('{{translate('Social Media Already taken')}}');
                    } else {
                        toastr.success('{{translate('Social Media inserted Successfully')}}.');
                    }
                    $('#name').val('');
                    $('#link').val('');
                    fetch_social_media();
                }
            });
        });
        $('#update').on('click', function () {
            let $btn = $(this);
            $btn.prop('disabled', true);
            let id = $('#id').val();
            let name = $('#name').val();
            let link = $('#link').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.business-settings.social-media-update') }}",
                method: 'POST',
                data: { id: id, name: name, link: link },
                success: function () {
                    $('#name').val('');
                    $('#link').val('');
                    toastr.success('{{ translate("Social info updated Successfully") }}.');
                    $('#update').addClass('d-none');
                    $('#add').removeClass('d-none');
                    fetch_social_media();
                },
                complete: function () {
                    $btn.prop('disabled', false);
                }
            });
        });

        $(document).on('click', '.delete', function () {
            let id = $(this).data('id');
            if (confirm("{{translate('Are you sure delete this social media')}}?")) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{route('admin.business-settings.social-media-delete')}}",
                    method: 'POST',
                    data: {id: id},
                    success: function (data) {
                        fetch_social_media();
                        toastr.success('{{translate('Social media deleted Successfully')}}.');
                    }
                });
            }
        });
        $(document).on('click', '.edit', function () {
            $('#update').removeClass('d-none');
            $('#add').addClass('d-none');
            let id = $(this).data('id');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.business-settings.social-media-edit') }}",
                method: 'POST',
                data: { id: id },
                success: function (data) {
                    $(window).scrollTop(0);
                    $('#id').val(data.id);
                    $('#name').val(data.name);
                    $('#link').val(data.link);
                    fetch_social_media();
                }
            });
        });

        $(document).on('change', '.status-toggle', function () {
            let id = $(this).data('id');
            let status = $(this).prop('checked') ? 1 : 0;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.business-settings.social-media-status-update') }}",
                method: 'POST',
                data: { id: id, status: status },
                success: function () {
                    toastr.success('{{ translate("Status updated successfully") }}');
                }
            });
        });
    </script>
@endpush
