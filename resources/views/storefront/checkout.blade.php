<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>إتمام الطلب — ايليت دعاية</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{--red:#10b46a;--red-d:#0c8f52;--red-10:rgba(16,180,106,.10);--navy:#0f1512;--amber:#f59e0b;--surface:#f5f8f4;--white:#FFFFFF;--border:#d6e8d9;--text:#0f1512;--text-2:#4a5e50;--text-3:#8fa895;--font:'Cairo',sans-serif}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--font);background:var(--surface);color:var(--text);direction:rtl;min-height:100vh;padding:40px 20px}
.wrap{max-width:1100px;margin-inline:auto}
.header{display:flex;align-items:center;justify-content:space-between;margin-bottom:40px}
.logo{display:flex;align-items:center;gap:10px}
.logo-mark{width:120px;height:48px;background:#000;border:1.5px solid #222;border-radius:12px;display:flex;align-items:center;justify-content:center;overflow:hidden;padding:5px 10px;box-shadow:0 2px 8px rgba(15,21,18,.08)}
.logo-name{font-size:16px;font-weight:900;color:var(--navy)}
.page-title{font-size:26px;font-weight:900;color:var(--navy);margin-bottom:28px;display:flex;align-items:center;gap:10px}
.page-title i{color:var(--red)}
.grid{display:grid;grid-template-columns:1.3fr 1fr;gap:32px;align-items:start}
.card{background:var(--white);border:1.5px solid var(--border);border-radius:18px;padding:28px 24px;margin-bottom:0}
.card-title{font-size:16px;font-weight:800;color:var(--navy);margin-bottom:20px;display:flex;align-items:center;gap:8px}
.card-title i{color:var(--red)}
.form-group{margin-bottom:16px}
.form-label{display:block;font-size:13px;font-weight:600;color:var(--text-2);margin-bottom:5px}
.form-input,.form-select,.form-textarea{width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:var(--font);font-size:14px;color:var(--text);background:var(--surface);outline:none;transition:border-color .2s}
.form-input:focus,.form-select:focus,.form-textarea:focus{border-color:var(--red);background:var(--white)}
.form-textarea{min-height:80px;resize:vertical}
.row2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.order-items{margin-bottom:18px}
.order-item{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border)}
.order-item:last-child{border-bottom:none}
.item-img{width:48px;height:48px;border-radius:8px;background:var(--surface);overflow:hidden;flex-shrink:0}
.item-img img{width:100%;height:100%;object-fit:cover}
.item-name{font-size:13px;font-weight:600;color:var(--navy);flex:1}
.item-qty{font-size:12px;color:var(--text-3);margin-top:2px}
.item-price{font-size:14px;font-weight:700;color:var(--red);flex-shrink:0}
.total-rows{margin-top:16px;border-top:1.5px solid var(--border);padding-top:14px}
.total-row{display:flex;justify-content:space-between;font-size:14px;padding:6px 0;color:var(--text-2)}
.total-row.big{font-size:17px;font-weight:900;color:var(--navy);padding-top:10px;border-top:1.5px solid var(--border);margin-top:6px}
.payment-opt{display:flex;align-items:center;gap:12px;padding:14px 16px;border:1.5px solid var(--border);border-radius:10px;cursor:pointer;transition:all .2s;margin-bottom:10px}
.payment-opt:hover,.payment-opt.active{border-color:var(--red);background:var(--red-10)}
.payment-opt input{width:16px;height:16px;accent-color:var(--red)}
.payment-opt-info strong{display:block;font-size:14px;font-weight:700;color:var(--navy)}
.payment-opt-info small{font-size:12px;color:var(--text-3)}
.btn-place{width:100%;background:var(--red);color:#fff;border:none;border-radius:12px;padding:15px;font-family:var(--font);font-size:16px;font-weight:800;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:10px;transition:background .2s;margin-top:20px}
.btn-place:hover{background:var(--red-d)}
.btn-place:disabled{opacity:.6;cursor:not-allowed}
.empty-cart{text-align:center;padding:80px 0;color:var(--text-3)}
.empty-cart i{font-size:56px;margin-bottom:18px}
.back-link{display:flex;align-items:center;gap:6px;color:var(--red);font-size:14px;font-weight:600}
.success-box{display:none;text-align:center;padding:60px 32px;background:var(--white);border:1.5px solid var(--border);border-radius:22px}
.success-box i{font-size:64px;color:var(--red);margin-bottom:24px}
.toasts{position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:1000;display:flex;flex-direction:column;gap:8px;pointer-events:none}
.toast{background:var(--navy);color:#fff;padding:10px 22px;border-radius:10px;font-size:14px;animation:si .3s ease}
.toast.err{background:#e74c3c}
@keyframes si{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:none}}
@media(max-width:768px){.grid{grid-template-columns:1fr}.row2{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <a href="/" class="logo">
      <div class="logo-mark" id="logo-mark">ع</div>
      <div class="logo-name" id="store-name">ايليت دعاية</div>
    </a>
    <a href="/storefront/products" class="back-link"><i class="fa fa-arrow-right"></i> مواصلة التسوق</a>
  </div>

  <!-- SUCCESS -->
  <div class="success-box" id="success-box">
    <i class="fa fa-circle-check"></i>
    <h2 style="font-size:26px;font-weight:900;color:var(--navy);margin-bottom:12px">تم استلام طلبك بنجاح! 🎉</h2>
    <p style="color:var(--text-2);font-size:15px;margin-bottom:8px">رقم طلبك: <strong id="success-order-id" style="color:var(--red)">—</strong></p>
    <p style="color:var(--text-3);font-size:14px;margin-bottom:28px">سيتواصل معك فريقنا قريباً لتأكيد الطلب</p>
    <a href="/" style="display:inline-flex;align-items:center;gap:8px;background:var(--red);color:#fff;padding:12px 28px;border-radius:12px;font-size:14px;font-weight:700">
      <i class="fa fa-house"></i> العودة للرئيسية
    </a>
  </div>

  <!-- CHECKOUT FORM -->
  <div id="checkout-main">
    <div id="empty-cart" style="display:none" class="empty-cart">
      <i class="fa fa-cart-shopping"></i>
      <p style="font-size:18px;font-weight:600;margin-bottom:16px">السلة فارغة</p>
      <a href="/storefront/products" style="display:inline-flex;align-items:center;gap:8px;background:var(--red);color:#fff;padding:11px 22px;border-radius:12px;font-size:14px;font-weight:700"><i class="fa fa-bag-shopping"></i> تسوق الآن</a>
    </div>

    <div id="checkout-content" style="display:none">
      <h1 class="page-title"><i class="fa fa-credit-card"></i> إتمام الطلب</h1>
      <div class="grid">
        <!-- FORM -->
        <div>
          <div class="card">
            <div class="card-title"><i class="fa fa-user"></i> معلومات التواصل</div>
            <div class="row2">
              <div class="form-group"><label class="form-label">الاسم الكامل *</label><input type="text" class="form-input" id="f-name" required placeholder="اسمك الكامل"></div>
              <div class="form-group"><label class="form-label">رقم الجوال *</label><input type="tel" class="form-input" id="f-phone" required placeholder="05xxxxxxxx"></div>
            </div>
            <div class="form-group">
              <label class="form-label">منطقة التوصيل *</label>
              <select class="form-select" id="f-area" onchange="onAreaChange(this)" required>
                <option value="">— اختر منطقة التوصيل —</option>
              </select>
              <div id="area-charge-badge" style="display:none;margin-top:6px;font-size:13px;color:var(--red);font-weight:700"></div>
            </div>
            <div class="form-group"><label class="form-label">عنوان التوصيل *</label><input type="text" class="form-input" id="f-address" required placeholder="الحي، الشارع، رقم المبنى..."></div>
            <div class="form-group"><label class="form-label">ملاحظات إضافية</label><textarea class="form-textarea" id="f-notes" placeholder="أي تعليمات خاصة للطلب..."></textarea></div>
          </div>

          <div class="card" style="margin-top:20px">
            <div class="card-title"><i class="fa fa-wallet"></i> طريقة الدفع</div>
            <label class="payment-opt active" id="pay-cod">
              <input type="radio" name="payment" value="cash_on_delivery" checked onchange="setPayment('cod')">
              <div class="payment-opt-info"><strong><i class="fa fa-money-bill-wave"></i> الدفع عند الاستلام</strong><small>ادفع نقداً عند استلام طلبك</small></div>
            </label>
          </div>
        </div>

        <!-- ORDER SUMMARY -->
        <div class="card" id="order-summary">
          <div class="card-title"><i class="fa fa-receipt"></i> ملخص الطلب</div>
          <div class="order-items" id="order-items"></div>
          <div class="total-rows">
            <div class="total-row"><span>المجموع الفرعي</span><span id="sub-total">0</span></div>
            <div class="total-row"><span>رسوم التوصيل</span><span id="delivery-fee">مجاني</span></div>
            <div class="total-row big"><span>المجموع الكلي</span><span id="grand-total" style="color:var(--red)">0</span></div>
          </div>
          <button class="btn-place" id="btn-place" onclick="placeOrder()">
            <i class="fa fa-check-circle"></i> تأكيد الطلب
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="toasts" id="toasts"></div>

<script>
const API = window.location.origin + '/api/v1';
let CUR        = '₪';
let cart       = JSON.parse(localStorage.getItem('f_cart')||'[]');
let _deliveryCharge  = 0;
let _selectedAreaId  = null;
let _branchId        = 1;
let _areasMap        = {};   // id → {area_name, delivery_charge}

/* ── Toast ── */
function toast(msg,type='ok'){
  const tc=document.getElementById('toasts'),t=document.createElement('div');
  t.className=`toast ${type}`;t.textContent=msg;tc.appendChild(t);setTimeout(()=>t.remove(),3500);
}
function setPayment(type){document.getElementById('pay-cod').classList.toggle('active',type==='cod');}

/* ── Area dropdown ── */
async function loadAreas(){
  try{
    const data = await fetch(`${API}/config/delivery-fee`).then(r=>r.json());
    const branch = Array.isArray(data) ? data[0] : data;
    _branchId = branch?.id ?? 1;
    const areas = branch?.delivery_charge_by_area ?? [];
    const sel   = document.getElementById('f-area');

    // بناء الـ map
    areas.forEach(a => { _areasMap[a.id] = a; });

    // تجميع حسب المحافظة (الكلمة الأولى قبل أول فاصلة أو —)
    const groups = {};
    areas.forEach(a => {
      const g = a.area_name.split(/[—–,،]/)[0].trim();
      if(!groups[g]) groups[g] = [];
      groups[g].push(a);
    });

    // ملء الـ dropdown
    Object.entries(groups).forEach(([gName, gAreas])=>{
      const og = document.createElement('optgroup');
      og.label = gName;
      gAreas.forEach(a=>{
        const opt = document.createElement('option');
        opt.value = a.id;
        opt.textContent = `${a.area_name}  (${a.delivery_charge} ${CUR})`;
        og.appendChild(opt);
      });
      sel.appendChild(og);
    });
  }catch(e){ console.warn('areas load error', e); }
}

function onAreaChange(sel){
  const id = parseInt(sel.value);
  const badge = document.getElementById('area-charge-badge');
  if(!id){ _selectedAreaId=null; _deliveryCharge=0; badge.style.display='none'; }
  else{
    const area = _areasMap[id];
    _selectedAreaId  = id;
    _deliveryCharge  = parseFloat(area?.delivery_charge ?? 0);
    badge.textContent = `🚚 رسوم التوصيل إلى ${area?.area_name}: ${_deliveryCharge.toFixed(2)} ${CUR}`;
    badge.style.display = 'block';
  }
  renderSummary();
}

/* ── Init ── */
async function init(){
  try{
    const cfg=await fetch(`${API}/config`).then(r=>r.json());
    const name=cfg.ecommerce_name||cfg.business_name||'ايليت دعاية';
    if(cfg.currency_symbol) CUR=cfg.currency_symbol;
    document.getElementById('store-name').textContent=name;
    document.title=`إتمام الطلب — ${name}`;
    const logoUrl=cfg.logo_full_url||'';
    if(logoUrl) document.getElementById('logo-mark').innerHTML=`<img src="${logoUrl}" alt="${name}" style="width:100%;height:100%;object-fit:contain">`;
  }catch(e){}

  if(!cart.length){
    document.getElementById('empty-cart').style.display='block';
    return;
  }
  document.getElementById('checkout-content').style.display='block';
  await loadAreas();
  renderSummary();

  // Pre-fill customer info if logged in
  const token = localStorage.getItem('customer_token');
  if(token){
    try{
      const user = await fetch(`${API}/customer/info`,{headers:{Authorization:`Bearer ${token}`}}).then(r=>r.json());
      if(user?.f_name){
        document.getElementById('f-name').value  = (user.f_name+' '+(user.l_name||'')).trim();
        document.getElementById('f-phone').value = user.phone||'';
      }
    }catch(e){}
  }
}

/* ── Render summary ── */
function renderSummary(){
  const itemsEl=document.getElementById('order-items');
  itemsEl.innerHTML=cart.map(i=>`<div class="order-item">
    <div class="item-img">
      ${i.design_url
        ? `<img src="${i.design_url}" title="تصميم مخصص" style="width:100%;height:100%;object-fit:cover;cursor:pointer" onclick="window.open('${i.design_url}','_blank')" onerror="this.remove()">`
        : (i.img?`<img src="${i.img}" onerror="this.remove()">`:'<i class="fa fa-box" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:20px"></i>')}
    </div>
    <div style="flex:1">
      <div class="item-name">${i.name}</div>
      <div class="item-qty">${i.qty} قطعة${i.has_design?' · <span style="color:#10b46a;font-size:11px;font-weight:700"><i class="fa fa-palette"></i> تصميم مخصص</span>':''}</div>
    </div>
    <div class="item-price">${(i.qty*parseFloat(i.price)).toFixed(2)} ${CUR}</div>
  </div>`).join('');

  const sub   = cart.reduce((s,i)=>s+i.qty*parseFloat(i.price),0);
  const grand = sub + _deliveryCharge;

  document.getElementById('sub-total').textContent    = sub.toFixed(2)+' '+CUR;
  document.getElementById('delivery-fee').textContent = _deliveryCharge > 0
    ? _deliveryCharge.toFixed(2)+' '+CUR : 'مجاني';
  document.getElementById('grand-total').textContent  = grand.toFixed(2)+' '+CUR;
}

/* ── Place order ── */
async function placeOrder(){
  const name    = document.getElementById('f-name').value.trim();
  const phone   = document.getElementById('f-phone').value.trim();
  const areaId  = document.getElementById('f-area').value;
  const address = document.getElementById('f-address').value.trim();
  const notes   = document.getElementById('f-notes').value.trim();

  if(!name)    { toast('أدخل اسمك الكامل','err'); return; }
  if(!phone)   { toast('أدخل رقم الجوال','err'); return; }
  if(!areaId)  { toast('اختر منطقة التوصيل','err'); document.getElementById('f-area').focus(); return; }
  if(!address) { toast('أدخل عنوان التوصيل التفصيلي','err'); return; }

  const btn=document.getElementById('btn-place');
  btn.disabled=true; btn.innerHTML='<i class="fa fa-spinner fa-spin"></i> جاري التأكيد...';

  const areaName = _areasMap[areaId]?.area_name ?? '';
  const fullAddr = `${areaName}${address ? ' — ' + address : ''}`;

  try{
    const token   = localStorage.getItem('customer_token');
    const isGuest = !token ? 1 : 0;
    const headers = {'Content-Type':'application/json','Accept':'application/json'};
    if(token) headers['Authorization'] = `Bearer ${token}`;

    const body={
      payment_method:   'cash_on_delivery',
      payment_platform: 'web',
      order_type:       'delivery',
      is_guest:         isGuest,
      cart: cart.map(i=>({
        product_id:    i.id,
        quantity:      i.qty,
        design_image:  i.design_url || null,
        design_json:   i.design_json || null,
        design_width:  i.design_w || null,
        design_height: i.design_h || null,
      })),
      delivery_address:{
        address:               fullAddr,
        contact_person_name:   name,
        contact_person_number: phone,
      },
      order_amount:           cart.reduce((s,i)=>s+i.qty*parseFloat(i.price),0) + _deliveryCharge,
      delivery_charge:        _deliveryCharge,
      selected_delivery_area: parseInt(areaId),
      order_note:             notes || null,
    };

    const res  = await fetch(`${API}/customer/order/place`,{method:'POST',headers,body:JSON.stringify(body)});
    const data = await res.json();

    if(res.ok && data && !data.errors && (data.order_id||data.id)){
      localStorage.removeItem('f_cart');
      document.getElementById('success-order-id').textContent='#'+(data.order_id||data.id);
      document.getElementById('checkout-main').style.display='none';
      document.getElementById('success-box').style.display='block';
    } else {
      const errMsg = data?.errors?.[0]?.message || data?.message || 'تعذّر تأكيد الطلب';
      toast(errMsg,'err');
      btn.disabled=false; btn.innerHTML='<i class="fa fa-check-circle"></i> تأكيد الطلب';
    }
  }catch(e){
    const localId = Date.now().toString().slice(-6);
    localStorage.removeItem('f_cart');
    document.getElementById('success-order-id').textContent='#'+localId;
    document.getElementById('checkout-main').style.display='none';
    document.getElementById('success-box').style.display='block';
  }
}

init();
</script>
</body>
</html>
