<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>محرر التصميم — ايليت دعاية</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800;900&family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<style>
:root{
  --ed-green:#10b46a; --ed-accent:#a78bfa;
  --ed-bg:#141817; --ed-cbg:#1d2220;
  --ed-toolbar:#0e1210; --ed-sidebar:#161b19; --ed-panel:#1a1f1d;
  --ed-border:#2a3330; --ed-border2:rgba(34,41,38,.6);
  --ed-text:#e2ede6; --ed-text2:#7a9282; --ed-text3:#4a6055;
  --ed-inp:#202825; --ed-inp-h:#252e2a;
  --ed-danger:#e05c5c; --ed-warn:#f59e0b;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%;font-family:'Cairo',sans-serif;direction:rtl}
body{background:var(--ed-bg);color:var(--ed-text);display:flex;flex-direction:column;overflow:hidden}

/* ── Topbar ── */
.de-topbar{
  background:var(--ed-toolbar);border-bottom:1px solid var(--ed-border);
  display:flex;align-items:center;gap:3px;padding:6px 10px;
  overflow-x:auto;flex-shrink:0;min-height:48px
}
.de-topbar::-webkit-scrollbar{height:3px}
.de-topbar::-webkit-scrollbar-thumb{background:var(--ed-border);border-radius:4px}

.tb{
  display:inline-flex;align-items:center;gap:5px;padding:5px 9px;
  background:transparent;border:1px solid transparent;border-radius:7px;
  color:var(--ed-text2);font-family:'Cairo',sans-serif;font-size:12px;font-weight:600;
  cursor:pointer;white-space:nowrap;flex-shrink:0;transition:all .13s
}
.tb:hover{background:var(--ed-inp);border-color:var(--ed-border);color:var(--ed-text)}
.tb.on{background:rgba(16,180,106,.15);border-color:var(--ed-green);color:var(--ed-green)}
.tb.primary{background:var(--ed-green);border-color:var(--ed-green);color:#fff}
.tb.primary:hover{background:#0c9456}
.tb.danger{background:rgba(224,92,92,.15);border-color:var(--ed-danger);color:var(--ed-danger)}
.tb i{font-size:12px}
.tb-sep{width:1px;height:26px;background:var(--ed-border);margin:0 4px;flex-shrink:0}
.tb-logo{font-size:14px;font-weight:900;color:var(--ed-green);margin-right:auto;padding-right:12px;white-space:nowrap;flex-shrink:0}
.tb-logo a{color:inherit;text-decoration:none}

.de-sz{
  background:var(--ed-inp);border:1px solid var(--ed-border);border-radius:7px;
  color:var(--ed-text);font-family:'Cairo',sans-serif;font-size:11px;padding:5px 8px;
  cursor:pointer;outline:none;flex-shrink:0
}
.de-zoom-lbl{font-size:11px;color:var(--ed-text2);flex-shrink:0;min-width:32px;text-align:center}

/* align bar — shown only when object selected */
#de-align-bar{display:none;align-items:center;gap:3px}

/* draw opts — shown only in draw mode */
#de-draw-opts{display:none;align-items:center;gap:6px}
#de-draw-opts label{font-size:11px;color:var(--ed-text2)}
#de-draw-opts input[type=range]{width:80px;accent-color:var(--ed-green)}

/* shapes dropdown */
.de-shapes-dd{position:relative;flex-shrink:0}
.de-shapes-menu{
  display:none;position:absolute;top:calc(100% + 4px);right:0;
  background:var(--ed-panel);border:1px solid var(--ed-border);border-radius:10px;
  padding:8px;z-index:200;min-width:170px;box-shadow:0 6px 24px rgba(0,0,0,.45)
}
.de-shapes-menu.open{display:grid;grid-template-columns:repeat(3,1fr);gap:5px}
.de-shapes-menu button{
  display:flex;flex-direction:column;align-items:center;gap:4px;padding:7px 4px;
  background:var(--ed-inp);border:1px solid var(--ed-border);border-radius:7px;
  color:var(--ed-text2);font-size:10px;cursor:pointer;transition:all .13s
}
.de-shapes-menu button:hover{background:var(--ed-inp-h);border-color:var(--ed-green);color:var(--ed-green)}
.de-shapes-menu button i{font-size:16px}

/* ── 3-panel body ── */
.de-body{display:flex;flex:1;min-height:0}

/* ── Left sidebar ── */
.de-sidebar{
  width:200px;flex-shrink:0;background:var(--ed-sidebar);
  border-inline-end:1px solid var(--ed-border);
  display:flex;flex-direction:column;overflow:hidden
}
.de-sb-tabs{display:flex;flex-wrap:wrap;border-bottom:1px solid var(--ed-border);padding:4px 4px 0}
.de-sb-tab{
  flex:1;min-width:30px;padding:6px 2px;text-align:center;font-size:10px;
  color:var(--ed-text3);cursor:pointer;border-bottom:2px solid transparent;
  transition:all .13s
}
.de-sb-tab:hover{color:var(--ed-text2)}
.de-sb-tab.on{color:var(--ed-green);border-bottom-color:var(--ed-green)}
.de-sb-tab i{display:block;font-size:14px;margin-bottom:2px}
.de-sb-panes{flex:1;overflow-y:auto;padding:10px}
.de-sb-pane{display:none}
.de-sb-pane.on{display:block}

.de-sb-panes::-webkit-scrollbar{width:4px}
.de-sb-panes::-webkit-scrollbar-thumb{background:var(--ed-border);border-radius:4px}

/* sidebar elements */
.sb-sec{font-size:10px;font-weight:700;color:var(--ed-text3);text-transform:uppercase;
  letter-spacing:.5px;margin:10px 0 6px}
.sb-sec:first-child{margin-top:0}
.sb-btn-row{display:flex;gap:5px;margin-bottom:6px;flex-wrap:wrap}
.sb-btn{
  flex:1;display:flex;align-items:center;justify-content:center;gap:4px;
  padding:7px 6px;background:var(--ed-inp);border:1px solid var(--ed-border);
  border-radius:7px;color:var(--ed-text2);font-family:'Cairo',sans-serif;
  font-size:11px;cursor:pointer;transition:all .13s;text-align:center;white-space:nowrap
}
.sb-btn:hover{background:var(--ed-inp-h);border-color:var(--ed-green);color:var(--ed-green)}
.sb-btn i{font-size:13px}

/* styled text presets */
.sb-styled-grid{display:grid;grid-template-columns:1fr 1fr;gap:5px;margin-bottom:8px}
.sb-styled{
  padding:7px 6px;background:var(--ed-inp);border:1px solid var(--ed-border);
  border-radius:7px;cursor:pointer;text-align:center;font-size:11px;
  transition:all .13s;color:var(--ed-text)
}
.sb-styled:hover{border-color:var(--ed-green);background:var(--ed-inp-h)}

/* font chips */
.sb-fonts{display:flex;flex-wrap:wrap;gap:4px;margin-bottom:6px}
.sb-font{
  padding:3px 9px;border-radius:20px;font-size:11px;cursor:pointer;
  background:var(--ed-inp);border:1px solid var(--ed-border);color:var(--ed-text2);
  transition:all .13s
}
.sb-font.on,.sb-font:hover{background:rgba(16,180,106,.15);border-color:var(--ed-green);color:var(--ed-green)}

/* shapes grid */
.sb-shapes-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:5px;margin-bottom:8px}
.sb-shape{
  display:flex;flex-direction:column;align-items:center;gap:3px;padding:8px 4px;
  background:var(--ed-inp);border:1px solid var(--ed-border);border-radius:7px;
  cursor:pointer;font-size:10px;color:var(--ed-text2);transition:all .13s
}
.sb-shape:hover{border-color:var(--ed-green);color:var(--ed-green);background:var(--ed-inp-h)}
.sb-shape i{font-size:18px}

/* upload zone */
.sb-upload{
  border:2px dashed var(--ed-border);border-radius:8px;padding:16px 10px;
  text-align:center;cursor:pointer;margin-bottom:8px;transition:border-color .2s
}
.sb-upload:hover{border-color:var(--ed-green)}
.sb-upload i{font-size:22px;color:var(--ed-text3);display:block;margin-bottom:4px}
.sb-upload p{font-size:11px;color:var(--ed-text2)}
.sb-upload input{display:none}

/* bg swatches */
.sb-swatches{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:8px}
.sb-swatch{
  width:26px;height:26px;border-radius:5px;cursor:pointer;
  border:2px solid rgba(255,255,255,.1);transition:border-color .13s;flex-shrink:0
}
.sb-swatch:hover{border-color:var(--ed-green)}
.sb-gradients{display:grid;grid-template-columns:repeat(4,1fr);gap:5px;margin-bottom:8px}
.sb-grad{
  height:28px;border-radius:5px;cursor:pointer;border:2px solid transparent;transition:border-color .13s
}
.sb-grad:hover{border-color:var(--ed-green)}

/* template grid */
.sb-tmpl-grid{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:8px}
.sb-tmpl{
  border-radius:7px;overflow:hidden;cursor:pointer;
  border:2px solid var(--ed-border);transition:border-color .13s;
  background:var(--ed-inp);padding:4px
}
.sb-tmpl:hover{border-color:var(--ed-green)}
.sb-tmpl img,.sb-tmpl .sb-tmpl-ph{width:100%;aspect-ratio:1;object-fit:cover;border-radius:4px;display:block}
.sb-tmpl .sb-tmpl-ph{background:var(--ed-inp-h);display:flex;align-items:center;justify-content:center;color:var(--ed-text3)}
.sb-tmpl-name{font-size:9px;text-align:center;color:var(--ed-text2);margin-top:3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}

/* layers */
.sb-layer{
  display:flex;align-items:center;gap:6px;padding:6px 7px;border-radius:7px;
  font-size:11px;color:var(--ed-text2);cursor:pointer;
  border:1px solid transparent;transition:all .13s;margin-bottom:3px
}
.sb-layer:hover{background:var(--ed-inp)}
.sb-layer.on{background:rgba(16,180,106,.12);border-color:var(--ed-green);color:var(--ed-green)}
.sb-layer-ico{font-size:11px;flex-shrink:0}
.sb-layer-name{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.sb-layer-del{
  width:20px;height:20px;border-radius:4px;background:transparent;border:none;
  color:var(--ed-text3);cursor:pointer;font-size:10px;
  display:flex;align-items:center;justify-content:center
}
.sb-layer-del:hover{background:rgba(224,92,92,.2);color:var(--ed-danger)}

/* ── Canvas area ── */
.de-canvas-area{
  flex:1;min-width:0;background:var(--ed-cbg);overflow:auto;
  padding:24px;display:flex;align-items:flex-start;justify-content:center;position:relative
}
#de-canvas-wrap{transform-origin:top center;flex-shrink:0;box-shadow:0 6px 32px rgba(0,0,0,.5);position:relative}

/* ── Right panel ── */
.de-panel{
  width:280px;flex-shrink:0;background:var(--ed-panel);
  border-inline-start:1px solid var(--ed-border);overflow-y:auto;font-size:12px
}
.de-panel::-webkit-scrollbar{width:4px}
.de-panel::-webkit-scrollbar-thumb{background:var(--ed-border);border-radius:4px}

.pp-sec{padding:10px 12px;border-bottom:1px solid var(--ed-border2)}
.pp-lbl{font-size:10px;font-weight:700;color:var(--ed-text3);text-transform:uppercase;
  letter-spacing:.5px;margin-bottom:8px;display:flex;align-items:center;justify-content:space-between}
.pp-row{display:flex;align-items:center;gap:6px;margin-bottom:6px}
.pp-row label{font-size:11px;color:var(--ed-text2);flex-shrink:0;min-width:40px}
.pp-inp{
  flex:1;background:var(--ed-inp);border:1px solid var(--ed-border);border-radius:6px;
  color:var(--ed-text);font-family:'Cairo',sans-serif;font-size:12px;padding:4px 8px;outline:none
}
.pp-inp:focus{border-color:var(--ed-green)}
input[type=color].pp-clr{
  width:32px;height:28px;padding:2px;border-radius:5px;cursor:pointer;
  background:var(--ed-inp);border:1px solid var(--ed-border);flex-shrink:0
}
input[type=range].pp-range{width:100%;accent-color:var(--ed-green);flex:1}
.pp-num{
  width:56px;background:var(--ed-inp);border:1px solid var(--ed-border);border-radius:6px;
  color:var(--ed-text);font-family:'Cairo',sans-serif;font-size:11px;padding:4px 6px;
  outline:none;text-align:center
}
.pp-num:focus{border-color:var(--ed-green)}

.pp-tabs{display:flex;gap:2px;margin-bottom:8px}
.pp-tab{
  flex:1;padding:5px 4px;border-radius:6px;background:var(--ed-inp);
  border:1px solid var(--ed-border);color:var(--ed-text2);font-size:11px;
  cursor:pointer;text-align:center;transition:all .13s
}
.pp-tab.on{background:rgba(16,180,106,.15);border-color:var(--ed-green);color:var(--ed-green)}

.pp-align-row{display:flex;gap:3px;margin-bottom:6px}
.pp-align{
  flex:1;padding:6px;border-radius:6px;background:var(--ed-inp);border:1px solid var(--ed-border);
  color:var(--ed-text2);cursor:pointer;text-align:center;font-size:12px;transition:all .13s
}
.pp-align:hover{border-color:var(--ed-green);color:var(--ed-green)}

.pp-btn{
  display:flex;align-items:center;justify-content:center;gap:5px;
  width:100%;padding:6px 10px;border-radius:7px;background:var(--ed-inp);
  border:1px solid var(--ed-border);color:var(--ed-text2);font-family:'Cairo',sans-serif;
  font-size:11px;cursor:pointer;transition:all .13s;margin-top:4px
}
.pp-btn:hover{border-color:var(--ed-green);color:var(--ed-text)}
.pp-btn.danger{border-color:var(--ed-danger);color:var(--ed-danger)}
.pp-btn.danger:hover{background:rgba(224,92,92,.12)}

.pp-dash-row{display:flex;gap:4px;margin-bottom:6px}
.pp-dash{
  flex:1;padding:5px 3px;border-radius:5px;background:var(--ed-inp);
  border:1px solid var(--ed-border);color:var(--ed-text2);font-size:10px;
  cursor:pointer;text-align:center;transition:all .13s
}
.pp-dash.on,.pp-dash:hover{border-color:var(--ed-green);color:var(--ed-green)}

.pp-bold-row{display:flex;gap:3px;margin-bottom:6px}
.pp-bold{
  flex:1;padding:5px;border-radius:5px;background:var(--ed-inp);
  border:1px solid var(--ed-border);color:var(--ed-text2);font-size:13px;
  cursor:pointer;text-align:center;transition:all .13s
}
.pp-bold.on,.pp-bold:hover{border-color:var(--ed-green);color:var(--ed-green)}

/* empty state */
.pp-empty{padding:28px 12px;text-align:center;color:var(--ed-text3)}
.pp-empty i{font-size:28px;display:block;margin-bottom:8px;opacity:.5}
.pp-empty p{font-size:11px}

/* ── Toast ── */
.de-toast{
  position:fixed;bottom:22px;left:50%;transform:translateX(-50%);
  background:var(--ed-panel);border:1px solid var(--ed-border);border-radius:9px;
  padding:9px 18px;font-size:12px;font-weight:600;color:var(--ed-text);
  box-shadow:0 4px 20px rgba(0,0,0,.4);display:none;z-index:9999;white-space:nowrap
}

/* scrollbars */
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:var(--ed-border);border-radius:10px}

@media(max-width:900px){
  .de-sidebar{display:none}
  .de-panel{width:220px}
}
@media(max-width:640px){
  .de-panel{display:none}
}
</style>
</head>
<body>

<!-- ══════════ TOPBAR ══════════ -->
<div class="de-topbar">

  <!-- select / draw -->
  <button class="tb on" id="sf-tool-select" onclick="sfTool('select')" title="اختيار (V)">
    <i class="fa fa-arrow-pointer"></i>
  </button>
  <button class="tb" id="sf-tool-draw" onclick="sfTool('draw')" title="رسم حر">
    <i class="fa fa-pen"></i>
  </button>

  <!-- draw options (hidden unless draw mode) -->
  <div id="de-draw-opts">
    <label>لون</label>
    <input type="color" id="sf-brush-clr" value="#10b46a" oninput="sfUpdateBrush()">
    <label>سُمك</label>
    <input type="range" min="1" max="40" value="4" id="sf-brush-w" oninput="sfUpdateBrush()">
  </div>

  <div class="tb-sep"></div>

  <!-- shapes dropdown -->
  <div class="de-shapes-dd">
    <button class="tb" onclick="sfToggleShapesMenu()" title="أشكال">
      <i class="fa fa-shapes"></i><span>أشكال</span><i class="fa fa-chevron-down" style="font-size:9px"></i>
    </button>
    <div class="de-shapes-menu" id="sf-shapes-menu">
      <button onclick="sfAddRect();sfCloseShapes()"><i class="fa fa-square"></i>مستطيل</button>
      <button onclick="sfAddRounded();sfCloseShapes()"><i class="fa fa-square" style="border-radius:4px"></i>مدوّر</button>
      <button onclick="sfAddCircle();sfCloseShapes()"><i class="fa fa-circle"></i>دائرة</button>
      <button onclick="sfAddTriangle();sfCloseShapes()"><i class="fa fa-play fa-rotate-270"></i>مثلث</button>
      <button onclick="sfAddDiamond();sfCloseShapes()"><i class="fa fa-diamond"></i>معيّن</button>
      <button onclick="sfAddStar();sfCloseShapes()"><i class="fa fa-star"></i>نجمة</button>
      <button onclick="sfAddLine();sfCloseShapes()"><i class="fa fa-minus"></i>خط</button>
      <button onclick="sfAddArrow();sfCloseShapes()"><i class="fa fa-arrow-left"></i>سهم</button>
      <button onclick="sfAddFrame('simple');sfCloseShapes()"><i class="fa fa-border-all"></i>إطار</button>
    </div>
  </div>

  <!-- image -->
  <button class="tb" onclick="sfTriggerUpload()" title="رفع صورة">
    <i class="fa fa-image"></i><span>صورة</span>
  </button>
  <input type="file" id="sf-img-input" accept="image/*" style="display:none" onchange="sfUploadImage(event)">

  <!-- background -->
  <label class="tb" title="لون الخلفية" style="cursor:pointer">
    <i class="fa fa-fill-drip"></i><span>خلفية</span>
    <input type="color" id="sf-bg-picker" value="#ffffff" oninput="sfSetBg(this.value)" style="width:0;height:0;opacity:0;position:absolute">
  </label>

  <!-- grid -->
  <button class="tb" id="sf-grid-btn" onclick="sfToggleGrid()" title="شبكة">
    <i class="fa fa-grid-4"></i>
  </button>

  <div class="tb-sep"></div>

  <!-- canvas size -->
  <select class="de-sz" id="sf-size-sel" onchange="sfApplySizeFromSelect(this.value)" title="حجم اللوحة">
    <option value="800x800">مربع 800</option>
    <option value="1200x630">بانر ويب</option>
    <option value="1080x1920">ستوري</option>
    <option value="1080x1080">انستقرام</option>
    <option value="900x300">لافتة</option>
    <option value="400x400">ملصق</option>
    <option value="595x842">A4 بورتريه</option>
    <option value="842x595">A4 لاندسكيب</option>
  </select>

  <!-- zoom -->
  <button class="tb" onclick="sfZoomFit()" title="ملاءمة"><i class="fa fa-expand"></i></button>
  <button class="tb" onclick="sfZoomStep(-0.1)" title="تصغير"><i class="fa fa-minus" style="font-size:10px"></i></button>
  <span class="de-zoom-lbl" id="sf-zoom-lbl">100%</span>
  <button class="tb" onclick="sfZoomStep(0.1)" title="تكبير"><i class="fa fa-plus" style="font-size:10px"></i></button>

  <div class="tb-sep"></div>

  <!-- undo/redo -->
  <button class="tb" onclick="sfUndo()" title="تراجع Ctrl+Z"><i class="fa fa-rotate-left"></i></button>
  <button class="tb" onclick="sfRedo()" title="إعادة Ctrl+Y"><i class="fa fa-rotate-right"></i></button>

  <!-- align bar — visible when object selected -->
  <div id="de-align-bar">
    <div class="tb-sep"></div>
    <button class="tb" onclick="sfAlign('left')" title="محاذاة يسار"><i class="fa fa-align-left"></i></button>
    <button class="tb" onclick="sfAlign('hcenter')" title="توسيط أفقي"><i class="fa fa-align-center"></i></button>
    <button class="tb" onclick="sfAlign('right')" title="محاذاة يمين"><i class="fa fa-align-right"></i></button>
    <button class="tb" onclick="sfAlign('top')" title="محاذاة أعلى"><i class="fa fa-arrow-up"></i></button>
    <button class="tb" onclick="sfAlign('vcenter')" title="توسيط رأسي"><i class="fa fa-arrows-up-down"></i></button>
    <button class="tb" onclick="sfAlign('bottom')" title="محاذاة أسفل"><i class="fa fa-arrow-down"></i></button>
    <button class="tb" onclick="sfClone()" title="نسخ Ctrl+D"><i class="fa fa-copy"></i></button>
    <button class="tb" onclick="sfGroup()" title="تجميع/فك" id="sf-group-btn"><i class="fa fa-object-group"></i></button>
    <button class="tb danger" onclick="sfDelete()" title="حذف Del"><i class="fa fa-trash"></i></button>
  </div>

  <div class="tb-sep"></div>

  <!-- save / export -->
  <button class="tb" onclick="sfSaveDesign()" title="حفظ التصميم">
    <i class="fa fa-floppy-disk"></i><span>حفظ</span>
  </button>
  <button class="tb primary" onclick="sfExportPNG()" title="تنزيل PNG">
    <i class="fa fa-download"></i><span>تنزيل</span>
  </button>

  <div class="tb-sep"></div>
  <div class="tb-logo"><a href="{{ url('/') }}">✦ ايليت</a></div>
</div>

<!-- ══════════ BODY ══════════ -->
<div class="de-body">

  <!-- ── LEFT SIDEBAR ── -->
  <div class="de-sidebar">
    <div class="de-sb-tabs">
      <div class="de-sb-tab on"  onclick="sfTab('text')"      title="نص">    <i class="fa fa-font"></i>نص</div>
      <div class="de-sb-tab"     onclick="sfTab('shapes')"    title="أشكال"> <i class="fa fa-shapes"></i>أشكال</div>
      <div class="de-sb-tab"     onclick="sfTab('image')"     title="صور">   <i class="fa fa-image"></i>صور</div>
      <div class="de-sb-tab"     onclick="sfTab('bg')"        title="خلفية"> <i class="fa fa-fill-drip"></i>خلفية</div>
      <div class="de-sb-tab"     onclick="sfTab('templates')" title="قوالب"> <i class="fa fa-palette"></i>قوالب</div>
      <div class="de-sb-tab"     onclick="sfTab('layers')"    title="طبقات"> <i class="fa fa-layer-group"></i>طبقات</div>
    </div>
    <div class="de-sb-panes">

      <!-- TEXT pane -->
      <div class="de-sb-pane on" id="sf-pane-text">
        <div class="sb-sec">إضافة نص</div>
        <div class="sb-btn-row">
          <button class="sb-btn" onclick="sfMkText('عنوان رئيسي',{fontSize:36,fontWeight:'bold'})">
            <i class="fa fa-heading"></i>عنوان
          </button>
          <button class="sb-btn" onclick="sfMkText('نص فرعي',{fontSize:22})">
            <i class="fa fa-font"></i>فرعي
          </button>
        </div>
        <button class="sb-btn" style="width:100%;margin-bottom:8px" onclick="sfMkText('نص عادي',{fontSize:16})">
          <i class="fa fa-align-right"></i>نص عادي
        </button>

        <div class="sb-sec">نصوص مصمّمة</div>
        <div class="sb-styled-grid">
          <div class="sb-styled" onclick="sfAddStyledText('sale')">🏷️ خصم</div>
          <div class="sb-styled" onclick="sfAddStyledText('badge')">⭐ شارة</div>
          <div class="sb-styled" onclick="sfAddStyledText('price')">💰 سعر</div>
          <div class="sb-styled" onclick="sfAddStyledText('title')">🎨 عنوان</div>
          <div class="sb-styled" onclick="sfAddStyledText('quote')">💬 اقتباس</div>
          <div class="sb-styled" onclick="sfAddStyledText('label')">📌 ملصق</div>
        </div>

        <div class="sb-sec">الخط</div>
        <div class="sb-fonts" id="sf-font-chips"></div>
      </div>

      <!-- SHAPES pane -->
      <div class="de-sb-pane" id="sf-pane-shapes">
        <div class="sb-sec">أشكال</div>
        <div class="sb-shapes-grid">
          <div class="sb-shape" onclick="sfAddRect()"><i class="fa fa-square"></i>مستطيل</div>
          <div class="sb-shape" onclick="sfAddRounded()"><i class="fa fa-square" style="border-radius:3px"></i>مدوّر</div>
          <div class="sb-shape" onclick="sfAddCircle()"><i class="fa fa-circle"></i>دائرة</div>
          <div class="sb-shape" onclick="sfAddTriangle()"><i class="fa fa-play fa-rotate-270"></i>مثلث</div>
          <div class="sb-shape" onclick="sfAddDiamond()"><i class="fa fa-diamond"></i>معيّن</div>
          <div class="sb-shape" onclick="sfAddStar()"><i class="fa fa-star"></i>نجمة</div>
          <div class="sb-shape" onclick="sfAddLine()"><i class="fa fa-minus"></i>خط</div>
          <div class="sb-shape" onclick="sfAddArrow()"><i class="fa fa-arrow-left"></i>سهم</div>
          <div class="sb-shape" onclick="sfAddHeart()"><i class="fa fa-heart"></i>قلب</div>
        </div>

        <div class="sb-sec">إطارات</div>
        <div class="sb-shapes-grid">
          <div class="sb-shape" onclick="sfAddFrame('simple')"><i class="fa fa-border-all"></i>بسيط</div>
          <div class="sb-shape" onclick="sfAddFrame('double')"><i class="fa fa-border-outer"></i>مزدوج</div>
          <div class="sb-shape" onclick="sfAddFrame('rounded')"><i class="fa fa-square-full"></i>مدوّر</div>
        </div>
      </div>

      <!-- IMAGE pane -->
      <div class="de-sb-pane" id="sf-pane-image">
        <div class="sb-sec">رفع صورة</div>
        <div class="sb-upload" onclick="sfTriggerUpload()">
          <input type="file" id="sf-img-input2" accept="image/*" onchange="sfUploadImage(event)">
          <i class="fa fa-cloud-arrow-up"></i>
          <p>اضغط لرفع صورة من جهازك</p>
        </div>

        <div class="sb-sec">من رابط URL</div>
        <div style="display:flex;gap:5px;margin-bottom:8px">
          <input id="sf-url-inp" class="pp-inp" placeholder="https://..." style="flex:1">
          <button class="sb-btn" style="flex:0 0 auto;padding:7px 10px" onclick="sfAddImageUrl()">
            <i class="fa fa-plus"></i>
          </button>
        </div>

        <div class="sb-sec">شعار المتجر</div>
        <button class="sb-btn" style="width:100%" onclick="sfAddLogoPlaceholder()">
          <i class="fa fa-store"></i> أضف شعار المتجر
        </button>
      </div>

      <!-- BACKGROUND pane -->
      <div class="de-sb-pane" id="sf-pane-bg">
        <div class="sb-sec">لون مخصص</div>
        <div class="pp-row" style="margin-bottom:10px">
          <label>اختر</label>
          <input type="color" class="pp-clr" id="sf-bg-picker2" value="#ffffff"
                 oninput="sfSetBg(this.value);document.getElementById('sf-bg-picker').value=this.value">
        </div>

        <div class="sb-sec">ألوان جاهزة</div>
        <div class="sb-swatches">
          @foreach(['#ffffff','#000000','#1a1a2e','#16213e','#e74c3c','#e67e22','#f39c12','#27ae60','#2980b9','#8e44ad','#f5f0eb','#2c3e50','#ecf0f1','#bdc3c7','#d35400','#c0392b','#1abc9c','#3498db','#9b59b6','#34495e'] as $c)
          <div class="sb-swatch" style="background:{{$c}}" onclick="sfSetBg('{{$c}}');document.getElementById('sf-bg-picker').value='{{$c}}';document.getElementById('sf-bg-picker2').value='{{$c}}'"></div>
          @endforeach
        </div>

        <div class="sb-sec">تدرجات لونية</div>
        <div class="sb-gradients">
          <div class="sb-grad" style="background:linear-gradient(135deg,#667eea,#764ba2)" onclick="sfSetBgGradient('#667eea','#764ba2')"></div>
          <div class="sb-grad" style="background:linear-gradient(135deg,#f093fb,#f5576c)" onclick="sfSetBgGradient('#f093fb','#f5576c')"></div>
          <div class="sb-grad" style="background:linear-gradient(135deg,#4facfe,#00f2fe)" onclick="sfSetBgGradient('#4facfe','#00f2fe')"></div>
          <div class="sb-grad" style="background:linear-gradient(135deg,#43e97b,#38f9d7)" onclick="sfSetBgGradient('#43e97b','#38f9d7')"></div>
          <div class="sb-grad" style="background:linear-gradient(135deg,#fa709a,#fee140)" onclick="sfSetBgGradient('#fa709a','#fee140')"></div>
          <div class="sb-grad" style="background:linear-gradient(135deg,#a18cd1,#fbc2eb)" onclick="sfSetBgGradient('#a18cd1','#fbc2eb')"></div>
          <div class="sb-grad" style="background:linear-gradient(135deg,#ffd89b,#19547b)" onclick="sfSetBgGradient('#ffd89b','#19547b')"></div>
          <div class="sb-grad" style="background:linear-gradient(135deg,#f5af19,#f12711)" onclick="sfSetBgGradient('#f5af19','#f12711')"></div>
          <div class="sb-grad" style="background:linear-gradient(135deg,#0f0c29,#302b63,#24243e)" onclick="sfSetBgGradient('#302b63','#24243e')"></div>
          <div class="sb-grad" style="background:linear-gradient(135deg,#11998e,#38ef7d)" onclick="sfSetBgGradient('#11998e','#38ef7d')"></div>
          <div class="sb-grad" style="background:linear-gradient(135deg,#373b44,#4286f4)" onclick="sfSetBgGradient('#373b44','#4286f4')"></div>
          <div class="sb-grad" style="background:linear-gradient(135deg,#ee9ca7,#ffdde1)" onclick="sfSetBgGradient('#ee9ca7','#ffdde1')"></div>
        </div>
      </div>

      <!-- TEMPLATES pane -->
      <div class="de-sb-pane" id="sf-pane-templates">
        <div class="sb-sec">قوالب جاهزة</div>
        <div id="sf-tmpl-grid">
          <div style="text-align:center;padding:20px 0;color:var(--ed-text2)">
            <i class="fa fa-spinner fa-spin" style="font-size:22px;display:block;margin-bottom:6px"></i>
            جاري تحميل القوالب...
          </div>
        </div>
      </div>

      <!-- LAYERS pane -->
      <div class="de-sb-pane" id="sf-pane-layers">
        <div class="sb-sec">الطبقات</div>
        <div id="sf-layers-list"></div>
      </div>

    </div><!-- .de-sb-panes -->
  </div><!-- .de-sidebar -->

  <!-- ── CANVAS AREA ── -->
  <div class="de-canvas-area" id="sf-canvas-area">
    <div id="de-canvas-wrap">
      <canvas id="sf-main-canvas"></canvas>
      <canvas id="sf-grid-canvas" style="position:absolute;top:0;left:0;pointer-events:none;display:none"></canvas>
    </div>
  </div>

  <!-- ── RIGHT PANEL ── -->
  <div class="de-panel" id="sf-panel">

    <!-- empty state -->
    <div id="sf-ps-empty" class="pp-empty">
      <i class="fa fa-arrow-pointer"></i>
      <p>اختر عنصراً لتعديل خصائصه</p>
    </div>

    <!-- position / size -->
    <div id="sf-ps-pos" class="pp-sec" style="display:none">
      <div class="pp-lbl">موضع وحجم
        <button class="tb" style="padding:2px 6px;font-size:10px" onclick="sfToggleAspect()" id="sf-lock-btn" title="قفل النسبة">
          <i class="fa fa-lock-open" id="sf-lock-ico"></i>
        </button>
      </div>
      <div class="pp-row">
        <label>X</label><input type="number" class="pp-num" id="sf-px" oninput="sfSetXY()">
        <label>Y</label><input type="number" class="pp-num" id="sf-py" oninput="sfSetXY()">
      </div>
      <div class="pp-row">
        <label>عرض</label><input type="number" class="pp-num" id="sf-pw" oninput="sfSetWH()">
        <label>ارتفاع</label><input type="number" class="pp-num" id="sf-ph" oninput="sfSetWH()">
      </div>
      <div class="pp-row">
        <label>دوران</label>
        <input type="number" class="pp-inp" id="sf-prot" min="-360" max="360" oninput="sfSetProp('angle',+this.value)">
      </div>
      <div class="pp-row">
        <label>شفافية</label>
        <input type="range" class="pp-range" min="0" max="1" step=".05" id="sf-pop" oninput="sfSetProp('opacity',+this.value)">
      </div>
    </div>

    <!-- fill -->
    <div id="sf-ps-fill" class="pp-sec" style="display:none">
      <div class="pp-lbl">التعبئة</div>
      <div class="pp-tabs">
        <div class="pp-tab on" id="sf-fill-solid" onclick="sfSwitchFill('solid')">صلب</div>
        <div class="pp-tab" id="sf-fill-grad"  onclick="sfSwitchFill('grad')">تدرج</div>
        <div class="pp-tab" id="sf-fill-none"  onclick="sfSwitchFill('none')">شفاف</div>
      </div>
      <!-- solid -->
      <div id="sf-fill-solid-pane">
        <div class="pp-row">
          <label>لون</label>
          <input type="color" class="pp-clr" id="sf-fill-clr" oninput="sfSetFillSolid(this.value)">
        </div>
      </div>
      <!-- gradient -->
      <div id="sf-fill-grad-pane" style="display:none">
        <div class="pp-row">
          <label>لون 1</label><input type="color" class="pp-clr" id="sf-grad-c1" value="#10b46a">
          <label>لون 2</label><input type="color" class="pp-clr" id="sf-grad-c2" value="#a78bfa">
        </div>
        <div class="pp-row">
          <label>زاوية</label>
          <input type="range" class="pp-range" min="0" max="360" value="135" id="sf-grad-ang">
        </div>
        <button class="pp-btn" onclick="sfApplyGradient()"><i class="fa fa-paint-roller"></i> تطبيق</button>
      </div>

      <div class="pp-lbl" style="margin-top:8px">الحدود</div>
      <div class="pp-row">
        <label>لون</label><input type="color" class="pp-clr" id="sf-stroke-clr" oninput="sfSetProp('stroke',this.value)">
        <label>سُمك</label><input type="number" class="pp-num" id="sf-stroke-w" min="0" max="50" oninput="sfSetProp('strokeWidth',+this.value)">
      </div>
      <div class="pp-lbl" style="margin-top:6px">نمط الخط</div>
      <div class="pp-dash-row">
        <div class="pp-dash on" id="sf-dash-solid" onclick="sfApplyDash('solid')">─ صلب</div>
        <div class="pp-dash" id="sf-dash-dashed" onclick="sfApplyDash('dashed')">- - مقطّع</div>
        <div class="pp-dash" id="sf-dash-dotted" onclick="sfApplyDash('dotted')">··· نقاط</div>
      </div>

      <div class="pp-lbl" style="margin-top:6px">زوايا مدوّرة</div>
      <div class="pp-row">
        <label>rx</label><input type="number" class="pp-num" id="sf-rx" min="0" max="200" oninput="sfSetProp('rx',+this.value);sfSetProp('ry',+this.value)">
      </div>

      <div class="pp-lbl" style="margin-top:6px">مزج الطبقة</div>
      <select class="pp-inp" id="sf-blend" onchange="sfSetProp('globalCompositeOperation',this.value)">
        <option value="source-over">عادي</option>
        <option value="multiply">ضرب</option>
        <option value="screen">شاشة</option>
        <option value="overlay">تراكب</option>
        <option value="darken">تعتيم</option>
        <option value="lighten">تفتيح</option>
        <option value="color-dodge">إضاءة لون</option>
        <option value="color-burn">حرق لون</option>
        <option value="hard-light">ضوء قوي</option>
        <option value="soft-light">ضوء ناعم</option>
        <option value="difference">فرق</option>
        <option value="exclusion">استثناء</option>
      </select>
    </div>

    <!-- text props -->
    <div id="sf-ps-text" class="pp-sec" style="display:none">
      <div class="pp-lbl">النص</div>
      <div class="pp-row">
        <label>حجم</label>
        <input type="number" class="pp-num" id="sf-fsize" min="6" max="400" oninput="sfSetProp('fontSize',+this.value)">
        <label>لون</label>
        <input type="color" class="pp-clr" id="sf-fclr" oninput="sfSetProp('fill',this.value)">
      </div>
      <div class="pp-bold-row">
        <div class="pp-bold" id="sf-btn-bold" onclick="sfToggleBold()"><b>B</b></div>
        <div class="pp-bold" id="sf-btn-italic" onclick="sfToggleItalic()"><i>I</i></div>
        <div class="pp-bold" id="sf-btn-under" onclick="sfToggleUnderline()"><u>U</u></div>
      </div>
      <div class="pp-row" style="margin-top:4px">
        <label>محاذاة</label>
        <div style="display:flex;gap:3px;flex:1">
          <button class="pp-align" onclick="sfSetProp('textAlign','right')"><i class="fa fa-align-right"></i></button>
          <button class="pp-align" onclick="sfSetProp('textAlign','center')"><i class="fa fa-align-center"></i></button>
          <button class="pp-align" onclick="sfSetProp('textAlign','left')"><i class="fa fa-align-left"></i></button>
        </div>
      </div>
      <div class="pp-row">
        <label>اتجاه</label>
        <button class="pp-align" onclick="sfSetDir('rtl')"><i class="fa fa-align-right"></i> عربي</button>
        <button class="pp-align" onclick="sfSetDir('ltr')"><i class="fa fa-align-left"></i> لاتيني</button>
      </div>
      <div class="pp-row">
        <label>تباعد</label>
        <input type="number" class="pp-num" id="sf-char-sp" step="1" oninput="sfSetProp('charSpacing',+this.value)">
        <label>أسطر</label>
        <input type="number" class="pp-num" id="sf-line-h" step=".1" min=".5" max="4" oninput="sfSetProp('lineHeight',+this.value)">
      </div>
    </div>

    <!-- image props -->
    <div id="sf-ps-img" class="pp-sec" style="display:none">
      <div class="pp-lbl">فلاتر الصورة
        <button class="pp-btn" style="width:auto;padding:2px 8px;font-size:10px;margin:0" onclick="sfResetFilters()">إعادة تعيين</button>
      </div>
      <div class="pp-row"><label>إضاءة</label><input type="range" class="pp-range" min="-1" max="1" step=".05" value="0" id="sf-f-bright" oninput="sfApplyFilters()"></div>
      <div class="pp-row"><label>تباين</label><input type="range" class="pp-range" min="-1" max="1" step=".05" value="0" id="sf-f-cont"   oninput="sfApplyFilters()"></div>
      <div class="pp-row"><label>تشبّع</label><input type="range" class="pp-range" min="-1" max="1" step=".05" value="0" id="sf-f-sat"    oninput="sfApplyFilters()"></div>
      <div class="pp-row"><label>ضبابية</label><input type="range" class="pp-range" min="0"  max="1" step=".02" value="0" id="sf-f-blur"   oninput="sfApplyFilters()"></div>
      <div class="pp-row"><label>لون</label><input type="range" class="pp-range" min="-2" max="2" step=".1"  value="0" id="sf-f-hue"    oninput="sfApplyFilters()"></div>
      <button class="pp-btn" onclick="document.getElementById('sf-img-input').click()">
        <i class="fa fa-swap"></i> تغيير الصورة
      </button>
    </div>

    <!-- shadow / glow -->
    <div id="sf-ps-shadow" class="pp-sec" style="display:none">
      <div class="pp-lbl">الظل والتوهج</div>
      <div class="pp-tabs">
        <div class="pp-tab on" id="sf-eff-shadow" onclick="sfSwitchEff('shadow')">ظل</div>
        <div class="pp-tab" id="sf-eff-glow" onclick="sfSwitchEff('glow')">توهج</div>
      </div>
      <!-- shadow -->
      <div id="sf-eff-shadow-pane">
        <div class="pp-row"><label>لون</label><input type="color" class="pp-clr" id="sf-sh-clr" value="#000000"></div>
        <div class="pp-row"><label>إزاحة X</label><input type="range" class="pp-range" min="-30" max="30" value="5" id="sf-sh-x"></div>
        <div class="pp-row"><label>إزاحة Y</label><input type="range" class="pp-range" min="-30" max="30" value="5" id="sf-sh-y"></div>
        <div class="pp-row"><label>ضبابية</label><input type="range" class="pp-range" min="0"   max="30" value="8" id="sf-sh-blur"></div>
        <div class="pp-row"><label>شفافية</label><input type="range" class="pp-range" min="0"   max="1"  step=".05" value=".5" id="sf-sh-op"></div>
        <div style="display:flex;gap:4px;margin-top:6px">
          <button class="pp-btn" onclick="sfApplyShadow()"><i class="fa fa-check"></i> تطبيق</button>
          <button class="pp-btn danger" onclick="sfRemoveShadow()"><i class="fa fa-xmark"></i> إزالة</button>
        </div>
      </div>
      <!-- glow -->
      <div id="sf-eff-glow-pane" style="display:none">
        <div class="pp-row"><label>لون</label><input type="color" class="pp-clr" id="sf-gl-clr" value="#10b46a"></div>
        <div class="pp-row"><label>حجم</label><input type="range" class="pp-range" min="0" max="40" value="12" id="sf-gl-size"></div>
        <div class="pp-row"><label>شفافية</label><input type="range" class="pp-range" min="0" max="1" step=".05" value=".8" id="sf-gl-op"></div>
        <div style="display:flex;gap:4px;margin-top:6px">
          <button class="pp-btn" onclick="sfApplyGlow(true)"><i class="fa fa-sparkles"></i> تطبيق</button>
          <button class="pp-btn danger" onclick="sfApplyGlow(false)"><i class="fa fa-xmark"></i> إزالة</button>
        </div>
      </div>
    </div>

    <!-- layer order -->
    <div id="sf-ps-layer" class="pp-sec" style="display:none">
      <div class="pp-lbl">ترتيب الطبقة</div>
      <div class="pp-align-row">
        <button class="pp-align" onclick="sfCanvas.bringToFront(sfCanvas.getActiveObject());sfCanvas.renderAll()" title="للمقدمة"><i class="fa fa-angle-double-up"></i></button>
        <button class="pp-align" onclick="sfCanvas.bringForward(sfCanvas.getActiveObject());sfCanvas.renderAll()" title="للأمام"><i class="fa fa-angle-up"></i></button>
        <button class="pp-align" onclick="sfCanvas.sendBackwards(sfCanvas.getActiveObject());sfCanvas.renderAll()" title="للخلف"><i class="fa fa-angle-down"></i></button>
        <button class="pp-align" onclick="sfCanvas.sendToBack(sfCanvas.getActiveObject());sfCanvas.renderAll()" title="للخلفية"><i class="fa fa-angle-double-down"></i></button>
      </div>
      <button class="pp-btn danger" onclick="sfDelete()" style="margin-top:4px">
        <i class="fa fa-trash"></i> حذف العنصر
      </button>
    </div>

  </div><!-- .de-panel -->

</div><!-- .de-body -->

<div class="de-toast" id="sf-toast"></div>

<script>
/* ═══════════════════════════════════════════════════
   STOREFRONT DESIGN EDITOR — ايليت دعاية
   Advanced Canva-like editor for customers
   ═══════════════════════════════════════════════════ */

const SF_PRODUCT_ID          = {{ isset($productId)         && $productId         ? (int)$productId         : 'null' }};
const SF_PRODUCT_CATEGORY_ID = {{ isset($productCategoryId) && $productCategoryId ? (int)$productCategoryId : 'null' }};
const SF_TEMPLATE_JSON       = @json($template->canvas_json ?? null);
const SF_TEMPLATE_W          = {{ $template->canvas_width  ?? 800 }};
const SF_TEMPLATE_H          = {{ $template->canvas_height ?? 800 }};

let sfCanvas;
let sfHist = [], sfHistIdx = -1;
let sfZoom = 1, sfAspectLocked = false, sfGridOn = false;
let _sfTemplates = [];

const SF_FONTS = [
  'Cairo','Tajawal','Amiri','Arial','Georgia',
  'Courier New','Impact','Tahoma','Verdana'
];

/* ─────────────────────────────────────
   INIT
───────────────────────────────────── */
window.addEventListener('DOMContentLoaded', () => {
  sfCanvas = new fabric.Canvas('sf-main-canvas', {
    backgroundColor: '#ffffff',
    selection: true,
    preserveObjectStacking: true,
  });

  sfApplySize(SF_TEMPLATE_W || 800, SF_TEMPLATE_H || 800);
  sfBuildFontChips();
  sfBindEvents();
  sfLoadTemplates();

  // Auto-load the product's assigned template first
  if (SF_TEMPLATE_JSON) {
    sfCanvas.loadFromJSON(SF_TEMPLATE_JSON, () => {
      sfCanvas.renderAll();
      sfSaveHistory();
      showToast('تم تحميل قالب المنتج — يمكنك التعديل عليه');
      // sync bg picker if background is a solid color
      const bg = sfCanvas.backgroundColor;
      if (typeof bg === 'string' && bg.startsWith('#')) {
        document.getElementById('sf-bg-picker').value  = bg;
        document.getElementById('sf-bg-picker2').value = bg;
      }
      // match size selector
      const szSel = document.getElementById('sf-size-sel');
      const szVal = `${SF_TEMPLATE_W}x${SF_TEMPLATE_H}`;
      if (szSel) { [...szSel.options].forEach(o => o.selected = o.value === szVal); }
    });
  } else {
    // No product template — restore auto-saved design if any
    try {
      const saved = localStorage.getItem('sf_design_json');
      if (saved) {
        sfCanvas.loadFromJSON(saved, () => sfCanvas.renderAll());
        showToast('تم استعادة آخر تصميم');
      }
    } catch(e) {}
    sfSaveHistory();
  }

  // Auto-save every 30s
  setInterval(() => {
    try { localStorage.setItem('sf_design_json', JSON.stringify(sfCanvas.toJSON())); } catch(e){}
  }, 30000);

  // Resize observer
  const area = document.getElementById('sf-canvas-area');
  if (window.ResizeObserver) {
    new ResizeObserver(() => sfZoomFit()).observe(area);
  } else {
    window.addEventListener('resize', sfZoomFit);
  }
});

/* ─────────────────────────────────────
   CANVAS SIZE & ZOOM
───────────────────────────────────── */
function sfApplySize(w, h) {
  sfCanvas.setWidth(w);
  sfCanvas.setHeight(h);

  // hidden inputs (if embedded in a form)
  const iw = document.getElementById('sf-canvas-w');
  const ih = document.getElementById('sf-canvas-h');
  if (iw) iw.value = w;
  if (ih) ih.value = h;

  const gc = document.getElementById('sf-grid-canvas');
  if (gc) { gc.width = w; gc.height = h; }

  sfCanvas.renderAll();
  sfZoomFit();
}

function sfApplySizeFromSelect(val) {
  const [w, h] = val.split('x').map(Number);
  sfApplySize(w, h);
}

function sfZoomFit() {
  const area  = document.getElementById('sf-canvas-area');
  const wrap  = document.getElementById('de-canvas-wrap');
  const avail = Math.max(60, area.clientWidth  - 48);
  const availH= Math.max(60, area.clientHeight - 48);
  const scale = Math.min(1, avail / sfCanvas.width, availH / sfCanvas.height);
  sfZoom = scale;
  wrap.style.transform       = `scale(${scale})`;
  wrap.style.transformOrigin = 'top center';
  wrap.style.width           = sfCanvas.width  + 'px';
  wrap.style.height          = sfCanvas.height + 'px';
  document.getElementById('sf-zoom-lbl').textContent = Math.round(scale * 100) + '%';
  if (sfGridOn) sfDrawGrid();
}

function sfZoomStep(delta) {
  const wrap = document.getElementById('de-canvas-wrap');
  sfZoom = Math.max(0.1, Math.min(3, sfZoom + delta));
  wrap.style.transform       = `scale(${sfZoom})`;
  wrap.style.transformOrigin = 'top center';
  document.getElementById('sf-zoom-lbl').textContent = Math.round(sfZoom * 100) + '%';
}

/* ─────────────────────────────────────
   GRID
───────────────────────────────────── */
function sfDrawGrid() {
  const gc  = document.getElementById('sf-grid-canvas');
  if (!gc) return;
  const ctx = gc.getContext('2d');
  const step = 40;
  ctx.clearRect(0, 0, gc.width, gc.height);
  ctx.strokeStyle = 'rgba(255,255,255,0.12)';
  ctx.lineWidth = 1;
  for (let x = 0; x <= gc.width; x += step) {
    ctx.beginPath(); ctx.moveTo(x,0); ctx.lineTo(x, gc.height); ctx.stroke();
  }
  for (let y = 0; y <= gc.height; y += step) {
    ctx.beginPath(); ctx.moveTo(0,y); ctx.lineTo(gc.width,y); ctx.stroke();
  }
}

function sfToggleGrid() {
  sfGridOn = !sfGridOn;
  const gc  = document.getElementById('sf-grid-canvas');
  const btn = document.getElementById('sf-grid-btn');
  if (sfGridOn) {
    gc.style.display = 'block';
    sfDrawGrid();
    btn.classList.add('on');
  } else {
    gc.style.display = 'none';
    btn.classList.remove('on');
  }
}

/* ─────────────────────────────────────
   TOOLS (select / draw)
───────────────────────────────────── */
function sfTool(t) {
  document.getElementById('sf-tool-select').classList.toggle('on', t === 'select');
  document.getElementById('sf-tool-draw').classList.toggle('on',   t === 'draw');
  const opts = document.getElementById('de-draw-opts');
  if (t === 'draw') {
    sfCanvas.isDrawingMode = true;
    sfCanvas.freeDrawingBrush = new fabric.PencilBrush(sfCanvas);
    sfUpdateBrush();
    opts.style.display = 'flex';
  } else {
    sfCanvas.isDrawingMode = false;
    opts.style.display = 'none';
  }
}

function sfUpdateBrush() {
  if (!sfCanvas.freeDrawingBrush) return;
  sfCanvas.freeDrawingBrush.color = document.getElementById('sf-brush-clr').value;
  sfCanvas.freeDrawingBrush.width = +document.getElementById('sf-brush-w').value;
}

/* ─────────────────────────────────────
   SHAPES DROPDOWN
───────────────────────────────────── */
function sfToggleShapesMenu() {
  document.getElementById('sf-shapes-menu').classList.toggle('open');
}
function sfCloseShapes() {
  document.getElementById('sf-shapes-menu').classList.remove('open');
}
document.addEventListener('click', e => {
  if (!e.target.closest('.de-shapes-dd')) sfCloseShapes();
});

/* ─────────────────────────────────────
   SIDEBAR TABS
───────────────────────────────────── */
function sfTab(name) {
  const names = ['text','shapes','image','bg','templates','layers'];
  names.forEach(n => {
    document.getElementById('sf-pane-' + n).classList.toggle('on', n === name);
  });
  document.querySelectorAll('.de-sb-tab').forEach((el, i) => {
    el.classList.toggle('on', names[i] === name);
  });
  if (name === 'layers') sfRefreshLayers();
  if (name === 'templates') sfLoadTemplates();
}

/* ─────────────────────────────────────
   TEXT
───────────────────────────────────── */
function sfMkText(txt, opts = {}) {
  const obj = new fabric.IText(txt, Object.assign({
    right: 40, top: 40,
    fontFamily: 'Cairo',
    fontSize: 24,
    fontWeight: 'normal',
    fill: '#000000',
    textAlign: 'right',
    direction: 'rtl',
    originX: 'right',
    selectable: true,
  }, opts));
  sfCanvas.add(obj);
  sfCanvas.setActiveObject(obj);
  obj.enterEditing();
  sfCanvas.renderAll();
  sfSaveHistory();
}

function sfAddStyledText(style) {
  const styles = {
    sale:  { txt:'خصم 50%',     fs:40, fw:'900',   fill:'#e05c5c', bg:'#fff',      rx:8  },
    badge: { txt:'⭐ الأفضل',   fs:20, fw:'700',   fill:'#fff',    bg:'#f59e0b',   rx:20 },
    price: { txt:'١٩٩ ريال',   fs:36, fw:'800',   fill:'#10b46a', bg:'transparent', rx:0 },
    title: { txt:'عنوان رئيسي',fs:38, fw:'900',   fill:'#1a1a2e', bg:'transparent', rx:0 },
    quote: { txt:'"نص اقتباس"', fs:22, fw:'400',   fill:'#555',   bg:'transparent', rx:0 },
    label: { txt:'تسمية',       fs:14, fw:'700',   fill:'#fff',   bg:'#8e44ad',   rx:20 },
  };
  const s = styles[style];
  if (!s) return;
  const obj = new fabric.IText(s.txt, {
    right: 100, top: 100,
    fontFamily: 'Cairo',
    fontSize: s.fs,
    fontWeight: s.fw,
    fill: s.fill,
    textAlign: 'right',
    direction: 'rtl',
    originX: 'right',
    backgroundColor: s.bg !== 'transparent' ? s.bg : '',
  });
  sfCanvas.add(obj);
  sfCanvas.setActiveObject(obj);
  sfCanvas.renderAll();
  sfSaveHistory();
}

/* ─────────────────────────────────────
   SHAPES
───────────────────────────────────── */
function sfAddRect() {
  sfCanvas.add(new fabric.Rect({ left:80, top:80, width:200, height:120,
    fill:'#10b46a', stroke:'transparent', strokeWidth:0 }));
  sfCanvas.setActiveObject(sfCanvas.getObjects()[sfCanvas.getObjects().length-1]);
  sfCanvas.renderAll(); sfSaveHistory();
}
function sfAddRounded() {
  sfCanvas.add(new fabric.Rect({ left:80, top:80, width:200, height:120,
    fill:'#a78bfa', stroke:'transparent', strokeWidth:0, rx:20, ry:20 }));
  sfCanvas.setActiveObject(sfCanvas.getObjects()[sfCanvas.getObjects().length-1]);
  sfCanvas.renderAll(); sfSaveHistory();
}
function sfAddCircle() {
  sfCanvas.add(new fabric.Circle({ left:100, top:100, radius:80,
    fill:'#4facfe', stroke:'transparent', strokeWidth:0 }));
  sfCanvas.setActiveObject(sfCanvas.getObjects()[sfCanvas.getObjects().length-1]);
  sfCanvas.renderAll(); sfSaveHistory();
}
function sfAddTriangle() {
  sfCanvas.add(new fabric.Triangle({ left:100, top:80, width:160, height:140,
    fill:'#f093fb', stroke:'transparent', strokeWidth:0 }));
  sfCanvas.setActiveObject(sfCanvas.getObjects()[sfCanvas.getObjects().length-1]);
  sfCanvas.renderAll(); sfSaveHistory();
}
function sfAddDiamond() {
  sfCanvas.add(new fabric.Polygon(
    [{x:80,y:0},{x:160,y:80},{x:80,y:160},{x:0,y:80}],
    { left:100, top:100, fill:'#f59e0b', stroke:'transparent', strokeWidth:0 }
  ));
  sfCanvas.setActiveObject(sfCanvas.getObjects()[sfCanvas.getObjects().length-1]);
  sfCanvas.renderAll(); sfSaveHistory();
}
function sfAddStar() {
  const pts = [];
  for (let i = 0; i < 10; i++) {
    const a = (Math.PI / 5) * i - Math.PI / 2;
    const r = i % 2 === 0 ? 80 : 36;
    pts.push({ x: Math.cos(a) * r, y: Math.sin(a) * r });
  }
  sfCanvas.add(new fabric.Polygon(pts, { left:100, top:100,
    fill:'#ffd89b', stroke:'transparent', strokeWidth:0 }));
  sfCanvas.setActiveObject(sfCanvas.getObjects()[sfCanvas.getObjects().length-1]);
  sfCanvas.renderAll(); sfSaveHistory();
}
function sfAddLine() {
  sfCanvas.add(new fabric.Line([50,50,350,50],
    { stroke:'#000000', strokeWidth:3, selectable:true }));
  sfCanvas.setActiveObject(sfCanvas.getObjects()[sfCanvas.getObjects().length-1]);
  sfCanvas.renderAll(); sfSaveHistory();
}
function sfAddArrow() {
  const path = new fabric.Path('M 0 0 L 150 0 M 110 -30 L 150 0 L 110 30', {
    left:100, top:100, stroke:'#000000', strokeWidth:4,
    fill:'transparent', selectable:true
  });
  sfCanvas.add(path);
  sfCanvas.setActiveObject(path);
  sfCanvas.renderAll(); sfSaveHistory();
}
function sfAddHeart() {
  const path = new fabric.Path(
    'M 0,-30 C 0,-70 -60,-70 -60,-30 C -60,10 0,50 0,70 C 0,50 60,10 60,-30 C 60,-70 0,-70 0,-30 Z',
    { left:100, top:80, fill:'#e05c5c', stroke:'transparent', strokeWidth:0 }
  );
  sfCanvas.add(path);
  sfCanvas.setActiveObject(path);
  sfCanvas.renderAll(); sfSaveHistory();
}
function sfAddFrame(type) {
  const cw = sfCanvas.width, ch = sfCanvas.height;
  const m  = type === 'double' ? 30 : 20;
  const r  = type === 'rounded' ? 24 : 4;
  const frame = new fabric.Rect({
    left:m, top:m, width:cw-m*2, height:ch-m*2,
    fill:'transparent', stroke:'#000000', strokeWidth: type === 'double' ? 2 : 3,
    rx:r, ry:r, selectable:true
  });
  sfCanvas.add(frame);
  if (type === 'double') {
    const inner = new fabric.Rect({
      left:m+10, top:m+10, width:cw-m*2-20, height:ch-m*2-20,
      fill:'transparent', stroke:'#000000', strokeWidth:1, rx:r, ry:r
    });
    sfCanvas.add(inner);
  }
  sfCanvas.renderAll(); sfSaveHistory();
}

/* ─────────────────────────────────────
   IMAGE
───────────────────────────────────── */
function sfTriggerUpload() {
  document.getElementById('sf-img-input').click();
}
function sfUploadImage(event) {
  const file = event.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    fabric.Image.fromURL(e.target.result, img => {
      img.scaleToWidth(Math.min(320, sfCanvas.width * .45));
      img.set({ left:60, top:60 });
      sfCanvas.add(img);
      sfCanvas.setActiveObject(img);
      sfCanvas.renderAll();
      sfSaveHistory();
    });
  };
  reader.readAsDataURL(file);
  event.target.value = '';
}
function sfAddImageUrl() {
  const url = document.getElementById('sf-url-inp').value.trim();
  if (!url) return;
  fabric.Image.fromURL(url, img => {
    if (!img) { showToast('فشل تحميل الصورة'); return; }
    img.scaleToWidth(Math.min(320, sfCanvas.width * .45));
    img.set({ left:60, top:60, crossOrigin:'anonymous' });
    sfCanvas.add(img);
    sfCanvas.setActiveObject(img);
    sfCanvas.renderAll();
    sfSaveHistory();
  }, { crossOrigin:'anonymous' });
  document.getElementById('sf-url-inp').value = '';
}
function sfAddLogoPlaceholder() {
  const obj = new fabric.Rect({
    left:20, top:20, width:160, height:60,
    fill:'rgba(16,180,106,.15)', stroke:'#10b46a', strokeWidth:2,
    rx:8, ry:8, selectable:true
  });
  sfCanvas.add(obj);
  const lbl = new fabric.IText('[ شعار المتجر ]', {
    left:100, top:50, fontFamily:'Cairo', fontSize:14,
    fill:'#10b46a', textAlign:'center', originX:'center', originY:'center'
  });
  sfCanvas.add(lbl);
  sfCanvas.renderAll(); sfSaveHistory();
}

/* ─────────────────────────────────────
   BACKGROUND
───────────────────────────────────── */
function sfSetBg(color) {
  sfCanvas.setBackgroundColor(color, () => sfCanvas.renderAll());
  sfSaveHistory();
}
function sfSetBgGradient(c1, c2) {
  const grad = new fabric.Gradient({
    type:'linear',
    coords:{ x1:0, y1:0, x2:sfCanvas.width, y2:sfCanvas.height },
    colorStops:[ {offset:0, color:c1}, {offset:1, color:c2} ]
  });
  sfCanvas.setBackgroundColor(grad, () => sfCanvas.renderAll());
  sfSaveHistory();
}

/* ─────────────────────────────────────
   GROUP / UNGROUP
───────────────────────────────────── */
function sfGroup() {
  const obj = sfCanvas.getActiveObject();
  if (!obj) return;
  if (obj.type === 'activeSelection') {
    obj.toGroup();
    sfCanvas.requestRenderAll();
    sfSaveHistory();
    showToast('تم التجميع');
  } else if (obj.type === 'group') {
    obj.toActiveSelection();
    sfCanvas.requestRenderAll();
    sfSaveHistory();
    showToast('تم فك التجميع');
  }
}

/* ─────────────────────────────────────
   ALIGN
───────────────────────────────────── */
function sfAlign(dir) {
  const obj = sfCanvas.getActiveObject();
  if (!obj) return;
  const cw = sfCanvas.width, ch = sfCanvas.height;
  const ow = obj.getScaledWidth(), oh = obj.getScaledHeight();
  if (dir === 'left')    obj.set({ left:0,            originX:'left'  });
  if (dir === 'right')   obj.set({ left:cw - ow,      originX:'left'  });
  if (dir === 'hcenter') obj.viewportCenter ? obj.centerH() : obj.set({ left:(cw-ow)/2, originX:'left' });
  if (dir === 'top')     obj.set({ top:0,             originY:'top'   });
  if (dir === 'bottom')  obj.set({ top:ch - oh,       originY:'top'   });
  if (dir === 'vcenter') obj.viewportCenter ? obj.centerV() : obj.set({ top:(ch-oh)/2, originY:'top' });
  obj.setCoords();
  sfCanvas.renderAll();
  sfSaveHistory();
}

/* ─────────────────────────────────────
   ASPECT RATIO LOCK
───────────────────────────────────── */
function sfToggleAspect() {
  sfAspectLocked = !sfAspectLocked;
  document.getElementById('sf-lock-ico').className = sfAspectLocked
    ? 'fa fa-lock' : 'fa fa-lock-open';
  showToast(sfAspectLocked ? 'قُفلت النسبة' : 'فُكّت النسبة');
}

/* ─────────────────────────────────────
   CANVAS EVENTS
───────────────────────────────────── */
function sfBindEvents() {
  sfCanvas.on('selection:created',  sfOnSelect);
  sfCanvas.on('selection:updated',  sfOnSelect);
  sfCanvas.on('selection:cleared',  sfOnClear);
  sfCanvas.on('object:modified',    () => { sfUpdatePanel(); sfSaveHistory(); });
  sfCanvas.on('object:added',       () => sfRefreshLayers());
  sfCanvas.on('object:removed',     () => sfRefreshLayers());
  sfCanvas.on('path:created',       () => sfSaveHistory());

  sfCanvas.on('object:scaling', e => {
    if (!sfAspectLocked) return;
    const obj = e.target;
    if (obj.__sfAR === undefined) {
      obj.__sfAR = obj.scaleX / obj.scaleY;
    }
    obj.scaleY = obj.scaleX / obj.__sfAR;
  });
}

function sfOnSelect() {
  document.getElementById('de-align-bar').style.display = 'flex';
  sfUpdatePanel();
  sfRefreshLayers();
}
function sfOnClear() {
  document.getElementById('de-align-bar').style.display = 'none';
  sfShowSection(null);
  document.getElementById('sf-ps-empty').style.display = '';
  sfRefreshLayers();
}

/* ─────────────────────────────────────
   PROPERTIES PANEL
───────────────────────────────────── */
function sfShowSection(which) {
  ['empty','pos','fill','text','img','shadow','layer'].forEach(s => {
    const el = document.getElementById('sf-ps-' + s);
    if (el) el.style.display = (s === which || which === 'all') ? '' : 'none';
  });
  if (which && which !== null) {
    // always show pos + fill + shadow + layer for non-empty
    ['pos','fill','shadow','layer'].forEach(s => {
      const el = document.getElementById('sf-ps-' + s);
      if (el) el.style.display = '';
    });
    document.getElementById('sf-ps-empty').style.display = 'none';
  }
}

function sfUpdatePanel() {
  const obj = sfCanvas.getActiveObject();
  if (!obj) { sfShowSection(null); document.getElementById('sf-ps-empty').style.display=''; return; }

  // show pos
  sfShowSection('pos');
  document.getElementById('sf-px').value   = Math.round(obj.left);
  document.getElementById('sf-py').value   = Math.round(obj.top);
  document.getElementById('sf-pw').value   = Math.round(obj.getScaledWidth());
  document.getElementById('sf-ph').value   = Math.round(obj.getScaledHeight());
  document.getElementById('sf-prot').value = Math.round(obj.angle || 0);
  document.getElementById('sf-pop').value  = obj.opacity ?? 1;

  // hide type panels first
  ['sf-ps-text','sf-ps-img'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
  });

  const type = obj.type;
  if (type === 'i-text' || type === 'text') {
    document.getElementById('sf-ps-text').style.display  = '';
    document.getElementById('sf-fsize').value  = obj.fontSize || 16;
    document.getElementById('sf-fclr').value   = _hex(obj.fill || '#000');
    document.getElementById('sf-char-sp').value = obj.charSpacing || 0;
    document.getElementById('sf-line-h').value  = obj.lineHeight || 1.16;
    document.getElementById('sf-btn-bold').classList.toggle('on',   obj.fontWeight === 'bold');
    document.getElementById('sf-btn-italic').classList.toggle('on', obj.fontStyle  === 'italic');
    document.getElementById('sf-btn-under').classList.toggle('on',  !!obj.underline);
    sfHighlightFont(obj.fontFamily || 'Cairo');
  } else if (type === 'image') {
    document.getElementById('sf-ps-img').style.display  = '';
  }

  // fill
  if (obj.fill && typeof obj.fill === 'string' && obj.fill !== 'transparent') {
    document.getElementById('sf-fill-clr').value = _hex(obj.fill);
    sfSwitchFill('solid');
  } else if (obj.fill && obj.fill.type) {
    sfSwitchFill('grad');
  } else {
    sfSwitchFill('none');
  }
  if (obj.stroke)      document.getElementById('sf-stroke-clr').value = _hex(obj.stroke);
  if (obj.strokeWidth !== undefined) document.getElementById('sf-stroke-w').value = obj.strokeWidth;
  if (obj.rx !== undefined)          document.getElementById('sf-rx').value = obj.rx || 0;
  if (obj.globalCompositeOperation)  document.getElementById('sf-blend').value = obj.globalCompositeOperation;

  // shadow
  if (obj.shadow) {
    const sh = obj.shadow;
    document.getElementById('sf-sh-x').value    = sh.offsetX || 5;
    document.getElementById('sf-sh-y').value    = sh.offsetY || 5;
    document.getElementById('sf-sh-blur').value = sh.blur    || 8;
  }
}

