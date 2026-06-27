{{--
  Embedded Fabric.js mini-editor — used inside product add/edit form.
  Requires: $embedId (string, e.g. 'prod' or 'edit-prod')
  Optional: $existingJson (string|null), $existingW (int), $existingH (int)
--}}
@php
$eId    = $embedId ?? 'prod';
$exJson = $existingJson ?? null;
$exW    = $existingW    ?? 800;
$exH    = $existingH    ?? 800;
@endphp

{{-- Hidden fields submitted with the product form --}}
<input type="hidden" name="tmpl_canvas_json"      id="{{ $eId }}-canvas-json"  value="">
<input type="hidden" name="tmpl_canvas_width"     id="{{ $eId }}-canvas-w"     value="{{ $exW }}">
<input type="hidden" name="tmpl_canvas_height"    id="{{ $eId }}-canvas-h"     value="{{ $exH }}">
<input type="hidden" name="tmpl_thumbnail_base64" id="{{ $eId }}-thumb"        value="">
<input type="hidden" name="tmpl_enabled"          id="{{ $eId }}-enabled"      value="0">
<input type="hidden" id="{{ $eId }}-enabled-flag" value="0">

@once
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<style>
:root{--de2-green:#10b46a;--de2-border:#3a4540;--de2-text:#e8f0eb;--de2-text2:#8fa895}
.de2-wrap{background:#1a1f1c;border-radius:10px;overflow:hidden;border:1px solid var(--de2-border)}
.de2-tb{background:#22282a;border-bottom:1px solid var(--de2-border);display:flex;align-items:center;gap:4px;padding:7px 10px;flex-wrap:wrap}
.de2-btn{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;background:transparent;border:1px solid transparent;border-radius:7px;color:var(--de2-text2);font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;font-family:inherit;white-space:nowrap}
.de2-btn:hover{background:#2a3230;border-color:var(--de2-border);color:var(--de2-text)}
.de2-btn.active{background:rgba(16,180,106,.15);border-color:var(--de2-green);color:var(--de2-green)}
.de2-sep{width:1px;height:20px;background:var(--de2-border);margin:0 3px;flex-shrink:0}
.de2-sel{background:#2a3230;border:1px solid var(--de2-border);border-radius:7px;color:var(--de2-text);font-size:12px;padding:3px 7px;cursor:pointer;font-family:inherit}
.de2-area{background:#2a2f2b;display:flex;align-items:center;justify-content:center;min-height:380px;padding:16px;overflow:auto}
</style>
@endonce

<div class="de2-wrap" id="{{ $eId }}-editor">
  {{-- Toolbar --}}
  <div class="de2-tb">
    <button type="button" class="de2-btn" onclick="de2AddText('{{ $eId }}')">
      <i class="fa fa-font"></i> {{ translate('add_text') ?: 'نص' }}
    </button>
    <button type="button" class="de2-btn" onclick="de2AddRect('{{ $eId }}')"><i class="fa fa-square"></i></button>
    <button type="button" class="de2-btn" onclick="de2AddCircle('{{ $eId }}')"><i class="fa fa-circle"></i></button>
    <button type="button" class="de2-btn" onclick="document.getElementById('{{ $eId }}-img-up').click()">
      <i class="fa fa-image"></i>
    </button>
    <input type="file" id="{{ $eId }}-img-up" accept="image/*" style="display:none"
           onchange="de2UploadImg('{{ $eId }}', event)">

    <div class="de2-sep"></div>

    <label style="display:inline-flex;align-items:center;gap:4px;cursor:pointer;color:var(--de2-text2);font-size:12px">
      <span>{{ translate('background') ?: 'خلفية' }}</span>
      <input type="color" value="#ffffff" style="width:26px;height:24px;border:none;cursor:pointer"
             onchange="de2SetBg('{{ $eId }}', this.value)">
    </label>

    <div class="de2-sep"></div>

    <select class="de2-sel" onchange="de2Size('{{ $eId }}', this.value)">
      <option value="800x800">800×800</option>
      <option value="1200x400">1200×400</option>
      <option value="900x600">900×600</option>
      <option value="600x900">600×900</option>
      <option value="1050x600">1050×600</option>
    </select>

    <div class="de2-sep"></div>

    <button type="button" class="de2-btn" onclick="de2Undo('{{ $eId }}')">
      <i class="fa fa-rotate-left"></i>
    </button>
    <button type="button" class="de2-btn" onclick="de2Redo('{{ $eId }}')">
      <i class="fa fa-rotate-right"></i>
    </button>
    <button type="button" class="de2-btn" onclick="de2Clear('{{ $eId }}')">
      <i class="fa fa-trash-can"></i> {{ translate('clear_canvas') ?: 'مسح' }}
    </button>

    {{-- Text props --}}
    <div class="de2-sep"></div>
    <div id="{{ $eId }}-text-props" style="display:none;display:inline-flex;align-items:center;gap:4px">
      <input type="number" id="{{ $eId }}-fsize" min="8" max="400" value="24"
             style="width:48px;padding:3px 5px;border-radius:6px;border:1px solid var(--de2-border);background:#2a3230;color:var(--de2-text);font-size:12px"
             oninput="de2Set('{{ $eId }}','fontSize',+this.value)">
      <input type="color" id="{{ $eId }}-fcolor" value="#000000"
             style="width:26px;height:24px;border:none;cursor:pointer"
             onchange="de2Set('{{ $eId }}','fill',this.value)">
      <select class="de2-sel" onchange="de2Set('{{ $eId }}','fontFamily',this.value)">
        <option>Cairo</option><option>Tajawal</option><option>Arial</option>
        <option>Georgia</option><option>Impact</option>
      </select>
    </div>
  </div>

  {{-- Canvas --}}
  <div class="de2-area" id="{{ $eId }}-area">
    <div id="{{ $eId }}-wrap">
      <canvas id="{{ $eId }}-canvas"></canvas>
    </div>
  </div>
</div>

<script>
(function() {
const EID = '{{ $eId }}';
const EXISTING_JSON = @json($exJson);
const INIT_W = {{ (int)$exW }};
const INIT_H = {{ (int)$exH }};

let cvs, hist = [], hidx = -1;

function init() {
  cvs = new fabric.Canvas(EID + '-canvas', {
    backgroundColor: '#ffffff', selection: true, preserveObjectStacking: true,
  });
  resize(INIT_W, INIT_H);

  if (EXISTING_JSON) {
    try { cvs.loadFromJSON(EXISTING_JSON, () => cvs.renderAll()); } catch(e) {}
  }

  cvs.on('selection:created', updateTextProps);
  cvs.on('selection:updated', updateTextProps);
  cvs.on('selection:cleared', () => { document.getElementById(EID+'-text-props').style.display='none'; });
  cvs.on('object:modified', saveH);
  cvs.on('object:added',    saveH);
  cvs.on('object:removed',  saveH);
  saveH();
}

function resize(w, h) {
  cvs.setWidth(w); cvs.setHeight(h);
  const area = document.getElementById(EID+'-area');
  const maxW = area.clientWidth - 32;
  const scale = Math.min(1, maxW / w);
  const wrap = document.getElementById(EID+'-wrap');
  wrap.style.transform = `scale(${scale})`;
  wrap.style.transformOrigin = 'top center';
  wrap.style.width = w + 'px';
  wrap.style.height = h + 'px';
  cvs.renderAll();
  document.getElementById(EID+'-canvas-w').value = w;
  document.getElementById(EID+'-canvas-h').value = h;
}

function saveH() {
  const j = JSON.stringify(cvs.toJSON());
  hist = hist.slice(0, hidx + 1); hist.push(j); hidx = hist.length - 1;
  serializeCanvas();
}

function serializeCanvas() {
  cvs.discardActiveObject();
  document.getElementById(EID+'-canvas-json').value = JSON.stringify(cvs.toJSON());
  const t = cvs.toDataURL({ format:'png', quality:.85, multiplier: 400/Math.max(cvs.width,cvs.height) });
  document.getElementById(EID+'-thumb').value = t;
  cvs.renderAll();
}

function updateTextProps() {
  const o = cvs.getActiveObject();
  const p = document.getElementById(EID+'-text-props');
  if (o && (o.type==='i-text'||o.type==='text')) {
    p.style.display='inline-flex';
    document.getElementById(EID+'-fsize').value = o.fontSize||24;
  } else { p.style.display='none'; }
}

/* exposed globally for onclick handlers */
window['de2AddText'] = function(id) { if(id!==EID) return;
  const o=new fabric.IText('اكتب هنا...',{right:60,top:60,fontFamily:'Cairo',fontSize:28,fill:'#000000',textAlign:'right',direction:'rtl',originX:'right'});
  cvs.add(o);cvs.setActiveObject(o);cvs.renderAll();
};
window['de2AddRect']   = function(id) { if(id!==EID)return; cvs.add(new fabric.Rect({left:100,top:100,width:200,height:120,fill:'#10b46a',rx:8,ry:8}));cvs.renderAll(); };
window['de2AddCircle'] = function(id) { if(id!==EID)return; cvs.add(new fabric.Circle({left:150,top:150,radius:80,fill:'#4ecdc4'}));cvs.renderAll(); };
window['de2SetBg']     = function(id, c) { if(id!==EID)return; cvs.setBackgroundColor(c,()=>cvs.renderAll()); };
window['de2Size']      = function(id, v) { if(id!==EID)return; const[w,h]=v.split('x').map(Number); resize(w,h); };
window['de2Set']       = function(id, p, v) { if(id!==EID)return; const o=cvs.getActiveObject();if(o){o.set(p,v);cvs.renderAll();} };
window['de2Undo']      = function(id) { if(id!==EID)return; if(hidx<=0)return; hidx--; cvs.loadFromJSON(hist[hidx],()=>cvs.renderAll()); };
window['de2Redo']      = function(id) { if(id!==EID)return; if(hidx>=hist.length-1)return; hidx++; cvs.loadFromJSON(hist[hidx],()=>cvs.renderAll()); };
window['de2Clear']     = function(id) { if(id!==EID)return; if(!confirm('مسح كل المحتوى؟'))return; cvs.clear();cvs.setBackgroundColor('#ffffff',()=>cvs.renderAll());saveH(); };
window['de2UploadImg'] = function(id, e) {
  if(id!==EID)return;
  const f=e.target.files[0]; if(!f)return;
  const r=new FileReader(); r.onload=ev=>{fabric.Image.fromURL(ev.target.result,img=>{img.scaleToWidth(Math.min(300,cvs.width/2));img.set({left:80,top:80});cvs.add(img);cvs.setActiveObject(img);cvs.renderAll();});};
  r.readAsDataURL(f); e.target.value='';
};
window['de2Serialize_' + EID] = function() { serializeCanvas(); };
window['de2Serialize'] = function(id) { if(id!==EID)return; serializeCanvas(); };

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
})();
</script>
