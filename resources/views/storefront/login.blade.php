<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title id="page-title">تسجيل الدخول</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{
  --green:#10b46a;--green-d:#0c8f52;--green-10:rgba(16,180,106,.10);
  --navy:#0f1512;--surface:#f5f8f4;--white:#fff;
  --border:#d6e8d9;--text:#0f1512;--text-2:#4a5e50;--text-3:#8fa895;
  --font:'IBM Plex Sans Arabic','Cairo',sans-serif;
  --r:14px;--sh:0 6px 30px rgba(15,21,18,.10);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--font);background:var(--surface);color:var(--text);direction:rtl;min-height:100vh;display:flex;flex-direction:column}
a{text-decoration:none;color:inherit}
.wrap{max-width:1260px;margin-inline:auto;padding-inline:20px}

/* ── Header ── */
.header{background:var(--white);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:200}
.hb{display:flex;align-items:center;gap:16px;padding:12px 0}
.logo{display:flex;align-items:center;gap:10px}
.logo-mark{width:120px;height:50px;background:#000;border:1.5px solid #222;border-radius:12px;display:flex;align-items:center;justify-content:center;overflow:hidden;padding:5px 10px;box-shadow:0 2px 8px rgba(15,21,18,.08)}
.logo-mark img{width:100%;height:100%;object-fit:contain}
.logo-name{font-size:17px;font-weight:900;color:var(--navy)}
.logo-tag{font-size:11px;color:var(--text-3)}
.nav-links{margin-inline-start:auto;display:flex;align-items:center;gap:16px}
.nav-links a{font-size:13px;font-weight:600;color:var(--text-2);display:flex;align-items:center;gap:5px;transition:color .2s}
.nav-links a:hover{color:var(--green)}

/* ── Main card ── */
main{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px}
.card{background:var(--white);border:1.5px solid var(--border);border-radius:22px;padding:44px 40px;width:100%;max-width:460px;box-shadow:var(--sh)}
.card-icon{width:72px;height:72px;background:var(--green-10);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;color:var(--green);font-size:28px}
.card-title{font-size:24px;font-weight:900;color:var(--navy);text-align:center;margin-bottom:6px}
.card-sub{font-size:14px;color:var(--text-3);text-align:center;margin-bottom:28px}

/* ── Form ── */
.fgroup{margin-bottom:18px}
.flabel{display:block;font-size:13px;font-weight:600;color:var(--text-2);margin-bottom:6px}
.finput{width:100%;padding:12px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:var(--font);font-size:14px;color:var(--text);background:var(--surface);outline:none;transition:border-color .2s}
.finput:focus{border-color:var(--green);background:var(--white)}
.finput.err{border-color:#e74c3c}
.finput-wrap{position:relative}
.finput-wrap .finput{padding-left:44px}
.finput-wrap .eye{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-3);cursor:pointer;font-size:15px;transition:color .2s}
.finput-wrap .eye:hover{color:var(--green)}
.ftabs{display:flex;border:1.5px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:18px}
.ftab{flex:1;padding:10px;text-align:center;font-size:13px;font-weight:700;cursor:pointer;color:var(--text-3);background:var(--surface);transition:all .2s;display:flex;align-items:center;justify-content:center;gap:6px}
.ftab.active{background:var(--green);color:#fff}
.btn{width:100%;background:var(--green);color:#fff;border:none;border-radius:12px;padding:13px;font-family:var(--font);font-size:15px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .2s;margin-top:6px}
.btn:hover{background:var(--green-d)}
.btn:disabled{opacity:.6;cursor:not-allowed}
.divider{display:flex;align-items:center;gap:12px;margin:20px 0;color:var(--text-3);font-size:13px}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border)}
.link-row{text-align:center;font-size:13px;color:var(--text-2);margin-top:20px}
.link-row a{color:var(--green);font-weight:700}
.link-row a:hover{color:var(--green-d)}
.ferr{font-size:12px;color:#e74c3c;margin-top:4px;display:none}
.alert{padding:12px 16px;border-radius:10px;font-size:14px;font-weight:600;margin-bottom:16px;display:none;text-align:center}
.alert.err{background:#fde8e8;border:1.5px solid #e74c3c;color:#c0392b}
.alert.ok{background:#d4f5e5;border:1.5px solid var(--green);color:var(--green-d)}

/* ── Footer ── */
footer{background:var(--navy);color:rgba(255,255,255,.45);text-align:center;padding:18px 20px;font-size:13px}
footer a{color:rgba(255,255,255,.45)}
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
        <a href="/storefront/register" style="background:var(--green);color:#fff;padding:8px 16px;border-radius:10px">
          <i class="fa fa-user-plus"></i> إنشاء حساب
        </a>
      </nav>
    </div>
  </div>
</header>

<main>
  <div class="card">
    <div class="card-icon"><i class="fa fa-right-to-bracket"></i></div>
    <div class="card-title">تسجيل الدخول</div>
    <div class="card-sub">مرحباً بك، أدخل بياناتك للمتابعة</div>

    <div id="alert" class="alert"></div>

    {{-- Login type tabs --}}
    <div class="ftabs">
      <div class="ftab active" id="tab-phone" onclick="setTab('phone')">
        <i class="fa fa-phone"></i> رقم الهاتف
      </div>
      <div class="ftab" id="tab-email" onclick="setTab('email')">
        <i class="fa fa-envelope"></i> البريد الإلكتروني
      </div>
    </div>

    <form id="login-form" onsubmit="doLogin(event)">
      <div class="fgroup">
        <label class="flabel" id="id-label">رقم الهاتف</label>
        <input class="finput" type="text" id="email_or_phone" placeholder="0599xxxxxx" autocomplete="username" required>
        <div class="ferr" id="err-identity"></div>
      </div>

      <div class="fgroup">
        <label class="flabel">كلمة المرور</label>
        <div class="finput-wrap">
          <input class="finput" type="password" id="password" placeholder="••••••••" autocomplete="current-password" required>
          <span class="eye" onclick="togglePass('password',this)"><i class="fa fa-eye"></i></span>
        </div>
        <div class="ferr" id="err-pass"></div>
      </div>

      <button type="submit" class="btn" id="login-btn">
        <i class="fa fa-right-to-bracket"></i> دخول
      </button>
    </form>

    <div class="divider">أو</div>

    <div class="link-row">
      ليس لديك حساب؟ <a href="/storefront/register">إنشاء حساب جديد</a>
    </div>
    <div class="link-row" style="margin-top:10px">
      <a href="/storefront/orders/track"><i class="fa fa-location-dot"></i> تتبع طلبك بدون تسجيل دخول</a>
    </div>
  </div>
</main>

<footer>
  <span id="footer-store">المتجر</span> — جميع الحقوق محفوظة
</footer>

<script>
const API   = window.location.origin + '/api/v1';
let loginType = 'phone';

/* ── Load config ── */
(async () => {
  try {
    const cfg = await fetch(`${API}/config`).then(r => r.json());
    const name = cfg.ecommerce_name || cfg.business_name || 'المتجر';
    document.getElementById('store-name').textContent  = name;
    document.getElementById('footer-store').textContent = name;
    document.title = 'تسجيل الدخول — ' + name;
    const logo = cfg.logo_full_url || '';
    if (logo) document.getElementById('logo-mark').innerHTML =
      `<img src="${logo}" alt="${name}">`;

    // If already logged in → redirect
    if (localStorage.getItem('customer_token')) {
      window.location.href = '/storefront/account';
    }
  } catch(e){}
})();

/* ── Tabs ── */
function setTab(type) {
  loginType = type;
  document.getElementById('tab-phone').classList.toggle('active', type === 'phone');
  document.getElementById('tab-email').classList.toggle('active', type === 'email');
  const inp = document.getElementById('email_or_phone');
  if (type === 'phone') {
    document.getElementById('id-label').textContent = 'رقم الهاتف';
    inp.placeholder = '0599xxxxxx';
    inp.type = 'tel';
  } else {
    document.getElementById('id-label').textContent = 'البريد الإلكتروني';
    inp.placeholder = 'example@email.com';
    inp.type = 'email';
  }
  inp.value = '';
}

/* ── Toggle password visibility ── */
function togglePass(id, el) {
  const inp = document.getElementById(id);
  const show = inp.type === 'password';
  inp.type = show ? 'text' : 'password';
  el.innerHTML = show ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
}

/* ── Login ── */
async function doLogin(e) {
  e.preventDefault();
  clearErrors();
  const identity = document.getElementById('email_or_phone').value.trim();
  const password  = document.getElementById('password').value;
  const btn       = document.getElementById('login-btn');

  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> جاري الدخول...';

  try {
    const res  = await fetch(`${API}/auth/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ email_or_phone: identity, password, type: loginType }),
    });
    const data = await res.json();

    if (res.ok && data.token) {
      localStorage.setItem('customer_token', data.token);
      if (data.user) localStorage.setItem('customer_user', JSON.stringify(data.user));
      showAlert('تم تسجيل الدخول بنجاح! جاري التوجيه…', 'ok');
      setTimeout(() => { window.location.href = '/'; }, 1000);
    } else {
      const errs = data.errors || [];
      let shown = false;
      errs.forEach(er => {
        if (er.code === 'email_or_phone' || er.code === 'phone' || er.code === 'email') {
          showFieldErr('err-identity', er.message); shown = true;
        } else if (er.code === 'password') {
          showFieldErr('err-pass', er.message); shown = true;
        }
      });
      if (!shown) showAlert(errs[0]?.message || 'بيانات خاطئة، تحقق من المعلومات وأعد المحاولة.', 'err');
    }
  } catch(err) {
    showAlert('حدث خطأ في الاتصال، أعد المحاولة.', 'err');
  }

  btn.disabled = false;
  btn.innerHTML = '<i class="fa fa-right-to-bracket"></i> دخول';
}

function showAlert(msg, type) {
  const el = document.getElementById('alert');
  el.textContent = msg;
  el.className = `alert ${type}`;
  el.style.display = 'block';
}
function showFieldErr(id, msg) {
  const el = document.getElementById(id);
  if (el) { el.textContent = msg; el.style.display = 'block'; }
}
function clearErrors() {
  document.getElementById('alert').style.display = 'none';
  ['err-identity','err-pass'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
  });
}
</script>
</body>
</html>