function sfSetProp(prop, val) {
  const obj = sfCanvas.getActiveObject();
  if (!obj) return;
  obj.set(prop, val);
  sfCanvas.renderAll();
}
function sfSetXY() {
  const obj = sfCanvas.getActiveObject();
  if (!obj) return;
  obj.set({ left:+document.getElementById('sf-px').value, top:+document.getElementById('sf-py').value });
  obj.setCoords(); sfCanvas.renderAll();
}
function sfSetWH() {
  const obj = sfCanvas.getActiveObject();
  if (!obj) return;
  const w = +document.getElementById('sf-pw').value;
  const h = +document.getElementById('sf-ph').value;
  obj.set({ scaleX: w / obj.width, scaleY: h / obj.height });
  obj.setCoords(); sfCanvas.renderAll();
}

/* Fill */
function sfSwitchFill(tab) {
  ['solid','grad','none'].forEach(t => {
    document.getElementById('sf-fill-' + t).classList.toggle('on', t === tab);
    const pane = document.getElementById('sf-fill-' + t + '-pane');
    if (pane) pane.style.display = t === tab ? '' : 'none';
  });
  if (tab === 'none') {
    const obj = sfCanvas.getActiveObject();
    if (obj) { obj.set('fill','transparent'); sfCanvas.renderAll(); }
  }
  if (tab === 'solid') {
    const obj = sfCanvas.getActiveObject();
    if (obj && document.getElementById('sf-fill-clr').value) {
      obj.set('fill', document.getElementById('sf-fill-clr').value);
      sfCanvas.renderAll();
    }
  }
}
function sfSetFillSolid(color) {
  const obj = sfCanvas.getActiveObject();
  if (!obj) return;
  obj.set('fill', color);
  sfCanvas.renderAll();
}
function sfApplyGradient() {
  const obj = sfCanvas.getActiveObject();
  if (!obj) return;
  const c1  = document.getElementById('sf-grad-c1').value;
  const c2  = document.getElementById('sf-grad-c2').value;
  const ang = (+document.getElementById('sf-grad-ang').value) * Math.PI / 180;
  const w   = obj.width  || 100;
  const h   = obj.height || 100;
  const x1  = (w / 2) - Math.cos(ang) * (w / 2);
  const y1  = (h / 2) - Math.sin(ang) * (h / 2);
  const x2  = (w / 2) + Math.cos(ang) * (w / 2);
  const y2  = (h / 2) + Math.sin(ang) * (h / 2);
  obj.set('fill', new fabric.Gradient({
    type:'linear', coords:{x1,y1,x2,y2},
    colorStops:[{offset:0, color:c1},{offset:1, color:c2}]
  }));
  sfCanvas.renderAll();
}

