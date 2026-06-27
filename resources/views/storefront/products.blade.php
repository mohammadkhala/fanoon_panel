<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title id="page-title">المنتجات — ايليت دعاية</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root {
  --red    : #10b46a;
  --red-d  : #0c8f52;
  --red-10 : rgba(16,180,106,.10);
  --navy   : #0f1512;
  --navy-2 : #1a2e1f;
  --amber  : #f59e0b;
  --surface: #f5f8f4;
  --white  : #FFFFFF;
  --border : #d6e8d9;
  --text   : #0f1512;
  --text-2 : #4a5e50;
  --text-3 : #8fa895;
  --r-sm   : 8px; --r-md: 14px; --r-lg: 22px;
  --sh-sm  : 0 2px 8px rgba(15,21,18,.07);
  --sh-md  : 0 6px 20px rgba(15,21,18,.11);
  --sh-lg  : 0 12px 40px rgba(15,21,18,.15);
  --max-w  : 1260px;
  --font   : 'IBM Plex Sans Arabic','Cairo',sans-serif;
  --ease   : cubic-bezier(.4,0,.2,1);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:var(--font);background:var(--surface);color:var(--text);direction:rtl;-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}
ul{list-style:none}
img{display:block;max-width:100%}
button{font-family:var(--font)}
.wrap{max-width:var(--max-w);margin-inline:auto;padding-inline:20px}

