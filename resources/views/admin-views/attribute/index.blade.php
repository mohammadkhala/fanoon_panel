@extends('layouts.admin.app')

@section('title', translate('Add new attribute'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/attribute.png')}}" alt="{{ translate('attribute') }}">
                {{translate('attribute_Setup')}}
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#attributeAddInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_attribute_add_btn') }}
            </button>
        </div>

        @include('admin-views.partials._help-instructions-modal', ['modalId' => 'attributeAddInstructionsModal', 'titleKey' => 'help_attribute_add_title', 'pageKey' => 'help_attribute_add_page'])

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.attribute.store')}}" method="post" id="attribute_form">
                    @csrf
                    @php($language=\App\Models\BusinessSetting::where('key','language')->first()?->value ?? null)
                    @php($default_lang = 'en')

                    @if($language)
                        @php($default_lang = json_decode($language)[0])
                        <ul class="nav nav-tabs mb-4 max-content">
                            @foreach(json_decode($language) as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}" href="#" id="{{$lang}}-link">{{Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="row align-items-end g-3 bg-light rounded p-3 mb-2">
                            <div class="col-12 col-lg-9">
                                @foreach(json_decode($language) as $lang)
                                    <div class="form-group {{$lang != $default_lang ? 'd-none':''}} lang_form" id="{{$lang}}-form">
                                        <label class="input-label" for="exampleFormControlInput1">
                                            {{translate('name')}} ({{strtoupper($lang)}})
                                            @if($lang == 'en')
                                                <span class="input-label-secondary text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="text" name="name[]" class="form-control" placeholder="{{ translate('New Attribute') }}" maxlength="255">
                                        @if($lang == 'en')
                                            <span class="error-text" data-error="name.0"></span>
                                        @endif
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="row align-items-end g-3 bg-light rounded p-3 mb-2">
                            <div class="col-12 col-lg-9">
                                <div class="form-group lang_form" id="{{$default_lang}}-form">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('name')}} ({{strtoupper($default_lang)}})<span class="input-label-secondary text-danger">*</span></label>
                                    <input type="text" name="name[]" class="form-control" placeholder="{{ translate('New Attribute') }}" >
                                    <span class="error-text" data-error="name.0"></span>
                                </div>
                                <input type="hidden" name="lang[]" value="{{$default_lang}}">
                            </div>
                        </div>
                    @endif
                    <div class="d-flex flex-wrap justify-content-end gap-2">
                        <button type="reset" class="btn btn-secondary min-w-120">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn-primary min-w-120">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mt-3">
            <div class="p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="m-0">{{translate('Attribute List ')}}</h6>
                        <span class="badge badge-soft-dark rounded-50 fz-10">{{$attributes->total()}}</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <form action="{{ request()->url() }}" method="GET">
                            @foreach (request()->except('search','page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <div class="input-group min-h-35">
                                <input id="datatableSearch_" type="search" name="search"
                                       class="form-control py-1 h-35 fs-12"
                                       placeholder="{{translate('Search by name')}}" aria-label="Search"
                                       value="{{$search}}" autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary px-2 py-1 min-h-35">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>



            <div class="table-responsive datatable-custom">
                <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>{{translate('name')}}</th>
                            <th class="text-center">{{translate('action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($attributes as $key=>$attribute)
                        <tr>
                            <td>{{$attributes->firstitem()+$key}}</td>
                            <td>{{ $attribute->name ?: $attribute->translations->first()?->value ?? '—' }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-info square-btn"
                                        href="{{route('admin.attribute.edit',[$attribute['id']])}}"><i class="tio tio-edit"></i></a>
                                    <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                       data-id="attribute-{{$attribute['id']}}"
                                       data-message="{{translate('Want to delete this attribute ?')}}">
                                        <i class="tio tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.attribute.delete',[$attribute['id']])}}"
                                        method="post" id="attribute-{{$attribute['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $attributes->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($attributes)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('Image Description') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        'use strict'

        $(".lang_link").click(function(e){
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            $("#"+lang+"-form").removeClass('d-none');
            if(lang == '{{$default_lang}}')
            {
                $(".from_part_2").removeClass('d-none');
            }
            else
            {
                $(".from_part_2").addClass('d-none');
            }
        });

        submitByAjax('#attribute_form', {
            hasEditors: false,
            languages: @json(json_decode($language) ?? []),
            successMessage: '{{ translate("Attribute added successfully!") }}',
            redirectUrl: '{{ route('admin.attribute.add-new') }}'
        });

    </script>
@endpush
