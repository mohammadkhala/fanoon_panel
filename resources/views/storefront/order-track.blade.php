<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تتبع الطلب</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{
  --green:#10b46a; --green-d:#0c8f52; --green-10:rgba(16,180,106,.10);
  --navy:#0f1512; --surface:#f5f8f4; --white:#fff; --border:#d6e8d9;
  --text:#0f1512; --text-2:#4a5e50; --text-3:#8fa895;
  --font:'Cairo',sans-serif; --radius:14px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--font);background:var(--surface);color:var(--text);direction:rtl;
  min-height:100vh;display:flex;flex-direction:column;align-items:center;padding:32px 16px}

/* ─── Card ─── */
.card{background:var(--white);border:1.5px solid var(--border);border-radius:22px;
  padding:40px 36px;width:100%;max-width:680px;box-shadow:0 6px 30px rgba(15,21,18,.08)}

/* ─── Logo ─── */
.logo{display:flex;align-items:center;gap:10px;justify-content:center;margin-bottom:28px;text-decoration:none}
.logo-mark{width:110px;height:46px;background:#000;border-radius:10px;
  display:flex;align-items:center;justify-content:center;overflow:hidden;padding:4px 8px}
.logo-mark img{width:100%;height:100%;object-fit:contain}
.logo-name{font-size:17px;font-weight:900;color:var(--navy)}

/* ─── Header ─── */
.icon-wrap{width:72px;height:72px;background:var(--green-10);border-radius:50%;
  display:flex;align-items:center;justify-content:center;margin:0 auto 20px;color:var(--green);font-size:28px}
h1{font-size:24px;font-weight:900;color:var(--navy);text-align:center;margin-bottom:8px}
.sub{color:var(--text-2);font-size:14px;text-align:center;line-height:1.7;margin-bottom:28px}

/* ─── Tabs ─── */
.tabs{display:flex;background:var(--surface);border:1.5px solid var(--border);
  border-radius:12px;padding:4px;margin-bottom:24px;gap:4px}
.tab-btn{flex:1;padding:10px 8px;border:none;background:transparent;
  border-radius:9px;font-family:var(--font);font-size:14px;font-weight:700;
  color:var(--text-2);cursor:pointer;display:flex;align-items:center;
  justify-content:center;gap:7px;transition:all .2s}
.tab-btn.active{background:var(--white);color:var(--green);
  box-shadow:0 2px 10px rgba(15,21,18,.10)}
.tab-btn i{font-size:14px}

/* ─── Form ─── */
.form-group{margin-bottom:16px}
.form-label{display:block;font-size:13px;font-weight:700;color:var(--text-2);margin-bottom:7px}

/* Phone input row */
.phone-row{display:flex;gap:0;border:1.5px solid var(--border);border-radius:12px;
  overflow:hidden;background:var(--white);transition:border-color .2s}
.phone-row:focus-within{border-color:var(--green)}

.prefix-select{
  flex-shrink:0;width:160px;padding:12px 10px;border:none;outline:none;
  font-family:var(--font);font-size:14px;font-weight:700;color:var(--navy);
  background:var(--surface);border-left:1.5px solid var(--border);
  appearance:none;-webkit-appearance:none;cursor:pointer;
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%238fa895'/%3E%3C/svg%3E");
  background-repeat:no-repeat;background-position:left 10px center;
  padding-left:28px;
}
.phone-local{flex:1;padding:12px 14px;border:none;outline:none;
  font-family:var(--font);font-size:15px;color:var(--text);background:transparent}
.phone-local::placeholder{color:var(--text-3)}

/* Phone preview badge */
.phone-preview{margin-top:8px;font-size:12px;color:var(--text-2);min-height:18px;
  display:flex;align-items:center;gap:6px}
.phone-preview .badge{background:var(--green-10);color:var(--green);
  border-radius:6px;padding:2px 8px;font-weight:700;font-family:monospace;font-size:12px}
.phone-preview .hint{color:var(--text-3)}

/* Regular input */
.form-input{width:100%;padding:12px 16px;border:1.5px solid var(--border);
  border-radius:12px;font-family:var(--font);font-size:15px;color:var(--text);
  background:var(--surface);outline:none;transition:border-color .2s}
.form-input:focus{border-color:var(--green);background:var(--white)}

