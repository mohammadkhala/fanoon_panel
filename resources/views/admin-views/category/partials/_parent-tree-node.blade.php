@php
    $nodeId = $node['id'] ?? 0;
    $nodeName = $node['name'] ?? '';
    $children = $node['children'] ?? [];
    $hasChildren = count($children) > 0;
    $isSelected = ($selectedId ?? null) == $nodeId;
@endphp
<div class="category-tree-node" data-id="{{ $nodeId }}" data-depth="{{ $depth ?? 0 }}">
    <div class="category-tree-item d-flex align-items-center gap-2 py-2 px-3 rounded cursor-pointer {{ $isSelected ? 'bg-primary text-white' : 'hover-bg-light' }}"
         style="padding-inline-start: {{ ($depth ?? 0) * 18 + 10 }}px;"
         role="button"
         tabindex="0">
        @if($hasChildren)
            <span class="category-tree-toggle text-muted" aria-label="expand/collapse">
                <i class="tio-chevron-right category-tree-icon-closed"></i>
                <i class="tio-chevron-down category-tree-icon-open d-none"></i>
            </span>
        @else
            <span class="category-tree-toggle-placeholder" style="width: 1.25rem; display: inline-block;"></span>
        @endif
        <span class="category-tree-label flex-grow-1">{{ $nodeName }}</span>
        @if($isSelected)
            <i class="tio-checkmark-circle text-success"></i>
        @endif
    </div>
    @if($hasChildren)
        <div class="category-tree-children">
            @foreach($children as $child)
                @include('admin-views.category.partials._parent-tree-node', [
                    'node' => $child,
                    'depth' => ($depth ?? 0) + 1,
                    'selectedId' => $selectedId ?? null,
                ])
            @endforeach
        </div>
    @endif
</div>
