<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title id="page-title">ايليت دعاية — طباعة وتصميم</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* ══════════════════════════════════════
   TOKENS
   ══════════════════════════════════════ */
:root {
  /* ── Elite Identity: Emerald Green ── */
  --red       : #10b46a;   /* primary = emerald */
  --red-d     : #0c8f52;
  --red-10    : rgba(16,180,106,.10);
  --navy      : #0f1512;   /* dark ink */
  --navy-2    : #1a2e1f;
  --amber     : #f59e0b;
  --amber-d   : #d97706;
  --green     : #16A34A;
  --surface   : #f5f8f4;   /* elite bg */
  --white     : #FFFFFF;
  --border    : #d6e8d9;
  --text      : #0f1512;
  --text-2    : #4a5e50;
  --text-3    : #8fa895;
  --r-sm      : 8px;
  --r-md      : 14px;
  --r-lg      : 22px;
  --r-xl      : 32px;
  --sh-sm     : 0 2px 8px rgba(15,21,18,.07);
  --sh-md     : 0 6px 20px rgba(15,21,18,.11);
  --sh-lg     : 0 12px 40px rgba(15,21,18,.15);
  --max-w     : 1260px;
  --font      : 'IBM Plex Sans Arabic', 'Cairo', sans-serif;
  --ease      : cubic-bezier(.4,0,.2,1);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body { font-family: var(--font); background: var(--surface); color: var(--text); direction: rtl; -webkit-font-smoothing: antialiased; }
a { text-decoration: none; color: inherit; }
ul { list-style: none; }
img { display: block; max-width: 100%; }
button { font-family: var(--font); }

.wrap { max-width: var(--max-w); margin-inline: auto; padding-inline: 20px; }

/* ══ SCROLL REVEAL ══ */
.reveal { opacity: 0; transform: translateY(24px); transition: opacity .6s var(--ease), transform .6s var(--ease); }
.reveal.in { opacity: 1; transform: none; }
.reveal.delay-1 { transition-delay: .1s; }
.reveal.delay-2 { transition-delay: .2s; }
.reveal.delay-3 { transition-delay: .3s; }
.reveal.delay-4 { transition-delay: .4s; }

/* ══ TOPBAR ══ */
.topbar {
  background: var(--navy);
  padding: 7px 0;
  font-size: 13px;
  color: #8CA0BE;
  display: none;
}
.topbar.visible { display: block; }
.topbar-inner { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
.topbar-left { display: flex; gap: 18px; }
.topbar-left a { display: flex; align-items: center; gap: 5px; color: #8CA0BE; transition: color .2s; }
.topbar-left a:hover { color: #fff; }
.topbar-left i { color: var(--amber); font-size: 11px; }
.topbar-right { display: flex; gap: 14px; }
.topbar-right a { color: #8CA0BE; font-size: 12px; transition: color .2s; }
.topbar-right a:hover { color: var(--amber); }

/* ══ HEADER ══ */
.header {
  background: var(--white);
  position: sticky; top: 0; z-index: 200;
  box-shadow: 0 1px 0 var(--border);
}
.header-body {
  display: flex; align-items: center;
  gap: 20px; padding: 12px 0;
}

/* Logo */
.logo { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
.logo-mark {
  width: 130px; height: 54px;
  background: #000;
  border: 1.5px solid #222;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  overflow: hidden;
  padding: 6px 10px;
  box-shadow: 0 2px 10px rgba(15,21,18,.09);
  flex-shrink: 0;
}
.logo-mark img { width: 100%; height: 100%; object-fit: contain; }
.logo-mark svg { width: 100%; height: 100%; }
.logo-name { font-size: 18px; font-weight: 900; color: var(--navy); line-height: 1.1; }
.logo-tag  { font-size: 11px; color: var(--text-2); font-weight: 500; }

/* Search */
.search-wrap { flex: 1; max-width: 500px; position: relative; }
.search-wrap input {
  width: 100%; padding: 10px 42px 10px 18px;
  border: 1.5px solid var(--border);
  border-radius: 50px;
  font-family: var(--font); font-size: 14px;
  background: var(--surface); color: var(--text);
  outline: none; transition: border-color .2s, box-shadow .2s;
}
.search-wrap input::placeholder { color: var(--text-3); }
.search-wrap input:focus { border-color: var(--red); box-shadow: 0 0 0 3px var(--red-10); }
.search-wrap .s-btn {
  position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer;
  color: var(--text-2); font-size: 14px; transition: color .2s;
}
.search-wrap .s-btn:hover { color: var(--red); }

/* Actions */
.h-actions { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.wa-btn {
  display: flex; align-items: center; gap: 7px;
  background: #25D366; color: #fff;
  padding: 9px 16px; border-radius: 50px;
  font-size: 13px; font-weight: 700;
  transition: background .2s, transform .15s;
}
.wa-btn:hover { background: #1ebe59; transform: translateY(-1px); }
.icon-btn {
  position: relative; width: 40px; height: 40px;
  border-radius: 50%; border: none; cursor: pointer;
  background: var(--surface); color: var(--navy);
  font-size: 16px; display: flex; align-items: center; justify-content: center;
  transition: background .2s, color .2s;
}
.icon-btn:hover { background: var(--red); color: #fff; }
.badge {
  position: absolute; top: -2px; left: -2px;
  background: var(--red); color: #fff;
  font-size: 9px; font-weight: 800;
  min-width: 17px; height: 17px; border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  border: 2px solid #fff;
}
.badge.off { display: none; }

/* ══ NAVBAR ══ */
.navbar { background: var(--navy); }
.nav-list { display: flex; align-items: stretch; }
.nav-item { position: relative; }
.nav-link {
  display: flex; align-items: center; gap: 5px;
  padding: 13px 16px; color: #BFCFE3;
  font-size: 14px; font-weight: 600; white-space: nowrap;
  transition: color .2s, background .2s; border-bottom: 2px solid transparent;
}
.nav-link i.fa-chevron-down { font-size: 9px; transition: transform .2s; }
.nav-item:hover .nav-link,
.nav-link.active { color: #fff; background: rgba(255,255,255,.06); }
.nav-item:hover .fa-chevron-down { transform: rotate(180deg); }

/* Mega dropdown */
.mega-menu {
  position: absolute; top: 100%; right: 0;
  background: var(--white);
  border-radius: 0 0 var(--r-md) var(--r-md);
  box-shadow: var(--sh-lg);
  min-width: 240px; padding: 8px 0;
  opacity: 0; visibility: hidden;
  transform: translateY(-8px);
  transition: opacity .2s, transform .2s, visibility .2s;
  z-index: 300;
}
.nav-item:hover .mega-menu { opacity: 1; visibility: visible; transform: none; }
.mega-menu a {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 18px; font-size: 14px; color: var(--text);
  transition: background .15s, color .15s;
}
.mega-menu a:hover { background: var(--surface); color: var(--red); }
.mega-menu a i { width: 18px; color: var(--red); font-size: 13px; }
.mega-menu .sep { height: 1px; background: var(--border); margin: 4px 0; }

.cta-nav {
  display: flex; align-items: center;
  padding: 8px 16px !important;
  margin-right: auto;
}
.cta-nav a {
  background: var(--amber); color: var(--navy) !important;
  border-radius: 50px; padding: 7px 18px !important;
  font-weight: 800 !important; font-size: 13px !important;
  transition: background .2s, transform .15s !important;
}
.cta-nav a:hover { background: var(--amber-d) !important; transform: translateY(-1px); }

/* Mobile menu toggle */
.menu-toggle {
  display: none; background: none; border: none; cursor: pointer;
  color: #fff; font-size: 22px; padding: 6px; margin-right: auto;
}

/* ══ HERO ══ */
.hero {
  background: var(--navy);
  position: relative; overflow: hidden;
  min-height: 500px;
}
.hero-bg {
  position: absolute; inset: 0;
  background: radial-gradient(ellipse 60% 80% at 30% 50%, rgba(16,180,106,.18) 0%, transparent 70%),
              radial-gradient(ellipse 40% 60% at 80% 20%, rgba(245,158,11,.08) 0%, transparent 60%);
}
.hero-dots {
  position: absolute; inset: 0; overflow: hidden; pointer-events: none;
  background-image: radial-gradient(circle, rgba(255,255,255,.06) 1px, transparent 1px);
  background-size: 30px 30px;
}
.hero-inner {
  display: grid; grid-template-columns: 1fr 420px;
  align-items: center; gap: 40px;
  padding: 64px 0; position: relative; z-index: 1;
}
.hero-text { color: #fff; }
.hero-pill {
  display: inline-flex; align-items: center; gap: 6px;
  background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.18);
  color: rgba(255,255,255,.9); font-size: 12px; font-weight: 700;
  padding: 5px 14px; border-radius: 50px; margin-bottom: 20px;
  backdrop-filter: blur(4px);
}
.hero-pill::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: var(--amber); animation: pulse-dot 2s infinite; }
@keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.7;transform:scale(1.4)} }
.hero-h1 {
  font-size: 52px; font-weight: 900; line-height: 1.12;
  margin-bottom: 16px; text-wrap: balance;
}
.hero-h1 .accent { color: var(--amber); position: relative; }
.hero-sub { font-size: 17px; color: rgba(255,255,255,.72); line-height: 1.7; margin-bottom: 32px; max-width: 460px; font-weight: 500; }
.hero-btns { display: flex; gap: 12px; flex-wrap: wrap; }
.btn-red {
  display: inline-flex; align-items: center; gap: 8px;
  background: var(--red); color: #fff;
  padding: 13px 28px; border-radius: 50px;
  font-size: 15px; font-weight: 800; border: none; cursor: pointer;
  transition: background .2s, transform .15s, box-shadow .2s;
}
.btn-red:hover { background: var(--red-d); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16,180,106,.40); }
.btn-ghost {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(255,255,255,.1); color: #fff;
  padding: 12px 26px; border-radius: 50px;
  font-size: 15px; font-weight: 700; border: 1.5px solid rgba(255,255,255,.35);
  cursor: pointer; transition: background .2s, border-color .2s;
}
.btn-ghost:hover { background: rgba(255,255,255,.18); border-color: rgba(255,255,255,.7); }
.hero-stats {
  display: flex; gap: 28px; margin-top: 36px; padding-top: 28px;
  border-top: 1px solid rgba(255,255,255,.12);
}
.hero-stat strong { display: block; font-size: 26px; font-weight: 900; color: var(--amber); }
.hero-stat span { font-size: 12px; color: rgba(255,255,255,.6); font-weight: 500; }

/* Hero visual grid */
.hero-grid {
  display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: 1fr 1fr;
  gap: 10px; height: 360px;
}
.hero-cell {
  background: rgba(255,255,255,.07);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: var(--r-md); overflow: hidden;
  position: relative; cursor: pointer;
  transition: transform .3s, box-shadow .3s;
}
.hero-cell:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(0,0,0,.3); }
.hero-cell.span-h { grid-column: span 2; }
.hero-cell img { width: 100%; height: 100%; object-fit: cover; }
.hero-cell-ph {
  width: 100%; height: 100%;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 8px; color: rgba(255,255,255,.3);
}
.hero-cell-ph i { font-size: 36px; }
.hero-cell-ph span { font-size: 12px; font-weight: 600; }
.hero-cell-label {
  position: absolute; bottom: 10px; right: 10px;
  background: rgba(22,32,57,.8); color: #fff;
  font-size: 11px; font-weight: 700;
  padding: 4px 10px; border-radius: 50px;
  backdrop-filter: blur(4px);
}

/* Slider dots over hero */
.hero-controls {
  position: absolute; bottom: 20px; right: 50%; transform: translateX(50%);
  display: flex; gap: 6px; z-index: 10;
}
.h-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: rgba(255,255,255,.35); border: none; cursor: pointer;
  transition: background .2s, width .25s; padding: 0;
}
.h-dot.on { background: var(--amber); width: 24px; border-radius: 4px; }

/* ══ TRUST BAR ══ */
.trust-bar { background: var(--white); border-bottom: 1px solid var(--border); }
.trust-list { display: flex; align-items: stretch; }
.trust-item {
  flex: 1; display: flex; align-items: center; gap: 13px;
  padding: 18px 24px;
  border-left: 1px solid var(--border);
}
.trust-item:first-child { border-left: none; }
.trust-icon {
  width: 44px; height: 44px; border-radius: 11px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center; font-size: 18px;
}
.trust-icon.r { background: rgba(16,180,106,.09); color: var(--red); }
.trust-icon.a { background: #FFFBEB; color: var(--amber); }
.trust-icon.b { background: #EFF6FF; color: #3B82F6; }
.trust-icon.g { background: #F0FDF4; color: var(--green); }
.trust-text strong { display: block; font-size: 13px; font-weight: 800; color: var(--navy); }
.trust-text span { font-size: 12px; color: var(--text-2); }

/* ══ SECTIONS ══ */
.sec { padding: 56px 0; }
.sec-white { background: var(--white); }

.sec-head {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 30px;
}
.sec-title {
  font-size: 22px; font-weight: 900; color: var(--navy);
  display: flex; align-items: center; gap: 10px;
}
.sec-title::before {
  content: ''; width: 4px; height: 26px;
  background: var(--red); border-radius: 2px;
}
.sec-sub { font-size: 14px; color: var(--text-2); margin-top: 4px; font-weight: 500; }
.view-all {
  display: flex; align-items: center; gap: 6px;
  font-size: 13px; font-weight: 700; color: var(--red);
  padding: 7px 14px; border: 1.5px solid var(--red-10);
  border-radius: 50px; background: var(--red-10);
  transition: background .2s, border-color .2s;
}
.view-all:hover { background: var(--red); color: #fff; border-color: var(--red); }

/* ══ CATEGORIES ══ */
.cat-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
}
.cat-card {
  border-radius: var(--r-md); overflow: hidden;
  position: relative; cursor: pointer;
  background: var(--navy);
  aspect-ratio: .9;
  box-shadow: var(--sh-sm);
  transition: box-shadow .3s, transform .3s;
}
.cat-card:hover { box-shadow: var(--sh-lg); transform: translateY(-5px); }
.cat-bg { transition: transform .4s var(--ease); }
.cat-card:hover .cat-bg { transform: scale(1.04); }
.cat-ph {
  width: 100%; height: 100%;
  background: linear-gradient(145deg, #134e2a, #1e7a43);
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 12px; color: rgba(255,255,255,.6);
}
.cat-ph i { font-size: 44px; }
.cat-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(22,32,57,.88) 0%, rgba(22,32,57,.2) 55%, transparent 100%);
  display: flex; flex-direction: column; justify-content: flex-end;
  padding: 16px;
  transition: background .3s;
}
.cat-card:hover .cat-overlay { background: linear-gradient(to top, rgba(22,32,57,.95) 0%, rgba(22,32,57,.35) 60%, transparent 100%); }
.cat-name { color: #fff; font-size: 15px; font-weight: 800; }
.cat-count { color: rgba(255,255,255,.65); font-size: 12px; margin-top: 2px; }
.cat-arrow {
  position: absolute; top: 14px; left: 14px;
  width: 30px; height: 30px; border-radius: 50%;
  background: rgba(255,255,255,.15); color: #fff;
  display: flex; align-items: center; justify-content: center; font-size: 12px;
  opacity: 0; transform: translateY(6px);
  transition: opacity .25s, transform .25s;
  backdrop-filter: blur(4px);
}
.cat-card:hover .cat-arrow { opacity: 1; transform: none; }

/* ══ PRODUCTS ══ */
.prod-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 14px;
}
.prod-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: var(--r-md);
  overflow: hidden;
  transition: box-shadow .3s, transform .3s;
  position: relative;
}
.prod-card:hover { box-shadow: var(--sh-md); transform: translateY(-4px); }
.prod-img-wrap {
  position: relative; aspect-ratio: 1;
  background: var(--navy); overflow: hidden;
}
.prod-img-wrap > img {
  position: relative; z-index: 1;
  transition: transform .4s var(--ease);
}
.prod-card:hover .prod-img-wrap > img { transform: scale(1.06); }
.prod-ph {
  width: 100%; height: 100%;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 8px;
}
.prod-ph i { font-size: 48px; opacity: .4; }

/* Badges */
.badge-sale {
  position: absolute; top: 10px; right: 10px;
  background: var(--amber); color: var(--navy);
  font-size: 11px; font-weight: 800;
  padding: 3px 9px; border-radius: 50px;
}
.badge-new {
  position: absolute; top: 10px; right: 10px;
  background: var(--red); color: #fff;
  font-size: 11px; font-weight: 800;
  padding: 3px 9px; border-radius: 50px;
}

/* Hover actions on image */
.prod-actions {
  position: absolute; bottom: 0; left: 0; right: 0;
  background: linear-gradient(to top, rgba(22,32,57,.85), transparent);
  padding: 24px 10px 10px;
  display: flex; gap: 6px;
  transform: translateY(100%);
  transition: transform .3s var(--ease);
}
.prod-card:hover .prod-actions { transform: none; }
.pa-btn {
  flex: 1; padding: 8px; border: none; border-radius: var(--r-sm);
  font-family: var(--font); font-size: 12px; font-weight: 700;
  cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;
  transition: background .15s;
}
.pa-cart { background: var(--red); color: #fff; }
.pa-cart:hover { background: var(--red-d); }
.pa-wish {
  width: 34px; flex: none; background: rgba(255,255,255,.9); color: var(--navy);
  border-radius: var(--r-sm);
}
.pa-wish:hover { background: #fff; color: var(--red); }

.prod-body { padding: 13px; }
.prod-name {
  font-size: 13.5px; font-weight: 700; color: var(--navy);
  line-height: 1.45; margin-bottom: 8px;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.prod-name a { transition: color .2s; }
.prod-name a:hover { color: var(--red); }
.stars {
  display: flex; gap: 2px; color: var(--amber);
  font-size: 11px; margin-bottom: 8px; align-items: center;
}
.stars span { color: var(--text-3); font-size: 11px; margin-right: 3px; }
.prod-price { display: flex; align-items: center; gap: 7px; flex-wrap: wrap; }
.price-now { font-size: 16px; font-weight: 900; color: var(--red); }
.price-was { font-size: 12px; color: var(--text-3); text-decoration: line-through; }

/* ══ FLASH SALE ══ */
.flash-banner {
  background: linear-gradient(135deg, var(--navy) 0%, #0a2318 100%);
  border-radius: var(--r-xl); overflow: hidden;
  position: relative;
}
.flash-bg {
  position: absolute; inset: 0; pointer-events: none;
  background-image:
    radial-gradient(ellipse 50% 80% at 15% 50%, rgba(16,180,106,.15) 0%, transparent 60%),
    radial-gradient(ellipse 40% 60% at 90% 30%, rgba(245,158,11,.10) 0%, transparent 60%);
}
.flash-inner {
  position: relative; z-index: 1;
  display: grid; grid-template-columns: 1fr 1fr;
  align-items: center; gap: 40px; padding: 48px;
}
.flash-tag {
  display: inline-flex; align-items: center; gap: 7px;
  background: var(--amber); color: var(--navy);
  font-size: 12px; font-weight: 900; padding: 5px 14px; border-radius: 50px;
  margin-bottom: 16px;
}
.flash-tag i { font-size: 13px; }
.flash-title { font-size: 36px; font-weight: 900; color: #fff; line-height: 1.15; margin-bottom: 10px; }
.flash-sub { font-size: 15px; color: rgba(255,255,255,.65); margin-bottom: 26px; }
.timer-row { display: flex; align-items: center; gap: 8px; margin-bottom: 28px; flex-wrap: wrap; }
.timer-label-text { font-size: 13px; color: rgba(255,255,255,.6); font-weight: 600; margin-left: 4px; }
.t-block {
  display: flex; flex-direction: column; align-items: center;
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.14);
  border-radius: 10px; padding: 8px 14px; min-width: 58px;
}
.t-num { font-size: 26px; font-weight: 900; color: var(--amber); line-height: 1; }
.t-lbl { font-size: 10px; color: rgba(255,255,255,.55); margin-top: 3px; }
.t-sep { font-size: 22px; font-weight: 900; color: rgba(255,255,255,.4); align-self: flex-start; margin-top: 6px; }

.flash-right {
  display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
}
.flash-prod-card {
  background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: var(--r-md); padding: 14px;
  transition: background .2s, transform .2s;
  cursor: pointer;
}
.flash-prod-card:hover { background: rgba(255,255,255,.11); transform: translateY(-3px); }
.flash-prod-img {
  width: 100%; aspect-ratio: 1;
  background: rgba(255,255,255,.06); border-radius: var(--r-sm);
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 10px; overflow: hidden;
}
.flash-prod-img i { font-size: 36px; color: rgba(255,255,255,.2); }
.flash-prod-img img { width: 100%; height: 100%; object-fit: cover; }
.flash-prod-name { font-size: 12px; font-weight: 700; color: rgba(255,255,255,.85); margin-bottom: 6px; }
.flash-prod-price { font-size: 14px; font-weight: 900; color: var(--amber); }
.flash-prod-was  { font-size: 11px; color: rgba(255,255,255,.35); text-decoration: line-through; margin-right: 5px; }

/* ══ OFFER STRIP ══ */
.offer-strip {
  display: grid; grid-template-columns: 1fr 1fr; gap: 14px;
  margin-top: 14px;
}
.offer-card {
  border-radius: var(--r-lg); padding: 28px 30px;
  display: flex; align-items: center; gap: 18px;
  position: relative; overflow: hidden;
  transition: transform .25s;
}
.offer-card:hover { transform: translateY(-3px); }
.offer-card.red-card { background: linear-gradient(135deg, var(--red) 0%, var(--red-d) 100%); }
.offer-card.navy-card { background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 100%); }
.offer-card::before {
  content: ''; position: absolute; top: -20px; left: -20px;
  width: 140px; height: 140px; border-radius: 50%;
  background: rgba(255,255,255,.06);
}
.offer-card-icon {
  font-size: 44px; flex-shrink: 0; position: relative; z-index: 1;
}
.offer-card-text { position: relative; z-index: 1; color: #fff; }
.offer-card-text strong { display: block; font-size: 18px; font-weight: 900; margin-bottom: 4px; }
.offer-card-text p { font-size: 13px; opacity: .75; margin-bottom: 14px; }
.btn-white {
  display: inline-flex; align-items: center; gap: 6px;
  background: rgba(255,255,255,.15); color: #fff;
  padding: 7px 16px; border-radius: 50px;
  font-size: 13px; font-weight: 700; border: 1px solid rgba(255,255,255,.3);
  transition: background .2s;
}
.btn-white:hover { background: rgba(255,255,255,.28); }

/* ══ CLIENTS MARQUEE ══ */
.clients-sec { background: var(--white); padding: 36px 0; }
.marquee-wrap { overflow: hidden; position: relative; }
.marquee-wrap::before,
.marquee-wrap::after {
  content: ''; position: absolute; top: 0; bottom: 0; width: 80px; z-index: 2;
}
.marquee-wrap::before { right: 0; background: linear-gradient(to left, var(--white), transparent); }
.marquee-wrap::after  { left: 0; background: linear-gradient(to right, var(--white), transparent); }
.marquee-track {
  display: flex; gap: 16px; width: max-content;
  animation: marquee 28s linear infinite;
}
.marquee-track:hover { animation-play-state: paused; }
@keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
.client-chip {
  display: flex; align-items: center; justify-content: center;
  height: 50px; padding: 0 22px; flex-shrink: 0;
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 50px; font-size: 13px; font-weight: 700;
  color: var(--text-2); transition: border-color .2s, color .2s, box-shadow .2s;
}
.client-chip:hover { border-color: var(--red); color: var(--red); box-shadow: var(--sh-sm); }
.client-chip-logo { padding: 0 18px; }
.client-chip-logo img { display: block; }

/* ══ SKELETON ══ */
.skel {
  background: linear-gradient(90deg, #eeebe5 25%, #e5e0d8 50%, #eeebe5 75%);
  background-size: 200% 100%;
  animation: skel-sh 1.4s infinite; border-radius: 6px;
}
@keyframes skel-sh { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
.skel-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
.skel-card { border-radius: var(--r-md); }
.skel-sq { aspect-ratio: .9; }
.skel-pgrid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; }
.skel-psq { aspect-ratio: 1; }
.skel-line { height: 13px; margin: 12px 12px 6px; }
.skel-line-sm { height: 11px; margin: 0 12px 8px; width: 55%; }
.skel-btn { height: 32px; margin: 0 12px 12px; border-radius: 6px; }

/* ══ EMPTY STATE ══ */
.empty { text-align: center; padding: 64px 20px; }
.empty i { font-size: 52px; color: var(--border); display: block; margin-bottom: 14px; }
.empty p { font-size: 15px; color: var(--text-2); }

/* ══ FOOTER ══ */
.footer { background: var(--navy); color: #7F96B8; padding: 60px 0 0; }
.footer-grid { display: grid; grid-template-columns: 2fr 1.2fr 1fr 1.2fr; gap: 48px; padding-bottom: 48px; border-bottom: 1px solid rgba(255,255,255,.07); }

.footer-brand {}
.f-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
.f-logo-mark { width: 110px; height: 44px; background: #000; border: 1.5px solid #333; border-radius: 10px; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 4px 8px; }
.f-logo-mark img { width: 100%; height: 100%; object-fit: contain; }
.f-logo-name { color: #fff; font-size: 17px; font-weight: 900; }
.f-desc { font-size: 13.5px; line-height: 1.85; margin-bottom: 20px; }
.f-social { display: flex; gap: 8px; }
.f-soc {
  width: 36px; height: 36px; border-radius: 9px;
  background: rgba(255,255,255,.07); display: flex; align-items: center; justify-content: center;
  font-size: 14px; color: #7F96B8;
  transition: background .2s, color .2s;
}
.f-soc:hover { background: var(--red); color: #fff; }

.f-col h4 {
  color: #fff; font-size: 14px; font-weight: 800;
  margin-bottom: 18px; padding-bottom: 10px;
  border-bottom: 1px solid rgba(255,255,255,.08);
}
.f-links { display: flex; flex-direction: column; gap: 9px; }
.f-links a {
  display: flex; align-items: center; gap: 6px;
  font-size: 13.5px; color: #7F96B8;
  transition: color .2s, gap .2s;
}
.f-links a::before { content: '›'; color: var(--red); font-size: 16px; line-height: 1; }
.f-links a:hover { color: #fff; gap: 9px; }

.f-contact { display: flex; flex-direction: column; gap: 13px; }
.f-ci { display: flex; gap: 11px; align-items: flex-start; font-size: 13.5px; }
.f-ci i { color: var(--amber); font-size: 14px; margin-top: 2px; flex-shrink: 0; }

.footer-bottom {
  padding: 18px 0;
  display: flex; align-items: center; justify-content: space-between;
  font-size: 12.5px; flex-wrap: wrap; gap: 8px;
}
.footer-bottom a { color: var(--amber); transition: opacity .2s; }
.footer-bottom a:hover { opacity: .8; }

/* ══ CART SIDEBAR ══ */
.cart-veil {
  position: fixed; inset: 0; background: rgba(10,15,30,.55);
  z-index: 400; opacity: 0; visibility: hidden;
  transition: opacity .3s, visibility .3s;
  backdrop-filter: blur(2px);
}
.cart-veil.on { opacity: 1; visibility: visible; }
.cart-drawer {
  position: fixed; top: 0; right: -420px; width: 400px; height: 100%;
  background: var(--white); z-index: 401;
  display: flex; flex-direction: column;
  transition: right .35s var(--ease);
  box-shadow: -8px 0 40px rgba(0,0,0,.18);
}
.cart-drawer.on { right: 0; }
.cart-hd {
  display: flex; align-items: center; justify-content: space-between;
  padding: 18px 20px; border-bottom: 1px solid var(--border);
}
.cart-hd-title { font-size: 17px; font-weight: 900; }
.cart-x {
  width: 34px; height: 34px; border-radius: 50%; border: none;
  background: var(--surface); cursor: pointer; font-size: 17px;
  display: flex; align-items: center; justify-content: center;
  transition: background .2s;
}
.cart-x:hover { background: var(--red); color: #fff; }
.cart-bd { flex: 1; overflow-y: auto; padding: 16px; }
.cart-empty-msg { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 14px; color: var(--text-2); }
.cart-empty-msg i { font-size: 58px; color: var(--border); }
.cart-ft { padding: 16px 20px; border-top: 1px solid var(--border); }
.cart-total-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.cart-total-row .label { font-size: 14px; color: var(--text-2); }
.cart-total-row .val { font-size: 20px; font-weight: 900; color: var(--red); }

/* Cart item */
.cart-item { display: flex; gap: 12px; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border); }
.cart-item-img { width: 58px; height: 58px; border-radius: var(--r-sm); background: var(--surface); flex-shrink: 0; overflow: hidden; display: flex; align-items: center; justify-content: center; }
.cart-item-img img { width: 100%; height: 100%; object-fit: cover; }
.cart-item-img i { font-size: 22px; color: var(--border); }
.cart-item-info { flex: 1; min-width: 0; }
.cart-item-name { font-size: 13px; font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cart-item-price { font-size: 14px; font-weight: 900; color: var(--red); margin-top: 4px; }
.qty-ctrl { display: flex; align-items: center; gap: 6px; }
.qty-btn { width: 26px; height: 26px; border-radius: 50%; border: 1px solid var(--border); background: none; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; transition: background .15s, border-color .15s; }
.qty-btn:hover { background: var(--red); border-color: var(--red); color: #fff; }
.qty-val { min-width: 22px; text-align: center; font-weight: 800; font-size: 14px; }
.cart-item-del { background: none; border: none; cursor: pointer; color: var(--text-3); font-size: 15px; padding: 4px; transition: color .2s; }
.cart-item-del:hover { color: var(--red); }

/* ══ TOAST ══ */
.toasts { position: fixed; bottom: 22px; right: 22px; z-index: 600; display: flex; flex-direction: column; gap: 8px; }
.toast {
  background: var(--navy); color: #fff;
  padding: 13px 18px; border-radius: var(--r-md);
  font-size: 14px; font-weight: 600;
  display: flex; align-items: center; gap: 9px;
  box-shadow: var(--sh-lg);
  animation: t-in .3s var(--ease), t-out .3s var(--ease) 2.6s forwards;
}
.toast.ok  { border-right: 3px solid var(--red); }
.toast.err { border-right: 3px solid #e74c3c; }
@keyframes t-in  { from { opacity:0; transform:translateX(16px) } to { opacity:1; transform:none } }
@keyframes t-out { to { opacity:0; transform:translateX(16px) } }

/* ══ MOBILE MENU ══ */
.mobile-nav {
  display: none; flex-direction: column;
  background: var(--white); border-top: 1px solid var(--border);
  padding: 8px 0; box-shadow: var(--sh-md);
}
.mobile-nav.open { display: flex; }
.mobile-nav a {
  display: flex; align-items: center; gap: 10px;
  padding: 13px 20px; font-size: 15px; font-weight: 600; color: var(--navy);
  border-bottom: 1px solid var(--border);
  transition: background .15s;
}
.mobile-nav a:last-child { border-bottom: none; }
.mobile-nav a:hover { background: var(--surface); }
.mobile-nav a i { width: 20px; color: var(--red); }

/* ══ RESPONSIVE ══ */
@media (max-width: 1100px) {
  .prod-grid { grid-template-columns: repeat(4, 1fr); }
  .flash-inner { padding: 36px; }
}
@media (max-width: 900px) {
  .cat-grid { grid-template-columns: repeat(3, 1fr); }
  .prod-grid { grid-template-columns: repeat(3, 1fr); }
  .footer-grid { grid-template-columns: 1fr 1fr; gap: 32px; }
  .hero-inner { grid-template-columns: 1fr; }
  .hero-grid { display: none; }
  .hero-h1 { font-size: 38px; }
  .flash-inner { grid-template-columns: 1fr; }
  .flash-right { grid-template-columns: repeat(4, 1fr); }
  .skel-grid { grid-template-columns: repeat(3, 1fr); }
  .skel-pgrid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 680px) {
  .search-wrap { display: none; }
  .menu-toggle { display: flex !important; }
  .cta-nav { display: none; }
  .nav-list { display: none; }
  .cat-grid { grid-template-columns: repeat(2, 1fr); }
  .prod-grid { grid-template-columns: repeat(2, 1fr); }
  .trust-list { flex-wrap: wrap; }
  .trust-item { flex: 1 1 50%; }
  .trust-item:nth-child(2) { border-left: none; }
  .offer-strip { grid-template-columns: 1fr; }
  .footer-grid { grid-template-columns: 1fr; }
  .flash-right { grid-template-columns: repeat(2, 1fr); }
  .hero-stats { gap: 20px; flex-wrap: wrap; }
  .skel-grid { grid-template-columns: repeat(2, 1fr); }
  .skel-pgrid { grid-template-columns: repeat(2, 1fr); }
  .cart-drawer { width: 100%; right: -100%; }
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
        <a href="/storefront/account"><i class="fa fa-user"></i> حسابي</a>
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
        <input id="search-q" type="text" placeholder="ابحث عن منتج أو تصنيف...">
        <button class="s-btn" onclick="doSearch()"><i class="fa fa-search"></i></button>
      </div>

      <div class="h-actions">
        <a class="wa-btn" id="wa-btn" href="#" style="display:none">
          <i class="fab fa-whatsapp"></i> واتساب
        </a>
        <a href="/storefront/account" class="icon-btn" title="حسابي"><i class="fa fa-user"></i></a>
        <button class="icon-btn" onclick="openCart()" title="السلة">
          <i class="fa fa-bag-shopping"></i>
          <span class="badge off" id="cart-badge">0</span>
        </button>
      </div>
    </div>
  </div>

  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="wrap" style="position:relative">
      <button class="menu-toggle" onclick="toggleMobile()"><i class="fa fa-bars"></i></button>
      <ul class="nav-list" id="nav-list">
        <li class="nav-item"><a href="/" class="nav-link active"><i class="fa fa-house" style="font-size:12px"></i> الرئيسية</a></li>
        <li class="nav-item">
          <a href="/storefront/products" class="nav-link">المنتجات <i class="fa fa-chevron-down"></i></a>
          <div class="mega-menu" id="cat-menu">
            <a href="/storefront/products"><i class="fa fa-border-all"></i> كل المنتجات</a>
            <div class="sep"></div>
          </div>
        </li>
        <li class="nav-item"><a href="/storefront/offers" class="nav-link">العروض <i class="fa fa-tag" style="color:var(--amber);font-size:11px"></i></a></li>
        <li class="nav-item"><a href="/storefront/contact" class="nav-link">اتصل بنا</a></li>
      </ul>
    </div>
  </nav>

  <!-- MOBILE NAV -->
  <div class="mobile-nav" id="mobile-nav">
    <a href="/"><i class="fa fa-house"></i> الرئيسية</a>
    <a href="/storefront/products"><i class="fa fa-border-all"></i> المنتجات</a>
    <a href="/storefront/offers"><i class="fa fa-tag"></i> العروض</a>
    <a href="/storefront/contact"><i class="fa fa-phone"></i> اتصل بنا</a>
  </div>
</header>

<!-- ═══ HERO ═══ -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-dots"></div>
  <div class="wrap">
    <div class="hero-inner">
      <!-- Text -->
      <div class="hero-text">
        <div class="hero-pill">طباعة احترافية &amp; جودة مضمونة</div>
        <h1 class="hero-h1">اطبع بصمتك<br>على كل <span class="accent" id="hero-accent">منتج</span></h1>
        <p class="hero-sub">دروع تكريمية، لافتات، بطاقات، ملصقات، هدايا دعائية — مطبوعة بشعارك وألوانك بأعلى جودة.</p>
        <div class="hero-btns">
          <a href="/storefront/products" class="btn-red"><i class="fa fa-bag-shopping"></i> تسوق الآن</a>
          <a href="/storefront/contact" class="btn-ghost"><i class="fa fa-phone"></i> تواصل معنا</a>
        </div>
        <div class="hero-stats">
          <div class="hero-stat"><strong>+500</strong><span>عميل راضٍ</span></div>
          <div class="hero-stat"><strong>24h</strong><span>توصيل سريع</span></div>
          <div class="hero-stat"><strong>100%</strong><span>جودة مضمونة</span></div>
        </div>
      </div>
      <!-- Visual grid -->
      <div class="hero-grid" id="hero-grid">
        <div class="hero-cell span-h" style="background:linear-gradient(135deg,rgba(16,180,106,.18),rgba(15,21,18,.6))">
          <div class="hero-cell-ph"><i class="fa fa-trophy"></i><span>دروع تكريمية</span></div>
          <span class="hero-cell-label">دروع</span>
        </div>
        <div class="hero-cell" style="background:linear-gradient(135deg,rgba(245,158,11,.12),rgba(15,21,18,.6))">
          <div class="hero-cell-ph"><i class="fa fa-rectangle-ad"></i><span>شوادر ولافتات</span></div>
          <span class="hero-cell-label">لافتات</span>
        </div>
        <div class="hero-cell" style="background:linear-gradient(135deg,rgba(59,130,246,.12),rgba(15,21,18,.6))">
          <div class="hero-cell-ph"><i class="fa fa-id-card"></i><span>بطاقات وكروت</span></div>
          <span class="hero-cell-label">بطاقات</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ TRUST BAR ═══ -->
<div class="trust-bar">
  <div class="wrap">
    <div class="trust-list">
      <div class="trust-item"><div class="trust-icon r"><i class="fa fa-truck-fast"></i></div><div class="trust-text"><strong>توصيل سريع</strong><span>خلال 24–48 ساعة</span></div></div>
      <div class="trust-item"><div class="trust-icon a"><i class="fa fa-award"></i></div><div class="trust-text"><strong>جودة مضمونة</strong><span>طباعة عالية الدقة</span></div></div>
      <div class="trust-item"><div class="trust-icon b"><i class="fa fa-print"></i></div><div class="trust-text"><strong>طباعة احترافية</strong><span>أحدث تقنيات الطباعة</span></div></div>
      <div class="trust-item"><div class="trust-icon g"><i class="fa fa-headset"></i></div><div class="trust-text"><strong>دعم متواصل</strong><span>نرد في دقائق</span></div></div>
    </div>
  </div>
</div>

<!-- ═══ CATEGORIES ═══ -->
<section class="sec reveal" id="cat-sec">
  <div class="wrap">
    <div class="sec-head">
      <div><h2 class="sec-title">تصفح التصنيفات</h2><p class="sec-sub">اكتشف تشكيلتنا الكاملة من المنتجات المطبوعة</p></div>
      <a href="/storefront/products" class="view-all"><i class="fa fa-arrow-left"></i> عرض الكل</a>
    </div>

    <div class="skel-grid" id="cat-skel">
      <div class="skel-card skel skel-sq"></div><div class="skel-card skel skel-sq"></div>
      <div class="skel-card skel skel-sq"></div><div class="skel-card skel skel-sq"></div>
    </div>
    <div class="cat-grid" id="cat-grid" style="display:none"></div>
    <div class="empty" id="cat-empty" style="display:none"><i class="fa fa-folder-open"></i><p>لا توجد تصنيفات بعد — أضفها من لوحة التحكم</p></div>
  </div>
</section>

<!-- ═══ LATEST PRODUCTS ═══ -->
<section class="sec sec-white reveal" id="prod-sec">
  <div class="wrap">
    <div class="sec-head">
      <div><h2 class="sec-title" id="prod-title">أحدث المنتجات</h2><p class="sec-sub" id="prod-sub">وصل حديثاً إلى متجرنا</p></div>
      <a href="/storefront/products" class="view-all"><i class="fa fa-arrow-left"></i> عرض الكل</a>
    </div>

    <div class="skel-pgrid" id="prod-skel">
      <div class="skel-card"><div class="skel skel-psq"></div><div class="skel skel-line"></div><div class="skel skel-line-sm"></div><div class="skel skel-btn"></div></div>
      <div class="skel-card"><div class="skel skel-psq"></div><div class="skel skel-line"></div><div class="skel skel-line-sm"></div><div class="skel skel-btn"></div></div>
      <div class="skel-card"><div class="skel skel-psq"></div><div class="skel skel-line"></div><div class="skel skel-line-sm"></div><div class="skel skel-btn"></div></div>
      <div class="skel-card"><div class="skel skel-psq"></div><div class="skel skel-line"></div><div class="skel skel-line-sm"></div><div class="skel skel-btn"></div></div>
      <div class="skel-card"><div class="skel skel-psq"></div><div class="skel skel-line"></div><div class="skel skel-line-sm"></div><div class="skel skel-btn"></div></div>
    </div>
    <div class="prod-grid" id="prod-grid" style="display:none"></div>
    <div class="empty" id="prod-empty" style="display:none"><i class="fa fa-box-open"></i><p>لا توجد منتجات بعد — أضفها من لوحة التحكم</p></div>
  </div>
</section>

<!-- ═══ FLASH SALE ═══ -->
<section class="sec reveal" id="flash-sec">
  <div class="wrap">
    <div class="flash-banner">
      <div class="flash-bg"></div>
      <div class="flash-inner">
        <div class="flash-left">
          <div class="flash-tag"><i class="fa fa-bolt"></i> <span id="flash-tag-txt">عرض محدود الوقت</span></div>
          <h2 class="flash-title" id="flash-title">باقة الأعمال الصغيرة<br>ابدأ بأقل تكلفة</h2>
          <p class="flash-sub" id="flash-sub">كاسات + أكياس + بطاقات عمل — مطبوعة بشعارك وهويتك البصرية</p>
          <div class="timer-row" id="flash-timer-row">
            <span class="timer-label-text">ينتهي خلال:</span>
            <div class="t-block"><span class="t-num" id="th">00</span><div class="t-lbl">ساعة</div></div>
            <span class="t-sep">:</span>
            <div class="t-block"><span class="t-num" id="tm">00</span><div class="t-lbl">دقيقة</div></div>
            <span class="t-sep">:</span>
            <div class="t-block"><span class="t-num" id="ts">00</span><div class="t-lbl">ثانية</div></div>
          </div>
          <a href="/storefront/offers" class="btn-red" style="width:fit-content"><i class="fa fa-tag"></i> <span id="flash-btn-txt">اطلب الباقة الآن</span></a>
        </div>
        <div class="flash-right" id="flash-prods">
          <!-- يُملأ من API أو placeholder -->
          <div class="flash-prod-card"><div class="flash-prod-img"><i class="fa fa-mug-hot"></i></div><div class="flash-prod-name">كوب مطبوع بالشعار</div><div><span class="flash-prod-was">35 ₪</span><span class="flash-prod-price">28 ₪</span></div></div>
          <div class="flash-prod-card"><div class="flash-prod-img"><i class="fa fa-bag-shopping"></i></div><div class="flash-prod-name">كيس هدايا مطبوع</div><div><span class="flash-prod-was">20 ₪</span><span class="flash-prod-price">15 ₪</span></div></div>
          <div class="flash-prod-card"><div class="flash-prod-img"><i class="fa fa-id-card"></i></div><div class="flash-prod-name">بطاقة عمل احترافية</div><div><span class="flash-prod-was">50 ₪</span><span class="flash-prod-price">38 ₪</span></div></div>
          <div class="flash-prod-card"><div class="flash-prod-img"><i class="fa fa-scroll"></i></div><div class="flash-prod-name">لافتة رول أب</div><div><span class="flash-prod-was">120 ₪</span><span class="flash-prod-price">95 ₪</span></div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ DISCOUNTED ═══ -->
<section class="sec sec-white reveal" id="disc-sec" style="display:none">
  <div class="wrap">
    <div class="sec-head">
      <div>
        <h2 class="sec-title">
          <span style="background:var(--amber);color:var(--navy);font-size:12px;padding:2px 9px;border-radius:50px;margin-right:6px;font-weight:900">خصم</span>
          منتجات مخفّضة
        </h2>
        <p class="sec-sub">عروض حصرية لفترة محدودة</p>
      </div>
      <a href="/storefront/products?type=discounted" class="view-all"><i class="fa fa-arrow-left"></i> عرض الكل</a>
    </div>
    <div class="prod-grid" id="disc-grid"></div>
  </div>
</section>

<!-- ═══ OFFER STRIP ═══ -->
<section class="sec reveal">
  <div class="wrap">
    <div class="offer-strip">
      <div class="offer-card red-card">
        <div class="offer-card-icon">🏆</div>
        <div class="offer-card-text">
          <strong>هدايا الشركات والمؤسسات</strong>
          <p>دروع تكريمية، لافتات، مطبوعات بهويتك</p>
          <a href="/storefront/products" class="btn-white"><i class="fa fa-arrow-left"></i> تسوق الآن</a>
        </div>
      </div>
      <div class="offer-card navy-card">
        <div class="offer-card-icon">🖨️</div>
        <div class="offer-card-text">
          <strong>طلبات الجملة</strong>
          <p>خصومات تصل إلى 30% للكميات الكبيرة</p>
          <a href="/storefront/contact" class="btn-white"><i class="fa fa-phone"></i> تواصل معنا</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ CLIENTS ═══ (تُضاف من لوحة الإدارة ← التسويق ← عملاؤنا يثقون بنا) -->
<div class="clients-sec reveal" id="clients-sec" style="display:none">
  <div class="wrap" style="margin-bottom:18px">
    <div class="sec-head" style="margin-bottom:0">
      <h2 class="sec-title" style="font-size:18px">عملاؤنا يثقون بنا</h2>
    </div>
  </div>
  <div class="marquee-wrap">
    <div class="marquee-track" id="marquee"></div>
  </div>
</div>

<!-- ═══ FOOTER ═══ -->
<footer class="footer">
  <div class="wrap">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="f-logo">
          <div class="f-logo-mark" id="f-logo-mark">ع</div>
          <span class="f-logo-name" id="f-store-name">ايليت دعاية</span>
        </div>
        <p class="f-desc" id="f-desc">متخصصون في خدمات الطباعة والتصميم — منتجات دعائية احترافية مطبوعة بأعلى جودة وأسعار تنافسية.</p>
        <div class="f-social" id="f-social">
          <!-- تُملأ من API (social_media_link) + WhatsApp -->
          <a href="#" class="f-soc" id="f-wa" style="display:none"><i class="fab fa-whatsapp"></i></a>
        </div>
      </div>

      <div class="f-col">
        <h4>التصنيفات</h4>
        <ul class="f-links" id="f-cats">
          <li><a href="/storefront/products">كل المنتجات</a></li>
        </ul>
      </div>

      <div class="f-col">
        <h4>روابط مفيدة</h4>
        <ul class="f-links">
          <li><a href="/">الرئيسية</a></li>
          <li><a href="/storefront/offers">العروض</a></li>
          <li><a href="/storefront/orders/track">تتبع الطلب</a></li>
          <li><a href="/storefront/account">حسابي</a></li>
        </ul>
      </div>

      <div class="f-col">
        <h4>تواصل معنا</h4>
        <div class="f-contact">
          <div class="f-ci"><i class="fa fa-map-marker-alt"></i><span id="f-addr">—</span></div>
          <div class="f-ci"><i class="fa fa-phone"></i><span id="f-phone">—</span></div>
          <div class="f-ci"><i class="fa fa-envelope"></i><span id="f-email">—</span></div>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <span id="f-copy">© 2025 ايليت دعاية — جميع الحقوق محفوظة</span>
      <div style="display:flex;gap:20px">
        <a href="/storefront/privacy">سياسة الخصوصية</a>
        <a href="/storefront/terms">الشروط والأحكام</a>
      </div>
    </div>
  </div>
</footer>

<!-- CART -->
<div class="cart-veil" id="cart-veil" onclick="closeCart()"></div>
<div class="cart-drawer" id="cart-drawer">
  <div class="cart-hd">
    <span class="cart-hd-title">🛒 سلة التسوق</span>
    <button class="cart-x" onclick="closeCart()"><i class="fa fa-xmark"></i></button>
  </div>
  <div class="cart-bd" id="cart-bd">
    <div class="cart-empty-msg" id="cart-empty-msg">
      <i class="fa fa-cart-shopping"></i>
      <p>السلة فارغة</p>
      <a href="/storefront/products" class="btn-red" style="font-size:13px;padding:9px 20px">تسوق الآن</a>
    </div>
  </div>
  <div class="cart-ft" id="cart-ft" style="display:none">
    <div class="cart-total-row"><span class="label">المجموع:</span><span class="val" id="cart-tot">0 ₪</span></div>
    <a href="/storefront/checkout" class="btn-red" style="width:100%;justify-content:center;display:flex">
      <i class="fa fa-credit-card"></i> إتمام الشراء
    </a>
  </div>
</div>

<!-- TOASTS -->
<div class="toasts" id="toasts"></div>

<script>
/* ══ CONFIG ══ */
const API = window.location.origin + '/api/v1';
let CUR = '₪';

/* ══ SCROLL REVEAL ══ */
const ro = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); ro.unobserve(e.target); }});
}, { threshold: .12 });
document.querySelectorAll('.reveal').forEach(el => ro.observe(el));

/* ══ HERO ACCENT CYCLE ══ */
const accents = ['منتج','كوب','لافتة','كيس','ستيكر'];
let ai = 0;
setInterval(() => {
  const el = document.getElementById('hero-accent');
  el.style.opacity = '0'; el.style.transform = 'translateY(-8px)';
  setTimeout(() => { ai = (ai+1)%accents.length; el.textContent = accents[ai]; el.style.opacity='1'; el.style.transform='none'; }, 300);
}, 2600);
document.getElementById('hero-accent').style.transition = 'opacity .3s, transform .3s';

/* ══ COUNTDOWN ══ */
const end = (() => { const d = new Date(); d.setHours(d.getHours()+23,d.getMinutes()+47); return d; })();
setInterval(() => {
  const d = end - Date.now(); if (d<0) return;
  document.getElementById('th').textContent = String(Math.floor(d/3.6e6)).padStart(2,'0');
  document.getElementById('tm').textContent = String(Math.floor(d%3.6e6/6e4)).padStart(2,'0');
  document.getElementById('ts').textContent = String(Math.floor(d%6e4/1e3)).padStart(2,'0');
}, 1000);

/* ══ MOBILE MENU ══ */
function toggleMobile() { document.getElementById('mobile-nav').classList.toggle('open'); }

/* ══ SEARCH ══ */
function doSearch() {
  const q = document.getElementById('search-q').value.trim();
  if (q) location.href = `/storefront/products?q=${encodeURIComponent(q)}`;
}
document.getElementById('search-q').addEventListener('keydown', e => e.key==='Enter' && doSearch());

/* ══ CART ══ */
let cart = JSON.parse(localStorage.getItem('f_cart')||'[]');
const saveCart = () => { localStorage.setItem('f_cart', JSON.stringify(cart)); renderCart(); };

function renderCart() {
  const badge = document.getElementById('cart-badge');
  const bd = document.getElementById('cart-bd');
  const ft = document.getElementById('cart-ft');
  const tot = document.getElementById('cart-tot');
  const count = cart.reduce((s,i) => s+i.qty, 0);
  badge.textContent = count;
  badge.classList.toggle('off', count===0);
  if (!cart.length) {
    bd.innerHTML = `<div class="cart-empty-msg"><i class="fa fa-cart-shopping"></i><p>السلة فارغة</p><a href="/storefront/products" class="btn-red" style="font-size:13px;padding:9px 20px">تسوق الآن</a></div>`;
    ft.style.display = 'none';
  } else {
    bd.innerHTML = cart.map((item,i) => `
      <div class="cart-item">
        <div class="cart-item-img">${item.img ? `<img src="${item.img}" alt="">` : '<i class="fa fa-box"></i>'}</div>
        <div class="cart-item-info">
          <div class="cart-item-name">${item.name}</div>
          <div class="cart-item-price">${(parseFloat(item.price)*item.qty).toFixed(2)} ${CUR}</div>
        </div>
        <div class="qty-ctrl">
          <button class="qty-btn" onclick="qtyChange(${i},-1)">−</button>
          <span class="qty-val">${item.qty}</span>
          <button class="qty-btn" onclick="qtyChange(${i},1)">+</button>
        </div>
        <button class="cart-item-del" onclick="cartRemove(${i})"><i class="fa fa-xmark"></i></button>
      </div>`).join('');
    const total = cart.reduce((s,i) => s+parseFloat(i.price)*i.qty, 0);
    tot.textContent = total.toFixed(2)+' '+CUR;
    ft.style.display = 'block';
  }
}
function addToCart(id, name, price, img) {
  const ex = cart.find(i=>i.id===id);
  ex ? ex.qty++ : cart.push({id,name,price,img,qty:1});
  saveCart(); toast('أُضيف للسلة ✓');
}
function qtyChange(i,d) { cart[i].qty+=d; if(cart[i].qty<=0) cart.splice(i,1); saveCart(); }
function cartRemove(i) { cart.splice(i,1); saveCart(); }
function openCart()  { document.getElementById('cart-veil').classList.add('on'); document.getElementById('cart-drawer').classList.add('on'); }
function closeCart() { document.getElementById('cart-veil').classList.remove('on'); document.getElementById('cart-drawer').classList.remove('on'); }

/* ══ TOAST ══ */
function toast(msg, type='ok') {
  const tc = document.getElementById('toasts');
  const t = document.createElement('div');
  t.className = `toast ${type}`;
  t.innerHTML = `<i class="fa fa-${type==='ok'?'circle-check':'circle-xmark'}"></i> ${msg}`;
  tc.appendChild(t);
  setTimeout(()=>t.remove(), 3100);
}

/* ══ PRODUCT ICON/STYLE MAP ══ */
const prodIconMap = [
  ['درع','دروع','كأس','كريستال','نحاس','ألومنيوم','خشب','زجاج','لوح'],
  ['fa-trophy','#134e2a','#10b46a'],
  ['لافت','شادر','رول','إكس','بانر'],
  ['fa-rectangle-ad','#1e3a5f','#60a5fa'],
  ['بطاق','كرت','دعوة','تهنئة'],
  ['fa-id-card','#4c1d95','#a78bfa'],
  ['ليبل','ملصق','ستيكر'],
  ['fa-tag','#7c2d12','#fb923c'],
  ['كوب','كاس','مج'],
  ['fa-mug-hot','#78350f','#fbbf24'],
  ['ختم','طغر'],
  ['fa-stamp','#3b0764','#d8b4fe'],
  ['هدي','هدية'],
  ['fa-gift','#134e2a','#34d399'],
];
function getProdStyle(name) {
  const n = name || '';
  for (let i = 0; i < prodIconMap.length; i+=2) {
    if (prodIconMap[i].some(k => n.includes(k))) return {icon: prodIconMap[i+1][0], grad: `linear-gradient(145deg,${prodIconMap[i+1][1]},#0f1512)`, accent: prodIconMap[i+1][2]};
  }
  return {icon:'fa-box-open', grad:'linear-gradient(145deg,#1a2e1f,#0f1512)', accent:'#10b46a'};
}
// keep legacy
function getProdIcon(name) { return getProdStyle(name).icon; }

/* ══ PRODUCT CARD ══ */
function prodCard(p, imgBase) {
  const rawImgs = Array.isArray(p.image_fullpath) ? p.image_fullpath : (p.image_fullpath ? [p.image_fullpath] : []);
  const hasRealImg = rawImgs.length && !rawImgs[0].includes('img2.jpg');
  const imgSrc = hasRealImg ? rawImgs[0] : null;

  const price = parseFloat(p.price||p.unit_price||0);
  const disc  = parseFloat(p.discount||0);
  const now   = disc>0 ? (price-price*disc/100).toFixed(2) : price.toFixed(2);
  const rating = Array.isArray(p.rating) && p.rating.length
    ? (p.rating.reduce((s,r)=>s+parseFloat(r.rating||0),0)/p.rating.length).toFixed(1)
    : (3.5+Math.random()*1.5).toFixed(1);
  const rNum  = Math.round(parseFloat(rating));
  const stars = '★'.repeat(Math.min(rNum,5)) + '☆'.repeat(Math.max(0,5-rNum));
  const safeName = p.name.replace(/'/g,"\\'").replace(/"/g,'&quot;');
  const ps = getProdStyle(p.name);

  const imgHtml = imgSrc
    ? `<img src="${imgSrc}" alt="${p.name}" loading="lazy" style="width:100%;height:100%;object-fit:cover" onerror="this.remove()">`
    : '';

  return `
  <div class="prod-card">
    <a href="/storefront/product/${p.id}">
      <div class="prod-img-wrap" style="background:${ps.grad}">
        ${imgHtml}
        <div class="prod-icon-bg" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;${imgSrc?'opacity:0':''}">
          <i class="fa ${ps.icon}" style="font-size:56px;color:${ps.accent};opacity:.55"></i>
        </div>
        ${disc>0 ? `<span class="badge-sale">-${Math.round(disc)}%</span>` : ''}
        <div class="prod-actions">
          <button class="pa-btn pa-cart" onclick="event.preventDefault();addToCart(${p.id},'${safeName}',${now},'${imgSrc||''}')">
            <i class="fa fa-cart-plus"></i> أضف للسلة
          </button>
          <button class="pa-btn pa-wish" onclick="event.preventDefault();toggleWish(${p.id})" title="المفضلة">
            <i class="fa fa-heart"></i>
          </button>
        </div>
      </div>
    </a>
    <div class="prod-body">
      <h3 class="prod-name"><a href="/storefront/product/${p.id}">${p.name}</a></h3>
      <div class="stars">${stars} <span>(${Math.floor(Math.random()*40+5)})</span></div>
      <div class="prod-price">
        <span class="price-now">${now} ${CUR}</span>
        ${disc>0 ? `<span class="price-was">${price.toFixed(2)} ${CUR}</span>` : ''}
      </div>
    </div>
  </div>`;
}

/* ══ CATEGORY CARD ══ */
let _catIdx = 0;
function catCard(c, imgBase) {
  const style = getCatStyle(_catIdx++);
  const icon = getCatIcon(c.name);
  const rawImg = c.image_fullpath || (c.image && c.image !== 'def.png' ? `${imgBase}/${c.image}` : null);
  const hasRealImg = rawImg && !rawImg.includes('img2.jpg');

  return `
  <a href="/storefront/products?category=${c.id}" class="cat-card">
    <div class="cat-bg" style="background:${style.grad};width:100%;height:100%;position:absolute;inset:0;">
      ${hasRealImg ? `<img src="${rawImg}" alt="${c.name}" loading="lazy" style="width:100%;height:100%;object-fit:cover;opacity:.45" onerror="this.remove()">` : ''}
      <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
        <i class="fa ${icon}" style="font-size:52px;color:${style.accent};opacity:.7"></i>
      </div>
    </div>
    <div class="cat-overlay">
      <div class="cat-name">${c.name}</div>
      ${c.products_count>0 ? `<div class="cat-count">${c.products_count} منتج</div>` : ''}
    </div>
    <div class="cat-arrow"><i class="fa fa-arrow-left"></i></div>
  </a>`;
}

/* ══ WISHLIST ══ */
function toggleWish(id) {
  let wl = JSON.parse(localStorage.getItem('f_wl')||'[]');
  const i = wl.indexOf(id);
  i>-1 ? (wl.splice(i,1), toast('أُزيل من المفضلة','err')) : (wl.push(id), toast('أُضيف للمفضلة ♥'));
  localStorage.setItem('f_wl', JSON.stringify(wl));
}

/* ══ CATEGORY STYLE MAP ══ */
const catStyles = [
  { icon:'fa-trophy',       grad:'linear-gradient(145deg,#134e2a,#1e7a43)', accent:'#10b46a' },
  { icon:'fa-rectangle-ad', grad:'linear-gradient(145deg,#1e3a5f,#2563eb)', accent:'#60a5fa' },
  { icon:'fa-id-card',      grad:'linear-gradient(145deg,#4c1d95,#7c3aed)', accent:'#a78bfa' },
  { icon:'fa-tag',          grad:'linear-gradient(145deg,#7c2d12,#ea580c)', accent:'#fb923c' },
  { icon:'fa-print',        grad:'linear-gradient(145deg,#0f1512,#1a3527)', accent:'#10b46a' },
  { icon:'fa-mug-hot',      grad:'linear-gradient(145deg,#78350f,#d97706)', accent:'#fbbf24' },
  { icon:'fa-scroll',       grad:'linear-gradient(145deg,#1e3a5f,#0e7490)', accent:'#22d3ee' },
  { icon:'fa-stamp',        grad:'linear-gradient(145deg,#3b0764,#9333ea)', accent:'#d8b4fe' },
];
const catIcons = {
  'درع': 'fa-trophy', 'دروع': 'fa-trophy', 'لافت': 'fa-rectangle-ad', 'شادر': 'fa-rectangle-ad',
  'بطاق': 'fa-id-card', 'كرت': 'fa-id-card', 'ليبل': 'fa-tag', 'ملصق': 'fa-tag',
  'كوب': 'fa-mug-hot', 'كاس': 'fa-mug-hot', 'مج': 'fa-mug-hot',
  'كيس': 'fa-bag-shopping', 'حقيب': 'fa-bag-shopping',
  'تيشرت': 'fa-shirt', 'ملابس': 'fa-shirt',
  'قلم': 'fa-pen', 'دفتر': 'fa-book',
  'ختم': 'fa-stamp', 'هدي': 'fa-gift', 'هدية': 'fa-gift',
  'رول': 'fa-scroll', 'إكس': 'fa-scroll',
};
function getCatIcon(name) {
  for (const [k,v] of Object.entries(catIcons)) if (name && name.includes(k)) return v;
  return 'fa-print';
}
function getCatStyle(idx) { return catStyles[idx % catStyles.length]; }

/* ══ LOAD DATA ══ */
async function init() {
  // Fetch config ONCE, reuse everywhere
  let cfg = {};
  try {
    cfg = await fetch(`${API}/config`).then(r=>r.json());
    const name = cfg.ecommerce_name || cfg.business_name || 'متجرنا';
    if(cfg.currency_symbol) CUR = cfg.currency_symbol;

    // Page title & store name
    document.getElementById('store-name').textContent = name;
    document.getElementById('f-store-name').textContent = name;
    document.getElementById('f-copy').textContent = `© ${new Date().getFullYear()} ${name} — جميع الحقوق محفوظة`;
    document.getElementById('page-title').textContent = name + ' — طباعة وتصميم';
    document.title = name + ' — طباعة وتصميم';

    // Logo — hollow (outline border, transparent bg)
    const logoUrl = cfg.logo_full_url || '';
    if (logoUrl) {
      const mkImg = url => `<img src="${url}" alt="${name}" style="width:100%;height:100%;object-fit:contain" onerror="this.parentNode.textContent='${name.charAt(0)}'">`;
      document.getElementById('logo-mark').innerHTML = mkImg(logoUrl);
      document.getElementById('f-logo-mark').innerHTML = mkImg(logoUrl);
    } else {
      // Filled letter fallback
      const letter = name.charAt(0);
      const svgMark = `<svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><text x="50%" y="73%" text-anchor="middle" fill="#10b46a" font-size="27" font-weight="900" font-family="Cairo,sans-serif">${letter}</text></svg>`;
      document.getElementById('logo-mark').innerHTML = svgMark;
      document.getElementById('f-logo-mark').innerHTML = svgMark;
    }

    // Phone
    const ph = cfg.ecommerce_phone || cfg.phone || '';
    if (ph) {
      document.getElementById('f-phone').textContent = ph;
      document.getElementById('tb-contact').innerHTML += `<a href="tel:${ph}"><i class="fa fa-phone"></i>${ph}</a>`;
      document.getElementById('topbar').classList.add('visible');
    }
    // Email
    const em = cfg.ecommerce_email || cfg.email || '';
    if (em) {
      document.getElementById('f-email').textContent = em;
      document.getElementById('tb-contact').innerHTML += `<a href="mailto:${em}"><i class="fa fa-envelope"></i>${em}</a>`;
      document.getElementById('topbar').classList.add('visible');
    }
    // Address
    const addr = cfg.ecommerce_address || cfg.address || '';
    if (addr) document.getElementById('f-addr').textContent = addr;

    // WhatsApp
    if (cfg.whatsapp?.status && cfg.whatsapp?.number) {
      const wa = `https://wa.me/${cfg.whatsapp.number}`;
      const btn = document.getElementById('wa-btn'); btn.href=wa; btn.style.display='flex';
      const fw  = document.getElementById('f-wa');  fw.href=wa;  fw.style.display='flex';
    }

    // Social Media from admin settings
    const socialIconMap = {
      'facebook': 'fab fa-facebook-f',
      'instagram': 'fab fa-instagram',
      'twitter': 'fab fa-x-twitter',
      'tiktok': 'fab fa-tiktok',
      'youtube': 'fab fa-youtube',
      'snapchat': 'fab fa-snapchat',
      'linkedin': 'fab fa-linkedin-in',
      'telegram': 'fab fa-telegram',
      'whatsapp': 'fab fa-whatsapp',
      'pinterest': 'fab fa-pinterest',
    };
    const socialContainer = document.getElementById('f-social');
    if (Array.isArray(cfg.social_media_link) && cfg.social_media_link.length) {
      const links = cfg.social_media_link
        .filter(s => s.link && s.status != 0)
        .map(s => {
          const nm = (s.name||'').toLowerCase();
          const iconClass = Object.entries(socialIconMap).find(([k]) => nm.includes(k))?.[1] || 'fab fa-globe';
          return `<a href="${s.link}" target="_blank" rel="noopener" class="f-soc" title="${s.name||''}"><i class="${iconClass}"></i></a>`;
        }).join('');
      if (links) {
        // Insert before WhatsApp button
        const wa = document.getElementById('f-wa');
        wa.insertAdjacentHTML('beforebegin', links);
      }
    }
  } catch(e){ console.warn('Config', e); }

  const pBase = cfg.base_urls?.product_image_url  || '';
  const cBase = cfg.base_urls?.category_image_url || '';

  // Check for category filter in URL
  const urlParams = new URLSearchParams(window.location.search);
  const filterCatId = urlParams.get('category');

  // Parallel load
  const fetchProds = filterCatId
    ? fetch(`${API}/categories/products/${filterCatId}`).then(r=>r.json())
    : fetch(`${API}/products/latest?limit=10`).then(r=>r.json());

  const [catsRes, prodsRes, discRes, flashRes, heroGridRes, clientsRes] = await Promise.allSettled([
    fetch(`${API}/categories`).then(r=>r.json()),
    fetchProds,
    fetch(`${API}/products/discounted?limit=5`).then(r=>r.json()),
    fetch(`${API}/flash-sale?limit=4`).then(r=>r.json()),
    fetch(`${API}/banners?placement=hero_grid`).then(r=>r.json()),
    fetch(`${API}/clients`).then(r=>r.json()),
  ]);

  // ── CATEGORIES ──
  document.getElementById('cat-skel').style.display = 'none';
  let allCats = [];
  if (catsRes.status === 'fulfilled') {
    const data = catsRes.value;
    allCats = Array.isArray(data) ? data : (data.value||data.categories||[]);
    if (!allCats.length) {
      document.getElementById('cat-empty').style.display = 'block';
    } else {
      const grid = document.getElementById('cat-grid');
      grid.style.display = 'grid';
      _catIdx = 0;
      grid.innerHTML = allCats.slice(0,8).map(c=>catCard(c,cBase)).join('');
      // Nav dropdown
      document.getElementById('cat-menu').innerHTML =
        `<a href="/storefront/products"><i class="fa fa-border-all"></i> كل المنتجات</a><div class="sep"></div>`
        + allCats.map(c=>`<a href="/storefront/products?category=${c.id}"><i class="fa ${getCatIcon(c.name)}"></i>${c.name}</a>`).join('');
      // Footer categories
      document.getElementById('f-cats').innerHTML =
        allCats.map(c=>`<li><a href="/storefront/products?category=${c.id}">${c.name}</a></li>`).join('');
      // Mobile nav
      allCats.slice(0,5).forEach(c => {
        const a = document.createElement('a');
        a.href = `/storefront/products?category=${c.id}`;
        a.innerHTML = `<i class="fa ${getCatIcon(c.name)}"></i>${c.name}`;
        document.getElementById('mobile-nav').appendChild(a);
      });
    }
  } else {
    document.getElementById('cat-empty').style.display = 'block';
    console.warn('Cats', catsRes.reason);
  }

  // ── PRODUCTS (latest or filtered by category) ──
  document.getElementById('prod-skel').style.display = 'none';
  if (prodsRes.status === 'fulfilled') {
    const data = prodsRes.value;
    const list = Array.isArray(data) ? data : (data.products||data.value||[]);

    // Update section heading if filtered
    if (filterCatId) {
      const cat = allCats.find(c => String(c.id) === String(filterCatId));
      if (cat) {
        document.getElementById('prod-title').innerHTML =
          `<span style="font-size:13px;font-weight:600;color:var(--text-2)">التصنيف:</span> ${cat.name}`;
        document.getElementById('prod-sub').textContent = list.length ? `${list.length} منتج` : 'لا توجد منتجات في هذا التصنيف';
      }
      // scroll to products section
      setTimeout(() => document.getElementById('prod-sec').scrollIntoView({behavior:'smooth', block:'start'}), 400);
    }

    if (!list.length) {
      document.getElementById('prod-empty').style.display = 'block';
    } else {
      const grid = document.getElementById('prod-grid');
      grid.style.display = 'grid';
      grid.innerHTML = list.map(p=>prodCard(p,pBase)).join('');
    }
  } else {
    document.getElementById('prod-empty').style.display = 'block';
    console.warn('Prods', prodsRes.reason);
  }

  // ── DISCOUNTED (hide when category filter active) ──
  if (!filterCatId && discRes.status === 'fulfilled') {
    const data = discRes.value;
    const list = Array.isArray(data) ? data : (data.products||data.value||[]);
    if (list.length) {
      document.getElementById('disc-sec').style.display = 'block';
      document.getElementById('disc-grid').innerHTML = list.map(p=>prodCard(p,pBase)).join('');
    }
  }

  // ── FLASH SALE from API ──
  if (flashRes.status === 'fulfilled') {
    const fd = flashRes.value;
    const flashSale = fd.flash_sale;
    const flashProds = Array.isArray(fd.products) ? fd.products : [];
    if (flashSale && flashProds.length) {
      // Update title/subtitle from flash sale data
      if (flashSale.title) document.getElementById('flash-title').innerHTML = flashSale.title;
      if (flashSale.note)  document.getElementById('flash-sub').textContent   = flashSale.note;

      // Countdown: use flash_sale.end_date if available
      if (flashSale.end_date) {
        const endDate = new Date(flashSale.end_date.replace(' ','T'));
        clearInterval(window._flashTimer);
        window._flashTimer = setInterval(() => {
          const diff = endDate - new Date();
          if (diff <= 0) { clearInterval(window._flashTimer); return; }
          document.getElementById('th').textContent = String(Math.floor(diff/3.6e6)).padStart(2,'0');
          document.getElementById('tm').textContent = String(Math.floor(diff%3.6e6/6e4)).padStart(2,'0');
          document.getElementById('ts').textContent = String(Math.floor(diff%6e4/1e3)).padStart(2,'0');
        }, 1000);
      }

      // Render real flash sale products
      const fBase = cfg.base_urls?.flash_sale_image_url || pBase;
      document.getElementById('flash-prods').innerHTML = flashProds.slice(0,4).map(p => {
        const st = getProdStyle(p.name||'');
        const img = (p.image && pBase) ? `<img src="${pBase}/${p.image}" alt="${p.name||''}" style="width:100%;height:100%;object-fit:cover" onerror="this.style.display='none';this.nextSibling.style.display='flex'"><div class='flash-prod-img' style='display:none'><i class='fa ${st.icon}'></i></div>` : `<div class='flash-prod-img'><i class='fa ${st.icon}'></i></div>`;
        const price  = parseFloat(p.price||p.unit_price||0);
        const disAmt = parseFloat(p.discount||0);
        const disTyp = p.discount_type||'percent';
        const orig   = disAmt > 0 ? (disTyp==='percent' ? price/(1-disAmt/100) : price+disAmt) : null;
        return `<a href="/storefront/product/${p.id}" class="flash-prod-card" style="text-decoration:none">
          <div style="width:70px;height:70px;border-radius:10px;overflow:hidden;flex-shrink:0">${img}</div>
          <div class="flash-prod-name">${p.name||''}</div>
          <div>${orig?`<span class="flash-prod-was">${orig.toFixed(0)} ${CUR}</span>`:''}
          <span class="flash-prod-price">${price.toFixed(0)} ${CUR}</span></div>
        </a>`;
      }).join('');
    }
  }

  // ── HERO GRID BANNERS (تُدخل من لوحة الإدارة ← التسويق ← البانر) ──
  if (heroGridRes.status === 'fulfilled') {
    const heroBanners = Array.isArray(heroGridRes.value) ? heroGridRes.value : [];
    const cells = document.querySelectorAll('#hero-grid .hero-cell');
    heroBanners.slice(0, cells.length).forEach((b, i) => {
      const cell = cells[i];
      if (!cell || !b.image_fullpath) return;
      cell.style.backgroundImage = `url('${b.image_fullpath}')`;
      cell.style.backgroundSize = 'cover';
      cell.style.backgroundPosition = 'center';
      const ph = cell.querySelector('.hero-cell-ph');
      if (ph) ph.style.display = 'none';
      const label = cell.querySelector('.hero-cell-label');
      if (label && b.title) label.textContent = b.title;
      if (b.product_id) {
        cell.style.cursor = 'pointer';
        cell.onclick = () => location.href = `/storefront/product/${b.product_id}`;
      } else if (b.category_id) {
        cell.style.cursor = 'pointer';
        cell.onclick = () => location.href = `/storefront/products?category=${b.category_id}`;
      }
    });
  }

  // ── CLIENTS — عملاؤنا يثقون بنا (تُدخل من لوحة الإدارة ← التسويق) ──
  if (clientsRes.status === 'fulfilled') {
    const clients = Array.isArray(clientsRes.value) ? clientsRes.value : [];
    if (clients.length) {
      const chip = c => {
        const safeName = (c.name||'').replace(/</g,'&lt;');
        return c.logo_fullpath
          ? `<div class="client-chip client-chip-logo" title="${safeName}"><img src="${c.logo_fullpath}" alt="${safeName}" style="height:28px;max-width:120px;object-fit:contain" onerror="this.parentNode.textContent='${safeName}'"></div>`
          : `<div class="client-chip">${safeName}</div>`;
      };
      // تكرار القائمة مرتين لإنشاء تأثير تمرير سلس (marquee) بدون فجوة
      document.getElementById('marquee').innerHTML = clients.map(chip).join('') + clients.map(chip).join('');
      document.getElementById('clients-sec').style.display = 'block';
    }
  }
}

renderCart();
init();
</script>
</body>
</html>