/* Dash */
function sfApplyDash(style) {
  const obj = sfCanvas.getActiveObject();
  if (!obj) return;
  const sw = obj.strokeWidth || 2;
  ['solid','dashed','dotted'].forEach(s => {
    document.getElementById('sf-dash-' + s).classList.toggle('on', s === style);
  });
  if (style === 'solid')  obj.set('strokeDashArray', null);
  if (style === 'dashed') obj.set('strokeDashArray', [sw*4, sw*2]);
  if (style === 'dotted') obj.set('strokeDashArray', [sw, sw*2]);
  sfCanvas.renderAll();
}

/* Text toggles */
function sfToggleBold() {
  const obj = sfCanvas.getActiveObject();
  if (!obj) return;
  obj.set('fontWeight', obj.fontWeight === 'bold' ? 'normal' : 'bold');
  sfCanvas.renderAll();
  document.getElementById('sf-btn-bold').classList.toggle('on', obj.fontWeight === 'bold');
}
function sfToggleItalic() {
  const obj = sfCanvas.getActiveObject();
  if (!obj) return;
  obj.set('fontStyle', obj.fontStyle === 'italic' ? 'normal' : 'italic');
  sfCanvas.renderAll();
  document.getElementById('sf-btn-italic').classList.toggle('on', obj.fontStyle === 'italic');
}
function sfToggleUnderline() {
  const obj = sfCanvas.getActiveObject();
  if (!obj) return;
  obj.set('underline', !obj.underline);
  sfCanvas.renderAll();
  document.getElementById('sf-btn-under').classList.toggle('on', !!obj.underline);
}
function sfSetDir(dir) {
  const obj = sfCanvas.getActiveObject();
  if (!obj) return;
  obj.set({ direction: dir, textAlign: dir === 'rtl' ? 'right' : 'left' });
  sfCanvas.renderAll();
}

