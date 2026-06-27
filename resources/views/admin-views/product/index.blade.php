@extends('layouts.admin.app')

@section('title', translate('Add new product'))

@push('css_or_js')
    <link href="{{asset('assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
    <style>
        .help-instructions-modal-header { background: #0d9488; color: #fff; border-bottom: none; padding: 1rem 1.25rem; }
        .help-instructions-modal-header .modal-title + .d-flex { margin-left: auto; }
        .help-instructions-modal-header .help-whatsapp-icon { color: #fff; display: flex; align-items: center; justify-content: center; width: 38px; height: 38px; padding: 0; border-radius: 6px; background: rgba(255,255,255,0.15); border: 2px solid #fff; transition: all 0.2s; }
        .help-instructions-modal-header .help-whatsapp-icon:hover { color: #fff; background: rgba(37,211,102,0.9); border-color: #25D366; }
        .help-instructions-modal-header .modal-title { color: #fff; font-weight: 600; font-size: 1.15rem; }
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
                <img width="20" src="{{asset('assets/admin/img/icons/product.png')}}"
                     alt="{{ translate('product') }}">
                {{translate('add_new_product')}}
            </h2>
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#productAddInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_products_add_btn') }}
            </button>
        </div>

        {{-- Modal تعليمات إضافة منتج --}}
        <div class="modal fade" id="productAddInstructionsModal" tabindex="-1" role="dialog" aria-labelledby="productAddInstructionsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header help-instructions-modal-header">
                        <h5 class="modal-title" id="productAddInstructionsModalLabel">
                            <i class="tio-book-outlined me-1"></i> {{ translate('help_products_add_title') }}
                        </h5>
                        <div class="d-flex align-items-center" style="gap: 0.5rem;">
                            <a href="https://wa.me/970599814758" target="_blank" rel="noopener" class="help-whatsapp-icon" title="{{ translate('contact us on WhatsApp') }}" aria-label="WhatsApp">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <div class="modal-body help-instructions-body">
                        {!! translate('help_products_add_page') !!}
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <form action="{{ route('admin.product.store') }}" method="post" id="product_form"
                      enctype="multipart/form-data">
                    @csrf
                    @php($language=\App\Models\BusinessSetting::where('key','language')->first()?->value ?? null)
                    @php($default_lang = 'ar')
                    @php($langListForValidation = $language ? json_decode($language, true) : [])
                    @php($langListForValidation = is_array($langListForValidation) ? $langListForValidation : [])
                    @php($defaultLocaleForProductName = config('app.locale', 'ar'))
                    @php($default_name_field_index = array_search($defaultLocaleForProductName, $langListForValidation, true))
                    @if($default_name_field_index === false)
                        @php($default_name_field_index = 0)
                    @endif
                    @if($language)
                        @php($default_lang = json_decode($language)[0] ?? 'ar')
                        <ul class="nav nav-tabs mb-4 max-content">

                            @foreach(json_decode($language) as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}" href="#"
                                       id="{{$lang}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                </li>
                            @endforeach

                        </ul>
                        @foreach(json_decode($language) as $langIdx => $lang)
                            <div class="card mb-3 card-body {{$lang != $default_lang ? 'd-none':''}} lang_form"
                                 id="{{$lang}}-form">
                                <div class="form-group">
                                    <label class="input-label" for="{{$lang}}_name">
                                        {{translate('name')}}({{strtoupper($lang)}})
                                        @if($lang == $default_lang)
                                            <span class="input-label-secondary text-danger">*</span>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-primary ms-2 translate-btn"
                                                    data-field="name" data-source-lang="{{$default_lang}}"
                                                    data-target-lang="{{$lang}}" data-source-id="{{$default_lang}}_name"
                                                    data-target-id="{{$lang}}_name" data-is-html="0"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="{{ translate('help_product_auto_translate') }}">
                                                <i class="tio-globe"></i> {{ translate('Auto translate') }}
                                            </button>
                                        @endif
                                    </label>
                                    <input type="text"  name="name[]"
                                           id="{{$lang}}_name" class="form-control"
                                           placeholder="{{ translate('New Product') }}"
                                           >
                                    @if((int) $langIdx === (int) $default_name_field_index)
                                        <span class="error-text" data-error="name.{{ $default_name_field_index }}"></span>
                                    @endif
                                </div>
                                <input type="hidden" name="lang[]" value="{{$lang}}">
                                <div class="form-group pt-4">
                                    <label class="input-label"
                                           for="{{$lang}}_description">{{ translate('short_description') }}
                                        ({{strtoupper($lang)}})
                                        @if($lang != $default_lang)
                                            <button type="button" class="btn btn-sm btn-outline-primary ms-2 translate-btn"
                                                    data-field="description" data-source-lang="{{$default_lang}}"
                                                    data-target-lang="{{$lang}}"
                                                    data-source-id="{{$default_lang}}_editor"
                                                    data-target-id="{{$lang}}_editor" data-is-html="1"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="{{ translate('help_product_auto_translate') }}">
                                                <i class="tio-globe"></i> {{ translate('Auto translate') }}
                                            </button>
                                        @endif
                                    </label>
                                    <div id="{{$lang}}_editor"></div>
                                    <textarea name="description[]" style="display:none"
                                              id="{{$lang}}_hiddenArea"></textarea>
                                    @if((int) $langIdx === (int) $default_name_field_index)
                                        <span class="error-text" data-error="description.{{ $default_name_field_index }}"></span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="card p-4" id="{{$default_lang}}-form">
                            <div class="form-group">
                                <label class="input-label">{{translate('name')}} ({{ strtoupper($default_lang) }})
                                    <span class="input-label-secondary text-danger">*</span>
                                </label>
                                <input type="text" name="name[]" class="form-control"
                                       placeholder="{{ translate('new_product') }}" >
                                <span class="error-text" data-error="name.0"></span>
                            </div>
                            <input type="hidden" name="lang[]" value="{{ $default_lang }}">
                            <div class="form-group pt-4">
                                <label class="input-label">{{ translate('short_description') }}
                                    ({{ strtoupper($default_lang) }})</label>
                                <div id="editor"></div>
                                <textarea name="description[]" style="display:none" id="hiddenArea"></textarea>
                                <span class="error-text" data-error="description.0"></span>
                            </div>
                        </div>
                    @endif

                    <div id="from_part_2">
                        {{-- القسم 1: التصنيف --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="tio-folder-outlined me-2"></i>{{ translate('section_category') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label class="input-label" for="category_id">{{ translate('category') }}
                                                <span class="input-label-secondary text-danger">*</span></label>
                                            <select name="category_id" id="category_id" class="form-control js-select2-custom"
                                                    onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-categories')">
                                                <option value="">--- {{ translate('select category') }} ---</option>
                                                @foreach($categories as $category)
                                                    <option value="{{$category['id']}}">{{$category['name']}}</option>
                                                @endforeach
                                            </select>
                                            <span class="error-text" data-error="category_id"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label class="input-label" for="sub-categories">{{ translate('sub_category') }}</label>
                                            <select name="sub_category_id" id="sub-categories"
                                                    class="form-control js-select2-custom"
                                                    onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-sub-categories')">
                                            </select>
                                            <span class="error-text" data-error="sub_category_id"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @include('admin-views.product.partials._tags-input')
                        @include('admin-views.product.partials._related-products')

                        {{-- القسم 2: السعر والمخزون --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="tio-money me-2"></i>{{ translate('section_price_stock') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="unit" value="">
                                <input type="hidden" name="tax" value="0">
                                <input type="hidden" name="tax_type" value="percent">
                                <div class="row g-3">
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="form-group">
                                            <label class="input-label" for="product_base_price">{{ translate('price') }}
                                                <span class="input-label-secondary text-danger">*</span></label>
                                            <input type="number" id="product_base_price" min="0" max="100000000" step="0.01" value=""
                                                   name="price" class="form-control"
                                                   placeholder="{{ translate('Ex : 100') }}"
                                                   onkeydown="return !['e','E','+','-'].includes(event.key)"
                                                   oninput="
                                                   if (this.value !== '' && parseFloat(this.value) < 0) this.value = 0;
                                                   if (this.value.includes('.')) {this.value = this.value.split('.').map((part, index) => index === 1 ? part.slice(0, 2) : part).join('.');}
                                                   typeof syncUserTypePricePlaceholders === 'function' && syncUserTypePricePlaceholders(this.value);
                                                   ">
                                            <span class="error-text" data-error="price"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="form-group">
                                            <label class="input-label" for="total_stock">{{ translate('stock') }}
                                                <span class="input-label-secondary text-danger">*</span></label>
                                            <input type="number" id="total_stock" min="0" max="100000000" value="" name="total_stock"
                                                   class="form-control"
                                                   placeholder="{{ translate('Ex : 100') }}"
                                                   onkeydown="return !['e','E','+','-','.'].includes(event.key)"
                                                   oninput="if (this.value !== '' && parseInt(this.value, 10) < 0) this.value = 0;">
                                            <span class="error-text" data-error="total_stock"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6">
                                        <div class="form-group">
                                            <label class="input-label" for="minimum_stock_alert">{{ translate('minimum_stock_alert') }}</label>
                                            <input type="number" min="0" max="100000000" value="" name="minimum_stock_alert"
                                                   id="minimum_stock_alert" class="form-control"
                                                   placeholder="{{ translate('Ex : 5') }}"
                                                   onkeydown="return !['e','E','+','-','.'].includes(event.key)">
                                            <small class="text-muted">{{ translate('minimum_stock_alert_hint') }}</small>
                                        </div>
                                    </div>
                                </div>

                                @if(isset($userTypes) && $userTypes->isNotEmpty())
                                <div class="mt-4 pt-3 border-top">
                                    {{-- ── Product Visibility by User Type ─────────────── --}}
                                    <div class="card border mb-3">
                                        <div class="card-header py-2">
                                            <h6 class="card-header-title mb-0">
                                                <i class="tio tio-user-switch"></i>
                                                {{ translate('product_visibility') ?: 'من يرى هذا المنتج' }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted small mb-3">
                                                اختر من يستطيع رؤية هذا المنتج في المتجر. اترك «كل الزوار» محدداً ليظهر للجميع.
                                            </p>

                                            {{-- Everyone toggle --}}
                                            <div class="custom-control custom-checkbox mb-3">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       id="prod-vis-everyone"
                                                       name="visible_everyone"
                                                       value="1"
                                                       checked
                                                       onchange="toggleProductVisibilityPanel(this)">
                                                <label class="custom-control-label font-weight-bold" for="prod-vis-everyone">
                                                    كل الزوار (بدون قيود)
                                                </label>
                                            </div>

                                            {{-- Specific types panel --}}
                                            <div id="prod-vis-panel" style="display:none">
                                                <div class="custom-control custom-checkbox mb-2">
                                                    <input type="checkbox"
                                                           class="custom-control-input"
                                                           id="prod-vis-guest"
                                                           name="visible_to_user_types[]"
                                                           value="0">
                                                    <label class="custom-control-label" for="prod-vis-guest">
                                                        <i class="tio tio-user-add"></i> الزوار غير المسجلين
                                                    </label>
                                                </div>
                                                @foreach($userTypes as $ut)
                                                <div class="custom-control custom-checkbox mb-2">
                                                    <input type="checkbox"
                                                           class="custom-control-input"
                                                           id="prod-vis-ut-{{ $ut->id }}"
                                                           name="visible_to_user_types[]"
                                                           value="{{ $ut->id }}">
                                                    <label class="custom-control-label" for="prod-vis-ut-{{ $ut->id }}">
                                                        {{ $ut->name }}
                                                        @if($ut->is_default)
                                                            <span class="badge badge-soft-success badge-sm">{{ translate('default') }}</span>
                                                        @endif
                                                    </label>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="mb-3 d-flex align-items-center gap-1">
                                        {{ translate('section_user_type_pricing') }}
                                        <i class="tio-info-outlined text-muted fs-14" data-toggle="tooltip" data-placement="top" title="{{ translate('help_product_user_type_pricing') }}"></i>
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="card bg-light p-3">
                                                <h6 class="mb-2">{{ translate('Prices by user type') }}</h6>
                                                <p class="text-muted small mb-2">{{ translate('Leave blank to use base price for that type') }}</p>
                                                <div class="row">
                                                    @foreach($userTypes as $ut)
                                                        <div class="col-md-4 col-6 mb-2">
                                                            <label class="input-label">{{ $ut->name }}</label>
                                                            <input type="number" name="user_type_price[{{ $ut->id }}]" class="form-control form-control-sm user-type-price-input" min="0" step="0.01"
                                                                   placeholder="1" value="" data-empty-placeholder="1">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="card bg-light p-3">
                                                <h6 class="mb-2">{{ translate('Discounts by user type') }}</h6>
                                                <p class="text-muted small mb-2">{{ translate('Leave blank to use base discount for that type') }}</p>
                                                <div class="row">
                                                    @foreach($userTypes as $ut)
                                                        <div class="col-md-6 col-lg-4 mb-2">
                                                            <label class="input-label">{{ $ut->name }}</label>
                                                            <div class="input-group discount-input-group">
                                                                <input type="number" name="user_type_discount[{{ $ut->id }}]" class="form-control discount-amount-input" min="0" step="0.01"
                                                                       placeholder="0" value="">
                                                                <select name="user_type_discount_type[{{ $ut->id }}]" class="form-control discount-type-select">
                                                                    <option value="percent">{{ translate('percent') }}</option>
                                                                    <option value="amount">{{ translate('amount') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- القسم 3: خصائص المنتج والمتغيرات --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-semibold d-flex align-items-center gap-1">
                                    <i class="tio-label me-2"></i>{{ translate('section_attributes_variants') }}
                                    <i class="tio-info-outlined text-muted fs-14" data-toggle="tooltip" data-placement="top" title="{{ translate('help_product_attributes') }}"></i>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="input-label d-flex align-items-center gap-1">
                                        {{ translate('select_attributes') }}
                                        <i class="tio-info-outlined text-muted fs-14" data-toggle="tooltip" data-placement="top" title="{{ translate('help_product_attributes') }}"></i>
                                    </label>
                                    <select name="attribute_id[]" id="choice_attributes"
                                            class="form-control js-select2-custom"
                                            multiple="multiple">
                                        @foreach(\App\Models\Attribute::orderBy('name')->get() as $attribute)
                                            <option value="{{$attribute['id']}}">{{$attribute['name']}}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">{{ translate('optional') }}</small>
                                    <span class="error-text" data-error="attribute_id"></span>
                                </div>
                                <div class="customer_choice_options mt-3" id="customer_choice_options"></div>
                                <div class="variant_combination mt-3" id="variant_combination"></div>
                            </div>
                        </div>

                        {{-- القسم 4: صور المنتج --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="tio-photo me-2"></i>{{ translate('section_product_images') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <label class="input-label">{{ translate('product_image') }}
                                        <span class="text-danger">*</span>
                                        <small class="text-muted">({{ translate('ratio') }} 1:1)</small>
                                    </label>
                                </div>
                                <div class="row" id="coba"></div>
                                <p class="fs-14 text-muted mb-0 mt-2">
                                    {{ translate('Image format') }}: {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |
                                    {{ translate('maximum size') }}: {{ readableUploadMaxFileSize('image') }}
                                </p>
                                <span class="error-text justify-content-start" data-error="images"></span>
                            </div>
                        </div>

                        {{-- قسم قالب التصميم (اختياري) --}}
                        <div class="card mb-3" id="design-tmpl-card">
                            <div class="card-header bg-light" style="cursor:pointer" onclick="toggleDesignTmpl()"
                                 data-toggle="collapse" data-target="#design-tmpl-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                                        <i class="tio-layers nav-icon fs-18"></i>
                                        {{ translate('section_design_template') ?: 'قالب التصميم (اختياري)' }}
                                    </h6>
                                    <div class="d-flex align-items-center gap-3">
                                        <label class="switcher mb-0" onclick="event.stopPropagation()">
                                            <input type="checkbox" class="switcher_input" id="enable-design-tmpl"
                                                   onchange="toggleDesignTmplEnabled(this.checked)">
                                            <span class="switcher_control"></span>
                                        </label>
                                        <i class="fa fa-chevron-down text-muted" id="design-tmpl-chevron"></i>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    {{ translate('design_template_hint') ?: 'اختر قالباً يظهر للعميل عند الضغط على "صمّم منتجك" في صفحة هذا المنتج.' }}
                                </small>
                            </div>
                            <div class="card-body" id="design-tmpl-body" style="display:none">
                                <input type="hidden" name="tmpl_selected_id" id="tmpl-selected-id" value="">
                                @include('admin-views.design-template._template_picker', [
                                    'designTemplates' => $designTemplates,
                                    'selectedId'      => null,
                                ])
                            </div>
                        </div>

                        {{-- أزرار الحفظ --}}
                        <div class="card mb-3">
                            <div class="card-body d-flex flex-wrap justify-content-end gap-2">
                                <button type="reset" class="btn btn-secondary min-w-120">{{ translate('reset') }}</button>
                                <button type="submit" class="btn btn-primary min-w-120">{{ translate('submit') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{asset('assets/admin/js/spartan-multi-image-picker.js')}}"></script>
    <script src="{{asset('assets/admin')}}/js/tags-input.min.js"></script>
    @include('admin-views.business-settings.partial.summernote-editor-scripts')
    <script>
        "use strict";

        function toggleProductVisibilityPanel(cb) {
            document.getElementById('prod-vis-panel').style.display = cb.checked ? 'none' : '';
        }

        /* ── Design Template section toggle ── */
        function toggleDesignTmpl() {
            const body = document.getElementById('design-tmpl-body');
            const chev = document.getElementById('design-tmpl-chevron');
            const open = body.style.display === 'none';
            body.style.display = open ? 'block' : 'none';
            chev.style.transform = open ? 'rotate(180deg)' : '';
        }

        function toggleDesignTmplEnabled(checked) {
            const body = document.getElementById('design-tmpl-body');
            if (checked && body.style.display === 'none') {
                body.style.display = 'block';
                document.getElementById('design-tmpl-chevron').style.transform = 'rotate(180deg)';
            }
            if (!checked) {
                document.getElementById('tmpl-selected-id').value = '';
            }
        }

        function syncUserTypePricePlaceholders(basePrice) {
            var val = (basePrice === '' || basePrice == null) ? '' : String(basePrice);
            document.querySelectorAll('.user-type-price-input').forEach(function(inp) {
                if (inp.value === '') {
                    inp.placeholder = val;
                }
                inp.setAttribute('data-empty-placeholder', val);
            });
        }

        $(document).ready(function() {
            $('#product_base_price').on('change', function() { syncUserTypePricePlaceholders(this.value); });
        });

        $(".lang_link").click(function (e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            $("#" + lang + "-form").removeClass('d-none');
            if (lang == '{{$default_lang}}') {
                $("#from_part_2").removeClass('d-none');
            } else {
                $("#from_part_2").addClass('d-none');
            }
        })

        $(function () {
            let maxSizeReadable = "{{ readableUploadMaxFileSize('image') }}";
            let maxFileSize = 2 * 1024 * 1024; // default 2MB

            if (maxSizeReadable.toLowerCase().includes('mb')) {
                maxFileSize = parseFloat(maxSizeReadable) * 1024 * 1024;
            } else if (maxSizeReadable.toLowerCase().includes('kb')) {
                maxFileSize = parseFloat(maxSizeReadable) * 1024;
            }

            function setAcceptForAllInputs() {
                const allowedExtensions = ".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}";
                $('#coba input[type=file]').each(function() {
                    $(this).attr('accept', allowedExtensions);
                });
            }
            setAcceptForAllInputs();

            $("#coba").spartanMultiImagePicker({
                fieldName: 'images[]',
                maxCount: 4,
                rowHeight: '215px',
                groupClassName: 'col-auto',
                maxFileSize: maxFileSize,
                placeholderImage: {
                    image: '{{asset('assets/admin/img/400x400/img2.jpg')}}',
                    width: '100%'
                },
                allowedExt:        'png|jpg|jpeg|gif|webp',
                dropFileLabel: "{{ translate('Drop Here') }}",
                onAddRow: function (index, file) {
                    setAcceptForAllInputs();
                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('{{ translate("Please only input png, jpg, jpeg, gif, webp type file") }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error(`"${file.name}" exceeds ${maxSizeReadable} limit!`, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });

        function getRequest(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }

        $(document).on('ready', function () {
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

        $('#choice_attributes').on('change', function () {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function () {
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        var choiceTitlePlaceholder = "{{ translate('Choice Title') }}";
        var choiceValuesPlaceholder = "{{ translate('Enter choice values') }}";
        function add_more_customer_choice_option(i, name) {
            let n = name.split(' ').join('');
            $('#customer_choice_options').append('<div class="row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i + '"><input type="text" class="form-control" name="choice[]" value="' + n + '" placeholder="' + choiceTitlePlaceholder + '" readonly></div><div class="col-lg-9"><input type="text" class="form-control" name="choice_options_' + i + '[]" placeholder="' + choiceValuesPlaceholder + '" data-role="tagsinput" onchange="combination_update()"></div></div>');
            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        function combination_update() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{route('admin.product.variant-combination')}}',
                data: $('#product_form').serialize(),
                success: function (data) {
                    $('#variant_combination').html(data.view);
                    if (data.length > 1) {
                        $('#quantity').hide();
                    } else {
                        $('#quantity').show();
                    }
                    if (typeof update_qty === 'function') {
                        update_qty();
                    }
                }
            });
        }

        var isRtl = '{{ session("local", "ar") }}' === 'ar';
        @if($language)
        @foreach(json_decode($language) as $lang)
        $('#{{$lang}}_editor').summernote({
            height: 200,
            toolbar: [['style',['style','bold','italic','underline','clear']],['para',['ul','ol','paragraph']],['table',['table']],['insert',['link','picture']],['view',['fullscreen','codeview']]],
            fontNames: ['Arial','Cairo','Helvetica','Times New Roman'],
            direction: isRtl ? 'rtl' : 'ltr',
            lang: isRtl ? 'ar-AR' : 'en-US',
            dialogsInBody: true
        });
        @endforeach
        @else
        $('#editor').summernote({
            height: 200,
            toolbar: [['style',['style','bold','italic','underline','clear']],['para',['ul','ol','paragraph']],['table',['table']],['insert',['link','picture']],['view',['fullscreen','codeview']]],
            fontNames: ['Arial','Cairo','Helvetica','Times New Roman'],
            direction: isRtl ? 'rtl' : 'ltr',
            lang: isRtl ? 'ar-AR' : 'en-US',
            dialogsInBody: true
        });
        @endif

        submitByAjax('#product_form', {
            hasEditors: true,
            languages: @json(json_decode($language) ?? []),
            successMessage: '{{ translate("product uploaded successfully!") }}',
            redirectUrl: '{{ route('admin.product.list') }}',
            redirectDelay: 0
        });

        $(document).on('click', '.translate-btn', function() {
            var btn = $(this);
            var sourceId = btn.data('source-id');
            var targetId = btn.data('target-id');
            var isHtml = btn.data('is-html') == 1;
            var sourceLang = btn.data('source-lang');
            var targetLang = btn.data('target-lang');

            var sourceEl = document.getElementById(sourceId);
            if (!sourceEl) return;

            var text = '';
            if (isHtml) {
                var $src = $('#' + sourceId);
                text = ($src.length && typeof $src.summernote === 'function') ? ($src.summernote('code') || '') : '';
                if (!text && sourceEl.querySelector && sourceEl.querySelector('.ql-editor')) {
                    text = sourceEl.querySelector('.ql-editor').innerHTML || '';
                }
            } else {
                text = $(sourceEl).val() || '';
            }

            text = $.trim(text);
            if (isHtml && (text === '' || text === '<p><br></p>' || text === '<p></p>')) text = '';
            if (!text) {
                toastr.warning('{{ translate("Please fill the source field first") }}', { CloseButton: true, ProgressBar: true });
                return;
            }

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> {{ translate("Translating...") }}');

            $.ajax({
                url: '{{ route("admin.product.translate") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    text: text,
                    source_lang: sourceLang,
                    target_lang: targetLang,
                    is_html: isHtml ? 1 : 0
                },
                success: function(res) {
                    if (res.success && res.translated_text) {
                        var targetEl = document.getElementById(targetId);
                        if (isHtml) {
                            var $tgt = $('#' + targetId);
                            if ($tgt.length && typeof $tgt.summernote === 'function') {
                                $tgt.summernote('code', res.translated_text);
                            } else if (targetEl && targetEl.querySelector && targetEl.querySelector('.ql-editor')) {
                                targetEl.querySelector('.ql-editor').innerHTML = res.translated_text;
                            }
                        } else {
                            $(targetEl).val(res.translated_text);
                        }
                        toastr.success('{{ translate("Translation applied. You can edit before saving.") }}', { CloseButton: true, ProgressBar: true });
                    } else {
                        toastr.error(res.message || '{{ translate("Translation failed") }}', { CloseButton: true, ProgressBar: true });
                    }
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : '{{ translate("Translation failed") }}';
                    toastr.error(msg, { CloseButton: true, ProgressBar: true });
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="tio-globe"></i> {{ translate("Auto translate") }}');
                }
            });
        });

        function update_qty() {
            var total_qty = 0;
            var qty_elements = $('input[name^="stock_"]');
            for (var i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val(), 10) || 0;
            }
            if (qty_elements.length > 0) {
                $('input[name="total_stock"]').attr("readonly", true);
                $('input[name="total_stock"]').val(total_qty);
            } else {
                $('input[name="total_stock"]').attr("readonly", false);
            }
        }
    </script>
@endpush
