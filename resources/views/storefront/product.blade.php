<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title id="page-title">تفاصيل المنتج — ايليت دعاية</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root {
  --red:#10b46a; --red-d:#0c8f52; --red-10:rgba(16,180,106,.10);
  --navy:#0f1512; --amber:#f59e0b;
  --surface:#f5f8f4; --white:#FFFFFF; --border:#d6e8d9;
  --text:#0f1512; --text-2:#4a5e50; --text-3:#8fa895;
  --r-sm:8px; --r-md:14px; --r-lg:22px;
  --sh-sm:0 2px 8px rgba(15,21,18,.07); --sh-md:0 6px 20px rgba(15,21,18,.11);
  --max-w:1260px; --font:'IBM Plex Sans Arabic','Cairo',sans-serif;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:var(--font);background:var(--surface);color:var(--text);direction:rtl;-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}
ul{list-style:none}
img{display:block;max-width:100%}
button{font-family:var(--font);cursor:pointer}
.wrap{max-width:var(--max-w);margin-inline:auto;padding-inline:20px}

/* TOPBAR */
.topbar{background:var(--navy);padding:7px 0;font-size:13px;color:#8CA0BE;display:none}
.topbar.visible{display:block}
.topbar-inner{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
.topbar-left{display:flex;gap:18px}
.topbar-left a{display:flex;align-items:center;gap:5px;color:#8CA0BE;transition:color .2s}
.topbar-left a:hover{color:#fff}
.topbar-left i{color:var(--amber);font-size:11px}
.topbar-right{display:flex;gap:14px}
.topbar-right a{color:#8CA0BE;font-size:12px;transition:color .2s}
.topbar-right a:hover{color:var(--amber)}

/* HEADER */
.header{background:var(--white);position:sticky;top:0;z-index:200;box-shadow:0 1px 0 var(--border)}
.header-body{display:flex;align-items:center;gap:20px;padding:12px 0}
.logo{display:flex;align-items:center;gap:10px;flex-shrink:0}
.logo-mark{width:120px;height:50px;background:#000;border:1.5px solid #222;border-radius:12px;display:flex;align-items:center;justify-content:center;overflow:hidden;padding:5px 10px;box-shadow:0 2px 8px rgba(15,21,18,.08)}
.logo-name{font-size:17px;font-weight:900;color:var(--navy);line-height:1.2}
.logo-tag{font-size:11px;color:var(--text-3);font-weight:500}
.search-wrap{flex:1;display:flex;align-items:center;background:#f5f8f4;border:1.5px solid var(--border);border-radius:12px;padding:0 12px;gap:10px;max-width:520px;transition:border-color .2s}
.search-wrap:focus-within{border-color:var(--red)}
.search-wrap input{flex:1;border:none;background:none;outline:none;font-family:var(--font);font-size:14px;color:var(--text);padding:10px 0}
.s-btn{background:var(--red);color:#fff;border:none;border-radius:9px;padding:7px 14px;font-size:13px;transition:background .2s;cursor:pointer}
.s-btn:hover{background:var(--red-d)}
.h-actions{display:flex;align-items:center;gap:10px;margin-inline-start:auto}
.wa-btn{display:flex;align-items:center;gap:7px;background:#25d366;color:#fff;padding:8px 16px;border-radius:10px;font-size:13px;font-weight:600;transition:background .2s}
.wa-btn:hover{background:#1aa84e}
.wa-btn .fab{font-size:16px}
.icon-btn{position:relative;width:42px;height:42px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:#f5f8f4;border:1.5px solid var(--border);color:var(--text-2);font-size:16px;transition:all .2s;cursor:pointer}
.icon-btn:hover{background:var(--red-10);border-color:var(--red);color:var(--red)}
.badge{position:absolute;top:-5px;right:-5px;background:var(--red);color:#fff;width:18px;height:18px;border-radius:50%;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center}
.badge.off{display:none}

/* BREADCRUMB */
.bc{background:var(--white);border-bottom:1px solid var(--border);padding:12px 0;font-size:13px;color:var(--text-3)}
.bc-inner{display:flex;align-items:center;gap:8px}
.bc-inner a{color:var(--text-3);transition:color .2s}
.bc-inner a:hover{color:var(--red)}
.bc-inner i{font-size:10px}

/* PRODUCT PAGE */
.prod-page{padding:40px 0 60px}
.prod-layout{display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:start}
.prod-gallery{position:sticky;top:80px}
.prod-main-img{width:100%;aspect-ratio:1;border-radius:var(--r-lg);overflow:hidden;background:linear-gradient(145deg,#134e2a,#1e7a43);display:flex;align-items:center;justify-content:center;margin-bottom:12px}
.prod-main-img img{width:100%;height:100%;object-fit:cover}
.prod-main-icon{font-size:100px;color:rgba(16,180,106,.5)}
.prod-thumbs{display:flex;gap:10px;flex-wrap:wrap}
.thumb{width:72px;height:72px;border-radius:10px;overflow:hidden;cursor:pointer;border:2px solid transparent;transition:border-color .2s}
.thumb.active,.thumb:hover{border-color:var(--red)}
.thumb img{width:100%;height:100%;object-fit:cover}

/* PRODUCT INFO */
.prod-info{}
.prod-badge-row{display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap}
.badge-cat{display:inline-flex;align-items:center;gap:5px;background:var(--red-10);color:var(--red);padding:5px 12px;border-radius:50px;font-size:12px;font-weight:600}
.badge-disc{background:var(--amber);color:var(--navy);padding:5px 12px;border-radius:50px;font-size:12px;font-weight:700}
.prod-name{font-size:26px;font-weight:800;color:var(--navy);line-height:1.4;margin-bottom:10px}
.prod-stars{display:flex;align-items:center;gap:8px;margin-bottom:18px;font-size:14px;color:var(--text-2)}
.stars-gold{color:#f59e0b;font-size:15px;letter-spacing:1px}
.prod-price-row{display:flex;align-items:baseline;gap:12px;margin-bottom:22px}
.price-now{font-size:32px;font-weight:900;color:var(--red)}
.price-was{font-size:18px;color:var(--text-3);text-decoration:line-through}
.price-unit{font-size:13px;color:var(--text-3);margin-top:4px}
.prod-desc{font-size:15px;color:var(--text-2);line-height:1.85;margin-bottom:24px}
.divider{height:1px;background:var(--border);margin:20px 0}

/* QTY & ACTION */
.qty-row{display:flex;align-items:center;gap:16px;margin-bottom:22px}
.qty-label{font-size:14px;font-weight:600;color:var(--text-2)}
.qty-ctrl{display:flex;align-items:center;border:1.5px solid var(--border);border-radius:10px;overflow:hidden}
.qty-btn{width:38px;height:38px;display:flex;align-items:center;justify-content:center;background:#f5f8f4;border:none;font-size:16px;font-weight:700;color:var(--text);cursor:pointer;transition:background .2s}
.qty-btn:hover{background:var(--red-10);color:var(--red)}
.qty-val{width:48px;text-align:center;font-size:16px;font-weight:700;color:var(--navy);border:none;outline:none;background:var(--white)}
.btn-add-cart{flex:1;background:var(--red);color:#fff;border:none;border-radius:12px;padding:14px 28px;font-size:15px;font-weight:700;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .2s;cursor:pointer}
.btn-add-cart:hover{background:var(--red-d)}
.btn-wish{width:50px;height:50px;display:flex;align-items:center;justify-content:center;border:1.5px solid var(--border);border-radius:12px;background:var(--white);color:var(--text-2);font-size:18px;cursor:pointer;transition:all .2s;flex-shrink:0}
.btn-wish:hover,.btn-wish.active{background:var(--red-10);border-color:var(--red);color:var(--red)}

/* INFO TABLE */
.info-table{width:100%;border-collapse:collapse;font-size:14px}
.info-table tr:nth-child(even){background:var(--surface)}
.info-table td{padding:9px 14px;border:1px solid var(--border)}
.info-table td:first-child{font-weight:600;color:var(--text-2);width:40%}

/* RELATED */
.related-sec{padding:50px 0}
.sec-title{font-size:20px;font-weight:800;color:var(--navy);margin-bottom:24px}
.prod-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
.prod-card{background:var(--white);border:1.5px solid var(--border);border-radius:var(--r-md);overflow:hidden;transition:box-shadow .2s,transform .2s}
.prod-card:hover{box-shadow:var(--sh-md);transform:translateY(-3px)}
.prod-img-wrap{position:relative;aspect-ratio:1;overflow:hidden}
.prod-actions{position:absolute;bottom:10px;left:50%;transform:translateX(-50%) translateY(10px);display:flex;gap:8px;opacity:0;transition:all .25s}
.prod-card:hover .prod-actions{opacity:1;transform:translateX(-50%) translateY(0)}
.pa-btn{background:rgba(255,255,255,.95);border:none;border-radius:8px;padding:7px 12px;font-size:12px;font-weight:600;display:flex;align-items:center;gap:5px;cursor:pointer;color:var(--navy);backdrop-filter:blur(4px);white-space:nowrap}
.pa-btn:hover{background:var(--red);color:#fff}
.pa-wish{padding:7px 9px}
.badge-sale{position:absolute;top:10px;right:10px;background:var(--amber);color:var(--navy);padding:3px 8px;border-radius:6px;font-size:11px;font-weight:700}
.prod-body{padding:14px}
.prod-name{font-size:14px;font-weight:600;color:var(--navy);margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.prod-name a{color:inherit}
.prod-price{display:flex;align-items:baseline;gap:8px}
.price-sm{font-size:15px;font-weight:800;color:var(--red)}
.price-sm-was{font-size:12px;color:var(--text-3);text-decoration:line-through}

/* SKELETON */
.skel{background:linear-gradient(90deg,#e8f0ea 25%,#d4e8d8 50%,#e8f0ea 75%);background-size:200% 100%;animation:shimmer 1.4s infinite}
@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}

/* CART */
.cart-veil{position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:500;opacity:0;pointer-events:none;transition:opacity .3s}
.cart-veil.on{opacity:1;pointer-events:all}
.cart-drawer{position:fixed;top:0;right:-420px;width:420px;height:100%;background:#fff;z-index:501;box-shadow:var(--sh-md);transition:right .3s;display:flex;flex-direction:column}
.cart-drawer.on{right:0}
.cart-hd{display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid var(--border);font-weight:700;font-size:16px}
.cart-x{background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-2)}
.cart-bd{flex:1;overflow-y:auto;padding:12px 20px}
.cart-ft{padding:16px 20px;border-top:1px solid var(--border)}
.cart-total-row{display:flex;justify-content:space-between;font-size:15px;font-weight:700;margin-bottom:14px}
.cart-item{display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border)}
.cart-item-img{width:52px;height:52px;border-radius:8px;background:var(--surface);overflow:hidden;flex-shrink:0}
.cart-item-img img{width:100%;height:100%;object-fit:cover}
.cart-item-name{font-size:13px;font-weight:600;color:var(--navy)}
.cart-item-price{font-size:12px;color:var(--text-2);margin-top:2px}
.cart-item-del{margin-inline-start:auto;background:none;border:none;color:var(--text-3);cursor:pointer;font-size:16px}
.cart-item-del:hover{color:#e74c3c}
.cart-empty-msg{display:flex;flex-direction:column;align-items:center;gap:14px;padding:50px 0;color:var(--text-3);text-align:center}
.cart-empty-msg i{font-size:48px}
.btn-red{display:inline-flex;align-items:center;gap:8px;background:var(--red);color:#fff;padding:11px 22px;border-radius:12px;font-size:14px;font-weight:700;transition:background .2s;cursor:pointer;border:none}
.btn-red:hover{background:var(--red-d)}

/* DESIGN BUTTON */
.btn-design{
  flex:1;background:#fff;color:var(--red);border:2px solid var(--red);
  border-radius:12px;padding:14px 28px;font-size:15px;font-weight:700;
  display:flex;align-items:center;justify-content:center;gap:8px;
  transition:all .2s;cursor:pointer
}
.btn-design:hover{background:var(--red-10)}

/* ══ DESIGN MODAL — Advanced Canva-like Editor ══ */
.design-veil{position:fixed;inset:0;background:rgba(0,0,0,.9);z-index:600;display:none;flex-direction:column}
.design-veil.on{display:flex}
.design-modal{flex:1;display:flex;flex-direction:column;overflow:hidden;font-family:'Cairo',sans-serif;background:#141817;min-height:0}

/* Header / topbar */
.dm-header{
  background:#0e1210;border-bottom:1px solid #2a3330;
  display:flex;align-items:center;gap:3px;padding:6px 10px;
  flex-shrink:0;min-height:48px;overflow-x:auto
}
.dm-header::-webkit-scrollbar{height:3px}
.dm-header::-webkit-scrollbar-thumb{background:#2a3330}
.dm-title{font-size:13px;font-weight:800;color:#e2ede6;white-space:nowrap;margin-inline-end:4px}
.dm-prod-lbl{font-size:11px;color:#7a9282;white-space:nowrap}
.dm-close{margin-inline-start:auto;background:none;border:none;color:#7a9282;font-size:18px;cursor:pointer;padding:5px 8px;border-radius:7px;transition:all .15s;flex-shrink:0}
.dm-close:hover{background:rgba(255,255,255,.1);color:#e2ede6}
.dm-body{flex:1;display:flex;overflow:hidden;min-height:0}

/* toolbar buttons */
.dm-tb{display:inline-flex;align-items:center;gap:4px;padding:5px 8px;background:transparent;border:1px solid transparent;border-radius:6px;color:#7a9282;font-family:'Cairo',sans-serif;font-size:11px;font-weight:600;cursor:pointer;white-space:nowrap;flex-shrink:0;transition:all .12s}
.dm-tb:hover{background:#202825;border-color:#2a3330;color:#e2ede6}
.dm-tb.on{background:rgba(16,180,106,.15);border-color:#10b46a;color:#10b46a}
.dm-tb.primary{background:#10b46a;border-color:#10b46a;color:#fff}
.dm-tb.primary:hover{background:#0c9456}
.dm-tb i{font-size:11px}
.dm-tb-sep{width:1px;height:24px;background:#2a3330;margin:0 3px;flex-shrink:0}
.dm-sz{background:#202825;border:1px solid #2a3330;border-radius:6px;color:#e2ede6;font-family:'Cairo',sans-serif;font-size:11px;padding:4px 7px;cursor:pointer;outline:none;flex-shrink:0}
.dm-zoom-lbl{font-size:10px;color:#7a9282;min-width:30px;text-align:center;flex-shrink:0}
#dm-align-bar{display:none;align-items:center;gap:2px}
#dm-draw-opts{display:none;align-items:center;gap:5px}
#dm-draw-opts label{font-size:10px;color:#7a9282}
#dm-draw-opts input[type=range]{width:70px;accent-color:#10b46a}
.dm-shapes-dd{position:relative;flex-shrink:0}
.dm-shapes-menu{display:none;position:absolute;top:calc(100% + 4px);right:0;background:#1a1f1d;border:1px solid #2a3330;border-radius:9px;padding:7px;z-index:400;min-width:150px;box-shadow:0 6px 20px rgba(0,0,0,.5)}
.dm-shapes-menu.open{display:grid;grid-template-columns:repeat(3,1fr);gap:4px}
.dm-shapes-menu button{display:flex;flex-direction:column;align-items:center;gap:3px;padding:6px 4px;background:#202825;border:1px solid #2a3330;border-radius:6px;color:#7a9282;font-size:10px;cursor:pointer;transition:all .12s}
.dm-shapes-menu button:hover{border-color:#10b46a;color:#10b46a}
.dm-shapes-menu button i{font-size:15px}

/* ── Left sidebar ── */
.dm-sidebar{width:190px;flex-shrink:0;background:#161b19;border-inline-end:1px solid #2a3330;display:flex;flex-direction:column;overflow:hidden}
.dm-sb-tabs{display:flex;border-bottom:1px solid #2a3330}
.dm-sb-tab{flex:1;padding:6px 2px;text-align:center;font-size:9px;color:#4a6055;cursor:pointer;border-bottom:2px solid transparent;transition:all .12s}
.dm-sb-tab:hover{color:#7a9282}
.dm-sb-tab.on{color:#10b46a;border-bottom-color:#10b46a}
.dm-sb-tab i{display:block;font-size:13px;margin-bottom:2px}
.dm-sb-panes{flex:1;overflow-y:auto;padding:9px}
.dm-sb-pane{display:none}
.dm-sb-pane.on{display:block}
.dm-sb-panes::-webkit-scrollbar{width:3px}
.dm-sb-panes::-webkit-scrollbar-thumb{background:#2a3330}
.dm-ssec{font-size:10px;font-weight:700;color:#4a6055;text-transform:uppercase;letter-spacing:.5px;margin:8px 0 5px}
.dm-ssec:first-child{margin-top:0}
.dm-s-row{display:flex;gap:4px;margin-bottom:5px;flex-wrap:wrap}
.dm-sbtn{flex:1;display:flex;align-items:center;justify-content:center;gap:3px;padding:6px 5px;background:#202825;border:1px solid #2a3330;border-radius:6px;color:#7a9282;font-family:'Cairo',sans-serif;font-size:10px;cursor:pointer;transition:all .12s;text-align:center;white-space:nowrap}
.dm-sbtn:hover{border-color:#10b46a;color:#10b46a;background:#252e2a}
.dm-sbtn i{font-size:12px}
.dm-styled-grid{display:grid;grid-template-columns:1fr 1fr;gap:4px;margin-bottom:6px}
.dm-styled{padding:6px 4px;background:#202825;border:1px solid #2a3330;border-radius:6px;cursor:pointer;text-align:center;font-size:10px;transition:all .12s;color:#e2ede6}
.dm-styled:hover{border-color:#10b46a;background:#252e2a}
.dm-fonts{display:flex;flex-wrap:wrap;gap:3px;margin-bottom:5px}
.dm-font{padding:3px 7px;border-radius:14px;font-size:10px;cursor:pointer;background:#202825;border:1px solid #2a3330;color:#7a9282;transition:all .12s}
.dm-font.on,.dm-font:hover{background:rgba(16,180,106,.15);border-color:#10b46a;color:#10b46a}
.dm-shapes-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:4px;margin-bottom:6px}
.dm-shape{display:flex;flex-direction:column;align-items:center;gap:2px;padding:7px 3px;background:#202825;border:1px solid #2a3330;border-radius:6px;cursor:pointer;font-size:9px;color:#7a9282;transition:all .12s}
.dm-shape:hover{border-color:#10b46a;color:#10b46a;background:#252e2a}
.dm-shape i{font-size:15px}
.dm-upload{border:2px dashed #2a3330;border-radius:7px;padding:14px 8px;text-align:center;cursor:pointer;margin-bottom:6px;transition:border-color .2s}
.dm-upload:hover{border-color:#10b46a}
.dm-upload i{font-size:20px;color:#4a6055;display:block;margin-bottom:3px}
.dm-upload p{font-size:10px;color:#7a9282}
.dm-upload input{display:none}
.dm-swatches{display:flex;flex-wrap:wrap;gap:4px;margin-bottom:6px}
.dm-swatch{width:22px;height:22px;border-radius:4px;cursor:pointer;border:2px solid rgba(255,255,255,.1);transition:border-color .12s;flex-shrink:0}
.dm-swatch:hover{border-color:#10b46a}
.dm-gradients{display:grid;grid-template-columns:repeat(4,1fr);gap:4px;margin-bottom:6px}
.dm-grad{height:24px;border-radius:4px;cursor:pointer;border:2px solid transparent;transition:border-color .12s}
.dm-grad:hover{border-color:#10b46a}
.dm-tmpl-grid{display:grid;grid-template-columns:1fr 1fr;gap:5px}
.dm-tmpl{border-radius:6px;overflow:hidden;cursor:pointer;border:2px solid #2a3330;transition:border-color .12s;background:#202825;padding:3px}
.dm-tmpl:hover{border-color:#10b46a}
.dm-tmpl img,.dm-tmpl .dm-tmpl-ph{width:100%;aspect-ratio:1;object-fit:cover;border-radius:3px;display:block}
.dm-tmpl .dm-tmpl-ph{background:#252e2a;display:flex;align-items:center;justify-content:center;color:#4a6055}
.dm-tmpl-nm{font-size:9px;text-align:center;color:#7a9282;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}

/* ── Canvas ── */
.dm-canvas-area{flex:1;min-width:0;background:#1d2220;overflow:auto;padding:20px;display:flex;align-items:flex-start;justify-content:center;position:relative}
#dm-canvas-wrap{transform-origin:top center;flex-shrink:0;box-shadow:0 6px 30px rgba(0,0,0,.5);position:relative}
#dm-canvas{display:block}
#dm-grid-canvas{position:absolute;top:0;left:0;pointer-events:none;display:none}

/* ── Right panel ── */
.dm-panel{width:250px;flex-shrink:0;background:#1a1f1d;border-inline-start:1px solid #2a3330;overflow-y:auto;font-size:12px}
.dm-panel::-webkit-scrollbar{width:3px}
.dm-panel::-webkit-scrollbar-thumb{background:#2a3330}
.dm-pp-sec{padding:9px 11px;border-bottom:1px solid rgba(34,41,38,.6)}
.dm-pp-lbl{font-size:10px;font-weight:700;color:#4a6055;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;display:flex;align-items:center;justify-content:space-between}
.dm-pp-row{display:flex;align-items:center;gap:5px;margin-bottom:5px}
.dm-pp-row label{font-size:10px;color:#7a9282;flex-shrink:0;min-width:36px}
.dm-pp-inp{flex:1;background:#202825;border:1px solid #2a3330;border-radius:5px;color:#e2ede6;font-family:'Cairo',sans-serif;font-size:11px;padding:3px 7px;outline:none}
.dm-pp-inp:focus{border-color:#10b46a}
input[type=color].dm-pp-clr{width:30px;height:26px;padding:2px;border-radius:4px;cursor:pointer;background:#202825;border:1px solid #2a3330;flex-shrink:0}
input[type=range].dm-pp-range{width:100%;accent-color:#10b46a;flex:1}
.dm-pp-num{width:50px;background:#202825;border:1px solid #2a3330;border-radius:5px;color:#e2ede6;font-family:'Cairo',sans-serif;font-size:10px;padding:3px 5px;outline:none;text-align:center}
.dm-pp-tabs{display:flex;gap:2px;margin-bottom:6px}
.dm-pp-tab{flex:1;padding:4px 3px;border-radius:5px;background:#202825;border:1px solid #2a3330;color:#7a9282;font-size:10px;cursor:pointer;text-align:center;transition:all .12s}
.dm-pp-tab.on{background:rgba(16,180,106,.15);border-color:#10b46a;color:#10b46a}
.dm-pp-btn{display:flex;align-items:center;justify-content:center;gap:4px;width:100%;padding:5px 8px;border-radius:6px;background:#202825;border:1px solid #2a3330;color:#7a9282;font-family:'Cairo',sans-serif;font-size:10px;cursor:pointer;transition:all .12s;margin-top:3px}
.dm-pp-btn:hover{border-color:#10b46a;color:#e2ede6}
.dm-pp-btn.dng{border-color:#e05c5c;color:#e05c5c}
.dm-pp-btn.dng:hover{background:rgba(224,92,92,.12)}
.dm-pp-align{flex:1;padding:5px;border-radius:5px;background:#202825;border:1px solid #2a3330;color:#7a9282;cursor:pointer;text-align:center;font-size:11px;transition:all .12s}
.dm-pp-align:hover{border-color:#10b46a;color:#10b46a}
.dm-bold-row{display:flex;gap:2px;margin-bottom:5px}
.dm-bold{flex:1;padding:4px;border-radius:5px;background:#202825;border:1px solid #2a3330;color:#7a9282;font-size:12px;cursor:pointer;text-align:center;transition:all .12s}
.dm-bold.on,.dm-bold:hover{border-color:#10b46a;color:#10b46a}
.dm-dash-row{display:flex;gap:3px;margin-bottom:5px}
.dm-dash{flex:1;padding:4px 2px;border-radius:5px;background:#202825;border:1px solid #2a3330;color:#7a9282;font-size:9px;cursor:pointer;text-align:center;transition:all .12s}
.dm-dash.on,.dm-dash:hover{border-color:#10b46a;color:#10b46a}
.dm-pp-empty{padding:24px 11px;text-align:center;color:#4a6055}
.dm-pp-empty i{font-size:24px;display:block;margin-bottom:7px;opacity:.5}
.dm-pp-empty p{font-size:10px}

/* Footer */
.dm-footer{background:#0e1210;border-top:1px solid #2a3330;padding:10px 14px;display:flex;align-items:center;gap:10px;flex-shrink:0}
.dm-hint{font-size:11px;color:#7a9282;flex:1}
.dm-btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:9px;font-family:'Cairo',sans-serif;font-size:13px;font-weight:700;cursor:pointer;border:none;transition:all .15s}
.dm-btn.secondary{background:#202825;color:#e2ede6;border:1px solid #2a3330}
.dm-btn.secondary:hover{background:#2a3330}
.dm-btn.primary{background:#10b46a;color:#fff}
.dm-btn.primary:hover{background:#0c9456}
.dm-btn.primary:disabled{opacity:.6;cursor:not-allowed}

/* Cart design thumbnail */
.cart-design-thumb{
  width:36px;height:36px;border-radius:6px;object-fit:cover;
  border:1.5px solid var(--red);margin-inline-start:8px;cursor:pointer;
  flex-shrink:0
}
.design-badge{
  display:inline-flex;align-items:center;gap:4px;background:rgba(16,180,106,.12);
  border:1px solid var(--red);color:var(--red);padding:2px 8px;border-radius:20px;
  font-size:11px;font-weight:700;margin-top:3px
}

/* TOASTS */
.toasts{position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:1000;display:flex;flex-direction:column;gap:8px;pointer-events:none}
.toast{background:var(--navy);color:#fff;padding:10px 22px;border-radius:10px;font-size:14px;font-weight:500;display:flex;align-items:center;gap:8px;box-shadow:var(--sh-md);animation:slide-in .3s ease}
.toast.err{background:#e74c3c}
@keyframes slide-in{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:none}}

@media(max-width:900px){
  .prod-layout{grid-template-columns:1fr}
  .prod-gallery{position:static}
  .prod-grid{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:600px){
  .prod-grid{grid-template-columns:repeat(2,1fr)}
  .header-body{flex-wrap:wrap;gap:10px}
  .search-wrap{order:3;max-width:100%}
  .cart-drawer{width:100%;right:-100%}
  .prod-name{font-size:20px}
}
</style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar" id="topbar">
  <div class="wrap">
    <div class="topbar-inner">
      <div class="topbar-left" id="tb-contact"></div>
      <div class="topbar-right">
        <a href="/storefront/orders/track"><i class="fa fa-location-dot"></i> تتبع الطلب</a>
      </div>
    </div>
  </div>
</div>

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
        <a class="wa-btn" id="wa-btn" href="#" style="display:none">
          <i class="fab fa-whatsapp"></i> واتساب
        </a>
        <button class="icon-btn" onclick="openCart()">
          <i class="fa fa-bag-shopping"></i>
          <span class="badge off" id="cart-badge">0</span>
        </button>
      </div>
    </div>
  </div>
</header>

<!-- BREADCRUMB -->
<div class="bc">
  <div class="wrap">
    <div class="bc-inner">
      <a href="/"><i class="fa fa-house"></i> الرئيسية</a>
      <i class="fa fa-chevron-left"></i>
      <a href="/storefront/products">المنتجات</a>
      <i class="fa fa-chevron-left"></i>
      <span id="bc-name">تفاصيل المنتج</span>
    </div>
  </div>
</div>

<!-- PRODUCT PAGE -->
<div class="prod-page" id="prod-page">
  <div class="wrap">
    <!-- SKELETON -->
    <div id="prod-skel" style="display:grid;grid-template-columns:1fr 1fr;gap:48px">
      <div class="skel" style="aspect-ratio:1;border-radius:22px"></div>
      <div style="display:flex;flex-direction:column;gap:16px;padding-top:10px">
        <div class="skel" style="height:32px;border-radius:8px;width:60%"></div>
        <div class="skel" style="height:50px;border-radius:8px"></div>
        <div class="skel" style="height:24px;border-radius:8px;width:40%"></div>
        <div class="skel" style="height:80px;border-radius:8px"></div>
        <div class="skel" style="height:52px;border-radius:12px"></div>
      </div>
    </div>

    <!-- REAL CONTENT -->
    <div class="prod-layout" id="prod-content" style="display:none">
      <!-- GALLERY -->
      <div class="prod-gallery">
        <div class="prod-main-img" id="main-img">
          <i class="fa fa-box-open prod-main-icon" id="main-icon"></i>
        </div>
        <div class="prod-thumbs" id="thumbs"></div>
      </div>

      <!-- INFO -->
      <div class="prod-info">
        <div class="prod-badge-row" id="badge-row"></div>
        <h1 class="prod-name" id="prod-name">جاري التحميل...</h1>
        <div class="prod-stars" id="prod-stars"></div>
        <div class="prod-price-row">
          <span class="price-now" id="price-now">—</span>
          <span class="price-was" id="price-was" style="display:none"></span>
        </div>
        <p class="prod-desc" id="prod-desc"></p>
        <div class="divider"></div>
        <!-- QTY -->
        <div class="qty-row">
          <span class="qty-label">الكمية:</span>
          <div class="qty-ctrl">
            <button class="qty-btn" onclick="changeQty(-1)">−</button>
            <input type="number" class="qty-val" id="qty" value="1" min="1" max="99">
            <button class="qty-btn" onclick="changeQty(1)">+</button>
          </div>
        </div>
        <div style="display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap">
          <button class="btn-design" id="btn-design" onclick="openDesignEditor()">
            <i class="fa fa-palette"></i> صمم منتجك
          </button>
          <button class="btn-add-cart" id="btn-cart" onclick="addProdToCart()">
            <i class="fa fa-cart-plus"></i> أضف للسلة
          </button>
          <button class="btn-wish" id="btn-wish" onclick="toggleWishThis()" title="أضف للمفضلة">
            <i class="fa fa-heart"></i>
          </button>
        </div>
        <div class="divider"></div>
        <!-- INFO TABLE -->
        <table class="info-table" id="info-table" style="display:none">
          <tbody id="info-tbody"></tbody>
        </table>
      </div>
    </div>

    <!-- NOT FOUND -->
    <div id="prod-notfound" style="display:none;text-align:center;padding:80px 0;color:var(--text-3)">
      <i class="fa fa-box-open" style="font-size:56px;margin-bottom:18px"></i>
      <p style="font-size:18px;font-weight:600">المنتج غير موجود</p>
      <a href="/storefront/products" class="btn-red" style="margin-top:20px">← تصفح المنتجات</a>
    </div>
  </div>
</div>

<!-- RELATED PRODUCTS -->
<div class="related-sec" id="related-sec" style="display:none">
  <div class="wrap">
    <h2 class="sec-title">منتجات مشابهة</h2>
    <div class="prod-grid" id="related-grid"></div>
  </div>
</div>

<!-- CART -->
<div class="cart-veil" id="cart-veil" onclick="closeCart()"></div>
<div class="cart-drawer" id="cart-drawer">
  <div class="cart-hd">
    <span>🛒 سلة التسوق</span>
    <button class="cart-x" onclick="closeCart()"><i class="fa fa-xmark"></i></button>
  </div>
  <div class="cart-bd" id="cart-bd"></div>
  <div class="cart-ft" id="cart-ft" style="display:none">
    <div class="cart-total-row"><span>المجموع:</span><span id="cart-tot">0 ₪</span></div>
    <a href="/storefront/checkout" class="btn-red" style="width:100%;justify-content:center;display:flex">
      <i class="fa fa-credit-card"></i> إتمام الشراء
    </a>
  </div>
</div>

<!-- ════════════════════════════════════
     DESIGN EDITOR MODAL
════════════════════════════════════ -->
<div class="design-veil" id="design-veil">
<div class="design-modal">

  <!-- ── TOPBAR ── -->
  <div class="dm-header">
    <i class="fa fa-palette" style="color:#10b46a;font-size:15px;flex-shrink:0"></i>
    <span class="dm-title">محرر التصميم</span>
    <span id="dm-prod-label" class="dm-prod-lbl"></span>
    <div class="dm-tb-sep"></div>

    <!-- tools -->
    <button class="dm-tb on" id="dm-t-sel" onclick="dmTool('select')"><i class="fa fa-arrow-pointer"></i></button>
    <button class="dm-tb" id="dm-t-draw" onclick="dmTool('draw')"><i class="fa fa-pen"></i></button>
    <div id="dm-draw-opts">
      <label>لون</label><input type="color" id="dm-brush-clr" value="#10b46a" oninput="dmUpdateBrush()">
      <label>سُمك</label><input type="range" min="1" max="40" value="4" id="dm-brush-w" oninput="dmUpdateBrush()">
    </div>
    <div class="dm-tb-sep"></div>

    <!-- shapes -->
    <div class="dm-shapes-dd">
      <button class="dm-tb" onclick="dmToggleShapes()"><i class="fa fa-shapes"></i><span style="font-size:9px">▾</span></button>
      <div class="dm-shapes-menu" id="dm-shapes-menu">
        <button onclick="dmAddRect();dmCloseShapes()"><i class="fa fa-square"></i>مستطيل</button>
        <button onclick="dmAddRounded();dmCloseShapes()"><i class="fa fa-square"></i>مدوّر</button>
        <button onclick="dmAddCircle();dmCloseShapes()"><i class="fa fa-circle"></i>دائرة</button>
        <button onclick="dmAddTriangle();dmCloseShapes()"><i class="fa fa-play fa-rotate-270"></i>مثلث</button>
        <button onclick="dmAddDiamond();dmCloseShapes()"><i class="fa fa-diamond"></i>معيّن</button>
        <button onclick="dmAddStar();dmCloseShapes()"><i class="fa fa-star"></i>نجمة</button>
        <button onclick="dmAddLine();dmCloseShapes()"><i class="fa fa-minus"></i>خط</button>
        <button onclick="dmAddArrow();dmCloseShapes()"><i class="fa fa-arrow-left"></i>سهم</button>
        <button onclick="dmAddFrame();dmCloseShapes()"><i class="fa fa-border-all"></i>إطار</button>
      </div>
    </div>

    <!-- image -->
    <button class="dm-tb" onclick="document.getElementById('dm-img-in').click()"><i class="fa fa-image"></i></button>
    <input type="file" id="dm-img-in" accept="image/*" style="display:none" onchange="dmUploadImg(event)">

    <!-- bg -->
    <label class="dm-tb" style="cursor:pointer">
      <i class="fa fa-fill-drip"></i>
      <input type="color" id="dm-bg-picker" value="#ffffff" oninput="dmSetBg(this.value)" style="width:0;height:0;opacity:0;position:absolute">
    </label>

    <!-- grid -->
    <button class="dm-tb" id="dm-grid-btn" onclick="dmToggleGrid()"><i class="fa fa-grid-4"></i></button>

    <div class="dm-tb-sep"></div>

    <!-- size -->
    <select class="dm-sz" id="dm-size-sel" onchange="dmApplySizeFromSel(this.value)">
      <option value="800x800">مربع 800</option>
      <option value="1200x630">بانر ويب</option>
      <option value="1080x1920">ستوري</option>
      <option value="1080x1080">انستقرام</option>
      <option value="400x400">ملصق</option>
    </select>

    <!-- zoom -->
    <button class="dm-tb" onclick="dmZoomFit()"><i class="fa fa-expand"></i></button>
    <button class="dm-tb" onclick="dmZoomStep(-.1)"><i class="fa fa-minus" style="font-size:9px"></i></button>
    <span class="dm-zoom-lbl" id="dm-zoom-lbl">100%</span>
    <button class="dm-tb" onclick="dmZoomStep(.1)"><i class="fa fa-plus" style="font-size:9px"></i></button>

    <div class="dm-tb-sep"></div>

    <!-- undo/redo -->
    <button class="dm-tb" onclick="dmUndo()"><i class="fa fa-rotate-left"></i></button>
    <button class="dm-tb" onclick="dmRedo()"><i class="fa fa-rotate-right"></i></button>

    <!-- align bar -->
    <div id="dm-align-bar">
      <div class="dm-tb-sep"></div>
      <button class="dm-tb" onclick="dmAlign('left')"><i class="fa fa-align-left"></i></button>
      <button class="dm-tb" onclick="dmAlign('hcenter')"><i class="fa fa-align-center"></i></button>
      <button class="dm-tb" onclick="dmAlign('right')"><i class="fa fa-align-right"></i></button>
      <button class="dm-tb" onclick="dmAlign('top')"><i class="fa fa-arrow-up"></i></button>
      <button class="dm-tb" onclick="dmAlign('vcenter')"><i class="fa fa-arrows-up-down"></i></button>
      <button class="dm-tb" onclick="dmAlign('bottom')"><i class="fa fa-arrow-down"></i></button>
      <button class="dm-tb" onclick="dmClone()"><i class="fa fa-copy"></i></button>
      <button class="dm-tb" style="color:#e05c5c" onclick="dmDelete()"><i class="fa fa-trash"></i></button>
    </div>

    <button class="dm-close" onclick="closeDesignEditor()"><i class="fa fa-xmark"></i></button>
  </div>

  <!-- ── BODY ── -->
  <div class="dm-body">

    <!-- LEFT SIDEBAR -->
    <div class="dm-sidebar">
      <div class="dm-sb-tabs">
        <div class="dm-sb-tab on"  onclick="dmTab('text')"      title="نص">     <i class="fa fa-font"></i>نص</div>
        <div class="dm-sb-tab"     onclick="dmTab('shapes')"    title="أشكال">  <i class="fa fa-shapes"></i>أشكال</div>
        <div class="dm-sb-tab"     onclick="dmTab('image')"     title="صور">    <i class="fa fa-image"></i>صور</div>
        <div class="dm-sb-tab"     onclick="dmTab('bg')"        title="خلفية">  <i class="fa fa-fill-drip"></i>خلفية</div>
        <div class="dm-sb-tab"     onclick="dmTab('templates')" title="قوالب">  <i class="fa fa-palette"></i>قوالب</div>
      </div>
      <div class="dm-sb-panes">

        <!-- TEXT -->
        <div class="dm-sb-pane on" id="dm-pane-text">
          <div class="dm-ssec">إضافة نص</div>
          <div class="dm-s-row">
            <div class="dm-sbtn" onclick="dmMkText('عنوان رئيسي',{fontSize:36,fontWeight:'bold'})"><i class="fa fa-heading"></i>عنوان</div>
            <div class="dm-sbtn" onclick="dmMkText('نص فرعي',{fontSize:22})"><i class="fa fa-font"></i>فرعي</div>
          </div>
          <div class="dm-sbtn" style="width:100%;margin-bottom:7px" onclick="dmMkText('نص عادي',{fontSize:16})"><i class="fa fa-align-right"></i>نص عادي</div>
          <div class="dm-ssec">نصوص مصمّمة</div>
          <div class="dm-styled-grid">
            <div class="dm-styled" onclick="dmAddStyled('sale')">🏷️ خصم</div>
            <div class="dm-styled" onclick="dmAddStyled('badge')">⭐ شارة</div>
            <div class="dm-styled" onclick="dmAddStyled('price')">💰 سعر</div>
            <div class="dm-styled" onclick="dmAddStyled('title')">🎨 عنوان</div>
            <div class="dm-styled" onclick="dmAddStyled('quote')">💬 اقتباس</div>
            <div class="dm-styled" onclick="dmAddStyled('label')">📌 ملصق</div>
          </div>
          <div class="dm-ssec">الخط</div>
          <div class="dm-fonts" id="dm-font-chips"></div>
        </div>

        <!-- SHAPES -->
        <div class="dm-sb-pane" id="dm-pane-shapes">
          <div class="dm-ssec">أشكال</div>
          <div class="dm-shapes-grid">
            <div class="dm-shape" onclick="dmAddRect()"><i class="fa fa-square"></i>مستطيل</div>
            <div class="dm-shape" onclick="dmAddRounded()"><i class="fa fa-square"></i>مدوّر</div>
            <div class="dm-shape" onclick="dmAddCircle()"><i class="fa fa-circle"></i>دائرة</div>
            <div class="dm-shape" onclick="dmAddTriangle()"><i class="fa fa-play fa-rotate-270"></i>مثلث</div>
            <div class="dm-shape" onclick="dmAddDiamond()"><i class="fa fa-diamond"></i>معيّن</div>
            <div class="dm-shape" onclick="dmAddStar()"><i class="fa fa-star"></i>نجمة</div>
            <div class="dm-shape" onclick="dmAddLine()"><i class="fa fa-minus"></i>خط</div>
            <div class="dm-shape" onclick="dmAddArrow()"><i class="fa fa-arrow-left"></i>سهم</div>
            <div class="dm-shape" onclick="dmAddHeart()"><i class="fa fa-heart"></i>قلب</div>
          </div>
          <div class="dm-ssec">إطارات</div>
          <div class="dm-s-row">
            <div class="dm-sbtn" onclick="dmAddFrame()"><i class="fa fa-border-all"></i>بسيط</div>
            <div class="dm-sbtn" onclick="dmAddFrame('double')"><i class="fa fa-border-outer"></i>مزدوج</div>
          </div>
        </div>

        <!-- IMAGE -->
        <div class="dm-sb-pane" id="dm-pane-image">
          <div class="dm-ssec">رفع صورة</div>
          <div class="dm-upload" onclick="document.getElementById('dm-img-in').click()">
            <i class="fa fa-cloud-arrow-up"></i>
            <p>اضغط لرفع صورة</p>
          </div>
          <div class="dm-ssec">من رابط</div>
          <div style="display:flex;gap:4px;margin-bottom:6px">
            <input id="dm-url-inp" class="dm-pp-inp" placeholder="https://..." style="flex:1;font-size:10px">
            <button class="dm-sbtn" style="flex:0 0 auto;padding:5px 8px" onclick="dmAddImageUrl()"><i class="fa fa-plus"></i></button>
          </div>
        </div>

        <!-- BG -->
        <div class="dm-sb-pane" id="dm-pane-bg">
          <div class="dm-ssec">لون مخصص</div>
          <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px">
            <label style="font-size:10px;color:#7a9282">اختر</label>
            <input type="color" class="dm-pp-clr" id="dm-bg-picker2" value="#ffffff" oninput="dmSetBg(this.value);document.getElementById('dm-bg-picker').value=this.value">
          </div>
          <div class="dm-ssec">ألوان جاهزة</div>
          <div class="dm-swatches">
            @foreach(['#ffffff','#000000','#1a1a2e','#16213e','#e74c3c','#e67e22','#f39c12','#27ae60','#2980b9','#8e44ad','#f5f0eb','#2c3e50','#ecf0f1','#bdc3c7','#d35400','#34495e'] as $c)
            <div class="dm-swatch" style="background:{{$c}}" onclick="dmSetBg('{{$c}}');document.getElementById('dm-bg-picker').value='{{$c}}';document.getElementById('dm-bg-picker2').value='{{$c}}'"></div>
            @endforeach
          </div>
          <div class="dm-ssec">تدرجات</div>
          <div class="dm-gradients">
            <div class="dm-grad" style="background:linear-gradient(135deg,#667eea,#764ba2)" onclick="dmSetBgGrad('#667eea','#764ba2')"></div>
            <div class="dm-grad" style="background:linear-gradient(135deg,#f093fb,#f5576c)" onclick="dmSetBgGrad('#f093fb','#f5576c')"></div>
            <div class="dm-grad" style="background:linear-gradient(135deg,#4facfe,#00f2fe)" onclick="dmSetBgGrad('#4facfe','#00f2fe')"></div>
            <div class="dm-grad" style="background:linear-gradient(135deg,#43e97b,#38f9d7)" onclick="dmSetBgGrad('#43e97b','#38f9d7')"></div>
            <div class="dm-grad" style="background:linear-gradient(135deg,#fa709a,#fee140)" onclick="dmSetBgGrad('#fa709a','#fee140')"></div>
            <div class="dm-grad" style="background:linear-gradient(135deg,#a18cd1,#fbc2eb)" onclick="dmSetBgGrad('#a18cd1','#fbc2eb')"></div>
            <div class="dm-grad" style="background:linear-gradient(135deg,#ffd89b,#19547b)" onclick="dmSetBgGrad('#ffd89b','#19547b')"></div>
            <div class="dm-grad" style="background:linear-gradient(135deg,#11998e,#38ef7d)" onclick="dmSetBgGrad('#11998e','#38ef7d')"></div>
          </div>
        </div>

        <!-- TEMPLATES -->
        <div class="dm-sb-pane" id="dm-pane-templates">
          <div class="dm-ssec">قوالب جاهزة</div>
          <div id="dm-tmpl-grid">
            <div style="text-align:center;padding:16px 0;color:#7a9282;font-size:10px">
              <i class="fa fa-spinner fa-spin" style="font-size:18px;display:block;margin-bottom:5px"></i>جاري التحميل...
            </div>
          </div>
        </div>

      </div><!-- .dm-sb-panes -->
    </div><!-- .dm-sidebar -->

    <!-- CANVAS -->
    <div class="dm-canvas-area" id="dm-canvas-area">
      <div id="dm-canvas-wrap">
        <canvas id="dm-canvas"></canvas>
        <canvas id="dm-grid-canvas"></canvas>
      </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="dm-panel" id="dm-panel">

      <div id="dm-pp-empty" class="dm-pp-empty">
        <i class="fa fa-arrow-pointer"></i>
        <p>اختر عنصراً للتعديل</p>
      </div>

      <!-- pos -->
      <div id="dm-pp-pos" class="dm-pp-sec" style="display:none">
        <div class="dm-pp-lbl">موضع وحجم</div>
        <div class="dm-pp-row">
          <label>X</label><input type="number" class="dm-pp-num" id="dp-x" oninput="dmSetXY()">
          <label>Y</label><input type="number" class="dm-pp-num" id="dp-y" oninput="dmSetXY()">
        </div>
        <div class="dm-pp-row">
          <label>عرض</label><input type="number" class="dm-pp-num" id="dp-w" oninput="dmSetWH()">
          <label>ارتفاع</label><input type="number" class="dm-pp-num" id="dp-h" oninput="dmSetWH()">
        </div>
        <div class="dm-pp-row">
          <label>دوران</label><input type="number" class="dm-pp-inp" id="dp-rot" oninput="dmSet('angle',+this.value)">
        </div>
        <div class="dm-pp-row">
          <label>شفافية</label><input type="range" class="dm-pp-range" min="0" max="1" step=".05" id="dp-op" oninput="dmSet('opacity',+this.value)">
        </div>
      </div>

      <!-- fill -->
      <div id="dm-pp-fill" class="dm-pp-sec" style="display:none">
        <div class="dm-pp-lbl">التعبئة</div>
        <div class="dm-pp-tabs">
          <div class="dm-pp-tab on" id="dp-fill-solid" onclick="dmSwitchFill('solid')">صلب</div>
          <div class="dm-pp-tab"    id="dp-fill-grad"  onclick="dmSwitchFill('grad')">تدرج</div>
          <div class="dm-pp-tab"    id="dp-fill-none"  onclick="dmSwitchFill('none')">شفاف</div>
        </div>
        <div id="dp-fill-solid-pane">
          <div class="dm-pp-row"><label>لون</label><input type="color" class="dm-pp-clr" id="dp-fill-clr" oninput="dmSet('fill',this.value)"></div>
        </div>
        <div id="dp-fill-grad-pane" style="display:none">
          <div class="dm-pp-row">
            <label>لون 1</label><input type="color" class="dm-pp-clr" id="dp-gc1" value="#10b46a">
            <label>لون 2</label><input type="color" class="dm-pp-clr" id="dp-gc2" value="#a78bfa">
          </div>
          <div class="dm-pp-row"><label>زاوية</label><input type="range" class="dm-pp-range" min="0" max="360" value="135" id="dp-gang"></div>
          <button class="dm-pp-btn" onclick="dmApplyGrad()"><i class="fa fa-paint-roller"></i> تطبيق</button>
        </div>
        <div class="dm-pp-row" style="margin-top:7px"><label>حدود</label>
          <input type="color" class="dm-pp-clr" id="dp-stroke-clr" oninput="dmSet('stroke',this.value)">
          <input type="number" class="dm-pp-num" id="dp-stroke-w" min="0" max="50" oninput="dmSet('strokeWidth',+this.value)">
        </div>
        <div class="dm-dash-row">
          <div class="dm-dash on" id="dp-dash-solid"  onclick="dmApplyDash('solid')">─ صلب</div>
          <div class="dm-dash"    id="dp-dash-dashed" onclick="dmApplyDash('dashed')">-- مقطّع</div>
          <div class="dm-dash"    id="dp-dash-dotted" onclick="dmApplyDash('dotted')">·· نقاط</div>
        </div>
        <div class="dm-pp-row"><label>rx</label><input type="number" class="dm-pp-num" id="dp-rx" min="0" max="200" oninput="dmSet('rx',+this.value);dmSet('ry',+this.value)"></div>
        <div class="dm-pp-row"><label>مزج</label>
          <select class="dm-pp-inp" id="dp-blend" onchange="dmSet('globalCompositeOperation',this.value)">
            <option value="source-over">عادي</option>
            <option value="multiply">ضرب</option>
            <option value="screen">شاشة</option>
            <option value="overlay">تراكب</option>
            <option value="darken">تعتيم</option>
            <option value="lighten">تفتيح</option>
            <option value="difference">فرق</option>
          </select>
        </div>
      </div>

      <!-- text -->
      <div id="dm-pp-text" class="dm-pp-sec" style="display:none">
        <div class="dm-pp-lbl">النص</div>
        <div class="dm-pp-row">
          <label>حجم</label><input type="number" class="dm-pp-num" id="dp-fsize" min="6" max="400" oninput="dmSet('fontSize',+this.value)">
          <label>لون</label><input type="color" class="dm-pp-clr" id="dp-fclr" oninput="dmSet('fill',this.value)">
        </div>
        <div class="dm-bold-row">
          <div class="dm-bold" id="dp-bold"   onclick="dmToggleBold()"><b>B</b></div>
          <div class="dm-bold" id="dp-italic" onclick="dmToggleItalic()"><i>I</i></div>
          <div class="dm-bold" id="dp-under"  onclick="dmToggleUnder()"><u>U</u></div>
        </div>
        <div class="dm-pp-row">
          <div style="display:flex;gap:2px;flex:1">
            <button class="dm-pp-align" onclick="dmSet('textAlign','right')"><i class="fa fa-align-right"></i></button>
            <button class="dm-pp-align" onclick="dmSet('textAlign','center')"><i class="fa fa-align-center"></i></button>
            <button class="dm-pp-align" onclick="dmSet('textAlign','left')"><i class="fa fa-align-left"></i></button>
          </div>
        </div>
        <div class="dm-pp-row">
          <label>اتجاه</label>
          <button class="dm-pp-align" onclick="dmSetDir('rtl')"><i class="fa fa-align-right"></i> ع</button>
          <button class="dm-pp-align" onclick="dmSetDir('ltr')"><i class="fa fa-align-left"></i> en</button>
        </div>
        <div class="dm-pp-row">
          <label>تباعد</label><input type="number" class="dm-pp-num" id="dp-csp" step="1" oninput="dmSet('charSpacing',+this.value)">
          <label>أسطر</label><input type="number" class="dm-pp-num" id="dp-lh"  step=".1" min=".5" oninput="dmSet('lineHeight',+this.value)">
        </div>
      </div>

      <!-- image filters -->
      <div id="dm-pp-img" class="dm-pp-sec" style="display:none">
        <div class="dm-pp-lbl">فلاتر الصورة
          <button class="dm-pp-btn" style="width:auto;padding:2px 6px;margin:0;font-size:9px" onclick="dmResetFilters()">إعادة</button>
        </div>
        <div class="dm-pp-row"><label>إضاءة</label><input type="range" class="dm-pp-range" min="-1" max="1" step=".05" value="0" id="dp-bright" oninput="dmApplyFilters()"></div>
        <div class="dm-pp-row"><label>تباين</label><input type="range" class="dm-pp-range" min="-1" max="1" step=".05" value="0" id="dp-cont"   oninput="dmApplyFilters()"></div>
        <div class="dm-pp-row"><label>تشبّع</label><input type="range" class="dm-pp-range" min="-1" max="1" step=".05" value="0" id="dp-sat"    oninput="dmApplyFilters()"></div>
        <div class="dm-pp-row"><label>ضبابية</label><input type="range" class="dm-pp-range" min="0"  max="1" step=".02" value="0" id="dp-blur"   oninput="dmApplyFilters()"></div>
        <div class="dm-pp-row"><label>لون</label><input type="range" class="dm-pp-range" min="-2" max="2" step=".1"  value="0" id="dp-hue"    oninput="dmApplyFilters()"></div>
      </div>

      <!-- shadow -->
      <div id="dm-pp-shadow" class="dm-pp-sec" style="display:none">
        <div class="dm-pp-lbl">الظل والتوهج</div>
        <div class="dm-pp-tabs">
          <div class="dm-pp-tab on" id="dp-eff-shadow" onclick="dmSwitchEff('shadow')">ظل</div>
          <div class="dm-pp-tab"    id="dp-eff-glow"   onclick="dmSwitchEff('glow')">توهج</div>
        </div>
        <div id="dp-shadow-pane">
          <div class="dm-pp-row"><label>لون</label><input type="color" class="dm-pp-clr" id="dp-sh-clr" value="#000000"></div>
          <div class="dm-pp-row"><label>إزاحة X</label><input type="range" class="dm-pp-range" min="-30" max="30" value="5" id="dp-sh-x"></div>
          <div class="dm-pp-row"><label>إزاحة Y</label><input type="range" class="dm-pp-range" min="-30" max="30" value="5" id="dp-sh-y"></div>
          <div class="dm-pp-row"><label>ضبابية</label><input type="range" class="dm-pp-range" min="0" max="30" value="8" id="dp-sh-blur"></div>
          <div class="dm-pp-row"><label>شفافية</label><input type="range" class="dm-pp-range" min="0" max="1" step=".05" value=".5" id="dp-sh-op"></div>
          <div style="display:flex;gap:3px;margin-top:4px">
            <button class="dm-pp-btn" onclick="dmApplyShadow()"><i class="fa fa-check"></i> تطبيق</button>
            <button class="dm-pp-btn dng" onclick="dmRemoveShadow()"><i class="fa fa-xmark"></i> إزالة</button>
          </div>
        </div>
        <div id="dp-glow-pane" style="display:none">
          <div class="dm-pp-row"><label>لون</label><input type="color" class="dm-pp-clr" id="dp-gl-clr" value="#10b46a"></div>
          <div class="dm-pp-row"><label>حجم</label><input type="range" class="dm-pp-range" min="0" max="40" value="12" id="dp-gl-size"></div>
          <div class="dm-pp-row"><label>شفافية</label><input type="range" class="dm-pp-range" min="0" max="1" step=".05" value=".8" id="dp-gl-op"></div>
          <div style="display:flex;gap:3px;margin-top:4px">
            <button class="dm-pp-btn" onclick="dmApplyGlow(true)"><i class="fa fa-sparkles"></i> تطبيق</button>
            <button class="dm-pp-btn dng" onclick="dmApplyGlow(false)"><i class="fa fa-xmark"></i> إزالة</button>
          </div>
        </div>
      </div>

      <!-- layer -->
      <div id="dm-pp-layer" class="dm-pp-sec" style="display:none">
        <div class="dm-pp-lbl">الطبقة</div>
        <div style="display:flex;gap:2px">
          <button class="dm-pp-align" onclick="dm.bringToFront(dmC());dm.renderAll()" title="للمقدمة"><i class="fa fa-angle-double-up"></i></button>
          <button class="dm-pp-align" onclick="dm.bringForward(dmC());dm.renderAll()" title="للأمام"><i class="fa fa-angle-up"></i></button>
          <button class="dm-pp-align" onclick="dm.sendBackwards(dmC());dm.renderAll()" title="للخلف"><i class="fa fa-angle-down"></i></button>
          <button class="dm-pp-align" onclick="dm.sendToBack(dmC());dm.renderAll()" title="للخلفية"><i class="fa fa-angle-double-down"></i></button>
        </div>
        <button class="dm-pp-btn dng" onclick="dmDelete()" style="margin-top:5px"><i class="fa fa-trash"></i> حذف العنصر</button>
      </div>

    </div><!-- .dm-panel -->

  </div><!-- .dm-body -->

  <!-- FOOTER -->
  <div class="dm-footer">
    <div class="dm-hint"><i class="fa fa-lightbulb" style="color:#f59e0b"></i> عدّل على القالب ثم اضغط "أضف للسلة مع التصميم"</div>
    <button class="dm-btn secondary" onclick="closeDesignEditor()">إلغاء</button>
    <button class="dm-btn secondary" onclick="dmClearDesign()"><i class="fa fa-eraser"></i> مسح</button>
    <button class="dm-btn primary" id="dm-add-cart-btn" onclick="dmAddToCart()">
      <i class="fa fa-cart-plus"></i> أضف للسلة مع التصميم
    </button>
  </div>

</div><!-- .design-modal -->
</div><!-- .design-veil -->

<!-- Fabric.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>

<div class="toasts" id="toasts"></div>

<script>
const API = window.location.origin + '/api/v1';
const PROD_ID = {{ $productId ?? 0 }};
let CUR = '₪';
let _prod = null;
let _pBase = '';

// CART
let cart = JSON.parse(localStorage.getItem('f_cart')||'[]');
function renderCart() {
  const badge=document.getElementById('cart-badge'), bd=document.getElementById('cart-bd'), ft=document.getElementById('cart-ft'), tot=document.getElementById('cart-tot');
  badge.textContent = cart.reduce((s,i)=>s+i.qty,0);
  badge.classList.toggle('off', cart.length===0);
  if(!cart.length){ bd.innerHTML=`<div class="cart-empty-msg"><i class="fa fa-cart-shopping"></i><p>السلة فارغة</p></div>`; ft.style.display='none'; return; }
  bd.innerHTML = cart.map(i=>`<div class="cart-item"><div class="cart-item-img">${i.img?`<img src="${i.img}" onerror="this.remove()">`:'<i class="fa fa-box" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#aaa"></i>'}</div><div><div class="cart-item-name">${i.name}</div><div class="cart-item-price">${i.qty} × ${parseFloat(i.price).toFixed(2)} ${CUR}</div></div><button class="cart-item-del" onclick="removeFromCart(${i.id})"><i class="fa fa-xmark"></i></button></div>`).join('');
  tot.textContent = cart.reduce((s,i)=>s+i.qty*parseFloat(i.price),0).toFixed(2)+' '+CUR;
  ft.style.display='block';
}
function removeFromCart(id){ cart=cart.filter(i=>i.id!==id); localStorage.setItem('f_cart',JSON.stringify(cart)); renderCart(); }
function openCart(){ document.getElementById('cart-veil').classList.add('on'); document.getElementById('cart-drawer').classList.add('on'); }
function closeCart(){ document.getElementById('cart-veil').classList.remove('on'); document.getElementById('cart-drawer').classList.remove('on'); }
function toast(msg,type='ok'){ const tc=document.getElementById('toasts'),t=document.createElement('div'); t.className=`toast ${type}`; t.textContent=msg; tc.appendChild(t); setTimeout(()=>t.remove(),3000); }
function doSearch(){ const q=document.getElementById('search-q').value.trim(); if(q) location.href=`/storefront/products?q=${encodeURIComponent(q)}`; }
document.getElementById('search-q').addEventListener('keydown',e=>e.key==='Enter'&&doSearch());

// QTY
function changeQty(d){ const el=document.getElementById('qty'); let v=parseInt(el.value)+d; el.value=Math.max(1,Math.min(99,v)); }

// WISHLIST
function toggleWishThis(){
  if(!_prod) return;
  let wl=JSON.parse(localStorage.getItem('f_wl')||'[]');
  const i=wl.indexOf(_prod.id);
  if(i>-1){ wl.splice(i,1); toast('أُزيل من المفضلة','err'); document.getElementById('btn-wish').classList.remove('active'); }
  else { wl.push(_prod.id); toast('أُضيف للمفضلة ♥'); document.getElementById('btn-wish').classList.add('active'); }
  localStorage.setItem('f_wl',JSON.stringify(wl));
}

// ADD TO CART
function addProdToCart(){
  if(!_prod) return;
  const qty = parseInt(document.getElementById('qty').value)||1;
  const price = parseFloat(_prod.price||_prod.unit_price||0);
  const disc  = parseFloat(_prod.discount||0);
  const finalPrice = disc>0 ? price-price*disc/100 : price;
  const imgSrc = (_prod.image_fullpath&&Array.isArray(_prod.image_fullpath)&&_prod.image_fullpath[0]) ? _prod.image_fullpath[0] : '';
  const existing = cart.find(i=>i.id===_prod.id);
  if(existing){ existing.qty+=qty; } else { cart.push({id:_prod.id,name:_prod.name,price:finalPrice,qty,img:imgSrc}); }
  localStorage.setItem('f_cart',JSON.stringify(cart));
  renderCart();
  toast(`أُضيف للسلة: ${_prod.name}`);
  openCart();
}

// PROD STYLE
const prodIconMap = [
  [['درع','دروع','كأس','كريستال','نحاس','ألومنيوم','خشب','زجاج','لوح'],['fa-trophy','linear-gradient(145deg,#134e2a,#1e7a43)','#10b46a']],
  [['لافت','شادر','رول','إكس','بانر','ستاند'],['fa-rectangle-ad','linear-gradient(145deg,#1e3a5f,#2563eb)','#60a5fa']],
  [['بطاقة','كارت','هوية','تعريف'],['fa-id-card','linear-gradient(145deg,#4c1d95,#7c3aed)','#a78bfa']],
  [['كيس','حقيبة','ظرف'],['fa-bag-shopping','linear-gradient(145deg,#7c2d12,#ea580c)','#fb923c']],
  [['كوب','مج','ماگ'],['fa-mug-hot','linear-gradient(145deg,#1e3a5f,#0ea5e9)','#38bdf8']],
  [['قميص','تيشرت','ملابس'],['fa-shirt','linear-gradient(145deg,#064e3b,#10b981)','#34d399']],
  [['قلم','طقم','أقلام'],['fa-pen','linear-gradient(145deg,#1e1b4b,#4338ca)','#818cf8']],
  [['هدية','توزيعة','مفاجأة'],['fa-gift','linear-gradient(145deg,#831843,#db2777)','#f472b6']],
];
function getProdStyle(name=''){
  for(const [kws,[icon,grad,accent]] of prodIconMap)
    for(const kw of kws) if(name.includes(kw)) return {icon,grad,accent};
  return {icon:'fa-print',grad:'linear-gradient(145deg,#134e2a,#1e7a43)',accent:'#10b46a'};
}

// MAIN
async function init(){
  // Config
  let cfg={};
  try{
    cfg = await fetch(`${API}/config`).then(r=>r.json());
    const name=cfg.ecommerce_name||cfg.business_name||'ايليت دعاية';
    if(cfg.currency_symbol) CUR=cfg.currency_symbol;
    document.getElementById('store-name').textContent=name;
    _pBase = cfg.base_urls?.product_image_url||'';
    const logoUrl=cfg.logo_full_url||'';
    if(logoUrl){ document.getElementById('logo-mark').innerHTML=`<img src="${logoUrl}" alt="${name}" style="width:100%;height:100%;object-fit:contain">`; }
    const ph=cfg.ecommerce_phone||cfg.phone||'';
    if(ph){ document.getElementById('tb-contact').innerHTML+=`<a href="tel:${ph}"><i class="fa fa-phone"></i>${ph}</a>`; document.getElementById('topbar').classList.add('visible'); }
    const em=cfg.ecommerce_email||cfg.email||'';
    if(em){ document.getElementById('tb-contact').innerHTML+=`<a href="mailto:${em}"><i class="fa fa-envelope"></i>${em}</a>`; document.getElementById('topbar').classList.add('visible'); }
    if(cfg.whatsapp?.status&&cfg.whatsapp?.number){ const wa=`https://wa.me/${cfg.whatsapp.number}`; const btn=document.getElementById('wa-btn'); btn.href=wa; btn.style.display='flex'; }
  }catch(e){}

  if(!PROD_ID){ showNotFound(); return; }

  try{
    const raw  = await fetch(`${API}/products/details/${PROD_ID}`).then(r=>r.json());
    const data = raw?.product ?? raw;          // API wraps in {product:{...}}
    if(!data||data.errors||!data.id){ showNotFound(); return; }
    _prod = data;
    renderProduct(data);

    // Load related
    if(data.category_ids&&data.category_ids.length){
      const catId = Array.isArray(data.category_ids[0]) ? data.category_ids[0] : (data.category_ids[0]?.id||data.category_ids[0]);
      fetch(`${API}/categories/products/${catId}?limit=8`).then(r=>r.json()).then(rd=>{
        const list = (Array.isArray(rd)?rd:(rd.products||[])).filter(p=>p.id!==PROD_ID).slice(0,4);
        if(list.length){
          document.getElementById('related-sec').style.display='block';
          document.getElementById('related-grid').innerHTML = list.map(p=>relCard(p)).join('');
        }
      }).catch(()=>{});
    }
  }catch(e){
    showNotFound();
  }
}

function showNotFound(){
  document.getElementById('prod-skel').style.display='none';
  document.getElementById('prod-notfound').style.display='block';
}

function renderProduct(p){
  document.getElementById('prod-skel').style.display='none';
  document.getElementById('prod-content').style.display='grid';

  const st = getProdStyle(p.name||'');

  // Page title + breadcrumb
  document.title = `${p.name} — ايليت دعاية`;
  document.getElementById('page-title').textContent = p.name + ' — ايليت دعاية';
  document.getElementById('bc-name').textContent = p.name;

  // Badges
  const discPct = parseFloat(p.discount||0);
  const badgeRow = document.getElementById('badge-row');
  if(p.category_name) badgeRow.innerHTML += `<span class="badge-cat"><i class="fa fa-tag"></i>${p.category_name}</span>`;
  if(discPct>0) badgeRow.innerHTML += `<span class="badge-disc">خصم ${Math.round(discPct)}%</span>`;

  // Name
  document.getElementById('prod-name').textContent = p.name||'';

  // Stars
  const rating = (Array.isArray(p.rating)&&p.rating.length)
    ? p.rating.reduce((s,r)=>s+parseFloat(r.rating||0),0)/p.rating.length
    : 4.2;
  const rNum = Math.round(rating);
  const reviewCount = Array.isArray(p.reviews)?p.reviews.length:(Math.floor(Math.random()*30+5));
  document.getElementById('prod-stars').innerHTML =
    `<span class="stars-gold">${'★'.repeat(Math.min(rNum,5))}${'☆'.repeat(Math.max(0,5-rNum))}</span> ${rating.toFixed(1)} (${reviewCount} تقييم)`;

  // Price
  const price  = parseFloat(p.price||p.unit_price||0);
  const finalP = discPct>0 ? price-price*discPct/100 : price;
  document.getElementById('price-now').textContent = `${finalP.toFixed(2)} ${CUR}`;
  if(discPct>0){
    const pw = document.getElementById('price-was');
    pw.textContent = `${price.toFixed(2)} ${CUR}`;
    pw.style.display='';
  }

  // Description
  const descEl = document.getElementById('prod-desc');
  if(p.description){ descEl.innerHTML = p.description; }
  else { descEl.style.display='none'; }

  // Main image
  const imgs = Array.isArray(p.image_fullpath)?p.image_fullpath:(p.image_fullpath?[p.image_fullpath]:[]);
  const realImgs = imgs.filter(u=>u&&!u.includes('img2.jpg'));
  const mainImgEl = document.getElementById('main-img');
  mainImgEl.style.background = st.grad;
  if(realImgs.length){
    document.getElementById('main-icon').style.display='none';
    mainImgEl.innerHTML = `<img src="${realImgs[0]}" alt="${p.name}" style="width:100%;height:100%;object-fit:cover" onerror="this.remove()">`;
    if(realImgs.length>1){
      const thumbs = document.getElementById('thumbs');
      realImgs.slice(0,5).forEach((url,i)=>{
        const t=document.createElement('div');
        t.className='thumb'+(i===0?' active':'');
        t.innerHTML=`<img src="${url}" alt="" loading="lazy">`;
        t.onclick=()=>{ mainImgEl.innerHTML=`<img src="${url}" alt="${p.name}" style="width:100%;height:100%;object-fit:cover">`; document.querySelectorAll('.thumb').forEach(x=>x.classList.remove('active')); t.classList.add('active'); };
        thumbs.appendChild(t);
      });
    }
  } else {
    document.getElementById('main-icon').className = `fa ${st.icon} prod-main-icon`;
    document.getElementById('main-icon').style.color = st.accent;
    document.getElementById('main-icon').style.opacity = '0.5';
  }

  // Info table
  const rows = [];
  if(p.unit)         rows.push(['الوحدة', p.unit]);
  if(p.capacity)     rows.push(['الحجم/الكمية', p.capacity]);
  if(p.set_menu)     rows.push(['عدد القطع', p.set_menu]);
  if(p.branch_name)  rows.push(['الفرع', p.branch_name]);
  if(rows.length){
    document.getElementById('info-table').style.display='table';
    document.getElementById('info-tbody').innerHTML = rows.map(([k,v])=>`<tr><td>${k}</td><td>${v}</td></tr>`).join('');
  }

  // Wishlist state
  const wl=JSON.parse(localStorage.getItem('f_wl')||'[]');
  if(wl.includes(p.id)) document.getElementById('btn-wish').classList.add('active');
}

function relCard(p){
  const st=getProdStyle(p.name||'');
  const imgs=Array.isArray(p.image_fullpath)?p.image_fullpath:(p.image_fullpath?[p.image_fullpath]:[]);
  const imgSrc=imgs.find(u=>u&&!u.includes('img2.jpg'))||null;
  const price=parseFloat(p.price||p.unit_price||0);
  const disc=parseFloat(p.discount||0);
  const now=disc>0?price-price*disc/100:price;
  const safeName=(p.name||'').replace(/'/g,"\\'");
  return `<div class="prod-card">
    <a href="/storefront/product/${p.id}">
      <div class="prod-img-wrap" style="background:${st.grad}">
        ${imgSrc?`<img src="${imgSrc}" alt="${p.name}" loading="lazy" style="width:100%;height:100%;object-fit:cover" onerror="this.remove()">`:''}
        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none">
          <i class="fa ${st.icon}" style="font-size:48px;color:${st.accent};opacity:.45"></i>
        </div>
        ${disc>0?`<span class="badge-sale">-${Math.round(disc)}%</span>`:''}
        <div class="prod-actions">
          <button class="pa-btn pa-cart" onclick="event.preventDefault();relAdd(${p.id},'${safeName}',${now.toFixed(2)},'${imgSrc||''}')"><i class="fa fa-cart-plus"></i> سلة</button>
        </div>
      </div>
    </a>
    <div class="prod-body">
      <div class="prod-name"><a href="/storefront/product/${p.id}">${p.name||''}</a></div>
      <div class="prod-price">
        <span class="price-sm">${now.toFixed(2)} ${CUR}</span>
        ${disc>0?`<span class="price-sm-was">${price.toFixed(2)} ${CUR}</span>`:''}
      </div>
    </div>
  </div>`;
}

function relAdd(id,name,price,img){
  const ex=cart.find(i=>i.id===id);
  if(ex){ex.qty++;}else{cart.push({id,name,price,qty:1,img});}
  localStorage.setItem('f_cart',JSON.stringify(cart));
  renderCart(); toast('أُضيف للسلة: '+name); openCart();
}

renderCart();
init();

/* ═══════════════════════════════════════════
   DESIGN EDITOR — Advanced Canva-like Modal
═══════════════════════════════════════════ */
let dm = null;
let dmHist = [], dmHistIdx = -1;
let dmZoom = 1, dmGridOn = false;
let _dmTemplates = [], _dmTemplatesLoaded = false;

const DM_FONTS = ['Cairo','Tajawal','Amiri','Arial','Georgia','Impact','Tahoma'];

/* ── Open / Close ── */
function openDesignEditor() {
  if (!_prod) return;
  document.getElementById('design-veil').classList.add('on');
  document.body.style.overflow = 'hidden';
  document.getElementById('dm-prod-label').textContent = _prod.name || '';
  setTimeout(initDmCanvas, 60);
}
function closeDesignEditor() {
  document.getElementById('design-veil').classList.remove('on');
  document.body.style.overflow = '';
}

/* ── Init ── */
function initDmCanvas() {
  if (dm) { dm.dispose(); dm = null; }

  dm = new fabric.Canvas('dm-canvas', {
    backgroundColor: '#ffffff',
    preserveObjectStacking: true,
    selection: true,
  });

  dmApplyCanvasSize(800, 800);
  dmBuildFontChips();
  dmBindEvents();
  dmLoadTemplates();
}

/* ── Canvas size & zoom ── */
function dmApplyCanvasSize(w, h) {
  dm.setWidth(w); dm.setHeight(h);
  const gc = document.getElementById('dm-grid-canvas');
  if (gc) { gc.width = w; gc.height = h; }
  dm.renderAll();
  setTimeout(dmZoomFit, 30);
}
function dmApplySizeFromSel(val) {
  const [w, h] = val.split('x').map(Number);
  dmApplyCanvasSize(w, h);
}
function dmZoomFit() {
  const area  = document.getElementById('dm-canvas-area');
  const wrap  = document.getElementById('dm-canvas-wrap');
  if (!area || !wrap) return;
  const avail  = Math.max(60, area.clientWidth  - 40);
  const availH = Math.max(60, area.clientHeight - 40);
  const scale  = Math.min(1, avail / dm.width, availH / dm.height);
  dmZoom = scale;
  wrap.style.transform       = `scale(${scale})`;
  wrap.style.transformOrigin = 'top center';
  wrap.style.width  = dm.width  + 'px';
  wrap.style.height = dm.height + 'px';
  document.getElementById('dm-zoom-lbl').textContent = Math.round(scale * 100) + '%';
}
function dmZoomStep(d) {
  const wrap = document.getElementById('dm-canvas-wrap');
  dmZoom = Math.max(0.1, Math.min(3, dmZoom + d));
  wrap.style.transform       = `scale(${dmZoom})`;
  wrap.style.transformOrigin = 'top center';
  document.getElementById('dm-zoom-lbl').textContent = Math.round(dmZoom * 100) + '%';
}

/* ── Grid ── */
function dmDrawGrid() {
  const gc = document.getElementById('dm-grid-canvas');
  if (!gc) return;
  const ctx = gc.getContext('2d');
  ctx.clearRect(0, 0, gc.width, gc.height);
  ctx.strokeStyle = 'rgba(255,255,255,.1)';
  ctx.lineWidth = 1;
  for (let x = 0; x <= gc.width;  x += 40) { ctx.beginPath(); ctx.moveTo(x,0); ctx.lineTo(x,gc.height); ctx.stroke(); }
  for (let y = 0; y <= gc.height; y += 40) { ctx.beginPath(); ctx.moveTo(0,y); ctx.lineTo(gc.width,y); ctx.stroke(); }
}
function dmToggleGrid() {
  dmGridOn = !dmGridOn;
  const gc = document.getElementById('dm-grid-canvas');
  gc.style.display = dmGridOn ? 'block' : 'none';
  document.getElementById('dm-grid-btn').classList.toggle('on', dmGridOn);
  if (dmGridOn) dmDrawGrid();
}

/* ── Tools ── */
function dmTool(t) {
  document.getElementById('dm-t-sel').classList.toggle('on',  t === 'select');
  document.getElementById('dm-t-draw').classList.toggle('on', t === 'draw');
  const opts = document.getElementById('dm-draw-opts');
  if (t === 'draw') {
    dm.isDrawingMode = true;
    dm.freeDrawingBrush = new fabric.PencilBrush(dm);
    dmUpdateBrush();
    opts.style.display = 'flex';
  } else {
    dm.isDrawingMode = false;
    opts.style.display = 'none';
  }
}
function dmUpdateBrush() {
  if (!dm.freeDrawingBrush) return;
  dm.freeDrawingBrush.color = document.getElementById('dm-brush-clr').value;
  dm.freeDrawingBrush.width = +document.getElementById('dm-brush-w').value;
}

/* ── Shapes dropdown ── */
function dmToggleShapes() { document.getElementById('dm-shapes-menu').classList.toggle('open'); }
function dmCloseShapes()  { document.getElementById('dm-shapes-menu').classList.remove('open'); }
document.addEventListener('click', e => { if (!e.target.closest('.dm-shapes-dd')) dmCloseShapes(); });

/* ── Sidebar tabs ── */
function dmTab(name) {
  ['text','shapes','image','bg','templates'].forEach(n => {
    document.getElementById('dm-pane-'+n).classList.toggle('on', n===name);
  });
  document.querySelectorAll('.dm-sb-tab').forEach((el,i) => {
    el.classList.toggle('on', ['text','shapes','image','bg','templates'][i]===name);
  });
  if (name==='templates') dmLoadTemplates();
}

/* ── Text ── */
function dmMkText(txt, opts={}) {
  if (!dm) return;
  const o = new fabric.IText(txt, Object.assign({
    right:40, top:40, fontFamily:'Cairo', fontSize:24,
    fontWeight:'normal', fill:'#000000', textAlign:'right',
    direction:'rtl', originX:'right', selectable:true,
  }, opts));
  dm.add(o); dm.setActiveObject(o); o.enterEditing(); dm.renderAll(); dmSaveHist();
}
function dmAddStyled(style) {
  const s = {
    sale:  {txt:'خصم 50%',    fs:38,fw:'900',fill:'#e05c5c'},
    badge: {txt:'⭐ الأفضل',  fs:18,fw:'700',fill:'#fff',  bg:'#f59e0b'},
    price: {txt:'١٩٩ ريال',  fs:34,fw:'800',fill:'#10b46a'},
    title: {txt:'عنوان رئيسي',fs:36,fw:'900',fill:'#1a1a2e'},
    quote: {txt:'"نص اقتباس"',fs:20,fw:'400',fill:'#555'},
    label: {txt:'تسمية',      fs:13,fw:'700',fill:'#fff',  bg:'#8e44ad'},
  }[style];
  if (!s) return;
  const o = new fabric.IText(s.txt, {
    right:100, top:100, fontFamily:'Cairo', fontSize:s.fs, fontWeight:s.fw,
    fill:s.fill, textAlign:'right', direction:'rtl', originX:'right',
    backgroundColor: s.bg||'',
  });
  dm.add(o); dm.setActiveObject(o); dm.renderAll(); dmSaveHist();
}

/* ── Shapes ── */
function _dmAdd(obj) { dm.add(obj); dm.setActiveObject(obj); dm.renderAll(); dmSaveHist(); }
function dmAddRect()     { _dmAdd(new fabric.Rect({left:80,top:80,width:180,height:110,fill:'#10b46a',stroke:'transparent',strokeWidth:0})); }
function dmAddRounded()  { _dmAdd(new fabric.Rect({left:80,top:80,width:180,height:110,fill:'#a78bfa',stroke:'transparent',strokeWidth:0,rx:20,ry:20})); }
function dmAddCircle()   { _dmAdd(new fabric.Circle({left:90,top:90,radius:70,fill:'#4facfe',stroke:'transparent',strokeWidth:0})); }
function dmAddTriangle() { _dmAdd(new fabric.Triangle({left:80,top:70,width:150,height:130,fill:'#f093fb',stroke:'transparent',strokeWidth:0})); }
function dmAddDiamond()  { _dmAdd(new fabric.Polygon([{x:80,y:0},{x:160,y:80},{x:80,y:160},{x:0,y:80}],{left:90,top:90,fill:'#f59e0b',stroke:'transparent',strokeWidth:0})); }
function dmAddStar() {
  const pts=[]; for(let i=0;i<10;i++){const a=(Math.PI/5)*i-Math.PI/2,r=i%2===0?80:36;pts.push({x:Math.cos(a)*r,y:Math.sin(a)*r});}
  _dmAdd(new fabric.Polygon(pts,{left:90,top:90,fill:'#ffd89b',stroke:'transparent',strokeWidth:0}));
}
function dmAddLine()  { _dmAdd(new fabric.Line([40,40,300,40],{stroke:'#000',strokeWidth:3,selectable:true})); }
function dmAddArrow() {
  _dmAdd(new fabric.Path('M 0 0 L 150 0 M 110 -25 L 150 0 L 110 25',{left:90,top:90,stroke:'#000',strokeWidth:4,fill:'transparent'}));
}
function dmAddHeart() {
  _dmAdd(new fabric.Path('M 0,-30 C 0,-70 -60,-70 -60,-30 C -60,10 0,50 0,70 C 0,50 60,10 60,-30 C 60,-70 0,-70 0,-30 Z',{left:90,top:80,fill:'#e05c5c',stroke:'transparent',strokeWidth:0}));
}
function dmAddFrame(type='simple') {
  const cw=dm.width, ch=dm.height, m=20, r=type==='double'?2:3;
  _dmAdd(new fabric.Rect({left:m,top:m,width:cw-m*2,height:ch-m*2,fill:'transparent',stroke:'#000',strokeWidth:r,rx:4,ry:4}));
  if(type==='double') _dmAdd(new fabric.Rect({left:m+10,top:m+10,width:cw-m*2-20,height:ch-m*2-20,fill:'transparent',stroke:'#000',strokeWidth:1,rx:4,ry:4}));
}

/* ── Image ── */
function dmUploadImg(ev) {
  const f=ev.target.files[0]; if(!f) return;
  const r=new FileReader();
  r.onload=e=>fabric.Image.fromURL(e.target.result,img=>{
    img.scaleToWidth(Math.min(280,dm.width*.45));
    img.set({left:60,top:60});
    dm.add(img); dm.setActiveObject(img); dm.renderAll(); dmSaveHist();
  });
  r.readAsDataURL(f); ev.target.value='';
}
function dmAddImageUrl() {
  const url=document.getElementById('dm-url-inp').value.trim(); if(!url) return;
  fabric.Image.fromURL(url,img=>{
    if(!img) return;
    img.scaleToWidth(Math.min(280,dm.width*.45));
    img.set({left:60,top:60,crossOrigin:'anonymous'});
    dm.add(img); dm.setActiveObject(img); dm.renderAll(); dmSaveHist();
  },{crossOrigin:'anonymous'});
  document.getElementById('dm-url-inp').value='';
}

/* ── Background ── */
function dmSetBg(color) { if(!dm) return; dm.setBackgroundColor(color,()=>dm.renderAll()); dmSaveHist(); }
function dmSetBgGrad(c1,c2) {
  if(!dm) return;
  dm.setBackgroundColor(new fabric.Gradient({type:'linear',coords:{x1:0,y1:0,x2:dm.width,y2:dm.height},colorStops:[{offset:0,color:c1},{offset:1,color:c2}]}),()=>dm.renderAll());
  dmSaveHist();
}

/* ── Object helpers ── */
function dmC() { return dm?.getActiveObject(); }
function dmSet(prop,val) { const o=dmC(); if(!o||o.isProductGhost) return; o.set(prop,val); dm.renderAll(); }
function dmSetXY() {
  const o=dmC(); if(!o) return;
  o.set({left:+document.getElementById('dp-x').value, top:+document.getElementById('dp-y').value});
  o.setCoords(); dm.renderAll();
}
function dmSetWH() {
  const o=dmC(); if(!o) return;
  const w=+document.getElementById('dp-w').value, h=+document.getElementById('dp-h').value;
  o.set({scaleX:w/o.width, scaleY:h/o.height}); o.setCoords(); dm.renderAll();
}
function dmAlign(pos) {
  const o=dmC(); if(!o||o.isProductGhost) return;
  const cw=dm.width, ch=dm.height, ow=o.getScaledWidth(), oh=o.getScaledHeight();
  if(pos==='left')    o.set({left:0,originX:'left'});
  if(pos==='right')   o.set({left:cw-ow,originX:'left'});
  if(pos==='hcenter') o.set({left:(cw-ow)/2,originX:'left'});
  if(pos==='top')     o.set({top:0,originY:'top'});
  if(pos==='bottom')  o.set({top:ch-oh,originY:'top'});
  if(pos==='vcenter') o.set({top:(ch-oh)/2,originY:'top'});
  o.setCoords(); dm.renderAll(); dmSaveHist();
}
function dmDelete() {
  const o=dmC(); if(!o||o.isProductGhost) return;
  if(o.type==='activeSelection'){o.getObjects().forEach(x=>!x.isProductGhost&&dm.remove(x));dm.discardActiveObject();}
  else dm.remove(o);
  dm.renderAll(); dmSaveHist();
}
function dmClone() {
  const o=dmC(); if(!o||o.isProductGhost) return;
  o.clone(cl=>{cl.set({left:o.left+20,top:o.top+20});dm.add(cl);dm.setActiveObject(cl);dm.renderAll();dmSaveHist();});
}

/* ── Fill ── */
function dmSwitchFill(tab) {
  ['solid','grad','none'].forEach(t=>{
    document.getElementById('dp-fill-'+t).classList.toggle('on',t===tab);
    const p=document.getElementById('dp-fill-'+t+'-pane'); if(p) p.style.display=t===tab?'':'none';
  });
  if(tab==='none'){const o=dmC();if(o){o.set('fill','transparent');dm.renderAll();}}
  if(tab==='solid'){const o=dmC();if(o){o.set('fill',document.getElementById('dp-fill-clr').value);dm.renderAll();}}
}
function dmApplyGrad() {
  const o=dmC(); if(!o) return;
  const c1=document.getElementById('dp-gc1').value, c2=document.getElementById('dp-gc2').value;
  const ang=+document.getElementById('dp-gang').value*Math.PI/180;
  const w=o.width||100, h=o.height||100;
  o.set('fill',new fabric.Gradient({type:'linear',coords:{x1:(w/2)-Math.cos(ang)*(w/2),y1:(h/2)-Math.sin(ang)*(h/2),x2:(w/2)+Math.cos(ang)*(w/2),y2:(h/2)+Math.sin(ang)*(h/2)},colorStops:[{offset:0,color:c1},{offset:1,color:c2}]}));
  dm.renderAll();
}
function dmApplyDash(style) {
  const o=dmC(); if(!o) return;
  const sw=o.strokeWidth||2;
  ['solid','dashed','dotted'].forEach(s=>document.getElementById('dp-dash-'+s).classList.toggle('on',s===style));
  if(style==='solid')  o.set('strokeDashArray',null);
  if(style==='dashed') o.set('strokeDashArray',[sw*4,sw*2]);
  if(style==='dotted') o.set('strokeDashArray',[sw,sw*2]);
  dm.renderAll();
}

/* ── Text controls ── */
function dmToggleBold()   { const o=dmC();if(!o)return;o.set('fontWeight',o.fontWeight==='bold'?'normal':'bold');dm.renderAll();document.getElementById('dp-bold').classList.toggle('on',o.fontWeight==='bold'); }
function dmToggleItalic() { const o=dmC();if(!o)return;o.set('fontStyle',o.fontStyle==='italic'?'normal':'italic');dm.renderAll();document.getElementById('dp-italic').classList.toggle('on',o.fontStyle==='italic'); }
function dmToggleUnder()  { const o=dmC();if(!o)return;o.set('underline',!o.underline);dm.renderAll();document.getElementById('dp-under').classList.toggle('on',!!o.underline); }
function dmSetDir(dir)    { const o=dmC();if(!o)return;o.set({direction:dir,textAlign:dir==='rtl'?'right':'left'});dm.renderAll(); }

/* ── Image filters ── */
function dmApplyFilters() {
  const o=dmC(); if(!o||o.type!=='image') return;
  o.filters=[
    new fabric.Image.filters.Brightness({brightness:+document.getElementById('dp-bright').value}),
    new fabric.Image.filters.Contrast({contrast:+document.getElementById('dp-cont').value}),
    new fabric.Image.filters.Saturation({saturation:+document.getElementById('dp-sat').value}),
    new fabric.Image.filters.Blur({blur:+document.getElementById('dp-blur').value}),
    new fabric.Image.filters.HueRotation({rotation:+document.getElementById('dp-hue').value}),
  ];
  o.applyFilters(); dm.renderAll();
}
function dmResetFilters() {
  ['dp-bright','dp-cont','dp-sat','dp-blur','dp-hue'].forEach(id=>document.getElementById(id).value=0);
  const o=dmC();if(o&&o.type==='image'){o.filters=[];o.applyFilters();dm.renderAll();}
}

/* ── Shadow / Glow ── */
function dmSwitchEff(tab) {
  ['shadow','glow'].forEach(t=>{
    document.getElementById('dp-eff-'+t).classList.toggle('on',t===tab);
    document.getElementById('dp-'+t+'-pane').style.display=t===tab?'':'none';
  });
}
function dmHex2rgb(hex){const r=parseInt(hex.slice(1,3),16),g=parseInt(hex.slice(3,5),16),b=parseInt(hex.slice(5,7),16);return`${r},${g},${b}`;}
function dmApplyShadow() {
  const o=dmC();if(!o) return;
  o.set('shadow',new fabric.Shadow({color:`rgba(${dmHex2rgb(document.getElementById('dp-sh-clr').value)},${+document.getElementById('dp-sh-op').value})`,offsetX:+document.getElementById('dp-sh-x').value,offsetY:+document.getElementById('dp-sh-y').value,blur:+document.getElementById('dp-sh-blur').value}));
  dm.renderAll();
}
function dmRemoveShadow() { const o=dmC();if(o){o.set('shadow',null);dm.renderAll();} }
function dmApplyGlow(on) {
  if(!on){dmRemoveShadow();return;}
  const o=dmC();if(!o)return;
  o.set('shadow',new fabric.Shadow({color:`rgba(${dmHex2rgb(document.getElementById('dp-gl-clr').value)},${+document.getElementById('dp-gl-op').value})`,offsetX:0,offsetY:0,blur:+document.getElementById('dp-gl-size').value}));
  dm.renderAll();
}

/* ── Fonts ── */
function dmBuildFontChips() {
  const grid=document.getElementById('dm-font-chips'); if(!grid||grid.children.length) return;
  DM_FONTS.forEach(f=>{
    const c=document.createElement('span'); c.className='dm-font'; c.textContent=f; c.style.fontFamily=f;
    c.onclick=()=>{dmSet('fontFamily',f);document.querySelectorAll('.dm-font').forEach(x=>x.classList.toggle('on',x.textContent===f));};
    grid.appendChild(c);
  });
}

/* ── Events ── */
function dmBindEvents() {
  dm.on('selection:created', dmOnSel);
  dm.on('selection:updated', dmOnSel);
  dm.on('selection:cleared', dmOnClear);
  dm.on('object:modified',   ()=>{dmUpdatePanel();dmSaveHist();});
  dm.on('path:created',      ()=>dmSaveHist());
}
function dmOnSel() {
  document.getElementById('dm-align-bar').style.display='flex';
  dmUpdatePanel();
}
function dmOnClear() {
  document.getElementById('dm-align-bar').style.display='none';
  ['dm-pp-pos','dm-pp-fill','dm-pp-text','dm-pp-img','dm-pp-shadow','dm-pp-layer'].forEach(id=>{
    document.getElementById(id).style.display='none';
  });
  document.getElementById('dm-pp-empty').style.display='';
}
function dmUpdatePanel() {
  const o=dmC(); if(!o||o.isProductGhost){dmOnClear();return;}
  document.getElementById('dm-pp-empty').style.display='none';
  ['dm-pp-pos','dm-pp-fill','dm-pp-shadow','dm-pp-layer'].forEach(id=>document.getElementById(id).style.display='');
  document.getElementById('dm-pp-text').style.display='none';
  document.getElementById('dm-pp-img').style.display='none';

  document.getElementById('dp-x').value  =Math.round(o.left);
  document.getElementById('dp-y').value  =Math.round(o.top);
  document.getElementById('dp-w').value  =Math.round(o.getScaledWidth());
  document.getElementById('dp-h').value  =Math.round(o.getScaledHeight());
  document.getElementById('dp-rot').value=Math.round(o.angle||0);
  document.getElementById('dp-op').value =o.opacity??1;

  if(o.type==='i-text'||o.type==='text') {
    document.getElementById('dm-pp-text').style.display='';
    document.getElementById('dp-fsize').value=o.fontSize||16;
    document.getElementById('dp-fclr').value=dmHex(o.fill||'#000');
    document.getElementById('dp-csp').value=o.charSpacing||0;
    document.getElementById('dp-lh').value=o.lineHeight||1.16;
    document.getElementById('dp-bold').classList.toggle('on',o.fontWeight==='bold');
    document.getElementById('dp-italic').classList.toggle('on',o.fontStyle==='italic');
    document.getElementById('dp-under').classList.toggle('on',!!o.underline);
    document.querySelectorAll('.dm-font').forEach(c=>c.classList.toggle('on',c.textContent===(o.fontFamily||'Cairo')));
  } else if(o.type==='image') {
    document.getElementById('dm-pp-img').style.display='';
  }

  if(o.fill&&typeof o.fill==='string'&&o.fill!=='transparent'){
    document.getElementById('dp-fill-clr').value=dmHex(o.fill); dmSwitchFill('solid');
  } else if(o.fill&&o.fill.type){ dmSwitchFill('grad'); }
  else { dmSwitchFill('none'); }
  if(o.stroke) document.getElementById('dp-stroke-clr').value=dmHex(o.stroke);
  if(o.strokeWidth!==undefined) document.getElementById('dp-stroke-w').value=o.strokeWidth;
  if(o.rx!==undefined)          document.getElementById('dp-rx').value=o.rx||0;
  if(o.globalCompositeOperation) document.getElementById('dp-blend').value=o.globalCompositeOperation;
}

function dmHex(color) {
  if(!color||color==='transparent') return '#000000';
  if(typeof color!=='string') return '#000000';
  if(color.startsWith('#')) return color.length===4?'#'+color[1]+color[1]+color[2]+color[2]+color[3]+color[3]:color;
  const m=color.match(/\d+/g); if(!m) return '#000000';
  return '#'+[m[0],m[1],m[2]].map(n=>(+n).toString(16).padStart(2,'0')).join('');
}

/* ── History ── */
function dmSaveHist() {
  if(!dm) return;
  const json=JSON.stringify(dm.toJSON(['isProductGhost','excludeFromExport','selectable','evented','shadow','globalCompositeOperation','strokeDashArray']));
  dmHist=dmHist.slice(0,dmHistIdx+1); dmHist.push(json); dmHistIdx=dmHist.length-1;
}
function dmRestoreHist(idx) {
  dm.loadFromJSON(dmHist[idx],()=>{
    dm.getObjects().forEach(o=>{ if(o.isProductGhost){o.selectable=false;o.evented=false;o.excludeFromExport=true;} });
    dm.renderAll();
  });
}
function dmUndo() { if(!dm||dmHistIdx<=0) return; dmHistIdx--; dmRestoreHist(dmHistIdx); }
function dmRedo() { if(!dm||dmHistIdx>=dmHist.length-1) return; dmHistIdx++; dmRestoreHist(dmHistIdx); }

/* ── Templates ── */
async function dmLoadTemplates() {
  if(_dmTemplatesLoaded&&_dmTemplates.length) { dmRenderTemplates(); return; }
  try {
    const res=await fetch(`${window.location.origin}/api/v1/design-templates`);
    _dmTemplates=res.ok?await res.json():[];
  } catch(e){_dmTemplates=[];}
  _dmTemplatesLoaded=true;
  dmRenderTemplates();

  // Auto-load template for this product
  if(_prod) {
    const tmpl=_dmTemplates.find(t=>t.product_id==_prod.id&&t.canvas_json);
    if(tmpl) {
      dmApplyCanvasSize(tmpl.canvas_width||800, tmpl.canvas_height||800);
      dm.loadFromJSON(tmpl.canvas_json,()=>{
        dm.renderAll(); dmSaveHist(); dmHistIdx=0; dmHist=[dmHist[dmHist.length-1]];
        // Sync bg picker
        const bg=dm.backgroundColor;
        if(typeof bg==='string'&&bg.startsWith('#')){
          document.getElementById('dm-bg-picker').value=bg;
          document.getElementById('dm-bg-picker2').value=bg;
        }
      });
    } else {
      dmSaveHist(); dmHistIdx=0; dmHist=[dmHist[dmHist.length-1]];
    }
  }
}
function dmRenderTemplates() {
  const grid=document.getElementById('dm-tmpl-grid');
  const list=_dmTemplates.filter(t=>!t.product_id||t.product_id==(_prod?.id));
  if(!list.length){
    grid.innerHTML='<div style="text-align:center;padding:16px;color:#7a9282;font-size:10px">لا توجد قوالب</div>'; return;
  }
  grid.innerHTML='<div class="dm-tmpl-grid">'+list.map(t=>`
    <div class="dm-tmpl" onclick="dmLoadTemplate(${t.id})">
      ${t.thumbnail?`<img src="${t.thumbnail}">`:'<div class="dm-tmpl-ph"><i class="fa fa-palette"></i></div>'}
      <div class="dm-tmpl-nm">${t.name}</div>
    </div>`).join('')+'</div>';
}
function dmLoadTemplate(id) {
  const t=_dmTemplates.find(x=>x.id===id); if(!t||!t.canvas_json) return;
  if(!confirm(`تحميل قالب "${t.name}"؟ سيُمسح التصميم الحالي.`)) return;
  dm.clear();
  dmApplyCanvasSize(t.canvas_width||800,t.canvas_height||800);
  dm.loadFromJSON(t.canvas_json,()=>{
    dm.renderAll(); dmSaveHist(); dmHistIdx=0; dmHist=[dmHist[dmHist.length-1]];
  });
}


/* ── Clear ── */
function dmClearDesign() {
  if(!dm) return;
  dm.clear(); dm.setBackgroundColor('#ffffff',()=>dm.renderAll());
  dmSaveHist();
}

/* ── Add to cart ── */
async function dmAddToCart() {
  if(!dm||!_prod) return;
  const btn=document.getElementById('dm-add-cart-btn');
  btn.disabled=true; btn.innerHTML='<i class="fa fa-spinner fa-spin"></i> جاري الحفظ...';
  try {
    dm.discardActiveObject(); dm.renderAll();
    const dataUrl=dm.toDataURL({format:'png',quality:.92,multiplier:2});

    // بيانات الـ canvas القابلة للتعديل — تُحفظ مع الطلب ليتمكن الأدمن من تحويلها لقالب
    const designJson=JSON.stringify(dm.toJSON(['selectable','hasControls','shadow','globalCompositeOperation','strokeDashArray']));
    const designW=dm.getWidth(), designH=dm.getHeight();

    const res=await fetch(`${API}/design/upload`,{
      method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json'},
      body:JSON.stringify({image:dataUrl,product_id:_prod.id}),
    });
    if(!res.ok) throw new Error('فشل رفع التصميم');
    const {url:designUrl}=await res.json();

    const qty=parseInt(document.getElementById('qty').value)||1;
    const price=parseFloat(_prod.price||_prod.unit_price||0);
    const disc=parseFloat(_prod.discount||0);
    const finalPrice=disc>0?price-price*disc/100:price;
    const imgSrc=(_prod.image_fullpath&&Array.isArray(_prod.image_fullpath)&&_prod.image_fullpath[0])?_prod.image_fullpath[0]:'';
    const existing=cart.find(i=>i.id===_prod.id&&i.design_url===designUrl);
    if(existing){existing.qty+=qty;}
    else{cart.push({id:_prod.id,name:_prod.name,price:finalPrice,qty,img:imgSrc,design_url:designUrl,design_json:designJson,design_w:designW,design_h:designH,has_design:true});}
    localStorage.setItem('f_cart',JSON.stringify(cart)); renderCart();
    closeDesignEditor(); toast('✓ أُضيف للسلة مع التصميم المخصص'); openCart();
  } catch(err){
    toast('خطأ: '+(err.message||'حدث خطأ غير متوقع'),'err');
  } finally {
    btn.disabled=false; btn.innerHTML='<i class="fa fa-cart-plus"></i> أضف للسلة مع التصميم';
  }
}

// ── تحديث renderCart لعرض صورة التصميم ──
// نُعيد تعريف renderCart لتشمل صورة التصميم
const _origRenderCart = renderCart;
renderCart = function() {
  const badge=document.getElementById('cart-badge');
  const bd=document.getElementById('cart-bd');
  const ft=document.getElementById('cart-ft');
  const tot=document.getElementById('cart-tot');
  badge.textContent = cart.reduce((s,i)=>s+i.qty,0);
  badge.classList.toggle('off', cart.length===0);
  if(!cart.length){
    bd.innerHTML=`<div class="cart-empty-msg"><i class="fa fa-cart-shopping"></i><p>السلة فارغة</p></div>`;
    ft.style.display='none'; return;
  }
  bd.innerHTML = cart.map(i=>`
    <div class="cart-item">
      <div class="cart-item-img">
        ${i.design_url
          ? `<img src="${i.design_url}" alt="تصميم" style="width:100%;height:100%;object-fit:cover" onclick="window.open('${i.design_url}','_blank')">`
          : (i.img ? `<img src="${i.img}" onerror="this.remove()">` : '<i class="fa fa-box" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:20px"></i>')
        }
      </div>
      <div style="flex:1">
        <div class="cart-item-name">${i.name}</div>
        <div class="cart-item-price">${i.qty} × ${parseFloat(i.price).toFixed(2)} ${CUR}</div>
        ${i.has_design ? '<span class="design-badge"><i class="fa fa-palette"></i> تصميم مخصص</span>' : ''}
      </div>
      <button class="cart-item-del" onclick="removeFromCart(${i.id},'${i.design_url||''}')"><i class="fa fa-xmark"></i></button>
    </div>`).join('');
  tot.textContent = cart.reduce((s,i)=>s+i.qty*parseFloat(i.price),0).toFixed(2)+' '+CUR;
  ft.style.display='block';
};

// removeFromCart يحتاج تحديث ليدعم نفس المنتج بتصاميم مختلفة
function removeFromCart(id, designUrl='') {
  if (designUrl) {
    cart = cart.filter(i => !(i.id===id && (i.design_url||'')===designUrl));
  } else {
    cart = cart.filter(i => i.id!==id);
  }
  localStorage.setItem('f_cart', JSON.stringify(cart));
  renderCart();
}

// keyboard shortcuts للمحرر
document.addEventListener('keydown', e => {
  if (!document.getElementById('design-veil').classList.contains('on')) return;
  if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
  if ((e.ctrlKey||e.metaKey) && e.key==='z') { e.preventDefault(); dmUndo(); }
  if ((e.ctrlKey||e.metaKey) && e.key==='y') { e.preventDefault(); dmRedo(); }
  if (e.key==='Escape') closeDesignEditor();
  if (e.key==='Delete'||e.key==='Backspace') { e.preventDefault(); dmDelete(); }
});
</script>
</body>
</html>