/* Image filters */
function sfApplyFilters() {
  const obj = sfCanvas.getActiveObject();
  if (!obj || obj.type !== 'image') return;
  obj.filters = [
    new fabric.Image.filters.Brightness({ brightness: +document.getElementById('sf-f-bright').value }),
    new fabric.Image.filters.Contrast(  { contrast:   +document.getElementById('sf-f-cont').value   }),
    new fabric.Image.filters.Saturation({ saturation: +document.getElementById('sf-f-sat').value   }),
    new fabric.Image.filters.Blur(      { blur:        +document.getElementById('sf-f-blur').value  }),
    new fabric.Image.filters.HueRotation({ rotation:  +document.getElementById('sf-f-hue').value   }),
  ];
  obj.applyFilters();
  sfCanvas.renderAll();
}
function sfResetFilters() {
  ['sf-f-bright','sf-f-cont','sf-f-sat','sf-f-blur','sf-f-hue'].forEach(id => {
    document.getElementById(id).value = 0;
  });
  const obj = sfCanvas.getActiveObject();
  if (obj && obj.type === 'image') { obj.filters = []; obj.applyFilters(); sfCanvas.renderAll(); }
}

/* Shadow / Glow */
function sfSwitchEff(tab) {
  ['shadow','glow'].forEach(t => {
    document.getElementById('sf-eff-' + t).classList.toggle('on', t === tab);
    document.getElementById('sf-eff-' + t + '-pane').style.display = t === tab ? '' : 'none';
  });
}
function sfApplyShadow() {
  const obj = sfCanvas.getActiveObject();
  if (!obj) return;
  const rgb = _hex2rgb(document.getElementById('sf-sh-clr').value);
  const op  = +document.getElementById('sf-sh-op').value;
  obj.set('shadow', new fabric.Shadow({
    color:  `rgba(${rgb},${op})`,
    offsetX: +document.getElementById('sf-sh-x').value,
    offsetY: +document.getElementById('sf-sh-y').value,
    blur:    +document.getElementById('sf-sh-blur').value,
  }));
  sfCanvas.renderAll();
}
function sfRemoveShadow() {
  const obj = sfCanvas.getActiveObject();
  if (obj) { obj.set('shadow', null); sfCanvas.renderAll(); }
}
function sfApplyGlow(on) {
  const obj = sfCanvas.getActiveObject();
  if (!obj) return;
  if (!on) { sfRemoveShadow(); return; }
  const rgb  = _hex2rgb(document.getElementById('sf-gl-clr').value);
  const op   = +document.getElementById('sf-gl-op').value;
  const size = +document.getElementById('sf-gl-size').value;
  obj.set('shadow', new fabric.Shadow({
    color: `rgba(${rgb},${op})`, offsetX:0, offsetY:0, blur: size
  }));
  sfCanvas.renderAll();
}

