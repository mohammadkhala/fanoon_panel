<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title id="page-title">اتصل بنا — ايليت دعاية</title>
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
.logo-name{font-size:17px;font-weight:900;color:var(--navy);line-height:1.2}
.logo-tag{font-size:11px;color:var(--text-3)}
.bc{background:var(--white);border-bottom:1px solid var(--border);padding:12px 0;font-size:13px;color:var(--text-3)}
.bc-inner{display:flex;align-items:center;gap:8px}
.bc-inner a{color:var(--text-3);transition:color .2s}
.bc-inner a:hover{color:var(--red)}
.bc-inner i{font-size:10px}

/* HERO */
.contact-hero{background:var(--navy);padding:60px 0 50px;color:#fff;text-align:center}
.contact-hero h1{font-size:36px;font-weight:900;margin-bottom:12px}
.contact-hero p{font-size:16px;color:rgba(255,255,255,.7);max-width:500px;margin-inline:auto}

/* GRID */
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:40px;padding:60px 0}
.contact-card{background:var(--white);border:1.5px solid var(--border);border-radius:var(--r-lg);padding:36px 32px;box-shadow:var(--sh-sm)}
.card-title{font-size:20px;font-weight:800;color:var(--navy);margin-bottom:24px;display:flex;align-items:center;gap:10px}
.card-title i{color:var(--red)}

/* INFO ITEMS */
.info-item{display:flex;align-items:flex-start;gap:16px;padding:16px 0;border-bottom:1px solid var(--border)}
.info-item:last-child{border-bottom:none}
.info-icon{width:44px;height:44px;background:var(--red-10);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--red);font-size:17px}
.info-label{font-size:12px;color:var(--text-3);font-weight:500;margin-bottom:3px}
.info-val{font-size:15px;font-weight:600;color:var(--navy)}
.info-val a{color:var(--red)}
.info-val a:hover{color:var(--red-d)}

