{{-- أنماط شجرة اختيار التصنيف الأب — لمسة فنية --}}
<style>
/* الحاوية الرئيسية */
.category-tree-container {
    max-height: 280px;
    overflow-y: auto;
    padding: 0.75rem;
    background: linear-gradient(135deg, #fafbfc 0%, #f1f3f5 100%);
    border: 1px solid rgba(0,0,0,.08);
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06), inset 0 1px 0 rgba(255,255,255,.8);
}
/* شريط التمرير المخصص */
.category-tree-container::-webkit-scrollbar { width: 8px; }
.category-tree-container::-webkit-scrollbar-track {
    background: rgba(0,0,0,.04);
    border-radius: 4px;
}
.category-tree-container::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,.2);
    border-radius: 4px;
}
.category-tree-container::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,.3); }
/* العناصر */
.category-tree-item {
    transition: background .2s ease, color .2s ease, transform .05s ease;
    border-radius: 8px;
    margin-bottom: 2px;
}
.category-tree-item:hover { transform: translateX(2px); }
.category-tree-item:focus { outline: none; box-shadow: 0 0 0 2px rgba(var(--primary-clr-rgb, 236, 34, 39), .25); }
.category-tree-item.hover-bg-light:hover {
    background: rgba(var(--primary-clr-rgb, 236, 34, 39), .08) !important;
    color: var(--primary-clr, #EC2227) !important;
}
.category-tree-item.hover-bg-light:hover .category-tree-toggle,
.category-tree-item.hover-bg-light:hover .text-muted { color: var(--primary-clr, #EC2227) !important; }
/* الحالة المحددة */
.category-tree-item.bg-primary {
    background: linear-gradient(135deg, var(--primary-clr, #EC2227) 0%, #d41e22 100%) !important;
    color: #fff !important;
    box-shadow: 0 2px 6px rgba(236, 34, 39, .35);
}
.category-tree-item.bg-primary .text-muted,
.category-tree-item.bg-primary .category-tree-toggle { color: rgba(255,255,255,.95) !important; }
.category-tree-item.bg-primary .category-tree-label { font-weight: 600; }
/* السهم */
.category-tree-toggle {
    cursor: pointer;
    user-select: none;
    transition: transform .25s ease;
    width: 1.25rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.category-tree-placeholder { width: 1.25rem; display: inline-block; }
.category-tree-item.bg-primary .tio-checkmark-circle { color: rgba(255,255,255,.95) !important; }
/* إظهار/إخفاء الأبناء */
.category-tree-node.collapsed .category-tree-children { display: none !important; }
.category-tree-node.collapsed .category-tree-icon-closed { display: inline !important; }
.category-tree-node.collapsed .category-tree-icon-open { display: none !important; }
.category-tree-node:not(.collapsed) .category-tree-icon-closed { display: inline !important; }
.category-tree-node:not(.collapsed) .category-tree-icon-open { display: none !important; }
/* خط ربط للأبناء (اختياري) */
.category-tree-children { border-inline-start: 2px solid rgba(0,0,0,.08); margin-inline-start: 0.5rem; }
/* تلميح أسفل الشجرة */
.category-parent-tree-picker .small.text-muted { font-size: 0.875rem; opacity: .9; }
</style>