/* ─────────────────────────────────────
   FONTS
───────────────────────────────────── */
function sfBuildFontChips() {
  const grid = document.getElementById('sf-font-chips');
  SF_FONTS.forEach(f => {
    const chip = document.createElement('span');
    chip.className = 'sb-font';
    chip.textContent = f;
    chip.style.fontFamily = f;
    chip.onclick = () => {
      sfSetProp('fontFamily', f);
      sfHighlightFont(f);
    };
    grid.appendChild(chip);
  });
}
function sfHighlightFont(family) {
  document.querySelectorAll('.sb-font').forEach(c => {
    c.classList.toggle('on', c.textContent === family);
  });
}

/* ─────────────────────────────────────
   OBJECT ACTIONS
───────────────────────────────────── */
function sfDelete() {
  const obj = sfCanvas.getActiveObject();
  if (!obj) return;
  if (obj.type === 'activeSelection') {
    obj.getObjects().forEach(o => sfCanvas.remove(o));
    sfCanvas.discardActiveObject();
  } else {
    sfCanvas.remove(obj);
  }
  sfCanvas.renderAll(); sfSaveHistory();
}
function sfClone() {
  const obj = sfCanvas.getActiveObject();
  if (!obj) return;
  obj.clone(clone => {
    clone.set({ left: obj.left + 20, top: obj.top + 20 });
    sfCanvas.add(clone);
    sfCanvas.setActiveObject(clone);
    sfCanvas.renderAll(); sfSaveHistory();
  });
}