/* ── HEADER ── */
.header{background:var(--white);position:sticky;top:0;z-index:200;box-shadow:0 1px 0 var(--border)}
.header-body{display:flex;align-items:center;gap:20px;padding:12px 0}
.logo{display:flex;align-items:center;gap:10px;flex-shrink:0}
.logo-mark{width:120px;height:50px;background:#000;border:1.5px solid #222;border-radius:12px;display:flex;align-items:center;justify-content:center;overflow:hidden;padding:5px 10px;box-shadow:0 2px 8px rgba(15,21,18,.08)}
.logo-mark img{width:100%;height:100%;object-fit:contain}
.logo-name{font-size:19px;font-weight:900;color:var(--navy);line-height:1.1}
.logo-tag{font-size:11px;color:var(--text-2);font-weight:500}
.search-wrap{flex:1;max-width:500px;position:relative}
.search-wrap input{width:100%;padding:10px 42px 10px 18px;border:1.5px solid var(--border);border-radius:50px;font-family:var(--font);font-size:14px;background:var(--surface);color:var(--text);outline:none;transition:border-color .2s,box-shadow .2s}
.search-wrap input:focus{border-color:var(--red);box-shadow:0 0 0 3px var(--red-10)}
.search-wrap .s-btn{position:absolute;left:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-2);font-size:14px;transition:color .2s}
.search-wrap .s-btn:hover{color:var(--red)}
.h-actions{display:flex;align-items:center;gap:10px;flex-shrink:0}
.icon-btn{position:relative;width:40px;height:40px;border-radius:50%;border:none;cursor:pointer;background:var(--surface);color:var(--navy);font-size:16px;display:flex;align-items:center;justify-content:center;transition:background .2s,color .2s}
.icon-btn:hover{background:var(--red);color:#fff}
.badge{position:absolute;top:-2px;left:-2px;background:var(--red);color:#fff;font-size:9px;font-weight:800;min-width:17px;height:17px;border-radius:9px;display:flex;align-items:center;justify-content:center;border:2px solid #fff}
.badge.off{display:none}

/* ── NAVBAR ── */
.navbar{background:var(--navy)}
.nav-list{display:flex;align-items:stretch}
.nav-item{position:relative}
.nav-link{display:flex;align-items:center;gap:5px;padding:13px 16px;color:#BFCFE3;font-size:14px;font-weight:600;white-space:nowrap;transition:color .2s,background .2s;border-bottom:2px solid transparent}
.nav-item:hover .nav-link,.nav-link.active{color:#fff;background:rgba(255,255,255,.06)}
.mega-menu{position:absolute;top:100%;right:0;background:var(--white);border-radius:0 0 var(--r-md) var(--r-md);box-shadow:var(--sh-lg);min-width:240px;padding:8px 0;opacity:0;visibility:hidden;transform:translateY(-8px);transition:opacity .2s,transform .2s,visibility .2s;z-index:300}
.nav-item:hover .mega-menu{opacity:1;visibility:visible;transform:none}
.mega-menu a{display:flex;align-items:center;gap:10px;padding:10px 18px;font-size:14px;color:var(--text);transition:background .15s,color .15s}
.mega-menu a:hover{background:var(--surface);color:var(--red)}
.mega-menu a i{width:18px;color:var(--red);font-size:13px}
.mega-menu .sep{height:1px;background:var(--border);margin:4px 0}
.menu-toggle{display:none;background:none;border:none;cursor:pointer;color:#fff;font-size:22px;padding:6px;margin-right:auto}
.mobile-nav{display:none;flex-direction:column;background:var(--white);border-top:1px solid var(--border);padding:8px 0;box-shadow:var(--sh-md)}
.mobile-nav.open{display:flex}
.mobile-nav a{display:flex;align-items:center;gap:10px;padding:13px 20px;font-size:15px;font-weight:600;color:var(--navy);border-bottom:1px solid var(--border);transition:background .15s}
.mobile-nav a:hover{background:var(--surface)}
.mobile-nav a i{width:20px;color:var(--red)}

/* ── PAGE HEADER ── */
.page-hd{background:var(--navy);padding:32px 0;position:relative;overflow:hidden}
.page-hd::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 80% at 30% 50%,rgba(16,180,106,.12) 0%,transparent 70%)}
.page-hd-inner{position:relative;z-index:1}
.breadcrumb{display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.5);margin-bottom:10px}
.breadcrumb a{color:var(--red);transition:color .2s}
.breadcrumb a:hover{color:#fff}
.breadcrumb i{font-size:9px}
.page-hd h1{font-size:28px;font-weight:900;color:#fff;margin-bottom:4px}
.page-hd p{font-size:14px;color:rgba(255,255,255,.6)}

/* ── LAYOUT ── */
.products-layout{display:grid;grid-template-columns:240px 1fr;gap:24px;padding:32px 0}

/* ── SIDEBAR ── */
.sidebar{flex-shrink:0}
.sidebar-box{background:var(--white);border:1px solid var(--border);border-radius:var(--r-md);overflow:hidden;margin-bottom:16px}
.sb-head{padding:14px 18px;font-size:14px;font-weight:800;color:var(--navy);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px}
.sb-head i{color:var(--red)}
.cat-list{padding:8px 0}
.cat-item{display:flex;align-items:center;gap:10px;padding:10px 18px;font-size:14px;color:var(--text-2);cursor:pointer;transition:background .15s,color .15s;border-right:3px solid transparent}
.cat-item:hover{background:var(--surface);color:var(--navy)}
.cat-item.active{background:var(--red-10);color:var(--red);font-weight:700;border-right-color:var(--red)}
.cat-item i{width:18px;font-size:13px;flex-shrink:0}
.cat-item .cnt{margin-right:auto;font-size:11px;background:var(--surface);border:1px solid var(--border);border-radius:50px;padding:1px 7px;color:var(--text-3)}

/* ── PRODUCTS AREA ── */
.products-area{}
.area-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;gap:12px;flex-wrap:wrap}
.results-info{font-size:14px;color:var(--text-2)}
.results-info strong{color:var(--navy);font-weight:800}
.sort-sel{padding:8px 14px;border:1.5px solid var(--border);border-radius:50px;font-family:var(--font);font-size:14px;background:var(--white);color:var(--navy);outline:none;cursor:pointer;transition:border-color .2s}
.sort-sel:focus{border-color:var(--red)}

.prod-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
.prod-card{background:var(--white);border:1px solid var(--border);border-radius:var(--r-md);overflow:hidden;transition:box-shadow .3s,transform .3s;position:relative}
.prod-card:hover{box-shadow:var(--sh-md);transform:translateY(-4px)}
.prod-img-wrap{position:relative;aspect-ratio:1;background:var(--navy);overflow:hidden}
.prod-img-wrap>img{position:relative;z-index:1;width:100%;height:100%;object-fit:cover;transition:transform .4s var(--ease)}
.prod-card:hover .prod-img-wrap>img{transform:scale(1.06)}
.badge-sale{position:absolute;top:10px;right:10px;background:var(--red);color:#fff;font-size:11px;font-weight:800;padding:3px 9px;border-radius:50px;z-index:2}
.prod-actions{position:absolute;bottom:0;left:0;right:0;background:linear-gradient(to top,rgba(22,32,57,.85),transparent);padding:24px 10px 10px;display:flex;gap:6px;transform:translateY(100%);transition:transform .3s var(--ease);z-index:2}
.prod-card:hover .prod-actions{transform:none}
.pa-btn{flex:1;padding:8px;border:none;border-radius:var(--r-sm);font-family:var(--font);font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:5px;transition:background .15s}
.pa-cart{background:var(--red);color:#fff}
.pa-cart:hover{background:var(--red-d)}
.pa-wish{width:34px;flex:none;background:rgba(255,255,255,.9);color:var(--navy);border-radius:var(--r-sm)}
.pa-wish:hover{background:#fff;color:var(--red)}
.prod-body{padding:13px}
.prod-name{font-size:13.5px;font-weight:700;color:var(--navy);line-height:1.45;margin-bottom:8px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.prod-name a:hover{color:var(--red)}
.stars{display:flex;gap:2px;color:var(--amber);font-size:11px;margin-bottom:8px;align-items:center}
.stars span{color:var(--text-3);font-size:11px;margin-right:3px}
.prod-price{display:flex;align-items:center;gap:7px;flex-wrap:wrap}
.price-now{font-size:16px;font-weight:900;color:var(--red)}
.price-was{font-size:12px;color:var(--text-3);text-decoration:line-through}

/* ── SKELETON ── */
.skel{background:linear-gradient(90deg,#eeebe5 25%,#e5e0d8 50%,#eeebe5 75%);background-size:200% 100%;animation:skel-sh 1.4s infinite;border-radius:6px}
@keyframes skel-sh{0%{background-position:200% 0}100%{background-position:-200% 0}}

/* ── PAGINATION ── */
.pager{display:flex;align-items:center;justify-content:center;gap:6px;margin-top:32px}
.page-btn{width:38px;height:38px;border-radius:50%;border:1.5px solid var(--border);background:var(--white);cursor:pointer;font-family:var(--font);font-size:14px;font-weight:700;color:var(--text-2);display:flex;align-items:center;justify-content:center;transition:all .2s}
.page-btn:hover{border-color:var(--red);color:var(--red)}
.page-btn.active{background:var(--red);border-color:var(--red);color:#fff}
.page-btn:disabled{opacity:.4;cursor:default}

/* ── EMPTY ── */
.empty{text-align:center;padding:64px 20px}
.empty i{font-size:52px;color:var(--border);display:block;margin-bottom:14px}
.empty p{font-size:15px;color:var(--text-2)}

/* ── CART ── */
.cart-veil{position:fixed;inset:0;background:rgba(10,15,30,.55);z-index:400;opacity:0;visibility:hidden;transition:opacity .3s,visibility .3s;backdrop-filter:blur(2px)}
.cart-veil.on{opacity:1;visibility:visible}
.cart-drawer{position:fixed;top:0;right:-420px;width:400px;height:100%;background:var(--white);z-index:401;display:flex;flex-direction:column;transition:right .35s var(--ease);box-shadow:-8px 0 40px rgba(0,0,0,.18)}
.cart-drawer.on{right:0}
.cart-hd{display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid var(--border)}
.cart-hd-title{font-size:17px;font-weight:900}
.cart-x{width:34px;height:34px;border-radius:50%;border:none;background:var(--surface);cursor:pointer;font-size:17px;display:flex;align-items:center;justify-content:center;transition:background .2s}
.cart-x:hover{background:var(--red);color:#fff}
.cart-bd{flex:1;overflow-y:auto;padding:16px}
.cart-empty-msg{height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;color:var(--text-2)}
.cart-empty-msg i{font-size:58px;color:var(--border)}
.cart-ft{padding:16px 20px;border-top:1px solid var(--border)}
.cart-total-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.cart-total-row .label{font-size:14px;color:var(--text-2)}
.cart-total-row .val{font-size:20px;font-weight:900;color:var(--red)}
.cart-item{display:flex;gap:12px;align-items:center;padding:12px 0;border-bottom:1px solid var(--border)}
.cart-item-img{width:58px;height:58px;border-radius:var(--r-sm);background:var(--surface);flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center}
.cart-item-img img{width:100%;height:100%;object-fit:cover}
.cart-item-img i{font-size:22px;color:var(--border)}
.cart-item-info{flex:1;min-width:0}
.cart-item-name{font-size:13px;font-weight:700;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.cart-item-price{font-size:14px;font-weight:900;color:var(--red);margin-top:4px}
.qty-ctrl{display:flex;align-items:center;gap:6px}
.qty-btn{width:26px;height:26px;border-radius:50%;border:1px solid var(--border);background:none;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;transition:background .15s,border-color .15s}
.qty-btn:hover{background:var(--red);border-color:var(--red);color:#fff}
.qty-val{min-width:22px;text-align:center;font-weight:800;font-size:14px}
.cart-item-del{background:none;border:none;cursor:pointer;color:var(--text-3);font-size:15px;padding:4px;transition:color .2s}
.cart-item-del:hover{color:var(--red)}
.btn-red{display:inline-flex;align-items:center;gap:8px;background:var(--red);color:#fff;padding:13px 28px;border-radius:50px;font-size:15px;font-weight:800;border:none;cursor:pointer;transition:background .2s,transform .15s,box-shadow .2s}
.btn-red:hover{background:var(--red-d);transform:translateY(-2px)}

/* ── TOAST ── */
.toasts{position:fixed;bottom:22px;right:22px;z-index:600;display:flex;flex-direction:column;gap:8px}
.toast{background:var(--navy);color:#fff;padding:13px 18px;border-radius:var(--r-md);font-size:14px;font-weight:600;display:flex;align-items:center;gap:9px;box-shadow:var(--sh-lg);animation:t-in .3s var(--ease),t-out .3s var(--ease) 2.6s forwards}
.toast.ok{border-right:3px solid #22C55E}
.toast.err{border-right:3px solid var(--red)}
@keyframes t-in{from{opacity:0;transform:translateX(16px)}to{opacity:1;transform:none}}
@keyframes t-out{to{opacity:0;transform:translateX(16px)}}

/* ── RESPONSIVE ── */
@media(max-width:1000px){.prod-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:780px){.products-layout{grid-template-columns:1fr}.sidebar{display:none}.prod-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:500px){.search-wrap{display:none}.menu-toggle{display:flex!important}.nav-list{display:none}}
</style>
</head>
<body>

<!-- HEADER -->
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
      <div class="search-wrap">
        <input id="search-q" type="text" placeholder="ابحث عن منتج...">
        <button class="s-btn" onclick="doSearch()"><i class="fa fa-search"></i></button>
      </div>
      <div class="h-actions">
        <button class="icon-btn" onclick="openCart()" title="السلة">
          <i class="fa fa-bag-shopping"></i>
          <span class="badge off" id="cart-badge">0</span>
        </button>
      </div>
    </div>
  </div>
  <nav class="navbar">
    <div class="wrap" style="position:relative">
      <button class="menu-toggle" onclick="toggleMobile()"><i class="fa fa-bars"></i></button>
      <ul class="nav-list" id="nav-list">
        <li class="nav-item"><a href="/" class="nav-link"><i class="fa fa-house" style="font-size:12px"></i> الرئيسية</a></li>
        <li class="nav-item">
          <a href="/storefront/products" class="nav-link active">المنتجات <i class="fa fa-chevron-down" style="font-size:9px"></i></a>
          <div class="mega-menu" id="cat-menu">
            <a href="/storefront/products"><i class="fa fa-border-all"></i> كل المنتجات</a>
            <div class="sep"></div>
          </div>
        </li>
        <li class="nav-item"><a href="/storefront/offers" class="nav-link">العروض</a></li>
        <li class="nav-item"><a href="/storefront/contact" class="nav-link">اتصل بنا</a></li>
      </ul>
    </div>
  </nav>
  <div class="mobile-nav" id="mobile-nav">
    <a href="/"><i class="fa fa-house"></i> الرئيسية</a>
    <a href="/storefront/products"><i class="fa fa-border-all"></i> المنتجات</a>
    <a href="/storefront/contact"><i class="fa fa-phone"></i> اتصل بنا</a>
  </div>
</header>

<!-- PAGE HEADER -->
<div class="page-hd">
  <div class="wrap page-hd-inner">
    <div class="breadcrumb">
      <a href="/">الرئيسية</a>
      <i class="fa fa-chevron-left"></i>
      <span id="bc-cat">المنتجات</span>
    </div>
    <h1 id="page-h1">جميع المنتجات</h1>
    <p id="page-sub">اكتشف تشكيلتنا الكاملة من المطبوعات الاحترافية</p>
  </div>
</div>

<!-- PRODUCTS LAYOUT -->
<div class="wrap">
  <div class="products-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="sidebar-box">
        <div class="sb-head"><i class="fa fa-border-all"></i> التصنيفات</div>
        <div class="cat-list" id="cat-list">
          <div class="cat-item active" onclick="filterCat(null)" data-id="">
            <i class="fa fa-th-large"></i> كل المنتجات
          </div>
        </div>
      </div>
    </aside>

    <!-- PRODUCTS -->
    <div class="products-area">
      <div class="area-top">
        <div class="results-info" id="results-info">جاري التحميل...</div>
        <select class="sort-sel" id="sort-sel" onchange="applySortFilter()">
          <option value="latest">الأحدث</option>
          <option value="price_asc">السعر: من الأقل</option>
          <option value="price_desc">السعر: من الأعلى</option>
          <option value="name">الاسم</option>
        </select>
      </div>
      <div class="prod-grid" id="prod-grid">
        <!-- skeleton -->
        <div style="border-radius:14px;overflow:hidden;background:#fff;border:1px solid #d6e8d9"><div class="skel" style="aspect-ratio:1"></div><div style="padding:13px"><div class="skel" style="height:13px;margin-bottom:8px"></div><div class="skel" style="height:11px;width:60%"></div></div></div>
        <div style="border-radius:14px;overflow:hidden;background:#fff;border:1px solid #d6e8d9"><div class="skel" style="aspect-ratio:1"></div><div style="padding:13px"><div class="skel" style="height:13px;margin-bottom:8px"></div><div class="skel" style="height:11px;width:60%"></div></div></div>
        <div style="border-radius:14px;overflow:hidden;background:#fff;border:1px solid #d6e8d9"><div class="skel" style="aspect-ratio:1"></div><div style="padding:13px"><div class="skel" style="height:13px;margin-bottom:8px"></div><div class="skel" style="height:11px;width:60%"></div></div></div>
        <div style="border-radius:14px;overflow:hidden;background:#fff;border:1px solid #d6e8d9"><div class="skel" style="aspect-ratio:1"></div><div style="padding:13px"><div class="skel" style="height:13px;margin-bottom:8px"></div><div class="skel" style="height:11px;width:60%"></div></div></div>
      </div>
      <div class="empty" id="empty-state" style="display:none">
        <i class="fa fa-box-open"></i>
        <p>لا توجد منتجات في هذا التصنيف</p>
      </div>
      <div class="pager" id="pager" style="display:none"></div>
    </div>

  </div>
</div>

<!-- CART -->
<div class="cart-veil" id="cart-veil" onclick="closeCart()"></div>
<div class="cart-drawer" id="cart-drawer">
  <div class="cart-hd">
    <span class="cart-hd-title">🛒 سلة التسوق</span>
    <button class="cart-x" onclick="closeCart()"><i class="fa fa-xmark"></i></button>
  </div>
  <div class="cart-bd" id="cart-bd">
    <div class="cart-empty-msg"><i class="fa fa-cart-shopping"></i><p>السلة فارغة</p></div>
  </div>
  <div class="cart-ft" id="cart-ft" style="display:none">
    <div class="cart-total-row"><span class="label">المجموع:</span><span class="val" id="cart-tot">0 ₪</span></div>
    <a href="/storefront/checkout" class="btn-red" style="width:100%;justify-content:center;display:flex">
      <i class="fa fa-credit-card"></i> إتمام الشراء
    </a>
  </div>
</div>
<div class="toasts" id="toasts"></div>

<script>
const API = window.location.origin + '/api/v1';
let CUR = '₪';
let allProducts = [];
let activeCatId = null;
let catIdx = 0;

/* ── STYLE MAP ── */
const prodIconMap = [
  ['درع','دروع','كأس','كريستال','نحاس','ألومنيوم','خشب','زجاج','لوح'],['fa-trophy','#134e2a','#10b46a'],
  ['لافت','شادر','رول','إكس','بانر'],['fa-rectangle-ad','#1e3a5f','#60a5fa'],
  ['بطاق','كرت','دعوة','تهنئة'],['fa-id-card','#4c1d95','#a78bfa'],
  ['ليبل','ملصق','ستيكر'],['fa-tag','#7c2d12','#fb923c'],
  ['كوب','كاس','مج'],['fa-mug-hot','#78350f','#fbbf24'],
  ['ختم','طغر'],['fa-stamp','#3b0764','#d8b4fe'],
  ['هدي','هدية'],['fa-gift','#134e2a','#34d399'],
];
function getProdStyle(name) {
  const n = name||'';
  for(let i=0;i<prodIconMap.length;i+=2)
    if(prodIconMap[i].some(k=>n.includes(k))) return {icon:prodIconMap[i+1][0],grad:`linear-gradient(145deg,${prodIconMap[i+1][1]},#0f1512)`,accent:prodIconMap[i+1][2]};
  return {icon:'fa-box-open',grad:'linear-gradient(145deg,#1a2e1f,#0f1512)',accent:'#10b46a'};
}
const catIcons = {'درع':'fa-trophy','دروع':'fa-trophy','لافت':'fa-rectangle-ad','شادر':'fa-rectangle-ad','بطاق':'fa-id-card','كرت':'fa-id-card','ليبل':'fa-tag','ملصق':'fa-tag','كوب':'fa-mug-hot','كاس':'fa-mug-hot','ختم':'fa-stamp','هدي':'fa-gift','رول':'fa-scroll','إكس':'fa-scroll'};
function getCatIcon(n){for(const[k,v]of Object.entries(catIcons))if(n&&n.includes(k))return v;return'fa-print'}

/* ── CART ── */
let cart = JSON.parse(localStorage.getItem('f_cart')||'[]');
const saveCart = () => { localStorage.setItem('f_cart',JSON.stringify(cart)); renderCart(); };
function renderCart() {
  const badge=document.getElementById('cart-badge'),bd=document.getElementById('cart-bd'),ft=document.getElementById('cart-ft'),tot=document.getElementById('cart-tot');
  const count=cart.reduce((s,i)=>s+i.qty,0);
  badge.textContent=count; badge.classList.toggle('off',count===0);
  if(!cart.length){bd.innerHTML='<div class="cart-empty-msg"><i class="fa fa-cart-shopping"></i><p>السلة فارغة</p></div>';ft.style.display='none';}
  else{
    bd.innerHTML=cart.map((item,i)=>`<div class="cart-item"><div class="cart-item-img">${item.img?`<img src="${item.img}" alt="">`:' <i class="fa fa-box"></i>'}</div><div class="cart-item-info"><div class="cart-item-name">${item.name}</div><div class="cart-item-price">${(parseFloat(item.price)*item.qty).toFixed(2)} ${CUR}</div></div><div class="qty-ctrl"><button class="qty-btn" onclick="qtyChange(${i},-1)">−</button><span class="qty-val">${item.qty}</span><button class="qty-btn" onclick="qtyChange(${i},1)">+</button></div><button class="cart-item-del" onclick="cartRemove(${i})"><i class="fa fa-xmark"></i></button></div>`).join('');
    const total=cart.reduce((s,i)=>s+parseFloat(i.price)*i.qty,0);
    tot.textContent=total.toFixed(2)+' '+CUR; ft.style.display='block';
  }
}
function addToCart(id,name,price,img){const ex=cart.find(i=>i.id===id);ex?ex.qty++:cart.push({id,name,price,img,qty:1});saveCart();toast('أُضيف للسلة ✓');}
function qtyChange(i,d){cart[i].qty+=d;if(cart[i].qty<=0)cart.splice(i,1);saveCart();}
function cartRemove(i){cart.splice(i,1);saveCart();}
function openCart(){document.getElementById('cart-veil').classList.add('on');document.getElementById('cart-drawer').classList.add('on');}
function closeCart(){document.getElementById('cart-veil').classList.remove('on');document.getElementById('cart-drawer').classList.remove('on');}
function toggleWish(id){let wl=JSON.parse(localStorage.getItem('f_wl')||'[]');const i=wl.indexOf(id);i>-1?(wl.splice(i,1),toast('أُزيل من المفضلة','err')):(wl.push(id),toast('أُضيف للمفضلة ♥'));localStorage.setItem('f_wl',JSON.stringify(wl));}

/* ── TOAST ── */
function toast(msg,type='ok'){const tc=document.getElementById('toasts');const t=document.createElement('div');t.className=`toast ${type}`;t.innerHTML=`<i class="fa fa-${type==='ok'?'circle-check':'circle-xmark'}"></i> ${msg}`;tc.appendChild(t);setTimeout(()=>t.remove(),3100);}

/* ── PRODUCT CARD ── */
function prodCard(p){
  const rawImgs=Array.isArray(p.image_fullpath)?p.image_fullpath:(p.image_fullpath?[p.image_fullpath]:[]);
  const hasReal=rawImgs.length&&!rawImgs[0].includes('img2.jpg');
  const imgSrc=hasReal?rawImgs[0]:null;
  const price=parseFloat(p.price||0),disc=parseFloat(p.discount||0);
  const now=disc>0?(price-price*disc/100).toFixed(2):price.toFixed(2);
  const ps=getProdStyle(p.name);
  const safeName=p.name.replace(/'/g,"\\'").replace(/"/g,'&quot;');
  return `<div class="prod-card">
    <a href="/storefront/product/${p.id}">
      <div class="prod-img-wrap" style="background:${ps.grad}">
        ${imgSrc?`<img src="${imgSrc}" alt="${p.name}" loading="lazy" onerror="this.remove()">` : ''}
        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;${imgSrc?'opacity:0':''}">
          <i class="fa ${ps.icon}" style="font-size:52px;color:${ps.accent};opacity:.55"></i>
        </div>
        ${disc>0?`<span class="badge-sale">-${Math.round(disc)}%</span>`:''}
        <div class="prod-actions">
          <button class="pa-btn pa-cart" onclick="event.preventDefault();addToCart(${p.id},'${safeName}',${now},'${imgSrc||''}')"><i class="fa fa-cart-plus"></i> أضف</button>
          <button class="pa-btn pa-wish" onclick="event.preventDefault();toggleWish(${p.id})"><i class="fa fa-heart"></i></button>
        </div>
      </div>
    </a>
    <div class="prod-body">
      <h3 class="prod-name"><a href="/storefront/product/${p.id}">${p.name}</a></h3>
      <div class="prod-price"><span class="price-now">${now} ${CUR}</span>${disc>0?`<span class="price-was">${price.toFixed(2)} ${CUR}</span>`:''}</div>
    </div>
  </div>`;
}

/* ── FILTER & SORT ── */
let _allProds = [], _cats = [], _pBase = '';
let _activeCat = null;

async function filterCat(catId) {
  _activeCat = catId;
  // Update sidebar
  document.querySelectorAll('.cat-item').forEach(el => el.classList.toggle('active', el.dataset.id == (catId||'')));
  // Update heading
  if (!catId) {
    document.getElementById('page-h1').textContent = 'جميع المنتجات';
    document.getElementById('bc-cat').textContent = 'المنتجات';
    document.getElementById('page-sub').textContent = 'اكتشف تشكيلتنا الكاملة';
  } else {
    const cat = _cats.find(c=>c.id==catId);
    if(cat){
      document.getElementById('page-h1').textContent = cat.name;
      document.getElementById('bc-cat').textContent = cat.name;
      document.getElementById('page-sub').textContent = `منتجات تصنيف ${cat.name}`;
      // update URL
      history.replaceState(null,'',`?category=${catId}`);
    }
  }
  showProds();
  if (catId !== null) {
    try {
      document.getElementById('prod-grid').innerHTML = `<div style="border-radius:14px;overflow:hidden;background:#fff;border:1px solid #d6e8d9"><div class="skel" style="aspect-ratio:1"></div></div>`.repeat(4);
      const data = await fetch(`${API}/categories/products/${catId}`).then(r=>r.json());
      const list = Array.isArray(data)?data:(data.products||[]);
      renderProds(list);
    } catch(e){renderProds([]);}
  } else {
    renderProds(_allProds);
  }
}

function applySortFilter() {
  const sort = document.getElementById('sort-sel').value;
  let list = [...(_activeCat ? document.currentProds||[] : _allProds)];
  if(sort==='price_asc') list.sort((a,b)=>a.price-b.price);
  else if(sort==='price_desc') list.sort((a,b)=>b.price-a.price);
  else if(sort==='name') list.sort((a,b)=>(a.name||'').localeCompare(b.name||'','ar'));
  renderProds(list);
}

function renderProds(list) {
  document.currentProds = list;
  const grid = document.getElementById('prod-grid');
  const empty = document.getElementById('empty-state');
  document.getElementById('results-info').innerHTML = `<strong>${list.length}</strong> منتج`;
  if(!list.length){grid.innerHTML='';empty.style.display='block';}
  else{empty.style.display='none';grid.innerHTML=list.map(p=>prodCard(p)).join('');}
}

function showProds() { /* placeholder for future pagination */ }
function doSearch(){const q=document.getElementById('search-q').value.trim();if(q)location.href=`?q=${encodeURIComponent(q)}`;}
document.getElementById('search-q').addEventListener('keydown',e=>e.key==='Enter'&&doSearch());
function toggleMobile(){document.getElementById('mobile-nav').classList.toggle('open');}

/* ── INIT ── */
async function init() {
  const urlParams = new URLSearchParams(window.location.search);
  const startCat = urlParams.get('category');
  // Pre-fill search box if ?q= is in URL
  const preQ = urlParams.get('q');
  if(preQ) document.getElementById('search-q').value = preQ;

  let cfg={};
  try{
    cfg = await fetch(`${API}/config`).then(r=>r.json());
    const name = cfg.ecommerce_name||cfg.business_name||'ايليت دعاية';
    if(cfg.currency_symbol) CUR=cfg.currency_symbol;
    document.getElementById('store-name').textContent=name;
    document.title = `المنتجات — ${name}`;
    const logoUrl=cfg.logo_full_url||'';
    if(logoUrl){document.getElementById('logo-mark').innerHTML=`<img src="${logoUrl}" alt="${name}" style="width:100%;height:100%;object-fit:contain">`;}
    else{document.getElementById('logo-mark').textContent=name.charAt(0);}
  }catch(e){}

  _pBase = cfg.base_urls?.product_image_url||'';

  // Load cats + all products in parallel
  const [catsRes, prodsRes] = await Promise.allSettled([
    fetch(`${API}/categories`).then(r=>r.json()),
    fetch(`${API}/products/latest?limit=63`).then(r=>r.json()),
  ]);

  if(catsRes.status==='fulfilled'){
    _cats = Array.isArray(catsRes.value)?catsRes.value:(catsRes.value.categories||[]);
    const list = document.getElementById('cat-list');
    _cats.forEach(c=>{
      const item=document.createElement('div');
      item.className='cat-item';
      item.dataset.id=c.id;
      item.onclick=()=>filterCat(c.id);
      item.innerHTML=`<i class="fa ${getCatIcon(c.name)}"></i>${c.name}${c.products_count>0?`<span class="cnt">${c.products_count}</span>`:''}`;
      list.appendChild(item);
    });
    // Nav dropdown
    document.getElementById('cat-menu').innerHTML=
      `<a href="/storefront/products"><i class="fa fa-border-all"></i> كل المنتجات</a><div class="sep"></div>`
      +_cats.map(c=>`<a href="/storefront/products?category=${c.id}"><i class="fa ${getCatIcon(c.name)}"></i>${c.name}</a>`).join('');
    // Mobile nav
    _cats.slice(0,5).forEach(c=>{const a=document.createElement('a');a.href=`/storefront/products?category=${c.id}`;a.innerHTML=`<i class="fa ${getCatIcon(c.name)}"></i>${c.name}`;document.getElementById('mobile-nav').appendChild(a);});
  }

  if(prodsRes.status==='fulfilled'){
    const d=prodsRes.value;
    _allProds=Array.isArray(d)?d:(d.products||[]);
  }

  // Apply start category or search query
  const searchQ = urlParams.get('q');
  if(searchQ) {
    await searchProds(searchQ);
  } else if(startCat) {
    await filterCat(parseInt(startCat));
  } else {
    renderProds(_allProds);
  }
}

async function searchProds(q) {
  document.getElementById('page-h1').textContent = `نتائج البحث: ${q}`;
  document.getElementById('bc-cat').textContent = 'بحث';
  document.getElementById('page-sub').textContent = 'جاري البحث...';
  document.getElementById('prod-grid').innerHTML = `<div style="border-radius:14px;overflow:hidden;background:#fff;border:1px solid #d6e8d9"><div class="skel" style="aspect-ratio:1"></div></div>`.repeat(4);
  try {
    const data = await fetch(`${API}/products/search?name=${encodeURIComponent(q)}&limit=50`).then(r=>r.json());
    const list = Array.isArray(data) ? data : (data.products||data.value||[]);
    document.getElementById('page-sub').textContent = `${list.length} نتيجة`;
    document.getElementById('results-info').innerHTML = `<strong>${list.length}</strong> نتيجة للبحث عن "<em>${q}</em>"`;
    renderProds(list);
  } catch(e) {
    document.getElementById('page-sub').textContent = 'تعذّر البحث';
    renderProds([]);
  }
}

renderCart();
init();
</script>
</body>
</html>
