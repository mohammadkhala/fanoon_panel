@extends('layouts.admin.app')

@section('title', translate('Update product'))

@push('css_or_js')
    <link href="{{asset('assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
    <style>
        .help-instructions-modal-header { background: #0d9488; color: #fff; border-bottom: none; padding: 1rem 1.25rem; }
        .help-instructions-modal-header .modal-title { order: 1; }
        .help-instructions-modal-header .d-flex.align-items-center { order: 2; margin-inline-start: auto; }
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
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i
                            class="tio-edit"></i> {{translate('product')}} {{translate('update')}}</h1>
                </div>
                <div class="col-sm-auto">
                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#productEditInstructionsModal">
                        <i class="tio-book-outlined"></i> {{ translate('help_products_edit_btn') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal تعليمات تعديل منتج --}}
        <div class="modal fade" id="productEditInstructionsModal" tabindex="-1" role="dialog" aria-labelledby="productEditInstructionsModalLabel" aria-hidden="true">
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
                        <h5 class="modal-title" id="productEditInstructionsModalLabel">
                            <i class="tio-book-outlined me-1"></i> {{ translate('help_products_edit_title') }}
                        </h5>
                    </div>
                    <div class="modal-body help-instructions-body">
                        {!! translate('help_products_edit_page') !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.product.update',[$product['id']])}}" method="post" id="product_form"
                      enctype="multipart/form-data">
                    @csrf
                    @php
                        $languageSetting = \App\Models\BusinessSetting::where('key','language')->first();
                        $language = $languageSetting?->value ?? null;
                        $canonicalLocale = strtolower((string) config('app.locale', 'ar'));
                        $langListForValidation = $language ? json_decode($language, true) : [];
                        if (!is_array($langListForValidation)) {
                            $langListForValidation = [];
                        }
                        $default_name_field_index = 0;
                        foreach ($langListForValidation as $idx => $lc) {
                            if (strtolower((string) $lc) === $canonicalLocale) {
                                $default_name_field_index = (int) $idx;
                                break;
                            }
                        }
                    @endphp
                    @if($language)
                        <ul class="nav nav-tabs mb-4">

                            @foreach(json_decode($language) as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link {{ strtolower((string) $lang) === $canonicalLocale ? 'active' : '' }}" href="#" id="{{$lang}}-link">{{\App\CentralLogics\Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                </li>
                            @endforeach

                        </ul>
                        @foreach(json_decode($language) as $langIdx => $lang)
                                @php
                                $translate = [];
                                if(count($product['translations'])){
                                    foreach($product['translations'] as $t)
                                    {
                                        if($t->locale == $lang && $t->key=="name"){
                                            $translate[$lang]['name'] = $t->value;
                                        }
                                        if($t->locale == $lang && $t->key=="description"){
                                            $translate[$lang]['description'] = $t->value;
                                        }
                                    }
                                }
                                @endphp
                            <div class="card p-4 {{ strtolower((string) $lang) !== $canonicalLocale ? 'd-none' : '' }} lang_form mb-3" id="{{$lang}}-form">
                                <div class="form-group">
                                    <label class="input-label" for="{{$lang}}_name">
                                        {{translate('name')}} ({{strtoupper($lang)}})
                                        @if(strtolower((string) $lang) === $canonicalLocale)
                                            <span class="input-label-secondary text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="text" name="name[]" id="{{$lang}}_name" value="{{ strtolower((string) $lang) === $canonicalLocale ? $product['name'] : ($translate[$lang]['name'] ?? '') }}" class="form-control" placeholder="New Product" >
                                    @if((int) $langIdx === (int) $default_name_field_index)
                                        <span class="error-text" data-error="name.{{ $default_name_field_index }}"></span>
                                    @endif
                                </div>
                                <input type="hidden" name="lang[]" value="{{$lang}}">
                                <div class="form-group pt-4">
                                    <label class="input-label"
                                           for="{{$lang}}_description">{{translate('short')}} {{translate('description')}}  ({{strtoupper($lang)}})</label>
                                    <div id="{{$lang}}_editor" class="min-h-15">{!! \App\CentralLogics\Helpers::sanitizeHtmlForDisplay(strtolower((string) $lang) === $canonicalLocale ? $product['description'] : ($translate[$lang]['description'] ?? '')) !!}</div>
                                    <textarea name="description[]" style="display:none" id="{{$lang}}_hiddenArea"></textarea>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="card p-4" id="{{ $canonicalLocale }}-form">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">
                                    {{translate('name')}} ({{ strtoupper($canonicalLocale) }})
                                    <span class="input-label-secondary text-danger">*</span>
                                </label>
                                <input type="text" name="name[]" value="{{$product['name']}}" class="form-control" placeholder="New Product" required>
                                <span class="error-text" data-error="name.0"></span>
                            </div>
                            <input type="hidden" name="lang[]" value="{{ $canonicalLocale }}">
                            <div class="form-group pt-4">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{translate('short')}} {{translate('description')}} (EN)</label>
                                <div id="editor" class="min-h-15">{!! \App\CentralLogics\Helpers::sanitizeHtmlForDisplay($product['description'] ?? '') !!}</div>
                                <textarea name="description[]" style="display:none" id="hiddenArea"></textarea>
                                <span class="error-text" data-error="description.0"></span>
                            </div>
                        </div>
                    @endif
                    <div id="from_part_2">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{translate('price')}}<span class="input-label-secondary text-danger">*</span></label>
                                    <input type="number" id="product_base_price" value="{{$product['price']}}" min="0" max="100000000" name="price"
                                           class="form-control" step="0.01"
                                           placeholder="Ex : 100"
                                           onkeydown="return !['e','E','+','-'].includes(event.key)"
                                           oninput="
                                                   if (this.value < 0) this.value = 0;
                                                   if (this.value.includes('.')) {this.value = this.value.split('.').map((part, index) => index === 1 ? part.slice(0, 2) : part).join('.');}
                                                   syncUserTypePricePlaceholders(this.value);
                                                   "
                                    >
                                    <span class="error-text" data-error="price"></span>
                                </div>
                            </div>
                            <input type="hidden" name="unit" value="{{ $product['unit'] ?? '' }}">
                            <input type="hidden" name="tax" value="{{ $product['tax'] ?? 0 }}">
                            <input type="hidden" name="tax_type" value="{{ $product['tax_type'] ?? 'percent' }}">
                        </div>

                            @if(isset($userTypes) && $userTypes->isNotEmpty())
                            @php
                                // Read raw DB value (bypasses 'array' cast edge-cases with null)
                                $rawProdVtu          = $product->getAttributes()['visible_to_user_types'] ?? null;
                                $prodEveryoneChecked = ($rawProdVtu === null || $rawProdVtu === '');
                                $prodSavedIds        = $prodEveryoneChecked ? [] : (json_decode($rawProdVtu, true) ?? []);
                            @endphp
                            <div class="col-12 mt-4">
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
                                                   {{ $prodEveryoneChecked ? 'checked' : '' }}
                                                   onchange="toggleProductVisibilityPanel(this)">
                                            <label class="custom-control-label font-weight-bold" for="prod-vis-everyone">
                                                كل الزوار (بدون قيود)
                                            </label>
                                        </div>

                                        {{-- Specific types panel --}}
                                        <div id="prod-vis-panel" style="{{ $prodEveryoneChecked ? 'display:none' : '' }}">
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       id="prod-vis-guest"
                                                       name="visible_to_user_types[]"
                                                       value="0"
                                                       {{ in_array(0, $prodSavedIds) ? 'checked' : '' }}>
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
                                                       value="{{ $ut->id }}"
                                                       {{ in_array($ut->id, $prodSavedIds) ? 'checked' : '' }}>
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

                                <div class="card bg-light p-3">
                                    <h6 class="mb-2 d-flex align-items-center gap-1">
                                        {{ translate('Prices by user type') }}
                                        <i class="tio-info-outlined text-muted fs-14" data-toggle="tooltip" data-placement="top" title="{{ translate('help_product_user_type_pricing') }}"></i>
                                    </h6>
                                    <p class="text-muted small mb-2">{{ translate('Leave blank to use base price for that type') }}</p>
                                    <div class="row">
                                        @foreach($userTypes as $ut)
                                            @php
                                                $typePrices = $product->userTypePrices ?? collect();
                                                $customPrice = $typePrices->where('user_type_id', $ut->id)->first();
                                            @endphp
                                            <div class="col-md-4 col-6 mb-2">
                                                <label class="input-label">{{ $ut->name }}</label>
                                                <input type="number" name="user_type_price[{{ $ut->id }}]" class="form-control form-control-sm user-type-price-input" min="0" step="0.01"
                                                       placeholder="{{ $product['price'] }}"
                                                       value="{{ $customPrice ? $customPrice->price : '' }}"
                                                       data-empty-placeholder="{{ $product['price'] }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="card bg-light p-3 mt-4">
                                    <h6 class="mb-2">{{ translate('Discounts by user type') }}</h6>
                                    <p class="text-muted small mb-2">{{ translate('Leave blank to use base discount for that type') }}</p>
                                    <div class="row">
                                        @foreach($userTypes as $ut)
                                            @php
                                                $typeDiscounts = $product->userTypeDiscounts ?? collect();
                                                $customDiscount = $typeDiscounts->where('user_type_id', $ut->id)->first();
                                            @endphp
                                            <div class="col-md-6 col-lg-4 mb-2">
                                                <label class="input-label">{{ $ut->name }}</label>
                                                <div class="input-group discount-input-group">
                                                    <input type="number" name="user_type_discount[{{ $ut->id }}]" class="form-control discount-amount-input" min="0" step="0.01"
                                                           placeholder="{{ $product['discount'] }}"
                                                           value="{{ $customDiscount ? $customDiscount->discount : '' }}">
                                                    <select name="user_type_discount_type[{{ $ut->id }}]" class="form-control discount-type-select">
                                                        <option value="percent" {{ ($customDiscount ? $customDiscount->discount_type : $product['discount_type']) == 'percent' ? 'selected' : '' }}>{{ translate('percent') }}</option>
                                                        <option value="amount" {{ ($customDiscount ? $customDiscount->discount_type : $product['discount_type']) == 'amount' ? 'selected' : '' }}>{{ translate('amount') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="col-12 mt-4"></div>
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{translate('stock')}} <span class="input-label-secondary text-danger">*</span></label>
                                    <input type="number" min="0" max="100000000" value="{{$product['total_stock']}}" name="total_stock" class="form-control"
                                           placeholder="Ex : 100"
                                           onkeydown="return !['e','E','+','-','.'].includes(event.key)"
                                           oninput="
                                                   if (this.value < 0) this.value = 0;
                                                   "
                                    >
                                    <span class="error-text" data-error="total_stock"></span>
                                </div>
                            </div>
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                    <label class="input-label" for="minimum_stock_alert">{{translate('minimum_stock_alert')}}</label>
                                    <input type="number" min="0" max="100000000" value="{{$product['minimum_stock_alert'] ?? ''}}" name="minimum_stock_alert"
                                           id="minimum_stock_alert" class="form-control"
                                           placeholder="{{ translate('Ex : 5') }}"
                                           onkeydown="return !['e','E','+','-','.'].includes(event.key)">
                                    <small class="text-muted">{{translate('minimum_stock_alert_hint')}}</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-6">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlSelect1">{{translate('category')}} <span class="input-label-secondary text-danger">*</span></label>
                                    <select name="category_id" id="category-id" class="form-control js-select2-custom"
                                            onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-categories')">
                                        @foreach($categories as $category)
                                            <option
                                                value="{{$category['id']}}" {{ (count($product_category) > 0 && $category->id == ($product_category[0]->id ?? null)) ? 'selected' : '' }}>{{$category['name']}}</option>
                                        @endforeach
                                    </select>
                                    <span class="error-text" data-error="category_id"></span>
                                </div>
                            </div>
                            <div class="col-md-6 col-6">
                                <div class="form-group">
                                    <label class="input-label"
                                           for="exampleFormControlSelect1">{{translate('sub_category')}}<span
                                            class="input-label-secondary"></span></label>
                                    <select name="sub_category_id" id="sub-categories"
                                            data-id="{{ count($product_category) >= 2 ? ($product_category[1]->id ?? '') : '' }}"
                                            class="form-control js-select2-custom"
                                            onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-sub-categories')">

                                    </select>
                                    <span class="error-text" data-error="sub_category_id"></span>
                                </div>
                            </div>
                        </div>

                        @include('admin-views.product.partials._tags-input')
                        @include('admin-views.product.partials._related-products')

                        <div class="row"
                             style="border: 1px solid #80808045; border-radius: 10px;padding-top: 10px;margin: 1px">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="input-label d-flex align-items-center gap-1"
                                           for="exampleFormControlSelect1">{{translate('attribute')}}<span
                                            class="input-label-secondary"></span>
                                        <i class="tio-info-outlined text-muted fs-14" data-toggle="tooltip" data-placement="top" title="{{ translate('help_product_attributes') }}"></i>
                                    </label>
                                    <select name="attribute_id[]" id="choice_attributes"
                                            class="form-control js-select2-custom"
                                            multiple="multiple">
                                        @foreach(\App\Models\Attribute::orderBy('name')->get() as $attribute)
                                            <option
                                                value="{{$attribute['id']}}" {{in_array($attribute->id, (array) (json_decode($product['attributes'] ?? '[]', true) ?? []))?'selected':''}}>{{$attribute['name']}}</option>
                                        @endforeach
                                    </select>
                                    <span class="error-text" data-error="attribute_id"></span>
                                </div>
                            </div>
                            <div class="col-md-12 mt-2 mb-2">
                                <div class="customer_choice_options" id="customer_choice_options">
                                    @include('admin-views.product.partials._choices',['choice_no'=>json_decode($product['attributes'] ?? '[]') ?? [],'choice_options'=>json_decode($product['choice_options'] ?? '{}', true) ?? []])
                                </div>
                            </div>
                            <div class="col-md-12 mt-2 mb-2">
                                <div class="variant_combination" id="variant_combination">
                                    @include('admin-views.product.partials._edit-combinations',['combinations'=>json_decode($product['variations'] ?? '[]', true) ?? []])
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label>{{translate('product')}} {{translate('image')}}</label><small
                                class="color-red">* ( {{translate('ratio')}} 1:1 )</small>
                            <div>
                                <div class="row mb-3">
                                    @foreach((array) (json_decode($product['image'] ?? '[]', true) ?? []) as $img)
                                        <div class="col-3">
                                            <img class="w-100 h-200px"
                                                 src="{{Helpers::onErrorImage(
                                                $img,
                                                asset('storage/product').'/' . $img,
                                                asset('assets/admin/img/160x160/img2.jpg') ,
                                                'product/')}}" alt="{{ translate('product') }}">
                                            <a href="{{route('admin.product.remove-image',[$product['id'],$img])}}"
                                               class="btn btn-danger btn-block btn-sm custom-class">{{translate('Remove')}}</a>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="row" id="coba"></div>
                                <p class="fs-14 text-muted mb-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                                <span class="error-text justify-content-start" data-error="images"></span>
                            </div>
                        </div>
                    </div>
                    {{-- قسم قالب التصميم (اختياري) --}}
                    @php
                        $productTmpl    = \App\Models\DesignTemplate::where('product_id', $product->id)->first();
                        $editDesignTmpl = \App\Models\DesignTemplate::active()->orderBy('position')->orderBy('id','desc')->get(['id','name','thumbnail','category_id','product_id']);
                        // الـ selectedId هو القالب المصدر — نبحث عن القالب بنفس الـ canvas_json إن وجد، أو null
                        $editSelectedId = null;
                    @endphp
                    <div class="card mb-3 mt-3" id="design-tmpl-card">
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
                                               {{ $productTmpl ? 'checked' : '' }}
                                               onchange="toggleDesignTmplEnabled(this.checked)">
                                        <span class="switcher_control"></span>
                                    </label>
                                    <i class="fa fa-chevron-down text-muted" id="design-tmpl-chevron"
                                       style="{{ $productTmpl ? 'transform:rotate(180deg)' : '' }}"></i>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1">
                                {{ translate('design_template_hint') ?: 'اختر قالباً يظهر للعميل عند الضغط على "صمّم منتجك" في صفحة هذا المنتج.' }}
                                @if($productTmpl)
                                    &nbsp;<strong class="text-success">✓ {{ translate('current_template') ?: 'القالب الحالي' }}: {{ $productTmpl->name }}</strong>
                                @endif
                            </small>
                        </div>
                        <div class="card-body" id="design-tmpl-body" style="{{ $productTmpl ? 'display:block' : 'display:none' }}">
                            <input type="hidden" name="tmpl_selected_id" id="tmpl-selected-id" value="">
                            @include('admin-views.design-template._template_picker', [
                                'designTemplates' => $editDesignTmpl,
                                'selectedId'      => $editSelectedId,
                                'inputId'         => 'tmpl-selected-id',
                            ])
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
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

        /* ── Design Template section toggle (edit page) ── */
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

        $(".lang_link").click(function(e){
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            let defaultLang = @json($canonicalLocale ?? 'ar');
            $("#"+lang+"-form").removeClass('d-none');
            if(lang == defaultLang)
            {
                $("#from_part_2").removeClass('d-none');
            }
            else
            {
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
                groupClassName: 'col-3',
                maxFileSize: maxFileSize,
                placeholderImage: {
                    image: '{{asset('assets/admin/img/400x400/img2.jpg')}}',
                    width: '100%'
                },
                allowedExt:        'png|jpg|jpeg|gif|webp',
                dropFileLabel: "Drop Here",
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
                    toastr.error('{{ translate("File size too big") }}', {
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

        $(document).ready(function () {
            setTimeout(function () {
                let category = $("#category-id").val();
                let sub_category = '{{ count($product_category) >= 2 ? ($product_category[1]->id ?? '') : '' }}';
                let sub_sub_category = '{{ count($product_category) >= 3 ? ($product_category[2]->id ?? '') : '' }}';
                getRequest('{{url('/')}}/admin/product/get-categories?parent_id=' + category + '&&sub_category=' + sub_category, 'sub-categories');
                getRequest('{{url('/')}}/admin/product/get-categories?parent_id=' + sub_category + '&&sub_category=' + sub_sub_category, 'sub-sub-categories');
            }, 1000)
        });

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

        function add_more_customer_choice_option(i, name) {
            let n = name.split(' ').join('');
            $('#customer_choice_options').append('<div class="row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i + '"><input type="text" class="form-control" name="choice[]" value="' + n + '" placeholder="Choice Title" readonly></div><div class="col-lg-9"><input type="text" class="form-control" name="choice_options_' + i + '[]" placeholder="Enter choice values" data-role="tagsinput" onchange="combination_update()"></div></div>');
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

        function update_qty() {
            var total_qty = 0;
            var qty_elements = $('input[name^="stock_"]');
            for(var i=0; i<qty_elements.length; i++)
            {
                total_qty += parseInt(qty_elements.eq(i).val(), 10) || 0;
            }
            if(qty_elements.length > 0)
            {
                $('input[name="total_stock"]').attr("readonly", true);
                $('input[name="total_stock"]').val(total_qty);
            }
            else{
                $('input[name="total_stock"]').attr("readonly", false);
            }
        }
    </script>
@endpush