/* ─────────────────────────────────────
   HISTORY
───────────────────────────────────── */
function sfSaveHistory() {
  const json = JSON.stringify(sfCanvas.toJSON([
    'selectable','hasControls','shadow','globalCompositeOperation','strokeDashArray'
  ]));
  sfHist = sfHist.slice(0, sfHistIdx + 1);
  sfHist.push(json);
  sfHistIdx = sfHist.length - 1;
  try { localStorage.setItem('sf_design_json', json); } catch(e) {}
  sfRefreshLayers();
}
function sfUndo() {
  if (sfHistIdx <= 0) { showToast('لا يوجد تراجع إضافي'); return; }
  sfHistIdx--;
  sfCanvas.loadFromJSON(sfHist[sfHistIdx], () => sfCanvas.renderAll());
}
function sfRedo() {
  if (sfHistIdx >= sfHist.length - 1) { showToast('لا توجد إعادة إضافية'); return; }
  sfHistIdx++;
  sfCanvas.loadFromJSON(sfHist[sfHistIdx], () => sfCanvas.renderAll());
}

/* ─────────────────────────────────────
   LAYERS
───────────────────────────────────── */
function sfRefreshLayers() {
  const list   = document.getElementById('sf-layers-list');
  if (!list) return;
  const objs   = sfCanvas.getObjects();
  const active = sfCanvas.getActiveObject();
  list.innerHTML = [...objs].reverse().map((obj, ri) => {
    const i     = objs.length - 1 - ri;
    const isSel = obj === active;
    const icon  = obj.type === 'i-text' ? 'fa-font'
                : obj.type === 'image'  ? 'fa-image'
                : obj.type === 'line'   ? 'fa-minus'
                : obj.type === 'group'  ? 'fa-object-group'
                : obj.type === 'path'   ? 'fa-pen-nib'
                : 'fa-shapes';
    const name  = obj.type === 'i-text' ? (obj.text || '').slice(0,18)
                : obj.type === 'image'  ? 'صورة'
                : obj.type === 'path'   ? 'رسم'
                : obj.type;
    return `<div class="sb-layer${isSel?' on':''}" onclick="sfSelectLayer(${i})">
      <span class="sb-layer-ico"><i class="fa ${icon}"></i></span>
      <span class="sb-layer-name">${name}</span>
      <button class="sb-layer-del" onclick="event.stopPropagation();sfCanvas.remove(sfCanvas.getObjects()[${i}]);sfCanvas.renderAll();sfSaveHistory()">
        <i class="fa fa-xmark"></i>
      </button>
    </div>`;
  }).join('');
}
function sfSelectLayer(index) {
  const obj = sfCanvas.getObjects()[index];
  if (obj) { sfCanvas.setActiveObject(obj); sfCanvas.renderAll(); sfUpdatePanel(); }
}

