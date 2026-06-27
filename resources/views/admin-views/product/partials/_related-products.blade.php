{{-- منتجات ذات صلة --}}
@php
    $productsForRelated = $productsForRelated ?? collect();
    $relatedIds = isset($product) ? $product->relatedProducts->pluck('id')->toArray() : [];
@endphp
<div class="card mb-3">
    <div class="card-header bg-light">
        <h6 class="mb-0 fw-semibold">
            <i class="tio-link me-2"></i>{{ translate('related_products') }}
        </h6>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label class="input-label">{{ translate('related_products') }}</label>
            <select name="related_product_ids[]" class="form-control js-select2-custom" multiple="multiple" data-placeholder="{{ translate('Search products') ?: 'ابحث عن منتجات' }}">
                @if(isset($product) && $product->relatedProducts->isNotEmpty())
                    @foreach($product->relatedProducts as $rp)
                        <option value="{{ $rp->id }}" selected>{{ $rp->name }}</option>
                    @endforeach
                @endif
                @foreach($productsForRelated as $p)
                    @if((!isset($product) || $p->id != $product->id) && !in_array($p->id, $relatedIds))
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endif
                @endforeach
            </select>
            <small class="text-muted">{{ translate('Select related products to show together') ?: 'اختر المنتجات ذات الصلة لعرضها معاً' }}</small>
        </div>
    </div>
</div>
