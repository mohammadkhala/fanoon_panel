@extends('layouts.admin.app')

@section('title', translate('design_templates') ?: 'قوالب التصميم')

@push('css_or_js')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
<style>
/* ═══ Editor Variables ═══ */
:root{
  --ed-green:#10b46a;--ed-accent:#a78bfa;
  --ed-bg:#141817;--ed-cbg:#1d2220;
  --ed-toolbar:#0e1210;--ed-sidebar:#161b19;--ed-panel:#1a1f1d;
  --ed-border:#2a3330;--ed-border2:#22292699;
  --ed-text:#e2ede6;--ed-text2:#7a9282;--ed-text3:#4a6055;
  --ed-inp:#202825;--ed-inp-h:#252e2a;
  --ed-danger:#e05c5c;--ed-warn:#f59e0b;
}
/* ═══ Root ═══ */
.de-root{background:var(--ed-bg);border-radius:14px;overflow:hidden;border:1px solid var(--ed-border);display:flex;flex-direction:column;font-family:'Cairo',sans-serif}
/* ═══ Fullscreen mode ═══ */
.de-root:fullscreen{width:100vw;height:100vh;border-radius:0;border:none}
.de-root:fullscreen .de-body{flex:1;min-height:0}
.de-root:-webkit-full-screen{width:100vw;height:100vh;border-radius:0;border:none}
/* ═══ Topbar ═══ */
.de-topbar{background:var(--ed-toolbar);border-bottom:1px solid var(--ed-border);display:flex;align-items:center;gap:3px;padding:6px 10px;overflow-x:auto;flex-shrink:0;min-height:46px}
.de-topbar::-webkit-scrollbar{height:3px}.de-topbar::-webkit-scrollbar-track{background:transparent}.de-topbar::-webkit-scrollbar-thumb{background:var(--ed-border)}
/* ═══ Buttons ═══ */
.de-btn{display:inline-flex;align-items:center;gap:4px;padding:5px 9px;background:transparent;border:1px solid transparent;border-radius:7px;color:var(--ed-text2);font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;transition:all .15s;font-family:inherit;line-height:1.3}
.de-btn:hover{background:var(--ed-inp-h);border-color:var(--ed-border);color:var(--ed-text)}
.de-btn.active{background:rgba(16,180,106,.15);border-color:var(--ed-green);color:var(--ed-green)}
.de-btn:disabled{opacity:.35;cursor:not-allowed;pointer-events:none}
.de-btn.de-danger{color:var(--ed-danger)}.de-btn.de-danger:hover{background:rgba(224,92,92,.12);border-color:var(--ed-danger)}
.de-btn.de-accent{color:var(--ed-accent)}.de-btn.de-accent:hover{background:rgba(167,139,250,.12);border-color:var(--ed-accent)}
.de-btn-sm{padding:3px 7px;font-size:11px}
/* ═══ Separator ═══ */
.de-sep{width:1px;height:22px;background:var(--ed-border);margin:0 3px;flex-shrink:0}
/* ═══ Select ═══ */
.de-select{background:var(--ed-inp);border:1px solid var(--ed-border);border-radius:7px;color:var(--ed-text);font-size:12px;padding:4px 8px;cursor:pointer;font-family:inherit}
.de-select:focus{outline:none;border-color:var(--ed-green)}
/* ═══ Body (3-panel) ═══ */
.de-body{display:flex;height:720px;min-height:550px}
/* ═══ Left Sidebar ═══ */
.de-sidebar{width:176px;flex-shrink:0;background:var(--ed-sidebar);border-inline-end:1px solid var(--ed-border);display:flex;flex-direction:column;overflow:hidden}
.de-sb-tabs{display:flex;border-bottom:1px solid var(--ed-border);flex-shrink:0}
.de-sb-tab{flex:1;padding:8px 3px;text-align:center;font-size:10px;color:var(--ed-text3);cursor:pointer;border-bottom:2px solid transparent;transition:all .15s;display:flex;flex-direction:column;align-items:center;gap:2px}
.de-sb-tab i{font-size:15px}
.de-sb-tab.active{color:var(--ed-green);border-bottom-color:var(--ed-green)}
.de-sb-panes{flex:1;overflow-y:auto}
.de-sb-panes::-webkit-scrollbar{width:3px}.de-sb-panes::-webkit-scrollbar-thumb{background:var(--ed-border)}
.de-sb-pane{display:none;padding:10px 8px}
.de-sb-pane.active{display:block}
.de-sb-sec{font-size:9px;font-weight:700;color:var(--ed-text3);text-transform:uppercase;letter-spacing:.6px;margin:10px 0 6px;padding:0 2px}
.de-sb-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:4px;margin-bottom:6px}
.de-sb-item{background:var(--ed-inp);border:1px solid var(--ed-border);border-radius:8px;padding:8px 2px;text-align:center;cursor:pointer;transition:all .15s;color:var(--ed-text2);font-size:9px;display:flex;flex-direction:column;align-items:center;gap:3px;line-height:1.2}
.de-sb-item i{font-size:17px}
.de-sb-item:hover{background:rgba(16,180,106,.12);border-color:var(--ed-green);color:var(--ed-green)}
.de-sb-text-btn{background:var(--ed-inp);border:1px solid var(--ed-border);border-radius:8px;padding:9px 10px;margin-bottom:5px;cursor:pointer;width:100%;text-align:start;transition:all .15s;color:var(--ed-text);font-family:'Cairo',sans-serif;display:block}
.de-sb-text-btn:hover{border-color:var(--ed-green);background:rgba(16,180,106,.08);color:var(--ed-green)}
.de-sb-text-btn .sb-t-label{font-size:10px;color:var(--ed-text3);display:block;margin-top:1px}
.de-sb-upload{background:rgba(16,180,106,.1);border:1px dashed var(--ed-green);border-radius:8px;padding:14px;text-align:center;cursor:pointer;color:var(--ed-green);font-size:12px;transition:all .15s;margin-bottom:8px;display:block}
.de-sb-upload:hover{background:rgba(16,180,106,.2)}
.de-sb-upload i{font-size:22px;display:block;margin-bottom:4px}
.de-sb-swatches{display:flex;flex-wrap:wrap;gap:5px;padding:4px 0}
.de-sb-swatch{width:26px;height:26px;border-radius:50%;cursor:pointer;border:2px solid rgba(255,255,255,.1);transition:transform .1s,border-color .1s;flex-shrink:0}
.de-sb-swatch:hover{transform:scale(1.2);border-color:var(--ed-green)}
/* ═══ Canvas Area ═══ */
.de-canvas-area{flex:1;min-width:0;background:var(--ed-cbg);overflow:auto;padding:24px;display:flex;align-items:flex-start;justify-content:center;position:relative}
.de-canvas-area::-webkit-scrollbar{width:6px;height:6px}.de-canvas-area::-webkit-scrollbar-thumb{background:var(--ed-border);border-radius:3px}
#de-canvas-wrap{transform-origin:top center;flex-shrink:0;box-shadow:0 6px 32px rgba(0,0,0,.5);position:relative}
.de-grid-overlay{position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;opacity:.3;display:none}
.de-grid-overlay.active{display:block}
/* ═══ Right Panel ═══ */
.de-panel{width:280px;flex-shrink:0;background:var(--ed-panel);border-inline-start:1px solid var(--ed-border);overflow-y:auto;font-size:12px}
.de-panel::-webkit-scrollbar{width:3px}.de-panel::-webkit-scrollbar-thumb{background:var(--ed-border)}
.de-ps{border-bottom:1px solid var(--ed-border2);padding:10px 12px}
.de-pt{font-size:10px;font-weight:700;text-transform:uppercase;color:var(--ed-green);letter-spacing:.6px;margin-bottom:9px;display:flex;align-items:center;gap:4px}
.de-pt i{font-size:11px;opacity:.7}
.de-row{display:flex;align-items:center;gap:5px;margin-bottom:6px}
.de-lbl{font-size:11px;color:var(--ed-text2);min-width:42px;flex-shrink:0;text-align:end}
.de-inp{flex:1;min-width:0;background:var(--ed-inp);border:1px solid var(--ed-border);border-radius:5px;color:var(--ed-text);font-size:12px;padding:4px 7px;font-family:inherit}
.de-inp:focus{outline:none;border-color:var(--ed-green)}
.de-inp[type=color]{width:30px;height:26px;padding:1px 2px;cursor:pointer;flex:none;border-radius:4px}
.de-inp[type=number]{-moz-appearance:textfield}.de-inp[type=number]::-webkit-outer-spin-button,.de-inp[type=number]::-webkit-inner-spin-button{-webkit-appearance:none}
.de-rng{flex:1;accent-color:var(--ed-green);min-width:0;height:4px;cursor:pointer}
.de-hint{color:var(--ed-text2);text-align:center;padding:28px 14px;line-height:1.9}
.de-hint i{font-size:32px;color:var(--ed-green);margin-bottom:10px;display:block;opacity:.5}
.de-hint strong{display:block;color:var(--ed-text);font-size:13px;margin-bottom:4px}
.de-hint small{display:block;font-size:10px;opacity:.7}
.de-lbtn-row{display:flex;gap:3px;margin-bottom:5px}
.de-lbtn-row .de-btn{flex:1;justify-content:center;font-size:11px;padding:4px 2px}
.de-cb-row{display:flex;align-items:center;gap:6px;margin-bottom:6px;color:var(--ed-text2);font-size:11px;cursor:pointer}
.de-cb-row input[type=checkbox]{accent-color:var(--ed-green);cursor:pointer}
/* Panel tabs */
.de-ptab{display:flex;gap:2px;margin-bottom:8px;background:var(--ed-inp);border-radius:6px;padding:2px}
.de-ptab-btn{flex:1;padding:4px;text-align:center;font-size:11px;border-radius:4px;cursor:pointer;color:var(--ed-text2);transition:all .15s;border:none;background:none;font-family:inherit}
.de-ptab-btn.active{background:var(--ed-green);color:#fff}
/* Swatches */
.de-swatches{display:flex;flex-wrap:wrap;gap:4px;margin-bottom:7px}
.de-swatch{width:20px;height:20px;border-radius:3px;cursor:pointer;border:1px solid rgba(255,255,255,.12);transition:transform .1s,box-shadow .1s;flex-shrink:0}
.de-swatch:hover{transform:scale(1.3);box-shadow:0 0 0 2px var(--ed-green)}
/* Gradient */
.de-grad-bar{height:18px;border-radius:4px;border:1px solid var(--ed-border);margin-bottom:7px;cursor:pointer}
/* Select full-width */
.de-select-fw{background:var(--ed-inp);border:1px solid var(--ed-border);border-radius:5px;color:var(--ed-text);font-size:11px;padding:4px 7px;font-family:inherit;width:100%}
.de-select-fw:focus{outline:none;border-color:var(--ed-green)}
/* val badge */
.de-val{font-size:10px;color:var(--ed-text3);min-width:28px;text-align:center;flex-shrink:0}
/* ═══ Align bar ═══ */
#de-align-bar{display:none;align-items:center;gap:2px}
#de-align-bar.show{display:flex}
/* ═══ Drawing cursor ═══ */
.de-drawing-mode #de-canvas-wrap canvas{cursor:crosshair!important}
/* ═══ Templates grid ═══ */
.tmpl-thumb-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px}
.tmpl-thumb-card{border:1px solid #dee2e6;border-radius:8px;overflow:hidden;background:#fff;transition:box-shadow .2s}
.tmpl-thumb-card:hover{box-shadow:0 2px 12px rgba(0,0,0,.12)}
.tmpl-thumb-card img{width:100%;aspect-ratio:1;object-fit:cover;display:block}
.tmpl-thumb-card .tmpl-ph{width:100%;aspect-ratio:1;background:#f0f4f8;display:flex;align-items:center;justify-content:center;color:#adb5bd}
.tmpl-thumb-card .tmpl-info{padding:8px}
.tmpl-thumb-card .tmpl-name{font-size:13px;font-weight:600;color:#1a1a1a}
.tmpl-thumb-card .tmpl-cat{font-size:11px;color:#6c757d;margin-top:2px}
.tmpl-thumb-card .tmpl-actions{display:flex;gap:6px;padding:6px 8px;border-top:1px solid #f1f3f5}
.category-form-card-header{border-bottom:2px solid var(--primary-clr,#EC2227)}
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <i class="tio-layers fs-22"></i>
                {{ $fromProduct ? 'إضافة قالب للمنتج' : (translate('design_templates') ?: 'قوالب التصميم') }}
            </h2>
            @if($fromProduct)
            <div class="mt-1 d-flex align-items-center gap-2">
                <span class="badge badge-soft-success" style="font-size:13px;padding:5px 12px">
                    <i class="fa fa-box me-1"></i> {{ $fromProduct->name }}
                </span>
                <a href="{{ route('admin.design-template.add-new') }}" class="text-muted small">
                    <i class="fa fa-xmark"></i> إلغاء
                </a>
            </div>
            @endif
        </div>
        <a href="{{ route('admin.design-template.by-category') }}" class="btn btn-soft-secondary btn-sm">
            <i class="fa fa-list me-1"></i> عرض كل القوالب
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- ───── Editor Card ───── --}}
    <div class="card mb-3">
        <div class="card-header bg-light" style="border-bottom:2px solid var(--primary-clr,#EC2227)">
            <h6 class="mb-0 fw-semibold"><i class="tio-edit me-2"></i>{{ translate('add_new_template') ?: 'إضافة قالب جديد' }}</h6>
        </div>
        <div class="card-body p-3">
            <form action="{{ route('admin.design-template.store') }}" method="POST" id="tmpl-form">
                @csrf
                <input type="hidden" name="canvas_json"       id="f_canvas_json">
                <input type="hidden" name="canvas_width"      id="f_canvas_width"  value="800">
                <input type="hidden" name="canvas_height"     id="f_canvas_height" value="800">
                <input type="hidden" name="thumbnail_base64"  id="f_thumbnail">
                @if($fromProductId)
                    <input type="hidden" name="product_id"    value="{{ $fromProductId }}">
                    <input type="hidden" name="category_id"   value="">
                @endif

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="input-label">{{ translate('template_name') ?: 'اسم القالب' }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="مثال: بطاقة عمل احترافية" required maxlength="255">
                    </div>

                    @if(!$fromProductId)
                    <div class="col-md-3">
                        <label class="input-label">{{ translate('main_category') ?: 'التصنيف الرئيسي' }}</label>
                        <select id="main-cat-sel" class="form-control" onchange="onMainCatChange(this.value)">
                            <option value="">-- اختر التصنيف الرئيسي --</option>
                            @foreach($mainCategories as $mc)
                                <option value="{{ $mc->id }}">{{ $mc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="input-label">{{ translate('sub_category') ?: 'التصنيف الفرعي' }}</label>
                        <select id="sub-cat-sel" class="form-control" onchange="onSubCatChange(this.value)" disabled>
                            <option value="">-- اختر الفرعي (اختياري) --</option>
                        </select>
                        <input type="hidden" name="category_id" id="category_id_input" value="">
                    </div>
                    <div class="col-md-3">
                        <label class="input-label">{{ translate('product') ?: 'المنتج' }} <span class="text-danger">*</span></label>
                        <select name="product_id" id="product-sel" class="form-control" required>
                            <option value="">-- {{ translate('select_product') ?: 'اختر المنتج' }} --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-md-2">
                        <label class="input-label">{{ translate('position') ?: 'الترتيب' }}</label>
                        <input type="number" name="position" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-primary w-100" onclick="submitTemplate()">
                            <i class="fa fa-floppy-disk me-1"></i> {{ translate('save_template') ?: 'حفظ القالب' }}
                        </button>
                    </div>
                </div>

                {{-- ═════════ Canva-like Editor ═════════ --}}
                <div class="de-root" id="de-root">

                    {{-- ── Top Bar ── --}}
                    <div class="de-topbar">
                        {{-- Tool mode --}}
                        <button type="button" class="de-btn active" id="de-tool-select" onclick="deTool('select')" title="تحديد (V)"><i class="fa fa-arrow-pointer"></i></button>
                        <button type="button" class="de-btn" id="de-tool-draw" onclick="deTool('draw')" title="رسم حر (D)"><i class="fa fa-pen-nib"></i></button>
                        <div class="de-sep"></div>
                        {{-- Draw settings (shown in draw mode) --}}
                        <div id="de-draw-opts" style="display:none;align-items:center;gap:4px">
                            <input type="color" id="de-brush-color" value="#000000" style="width:26px;height:24px;border:none;border-radius:4px;cursor:pointer" onchange="deCanvas.freeDrawingBrush.color=this.value" title="لون الفرشاة">
                            <input type="range" id="de-brush-w" min="1" max="40" value="4" style="width:60px;accent-color:var(--ed-green)" oninput="deCanvas.freeDrawingBrush.width=+this.value;document.getElementById('de-brush-wv').textContent=this.value+'px'" title="سُمك">
                            <span id="de-brush-wv" style="font-size:11px;color:var(--ed-text2)">4px</span>
                            <div class="de-sep"></div>
                        </div>
                        {{-- Add elements --}}
                        <button type="button" class="de-btn" onclick="deAddText()" title="نص (T)"><i class="fa fa-font"></i> نص</button>
                        <div class="dropdown d-inline-block">
                            <button type="button" class="de-btn" data-toggle="dropdown"><i class="fa fa-shapes"></i> أشكال <i class="fa fa-chevron-down" style="font-size:8px"></i></button>
                            <div class="dropdown-menu py-1" style="background:#1a1f1d;border:1px solid #2a3330;min-width:148px;z-index:9999;border-radius:10px">
                                <a class="dropdown-item py-1 de-ddi" href="javascript:void(0)" onclick="deAddRect()"><i class="fa fa-square" style="color:#10b46a;width:18px"></i>مستطيل</a>
                                <a class="dropdown-item py-1 de-ddi" href="javascript:void(0)" onclick="deAddRoundedRect()"><i class="fa fa-square" style="color:#4ecdc4;width:18px;border-radius:4px"></i>مستطيل مدوّر</a>
                                <a class="dropdown-item py-1 de-ddi" href="javascript:void(0)" onclick="deAddCircle()"><i class="fa fa-circle" style="color:#4ecdc4;width:18px"></i>دائرة / بيضاوي</a>
                                <a class="dropdown-item py-1 de-ddi" href="javascript:void(0)" onclick="deAddTriangle()"><i class="fa fa-play" style="color:#f7c59f;width:18px;transform:rotate(90deg)"></i>مثلث</a>
                                <a class="dropdown-item py-1 de-ddi" href="javascript:void(0)" onclick="deAddDiamond()"><i class="fa fa-diamond" style="color:#a78bfa;width:18px"></i>معين</a>
                                <a class="dropdown-item py-1 de-ddi" href="javascript:void(0)" onclick="deAddStar()"><i class="fa fa-star" style="color:#f7d354;width:18px"></i>نجمة</a>
                                <a class="dropdown-item py-1 de-ddi" href="javascript:void(0)" onclick="deAddHeart()"><i class="fa fa-heart" style="color:#e05c5c;width:18px"></i>قلب</a>
                                <div class="dropdown-divider" style="border-color:#2a3330;margin:3px 0"></div>
                                <a class="dropdown-item py-1 de-ddi" href="javascript:void(0)" onclick="deAddLine()"><i class="fa fa-minus" style="color:#aaa;width:18px"></i>خط</a>
                                <a class="dropdown-item py-1 de-ddi" href="javascript:void(0)" onclick="deAddArrow()"><i class="fa fa-arrow-right" style="color:#aaa;width:18px"></i>سهم</a>
                            </div>
                        </div>
                        <button type="button" class="de-btn" onclick="document.getElementById('de-img-upload').click()" title="رفع صورة"><i class="fa fa-image"></i> صورة</button>
                        <input type="file" id="de-img-upload" accept="image/*" style="display:none" onchange="deUploadImg(event)">
                        <div class="de-sep"></div>
                        {{-- Background --}}
                        <label style="display:inline-flex;align-items:center;gap:4px;cursor:pointer;color:var(--ed-text2);font-size:12px" title="خلفية اللوحة">
                            <i class="fa fa-fill-drip" style="font-size:13px"></i>
                            <input type="color" id="de-bg-picker" value="#ffffff" style="width:24px;height:22px;border:none;background:none;cursor:pointer;border-radius:4px" onchange="deSetBg(this.value)">
                        </label>
                        <button type="button" class="de-btn de-btn-sm" onclick="deToggleGrid()" id="de-grid-btn" title="تبديل الشبكة"><i class="fa fa-border-all"></i></button>
                        <div class="de-sep"></div>
                        {{-- Canvas size --}}
                        <select class="de-select" id="de-size-sel" onchange="deChangeSize(this.value)" title="حجم اللوحة" style="font-size:11px;padding:3px 6px">
                            <option value="800x800">800×800</option>
                            <option value="1200x628">1200×628 سوشيال</option>
                            <option value="1200x600">1200×600</option>
                            <option value="1200x400">1200×400 بانر</option>
                            <option value="900x600">900×600</option>
                            <option value="600x900">600×900 عمودي</option>
                            <option value="1050x600">1050×600 بطاقة</option>
                            <option value="595x842">A4 595×842</option>
                            <option value="custom">مخصص...</option>
                        </select>
                        <div class="de-sep"></div>
                        {{-- Undo / Redo --}}
                        <button type="button" class="de-btn de-btn-sm" onclick="deUndo()" title="تراجع Ctrl+Z"><i class="fa fa-rotate-left"></i></button>
                        <button type="button" class="de-btn de-btn-sm" onclick="deRedo()" title="إعادة Ctrl+Y"><i class="fa fa-rotate-right"></i></button>
                        <div class="de-sep"></div>
                        {{-- Alignment bar (shows when object selected) --}}
                        <div id="de-align-bar">
                            <button type="button" class="de-btn de-btn-sm" onclick="deAlign('left')"   title="محاذاة يمين اللوحة"><i class="fa fa-align-right"></i></button>
                            <button type="button" class="de-btn de-btn-sm" onclick="deAlign('hcenter')" title="توسيط أفقي"><i class="fa fa-align-center"></i></button>
                            <button type="button" class="de-btn de-btn-sm" onclick="deAlign('right')"  title="محاذاة يسار اللوحة"><i class="fa fa-align-left"></i></button>
                            <button type="button" class="de-btn de-btn-sm" onclick="deAlign('top')"    title="محاذاة أعلى"><i class="fa fa-arrows-up-to-line"></i></button>
                            <button type="button" class="de-btn de-btn-sm" onclick="deAlign('vcenter')" title="توسيط رأسي"><i class="fa fa-minus" style="transform:rotate(90deg)"></i></button>
                            <button type="button" class="de-btn de-btn-sm" onclick="deAlign('bottom')" title="محاذاة أسفل"><i class="fa fa-arrows-down-to-line"></i></button>
                            <div class="de-sep"></div>
                            <button type="button" class="de-btn de-btn-sm" id="de-dup-btn" onclick="deDuplicate()" title="نسخ Ctrl+D"><i class="fa fa-copy"></i></button>
                            <button type="button" class="de-btn de-btn-sm de-danger" id="de-del-btn" onclick="deDeleteSelected()" title="حذف Del"><i class="fa fa-trash"></i></button>
                            <div class="de-sep"></div>
                        </div>
                        {{-- Zoom --}}
                        <button type="button" class="de-btn de-btn-sm" onclick="deZoomChange(-0.1)"><i class="fa fa-minus"></i></button>
                        <span id="de-zoom-lbl" style="font-size:11px;color:var(--ed-text2);min-width:34px;text-align:center;cursor:default">100%</span>
                        <button type="button" class="de-btn de-btn-sm" onclick="deZoomChange(0.1)"><i class="fa fa-plus"></i></button>
                        <button type="button" class="de-btn de-btn-sm" onclick="deZoomFit()" title="ملاءمة"><i class="fa fa-expand"></i></button>
                        <div class="de-sep"></div>
                        <button type="button" class="de-btn de-btn-sm" id="de-fs-btn" onclick="deToggleFullscreen()" title="وضع ملء الشاشة (F)"><i class="fa fa-up-right-and-down-left-from-center"></i></button>
                        <div class="de-sep"></div>
                        <button type="button" class="de-btn de-danger de-btn-sm" onclick="deClear()" title="مسح الكل"><i class="fa fa-trash-can"></i></button>
                    </div>

                    {{-- ── Body: Sidebar | Canvas | Panel ── --}}
                    <div class="de-body">

                        {{-- Left Sidebar --}}
                        <div class="de-sidebar">
                            <div class="de-sb-tabs">
                                <div class="de-sb-tab active" onclick="sbTab('text',this)"><i class="fa fa-font"></i><span>نص</span></div>
                                <div class="de-sb-tab" onclick="sbTab('shapes',this)"><i class="fa fa-shapes"></i><span>أشكال</span></div>
                                <div class="de-sb-tab" onclick="sbTab('image',this)"><i class="fa fa-image"></i><span>صور</span></div>
                                <div class="de-sb-tab" onclick="sbTab('bg',this)"><i class="fa fa-palette"></i><span>خلفية</span></div>
                            </div>
                            <div class="de-sb-panes">
                                {{-- Text pane --}}
                                <div class="de-sb-pane active" id="sb-text">
                                    <div class="de-sb-sec">إضافة نص</div>
                                    <button class="de-sb-text-btn" onclick="deAddHeading()" style="font-size:18px;font-weight:700;font-family:'Cairo',sans-serif">
                                        عنوان رئيسي
                                        <span class="sb-t-label">Heading · 36px</span>
                                    </button>
                                    <button class="de-sb-text-btn" onclick="deAddSubheading()" style="font-size:14px;font-weight:600;font-family:'Cairo',sans-serif">
                                        عنوان فرعي
                                        <span class="sb-t-label">Subheading · 24px</span>
                                    </button>
                                    <button class="de-sb-text-btn" onclick="deAddText()" style="font-size:12px;font-family:'Cairo',sans-serif">
                                        نص عادي
                                        <span class="sb-t-label">Body · 16px</span>
                                    </button>
                                    <div class="de-sb-sec">أنماط جاهزة</div>
                                    <div class="de-sb-grid">
                                        <div class="de-sb-item" onclick="deAddStyledText('sale')" title="تخفيض"><i class="fa fa-percent" style="color:#e05c5c"></i>تخفيض</div>
                                        <div class="de-sb-item" onclick="deAddStyledText('badge')" title="شارة"><i class="fa fa-certificate" style="color:#f7d354"></i>شارة</div>
                                        <div class="de-sb-item" onclick="deAddStyledText('price')" title="سعر"><i class="fa fa-tag" style="color:#10b46a"></i>سعر</div>
                                        <div class="de-sb-item" onclick="deAddStyledText('title')" title="عنوان بإطار"><i class="fa fa-heading" style="color:#a78bfa"></i>عنوان</div>
                                        <div class="de-sb-item" onclick="deAddStyledText('quote')" title="اقتباس"><i class="fa fa-quote-right" style="color:#4ecdc4"></i>اقتباس</div>
                                        <div class="de-sb-item" onclick="deAddStyledText('label')" title="تسمية"><i class="fa fa-bookmark" style="color:#f7c59f"></i>تسمية</div>
                                    </div>
                                    <div class="de-sb-sec">خطوط متاحة</div>
                                    <div style="display:flex;flex-direction:column;gap:3px">
                                        @foreach(['Cairo','Tajawal','Amiri','Arial','Impact','Georgia','Courier New'] as $fn)
                                        <div onclick="ppSet('fontFamily','{{ $fn }}')" style="font-family:'{{ $fn }}',sans-serif;font-size:13px;padding:5px 8px;background:var(--ed-inp);border-radius:6px;cursor:pointer;border:1px solid var(--ed-border);color:var(--ed-text);transition:border-color .15s" onmouseover="this.style.borderColor='var(--ed-green)'" onmouseout="this.style.borderColor='var(--ed-border)'">{{ $fn }}</div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Shapes pane --}}
                                <div class="de-sb-pane" id="sb-shapes">
                                    <div class="de-sb-sec">أشكال أساسية</div>
                                    <div class="de-sb-grid">
                                        <div class="de-sb-item" onclick="deAddRect()"><i class="fa fa-square" style="color:#10b46a"></i>مستطيل</div>
                                        <div class="de-sb-item" onclick="deAddRoundedRect()"><i class="fa fa-square" style="color:#4ecdc4;border-radius:4px"></i>مدوّر</div>
                                        <div class="de-sb-item" onclick="deAddCircle()"><i class="fa fa-circle" style="color:#4ecdc4"></i>دائرة</div>
                                        <div class="de-sb-item" onclick="deAddTriangle()"><i class="fa fa-play" style="color:#f7c59f;transform:rotate(90deg)"></i>مثلث</div>
                                        <div class="de-sb-item" onclick="deAddDiamond()"><i class="fa fa-diamond" style="color:#a78bfa"></i>معين</div>
                                        <div class="de-sb-item" onclick="deAddStar()"><i class="fa fa-star" style="color:#f7d354"></i>نجمة</div>
                                        <div class="de-sb-item" onclick="deAddHeart()"><i class="fa fa-heart" style="color:#e05c5c"></i>قلب</div>
                                        <div class="de-sb-item" onclick="deAddLine()"><i class="fa fa-minus" style="color:#aaa"></i>خط</div>
                                        <div class="de-sb-item" onclick="deAddArrow()"><i class="fa fa-arrow-right" style="color:#aaa"></i>سهم</div>
                                    </div>
                                    <div class="de-sb-sec">إطارات</div>
                                    <div class="de-sb-grid">
                                        <div class="de-sb-item" onclick="deAddFrame('thin')"><i class="fa fa-border-top-left" style="color:#aaa"></i>رفيع</div>
                                        <div class="de-sb-item" onclick="deAddFrame('thick')"><i class="fa fa-square" style="color:transparent;box-shadow:inset 0 0 0 3px #aaa;border-radius:1px"></i>سميك</div>
                                        <div class="de-sb-item" onclick="deAddFrame('double')"><i class="fa fa-border-all" style="color:#aaa"></i>مزدوج</div>
                                    </div>
                                </div>

                                {{-- Images pane --}}
                                <div class="de-sb-pane" id="sb-image">
                                    <div class="de-sb-sec">رفع صورة</div>
                                    <label class="de-sb-upload" title="اختر صورة من جهازك">
                                        <i class="fa fa-cloud-arrow-up"></i>
                                        اختر صورة
                                        <input type="file" accept="image/*" style="display:none" onchange="deUploadImg(event)">
                                    </label>
                                    <div class="de-sb-sec">من رابط</div>
                                    <div style="display:flex;gap:4px">
                                        <input type="text" id="de-img-url" class="de-inp" placeholder="https://..." style="flex:1;font-size:11px">
                                        <button class="de-btn de-btn-sm" onclick="deAddImgUrl()" style="flex:none;border-color:var(--ed-green);color:var(--ed-green)"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>

                                {{-- Background pane --}}
                                <div class="de-sb-pane" id="sb-bg">
                                    <div class="de-sb-sec">لون الخلفية</div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                                        <input type="color" id="de-bg-picker2" value="#ffffff" style="width:36px;height:30px;border:none;border-radius:6px;cursor:pointer" onchange="document.getElementById('de-bg-picker').value=this.value;deSetBg(this.value)">
                                        <span style="font-size:11px;color:var(--ed-text2)">اختر لوناً</span>
                                    </div>
                                    <div class="de-sb-swatches">
                                        @foreach(['#ffffff','#000000','#1a1a2e','#16213e','#f8f9fa','#e9ecef','#dee2e6','#ffd43b','#ff6b6b','#ee5a24','#10b46a','#4ecdc4','#a78bfa','#f39c12','#e74c3c','#2ecc71','#3498db','#9b59b6','#1abc9c','#e8f4fd'] as $sw)
                                        <div class="de-sb-swatch" style="background:{{ $sw }}" onclick="document.getElementById('de-bg-picker').value='{{ $sw }}';document.getElementById('de-bg-picker2').value='{{ $sw }}';deSetBg('{{ $sw }}')" title="{{ $sw }}"></div>
                                        @endforeach
                                    </div>
                                    <div class="de-sb-sec">تدرجات جاهزة</div>
                                    @foreach([['#667eea','#764ba2'],['#f093fb','#f5576c'],['#4facfe','#00f2fe'],['#43e97b','#38f9d7'],['#fa709a','#fee140'],['#a18cd1','#fbc2eb'],['#ffecd2','#fcb69f'],['#2d3436','#636e72']] as $gr)
                                    <div onclick="deSetBgGradient('{{ $gr[0] }}','{{ $gr[1] }}')" style="height:28px;border-radius:6px;margin-bottom:5px;cursor:pointer;background:linear-gradient(135deg,{{ $gr[0] }},{{ $gr[1] }});border:1px solid rgba(255,255,255,.1);transition:transform .1s" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'"></div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Canvas area --}}
                        <div class="de-canvas-area" id="de-canvas-area">
                            <div id="de-canvas-wrap">
                                <canvas id="de-main-canvas"></canvas>
                                <canvas id="de-grid-canvas" style="position:absolute;top:0;left:0;pointer-events:none;display:none"></canvas>
                            </div>
                        </div>

                        {{-- ── Right Properties Panel ── --}}
                        <div class="de-panel" id="de-panel">

                            {{-- Empty state --}}
                            <div id="de-ps-empty" class="de-ps">
                                <div class="de-hint">
                                    <i class="fa fa-hand-pointer"></i>
                                    <strong>اختر عنصراً</strong>
                                    انقر على أي عنصر في اللوحة لتظهر خصائصه هنا
                                    <small style="margin-top:8px;display:block">أو اختر من الشريط الجانبي لإضافة عنصر جديد</small>
                                </div>
                            </div>

                            {{-- Position & Size --}}
                            <div id="de-ps-pos" class="de-ps" style="display:none">
                                <div class="de-pt"><i class="fa fa-up-down-left-right"></i> الموضع والحجم</div>
                                <div class="de-row">
                                    <span class="de-lbl">X</span><input class="de-inp" id="pp-x" type="number" oninput="ppApplyXY()">
                                    <span class="de-lbl">Y</span><input class="de-inp" id="pp-y" type="number" oninput="ppApplyXY()">
                                </div>
                                <div class="de-row">
                                    <span class="de-lbl">عرض</span><input class="de-inp" id="pp-w" type="number" oninput="ppApplyWH()">
                                    <span class="de-lbl" style="min-width:34px">ارتفاع</span><input class="de-inp" id="pp-h" type="number" oninput="ppApplyWH()">
                                </div>
                                <div class="de-row">
                                    <span class="de-lbl">دوران</span>
                                    <input class="de-inp" id="pp-angle" type="number" min="-360" max="360" oninput="ppSet('angle',+this.value)">
                                    <span style="font-size:11px;color:var(--ed-text2)">°</span>
                                    <button type="button" class="de-btn de-btn-sm" onclick="ppSet('angle',0)"><i class="fa fa-undo"></i></button>
                                </div>
                                <label class="de-cb-row"><input type="checkbox" id="pp-lock-ratio" onchange="window.deAspectLocked=this.checked"> تثبيت النسبة</label>
                            </div>

                            {{-- Fill & Stroke --}}
                            <div id="de-ps-fill" class="de-ps" style="display:none">
                                <div class="de-pt"><i class="fa fa-fill-drip"></i> التعبئة والحدود</div>
                                {{-- Fill tabs --}}
                                <div class="de-ptab">
                                    <button type="button" class="de-ptab-btn active" id="ftab-solid" onclick="switchFillTab('solid')">صلب</button>
                                    <button type="button" class="de-ptab-btn" id="ftab-gradient" onclick="switchFillTab('gradient')">تدرج</button>
                                    <button type="button" class="de-ptab-btn" id="ftab-none" onclick="ppSet('fill','transparent')">بلا</button>
                                </div>
                                {{-- Solid fill --}}
                                <div id="fill-solid-opts">
                                    <div class="de-row"><span class="de-lbl">لون</span><input class="de-inp" type="color" id="pp-fill" oninput="ppSet('fill',this.value)"></div>
                                    <div class="de-swatches">
                                        @foreach(['#1a1a2e','#e05c5c','#f7d354','#10b46a','#4ecdc4','#a78bfa','#f7c59f','#3498db','#ffffff','#000000','#ee5a24','#9b59b6'] as $sw)
                                        <div class="de-swatch" style="background:{{ $sw }}" onclick="document.getElementById('pp-fill').value='{{ $sw }}';ppSet('fill','{{ $sw }}')"></div>
                                        @endforeach
                                    </div>
                                </div>
                                {{-- Gradient fill --}}
                                <div id="fill-gradient-opts" style="display:none">
                                    <div class="de-row">
                                        <span class="de-lbl">لون 1</span><input class="de-inp" type="color" id="pp-grad-c1" value="#10b46a" oninput="ppApplyGradient()">
                                        <span class="de-lbl">لون 2</span><input class="de-inp" type="color" id="pp-grad-c2" value="#4ecdc4" oninput="ppApplyGradient()">
                                    </div>
                                    <div class="de-row">
                                        <span class="de-lbl">زاوية</span>
                                        <input class="de-rng" type="range" id="pp-grad-angle" min="0" max="360" value="135" oninput="ppApplyGradient();document.getElementById('pp-grad-av').textContent=this.value+'°'">
                                        <span class="de-val" id="pp-grad-av">135°</span>
                                    </div>
                                    <div id="pp-grad-preview" class="de-grad-bar" style="background:linear-gradient(135deg,#10b46a,#4ecdc4)"></div>
                                </div>
                                {{-- Stroke --}}
                                <div style="border-top:1px solid var(--ed-border2);padding-top:8px;margin-top:4px">
                                    <div class="de-row">
                                        <span class="de-lbl">حد</span>
                                        <input class="de-inp" type="color" id="pp-stroke" value="#000000" oninput="ppSet('stroke',this.value)">
                                        <input class="de-inp" id="pp-stroke-w" type="number" min="0" max="50" value="0" style="width:44px;flex:none" oninput="ppSet('strokeWidth',+this.value)">
                                        <span style="font-size:10px;color:var(--ed-text2)">px</span>
                                    </div>
                                    <div class="de-row">
                                        <span class="de-lbl">نوع</span>
                                        <select class="de-select-fw" id="pp-stroke-dash" onchange="ppApplyDash(this.value)">
                                            <option value="solid">—— صلب</option>
                                            <option value="dashed">- - متقطع</option>
                                            <option value="dotted">· · · منقط</option>
                                        </select>
                                    </div>
                                </div>
                                {{-- Corner radius (rect only) --}}
                                <div id="pp-radius-row" style="display:none;border-top:1px solid var(--ed-border2);padding-top:8px;margin-top:4px">
                                    <div class="de-row">
                                        <span class="de-lbl">زوايا</span>
                                        <input class="de-rng" type="range" id="pp-radius" min="0" max="200" value="0" oninput="ppSet('rx',+this.value);ppSet('ry',+this.value);document.getElementById('pp-radius-v').textContent=this.value+'px'">
                                        <span class="de-val" id="pp-radius-v">0px</span>
                                    </div>
                                </div>
                                {{-- Opacity --}}
                                <div style="border-top:1px solid var(--ed-border2);padding-top:8px;margin-top:4px">
                                    <div class="de-row">
                                        <span class="de-lbl">شفافية</span>
                                        <input class="de-rng" type="range" id="pp-opacity" min="0" max="1" step="0.01" value="1" oninput="ppSet('opacity',+this.value);document.getElementById('pp-opacity-v').textContent=Math.round(this.value*100)+'%'">
                                        <span class="de-val" id="pp-opacity-v">100%</span>
                                    </div>
                                </div>
                                {{-- Blend mode --}}
                                <div class="de-row" style="margin-top:6px">
                                    <span class="de-lbl">مزج</span>
                                    <select class="de-select-fw" id="pp-blend" onchange="ppSet('globalCompositeOperation',this.value)">
                                        <option value="source-over">عادي</option>
                                        <option value="multiply">multiply</option>
                                        <option value="screen">screen</option>
                                        <option value="overlay">overlay</option>
                                        <option value="darken">darken</option>
                                        <option value="lighten">lighten</option>
                                        <option value="color-dodge">color-dodge</option>
                                        <option value="color-burn">color-burn</option>
                                        <option value="hard-light">hard-light</option>
                                        <option value="soft-light">soft-light</option>
                                        <option value="difference">difference</option>
                                        <option value="exclusion">exclusion</option>
                                        <option value="hue">hue</option>
                                        <option value="saturation">saturation</option>
                                        <option value="luminosity">luminosity</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Text Options --}}
                            <div id="de-ps-text" class="de-ps" style="display:none">
                                <div class="de-pt"><i class="fa fa-font"></i> الخط والنص</div>
                                <div class="de-row">
                                    <span class="de-lbl">خط</span>
                                    <select class="de-inp" id="pp-font" onchange="ppSet('fontFamily',this.value)">
                                        <option>Cairo</option><option>Tajawal</option><option>Amiri</option>
                                        <option>Arial</option><option>Georgia</option><option>Impact</option>
                                        <option>Courier New</option><option>Times New Roman</option><option>Verdana</option>
                                    </select>
                                </div>
                                <div class="de-row">
                                    <span class="de-lbl">حجم</span>
                                    <input class="de-inp" id="pp-fsize" type="number" min="6" max="600" oninput="ppSet('fontSize',+this.value)">
                                    <span style="font-size:10px;color:var(--ed-text2)">px</span>
                                </div>
                                <div class="de-row">
                                    <span class="de-lbl">لون</span>
                                    <input class="de-inp" type="color" id="pp-fcolor" oninput="ppSet('fill',this.value)">
                                    <span class="de-lbl">خلفية</span>
                                    <input class="de-inp" type="color" id="pp-text-bg" value="#00000000" oninput="ppSet('textBackgroundColor',this.value)">
                                </div>
                                <div class="de-row">
                                    <span class="de-lbl">تنسيق</span>
                                    <button type="button" class="de-btn de-btn-sm" id="pp-bold"   onclick="ppToggle('fontWeight','bold','normal')"><b>B</b></button>
                                    <button type="button" class="de-btn de-btn-sm" id="pp-italic" onclick="ppToggle('fontStyle','italic','normal')"><i>I</i></button>
                                    <button type="button" class="de-btn de-btn-sm" id="pp-under"  onclick="ppToggleProp('underline')"><u>U</u></button>
                                    <button type="button" class="de-btn de-btn-sm" id="pp-strike" onclick="ppToggleProp('linethrough')"><s>S</s></button>
                                </div>
                                <div class="de-row">
                                    <span class="de-lbl">محاذاة</span>
                                    <button type="button" class="de-btn de-btn-sm" id="pp-ar" onclick="ppSetAlign('right')"><i class="fa fa-align-right"></i></button>
                                    <button type="button" class="de-btn de-btn-sm" id="pp-ac" onclick="ppSetAlign('center')"><i class="fa fa-align-center"></i></button>
                                    <button type="button" class="de-btn de-btn-sm" id="pp-al" onclick="ppSetAlign('left')"><i class="fa fa-align-left"></i></button>
                                    <button type="button" class="de-btn de-btn-sm" id="pp-aj" onclick="ppSetAlign('justify')"><i class="fa fa-align-justify"></i></button>
                                </div>
                                <div class="de-row">
                                    <span class="de-lbl">تباعد أحرف</span>
                                    <input class="de-rng" type="range" id="pp-cspacing" min="-5" max="60" step="0.5" value="0" oninput="ppSet('charSpacing',+this.value*10);document.getElementById('pp-cspacing-v').textContent=this.value">
                                    <span class="de-val" id="pp-cspacing-v">0</span>
                                </div>
                                <div class="de-row">
                                    <span class="de-lbl">تباعد سطور</span>
                                    <input class="de-rng" type="range" id="pp-lheight" min="0.5" max="3" step="0.1" value="1.2" oninput="ppSet('lineHeight',+this.value);document.getElementById('pp-lheight-v').textContent=(+this.value).toFixed(1)">
                                    <span class="de-val" id="pp-lheight-v">1.2</span>
                                </div>
                                {{-- Text outline (stroke) --}}
                                <div style="border-top:1px solid var(--ed-border2);padding-top:8px;margin-top:4px">
                                    <div class="de-row">
                                        <span class="de-lbl">إطار</span>
                                        <input class="de-inp" type="color" id="pp-txt-stroke" value="#000000" oninput="ppSet('stroke',this.value)">
                                        <input class="de-inp" id="pp-txt-stroke-w" type="number" min="0" max="20" value="0" style="width:44px;flex:none" oninput="ppSet('strokeWidth',+this.value)">
                                        <span style="font-size:10px;color:var(--ed-text2)">px</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Image Filters (shown only for images) --}}
                            <div id="de-ps-img" class="de-ps" style="display:none">
                                <div class="de-pt"><i class="fa fa-sliders"></i> فلاتر الصورة</div>
                                @foreach([['pp-f-brightness','السطوع','brightness','-1','1','0'],['pp-f-contrast','التباين','contrast','-1','1','0'],['pp-f-saturation','التشبع','saturation','-1','1','0'],['pp-f-blur','ضبابية','blur','0','1','0'],['pp-f-hue','درجة اللون','hue','-2','2','0']] as $f)
                                <div class="de-filter-lbl" style="font-size:10px;color:var(--ed-text2);margin-bottom:2px;display:flex;justify-content:space-between"><span>{{ $f[1] }}</span><span id="{{ $f[0] }}-v">{{ $f[5] }}</span></div>
                                <div style="margin-bottom:6px"><input class="de-rng" type="range" id="{{ $f[0] }}" min="{{ $f[3] }}" max="{{ $f[4] }}" step="0.01" value="{{ $f[5] }}" style="width:100%" oninput="ppApplyImgFilter();document.getElementById('{{ $f[0] }}-v').textContent=(+this.value).toFixed(2)"></div>
                                @endforeach
                                <button type="button" class="de-btn de-btn-sm" onclick="ppResetFilters()" style="width:100%;justify-content:center;margin-top:4px"><i class="fa fa-undo me-1"></i>إعادة ضبط الفلاتر</button>
                            </div>

                            {{-- Shadow & Glow --}}
                            <div id="de-ps-shadow" class="de-ps" style="display:none">
                                <div class="de-pt"><i class="fa fa-circle-half-stroke"></i> الظل والتوهج</div>
                                <div class="de-ptab">
                                    <button type="button" class="de-ptab-btn active" id="efx-shadow" onclick="switchEffTab('shadow')">ظل</button>
                                    <button type="button" class="de-ptab-btn" id="efx-glow" onclick="switchEffTab('glow')">توهج</button>
                                </div>
                                {{-- Shadow --}}
                                <div id="eff-shadow-opts">
                                    <label class="de-cb-row"><input type="checkbox" id="pp-shadow-on" onchange="ppToggleShadow(this.checked)"> تفعيل الظل</label>
                                    <div id="pp-shadow-opts" style="display:none">
                                        <div class="de-row"><span class="de-lbl">لون</span><input class="de-inp" type="color" id="pp-sh-color" value="#000000" oninput="ppApplyShadow()"><span class="de-lbl">ضباب</span><input class="de-inp" id="pp-sh-blur" type="number" min="0" max="100" value="10" oninput="ppApplyShadow()"></div>
                                        <div class="de-row"><span class="de-lbl">X</span><input class="de-inp" id="pp-sh-x" type="number" value="5" oninput="ppApplyShadow()"><span class="de-lbl">Y</span><input class="de-inp" id="pp-sh-y" type="number" value="5" oninput="ppApplyShadow()"></div>
                                        <div class="de-row"><span class="de-lbl">شفافية</span><input class="de-rng" type="range" id="pp-sh-opacity" min="0" max="1" step="0.01" value="0.5" oninput="ppApplyShadow()"><span class="de-val" id="pp-sh-op-v">50%</span></div>
                                    </div>
                                </div>
                                {{-- Glow --}}
                                <div id="eff-glow-opts" style="display:none">
                                    <label class="de-cb-row"><input type="checkbox" id="pp-glow-on" onchange="ppToggleGlow(this.checked)"> تفعيل التوهج</label>
                                    <div id="pp-glow-opts" style="display:none">
                                        <div class="de-row"><span class="de-lbl">لون</span><input class="de-inp" type="color" id="pp-glow-color" value="#10b46a" oninput="ppApplyGlow()"><span class="de-lbl">حجم</span><input class="de-inp" id="pp-glow-blur" type="number" min="0" max="80" value="15" oninput="ppApplyGlow()"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Layer & Actions --}}
                            <div id="de-ps-layer" class="de-ps" style="display:none">
                                <div class="de-pt"><i class="fa fa-layer-group"></i> الترتيب والأفعال</div>
                                <div class="de-lbtn-row">
                                    <button type="button" class="de-btn" onclick="deLayer('front')"    title="للأمام تماماً"><i class="fa fa-angles-up"></i></button>
                                    <button type="button" class="de-btn" onclick="deLayer('forward')"  title="خطوة للأمام"><i class="fa fa-angle-up"></i></button>
                                    <button type="button" class="de-btn" onclick="deLayer('backward')" title="خطوة للخلف"><i class="fa fa-angle-down"></i></button>
                                    <button type="button" class="de-btn" onclick="deLayer('back')"     title="للخلف تماماً"><i class="fa fa-angles-down"></i></button>
                                </div>
                                <div class="de-lbtn-row">
                                    <button type="button" class="de-btn" onclick="deFlip('x')" title="عكس أفقي"><i class="fa fa-left-right"></i></button>
                                    <button type="button" class="de-btn" onclick="deFlip('y')" title="عكس رأسي"><i class="fa fa-up-down"></i></button>
                                    <button type="button" class="de-btn de-accent" onclick="deGroup()" title="تجميع"><i class="fa fa-object-group"></i></button>
                                    <button type="button" class="de-btn" onclick="deDuplicate()" title="نسخ Ctrl+D"><i class="fa fa-copy"></i></button>
                                </div>
                                <div class="de-lbtn-row">
                                    <button type="button" class="de-btn" style="flex:1;justify-content:center" onclick="deAlign('hcenter')"><i class="fa fa-align-center"></i> توسيط أفقي</button>
                                    <button type="button" class="de-btn" style="flex:1;justify-content:center" onclick="deAlign('vcenter')"><i class="fa fa-align-center" style="transform:rotate(90deg)"></i> توسيط رأسي</button>
                                </div>
                                <button type="button" class="de-btn de-danger" onclick="deDeleteSelected()" style="width:100%;justify-content:center;margin-top:4px"><i class="fa fa-trash me-1"></i>حذف العنصر</button>
                            </div>

                        </div>{{-- /de-panel --}}
                    </div>{{-- /de-body --}}
                </div>{{-- /de-root --}}
                <style>
                .de-ddi{color:#e2ede6!important;font-size:12px;padding:5px 14px!important;transition:background .12s}
                .de-ddi:hover{background:rgba(16,180,106,.15)!important;color:var(--ed-green)!important}
                </style>
            </form>
        </div>
    </div>

    {{-- ───── Templates List ───── --}}
    <div class="card" style="display:none">
        <div class="card-header bg-light category-form-card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                <i class="tio-layers me-1"></i>
                {{ translate('design_templates') ?: 'القوالب الحالية' }}
            </h6>
            <span class="badge" style="background:var(--primary-clr,#EC2227);color:#fff;font-size:.95rem;padding:.35rem .75rem">{{ $templates->total() }}</span>
        </div>

        {{-- Search --}}
        <div class="card-body p-2 bg-light">
            <form action="{{ request()->url() }}" method="GET">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-5">
                        <input type="search" name="search" class="form-control form-control-sm"
                               placeholder="{{ translate('Search by name or category') ?: 'بحث بالاسم أو التصنيف' }}"
                               value="{{ $search }}">
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="tio-checkmark-circle-outlined me-1"></i>{{ translate('Show_Data') }}</button>
                        <a href="{{ route('admin.design-template.add-new') }}" class="btn btn-soft-secondary btn-sm">{{ translate('clear') }}</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            @if($templates->isEmpty())
                <div class="text-center py-4">
                    <img class="mb-3 width-7rem" src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}" alt="">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @else
                <div class="tmpl-thumb-grid">
                    @foreach($templates as $tmpl)
                    <div class="tmpl-thumb-card">
                        @if($tmpl->thumbnail_fullpath)
                            <img src="{{ $tmpl->thumbnail_fullpath }}" alt="{{ $tmpl->name }}">
                        @else
                            <div class="tmpl-ph"><i class="fa fa-palette fa-2x"></i></div>
                        @endif
                        <div class="tmpl-info">
                            <div class="tmpl-name">{{ $tmpl->name }}</div>
                            @if($tmpl->mainCategory)
                                <div class="tmpl-cat">
                                    @if($tmpl->mainCategory->parent_id > 0)
                                        <span style="color:#adb5bd">{{ optional($tmpl->mainCategory->parent)->name }}</span>
                                        <i class="fa fa-chevron-left" style="font-size:9px;margin:0 2px"></i>
                                    @endif
                                    {{ $tmpl->mainCategory->name }}
                                </div>
                            @endif
                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <label class="switcher switcher-sm mb-0">
                                    <input type="checkbox" class="switcher_input change-status"
                                           {{ $tmpl->status ? 'checked' : '' }}
                                           data-route="{{ route('admin.design-template.status', [$tmpl->id, $tmpl->status ? 0 : 1]) }}">
                                    <span class="switcher_control"></span>
                                </label>
                                <span class="badge badge-soft-secondary" style="font-size:10px">#{{ $tmpl->position }}</span>
                            </div>
                        </div>
                        <div class="tmpl-actions">
                            <a href="{{ route('admin.design-template.edit', $tmpl->id) }}"
                               class="btn btn-outline-info btn-sm flex-fill text-center">
                                <i class="tio-edit"></i>
                            </a>
                            <a href="javascript:" class="btn btn-outline-danger btn-sm flex-fill text-center form-alert"
                               data-id="tmpl-del-{{ $tmpl->id }}"
                               data-message="{{ translate('Want to delete this template?') ?: 'حذف هذا القالب؟' }}">
                                <i class="tio-delete"></i>
                            </a>
                        </div>
                        <form id="tmpl-del-{{ $tmpl->id }}"
                              action="{{ route('admin.design-template.delete', $tmpl->id) }}"
                              method="POST" style="display:none">
                            @csrf @method('DELETE')
                        </form>
                    </div>
                    @endforeach
                </div>
                <div class="mt-3">
                    {!! $templates->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
/* ═══ Category Dropdowns ═══ */
const allSubCats=@json($subCategories->map(fn($s)=>['id'=>$s->id,'name'=>$s->name,'parent_id'=>$s->parent_id]));
function onMainCatChange(val){const s=document.getElementById('sub-cat-sel');s.innerHTML='<option value="">-- اختر الفرعي (اختياري) --</option>';const subs=allSubCats.filter(x=>x.parent_id==val);if(subs.length){subs.forEach(x=>{const o=document.createElement('option');o.value=x.id;o.textContent=x.name;s.appendChild(o);});s.disabled=false;}else s.disabled=true;document.getElementById('category_id_input').value=val||'';}
function onSubCatChange(val){document.getElementById('category_id_input').value=val||document.getElementById('main-cat-sel').value||'';}

/* ═══════════════════════════════════════════════
   Canva-like Design Editor — Full Feature Set
═══════════════════════════════════════════════ */
let deCanvas, deHist=[], deHistIdx=-1, deZoomScale=1, deAspectLocked=false, deGridOn=false;

window.addEventListener('DOMContentLoaded',()=>{
    deCanvas=new fabric.Canvas('de-main-canvas',{backgroundColor:'#ffffff',selection:true,preserveObjectStacking:true});
    deApplySize(800,800);
    deCanvas.on('selection:created',deOnSelect);
    deCanvas.on('selection:updated',deOnSelect);
    deCanvas.on('selection:cleared',deOnDeselect);
    deCanvas.on('object:modified',()=>{deUpdatePanel();deSaveHist();});
    deCanvas.on('object:added',deSaveHist);
    deCanvas.on('object:removed',deSaveHist);
    deCanvas.on('object:moving',deUpdatePanel);
    deCanvas.on('object:scaling',()=>{if(deAspectLocked){const o=deCanvas.getActiveObject();if(o){const r=o.width/o.height;if(o._lastScale!==o.scaleX){o.scaleY=o.scaleX/r;o._lastScale=o.scaleX;}else{o.scaleX=o.scaleY*r;o._lastScale=o.scaleX;}}}deUpdatePanel();});
    deCanvas.on('object:rotating',deUpdatePanel);
    deSaveHist();
    if(window.ResizeObserver)new ResizeObserver(()=>deZoomFit()).observe(document.getElementById('de-canvas-area'));
    document.addEventListener('keydown',e=>{
        if(['INPUT','TEXTAREA','SELECT'].includes(e.target.tagName))return;
        if((e.ctrlKey||e.metaKey)&&e.key==='z'){e.preventDefault();deUndo();return;}
        if((e.ctrlKey||e.metaKey)&&e.key==='y'){e.preventDefault();deRedo();return;}
        if((e.ctrlKey||e.metaKey)&&e.key==='d'){e.preventDefault();deDuplicate();return;}
        if((e.ctrlKey||e.metaKey)&&e.key==='g'){e.preventDefault();deGroup();return;}
        if((e.ctrlKey||e.metaKey)&&e.key==='a'){e.preventDefault();deCanvas.setActiveObject(new fabric.ActiveSelection(deCanvas.getObjects(),{canvas:deCanvas}));deCanvas.renderAll();return;}
        if(e.key==='f'||e.key==='F'){e.preventDefault();deToggleFullscreen();return;}
        const obj=deCanvas.getActiveObject();
        if(e.key==='Delete'||e.key==='Backspace'){if(obj&&!deCanvas.isDrawingMode)deDeleteSelected();return;}
        if(!obj)return;
        const d=e.shiftKey?10:1;
        if(e.key==='ArrowLeft'){e.preventDefault();obj.set('left',obj.left-d);}
        if(e.key==='ArrowRight'){e.preventDefault();obj.set('left',obj.left+d);}
        if(e.key==='ArrowUp'){e.preventDefault();obj.set('top',obj.top-d);}
        if(e.key==='ArrowDown'){e.preventDefault();obj.set('top',obj.top+d);}
        deCanvas.renderAll();deUpdatePanel();
    });

    @if(!empty($prefill))
    /* ── تحميل تصميم زبون لتحويله إلى قالب ── */
    (function(){
        const PF = @json($prefill);
        try {
            const data = (typeof PF.json === 'string') ? JSON.parse(PF.json) : PF.json;
            deApplySize(PF.width || 800, PF.height || 800);
            deCanvas.loadFromJSON(data, () => {
                deCanvas.renderAll();
                deHist = []; deHistIdx = -1; deSaveHist();
                deZoomFit();
            });
        } catch (e) { console.error('فشل تحميل تصميم الزبون', e); }
        const nameEl = document.querySelector('[name="name"]');
        if (nameEl && !nameEl.value) nameEl.value = PF.name || '';
        const prodEl = document.getElementById('product-sel');
        if (prodEl && PF.product_id) prodEl.value = String(PF.product_id);
    })();
    @endif
});

/* ── Sidebar tabs ── */
function sbTab(name,el){document.querySelectorAll('.de-sb-tab').forEach(t=>t.classList.remove('active'));document.querySelectorAll('.de-sb-pane').forEach(p=>p.classList.remove('active'));el.classList.add('active');document.getElementById('sb-'+name).classList.add('active');}

/* ── Canvas sizing & zoom ── */
function deApplySize(w,h){deCanvas.setWidth(w);deCanvas.setHeight(h);deCanvas.renderAll();document.getElementById('f_canvas_width').value=w;document.getElementById('f_canvas_height').value=h;deDrawGrid();deZoomFit();}
function deZoomFit(){const area=document.getElementById('de-canvas-area'),wrap=document.getElementById('de-canvas-wrap');if(!area||!wrap||!deCanvas)return;const avail=area.clientWidth-56;const scale=Math.min(1,avail/deCanvas.getWidth());deZoomScale=scale;wrap.style.transform=`scale(${scale})`;wrap.style.transformOrigin='top center';wrap.style.width=deCanvas.getWidth()+'px';wrap.style.height=deCanvas.getHeight()+'px';document.getElementById('de-zoom-lbl').textContent=Math.round(scale*100)+'%';}
function deZoomChange(d){const wrap=document.getElementById('de-canvas-wrap');deZoomScale=Math.max(0.1,Math.min(3,deZoomScale+d));wrap.style.transform=`scale(${deZoomScale})`;wrap.style.transformOrigin='top center';document.getElementById('de-zoom-lbl').textContent=Math.round(deZoomScale*100)+'%';}
function deChangeSize(val){if(val==='custom'){const w=parseInt(prompt('العرض (px):',deCanvas.getWidth())),h=parseInt(prompt('الارتفاع (px):',deCanvas.getHeight()));if(w>0&&h>0)deApplySize(w,h);document.getElementById('de-size-sel').value=deCanvas.getWidth()+'x'+deCanvas.getHeight();return;}deApplySize(...val.split('x').map(Number));}

/* ── Fullscreen ── */
function deToggleFullscreen(){
    const root=document.getElementById('de-root');
    if(!document.fullscreenElement && !document.webkitFullscreenElement){
        (root.requestFullscreen||root.webkitRequestFullscreen||(()=>{})).call(root);
    }else{
        (document.exitFullscreen||document.webkitExitFullscreen||(()=>{})).call(document);
    }
}
function deOnFullscreenChange(){
    const on=!!(document.fullscreenElement||document.webkitFullscreenElement);
    const icon=document.querySelector('#de-fs-btn i');
    if(icon) icon.className='fa '+(on?'fa-down-left-and-up-right-to-center':'fa-up-right-and-down-left-from-center');
    setTimeout(()=>deZoomFit(),120);
}
document.addEventListener('fullscreenchange',deOnFullscreenChange);
document.addEventListener('webkitfullscreenchange',deOnFullscreenChange);

/* ── Grid ── */
function deDrawGrid(){const gc=document.getElementById('de-grid-canvas');if(!gc||!deCanvas)return;const w=deCanvas.getWidth(),h=deCanvas.getHeight(),step=40;gc.width=w;gc.height=h;if(!deGridOn)return;const ctx=gc.getContext('2d');ctx.clearRect(0,0,w,h);ctx.strokeStyle='rgba(255,255,255,0.1)';ctx.lineWidth=1;for(let x=0;x<=w;x+=step){ctx.beginPath();ctx.moveTo(x,0);ctx.lineTo(x,h);ctx.stroke();}for(let y=0;y<=h;y+=step){ctx.beginPath();ctx.moveTo(0,y);ctx.lineTo(w,y);ctx.stroke();}}
function deToggleGrid(){deGridOn=!deGridOn;document.getElementById('de-grid-btn').classList.toggle('active',deGridOn);document.getElementById('de-grid-canvas').style.display=deGridOn?'block':'none';deDrawGrid();}

/* ── Tool modes ── */
function deTool(t){deCanvas.isDrawingMode=(t==='draw');document.querySelectorAll('[id^="de-tool-"]').forEach(b=>b.classList.remove('active'));document.getElementById('de-tool-'+t)?.classList.add('active');document.getElementById('de-draw-opts').style.display=t==='draw'?'inline-flex':'none';if(t==='draw'){deCanvas.freeDrawingBrush=new fabric.PencilBrush(deCanvas);deCanvas.freeDrawingBrush.color=document.getElementById('de-brush-color').value;deCanvas.freeDrawingBrush.width=+document.getElementById('de-brush-w').value;}}

/* ── Add text objects ── */
function _mkText(txt,opts){const o=new fabric.IText(txt,{left:deCanvas.width/2,top:80,originX:'center',fontFamily:'Cairo',fill:'#1a1a1a',textAlign:'right',direction:'rtl',...opts});deCanvas.add(o);deCanvas.setActiveObject(o);o.enterEditing();deCanvas.renderAll();}
function deAddText(){_mkText('اكتب هنا...',{fontSize:18});}
function deAddHeading(){_mkText('العنوان الرئيسي',{fontSize:48,fontWeight:'700'});}
function deAddSubheading(){_mkText('العنوان الفرعي',{fontSize:28,fontWeight:'600'});}
function deAddStyledText(style){
    const styles={
        sale:{text:'خصم 50%',fontSize:40,fontWeight:'700',fill:'#ffffff',backgroundColor:'#e05c5c',padding:12},
        badge:{text:'جديد',fontSize:22,fontWeight:'700',fill:'#1a1a2e',backgroundColor:'#f7d354',padding:10},
        price:{text:'99.99 ريال',fontSize:34,fontWeight:'700',fill:'#10b46a'},
        title:{text:'عنوان مميز',fontSize:32,fontWeight:'700',fill:'#1a1a2e',stroke:'#10b46a',strokeWidth:1},
        quote:{text:'"اقتباس ملهم هنا"',fontSize:20,fontStyle:'italic',fill:'#4ecdc4'},
        label:{text:'تسمية',fontSize:16,fill:'#f7c59f',fontWeight:'600'},
    };
    const s=styles[style]||styles.sale;
    _mkText(s.text,s);
}

/* ── Shapes ── */
function deAddRect(){const o=new fabric.Rect({left:120,top:120,width:220,height:140,fill:'#10b46a',rx:0,ry:0});deCanvas.add(o);deCanvas.setActiveObject(o);deCanvas.renderAll();}
function deAddRoundedRect(){const o=new fabric.Rect({left:120,top:120,width:220,height:140,fill:'#10b46a',rx:20,ry:20});deCanvas.add(o);deCanvas.setActiveObject(o);deCanvas.renderAll();}
function deAddCircle(){const o=new fabric.Circle({left:160,top:160,radius:90,fill:'#4ecdc4'});deCanvas.add(o);deCanvas.setActiveObject(o);deCanvas.renderAll();}
function deAddTriangle(){const o=new fabric.Triangle({left:150,top:130,width:160,height:140,fill:'#f7c59f'});deCanvas.add(o);deCanvas.setActiveObject(o);deCanvas.renderAll();}
function deAddDiamond(){const o=new fabric.Polygon([{x:100,y:0},{x:200,y:100},{x:100,y:200},{x:0,y:100}],{left:150,top:120,fill:'#a78bfa'});deCanvas.add(o);deCanvas.setActiveObject(o);deCanvas.renderAll();}
function deAddStar(){const pts=[],n=5,R=80,r=36;for(let i=0;i<n*2;i++){const rad=(Math.PI/n)*i-Math.PI/2,d=i%2===0?R:r;pts.push({x:d*Math.cos(rad),y:d*Math.sin(rad)});}const o=new fabric.Polygon(pts,{left:200,top:200,fill:'#f7d354',stroke:'#d4a800',strokeWidth:2});deCanvas.add(o);deCanvas.setActiveObject(o);deCanvas.renderAll();}
function deAddHeart(){const o=new fabric.Path('M 0,-30 C 20,-60 60,-40 60,0 C 60,30 30,55 0,80 C -30,55 -60,30 -60,0 C -60,-40 -20,-60 0,-30 Z',{left:160,top:140,fill:'#e05c5c'});deCanvas.add(o);deCanvas.setActiveObject(o);deCanvas.renderAll();}
function deAddLine(){const o=new fabric.Line([80,100,400,100],{stroke:'#555',strokeWidth:3,strokeLineCap:'round'});deCanvas.add(o);deCanvas.setActiveObject(o);deCanvas.renderAll();}
function deAddArrow(){const pts=[{x:0,y:0},{x:200,y:0},{x:200,y:-14},{x:240,y:0},{x:200,y:14},{x:200,y:0}];const o=new fabric.Polyline(pts,{left:80,top:160,fill:'#555',stroke:'#555',strokeWidth:2});deCanvas.add(o);deCanvas.setActiveObject(o);deCanvas.renderAll();}
function deAddFrame(type){const w=deCanvas.getWidth(),h=deCanvas.getHeight(),pad=type==='thick'?20:type==='double'?30:10,sw=type==='thick'?8:type==='double'?4:2;const o=new fabric.Rect({left:pad,top:pad,width:w-pad*2,height:h-pad*2,fill:'transparent',stroke:'#10b46a',strokeWidth:sw,rx:4,ry:4});deCanvas.add(o);if(type==='double'){const o2=new fabric.Rect({left:pad+10,top:pad+10,width:w-pad*2-20,height:h-pad*2-20,fill:'transparent',stroke:'#10b46a',strokeWidth:2,rx:2,ry:2});deCanvas.add(o2);}deCanvas.setActiveObject(o);deCanvas.renderAll();}

/* ── Images ── */
function deUploadImg(e){const f=e.target.files[0];if(!f)return;const r=new FileReader();r.onload=ev=>{fabric.Image.fromURL(ev.target.result,img=>{img.scaleToWidth(Math.min(360,deCanvas.width/2));img.set({left:60,top:60});deCanvas.add(img);deCanvas.setActiveObject(img);deCanvas.renderAll();});};r.readAsDataURL(f);e.target.value='';}
function deAddImgUrl(){const url=document.getElementById('de-img-url').value.trim();if(!url)return;fabric.Image.fromURL(url,img=>{img.scaleToWidth(Math.min(360,deCanvas.width/2));img.set({left:60,top:60});deCanvas.add(img);deCanvas.setActiveObject(img);deCanvas.renderAll();},{crossOrigin:'anonymous'});document.getElementById('de-img-url').value='';}

/* ── Background ── */
function deSetBg(c){deCanvas.setBackgroundColor(c,()=>deCanvas.renderAll());}
function deSetBgGradient(c1,c2){const grad=new fabric.Gradient({type:'linear',gradientUnits:'pixels',coords:{x1:0,y1:0,x2:deCanvas.width,y2:deCanvas.height},colorStops:[{offset:0,color:c1},{offset:1,color:c2}]});deCanvas.setBackgroundColor(grad,()=>deCanvas.renderAll());}

/* ── History ── */
function deSaveHist(){const j=JSON.stringify(deCanvas.toJSON(['selectable','hasControls','shadow','globalCompositeOperation']));deHist=deHist.slice(0,deHistIdx+1);deHist.push(j);deHistIdx=deHist.length-1;}
function deUndo(){if(deHistIdx<=0)return;deHistIdx--;deCanvas.loadFromJSON(deHist[deHistIdx],()=>{deCanvas.renderAll();deDrawGrid();});}
function deRedo(){if(deHistIdx>=deHist.length-1)return;deHistIdx++;deCanvas.loadFromJSON(deHist[deHistIdx],()=>{deCanvas.renderAll();deDrawGrid();});}
function deClear(){if(!confirm('مسح كل محتوى اللوحة؟'))return;deCanvas.clear();deCanvas.setBackgroundColor('#ffffff',()=>deCanvas.renderAll());document.getElementById('de-bg-picker').value='#ffffff';document.getElementById('de-bg-picker2').value='#ffffff';deSaveHist();}

/* ── Object actions ── */
function deDeleteSelected(){const obj=deCanvas.getActiveObject();if(!obj)return;if(obj.type==='activeSelection'){obj.getObjects().forEach(o=>deCanvas.remove(o));deCanvas.discardActiveObject();}else deCanvas.remove(obj);deCanvas.renderAll();}
function deLayer(a){const obj=deCanvas.getActiveObject();if(!obj)return;const fn={front:'bringToFront',forward:'bringForward',backward:'sendBackwards',back:'sendToBack'}[a];if(fn)deCanvas[fn](obj);deCanvas.renderAll();}
function deFlip(ax){const obj=deCanvas.getActiveObject();if(!obj)return;obj.set('flip'+ax.toUpperCase(),!obj['flip'+ax.toUpperCase()]);deCanvas.renderAll();}
function deDuplicate(){const obj=deCanvas.getActiveObject();if(!obj)return;obj.clone(c=>{c.set({left:obj.left+22,top:obj.top+22});deCanvas.add(c);deCanvas.setActiveObject(c);deCanvas.renderAll();});}
function deGroup(){const obj=deCanvas.getActiveObject();if(!obj)return;if(obj.type==='activeSelection'){const g=obj.toGroup();deCanvas.setActiveObject(g);deCanvas.renderAll();}else if(obj.type==='group'){const objs=obj.getObjects();obj.toActiveSelection();deCanvas.setActiveObject(new fabric.ActiveSelection(objs,{canvas:deCanvas}));deCanvas.renderAll();}}
function deAlign(dir){const obj=deCanvas.getActiveObject();if(!obj)return;const cw=deCanvas.getWidth(),ch=deCanvas.getHeight();const ow=obj.getScaledWidth?obj.getScaledWidth():obj.width,oh=obj.getScaledHeight?obj.getScaledHeight():obj.height;if(dir==='left')obj.set('left',cw-ow/2);/* RTL: left edge of canvas is visual right */
if(dir==='right')obj.set('left',ow/2);
if(dir==='hcenter')obj.set('left',cw/2,{originX:'center'});
if(dir==='top')obj.set('top',oh/2);
if(dir==='bottom')obj.set('top',ch-oh/2);
if(dir==='vcenter')obj.set({top:ch/2},{originY:'center'});
/* simpler centering: */
if(dir==='hcenter'){obj.centerH();}
if(dir==='vcenter'){obj.centerV();}
deCanvas.renderAll();deSaveHist();}

/* ── Properties Panel ── */
function deOnSelect(){const obj=deCanvas.getActiveObject();if(!obj)return;const isText=obj.type==='i-text'||obj.type==='text';const isImg=obj.type==='image';deShowPanel(true,isText,isImg);document.getElementById('de-align-bar').classList.add('show');deUpdatePanel();}
function deOnDeselect(){deShowPanel(false,false,false);document.getElementById('de-align-bar').classList.remove('show');}
function deShowPanel(has,isText,isImg){document.getElementById('de-ps-empty').style.display=has?'none':'block';document.getElementById('de-ps-pos').style.display=has?'block':'none';document.getElementById('de-ps-fill').style.display=has?'block':'none';document.getElementById('de-ps-text').style.display=isText?'block':'none';document.getElementById('de-ps-img').style.display=isImg?'block':'none';document.getElementById('de-ps-shadow').style.display=has?'block':'none';document.getElementById('de-ps-layer').style.display=has?'block':'none';}

function deUpdatePanel(){
    const obj=deCanvas.getActiveObject();if(!obj)return;
    const isText=obj.type==='i-text'||obj.type==='text';
    const isRect=obj.type==='rect';

    set('pp-x',Math.round(obj.left||0));set('pp-y',Math.round(obj.top||0));
    set('pp-w',Math.round(obj.getScaledWidth?obj.getScaledWidth():(obj.width||0)));
    set('pp-h',Math.round(obj.getScaledHeight?obj.getScaledHeight():(obj.height||0)));
    set('pp-angle',Math.round(obj.angle||0));

    const fc=typeof obj.fill==='string'&&obj.fill.startsWith('#')?obj.fill:'#222222';
    set('pp-fill',fc);
    const sc=obj.stroke&&typeof obj.stroke==='string'&&obj.stroke.startsWith('#')?obj.stroke:'#000000';
    set('pp-stroke',sc);set('pp-stroke-w',obj.strokeWidth||0);
    const op=obj.opacity!==undefined?obj.opacity:1;
    set('pp-opacity',op);setTxt('pp-opacity-v',Math.round(op*100)+'%');
    set('pp-blend',obj.globalCompositeOperation||'source-over');

    // corner radius
    document.getElementById('pp-radius-row').style.display=isRect?'block':'none';
    if(isRect)set('pp-radius',obj.rx||0);

    if(isText){
        set('pp-font',obj.fontFamily||'Cairo');set('pp-fsize',obj.fontSize||24);
        const tc=obj.fill&&typeof obj.fill==='string'&&obj.fill.startsWith('#')?obj.fill:'#000000';
        set('pp-fcolor',tc);
        setActive('pp-bold',obj.fontWeight==='bold');setActive('pp-italic',obj.fontStyle==='italic');
        setActive('pp-under',!!obj.underline);setActive('pp-strike',!!obj.linethrough);
        ['ar','ac','al','aj'].forEach((id,i)=>setActive('pp-'+id,obj.textAlign===['right','center','left','justify'][i]));
        const cs=(obj.charSpacing||0)/10;set('pp-cspacing',cs);setTxt('pp-cspacing-v',cs.toFixed(1));
        const lh=obj.lineHeight||1.2;set('pp-lheight',lh);setTxt('pp-lheight-v',(+lh).toFixed(1));
        set('pp-txt-stroke',obj.stroke&&obj.stroke.startsWith('#')?obj.stroke:'#000000');
        set('pp-txt-stroke-w',obj.strokeWidth||0);
    }

    const hasSh=!!obj.shadow;setChk('pp-shadow-on',hasSh);
    document.getElementById('pp-shadow-opts').style.display=hasSh?'block':'none';
    if(hasSh&&obj.shadow){
        set('pp-sh-color',obj.shadow.color||'#000000');
        set('pp-sh-blur',obj.shadow.blur||10);
        set('pp-sh-x',obj.shadow.offsetX||5);
        set('pp-sh-y',obj.shadow.offsetY||5);
    }
}

const set=(id,v)=>{const el=document.getElementById(id);if(el)el.value=v;};
const setTxt=(id,v)=>{const el=document.getElementById(id);if(el)el.textContent=v;};
const setChk=(id,v)=>{const el=document.getElementById(id);if(el)el.checked=v;};
const setActive=(id,v)=>{document.getElementById(id)?.classList.toggle('active',!!v);};

function ppSet(prop,val){const obj=deCanvas.getActiveObject();if(!obj)return;obj.set(prop,val);deCanvas.renderAll();}
function ppApplyXY(){ppSet('left',+document.getElementById('pp-x').value);ppSet('top',+document.getElementById('pp-y').value);}
function ppApplyWH(){const obj=deCanvas.getActiveObject();if(!obj)return;const w=+document.getElementById('pp-w').value,h=+document.getElementById('pp-h').value;obj.set({scaleX:w/(obj.width||1),scaleY:h/(obj.height||1)});deCanvas.renderAll();}
function ppToggle(p,a,b){const obj=deCanvas.getActiveObject();if(!obj)return;obj.set(p,obj[p]===a?b:a);deCanvas.renderAll();deUpdatePanel();}
function ppToggleProp(p){const obj=deCanvas.getActiveObject();if(!obj)return;obj.set(p,!obj[p]);deCanvas.renderAll();deUpdatePanel();}
function ppSetAlign(a){ppSet('textAlign',a);deUpdatePanel();}
function ppToggleShadow(on){const obj=deCanvas.getActiveObject();if(!obj)return;document.getElementById('pp-shadow-opts').style.display=on?'block':'none';if(on)ppApplyShadow();else{obj.set('shadow',null);deCanvas.renderAll();}}
function ppApplyShadow(){const obj=deCanvas.getActiveObject();if(!obj)return;const op=+document.getElementById('pp-sh-opacity').value;const color=document.getElementById('pp-sh-color').value;const hex2rgb=h=>parseInt(h.slice(1,3),16)+','+parseInt(h.slice(3,5),16)+','+parseInt(h.slice(5,7),16);obj.set('shadow',new fabric.Shadow({color:`rgba(${hex2rgb(color)},${op})`,blur:+document.getElementById('pp-sh-blur').value,offsetX:+document.getElementById('pp-sh-x').value,offsetY:+document.getElementById('pp-sh-y').value}));deCanvas.renderAll();setTxt('pp-sh-op-v',Math.round(op*100)+'%');}
function ppToggleGlow(on){const obj=deCanvas.getActiveObject();if(!obj)return;document.getElementById('pp-glow-opts').style.display=on?'block':'none';if(on)ppApplyGlow();else{obj.set('shadow',null);deCanvas.renderAll();}}
function ppApplyGlow(){const obj=deCanvas.getActiveObject();if(!obj)return;const c=document.getElementById('pp-glow-color').value,b=+document.getElementById('pp-glow-blur').value;obj.set('shadow',new fabric.Shadow({color:c,blur:b,offsetX:0,offsetY:0}));deCanvas.renderAll();}

/* ── Gradient fill ── */
function switchFillTab(tab){['solid','gradient','none'].forEach(t=>{document.getElementById('ftab-'+t).classList.toggle('active',t===tab);});document.getElementById('fill-solid-opts').style.display=tab==='solid'?'block':'none';document.getElementById('fill-gradient-opts').style.display=tab==='gradient'?'block':'none';if(tab==='none')ppSet('fill','transparent');}
function ppApplyGradient(){const obj=deCanvas.getActiveObject();if(!obj)return;const c1=document.getElementById('pp-grad-c1').value,c2=document.getElementById('pp-grad-c2').value,ang=+document.getElementById('pp-grad-angle').value;const rad=ang*Math.PI/180;const w=obj.width||100,h=obj.height||100;const grad=new fabric.Gradient({type:'linear',gradientUnits:'pixels',coords:{x1:w/2*Math.cos(rad+Math.PI)+w/2,y1:h/2*Math.sin(rad+Math.PI)+h/2,x2:w/2*Math.cos(rad)+w/2,y2:h/2*Math.sin(rad)+h/2},colorStops:[{offset:0,color:c1},{offset:1,color:c2}]});obj.set('fill',grad);deCanvas.renderAll();document.getElementById('pp-grad-preview').style.background=`linear-gradient(${ang}deg,${c1},${c2})`;}

/* ── Stroke dash ── */
function ppApplyDash(style){const obj=deCanvas.getActiveObject();if(!obj)return;const sw=obj.strokeWidth||1;const dashes={solid:null,dashed:[sw*4,sw*2],dotted:[sw,sw*2]};obj.set('strokeDashArray',dashes[style]);deCanvas.renderAll();}

/* ── Image filters ── */
function ppApplyImgFilter(){const obj=deCanvas.getActiveObject();if(!obj||obj.type!=='image')return;obj.filters=[];const g=id=>+document.getElementById(id).value;if(g('pp-f-brightness')!==0)obj.filters.push(new fabric.Image.filters.Brightness({brightness:g('pp-f-brightness')}));if(g('pp-f-contrast')!==0)obj.filters.push(new fabric.Image.filters.Contrast({contrast:g('pp-f-contrast')}));if(g('pp-f-saturation')!==0)obj.filters.push(new fabric.Image.filters.Saturation({saturation:g('pp-f-saturation')}));if(g('pp-f-blur')!==0)obj.filters.push(new fabric.Image.filters.Blur({blur:g('pp-f-blur')}));if(g('pp-f-hue')!==0)obj.filters.push(new fabric.Image.filters.HueRotation({rotation:g('pp-f-hue')}));obj.applyFilters();deCanvas.renderAll();}
function ppResetFilters(){['pp-f-brightness','pp-f-contrast','pp-f-saturation','pp-f-blur','pp-f-hue'].forEach(id=>{const el=document.getElementById(id);if(el){el.value=0;const vEl=document.getElementById(id+'-v');if(vEl)vEl.textContent='0.00';}});ppApplyImgFilter();}

/* ── Effects tab switch ── */
function switchEffTab(tab){['shadow','glow'].forEach(t=>{document.getElementById('efx-'+t).classList.toggle('active',t===tab);document.getElementById('eff-'+t+'-opts').style.display=t===tab?'block':'none';});}

/* ── Submit ── */
function submitTemplate(){
    const nameEl=document.querySelector('[name="name"]');
    if(!nameEl?.value.trim()){alert('يرجى إدخال اسم القالب أولاً');nameEl?.focus();return;}
    deCanvas.discardActiveObject();deCanvas.renderAll();
    document.getElementById('f_canvas_json').value=JSON.stringify(deCanvas.toJSON(['selectable','hasControls','shadow','globalCompositeOperation','strokeDashArray']));
    const m=400/Math.max(deCanvas.width,deCanvas.height);
    document.getElementById('f_thumbnail').value=deCanvas.toDataURL({format:'png',quality:.88,multiplier:m});
    document.getElementById('tmpl-form').submit();
}
</script>
@endpush