/* ─── Button ─── */
.btn{width:100%;background:var(--green);color:#fff;border:none;border-radius:12px;
  padding:14px;font-family:var(--font);font-size:15px;font-weight:700;cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:8px;transition:background .2s;margin-top:4px}
.btn:hover{background:var(--green-d)}
.btn:disabled{opacity:.65;cursor:not-allowed}

/* ─── Messages ─── */
.err-msg{display:none;background:#fde8e8;border:1.5px solid #e74c3c;border-radius:10px;
  color:#c0392b;padding:12px 16px;font-size:14px;font-weight:600;margin-top:16px;text-align:center}

/* ─── Results ─── */
.results-wrap{margin-top:24px;display:none}
.results-wrap h2{font-size:16px;font-weight:800;color:var(--navy);margin-bottom:16px;
  display:flex;align-items:center;gap:8px}

/* Order card */
.order-card{background:var(--surface);border:1.5px solid var(--border);
  border-radius:14px;overflow:hidden;margin-bottom:16px}
.order-card-header{padding:14px 18px;display:flex;align-items:center;
  justify-content:space-between;cursor:pointer;user-select:none;gap:12px;flex-wrap:wrap}
.order-card-header:hover{background:rgba(16,180,106,.04)}
.order-id-label{font-size:15px;font-weight:800;color:var(--navy)}
.order-date{font-size:12px;color:var(--text-3);margin-top:2px}
.order-card-meta{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.toggle-icon{color:var(--text-3);font-size:12px;transition:transform .2s}
.order-card.open .toggle-icon{transform:rotate(180deg)}

/* Summary rows */
.order-summary{padding:0 18px 18px;display:none;border-top:1px solid var(--border)}
.order-card.open .order-summary{display:block}
.sum-row{display:flex;justify-content:space-between;align-items:center;
  padding:9px 0;border-bottom:1px solid var(--border);font-size:14px}
.sum-row:last-child{border-bottom:none}
.sum-label{color:var(--text-3);font-weight:500}
.sum-val{font-weight:700;color:var(--navy)}

/* Status badges */
.status-badge{display:inline-flex;align-items:center;gap:5px;
  padding:4px 12px;border-radius:50px;font-size:12px;font-weight:700}
.s-pending{background:rgba(245,158,11,.12);color:#d97706}
.s-confirmed,.s-processing,.s-out_for_delivery{background:var(--green-10);color:var(--green)}
.s-delivered{background:#d4f5e5;color:#0c8f52}
.s-canceled,.s-cancelled,.s-returned,.s-failed{background:rgba(231,76,60,.1);color:#e74c3c}
.s-custom{background:rgba(99,102,241,.10);color:#4f46e5}

/* ─── Timeline ─── */
.tl-wrap{margin-top:16px}
.tl-wrap h4{font-size:13px;font-weight:800;color:var(--text-2);
  margin-bottom:14px;display:flex;align-items:center;gap:6px;text-transform:uppercase;letter-spacing:.5px}
.timeline{position:relative;padding-right:26px}
.timeline::before{content:'';position:absolute;right:9px;top:0;bottom:0;
  width:2px;background:var(--border)}
.tl-item{position:relative;padding-bottom:20px}
.tl-item:last-child{padding-bottom:0}
.tl-dot{position:absolute;right:-17px;width:16px;height:16px;border-radius:50%;
  background:var(--green);border:2px solid var(--white);box-shadow:0 0 0 2px var(--green);top:3px}
.tl-dot.old{background:var(--border);box-shadow:0 0 0 2px var(--border)}
.tl-body{background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:12px 14px}
.tl-status{font-size:14px;font-weight:800;color:var(--navy);margin-bottom:3px}
.tl-note{font-size:13px;color:var(--text-2);margin-top:3px;line-height:1.6}
.tl-time{font-size:11px;color:var(--text-3);margin-top:5px}

/* ─── Bottom links ─── */
.bottom-links{display:flex;gap:16px;justify-content:center;margin-top:24px;flex-wrap:wrap}
.bottom-links a{color:var(--green);font-size:13px;font-weight:700;
  display:flex;align-items:center;gap:5px;text-decoration:none}
.bottom-links a:hover{color:var(--green-d)}

@media(max-width:520px){
  .card{padding:28px 18px}
  .prefix-select{width:130px}
  .order-card-header{gap:8px}
}
</style>
</head>
<body>

<div class="card">

  {{-- Logo --}}
  <a href="/" class="logo">
    <div class="logo-mark" id="logo-mark">ع</div>
    <span class="logo-name" id="store-name">المتجر</span>
  </a>

  <div class="icon-wrap"><i class="fa fa-location-dot"></i></div>
  <h1>تتبع طلبك</h1>
  <p class="sub">ابحث عن طلبك برقم هاتفك أو برقم الطلب مباشرة</p>

  {{-- Tab switch --}}
  <div class="tabs">
    <button class="tab-btn active" id="tab-phone-btn" onclick="switchTab('phone')">
      <i class="fa fa-mobile-screen-button"></i> رقم الهاتف
    </button>
    <button class="tab-btn" id="tab-order-btn" onclick="switchTab('order')">
      <i class="fa fa-hashtag"></i> رقم الطلب
    </button>
  </div>

  {{-- ════ Tab: phone ════ --}}
  <div id="tab-phone">
    <div class="form-group">
      <label class="form-label">رقم هاتفك *</label>

      {{-- Prefix selector + local number --}}
      <div class="phone-row" id="phone-row">
        <input type="tel" class="phone-local" id="phone-local"
               placeholder="0599 123 456"
               oninput="updatePhonePreview()"
               onkeydown="if(event.key==='Enter')trackByPhone()">
        <select class="prefix-select" id="phone-prefix" onchange="updatePhonePreview()">
          <option value="+972">+972 🇮🇱 (واتساب)</option>
          <option value="+970">+970 🇵🇸 (السلطة)</option>
        </select>
      </div>

      {{-- Live preview of normalized number --}}
      <div class="phone-preview" id="phone-preview"></div>
    </div>

    <button class="btn" id="btn-phone" onclick="trackByPhone()">
      <i class="fa fa-search"></i> بحث بالهاتف
    </button>
  </div>

  {{-- ════ Tab: order ID ════ --}}
  <div id="tab-order" style="display:none">
    <div class="form-group">
      <label class="form-label">رقم الطلب *</label>
      <input type="number" class="form-input" id="order-id"
             placeholder="مثال: 100023"
             onkeydown="if(event.key==='Enter')trackByOrderId()">
    </div>
    <button class="btn" id="btn-order" onclick="trackByOrderId()">
      <i class="fa fa-search"></i> بحث برقم الطلب
    </button>
  </div>

  {{-- Error message --}}
  <div class="err-msg" id="err-msg"></div>

  {{-- Results --}}
  <div class="results-wrap" id="results-wrap">
    <h2 id="results-title"><i class="fa fa-box-open" style="color:var(--green)"></i> نتائج البحث</h2>
    <div id="orders-list"></div>
  </div>

  <div class="bottom-links">
    <a href="/"><i class="fa fa-house"></i> الرئيسية</a>
    <a href="/storefront/products"><i class="fa fa-border-all"></i> تسوق</a>
    <a href="/storefront/contact"><i class="fa fa-phone"></i> تواصل معنا</a>
  </div>

</div><!-- .card -->

<script>
const API = window.location.origin + '/api/v1';
let CUR  = '₪';
let STORE = 'المتجر';

/* ══════════════════════════════
   Init: load store config
══════════════════════════════ */
async function init() {
  try {
    const cfg = await fetch(`${API}/config`).then(r => r.json());
    STORE = cfg.ecommerce_name || cfg.business_name || 'المتجر';
    if (cfg.currency_symbol) CUR = cfg.currency_symbol;
    document.getElementById('store-name').textContent = STORE;
    const logo = cfg.logo_full_url || '';
    if (logo) {
      document.getElementById('logo-mark').innerHTML =
        `<img src="${logo}" alt="${STORE}" style="width:100%;height:100%;object-fit:contain">`;
    }
  } catch(e) {}
  updatePhonePreview();
}

/* ══════════════════════════════
   Tab switching
══════════════════════════════ */
function switchTab(tab) {
  document.getElementById('tab-phone').style.display = tab === 'phone' ? '' : 'none';
  document.getElementById('tab-order').style.display = tab === 'order' ? '' : 'none';
  document.getElementById('tab-phone-btn').classList.toggle('active', tab === 'phone');
  document.getElementById('tab-order-btn').classList.toggle('active', tab === 'order');
  hideResults();
}

/* ══════════════════════════════
   Phone normalization (client-side)
══════════════════════════════ */
function normalizeLocalPhone(local) {
  // Strip all non-digits
  let d = local.replace(/[^\d]/g, '');
  // Strip leading zero
  if (d.startsWith('0')) d = d.slice(1);
  return d;
}

function buildFullPhone() {
  const prefix = document.getElementById('phone-prefix').value;   // +972 or +970
  const local  = document.getElementById('phone-local').value;
  const core   = normalizeLocalPhone(local);
  if (!core) return '';
  return prefix + core;
}

function updatePhonePreview() {
  const full    = buildFullPhone();
  const preview = document.getElementById('phone-preview');
  const local   = document.getElementById('phone-local').value;
  const stripped = local.replace(/[^\d]/g, '');

  if (!full) { preview.innerHTML = ''; return; }

  let hint = '';
  if (stripped.startsWith('0')) {
    hint = `<span class="hint">(تمّ حذف الصفر تلقائيًا)</span>`;
  }

  preview.innerHTML = `
    <i class="fa fa-check-circle" style="color:var(--green)"></i>
    سيُبحث عن: <span class="badge">${full}</span> ${hint}`;
}

/* ══════════════════════════════
   Track by phone
══════════════════════════════ */
async function trackByPhone() {
  const full = buildFullPhone();
  if (!full) { showError('أدخل رقم هاتفك أولاً'); return; }

  const btn = document.getElementById('btn-phone');
  setLoading(btn, true);
  hideResults();

  try {
    const res  = await fetch(`${API}/customer/order/track-by-phone`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ phone: full }),
    });
    const data = await res.json();

    if (res.ok && Array.isArray(data) && data.length) {
      renderOrderList(data, `الطلبات المرتبطة بـ <span style="font-family:monospace;color:var(--green)">${full}</span>`);
    } else {
      showError('لم يتم العثور على أي طلبات لهذا الرقم. تأكد من المقدمة ورقم الهاتف.');
    }
  } catch(e) {
    showError('حدث خطأ أثناء البحث. حاول مرة أخرى.');
  }
  setLoading(btn, false);
}

/* ══════════════════════════════
   Track by order ID
══════════════════════════════ */
async function trackByOrderId() {
  const orderId = document.getElementById('order-id').value.trim();
  if (!orderId) { showError('أدخل رقم الطلب أولاً'); return; }

  const btn = document.getElementById('btn-order');
  setLoading(btn, true);
  hideResults();

  try {
    const res  = await fetch(`${API}/customer/order/track`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ order_id: orderId }),
    });
    const data = await res.json();

    if (res.ok && data && !data.errors && data.id) {
      renderOrderList([data], `تفاصيل الطلب #${data.id}`);
    } else {
      showError('لم يتم العثور على الطلب. تأكد من رقم الطلب.');
    }
  } catch(e) {
    showError('حدث خطأ أثناء البحث. حاول مرة أخرى.');
  }
  setLoading(btn, false);
}