/* FORM */
.form-group{margin-bottom:18px}
.form-label{display:block;font-size:13px;font-weight:600;color:var(--text-2);margin-bottom:6px}
.form-input,.form-textarea{width:100%;padding:12px 16px;border:1.5px solid var(--border);border-radius:10px;font-family:var(--font);font-size:14px;color:var(--text);background:var(--surface);outline:none;transition:border-color .2s}
.form-input:focus,.form-textarea:focus{border-color:var(--red);background:var(--white)}
.form-textarea{min-height:130px;resize:vertical}
.btn-submit{width:100%;background:var(--red);color:#fff;border:none;border-radius:12px;padding:14px;font-family:var(--font);font-size:15px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .2s}
.btn-submit:hover{background:var(--red-d)}
.form-msg{display:none;padding:12px 16px;border-radius:10px;font-size:14px;font-weight:600;margin-bottom:16px;text-align:center}
.form-msg.ok{background:#d4f5e5;color:#0c8f52;border:1.5px solid #10b46a}
.form-msg.err{background:#fde8e8;color:#c0392b;border:1.5px solid #e74c3c}

/* SOCIAL */
.social-row{display:flex;gap:12px;margin-top:20px;flex-wrap:wrap}
.soc-btn{display:flex;align-items:center;gap:8px;padding:10px 18px;border-radius:10px;font-size:13px;font-weight:600;background:var(--surface);border:1.5px solid var(--border);color:var(--text-2);transition:all .2s}
.soc-btn:hover{background:var(--red-10);border-color:var(--red);color:var(--red)}
.soc-btn.wa{background:#25d366;color:#fff;border-color:#25d366}
.soc-btn.wa:hover{background:#1aa84e}

/* FOOTER MINI */
.footer-mini{background:var(--navy);color:rgba(255,255,255,.5);text-align:center;padding:20px;font-size:13px}
.footer-mini a{color:rgba(255,255,255,.5)}

.toasts{position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:1000;display:flex;flex-direction:column;gap:8px;pointer-events:none}
.toast{background:var(--navy);color:#fff;padding:10px 22px;border-radius:10px;font-size:14px;display:flex;align-items:center;gap:8px;box-shadow:var(--sh-md);animation:si .3s ease}
.toast.err{background:#e74c3c}
@keyframes si{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:none}}
@media(max-width:768px){.contact-grid{grid-template-columns:1fr}.contact-hero h1{font-size:26px}}
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
      <div style="margin-inline-start:auto;display:flex;gap:10px">
        <a href="/" style="display:flex;align-items:center;gap:6px;color:var(--text-2);font-size:14px;font-weight:600"><i class="fa fa-house"></i> الرئيسية</a>
        <a href="/storefront/products" style="display:flex;align-items:center;gap:6px;color:var(--text-2);font-size:14px;font-weight:600"><i class="fa fa-border-all"></i> المنتجات</a>
      </div>
    </div>
  </div>
</header>

<div class="bc">
  <div class="wrap">
    <div class="bc-inner">
      <a href="/"><i class="fa fa-house"></i> الرئيسية</a>
      <i class="fa fa-chevron-left"></i>
      <span>اتصل بنا</span>
    </div>
  </div>
</div>

<div class="contact-hero">
  <div class="wrap">
    <h1><i class="fa fa-headset" style="color:var(--red)"></i> نحن هنا لمساعدتك</h1>
    <p>تواصل معنا لأي استفسار عن منتجاتنا أو طلبات الطباعة والتصميم</p>
  </div>
</div>

<div class="wrap">
  <div class="contact-grid">

    <!-- CONTACT INFO -->
    <div class="contact-card">
      <div class="card-title"><i class="fa fa-circle-info"></i> معلومات التواصل</div>

      <div class="info-item">
        <div class="info-icon"><i class="fa fa-map-marker-alt"></i></div>
        <div>
          <div class="info-label">العنوان</div>
          <div class="info-val" id="c-addr">—</div>
        </div>
      </div>
      <div class="info-item">
        <div class="info-icon"><i class="fa fa-phone"></i></div>
        <div>
          <div class="info-label">الهاتف</div>
          <div class="info-val" id="c-phone">—</div>
        </div>
      </div>
      <div class="info-item">
        <div class="info-icon"><i class="fa fa-envelope"></i></div>
        <div>
          <div class="info-label">البريد الإلكتروني</div>
          <div class="info-val" id="c-email">—</div>
        </div>
      </div>
      <div class="info-item" id="c-hours-row" style="display:none">
        <div class="info-icon"><i class="fa fa-clock"></i></div>
        <div>
          <div class="info-label">ساعات العمل</div>
          <div class="info-val">السبت–الخميس: 8 ص – 6 م</div>
        </div>
      </div>

      <div class="social-row" id="c-social">
        <!-- تُملأ من API -->
      </div>
    </div>

    <!-- CONTACT FORM -->
    <div class="contact-card">
      <div class="card-title"><i class="fa fa-paper-plane"></i> أرسل لنا رسالة</div>
      <div class="form-msg" id="form-msg"></div>
      <form id="contact-form" onsubmit="submitForm(event)">
        <div class="form-group">
          <label class="form-label">الاسم *</label>
          <input type="text" class="form-input" name="name" required placeholder="اسمك الكامل">
        </div>
        <div class="form-group">
          <label class="form-label">رقم الجوال أو البريد</label>
          <input type="text" class="form-input" name="contact" placeholder="0599xxxxxx أو email@domain.com">
        </div>
        <div class="form-group">
          <label class="form-label">الموضوع *</label>
          <input type="text" class="form-input" name="subject" required placeholder="موضوع رسالتك">
        </div>
        <div class="form-group">
          <label class="form-label">الرسالة *</label>
          <textarea class="form-textarea" name="message" required placeholder="اكتب رسالتك هنا..."></textarea>
        </div>
        <button type="submit" class="btn-submit" id="submit-btn">
          <i class="fa fa-paper-plane"></i> إرسال الرسالة
        </button>
      </form>
    </div>

  </div>
</div>

<div class="footer-mini">
  <a href="/">الرئيسية</a> &nbsp;·&nbsp;
  <a href="/storefront/products">المنتجات</a> &nbsp;·&nbsp;
  <a href="/storefront/account">حسابي</a>
</div>

<div class="toasts" id="toasts"></div>

<script>
const API = window.location.origin + '/api/v1';

function toast(msg,type='ok'){const tc=document.getElementById('toasts'),t=document.createElement('div');t.className=`toast ${type}`;t.textContent=msg;tc.appendChild(t);setTimeout(()=>t.remove(),3000);}

async function init(){
  try{
    const cfg = await fetch(`${API}/config`).then(r=>r.json());
    const name = cfg.ecommerce_name||cfg.business_name||'ايليت دعاية';
    document.getElementById('store-name').textContent = name;
    document.title = `اتصل بنا — ${name}`;

    const logoUrl = cfg.logo_full_url||'';
    if(logoUrl) document.getElementById('logo-mark').innerHTML=`<img src="${logoUrl}" alt="${name}" style="width:100%;height:100%;object-fit:contain">`;

    const ph = cfg.ecommerce_phone||cfg.phone||'';
    if(ph) document.getElementById('c-phone').innerHTML=`<a href="tel:${ph}">${ph}</a>`;

    const em = cfg.ecommerce_email||cfg.email||'';
    if(em) document.getElementById('c-email').innerHTML=`<a href="mailto:${em}">${em}</a>`;

    const addr = cfg.ecommerce_address||cfg.address||'';
    if(addr) document.getElementById('c-addr').textContent=addr;
    document.getElementById('c-hours-row').style.display='flex';

    // Social
    const socialContainer = document.getElementById('c-social');
    const socialIconMap={'facebook':'fab fa-facebook-f','instagram':'fab fa-instagram','twitter':'fab fa-x-twitter','tiktok':'fab fa-tiktok','youtube':'fab fa-youtube','snapchat':'fab fa-snapchat','linkedin':'fab fa-linkedin-in','telegram':'fab fa-telegram','whatsapp':'fab fa-whatsapp'};
    if(Array.isArray(cfg.social_media_link)&&cfg.social_media_link.length){
      cfg.social_media_link.filter(s=>s.link&&s.status!=0).forEach(s=>{
        const nm=(s.name||'').toLowerCase();
        const iconClass=Object.entries(socialIconMap).find(([k])=>nm.includes(k))?.[1]||'fab fa-globe';
        const isWa=nm.includes('whatsapp');
        socialContainer.innerHTML+=`<a href="${s.link}" target="_blank" class="soc-btn${isWa?' wa':''}"><i class="${iconClass}"></i>${s.name||''}</a>`;
      });
    }
    if(cfg.whatsapp?.status&&cfg.whatsapp?.number){
      socialContainer.innerHTML=`<a href="https://wa.me/${cfg.whatsapp.number}" target="_blank" class="soc-btn wa"><i class="fab fa-whatsapp"></i> واتساب</a>`+socialContainer.innerHTML;
    }
    if(!socialContainer.innerHTML.trim()){
      socialContainer.style.display='none';
    }
  }catch(e){}
}

async function submitForm(e){
  e.preventDefault();
  const form = document.getElementById('contact-form');
  const btn  = document.getElementById('submit-btn');
  const msg  = document.getElementById('form-msg');
  const fd   = new FormData(form);
  const body = {
    name:    fd.get('name'),
    email:   fd.get('contact').includes('@')?fd.get('contact'):'visitor@contact.local',
    subject: fd.get('subject'),
    message: fd.get('message'),
  };
  btn.disabled=true; btn.innerHTML='<i class="fa fa-spinner fa-spin"></i> جاري الإرسال...';
  try{
    const res = await fetch(`${API}/contact-us`, {
      method:'POST',
      headers:{'Content-Type':'application/json','Accept':'application/json'},
      body: JSON.stringify(body)
    });
    if(res.ok||res.status===200||res.status===201){
      msg.className='form-msg ok'; msg.textContent='✓ تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.'; msg.style.display='block';
      form.reset();
      toast('تم الإرسال بنجاح ✓');
    } else {
      throw new Error();
    }
  }catch(err){
    msg.className='form-msg err'; msg.textContent='تعذّر الإرسال. يمكنك التواصل مباشرة عبر الهاتف أو الواتساب.'; msg.style.display='block';
  }
  btn.disabled=false; btn.innerHTML='<i class="fa fa-paper-plane"></i> إرسال الرسالة';
}

init();
</script>
</body>
</html>
