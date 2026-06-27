{{-- وسوم المنتجات --}}
<div class="card mb-3">
    <div class="card-header bg-light">
        <h6 class="mb-0 fw-semibold">
            <i class="tio-label me-2"></i>{{ translate('product_tags') }}
        </h6>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label class="input-label">{{ translate('product_tags') }}</label>
            <select name="tag_ids[]" class="form-control js-select2-custom" multiple="multiple" data-placeholder="{{ translate('product_tags_hint') }}">
                @foreach(\App\Models\Tag::orderBy('sort_order')->orderBy('name')->get() as $tag)
                    <option value="{{ $tag->id }}" {{ isset($selectedTagIds) && in_array($tag->id, $selectedTagIds) ? 'selected' : '' }}>{{ $tag->name }}</option>
                @endforeach
            </select>
            <small class="text-muted">{{ translate('product_tags_hint') }}</small>
        </div>
    </div>
</div>