/* ══════════════════════════════
   Render helpers
══════════════════════════════ */
const STATUS_LABEL = {
  pending:          '⏳ معلّق',
  confirmed:        '✅ مؤكد',
  processing:       '⚙️ قيد المعالجة',
  out_for_delivery: '🚚 في الطريق إليك',
  delivered:        '🎉 تم التوصيل',
  cancelled:        '🚫 ملغي',
  canceled:         '🚫 ملغي',
  returned:         '↩️ مُرجَع',
  failed:           '❌ فشل التوصيل',
};

function statusLabel(s) { return STATUS_LABEL[s] || ('📦 ' + s); }

function statusClass(s) {
  const known = ['pending','confirmed','processing','out_for_delivery','delivered','cancelled','canceled','returned','failed'];
  return 's-' + (known.includes(s) ? s : 'custom');
}

function paymentLabel(m) {
  const map = { cash_on_delivery: 'دفع عند الاستلام', wallet: 'المحفظة', online: 'دفع إلكتروني' };
  return map[m] || m || '—';
}

function formatDate(dt) {
  if (!dt) return '—';
  try { return new Date(dt).toLocaleDateString('ar-EG', { year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' }); }
  catch(e) { return dt.split('T')[0]; }
}

function renderOrderList(orders, title) {
  document.getElementById('results-title').innerHTML =
    `<i class="fa fa-box-open" style="color:var(--green)"></i> ${title}`;

  const listEl = document.getElementById('orders-list');
  listEl.innerHTML = orders.map((order, idx) => renderOrderCard(order, idx)).join('');

  // Auto-open first card
  if (orders.length === 1) {
    const firstCard = listEl.querySelector('.order-card');
    if (firstCard) firstCard.classList.add('open');
  }

  document.getElementById('results-wrap').style.display = 'block';
  document.getElementById('err-msg').style.display      = 'none';
}

function renderOrderCard(order, idx) {
  const st  = order.order_status || '';
  const sc  = statusClass(st);
  const sl  = statusLabel(st);
  const logs = (order.status_logs || []).sort((a,b) => new Date(b.created_at) - new Date(a.created_at));

  const timeline = logs.length
    ? `<div class="tl-wrap">
        <h4><i class="fa fa-clock-rotate-left"></i> سجل التحديثات</h4>
        <div class="timeline">${logs.map((log, i) => renderTlItem(log, i===0)).join('')}</div>
       </div>`
    : '';

  const total = parseFloat(order.order_amount || 0).toFixed(2);
  const deliveryCharge = parseFloat(order.delivery_charge || 0).toFixed(2);

  return `
  <div class="order-card" id="oc-${idx}">
    <div class="order-card-header" onclick="toggleCard(${idx})">
      <div>
        <div class="order-id-label">طلب رقم #${order.id}</div>
        <div class="order-date">${formatDate(order.created_at)}</div>
      </div>
      <div class="order-card-meta">
        <span class="status-badge ${sc}">${sl}</span>
        <i class="fa fa-chevron-down toggle-icon"></i>
      </div>
    </div>
    <div class="order-summary">
      <div class="sum-row">
        <span class="sum-label">الحالة</span>
        <span class="sum-val"><span class="status-badge ${sc}">${sl}</span></span>
      </div>
      <div class="sum-row">
        <span class="sum-label">المبلغ الكلي</span>
        <span class="sum-val">${total} ${CUR}</span>
      </div>
      <div class="sum-row">
        <span class="sum-label">رسوم التوصيل</span>
        <span class="sum-val">${deliveryCharge} ${CUR}</span>
      </div>
      <div class="sum-row">
        <span class="sum-label">طريقة الدفع</span>
        <span class="sum-val">${paymentLabel(order.payment_method)}</span>
      </div>
      <div class="sum-row">
        <span class="sum-label">حالة الدفع</span>
        <span class="sum-val" style="color:${order.payment_status==='paid'?'var(--green)':'#e74c3c'}">
          ${order.payment_status === 'paid' ? '✅ مدفوع' : '⏳ غير مدفوع'}
        </span>
      </div>
      ${timeline}
    </div>
  </div>`;
}

function renderTlItem(log, isLatest) {
  const note = log.note
    ? `<div class="tl-note"><i class="fa fa-comment-dots" style="color:var(--green)"></i> ${log.note}</div>` : '';
  return `
    <div class="tl-item">
      <div class="tl-dot${isLatest?'':' old'}"></div>
      <div class="tl-body">
        <div class="tl-status">${statusLabel(log.new_status || log.custom_status || '')}</div>
        ${note}
        <div class="tl-time"><i class="fa fa-clock"></i> ${formatDate(log.created_at)}</div>
      </div>
    </div>`;
}

function toggleCard(idx) {
  const card = document.getElementById('oc-' + idx);
  card.classList.toggle('open');
}

/* ══════════════════════════════
   UI helpers
══════════════════════════════ */
function setLoading(btn, on) {
  btn.disabled = on;
  if (on) {
    btn._orig = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> جاري البحث...';
  } else {
    btn.innerHTML = btn._orig || btn.innerHTML;
  }
}

function showError(msg) {
  const el = document.getElementById('err-msg');
  el.innerHTML = `<i class="fa fa-circle-exclamation"></i> ${msg}`;
  el.style.display = 'block';
  document.getElementById('results-wrap').style.display = 'none';
}

function hideResults() {
  document.getElementById('err-msg').style.display      = 'none';
  document.getElementById('results-wrap').style.display = 'none';
}

init();
</script>
</body>
</html>
