<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title id="page-title">{{ $type === 'privacy' ? 'سياسة الخصوصية' : 'الشروط والأحكام' }} — ايليت دعاية</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{--red:#10b46a;--navy:#0f1512;--surface:#f5f8f4;--white:#FFFFFF;--border:#d6e8d9;--text:#0f1512;--text-2:#4a5e50;--text-3:#8fa895;--max-w:800px;--font:'Cairo',sans-serif}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--font);background:var(--surface);color:var(--text);direction:rtl;padding:40px 20px}
.wrap{max-width:var(--max-w);margin-inline:auto}
.header{display:flex;align-items:center;justify-content:space-between;margin-bottom:40px}
.logo{display:flex;align-items:center;gap:10px}
.logo-mark{width:120px;height:48px;background:#000;border:1.5px solid #222;border-radius:12px;display:flex;align-items:center;justify-content:center;overflow:hidden;padding:5px 10px;box-shadow:0 2px 8px rgba(15,21,18,.08)}
.logo-name{font-size:16px;font-weight:900;color:var(--navy)}
.back-link{display:flex;align-items:center;gap:6px;color:var(--red);font-size:14px;font-weight:600}
h1{font-size:28px;font-weight:900;color:var(--navy);margin-bottom:8px}
.date{font-size:13px;color:var(--text-3);margin-bottom:32px}
.content{background:var(--white);border:1.5px solid var(--border);border-radius:18px;padding:40px;line-height:1.9;font-size:15px;color:var(--text-2)}
.content h2{font-size:18px;font-weight:800;color:var(--navy);margin:28px 0 12px}
.content p{margin-bottom:14px}
.empty-note{text-align:center;padding:60px 0;color:var(--text-3)}
.empty-note i{font-size:48px;margin-bottom:16px}
.footer-mini{text-align:center;margin-top:32px;font-size:13px;color:var(--text-3)}
.footer-mini a{color:var(--red)}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <a href="/" class="logo">
      <div class="logo-mark" id="logo-mark">ع</div>
      <div class="logo-name" id="store-name">ايليت دعاية</div>
    </a>
    <a href="/" class="back-link"><i class="fa fa-arrow-right"></i> الرئيسية</a>
  </div>

  <h1 id="page-h1">{{ $type === 'privacy' ? 'سياسة الخصوصية' : 'الشروط والأحكام' }}</h1>
  <div class="date">آخر تحديث: {{ now()->format('Y-m-d') }}</div>

  <div class="content" id="policy-content">
    <div class="empty-note" id="loading-note"><i class="fa fa-spinner fa-spin"></i><p>جاري التحميل...</p></div>
  </div>

  <div class="footer-mini">
    <a href="/">الرئيسية</a> · <a href="/storefront/products">المنتجات</a> · <a href="/storefront/contact">اتصل بنا</a>
  </div>
</div>

<script>
const API = window.location.origin + '/api/v1';
const POLICY_TYPE = '{{ $type }}';

async function init(){
  try{
    const cfg=await fetch(`${API}/config`).then(r=>r.json());
    const name=cfg.ecommerce_name||cfg.business_name||'ايليت دعاية';
    document.getElementById('store-name').textContent=name;
    const logoUrl=cfg.logo_full_url||'';
    if(logoUrl) document.getElementById('logo-mark').innerHTML=`<img src="${logoUrl}" alt="${name}" style="width:100%;height:100%;object-fit:contain">`;

    const key = POLICY_TYPE==='privacy' ? 'privacy_policy' : 'terms_and_conditions';
    const text = cfg[key];
    const el = document.getElementById('policy-content');
    if(text&&text.trim()){
      el.innerHTML = text;
    } else {
      el.innerHTML = `<div class="empty-note"><i class="fa fa-file-alt"></i><p>لم يتم إضافة ${POLICY_TYPE==='privacy'?'سياسة الخصوصية':'الشروط والأحكام'} بعد.<br>يمكن إضافتها من لوحة التحكم ← إعدادات الأعمال.</p></div>`;
    }
  }catch(e){
    document.getElementById('policy-content').innerHTML='<div class="empty-note"><i class="fa fa-exclamation-triangle"></i><p>تعذّر التحميل</p></div>';
  }
}
init();
</script>
</body>
</html>
