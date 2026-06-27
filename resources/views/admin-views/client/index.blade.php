@extends('layouts.admin.app')

@section('title', translate('our_clients') ?: 'عملاؤنا يثقون بنا')

@push('css_or_js')
<style>
.category-form-card-header { border-bottom: 2px solid var(--primary-clr, #EC2227); }
.category-form-card-header h6 { font-size: 1.15rem !important; }
.badge-category-count { font-size: 1rem !important; font-weight: 600; padding: 0.4rem 0.75rem; background-color: var(--primary-clr, #EC2227) !important; color: #fff !important; }
.client-logo-wrap { width: 60px; height: 60px; border-radius: 8px; overflow: hidden; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; }
.client-logo-wrap img { width: 100%; height: 100%; object-fit: contain; }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <i class="tio-group fs-22"></i>
                {{ translate('our_clients') ?: 'عملاؤنا يثقون بنا' }}
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <p class="text-muted small mb-3">
                    أضف عملاءك هنا مع شعار كل شركة. تظهر هذه الشعارات في قسم "عملاؤنا يثقون بنا" بالواجهة الرئيسية للمتجر.
                </p>
                <form action="{{ route('admin.client.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-light rounded p-3">
                        <div class="row g-3">
                            <div class="col-lg-5">
                                <div class="mb-3">
                                    <label class="input-label">{{ translate('name') ?: 'اسم العميل / الشركة' }}
                                        <span class="input-label-secondary text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="مثال: شركة المستقبل" required maxlength="255">
                                </div>
                                <div class="mb-3">
                                    <label class="input-label">{{ translate('link') ?: 'رابط الموقع (اختياري)' }}</label>
                                    <input type="url" name="link" class="form-control" placeholder="https://example.com">
                                </div>
                                <div class="mb-0">
                                    <label class="input-label">{{ translate('position') ?: 'ترتيب الظهور' }}</label>
                                    <input type="number" name="position" class="form-control" min="0" value="0" placeholder="0">
                                    <small class="text-muted">{{ translate('position_hint') ?: 'الأصغر يظهر أولاً' }}</small>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="form-group mb-0">
                                    <label class="mb-2">{{ translate('logo') ?: 'شعار الشركة' }}</label>
                                    <div class="custom_upload_input max-h200px ratio-1">
                                        <input type="file" name="logo" class="custom-upload-input-file meta-img" id="" data-imgpreview="pre_client_logo_viewer"
                                               accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                               data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">

                                        <span class="delete_file_input btn btn-outline-danger btn-sm square-btn" style="display: none">
                                            <i class="tio-delete"></i>
                                        </span>

                                        <div class="img_area_with_preview position-absolute z-index-2">
                                            <img id="pre_client_logo_viewer" class="aspect-1 bg-white" src="img" onerror="this.classList.add('d-none')" alt="{{ translate('logo') }}">
                                        </div>
                                        <div class="position-absolute h-100 top-0 w-100 d-flex align-content-center justify-content-center">
                                            <div class="d-flex flex-column justify-content-center align-items-center overflow-hidden">
                                                <h3 class="text-muted">{{ translate('Drag & Drop here') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="fs-14 text-muted mb-0 mt-2">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="reset" class="btn btn--reset min-w-120">{{translate('reset')}}</button>
                            <button type="submit" class="btn btn-primary min-w-120">{{translate('submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-light category-form-card-header">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                            <i class="tio-folder-outlined me-2"></i>{{ translate('our_clients') ?: 'عملاؤنا يثقون بنا' }}
                        </h6>
                        <span class="badge badge-category-count">{{ $clients->total() }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body p-2 bg-light">
                <form action="{{ request()->url() }}" method="GET" class="category-filter-form">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <div class="row align-items-end g-2">
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="form-label small mb-1">{{ translate('Search by name') ?: 'بحث بالاسم' }}</label>
                            <input type="search" name="search" class="form-control form-control-sm"
                                   placeholder="{{ translate('Search by name') }}" value="{{ $search ?? '' }}" autocomplete="off">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label small mb-1">{{ translate('status') }}</label>
                            <select class="form-control form-control-sm" name="status">
                                <option value="" {{ (($status ?? '') === '') ? 'selected' : '' }}>{{ translate('all') }}</option>
                                <option value="1" {{ ($status ?? '') === '1' ? 'selected' : '' }}>{{ translate('active') }}</option>
                                <option value="0" {{ ($status ?? '') === '0' ? 'selected' : '' }}>{{ translate('inactive') }}</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 d-flex gap-2 align-items-end category-filter-btns">
                            <button type="submit" class="btn btn-primary">
                                <i class="tio-checkmark-circle-outlined me-1"></i>{{ translate('Show_Data') }}
                            </button>
                            <a href="{{ route('admin.client.add-new') }}" class="btn btn-soft-secondary d-inline-flex align-items-center justify-content-center">{{ translate('clear') }}</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
            <div class="table-responsive datatable-custom">
                <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>{{ translate('logo') ?: 'الشعار' }}</th>
                            <th>{{ translate('name') ?: 'الاسم' }}</th>
                            <th>{{ translate('position') ?: 'الترتيب' }}</th>
                            <th>{{translate('status')}}</th>
                            <th class="text-center">{{translate('action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($clients as $key=>$client)
                        <tr>
                            <td>{{$clients->firstitem()+$key}}</td>
                            <td>
                                <div class="client-logo-wrap">
                                    @if($client->logo_fullpath)
                                        <img src="{{ $client->logo_fullpath }}" alt="{{ $client->name }}">
                                    @else
                                        <i class="tio-image text-muted fs-22"></i>
                                    @endif
                                </div>
                            </td>
                            <td>{{$client->name}}</td>
                            <td>{{$client->position}}</td>
                            <td>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input change-status"
                                           {{ $client->status == 1 ? 'checked' : '' }}
                                           data-route="{{ route('admin.client.status', [$client->id, $client->status == 1 ? 0 : 1]) }}">
                                    <span class="switcher_control"></span>
                                </label>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-info square-btn"
                                        href="{{route('admin.client.edit',[$client->id])}}"><i class="tio tio-edit"></i></a>
                                    <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                       data-id="client-{{$client->id}}"
                                       data-message="{{translate('Want to delete this client ?')}}">
                                        <i class="tio tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.client.delete',[$client->id])}}"
                                        method="post" id="client-{{$client->id}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {!! $clients->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($clients)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('assets/admin//svg/illustrations/sorry.svg')}}" alt="{{ translate('Image Description') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('assets/admin/js/image-upload.js') }}"></script>
@endpush
