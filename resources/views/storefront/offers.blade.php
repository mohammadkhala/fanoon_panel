<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title id="page-title">العروض — ايليت دعاية</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{--red:#10b46a;--red-d:#0c8f52;--red-10:rgba(16,180,106,.10);--navy:#0f1512;--amber:#f59e0b;--surface:#f5f8f4;--white:#FFFFFF;--border:#d6e8d9;--text:#0f1512;--text-2:#4a5e50;--text-3:#8fa895;--r-md:14px;--r-lg:22px;--sh-sm:0 2px 8px rgba(15,21,18,.07);--sh-md:0 6px 20px rgba(15,21,18,.11);--max-w:1260px;--font:'IBM Plex Sans Arabic','Cairo',sans-serif}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--font);background:var(--surface);color:var(--text);direction:rtl;-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}
.wrap{max-width:var(--max-w);margin-inline:auto;padding-inline:20px}
.header{background:var(--white);position:sticky;top:0;z-index:200;box-shadow:0 1px 0 var(--border)}
.header-body{display:flex;align-items:center;gap:20px;padding:12px 0}
.logo{display:flex;align-items:center;gap:10px;flex-shrink:0}
.logo-mark{width:120px;height:50px;background:#000;border:1.5px solid #222;border-radius:12px;display:flex;align-items:center;justify-content:center;overflow:hidden;padding:5px 10px;box-shadow:0 2px 8px rgba(15,21,18,.08)}
.logo-name{font-size:17px;font-weight:900;color:var(--navy)}
.logo-tag{font-size:11px;color:var(--text-3)}
.bc{background:var(--white);border-bottom:1px solid var(--border);padding:12px 0;font-size:13px;color:var(--text-3)}
.bc-inner{display:flex;align-items:center;gap:8px}
.bc-inner a{color:var(--text-3);transition:color .2s}
.bc-inner a:hover{color:var(--red)}
.bc-inner i{font-size:10px}
.sec{padding:50px 0}
.sec-title{font-size:24px;font-weight:800;color:var(--navy);margin-bottom:8px}
.sec-sub{font-size:15px;color:var(--text-2);margin-bottom:28px}
.sec-head{display:flex;align-items:flex-end;justify-content:space-between;gap:12px;margin-bottom:28px}
.view-all{display:inline-flex;align-items:center;gap:6px;color:var(--red);font-size:13px;font-weight:600}

/* FLASH BANNER */
.flash-banner{background:linear-gradient(135deg,var(--navy) 0%,#1a2e1f 100%);border-radius:var(--r-lg);overflow:hidden;position:relative;margin-bottom:50px}
.flash-inner{display:grid;grid-template-columns:1fr 1.4fr;gap:0;align-items:stretch;min-height:220px}
.flash-left{padding:40px 48px;display:flex;flex-direction:column;justify-content:center;gap:14px;z-index:1}
.flash-tag{display:inline-flex;align-items:center;gap:7px;background:rgba(245,158,11,.15);color:var(--amber);padding:6px 14px;border-radius:50px;font-size:12px;font-weight:700;width:fit-content}
.flash-title{font-size:26px;font-weight:900;color:#fff;line-height:1.4}
.flash-sub{font-size:14px;color:rgba(255,255,255,.65);line-height:1.7}
.timer-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.t-block{display:flex;flex-direction:column;align-items:center;background:rgba(255,255,255,.08);border-radius:8px;padding:8px 12px;min-width:52px}
.t-num{font-size:22px;font-weight:900;color:var(--amber);font-variant-numeric:tabular-nums}
.t-lbl{font-size:9px;color:rgba(255,255,255,.5);font-weight:600;text-transform:uppercase}
.t-sep{color:rgba(255,255,255,.4);font-size:20px;font-weight:700}
.timer-label-text{font-size:12px;color:rgba(255,255,255,.5)}
.btn-offer{display:inline-flex;align-items:center;gap:8px;background:var(--red);color:#fff;padding:11px 22px;border-radius:12px;font-size:14px;font-weight:700;transition:background .2s;width:fit-content}
.btn-offer:hover{background:var(--red-d)}
.flash-right{display:grid;grid-template-columns:1fr 1fr;gap:2px;background:rgba(255,255,255,.04)}
.flash-prod-card{padding:20px 16px;display:flex;flex-direction:column;gap:8px;background:rgba(255,255,255,.03);transition:background .2s;cursor:pointer;text-decoration:none}
.flash-prod-card:hover{background:rgba(255,255,255,.07)}
.flash-prod-img{width:52px;height:52px;border-radius:10px;background:rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;font-size:22px}
.flash-prod-name{font-size:13px;font-weight:600;color:#fff;line-height:1.3}
.flash-prod-was{font-size:11px;color:rgba(255,255,255,.4);text-decoration:line-through;margin-left:4px}
.flash-prod-price{font-size:14px;font-weight:800;color:var(--amber)}
.flash-empty{padding:40px 0;text-align:center;color:rgba(255,255,255,.4);font-size:15px}

/* PRODUCTS */
.prod-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
.prod-card{background:var(--white);border:1.5px solid var(--border);border-radius:var(--r-md);overflow:hidden;transition:box-shadow .2s,transform .2s}
.prod-card:hover{box-shadow:var(--sh-md);transform:translateY(-3px)}
.prod-img-wrap{position:relative;aspect-ratio:1;overflow:hidden}
.prod-actions{position:absolute;bottom:10px;left:50%;transform:translateX(-50%) translateY(10px);display:flex;gap:8px;opacity:0;transition:all .25s}
.prod-card:hover .prod-actions{opacity:1;transform:translateX(-50%) translateY(0)}
.pa-btn{background:rgba(255,255,255,.95);border:none;border-radius:8px;padding:7px 12px;font-size:12px;font-weight:600;display:flex;align-items:center;gap:5px;cursor:pointer;color:var(--navy);white-space:nowrap}
.pa-btn:hover{background:var(--red);color:#fff}
.badge-sale{position:absolute;top:10px;right:10px;background:var(--amber);color:var(--navy);padding:3px 8px;border-radius:6px;font-size:11px;font-weight:700}
.prod-body{padding:14px}
.prod-name{font-size:14px;font-weight:600;color:var(--navy);margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.prod-name a{color:inherit}
.prod-price{display:flex;align-items:baseline;gap:8px}
.price-now{font-size:16px;font-weight:800;color:var(--red)}
.price-was{font-size:12px;color:var(--text-3);text-decoration:line-through}
.empty-state{text-align:center;padding:80px 0;color:var(--text-3)}
.empty-state i{font-size:56px;margin-bottom:18px}
.skel{background:linear-gradient(90deg,#e8f0ea 25%,#d4e8d8 50%,#e8f0ea 75%);background-size:200% 100%;animation:shimmer 1.4s infinite}
@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}

/* CART */
.cart-veil{position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:500;opacity:0;pointer-events:none;transition:opacity .3s}
.cart-veil.on{opacity:1;pointer-events:all}
.cart-drawer{position:fixed;top:0;right:-420px;width:420px;height:100%;background:#fff;z-index:501;box-shadow:var(--sh-md);transition:right .3s;display:flex;flex-direction:column}
.cart-drawer.on{right:0}
.cart-hd{display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid var(--border);font-weight:700;font-size:16px}
.cart-x{background:none;border:none;font-size:20px;cursor:pointer}
.cart-bd{flex:1;overflow-y:auto;padding:12px 20px}
.cart-ft{padding:16px 20px;border-top:1px solid var(--border)}
.cart-total-row{display:flex;justify-content:space-between;font-size:15px;font-weight:700;margin-bottom:14px}
.cart-item{display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border)}
.cart-item-del{margin-inline-start:auto;background:none;border:none;cursor:pointer;font-size:16px;color:var(--text-3)}
.btn-red{display:inline-flex;align-items:center;gap:8px;background:var(--red);color:#fff;padding:11px 22px;border-radius:12px;font-size:14px;font-weight:700;transition:background .2s;cursor:pointer;border:none}
.btn-red:hover{background:var(--red-d)}
.icon-btn{position:relative;width:42px;height:42px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:#f5f8f4;border:1.5px solid var(--border);color:var(--text-2);font-size:16px;cursor:pointer;transition:all .2s}
.icon-btn:hover{background:var(--red-10);border-color:var(--red);color:var(--red)}
.badge{position:absolute;top:-5px;right:-5px;background:var(--red);color:#fff;width:18px;height:18px;border-radius:50%;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center}
.badge.off{display:none}
.toasts{position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:1000;display:flex;flex-direction:column;gap:8px;pointer-events:none}
.toast{background:var(--navy);color:#fff;padding:10px 22px;border-radius:10px;font-size:14px;animation:si .3s ease}
.toast.err{background:#e74c3c}
@keyframes si{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:none}}
.footer-mini{background:var(--navy);color:rgba(255,255,255,.5);text-align:center;padding:20px;font-size:13px;margin-top:40px}
.footer-mini a{color:rgba(255,255,255,.5)}
@media(max-width:900px){.flash-inner{grid-template-columns:1fr}.flash-right{display:none}.prod-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){.prod-grid{grid-template-columns:repeat(2,1fr)}.cart-drawer{width:100%;right:-100%}}
</style>
</head>
<body>

<header class="header">
  <div class="wrap">
    <div class="header-body">
      <a href="/" class="logo">
        <div class="logo-mark" id="logo-mark">ع</div>
        <div>
          <div class="logo-name" id="store-name">ايليت دعاية</div>
          <div class="logo-tag">طباعة وتصميم</div>
        </div>
      </a>
      <div style="margin-inline-start:auto;display:flex;gap:10px;align-items:center">
        <a href="/storefront/products" style="display:flex;align-items:center;gap:6px;color:var(--text-2);font-size:14px;font-weight:600"><i class="fa fa-border-all"></i> المنتجات</a>
        <a href="/storefront/contact" style="display:flex;align-items:center;gap:6px;color:var(--text-2);font-size:14px;font-weight:600"><i class="fa fa-phone"></i> اتصل بنا</a>
        <button class="icon-btn" onclick="openCart()">
          <i class="fa fa-bag-shopping"></i>
          <span class="badge off" id="cart-badge">0</span>
        </button>
      </div>
    </div>
  </div>
</header>

<div class="bc">
  <div class="wrap">
    <div class="bc-inner">
      <a href="/"><i class="fa fa-house"></i> الرئيسية</a>
      <i class="fa fa-chevron-left"></i>
      <span>العروض</span>
    </div>
  </div>
</div>

<div class="wrap">
  <!-- FLASH SALE -->
  <div style="padding:50px 0 0">
    <div class="flash-banner" id="flash-banner">
      <div class="flash-inner">
        <div class="flash-left">
          <div class="flash-tag"><i class="fa fa-bolt"></i> <span id="flash-tag-txt">عرض فلاش</span></div>
          <h2 class="flash-title" id="flash-title">باقة الأعمال الصغيرة</h2>
          <p class="flash-sub" id="flash-sub">منتجات مطبوعة بأفضل الأسعار</p>
          <div class="timer-row">
            <span class="timer-label-text">ينتهي خلال:</span>
            <div class="t-block"><span class="t-num" id="th">00</span><div class="t-lbl">ساعة</div></div>
            <span class="t-sep">:</span>
            <div class="t-block"><span class="t-num" id="tm">00</span><div class="t-lbl">دقيقة</div></div>
            <span class="t-sep">:</span>
            <div class="t-block"><span class="t-num" id="ts">00</span><div class="t-lbl">ثانية</div></div>
          </div>
        </div>
        <div class="flash-right" id="flash-prods">
          <div class="flash-empty">جاري التحميل...</div>
        </div>
      </div>
    </div>
  </div>

  <!-- DISCOUNTED PRODUCTS -->
  <div class="sec">
    <div class="sec-head">
      <div>
        <h2 class="sec-title"><span style="background:var(--amber);color:var(--navy);font-size:12px;padding:2px 9px;border-radius:50px;margin-left:8px;font-weight:900">خصم</span>منتجات مخفّضة</h2>
        <p class="sec-sub">عروض حصرية على المنتجات المختارة</p>
      </div>
      <a href="/storefront/products?type=discounted" class="view-all"><i class="fa fa-arrow-left"></i> عرض الكل</a>
    </div>
    <div class="prod-grid" id="disc-grid" style="display:none"></div>
    <div id="disc-skel" style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px">
      <div class="skel" style="aspect-ratio:1;border-radius:14px"></div>
      <div class="skel" style="aspect-ratio:1;border-radius:14px"></div>
      <div class="skel" style="aspect-ratio:1;border-radius:14px"></div>
      <div class="skel" style="aspect-ratio:1;border-radius:14px"></div>
    </div>
    <div id="disc-empty" style="display:none" class="empty-state"><i class="fa fa-tag"></i><p>لا توجد منتجات مخفّضة حالياً</p></div>
  </div>

  <!-- ALL PRODUCTS -->
  <div class="sec" style="padding-top:0">
    <div class="sec-head">
      <div>
        <h2 class="sec-title">جميع المنتجات</h2>
        <p class="sec-sub" id="all-sub">تصفح كامل التشكيلة</p>
      </div>
      <a href="/storefront/products" class="view-all"><i class="fa fa-arrow-left"></i> صفحة المنتجات</a>
    </div>
    <div class="prod-grid" id="all-grid" style="display:none"></div>
    <div id="all-skel" style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px">
      <div class="skel" style="aspect-ratio:1;border-radius:14px"></div>
      <div class="skel" style="aspect-ratio:1;border-radius:14px"></div>
      <div class="skel" style="aspect-ratio:1;border-radius:14px"></div>
      <div class="skel" style="aspect-ratio:1;border-radius:14px"></div>
    </div>
  </div>
</div>

<div class="footer-mini">
  <a href="/">الرئيسية</a> &nbsp;·&nbsp; <a href="/storefront/products">المنتجات</a> &nbsp;·&nbsp; <a href="/storefront/contact">اتصل بنا</a>
</div>

<div class="cart-veil" id="cart-veil" onclick="closeCart()"></div>
<div class="cart-drawer" id="cart-drawer">
  <div class="cart-hd"><span>🛒 السلة</span><button class="cart-x" onclick="closeCart()"><i class="fa fa-xmark"></i></button></div>
  <div class="cart-bd" id="cart-bd"></div>
  <div class="cart-ft" id="cart-ft" style="display:none">
    <div class="cart-total-row"><span>المجموع:</span><span id="cart-tot">0 ₪</span></div>
    <a href="/storefront/checkout" class="btn-red" style="width:100%;justify-content:center;display:flex"><i class="fa fa-credit-card"></i> إتمام الشراء</a>
  </div>
</div>
<div class="toasts" id="toasts"></div>

<script>
const API = window.location.origin + '/api/v1';
let CUR = '₪';
let _pBase = '';
let cart = JSON.parse(localStorage.getItem('f_cart')||'[]');

function renderCart(){
  const badge=document.getElementById('cart-badge'),bd=document.getElementById('cart-bd'),ft=document.getElementById('cart-ft'),tot=document.getElementById('cart-tot');
  badge.textContent=cart.reduce((s,i)=>s+i.qty,0); badge.classList.toggle('off',cart.length===0);
  if(!cart.length){bd.innerHTML=`<div style="text-align:center;padding:40px;color:var(--text-3)"><i class="fa fa-cart-shopping" style="font-size:40px;margin-bottom:14px"></i><p>السلة فارغة</p></div>`;ft.style.display='none';return;}
  bd.innerHTML=cart.map(i=>`<div class="cart-item"><div style="width:52px;height:52px;border-radius:8px;background:var(--surface);overflow:hidden;flex-shrink:0">${i.img?`<img src="${i.img}" style="width:100%;height:100%;object-fit:cover">`:''}</div><div><div style="font-size:13px;font-weight:600">${i.name}</div><div style="font-size:12px;color:var(--text-2)">${i.qty}×${parseFloat(i.price).toFixed(2)} ${CUR}</div></div><button class="cart-item-del" onclick="removeFromCart(${i.id})"><i class="fa fa-xmark"></i></button></div>`).join('');
  tot.textContent=cart.reduce((s,i)=>s+i.qty*parseFloat(i.price),0).toFixed(2)+' '+CUR; ft.style.display='block';
}
function removeFromCart(id){cart=cart.filter(i=>i.id!==id);localStorage.setItem('f_cart',JSON.stringify(cart));renderCart();}
function openCart(){document.getElementById('cart-veil').classList.add('on');document.getElementById('cart-drawer').classList.add('on');}
function closeCart(){document.getElementById('cart-veil').classList.remove('on');document.getElementById('cart-drawer').classList.remove('on');}
function toast(msg,type='ok'){const tc=document.getElementById('toasts'),t=document.createElement('div');t.className=`toast ${type}`;t.textContent=msg;tc.appendChild(t);setTimeout(()=>t.remove(),3000);}
function addToCart(id,name,price,img){const ex=cart.find(i=>i.id===id);if(ex){ex.qty++;}else{cart.push({id,name,price:parseFloat(price),qty:1,img});}localStorage.setItem('f_cart',JSON.stringify(cart));renderCart();toast('أُضيف للسلة: '+name);openCart();}

const prodIconMap=[
  [['درع','دروع','كأس','كريستال'],['fa-trophy','linear-gradient(145deg,#134e2a,#1e7a43)','#10b46a']],
  [['لافت','شادر','رول','بانر'],['fa-rectangle-ad','linear-gradient(145deg,#1e3a5f,#2563eb)','#60a5fa']],
  [['بطاقة','كارت','هوية'],['fa-id-card','linear-gradient(145deg,#4c1d95,#7c3aed)','#a78bfa']],
  [['كيس','حقيبة'],['fa-bag-shopping','linear-gradient(145deg,#7c2d12,#ea580c)','#fb923c']],
  [['كوب','مج'],['fa-mug-hot','linear-gradient(145deg,#1e3a5f,#0ea5e9)','#38bdf8']],
  [['قميص','تيشرت'],['fa-shirt','linear-gradient(145deg,#064e3b,#10b981)','#34d399']],
  [['هدية','توزيعة'],['fa-gift','linear-gradient(145deg,#831843,#db2777)','#f472b6']],
];
function getProdStyle(n=''){for(const[kws,[i,g,a]]of prodIconMap)for(const k of kws)if(n.includes(k))return{icon:i,grad:g,accent:a};return{icon:'fa-print',grad:'linear-gradient(145deg,#134e2a,#1e7a43)',accent:'#10b46a'};}

function prodCard(p){
  const st=getProdStyle(p.name||'');
  const imgs=Array.isArray(p.image_fullpath)?p.image_fullpath:(p.image_fullpath?[p.image_fullpath]:[]);
  const imgSrc=imgs.find(u=>u&&!u.includes('img2.jpg'))||null;
  const price=parseFloat(p.price||p.unit_price||0);
  const disc=parseFloat(p.discount||0);
  const now=disc>0?price-price*disc/100:price;
  const sn=(p.name||'').replace(/'/g,"\\'");
  return `<div class="prod-card"><a href="/storefront/product/${p.id}"><div class="prod-img-wrap" style="background:${st.grad}">
    ${imgSrc?`<img src="${imgSrc}" alt="${p.name}" loading="lazy" style="width:100%;height:100%;object-fit:cover" onerror="this.remove()">`:''}
    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none"><i class="fa ${st.icon}" style="font-size:52px;color:${st.accent};opacity:.45"></i></div>
    ${disc>0?`<span class="badge-sale">-${Math.round(disc)}%</span>`:''}
    <div class="prod-actions"><button class="pa-btn" onclick="event.preventDefault();addToCart(${p.id},'${sn}',${now.toFixed(2)},'${imgSrc||''}')"><i class="fa fa-cart-plus"></i> سلة</button></div>
  </div></a>
  <div class="prod-body"><div class="prod-name"><a href="/storefront/product/${p.id}">${p.name||''}</a></div>
  <div class="prod-price"><span class="price-now">${now.toFixed(2)} ${CUR}</span>${disc>0?`<span class="price-was">${price.toFixed(2)} ${CUR}</span>`:''}</div></div></div>`;
}

async function init(){
  let cfg={};
  try{
    cfg=await fetch(`${API}/config`).then(r=>r.json());
    const name=cfg.ecommerce_name||cfg.business_name||'ايليت دعاية';
    if(cfg.currency_symbol)CUR=cfg.currency_symbol;
    document.getElementById('store-name').textContent=name;
    document.title=`العروض — ${name}`;
    _pBase=cfg.base_urls?.product_image_url||'';
    const logoUrl=cfg.logo_full_url||'';
    if(logoUrl)document.getElementById('logo-mark').innerHTML=`<img src="${logoUrl}" alt="${name}" style="width:100%;height:100%;object-fit:contain">`;
  }catch(e){}

  const [discRes,flashRes,allRes]=await Promise.allSettled([
    fetch(`${API}/products/discounted?limit=12`).then(r=>r.json()),
    fetch(`${API}/flash-sale?limit=4`).then(r=>r.json()),
    fetch(`${API}/products/latest?limit=8`).then(r=>r.json()),
  ]);

  // Discounted
  document.getElementById('disc-skel').style.display='none';
  if(discRes.status==='fulfilled'){
    const list=Array.isArray(discRes.value)?discRes.value:(discRes.value.products||[]);
    if(list.length){document.getElementById('disc-grid').style.display='grid';document.getElementById('disc-grid').innerHTML=list.map(prodCard).join('');}
    else document.getElementById('disc-empty').style.display='block';
  }else document.getElementById('disc-empty').style.display='block';

  // Flash sale
  if(flashRes.status==='fulfilled'){
    const fd=flashRes.value;
    const fs=fd.flash_sale;
    const fp=Array.isArray(fd.products)?fd.products:[];
    if(fs&&fp.length){
      if(fs.title)document.getElementById('flash-title').innerHTML=fs.title;
      if(fs.note)document.getElementById('flash-sub').textContent=fs.note;
      if(fs.end_date){
        const end=new Date(fs.end_date.replace(' ','T'));
        const tick=()=>{const d=end-new Date();if(d<=0)return;document.getElementById('th').textContent=String(Math.floor(d/3.6e6)).padStart(2,'0');document.getElementById('tm').textContent=String(Math.floor(d%3.6e6/6e4)).padStart(2,'0');document.getElementById('ts').textContent=String(Math.floor(d%6e4/1e3)).padStart(2,'0');};
        tick();setInterval(tick,1000);
      }
      document.getElementById('flash-prods').innerHTML=fp.slice(0,4).map(p=>{
        const st=getProdStyle(p.name||'');
        const pr=parseFloat(p.price||p.unit_price||0);
        const d=parseFloat(p.discount||0);
        const now=d>0?pr-pr*d/100:pr;
        const orig=d>0?pr:null;
        const imgs=Array.isArray(p.image_fullpath)?p.image_fullpath:(p.image_fullpath?[p.image_fullpath]:[]);
        const imgSrc=imgs.find(u=>u&&!u.includes('img2.jpg'))||null;
        return `<a href="/storefront/product/${p.id}" class="flash-prod-card">
          <div class="flash-prod-img">${imgSrc?`<img src="${imgSrc}" style="width:100%;height:100%;object-fit:cover;border-radius:8px">`:`<i class="fa ${st.icon}"></i>`}</div>
          <div class="flash-prod-name">${p.name||''}</div>
          <div>${orig?`<span class="flash-prod-was">${orig.toFixed(0)} ${CUR}</span>`:''}<span class="flash-prod-price">${now.toFixed(0)} ${CUR}</span></div>
        </a>`;
      }).join('');
    }else{
      document.getElementById('flash-prods').innerHTML=`<div class="flash-empty">لا يوجد عرض فلاش نشط حالياً</div>`;
    }
  }

  // All
  document.getElementById('all-skel').style.display='none';
  if(allRes.status==='fulfilled'){
    const list=Array.isArray(allRes.value)?allRes.value:(allRes.value.products||[]);
    document.getElementById('all-sub').textContent=`${list.length} منتج`;
    if(list.length){document.getElementById('all-grid').style.display='grid';document.getElementById('all-grid').innerHTML=list.map(prodCard).join('');}
  }
}

renderCart();
init();
</script>
</body>
</html>
