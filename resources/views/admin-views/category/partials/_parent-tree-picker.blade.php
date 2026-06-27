{{--
  شجرة قابلة للنقر لاختيار التصنيف الأب
  المتغيرات: $parentCategoryTree (array), $selectedParentId (int|null), $inputName (string)
--}}
@php
    $inputName = $inputName ?? 'parent_id';
    $selectedParentId = $selectedParentId ?? null;
@endphp

<div class="category-parent-tree-picker" data-input-name="{{ $inputName }}">
    <input type="hidden" name="{{ $inputName }}" value="{{ $selectedParentId ?? '' }}" class="category-parent-id-input" {{ count($parentCategoryTree ?? []) > 0 ? 'required' : '' }}>
    <div class="category-tree-container">
        @forelse($parentCategoryTree ?? [] as $node)
            @include('admin-views.category.partials._parent-tree-node', [
                'node' => $node,
                'depth' => 0,
                'selectedId' => $selectedParentId,
            ])
        @empty
            <p class="text-muted mb-0">{{ translate('No data to show') }}</p>
        @endforelse
    </div>
    <p class="small text-muted mt-2 mb-0 category-tree-hint">
        <i class="tio-info-outlined"></i>
        {{ translate('category_parent_tree_hint') }}
    </p>
</div>
