<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title id="page-title">إنشاء حساب</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{
  --green:#10b46a;--green-d:#0c8f52;--green-10:rgba(16,180,106,.10);
  --navy:#0f1512;--surface:#f5f8f4;--white:#fff;--amber:#f59e0b;
  --border:#d6e8d9;--text:#0f1512;--text-2:#4a5e50;--text-3:#8fa895;
  --font:'IBM Plex Sans Arabic','Cairo',sans-serif;
  --sh:0 6px 30px rgba(15,21,18,.10);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--font);background:var(--surface);color:var(--text);direction:rtl;min-height:100vh;display:flex;flex-direction:column}
a{text-decoration:none;color:inherit}
.wrap{max-width:1260px;margin-inline:auto;padding-inline:20px}

/* ── Header ── */
.header{background:var(--white);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:200}
.hb{display:flex;align-items:center;gap:16px;padding:12px 0}
.logo{display:flex;align-items:center;gap:10px}
.logo-mark{width:120px;height:50px;background:#000;border:1.5px solid #222;border-radius:12px;display:flex;align-items:center;justify-content:center;overflow:hidden;padding:5px 10px}
.logo-mark img{width:100%;height:100%;object-fit:contain}
.logo-name{font-size:17px;font-weight:900;color:var(--navy)}
.logo-tag{font-size:11px;color:var(--text-3)}
.nav-links{margin-inline-start:auto;display:flex;align-items:center;gap:16px}
.nav-links a{font-size:13px;font-weight:600;color:var(--text-2);display:flex;align-items:center;gap:5px;transition:color .2s}
.nav-links a:hover,.nav-links a:focus{color:var(--green)}

/* ── Main card ── */
main{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px}
.card{background:var(--white);border:1.5px solid var(--border);border-radius:22px;padding:44px 40px;width:100%;max-width:500px;box-shadow:var(--sh)}

/* ── Steps bar ── */
.steps{display:flex;align-items:center;justify-content:center;gap:0;margin-bottom:32px}
.step-item{display:flex;flex-direction:column;align-items:center;gap:6px;position:relative}
.step-circle{width:36px;height:36px;border-radius:50%;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:var(--text-3);background:var(--white);transition:all .3s;z-index:1;position:relative}
.step-label{font-size:11px;font-weight:600;color:var(--text-3);white-space:nowrap;transition:color .3s}
.step-item.active .step-circle{background:var(--green);border-color:var(--green);color:#fff;box-shadow:0 0 0 4px var(--green-10)}
.step-item.active .step-label{color:var(--navy)}
.step-item.done .step-circle{background:var(--green-d);border-color:var(--green-d);color:#fff}
.step-item.done .step-label{color:var(--green-d)}
.step-line{width:60px;height:2px;background:var(--border);margin-bottom:22px;transition:background .3s}
.step-line.done{background:var(--green)}

/* ── Card header ── */
.card-icon{width:68px;height:68px;background:var(--green-10);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:var(--green);font-size:26px;transition:all .3s}
.card-title{font-size:22px;font-weight:900;color:var(--navy);text-align:center;margin-bottom:6px}
.card-sub{font-size:14px;color:var(--text-3);text-align:center;margin-bottom:28px;line-height:1.6}

/* ── Form elements ── */
.row2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.fgroup{margin-bottom:16px}
.flabel{display:block;font-size:13px;font-weight:600;color:var(--text-2);margin-bottom:5px}
.req{color:#e74c3c}
.finput{width:100%;padding:12px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:var(--font);font-size:15px;color:var(--text);background:var(--surface);outline:none;transition:border-color .2s,background .2s}
.finput:focus{border-color:var(--green);background:var(--white)}
.finput.has-err{border-color:#e74c3c}
.pw-wrap{position:relative}
.pw-wrap .finput{padding-left:44px}
.eye{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-3);cursor:pointer;font-size:15px}
.eye:hover{color:var(--green)}
.ferr{font-size:12px;color:#e74c3c;margin-top:4px;display:none}
.hint{font-size:12px;color:var(--text-3);margin-top:4px}

/* OTP boxes */
.otp-row{display:flex;gap:10px;justify-content:center;margin:8px 0 4px}
.otp-box{width:48px;height:54px;text-align:center;font-size:22px;font-weight:900;border:2px solid var(--border);border-radius:12px;font-family:var(--font);color:var(--navy);background:var(--surface);outline:none;transition:border-color .2s,background .2s;caret-color:var(--green)}
.otp-box:focus{border-color:var(--green);background:var(--white)}
.otp-box.filled{border-color:var(--green);background:var(--green-10)}
.otp-box.err{border-color:#e74c3c;background:#fde8e8}

/* Strength bar */
.strength-bar{height:4px;border-radius:4px;margin-top:6px;background:var(--border);overflow:hidden}
.strength-fill{height:100%;width:0;border-radius:4px;transition:width .3s,background .3s}

/* Agree */
.agree-row{display:flex;align-items:flex-start;gap:10px;margin-bottom:20px;font-size:13px;color:var(--text-2);line-height:1.7;cursor:pointer}
.agree-row input{margin-top:3px;accent-color:var(--green);width:16px;height:16px;flex-shrink:0;cursor:pointer}
.agree-row a{color:var(--green);font-weight:600}

/* Buttons */
.btn{width:100%;background:var(--green);color:#fff;border:none;border-radius:12px;padding:13px;font-family:var(--font);font-size:15px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .2s;margin-top:4px}
.btn:hover{background:var(--green-d)}
.btn:disabled{opacity:.55;cursor:not-allowed}
.btn-outline{background:transparent;border:1.5px solid var(--border);color:var(--text-2)}
.btn-outline:hover{border-color:var(--green);color:var(--green);background:var(--green-10)}
.resend-row{text-align:center;font-size:13px;color:var(--text-3);margin-top:14px}
.resend-btn{color:var(--green);font-weight:700;cursor:pointer;background:none;border:none;font-family:var(--font);font-size:13px}
.resend-btn:disabled{color:var(--text-3);cursor:not-allowed}

/* Alert */
.alert{padding:12px 16px;border-radius:10px;font-size:14px;font-weight:600;margin-bottom:16px;display:none;text-align:center}
.alert.err{background:#fde8e8;border:1.5px solid #e74c3c;color:#c0392b}
.alert.ok{background:#d4f5e5;border:1.5px solid var(--green);color:var(--green-d)}
.alert.warn{background:#fffbeb;border:1.5px solid var(--amber);color:#92400e}

/* Phone display badge */
.phone-badge{display:inline-flex;align-items:center;gap:8px;background:var(--green-10);border:1.5px solid var(--border);border-radius:10px;padding:8px 16px;font-size:14px;font-weight:700;color:var(--navy);margin-bottom:20px}
.phone-badge i{color:var(--green)}

.link-row{text-align:center;font-size:13px;color:var(--text-2);margin-top:20px}
.link-row a{color:var(--green);font-weight:700}

footer{background:var(--navy);color:rgba(255,255,255,.45);text-align:center;padding:18px 20px;font-size:13px}

@media(max-width:500px){.row2{grid-template-columns:1fr}.card{padding:28px 18px}.otp-box{width:42px;height:48px;font-size:20px}}
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
        <a href="/storefront/login" style="background:var(--green);color:#fff;padding:8px 16px;border-radius:10px">
          <i class="fa fa-right-to-bracket"></i> تسجيل الدخول
        </a>
      </nav>
    </div>
  </div>
</header>

<main>
  <div class="card">

    {{-- Steps bar --}}
    <div class="steps">
      <div class="step-item active" id="si-1">
        <div class="step-circle" id="sc-1">1</div>
        <div class="step-label">رقم الهاتف</div>
      </div>
      <div class="step-line" id="sl-1"></div>
      <div class="step-item" id="si-2">
        <div class="step-circle" id="sc-2">2</div>
        <div class="step-label">رمز التحقق</div>
      </div>
      <div class="step-line" id="sl-2"></div>
      <div class="step-item" id="si-3">
        <div class="step-circle" id="sc-3">3</div>
        <div class="step-label">البيانات</div>
      </div>
    </div>

    <div id="alert" class="alert"></div>

    {{-- ══ STEP 1: Phone ══ --}}
    <div id="step1">
      <div class="card-icon"><i class="fa fa-mobile-screen-button"></i></div>
      <div class="card-title">أدخل رقم هاتفك</div>
      <div class="card-sub">سنرسل رمز تحقق مكوّن من 6 أرقام على واتساب</div>

      <form onsubmit="sendOtp(event)">
        <div class="fgroup">
          <label class="flabel">رقم الهاتف <span class="req">*</span></label>
          <div style="position:relative">
            <span style="position:absolute;right:14px;top:50%;transform:translateY(-50%);font-size:18px">🇵🇸</span>
            <input class="finput" type="tel" id="phone"
                   placeholder="0599xxxxxx"
                   style="padding-right:44px"
                   autocomplete="tel" required>
          </div>
          <div class="ferr" id="err-phone"></div>
          <div class="hint"><i class="fa fa-whatsapp" style="color:#25d366"></i> يجب أن يكون الرقم مفعّلاً على واتساب</div>
        </div>
        <button type="submit" class="btn" id="send-btn">
          <i class="fa fa-paper-plane"></i> إرسال رمز التحقق
        </button>
      </form>

      <div class="link-row">لديك حساب؟ <a href="/storefront/login">تسجيل الدخول</a></div>
    </div>

    {{-- ══ STEP 2: OTP ══ --}}
    <div id="step2" style="display:none">
      <div class="card-icon" style="color:#25d366;background:rgba(37,211,102,.10)">
        <i class="fa-brands fa-whatsapp"></i>
      </div>
      <div class="card-title">أدخل رمز التحقق</div>
      <div class="card-sub">
        تم إرسال رمز مكوّن من 6 أرقام على واتساب إلى<br>
        <span class="phone-badge" id="phone-display"><i class="fa fa-phone"></i> <span id="phone-shown">—</span></span>
      </div>

      <form onsubmit="verifyOtp(event)">
        <div class="otp-row" id="otp-boxes">
          <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
          <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
          <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
          <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
          <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
          <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]">
        </div>
        <div class="ferr" id="err-otp" style="text-align:center;margin-bottom:8px"></div>

        <button type="submit" class="btn" id="verify-btn" disabled>
          <i class="fa fa-check-circle"></i> تحقق من الرمز
        </button>
      </form>

      <div class="resend-row">
        لم يصلك الرمز؟
        <button class="resend-btn" id="resend-btn" onclick="resendOtp()" disabled>
          إعادة إرسال <span id="resend-timer">(60)</span>
        </button>
      </div>
      <div style="text-align:center;margin-top:12px">
        <a href="#" onclick="goStep(1);return false" style="font-size:13px;color:var(--text-3)">
          <i class="fa fa-arrow-right"></i> تغيير رقم الهاتف
        </a>
      </div>
    </div>

    {{-- ══ STEP 3: Details ══ --}}
    <div id="step3" style="display:none">
      <div class="card-icon"><i class="fa fa-user-plus"></i></div>
      <div class="card-title">أكمل بياناتك</div>
      <div class="card-sub">خطوة أخيرة لإنشاء حسابك ✨</div>

      <form id="reg-form" onsubmit="doRegister(event)" novalidate>

        <div class="row2">
          <div class="fgroup">
            <label class="flabel">الاسم الأول <span class="req">*</span></label>
            <input class="finput" type="text" id="f_name" placeholder="محمد" autocomplete="given-name" required>
            <div class="ferr" id="err-fname"></div>
          </div>
          <div class="fgroup">
            <label class="flabel">الاسم الأخير <span class="req">*</span></label>
            <input class="finput" type="text" id="l_name" placeholder="أحمد" autocomplete="family-name" required>
            <div class="ferr" id="err-lname"></div>
          </div>
        </div>

        <div class="fgroup">
          <label class="flabel">البريد الإلكتروني <span style="color:var(--text-3);font-weight:400">(اختياري)</span></label>
          <input class="finput" type="email" id="email" placeholder="example@email.com" autocomplete="email">
          <div class="ferr" id="err-email"></div>
        </div>

        <div class="fgroup">
          <label class="flabel">كلمة المرور <span class="req">*</span></label>
          <div class="pw-wrap">
            <input class="finput" type="password" id="password" placeholder="••••••••"
                   autocomplete="new-password" minlength="6"
                   oninput="checkStrength(this.value)" required>
            <span class="eye" onclick="togglePass('password',this)"><i class="fa fa-eye"></i></span>
          </div>
          <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
          <div class="hint" id="strength-label"></div>
          <div class="ferr" id="err-pass"></div>
        </div>

        <div class="fgroup">
          <label class="flabel">تأكيد كلمة المرور <span class="req">*</span></label>
          <div class="pw-wrap">
            <input class="finput" type="password" id="confirm_password" placeholder="••••••••"
                   autocomplete="new-password" required>
            <span class="eye" onclick="togglePass('confirm_password',this)"><i class="fa fa-eye"></i></span>
          </div>
          <div class="ferr" id="err-confirm"></div>
        </div>

        <label class="agree-row">
          <input type="checkbox" id="agree" required>
          <span>
            أوافق على <a href="/storefront/terms" target="_blank">الشروط والأحكام</a>
            و<a href="/storefront/privacy" target="_blank">سياسة الخصوصية</a>
          </span>
        </label>

        <button type="submit" class="btn" id="reg-btn">
          <i class="fa fa-user-plus"></i> إنشاء الحساب
        </button>
      </form>
    </div>

  </div>
</main>

<footer>
  <span id="footer-store">المتجر</span> — جميع الحقوق محفوظة
</footer>

<script>
const API = window.location.origin + '/api/v1';
let verifiedPhone  = null;
let verifiedToken  = null;
let resendCountdown = null;

/* ── Init ── */
(async () => {
  try {
    if (localStorage.getItem('customer_token')) { window.location.href = '/'; return; }
    const cfg  = await fetch(`${API}/config`).then(r => r.json());
    const name = cfg.ecommerce_name || cfg.business_name || 'المتجر';
    document.getElementById('store-name').textContent   = name;
    document.getElementById('footer-store').textContent = name;
    document.title = 'إنشاء حساب — ' + name;
    if (cfg.logo_full_url)
      document.getElementById('logo-mark').innerHTML = `<img src="${cfg.logo_full_url}" alt="${name}">`;
  } catch(e){}
  initOtpBoxes();
})();

/* ── Step navigation ── */
function goStep(n) {
  [1,2,3].forEach(i => document.getElementById('step'+i).style.display = i===n ? 'block' : 'none');
  clearAlert();
  [1,2,3].forEach(i => {
    const si = document.getElementById('si-'+i);
    si.classList.remove('active','done');
    if (i < n)       si.classList.add('done');
    else if (i === n) si.classList.add('active');
    if (i < 3) {
      document.getElementById('sl-'+i).classList.toggle('done', i < n);
    }
    // checkmark for done steps
    const sc = document.getElementById('sc-'+i);
    if (i < n) sc.innerHTML = '<i class="fa fa-check" style="font-size:13px"></i>';
    else sc.textContent = i;
  });
}

/* ══ STEP 1 — Send OTP ══ */
async function sendOtp(e) {
  e.preventDefault();
  clearAlert();
  const phone = document.getElementById('phone').value.trim();
  const btn   = document.getElementById('send-btn');

  document.getElementById('err-phone').style.display = 'none';
  if (!phone) { showFieldErr('err-phone', 'أدخل رقم الهاتف'); return; }

  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> جاري الإرسال…';

  try {
    const res  = await fetch(`${API}/auth/send-whatsapp-otp`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ phone }),
    });
    const data = await res.json();

    if (res.ok) {
      verifiedPhone = phone;
      document.getElementById('phone-shown').textContent = phone;
      goStep(2);
      startResendTimer(60);
      document.querySelector('.otp-box')?.focus();
    } else {
      const msg = data.errors?.[0]?.message || 'حدث خطأ، أعد المحاولة';
      showFieldErr('err-phone', msg);
    }
  } catch(err) {
    showAlert('خطأ في الاتصال، تحقق من الإنترنت وأعد المحاولة.', 'err');
  }

  btn.disabled = false;
  btn.innerHTML = '<i class="fa fa-paper-plane"></i> إرسال رمز التحقق';
}

/* ══ STEP 2 — Verify OTP ══ */
function initOtpBoxes() {
  const boxes = document.querySelectorAll('.otp-box');
  boxes.forEach((box, i) => {
    box.addEventListener('input', e => {
      const v = e.target.value.replace(/\D/g,'');
      e.target.value = v.slice(-1);
      e.target.classList.toggle('filled', !!v);
      if (v && i < boxes.length - 1) boxes[i+1].focus();
      checkOtpComplete();
    });
    box.addEventListener('keydown', e => {
      if (e.key === 'Backspace' && !box.value && i > 0) boxes[i-1].focus();
    });
    box.addEventListener('paste', e => {
      e.preventDefault();
      const pasted = e.clipboardData.getData('text').replace(/\D/g,'').slice(0,6);
      boxes.forEach((b, j) => {
        b.value = pasted[j] || '';
        b.classList.toggle('filled', !!b.value);
      });
      if (pasted.length === 6) { boxes[5].focus(); checkOtpComplete(); }
    });
  });
}

function getOtpValue() {
  return [...document.querySelectorAll('.otp-box')].map(b => b.value).join('');
}

function checkOtpComplete() {
  const otp = getOtpValue();
  document.getElementById('verify-btn').disabled = otp.length < 6;
}

async function verifyOtp(e) {
  e.preventDefault();
  clearAlert();
  const otp = getOtpValue();
  if (otp.length < 6) return;

  document.getElementById('err-otp').style.display = 'none';
  const btn = document.getElementById('verify-btn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> جاري التحقق…';

  // Mark boxes as loading
  document.querySelectorAll('.otp-box').forEach(b => b.classList.remove('err'));

  try {
    const res  = await fetch(`${API}/auth/verify-whatsapp-otp`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ phone: verifiedPhone, otp }),
    });
    const data = await res.json();

    if (res.ok && data.verified) {
      verifiedToken = data.verified_token;
      clearInterval(resendCountdown);
      goStep(3);
      document.getElementById('f_name').focus();
    } else {
      const msg = data.errors?.[0]?.message || 'رمز غير صحيح';
      showFieldErr('err-otp', msg);
      document.querySelectorAll('.otp-box').forEach(b => { b.classList.add('err'); b.value = ''; b.classList.remove('filled'); });
      document.querySelector('.otp-box')?.focus();
      btn.disabled = false;
      btn.innerHTML = '<i class="fa fa-check-circle"></i> تحقق من الرمز';
    }
  } catch(err) {
    showAlert('خطأ في الاتصال.', 'err');
    btn.disabled = false;
    btn.innerHTML = '<i class="fa fa-check-circle"></i> تحقق من الرمز';
  }
}

async function resendOtp() {
  if (!verifiedPhone) return;
  const btn = document.getElementById('resend-btn');
  btn.disabled = true;
  btn.textContent = 'جاري الإرسال…';

  try {
    const res = await fetch(`${API}/auth/send-whatsapp-otp`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ phone: verifiedPhone }),
    });
    const data = await res.json();
    if (res.ok) {
      showAlert('تم إعادة إرسال الرمز على واتساب ✓', 'ok');
      // Clear OTP boxes
      document.querySelectorAll('.otp-box').forEach(b => { b.value=''; b.classList.remove('filled','err'); });
      document.querySelector('.otp-box')?.focus();
      startResendTimer(60);
    } else {
      const wait = data.errors?.[0]?.message || 'أعد المحاولة لاحقاً';
      showAlert(wait, 'warn');
      startResendTimer(30);
    }
  } catch(e) {
    showAlert('خطأ في الاتصال.', 'err');
    btn.disabled = false;
  }
}

function startResendTimer(seconds) {
  clearInterval(resendCountdown);
  const btn   = document.getElementById('resend-btn');
  const timer = document.getElementById('resend-timer');
  let left    = seconds;
  btn.disabled = true;
  timer.textContent = `(${left})`;
  resendCountdown = setInterval(() => {
    left--;
    timer.textContent = left > 0 ? `(${left})` : '';
    if (left <= 0) {
      clearInterval(resendCountdown);
      btn.disabled = false;
      btn.textContent = 'إعادة الإرسال';
    }
  }, 1000);
}

/* ══ STEP 3 — Register ══ */
function togglePass(id, el) {
  const inp = document.getElementById(id);
  inp.type  = inp.type === 'password' ? 'text' : 'password';
  el.innerHTML = inp.type === 'text' ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
}

function checkStrength(val) {
  const fill  = document.getElementById('strength-fill');
  const label = document.getElementById('strength-label');
  if (!val) { fill.style.width='0'; label.textContent=''; return; }
  let score = 0;
  if (val.length >= 8)           score++;
  if (/[A-Z]/.test(val))          score++;
  if (/[0-9]/.test(val))          score++;
  if (/[^A-Za-z0-9]/.test(val))   score++;
  const levels = [
    {w:'25%',bg:'#e74c3c',t:'ضعيفة'},
    {w:'50%',bg:var_amber(),t:'مقبولة'},
    {w:'75%',bg:'#3b82f6',t:'جيدة'},
    {w:'100%',bg:'#10b46a',t:'قوية'},
  ];
  const lv = levels[Math.max(0,score-1)];
  fill.style.width = lv.w; fill.style.background = lv.bg;
  label.textContent = 'قوة كلمة المرور: ' + lv.t;
  label.style.color = lv.bg;
}
function var_amber(){return '#f59e0b';}

async function doRegister(e) {
  e.preventDefault();
  clearErrors();
  if (!verifiedPhone || !verifiedToken) {
    showAlert('يجب التحقق من رقم الهاتف أولاً.', 'err'); return;
  }

  const fName  = document.getElementById('f_name').value.trim();
  const lName  = document.getElementById('l_name').value.trim();
  const email  = document.getElementById('email').value.trim();
  const pass   = document.getElementById('password').value;
  const conf   = document.getElementById('confirm_password').value;
  const agreed = document.getElementById('agree').checked;

  let valid = true;
  if (!fName)           { showFieldErr('err-fname',   'الاسم الأول مطلوب'); valid=false; }
  if (!lName)           { showFieldErr('err-lname',   'الاسم الأخير مطلوب'); valid=false; }
  if (pass.length < 6)  { showFieldErr('err-pass',    'كلمة المرور يجب أن تكون 6 أحرف على الأقل'); valid=false; }
  if (pass !== conf)    { showFieldErr('err-confirm', 'كلمة المرور وتأكيدها غير متطابقتين'); valid=false; }
  if (!agreed)          { showAlert('يجب الموافقة على الشروط للمتابعة.', 'err'); valid=false; }
  if (!valid) return;

  const btn = document.getElementById('reg-btn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> جاري إنشاء الحساب…';

  const body = { f_name: fName, l_name: lName, phone: verifiedPhone, password: pass };
  if (email) body.email = email;

  try {
    const res  = await fetch(`${API}/auth/registration`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(body),
    });
    const data = await res.json();

    if (res.ok && data.token) {
      localStorage.setItem('customer_token', data.token);
      if (data.user) localStorage.setItem('customer_user', JSON.stringify(data.user));
      showAlert('تم إنشاء الحساب بنجاح! مرحباً بك 🎉', 'ok');
      setTimeout(() => { window.location.href = '/'; }, 1200);
    } else {
      const errs = data.errors || [];
      const fieldMap = { f_name:'err-fname', l_name:'err-lname', phone:'err-phone', email:'err-email', password:'err-pass' };
      let shown = false;
      errs.forEach(er => { const fid = fieldMap[er.code]; if(fid){showFieldErr(fid,er.message);shown=true;} });
      if (!shown) showAlert(errs[0]?.message || 'حدث خطأ، تحقق من البيانات.', 'err');
    }
  } catch(err) {
    showAlert('خطأ في الاتصال.', 'err');
  }

  btn.disabled = false;
  btn.innerHTML = '<i class="fa fa-user-plus"></i> إنشاء الحساب';
}

/* ── Helpers ── */
function showAlert(msg, type) {
  const el = document.getElementById('alert');
  el.textContent = msg; el.className = `alert ${type}`; el.style.display = 'block';
  el.scrollIntoView({ behavior:'smooth', block:'nearest' });
}
function showFieldErr(id, msg) {
  const el = document.getElementById(id);
  if (el) { el.textContent = msg; el.style.display = 'block'; }
}
function clearAlert() { document.getElementById('alert').style.display = 'none'; }
function clearErrors() {
  clearAlert();
  ['err-fname','err-lname','err-phone','err-email','err-pass','err-confirm','err-otp'].forEach(id => {
    const el = document.getElementById(id); if (el) el.style.display = 'none';
  });
}
</script>
</body>
</html>