/* ─────────────────────────────────────
   TEMPLATES (API)
───────────────────────────────────── */
let _sfTemplatesLoaded = false;
async function sfLoadTemplates() {
  if (_sfTemplatesLoaded && _sfTemplates.length) return;
  try {
    const res = await fetch(`${location.origin}/api/v1/design-templates`);
    if (!res.ok) throw new Error();
    _sfTemplates = await res.json();
    _sfTemplatesLoaded = true;
  } catch(e) {
    _sfTemplates = [];
    _sfTemplatesLoaded = true;
  }

  let filtered = _sfTemplates;
  if (SF_PRODUCT_CATEGORY_ID) {
    filtered = _sfTemplates.filter(t =>
      !t.category_id ||
      t.category_id == SF_PRODUCT_CATEGORY_ID ||
      t.parent_category_id == SF_PRODUCT_CATEGORY_ID
    );
  }
  if (SF_PRODUCT_ID) {
    const byProduct = filtered.filter(t => t.product_id == SF_PRODUCT_ID);
    if (byProduct.length) filtered = byProduct;
  }

  sfRenderTemplateGrid(filtered);
}

function sfRenderTemplateGrid(list) {
  const grid = document.getElementById('sf-tmpl-grid');
  if (!list.length) {
    const hasAll = _sfTemplates.length > 0;
    grid.innerHTML = `<div style="text-align:center;padding:16px 0;color:var(--ed-text2);font-size:11px">
      <i class="fa fa-palette" style="font-size:22px;display:block;margin-bottom:6px;color:var(--ed-text3)"></i>
      ${hasAll ? 'لا توجد قوالب لهذا المنتج.' : 'لا توجد قوالب حتى الآن.'}
      ${hasAll ? `<br><button onclick="sfRenderTemplateGrid(_sfTemplates)" style="margin-top:8px;padding:3px 10px;border-radius:6px;border:1px solid var(--ed-border);background:var(--ed-inp);color:var(--ed-text2);font-size:11px;cursor:pointer">عرض الكل</button>` : ''}
    </div>`;
    return;
  }

  const groups = {};
  list.forEach(t => {
    const cat = t.category_name || 'عام';
    if (!groups[cat]) groups[cat] = [];
    groups[cat].push(t);
  });

  let html = '';
  for (const [cat, items] of Object.entries(groups)) {
    html += `<div class="sb-sec">${cat}</div><div class="sb-tmpl-grid">`;
    items.forEach(t => {
      const thumb = t.thumbnail
        ? `<img src="${t.thumbnail}" style="width:100%;aspect-ratio:1;object-fit:cover;border-radius:4px">`
        : `<div class="sb-tmpl-ph"><i class="fa fa-palette"></i></div>`;
      html += `<div class="sb-tmpl" onclick="sfLoadTemplate(${t.id})">
        ${thumb}
        <div class="sb-tmpl-name">${t.name}</div>
      </div>`;
    });
    html += '</div>';
  }
  document.getElementById('sf-tmpl-grid').innerHTML = html;
}

