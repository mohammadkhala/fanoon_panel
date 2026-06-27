@extends('layouts.admin.app')

@section('title', translate('Product Bulk Import'))

@push('css_or_js')
<style>
    .help-instructions-modal-header { background: #0d9488; color: #fff; border-bottom: none; padding: 1rem 1.25rem; }
    .help-instructions-modal-header .modal-title { order: 1; color: #fff; font-weight: 600; font-size: 1.15rem; }
    .help-instructions-modal-header .d-flex.align-items-center { order: 2; margin-inline-start: auto; }
    .help-instructions-modal-header .help-whatsapp-icon { color: #fff; display: flex; align-items: center; justify-content: center; width: 38px; height: 38px; padding: 0; border-radius: 6px; background: rgba(255,255,255,0.15); border: 2px solid #fff; transition: all 0.2s; }
    .help-instructions-modal-header .help-whatsapp-icon:hover { color: #fff; background: rgba(37,211,102,0.9); border-color: #25D366; }
    .help-instructions-modal-header .close { color: #fff !important; opacity: 1; font-size: 1.5rem; line-height: 1; padding: 0; margin: 0; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 6px; background: rgba(255,255,255,0.25); border: none; }
    .help-instructions-modal-header .close:hover { color: #fff !important; background: rgba(255,255,255,0.4); }
    .help-instructions-modal-header .close span { font-size: 1.5rem; line-height: 1; }
    .help-instructions-body { line-height: 1.8; }
    .help-step { margin-bottom: 1.25rem; }
    .help-step:last-child { margin-bottom: 0; }
    .help-step-title { font-weight: 600; color: #0d9488; font-size: 1rem; margin-bottom: 0.35rem; }
    .help-step-title::after { content: ''; display: block; height: 1px; background: #99f6e4; margin-top: 0.5rem; }
    .help-step-desc { color: #475569; font-size: 0.9375rem; padding-top: 0.25rem; }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/bulk-import.png')}}" alt="{{ translate('bulk-import') }}">
                {{ translate('product_bulk_import') }}
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#bulkImportInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_bulk_import_btn') }}
            </button>
        </div>

        {{-- Modal تعليمات الاستيراد الجماعي --}}
        <div class="modal fade" id="bulkImportInstructionsModal" tabindex="-1" role="dialog" aria-labelledby="bulkImportInstructionsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header help-instructions-modal-header">
                        <div class="d-flex align-items-center" style="gap: 0.5rem;">
                            <a href="https://wa.me/970599814758" target="_blank" rel="noopener" class="help-whatsapp-icon" title="{{ translate('contact us on WhatsApp') }}" aria-label="WhatsApp">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <h5 class="modal-title" id="bulkImportInstructionsModalLabel">
                            <i class="tio-book-outlined me-1"></i> {{ translate('help_bulk_import_title') }}
                        </h5>
                    </div>
                    <div class="modal-body help-instructions-body">
                        {!! translate('help_bulk_import_page') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3 bulk-import-card">
            <div class="card-header bg-light bulk-import-card-header">
                <h5 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                    <i class="tio-info-outlined"></i>
                    {{ translate('Instructions') }}
                </h5>
            </div>
            <div class="card-body">
                <ol class="d-flex flex-column gap-2 ps-4 mb-0">
                    <li>{{ translate(' Download the format file and fill it with proper data.') }}</li>
                    <li>{{ translate(' You can download the example file to understand how the data must be filled.') }}</li>
                    <li>{{ translate(' Once you have downloaded and filled the format file, upload it in the form below and submit.') }}</li>
                    <li>{{ translate(" After uploading products you need to edit them and set product's images and choices.") }}</li>
                    <li>{{ translate(' You can get category and sub-category id from their list, please input the right ids.') }}</li>
                </ol>
            </div>
        </div>

        <div class="card bulk-import-card">
            <div class="card-header bg-light bulk-import-card-header">
                <h5 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                    <i class="tio-upload-outlined"></i>
                    {{ translate('Upload Products File') }}
                </h5>
            </div>
            <div class="card-body">
                <form class="product-form" action="{{route('admin.product.bulk-import')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="rest-part">
                        <div class="bulk-import-template-section mb-4">
                            <h6 class="mb-2 fw-semibold">{{ translate('bulk_import_template_title') }}</h6>
                            <p class="text-muted mb-3 bulk-import-desc">{{ translate('bulk_import_template_desc') }}</p>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{asset('assets/product_bulk_format.xlsx')}}" download=""
                                   class="btn btn-outline-primary btn-sm bulk-import-download-btn">
                                    <i class="tio-download-to me-1"></i>
                                    {{ translate('Download XLSX Format') }}
                                </a>
                                <a href="{{asset('assets/product_bulk_format.csv')}}" download=""
                                   class="btn btn-outline-primary btn-sm bulk-import-download-btn">
                                    <i class="tio-download-to me-1"></i>
                                    {{ translate('Download CSV Format') }}
                                </a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label fw-semibold mb-2">{{ translate('bulk_import_upload_label') }}</label>
                            <div class="row justify-content-center">
                                <div class="col-auto">
                                    <div class="upload-file--bulk-import" id="bulk-import-drop-zone">
                                        <input type="file" name="products_file" id="products_file_input" accept=".xlsx,.csv"
                                               class="upload-file__input" data-maxFileSize="{{ readableUploadMaxFileSize('file') }}">
                                        <div class="upload-file__img_drag upload-file__img" id="bulk-import-placeholder">
                                            <img src="{{asset('assets/admin/img/icons/drag-upload-file.png')}}" alt="{{ translate('upload') }}">
                                        </div>
                                        <div class="bulk-import-file-info d-none" id="bulk-import-file-info">
                                            <i class="tio-file-outlined bulk-import-file-icon"></i>
                                            <span class="bulk-import-file-name" id="bulk-import-file-name"></span>
                                            <span class="bulk-import-file-size" id="bulk-import-file-size"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-3">
                        <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                        <button type="submit" class="btn btn-primary btn--primary">{{ translate('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
<script>
(function () {
    var $zone = $('#bulk-import-drop-zone');
    var $input = $('#products_file_input');
    var $placeholder = $('#bulk-import-placeholder');
    var $fileInfo = $('#bulk-import-file-info');
    var $fileName = $('#bulk-import-file-name');
    var $fileSize = $('#bulk-import-file-size');
    if (!$zone.length || !$input.length) return;

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function showFileInfo(file) {
        if (file) {
            $fileName.text(file.name);
            $fileSize.text(formatFileSize(file.size));
            $placeholder.addClass('d-none');
            $fileInfo.removeClass('d-none');
            $zone.addClass('upload-file--has-file');
        } else {
            $placeholder.removeClass('d-none');
            $fileInfo.addClass('d-none');
            $zone.removeClass('upload-file--has-file');
        }
    }

    $input.on('change', function () {
        var file = this.files && this.files[0];
        showFileInfo(file || null);
    });

    $zone.closest('form').on('reset', function () {
        setTimeout(function () { showFileInfo(null); }, 0);
    });

    $zone.on('click', function (e) {
        if (e.target === $input[0] || $input[0].contains(e.target)) return;
        e.preventDefault();
        $input[0].click();
    });

    $zone.on('dragover dragenter', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $zone.addClass('upload-file--dragover');
    });
    $zone.on('dragleave drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $zone.removeClass('upload-file--dragover');
    });
    $zone.on('drop', function (e) {
        var files = e.originalEvent && e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.files;
        if (!files || !files.length) return;
        var file = files[0];
        var ext = (file.name || '').split('.').pop().toLowerCase();
        if (ext !== 'xlsx' && ext !== 'csv') {
            if (typeof toastr !== 'undefined' && toastr.error) {
                toastr.error('{{ translate("Only .xlsx and .csv files are allowed.") }}');
            }
            return;
        }
        var dt = new DataTransfer();
        dt.items.add(file);
        $input[0].files = dt.files;
        $input.trigger('change');
    });
})();
</script>
@endpush

@push('css_or_js')
<style>
/* هوية بصرية - استيراد المنتجات */
.bulk-import-card-header {
    border-bottom: 2px solid var(--primary-clr, #EC2227);
}
.bulk-import-template-section {
    padding: 1rem;
    background: #f8fafd;
    border-radius: 8px;
}
.bulk-import-desc {
    font-size: 1rem !important;
    line-height: 1.6;
}
.bulk-import-download-btn {
    border-color: var(--primary-clr, #EC2227);
    color: var(--primary-clr, #EC2227);
}
.bulk-import-download-btn:hover {
    background-color: var(--primary-clr, #EC2227);
    border-color: var(--primary-clr, #EC2227);
    color: #fff;
}
.upload-file--bulk-import {
    position: relative;
    min-height: 12rem;
    min-width: 16rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px dashed #e1e4e8;
    border-radius: 10px;
    cursor: pointer;
    transition: border-color .2s, background-color .2s;
}
.upload-file--bulk-import .upload-file__input {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}
.upload-file--bulk-import:hover {
    border-color: var(--primary-clr, #EC2227);
    background-color: rgba(236, 34, 39, 0.04);
}
.upload-file--bulk-import .upload-file__img_drag {
    pointer-events: none;
}
.upload-file--bulk-import.upload-file--dragover {
    border-color: var(--primary-clr, #EC2227);
    background-color: rgba(236, 34, 39, 0.08);
}
.upload-file--bulk-import.upload-file--has-file {
    border-color: var(--secondary-clr, #747113);
    background-color: rgba(116, 113, 19, 0.06);
}
.bulk-import-file-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    pointer-events: none;
    padding: 1rem;
    text-align: center;
}
.bulk-import-file-icon {
    font-size: 2.5rem;
    color: var(--secondary-clr, #747113);
}
.bulk-import-file-name {
    font-weight: 600;
    font-size: 0.9rem;
    color: #1e2022;
    word-break: break-all;
    max-width: 14rem;
}
.bulk-import-file-size {
    font-size: 0.8rem;
    color: #8c98a4;
}
</style>
@endpush
