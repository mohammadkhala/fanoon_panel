@extends('layouts.admin.app')

@section('title', 'قوالب التصميم حسب التصنيف')

@push('css_or_js')
<style>
/* ── Page ── */
.bc-wrap { padding: 0 }

/* ── Stats ── */
.bc-stats { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:22px }
.bc-stat { background:#fff; border:1px solid #e7eaf3; border-radius:10px; padding:12px 20px; display:flex; align-items:center; gap:10px; flex:1; min-width:140px }
.bc-stat-icon { width:40px; height:40px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0 }
.bc-stat-num { font-size:22px; font-weight:700; line-height:1; color:#1e2d3d }
.bc-stat-lbl { font-size:11px; color:#8c98a4; margin-top:2px }

/* ── Category section ── */
.bc-cat-section { margin-bottom:30px }
.bc-cat-header {
    display:flex; align-items:center; gap:10px;
    background:#fff; border:1px solid #e7eaf3;
    border-radius:10px 10px 0 0; padding:12px 18px;
    border-bottom:3px solid var(--primary-clr,#EC2227);
    position:sticky; top:60px; z-index:10;
}
.bc-cat-header .bc-cat-icon { color:var(--primary-clr,#EC2227); font-size:16px }
.bc-cat-header .bc-cat-name { font-size:15px; font-weight:700; color:#1e2d3d; flex:1 }
.bc-cat-header .bc-cat-count {
    background:var(--primary-clr,#EC2227); color:#fff;
    border-radius:20px; padding:2px 10px; font-size:12px; font-weight:600
}

/* ── Templates grid ── */
.bc-tmpl-grid {
    background:#f8f9fa; border:1px solid #e7eaf3; border-top:none;
    border-radius:0 0 10px 10px; padding:16px;
    display:grid; grid-template-columns:repeat(auto-fill, minmax(160px, 1fr)); gap:14px;
}

/* ── Template card ── */
.bc-tmpl-card {
    background:#fff; border:1px solid #e7eaf3; border-radius:10px;
    overflow:hidden; transition:box-shadow .2s, transform .15s;
    display:flex; flex-direction:column;
}
.bc-tmpl-card:hover { box-shadow:0 4px 18px rgba(0,0,0,.1); transform:translateY(-2px) }

.bc-tmpl-thumb {
    width:100%; aspect-ratio:1; background:#f0f4f8;
    display:flex; align-items:center; justify-content:center;
    overflow:hidden; position:relative;
}
.bc-tmpl-thumb img { width:100%; height:100%; object-fit:cover; display:block }
.bc-tmpl-thumb .bc-ph { color:#c8d3dd; font-size:32px }
.bc-tmpl-thumb .bc-status-dot {
    position:absolute; top:6px; right:6px;
    width:9px; height:9px; border-radius:50%;
    border:2px solid #fff;
}
.bc-tmpl-thumb .bc-status-dot.on  { background:#10b46a }
.bc-tmpl-thumb .bc-status-dot.off { background:#d5dae3 }

.bc-tmpl-body { padding:9px 10px; flex:1; display:flex; flex-direction:column; gap:4px }
.bc-tmpl-name { font-size:12px; font-weight:600; color:#1e2d3d; white-space:nowrap; overflow:hidden; text-overflow:ellipsis }
.bc-tmpl-prod { font-size:10px; color:#8c98a4; white-space:nowrap; overflow:hidden; text-overflow:ellipsis }
.bc-tmpl-size { font-size:10px; color:#b9c3ce }

.bc-tmpl-foot {
    display:flex; border-top:1px solid #f0f3f7;
}
.bc-tmpl-foot a {
    flex:1; padding:6px 0; text-align:center;
    font-size:12px; color:#8c98a4; transition:background .15s, color .15s;
    border-left:1px solid #f0f3f7;
    display:flex; align-items:center; justify-content:center;
}
.bc-tmpl-foot a:last-child { border-left:none }
.bc-tmpl-foot a:hover { background:#f8f9fa }
.bc-tmpl-foot a.edit:hover  { color:#0d6efd }
.bc-tmpl-foot a.del:hover   { color:#EC2227 }

/* ── Empty ── */
.bc-empty { text-align:center; padding:48px 20px; color:#8c98a4 }
.bc-empty i { font-size:48px; margin-bottom:12px; display:block; opacity:.35 }

/* ── Switcher small ── */
.switcher-xs .switcher_control { width:30px; height:16px }
.switcher-xs .switcher_control::after { width:12px; height:12px; top:2px; left:2px }
.switcher-xs .switcher_input:checked + .switcher_control { background:var(--primary-clr,#EC2227) }
.switcher-xs .switcher_input:checked + .switcher_control::after { left:calc(100% - 14px) }

/* ── Responsive ── */
@media(max-width:576px){
    .bc-tmpl-grid { grid-template-columns:repeat(auto-fill,minmax(130px,1fr)); gap:10px }
}
</style>
@endpush

@section('content')
<div class="content container-fluid bc-wrap">

    {{-- ── Page Header ── --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h2 class="mb-0 d-flex align-items-center gap-2" style="font-size:20px">
                <i class="tio tio-grid" style="color:var(--primary-clr,#EC2227)"></i>
                قوالب التصميم حسب التصنيف
            </h2>
            @if($filterProduct)
                <div class="mt-1 d-flex align-items-center gap-2">
                    <span class="badge badge-soft-info" style="font-size:12px">
                        <i class="tio tio-cube me-1"></i> {{ $filterProduct->name }}
                    </span>
                    <a href="{{ route('admin.design-template.by-category') }}" class="text-muted" style="font-size:11px">
                        <i class="tio tio-clear"></i> إلغاء الفلتر
                    </a>
                </div>
            @endif
        </div>
        <a href="{{ route('admin.design-template.add-new') }}{{ $productId ? '?product_id='.$productId : '' }}"
           class="btn btn-primary btn-sm">
            <i class="tio tio-add me-1"></i> إضافة قالب جديد
        </a>
    </div>

    {{-- ── Stats ── --}}
    @php
        $totalTemplates = $grouped->flatten()->count();
        $totalActive    = $grouped->flatten()->where('status', 1)->count();
        $totalCats      = $grouped->count();
    @endphp
    <div class="bc-stats">
        <div class="bc-stat">
            <div class="bc-stat-icon" style="background:#fff5f5">
                <i class="tio tio-palette" style="color:#EC2227"></i>
            </div>
            <div>
                <div class="bc-stat-num">{{ $totalTemplates }}</div>
                <div class="bc-stat-lbl">إجمالي القوالب</div>
            </div>
        </div>
        <div class="bc-stat">
            <div class="bc-stat-icon" style="background:#f0fdf4">
                <i class="tio tio-checkmark-circle" style="color:#10b46a"></i>
            </div>
            <div>
                <div class="bc-stat-num">{{ $totalActive }}</div>
                <div class="bc-stat-lbl">قوالب مفعّلة</div>
            </div>
        </div>
        <div class="bc-stat">
            <div class="bc-stat-icon" style="background:#eff6ff">
                <i class="tio tio-folder-opened" style="color:#3b82f6"></i>
            </div>
            <div>
                <div class="bc-stat-num">{{ $totalCats }}</div>
                <div class="bc-stat-lbl">تصنيف</div>
            </div>
        </div>
        <div class="bc-stat">
            <div class="bc-stat-icon" style="background:#fefce8">
                <i class="tio tio-blocked" style="color:#f59e0b"></i>
            </div>
            <div>
                <div class="bc-stat-num">{{ $totalTemplates - $totalActive }}</div>
                <div class="bc-stat-lbl">قوالب معطّلة</div>
            </div>
        </div>
    </div>

    {{-- ── Filter (مطابق لأسلوب قائمة المنتجات) ── --}}
    <div class="card mb-3">
        <div class="p-3">
            <form action="{{ route('admin.design-template.by-category') }}" method="GET" class="filter-form" id="template-filter-form">
                <div class="bg-light rounded p-2">
                    {{-- صف البحث --}}
                    <div class="row g-2 mb-2">
                        <div class="col-12">
                            <input type="search" name="search" class="form-control"
                                   placeholder="بحث بالاسم..." aria-label="بحث"
                                   value="{{ $search }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="row align-items-end g-2">
                        {{-- التصنيف --}}
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label small mb-1">التصنيف</label>
                            <select class="form-control form-control-sm" name="category_id">
                                <option value="">كل التصنيفات</option>
                                @foreach($mainCategories as $cat)
                                    <option value="{{ $cat->id }}" {{ (string)($categoryId ?? '') === (string)$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- المنتج --}}
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label small mb-1">المنتج</label>
                            <select class="form-control form-control-sm" name="product_id">
                                <option value="">كل المنتجات</option>
                                @foreach($allProducts as $p)
                                    <option value="{{ $p->id }}" {{ (string)($productId ?? '') === (string)$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- الحالة --}}
                        <div class="col-6 col-md-4 col-lg-3">
                            <label class="form-label small mb-1">الحالة</label>
                            <select class="form-control form-control-sm" name="status">
                                <option value=""  {{ ($status ?? '') === ''  ? 'selected' : '' }}>كل الحالات</option>
                                <option value="1" {{ ($status ?? '') === '1' ? 'selected' : '' }}>مفعّل</option>
                                <option value="0" {{ ($status ?? '') === '0' ? 'selected' : '' }}>معطّل</option>
                            </select>
                        </div>

                        {{-- الأزرار --}}
                        <div class="col-6 col-md-4 col-lg-3 d-flex gap-2 align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                <i class="tio tio-checkmark-circle-outlined me-1"></i> عرض البيانات
                            </button>
                            <a href="{{ route('admin.design-template.by-category') }}" class="btn btn-soft-secondary btn-sm flex-grow-1 text-center">مسح</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Categories ── --}}
    @if($grouped->isEmpty())
        <div class="bc-empty">
            <i class="tio tio-inbox"></i>
            <p class="mb-0">لا توجد قوالب {{ $search ? 'تطابق البحث' : '' }}</p>
        </div>
    @else
        @foreach($grouped as $catName => $templates)
        <div class="bc-cat-section">

            {{-- Category Header --}}
            <div class="bc-cat-header">
                <i class="tio tio-folder bc-cat-icon"></i>
                <span class="bc-cat-name">{{ $catName }}</span>
                <span class="bc-cat-count">{{ $templates->count() }} قالب</span>
            </div>

            {{-- Templates Grid --}}
            <div class="bc-tmpl-grid">
                @foreach($templates as $tmpl)
                <div class="bc-tmpl-card">

                    {{-- Thumbnail --}}
                    <div class="bc-tmpl-thumb">
                        @if($tmpl->thumbnail_fullpath)
                            <img src="{{ $tmpl->thumbnail_fullpath }}" alt="{{ $tmpl->name }}">
                        @else
                            <i class="tio tio-palette bc-ph"></i>
                        @endif
                        <span class="bc-status-dot {{ $tmpl->status ? 'on' : 'off' }}"
                              title="{{ $tmpl->status ? 'مفعّل' : 'معطّل' }}"></span>
                    </div>

                    {{-- Body --}}
                    <div class="bc-tmpl-body">
                        <div class="bc-tmpl-name" title="{{ $tmpl->name }}">{{ $tmpl->name }}</div>
                        @if($tmpl->product)
                            <div class="bc-tmpl-prod">
                                <i class="tio tio-cube" style="font-size:9px"></i>
                                {{ $tmpl->product->name }}
                            </div>
                        @endif
                        <div class="bc-tmpl-size">
                            {{ $tmpl->canvas_width }}×{{ $tmpl->canvas_height }}
                        </div>

                        {{-- Status toggle --}}
                        <div class="mt-1">
                            <label class="switcher switcher-xs mb-0" title="{{ $tmpl->status ? 'إيقاف' : 'تفعيل' }}">
                                <input type="checkbox" class="switcher_input change-status"
                                       {{ $tmpl->status ? 'checked' : '' }}
                                       data-route="{{ route('admin.design-template.status', [$tmpl->id, $tmpl->status ? 0 : 1]) }}">
                                <span class="switcher_control"></span>
                            </label>
                        </div>
                    </div>

                    {{-- Footer actions --}}
                    <div class="bc-tmpl-foot">
                        <a href="{{ route('admin.design-template.edit', $tmpl->id) }}"
                           class="edit" title="تعديل">
                            <i class="tio tio-edit"></i>
                        </a>
                        <a href="javascript:" class="del form-alert"
                           data-id="del-bc-{{ $tmpl->id }}"
                           data-message="حذف قالب «{{ $tmpl->name }}»؟"
                           title="حذف">
                            <i class="tio tio-delete"></i>
                        </a>
                    </div>

                    <form id="del-bc-{{ $tmpl->id }}"
                          action="{{ route('admin.design-template.delete', $tmpl->id) }}"
                          method="POST" style="display:none">
                        @csrf @method('DELETE')
                    </form>
                </div>
                @endforeach
            </div>

        </div>
        @endforeach
    @endif

</div>
@endsection

@push('script_2')
<script>
// Status toggle
document.querySelectorAll('.change-status').forEach(el => {
    el.addEventListener('change', function () {
        fetch(this.dataset.route)
            .then(r => r.json())
            .then(d => {
                if (!d.success) this.checked = !this.checked;
            })
            .catch(() => { this.checked = !this.checked; });
    });
});
</script>
@endpush
