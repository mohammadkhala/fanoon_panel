<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title id="page-title">حسابي</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{
  --green:#10b46a;--green-d:#0c8f52;--green-10:rgba(16,180,106,.10);
  --navy:#0f1512;--surface:#f5f8f4;--white:#fff;--amber:#f59e0b;
  --border:#d6e8d9;--text:#0f1512;--text-2:#4a5e50;--text-3:#8fa895;
  --font:'IBM Plex Sans Arabic','Cairo',sans-serif;
  --r:14px;--sh:0 4px 20px rgba(15,21,18,.08);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--font);background:var(--surface);color:var(--text);direction:rtl;min-height:100vh;display:flex;flex-direction:column}
a{text-decoration:none;color:inherit}
.wrap{max-width:1100px;margin-inline:auto;padding-inline:20px}

/* ── Header ── */
.header{background:var(--white);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:200}
.hb{display:flex;align-items:center;gap:16px;padding:12px 0}
.logo{display:flex;align-items:center;gap:10px}
.logo-mark{width:120px;height:50px;background:#000;border:1.5px solid #222;border-radius:12px;display:flex;align-items:center;justify-content:center;overflow:hidden;padding:5px 10px}
.logo-mark img{width:100%;height:100%;object-fit:contain}
.logo-name{font-size:17px;font-weight:900;color:var(--navy)}
.logo-tag{font-size:11px;color:var(--text-3)}
.nav-links{margin-inline-start:auto;display:flex;align-items:center;gap:14px;flex-wrap:wrap}
.nav-links a{font-size:13px;font-weight:600;color:var(--text-2);display:flex;align-items:center;gap:5px;transition:color .2s}
.nav-links a:hover{color:var(--green)}
.btn-logout{background:transparent;border:1.5px solid #e74c3c;color:#e74c3c;border-radius:8px;padding:6px 14px;font-family:var(--font);font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:5px;transition:all .2s}
.btn-logout:hover{background:#e74c3c;color:#fff}

/* ── Loading / gate ── */
#gate{display:flex;flex-direction:column;align-items:center;justify-content:center;flex:1;padding:60px 20px;text-align:center}
#gate .g-icon{font-size:50px;color:var(--green);margin-bottom:16px}
#gate h2{font-size:22px;font-weight:900;color:var(--navy);margin-bottom:8px}
#gate p{color:var(--text-3);font-size:15px;margin-bottom:24px}
.btn-green{background:var(--green);color:#fff;border:none;border-radius:12px;padding:12px 28px;font-family:var(--font);font-size:15px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:background .2s;text-decoration:none}
.btn-green:hover{background:var(--green-d)}

/* ── Main layout ── */
#app{display:none;flex:1;padding:32px 0 60px}
.account-grid{display:grid;grid-template-columns:260px 1fr;gap:28px;align-items:start}

/* ── Sidebar ── */
.sidebar-card{background:var(--white);border:1.5px solid var(--border);border-radius:20px;overflow:hidden;box-shadow:var(--sh)}
.profile-top{background:var(--navy);padding:28px 24px;text-align:center}
.avatar{width:72px;height:72px;background:var(--green);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:900;color:#fff;margin:0 auto 12px}
.profile-name{font-size:17px;font-weight:800;color:#fff;margin-bottom:4px}
.profile-type{display:inline-block;background:rgba(16,180,106,.2);color:var(--green);font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px}
.sidebar-nav{padding:8px 0}
.snav-item{display:flex;align-items:center;gap:10px;padding:12px 20px;font-size:14px;font-weight:600;color:var(--text-2);cursor:pointer;transition:all .2s;border-right:3px solid transparent}
.snav-item:hover{background:var(--green-10);color:var(--green)}
.snav-item.active{background:var(--green-10);color:var(--green);border-right-color:var(--green)}
.snav-item i{width:18px;text-align:center}

/* ── Panels ── */
.panel{display:none}
.panel.show{display:block}
.section-card{background:var(--white);border:1.5px solid var(--border);border-radius:20px;padding:28px 28px;box-shadow:var(--sh);margin-bottom:20px}
.section-title{font-size:18px;font-weight:900;color:var(--navy);margin-bottom:20px;display:flex;align-items:center;gap:8px}
.section-title i{color:var(--green)}

/* Profile form */
.f2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.fgroup{margin-bottom:14px}
.flabel{display:block;font-size:13px;font-weight:600;color:var(--text-2);margin-bottom:5px}
.finput{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:var(--font);font-size:14px;color:var(--text);background:var(--surface);outline:none;transition:border-color .2s}
.finput:focus{border-color:var(--green);background:var(--white)}
.finput:read-only{cursor:default;color:var(--text-3)}
.save-btn{background:var(--green);color:#fff;border:none;border-radius:10px;padding:10px 24px;font-family:var(--font);font-size:14px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:background .2s}
.save-btn:hover{background:var(--green-d)}

/* Orders table */
.orders-empty{text-align:center;padding:48px 0;color:var(--text-3)}
.orders-empty i{font-size:40px;margin-bottom:12px;display:block}
.otable{width:100%;border-collapse:collapse;font-size:14px}
.otable th{padding:10px 12px;text-align:right;font-size:12px;font-weight:700;color:var(--text-3);border-bottom:2px solid var(--border);white-space:nowrap}
.otable td{padding:12px;border-bottom:1px solid var(--border);vertical-align:middle}
.otable tr:last-child td{border-bottom:none}
.otable tr:hover td{background:var(--surface)}
.obadge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.b-pending{background:rgba(245,158,11,.12);color:#d97706}
.b-confirmed,.b-processing,.b-out_for_delivery{background:var(--green-10);color:var(--green)}
.b-delivered{background:#d4f5e5;color:#0c8f52}
.b-canceled,.b-returned,.b-failed{background:rgba(231,76,60,.10);color:#e74c3c}
.track-link{color:var(--green);font-weight:600;font-size:13px;display:inline-flex;align-items:center;gap:4px}
.track-link:hover{color:var(--green-d)}

/* Loader skeleton */
.skel{background:linear-gradient(90deg,#eef2ee 25%,#dfe9df 50%,#eef2ee 75%);background-size:200% 100%;animation:sk 1.4s infinite;border-radius:8px;height:14px}
@keyframes sk{0%{background-position:200% 0}100%{background-position:-200% 0}}

/* Stats bar */
.stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px}
.stat-box{background:var(--white);border:1.5px solid var(--border);border-radius:16px;padding:18px 16px;text-align:center;box-shadow:var(--sh)}
.stat-num{font-size:26px;font-weight:900;color:var(--navy)}
.stat-label{font-size:12px;color:var(--text-3);margin-top:4px}

footer{background:var(--navy);color:rgba(255,255,255,.45);text-align:center;padding:18px 20px;font-size:13px}

@media(max-width:768px){
  .account-grid{grid-template-columns:1fr}
  .f2{grid-template-columns:1fr}
  .stats-row{grid-template-columns:1fr 1fr}
}
</style>
</head>
<body>

<header class="header">
  <div class="wrap">
    <div class="hb">
      <a href="/" class="logo">
        <div class="logo-mark" id="logo-mark">ع</div>
        <div>
          <div class="logo-name" id="store-name">المتجر</div>
          <div class="logo-tag">طباعة وتصميم</div>
        </div>
      </a>
      <nav class="nav-links">
        <a href="/"><i class="fa fa-house"></i> الرئيسية</a>
        <a href="/storefront/products"><i class="fa fa-border-all"></i> المنتجات</a>
        <a href="/storefront/orders/track"><i class="fa fa-location-dot"></i> تتبع الطلب</a>
        <button class="btn-logout" id="logout-btn" onclick="doLogout()" style="display:none">
          <i class="fa fa-right-from-bracket"></i> خروج
        </button>
      </nav>
    </div>
  </div>
</header>

{{-- ── Gate: not logged in ── --}}
<div id="gate" style="display:none">
  <div class="g-icon"><i class="fa fa-lock"></i></div>
  <h2>يجب تسجيل الدخول أولاً</h2>
  <p>قم بتسجيل الدخول للوصول إلى حسابك وطلباتك</p>
  <a href="/storefront/login" class="btn-green">
    <i class="fa fa-right-to-bracket"></i> تسجيل الدخول
  </a>
  <div style="margin-top:14px;font-size:13px;color:var(--text-3)">
    ليس لديك حساب؟
    <a href="/storefront/register" style="color:var(--green);font-weight:700">إنشاء حساب</a>
  </div>
</div>

{{-- ── Main App ── --}}
<div id="app">
  <div class="wrap">

    {{-- Stats quick row --}}
    <div class="stats-row" id="stats-row">
      <div class="stat-box"><div class="stat-num" id="stat-total">—</div><div class="stat-label">إجمالي الطلبات</div></div>
      <div class="stat-box"><div class="stat-num" id="stat-delivered">—</div><div class="stat-label">طلبات مُسلَّمة</div></div>
      <div class="stat-box"><div class="stat-num" id="stat-pending">—</div><div class="stat-label">طلبات معلقة</div></div>
    </div>

    <div class="account-grid">

      {{-- ── Sidebar ── --}}
      <aside>
        <div class="sidebar-card">
          <div class="profile-top">
            <div class="avatar" id="avatar-initials">؟</div>
            <div class="profile-name" id="profile-name">جاري التحميل…</div>
            <div class="profile-type" id="profile-type" style="display:none"></div>
          </div>
          <nav class="sidebar-nav">
            <div class="snav-item active" onclick="showPanel('orders')" id="nav-orders">
              <i class="fa fa-box"></i> طلباتي
            </div>
            <div class="snav-item" onclick="showPanel('profile')" id="nav-profile">
              <i class="fa fa-user"></i> معلوماتي
            </div>
            <div class="snav-item" onclick="showPanel('track')" id="nav-track">
              <i class="fa fa-location-dot"></i> تتبع طلب
            </div>
          </nav>
        </div>
      </aside>

      {{-- ── Content panels ── --}}
      <main>

        {{-- Orders panel --}}
        <div class="panel show" id="panel-orders">
          <div class="section-card">
            <div class="section-title"><i class="fa fa-box"></i> طلباتي</div>
            <div id="orders-body">
              <div style="padding:20px 0">
                <div class="skel" style="width:100%;margin-bottom:12px"></div>
                <div class="skel" style="width:80%;margin-bottom:12px"></div>
                <div class="skel" style="width:90%"></div>
              </div>
            </div>
          </div>
        </div>

        {{-- Profile panel --}}
        <div class="panel" id="panel-profile">
          <div class="section-card">
            <div class="section-title"><i class="fa fa-user"></i> معلوماتي الشخصية</div>
            <div class="f2">
              <div class="fgroup">
                <label class="flabel">الاسم الأول</label>
                <input class="finput" id="pf-fname" type="text" readonly>
              </div>
              <div class="fgroup">
                <label class="flabel">الاسم الأخير</label>
                <input class="finput" id="pf-lname" type="text" readonly>
              </div>
            </div>
            <div class="fgroup">
              <label class="flabel">رقم الهاتف</label>
              <input class="finput" id="pf-phone" type="text" readonly>
            </div>
            <div class="fgroup">
              <label class="flabel">البريد الإلكتروني</label>
              <input class="finput" id="pf-email" type="text" readonly>
            </div>
            <div class="fgroup">
              <label class="flabel">نوع الحساب</label>
              <input class="finput" id="pf-type" type="text" readonly>
            </div>
            <p style="font-size:13px;color:var(--text-3);margin-top:8px">
              <i class="fa fa-circle-info"></i>
              لتعديل بياناتك تواصل مع المتجر مباشرةً.
            </p>
          </div>
        </div>

        {{-- Track panel --}}
        <div class="panel" id="panel-track">
          <div class="section-card">
            <div class="section-title"><i class="fa fa-location-dot"></i> تتبع طلب</div>
            <p style="color:var(--text-3);font-size:14px;margin-bottom:20px">
              يمكنك تتبع أي طلب عبر رقمه
            </p>
            <div style="max-width:400px">
              <div class="fgroup">
                <label class="flabel">رقم الطلب</label>
                <input class="finput" id="track-id" type="text" placeholder="1234">
              </div>
              <button class="save-btn" onclick="goTrack()">
                <i class="fa fa-search"></i> تتبع الطلب
              </button>
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>
</div>

<footer>
  <span id="footer-store">المتجر</span> — جميع الحقوق محفوظة
</footer>

<script>
const API = window.location.origin + '/api/v1';
let TOKEN = null;
let USER  = null;

/* ── Status helpers ── */
const STATUS_LABEL = {
  pending:'معلّق', confirmed:'مؤكد', processing:'قيد المعالجة',
  out_for_delivery:'في الطريق', delivered:'تم التوصيل',
  cancelled:'ملغي', canceled:'ملغي', returned:'مُرجَع', failed:'فشل'
};
function statusBadge(s) {
  const known = ['pending','confirmed','processing','out_for_delivery','delivered','cancelled','canceled','returned','failed'];
  const cls   = known.includes(s) ? 'b-'+s : 'b-confirmed';
  const label = STATUS_LABEL[s] || s;
  return `<span class="obadge ${cls}">${label}</span>`;
}

/* ── Init ── */
(async () => {
  TOKEN = localStorage.getItem('customer_token');

  // Load config (store name / logo)
  try {
    const cfg  = await fetch(`${API}/config`).then(r => r.json());
    const name = cfg.ecommerce_name || cfg.business_name || 'المتجر';
    document.getElementById('store-name').textContent   = name;
    document.getElementById('footer-store').textContent = name;
    document.title = 'حسابي — ' + name;
    if (cfg.logo_full_url)
      document.getElementById('logo-mark').innerHTML =
        `<img src="${cfg.logo_full_url}" alt="${name}">`;
  } catch(e){}

  if (!TOKEN) {
    document.getElementById('gate').style.display = 'flex';
    return;
  }

  document.getElementById('logout-btn').style.display = 'flex';
  document.getElementById('app').style.display        = 'block';

  await loadUser();
  await loadOrders();
})();

/* ── Load user info ── */
async function loadUser() {
  try {
    const res  = await fetch(`${API}/customer/info`, {
      headers: { 'Authorization': 'Bearer ' + TOKEN, 'Accept': 'application/json' }
    });
    if (res.status === 401) { doLogout(); return; }
    const data = await res.json();
    USER = data;

    const fullName = (data.f_name || '') + ' ' + (data.l_name || '');
    const initials = ((data.f_name||'')[0]||'') + ((data.l_name||'')[0]||'');
    document.getElementById('avatar-initials').textContent = initials || '؟';
    document.getElementById('profile-name').textContent    = fullName.trim() || 'العميل';

    if (data.user_type?.name) {
      const t = document.getElementById('profile-type');
      t.textContent    = data.user_type.name;
      t.style.display  = 'inline-block';
    }

    // Profile fields
    document.getElementById('pf-fname').value = data.f_name || '';
    document.getElementById('pf-lname').value = data.l_name || '';
    document.getElementById('pf-phone').value = data.phone  || '';
    document.getElementById('pf-email').value = data.email  || '';
    document.getElementById('pf-type').value  = data.user_type?.name || 'عام';
  } catch(e) {}
}

/* ── Load orders ── */
async function loadOrders() {
  try {
    const res  = await fetch(`${API}/customer/order/list`, {
      headers: { 'Authorization': 'Bearer ' + TOKEN, 'Accept': 'application/json' }
    });
    if (res.status === 401) { doLogout(); return; }
    const data = await res.json();
    const orders = Array.isArray(data) ? data : (data.orders || data.data || []);

    // Stats
    document.getElementById('stat-total').textContent     = orders.length;
    document.getElementById('stat-delivered').textContent = orders.filter(o => o.order_status === 'delivered').length;
    document.getElementById('stat-pending').textContent   = orders.filter(o => ['pending','confirmed','processing'].includes(o.order_status)).length;

    const body = document.getElementById('orders-body');
    if (!orders.length) {
      body.innerHTML = `
        <div class="orders-empty">
          <i class="fa fa-box-open"></i>
          <p>لا توجد طلبات بعد</p>
          <a href="/storefront/products" class="btn-green" style="margin-top:16px">
            <i class="fa fa-bag-shopping"></i> ابدأ التسوق
          </a>
        </div>`;
      return;
    }

    let html = `<div style="overflow-x:auto">
      <table class="otable">
        <thead>
          <tr>
            <th>رقم الطلب</th>
            <th>التاريخ</th>
            <th>المبلغ</th>
            <th>الحالة</th>
            <th>الدفع</th>
            <th></th>
          </tr>
        </thead>
        <tbody>`;

    orders.forEach(o => {
      const date  = (o.created_at || '').split('T')[0] || '—';
      const total = parseFloat(o.order_amount || 0).toFixed(2);
      const pay   = o.payment_status === 'paid' ? '<span style="color:var(--green);font-weight:700">مدفوع</span>'
                                                  : '<span style="color:#d97706;font-weight:700">غير مدفوع</span>';
      html += `<tr>
        <td><strong>#${o.id}</strong></td>
        <td>${date}</td>
        <td>${total} ₪</td>
        <td>${statusBadge(o.order_status)}</td>
        <td>${pay}</td>
        <td><a class="track-link" href="/storefront/orders/track?id=${o.id}">
          <i class="fa fa-location-dot"></i> تتبع
        </a></td>
      </tr>`;
    });

    html += '</tbody></table></div>';
    body.innerHTML = html;
  } catch(e) {
    document.getElementById('orders-body').innerHTML =
      '<p style="color:#e74c3c;padding:16px">حدث خطأ في تحميل الطلبات.</p>';
  }
}

/* ── Panel switching ── */
function showPanel(name) {
  ['orders','profile','track'].forEach(p => {
    document.getElementById('panel-'+p).classList.toggle('show', p === name);
    document.getElementById('nav-'+p).classList.toggle('active', p === name);
  });
}

/* ── Go to track page ── */
function goTrack() {
  const id = document.getElementById('track-id').value.trim();
  window.location.href = id
    ? `/storefront/orders/track?id=${id}`
    : '/storefront/orders/track';
}

/* ── Logout ── */
function doLogout() {
  localStorage.removeItem('customer_token');
  localStorage.removeItem('customer_user');
  window.location.href = '/storefront/login';
}
</script>
</body>
</html>