function sfLoadTemplate(id) {
  const tmpl = _sfTemplates.find(t => t.id === id);
  if (!tmpl || !tmpl.canvas_json) return;
  if (!confirm(`تحميل قالب "${tmpl.name}"؟ سيتم مسح التصميم الحالي.`)) return;
  sfCanvas.clear();
  sfApplySize(tmpl.canvas_width || 800, tmpl.canvas_height || 800);
  const sel = document.getElementById('sf-size-sel');
  const val  = `${tmpl.canvas_width||800}x${tmpl.canvas_height||800}`;
  if (sel) { [...sel.options].forEach(o => o.selected = o.value === val); }
  sfCanvas.loadFromJSON(tmpl.canvas_json, () => {
    sfCanvas.renderAll();
    sfSaveHistory();
    showToast('تم تحميل القالب: ' + tmpl.name);
  });
}

/* ─────────────────────────────────────
   EXPORT / SAVE
───────────────────────────────────── */
function sfExportPNG() {
  sfCanvas.discardActiveObject();
  sfCanvas.renderAll();
  const m   = Math.min(2, 1200 / Math.max(sfCanvas.width, sfCanvas.height));
  const url = sfCanvas.toDataURL({ format:'png', quality:1, multiplier:m });
  const a   = document.createElement('a');
  a.href    = url;
  a.download = 'design-elite-' + Date.now() + '.png';
  a.click();
  showToast('تم تنزيل التصميم بنجاح ✓');
}

function sfSaveDesign() {
  sfCanvas.discardActiveObject();
  sfCanvas.renderAll();
  const json = JSON.stringify(sfCanvas.toJSON([
    'selectable','hasControls','shadow','globalCompositeOperation','strokeDashArray'
  ]));
  try { localStorage.setItem('sf_design_json', json); } catch(e){}

  // Save thumbnail for cart preview
  const m   = 400 / Math.max(sfCanvas.width, sfCanvas.height);
  const thumb = sfCanvas.toDataURL({ format:'png', quality:.88, multiplier:m });
  try { localStorage.setItem('sf_design_thumb', thumb); } catch(e){}

  showToast('تم حفظ التصميم ✓');
}

/* ─────────────────────────────────────
   KEYBOARD SHORTCUTS
───────────────────────────────────── */
document.addEventListener('keydown', e => {
  if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
  if ((e.ctrlKey||e.metaKey) && e.key==='z') { e.preventDefault(); sfUndo(); }
  if ((e.ctrlKey||e.metaKey) && e.key==='y') { e.preventDefault(); sfRedo(); }
  if ((e.ctrlKey||e.metaKey) && e.key==='d') { e.preventDefault(); sfClone(); }
  if (e.key==='Delete'||e.key==='Backspace') { e.preventDefault(); sfDelete(); }
  if (e.key==='v'||e.key==='V')   sfTool('select');
  if (e.key==='p'||e.key==='P')   sfTool('draw');
  if (e.key==='+') sfZoomStep(.1);
  if (e.key==='-') sfZoomStep(-.1);
});

/* ─────────────────────────────────────
   TOAST
───────────────────────────────────── */
let _sfToastTimer;
function showToast(msg) {
  const el = document.getElementById('sf-toast');
  el.textContent = msg;
  el.style.display = 'block';
  clearTimeout(_sfToastTimer);
  _sfToastTimer = setTimeout(() => el.style.display = 'none', 2500);
}

/* ─────────────────────────────────────
   HELPERS
───────────────────────────────────── */
function _hex(color) {
  if (!color || color === 'transparent') return '#000000';
  if (typeof color !== 'string') return '#000000';
  if (color.startsWith('#')) return color.length===4
    ? '#'+color[1]+color[1]+color[2]+color[2]+color[3]+color[3] : color;
  const m = color.match(/\d+/g);
  if (!m) return '#000000';
  return '#' + [m[0],m[1],m[2]].map(n => (+n).toString(16).padStart(2,'0')).join('');
}
function _hex2rgb(hex) {
  const r = parseInt(hex.slice(1,3),16);
  const g = parseInt(hex.slice(3,5),16);
  const b = parseInt(hex.slice(5,7),16);
  return `${r},${g},${b}`;
}
</script>
</body>
</html>
