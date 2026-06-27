<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import StoreLayout from '@/Layouts/StoreLayout.vue';

const props = defineProps({
    order:  Object,          // single order detail
    orders: Array,           // multiple orders list
    error:  String,
    q:      String,
});

const input = ref(props.q || '');

function search() {
    const v = input.value.trim();
    if (!v) return;
    router.get(route('track'), { q: v }, { preserveScroll: false });
}

// ── Timeline ──
const steps = [
    { status: 'pending_review', label: 'استلام الطلب',  icon: '📋', desc: 'تم استلام طلبك وهو قيد المراجعة' },
    { status: 'approved',       label: 'الموافقة',       icon: '✅', desc: 'وافقت الإدارة على التصميم' },
    { status: 'in_production',  label: 'التصنيع',        icon: '⚙️', desc: 'يتم تصنيع طلبك الآن' },
    { status: 'ready',          label: 'جاهز للتسليم',   icon: '📦', desc: 'جاهز للاستلام أو الشحن' },
    { status: 'delivered',      label: 'تم التسليم',     icon: '🎉', desc: 'تم تسليم طلبك بنجاح' },
];
const statusOrder = steps.map(s => s.status);

function stepState(step) {
    if (!props.order) return 'future';
    if (props.order.status === 'cancelled') return 'cancelled';
    const cur = statusOrder.indexOf(props.order.status);
    const idx = statusOrder.indexOf(step.status);
    if (idx < cur)   return 'done';
    if (idx === cur) return 'active';
    return 'future';
}

const statusTone = {
    pending_review: 'warn',
    approved:       'ok',
    in_production:  'info',
    ready:          'ok',
    delivered:      'ok',
    cancelled:      'bad',
};

// Search single order from list
function trackOne(ref) {
    router.get(route('track'), { q: ref }, { preserveScroll: false });
}
</script>

<template>
    <Head title="تتبع الطلب" />
    <StoreLayout>

        <!-- Page head -->
        <div class="wrap">
            <div class="phead">
                <div class="crumb">
                    <Link :href="route('home')">الرئيسية</Link>
                    <span class="sep">/</span>
                    <span>تتبع الطلب</span>
                </div>
                <span class="eyebrow rv">تتبع الطلب</span>
                <h1 class="rv d1">اعرف أين وصل طلبك</h1>
                <p class="rv d2">ابحث برقم الطلب أو رقم الهاتف أو البريد الإلكتروني</p>
            </div>
        </div>

        <div class="wrap tpage">

            <!-- ── Search box ── -->
            <div class="sbox rv">
                <div class="sinner" :class="{ focused: true }">
                    <span class="sico">🔍</span>
                    <input
                        v-model="input"
                        class="sinp"
                        placeholder="رقم الطلب / الهاتف / البريد الإلكتروني"
                        @keydown.enter="search"
                        autocomplete="off"
                        spellcheck="false"
                    >
                    <button class="sbtn" @click="search">تتبع</button>
                </div>
                <div class="shints">
                    <span class="hint-chip">📋 ELT-XXXXXXXX</span>
                    <span class="hint-sep">أو</span>
                    <span class="hint-chip">📞 رقم الهاتف</span>
                    <span class="hint-sep">أو</span>
                    <span class="hint-chip">📧 البريد الإلكتروني</span>
                </div>
            </div>

            <!-- ── Error ── -->
            <div v-if="error" class="err rv">
                <span>⚠️</span> {{ error }}
            </div>

            <!-- ── Multiple orders list ── -->
            <div v-if="orders && orders.length" class="result rv">
                <div class="multi-header">
                    <h2 class="multi-title">🔎 تم العثور على {{ orders.length }} طلب</h2>
                    <p class="multi-sub">اختر الطلب لعرض تفاصيله</p>
                </div>
                <div
                    v-for="o in orders"
                    :key="o.reference"
                    class="order-row"
                    @click="trackOne(o.reference)"
                >
                    <div class="or-ref lat">{{ o.reference }}</div>
                    <div class="or-meta">
                        <span class="or-dim">{{ o.created_at }}</span>
                        <span class="or-dot">·</span>
                        <span class="or-dim">{{ o.items_count }} عنصر</span>
                        <span class="or-dot">·</span>
                        <span class="or-price">₪{{ o.total }}</span>
                    </div>
                    <span class="badge" :class="statusTone[o.status]">{{ o.status_label }}</span>
                    <span class="or-arrow">←</span>
                </div>
            </div>

            <!-- ── Single order detail ── -->
            <div v-else-if="order" class="result rv">

                <!-- Header card -->
                <div class="rcard rhead-card">
                    <div class="rh-ref lat">{{ order.reference }}</div>
                    <div class="rh-meta">
                        <span class="rmuted">{{ order.items_count }} عنصر</span>
                        <span class="dot">·</span>
                        <span class="rmuted">₪{{ order.total }}</span>
                        <span class="dot">·</span>
                        <span class="rmuted">{{ order.created_at }}</span>
                    </div>
                    <div class="rh-row">
                        <span class="badge" :class="statusTone[order.status]">{{ order.status_label }}</span>
                        <span v-if="order.contact_name"  class="rh-contact">👤 {{ order.contact_name }}</span>
                        <span v-if="order.contact_phone" class="rh-contact">📞 {{ order.contact_phone }}</span>
                    </div>
                </div>

                <!-- Timeline -->
                <div v-if="order.status !== 'cancelled'" class="rcard">
                    <h3 class="rtitle">مراحل الطلب</h3>
                    <div class="timeline">
                        <div
                            v-for="step in steps"
                            :key="step.status"
                            class="tstep"
                            :class="stepState(step)"
                        >
                            <div class="tline"></div>
                            <div class="tnode">
                                <span class="ticon">{{ step.icon }}</span>
                            </div>
                            <div class="tinfo">
                                <div class="tlabel">{{ step.label }}</div>
                                <div class="tdesc">{{ step.desc }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="rcard cancelled-card">
                    <span class="cn-ico">❌</span>
                    <div>
                        <div class="cn-title">تم إلغاء هذا الطلب</div>
                        <div class="cn-sub">تواصل معنا إذا كان لديك استفسار.</div>
                    </div>
                </div>

                <!-- Delivery -->
                <div v-if="order.delivery_zone || order.eta" class="rcard dcard">
                    <div class="dico">🚚</div>
                    <div>
                        <div class="dlabel">معلومات التوصيل</div>
                        <div v-if="order.delivery_zone" class="dval">المنطقة: {{ order.delivery_zone }}</div>
                        <div v-if="order.eta" class="dval">الوقت المتوقع: {{ order.eta }}</div>
                    </div>
                </div>

                <!-- Help -->
                <div class="rcard help-card">
                    <span>💬</span>
                    <div>
                        <div class="hlabel">هل تحتاج مساعدة؟</div>
                        <div class="hsub">
                            <Link :href="route('contact')" style="color:var(--emerald-soft)">تواصل معنا</Link>
                            وسنساعدك في متابعة طلبك.
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── Empty state ── -->
            <div v-else-if="!error" class="nostate rv">
                <div class="ns-ico">📦</div>
                <div class="ns-title">ابحث عن طلبك</div>
                <div class="ns-sub">أدخل أي من المعلومات أدناه للعثور على طلبك</div>
                <div class="ns-methods">
                    <div class="ns-method">
                        <span class="ns-mico">📋</span>
                        <div>
                            <div class="ns-mlabel">رقم الطلب</div>
                            <div class="ns-msub">يبدأ بـ ELT- ويُرسَل في رسالة التأكيد</div>
                        </div>
                    </div>
                    <div class="ns-method">
                        <span class="ns-mico">📞</span>
                        <div>
                            <div class="ns-mlabel">رقم الهاتف</div>
                            <div class="ns-msub">الرقم المُسجَّل عند الطلب</div>
                        </div>
                    </div>
                    <div class="ns-method">
                        <span class="ns-mico">📧</span>
                        <div>
                            <div class="ns-mlabel">البريد الإلكتروني</div>
                            <div class="ns-msub">بريدك المُسجَّل في الحساب أو عند الطلب</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </StoreLayout>
</template>

<style scoped>
.tpage {
    padding-top: 32px;
    padding-bottom: 80px;
    max-width: 660px;
    margin-left: auto;
    margin-right: auto;
    display: flex;
    flex-direction: column;
    gap: 18px;
}

/* ── Search box ── */
.sinner {
    display: flex;
    align-items: center;
    background: var(--bg2);
    border: 1.5px solid var(--hair);
    border-radius: 16px;
    padding: 6px 6px 6px 14px;
    gap: 10px;
    transition: border-color .2s;
}
.sinner:focus-within { border-color: var(--emerald-soft); }
.sico { font-size: 18px; flex-shrink: 0; }
.sinp {
    flex: 1; background: none; border: none; outline: none;
    font-size: 15px; color: var(--ink); font-family: inherit;
    min-width: 0;
}
.sinp::placeholder { color: var(--muted); }
.sbtn {
    background: var(--emerald-deep); color: var(--on-emerald);
    border: none; border-radius: 11px;
    padding: 10px 22px; font-size: 14px; font-weight: 600;
    cursor: pointer; font-family: inherit;
    transition: opacity .2s; white-space: nowrap;
}
.sbtn:hover { opacity: .88; }

.shints {
    display: flex; align-items: center; gap: 8px;
    flex-wrap: wrap; margin-top: 10px; padding-right: 4px;
}
.hint-chip {
    font-size: 12px; color: var(--muted);
    background: var(--glass);
    border: 1px solid var(--hair);
    border-radius: 999px; padding: 3px 10px;
}
.hint-sep { font-size: 11px; color: var(--muted); }

/* ── Error ── */
.err {
    background: rgba(231,76,60,.08);
    border: 1px solid rgba(231,76,60,.2);
    border-radius: 14px; padding: 14px 18px;
    font-size: 14px; color: #ff7a6b;
    display: flex; gap: 10px; align-items: center;
}

/* ── Multiple orders ── */
.multi-header { margin-bottom: 4px; }
.multi-title  { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
.multi-sub    { font-size: 13px; color: var(--muted); }

.order-row {
    display: flex; align-items: center; gap: 12px;
    background: var(--bg2);
    border: 1.5px solid var(--hair);
    border-radius: 14px; padding: 14px 16px;
    cursor: pointer;
    transition: border-color .2s, transform .15s;
}
.order-row:hover { border-color: var(--emerald-soft); transform: translateX(-2px); }
.or-ref   { font-size: 14px; font-weight: 700; flex-shrink: 0; }
.or-meta  { display: flex; align-items: center; gap: 6px; flex: 1; flex-wrap: wrap; }
.or-dim   { font-size: 12px; color: var(--muted); }
.or-dot   { color: var(--hair); font-size: 10px; }
.or-price { font-size: 13px; font-weight: 600; }
.or-arrow { color: var(--muted); font-size: 16px; flex-shrink: 0; }

/* ── Result generic card ── */
.result { display: flex; flex-direction: column; gap: 14px; }
.rcard {
    background: var(--bg2);
    border: 1px solid var(--hair);
    border-radius: 18px; padding: 22px;
}

/* Header card */
.rh-ref     { font-size: 21px; font-weight: 800; letter-spacing: .06em; margin-bottom: 7px; }
.rh-meta    { display: flex; align-items: center; gap: 8px; color: var(--muted); font-size: 13px; margin-bottom: 13px; flex-wrap: wrap; }
.dot        { color: var(--hair); }
.rh-row     { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.rh-contact { font-size: 13px; color: var(--muted); }

/* Badges */
.badge { padding: 5px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; white-space: nowrap; }
.badge.warn { background: rgba(255,193,7,.15);  color: #e0a800; }
.badge.ok   { background: rgba(52,215,127,.15); color: var(--emerald-soft); }
.badge.info { background: rgba(33,150,243,.15); color: #42a5f5; }
.badge.bad  { background: rgba(231,76,60,.15);  color: #ff7a6b; }

/* Timeline */
.rtitle { font-size: 14px; font-weight: 700; margin-bottom: 16px; }
.timeline { display: flex; flex-direction: column; }
.tstep {
    display: grid;
    grid-template-columns: 2px 36px 1fr;
    gap: 0 12px;
    padding-bottom: 20px;
}
.tstep:last-child { padding-bottom: 0; }
.tline {
    width: 2px; background: var(--hair);
    height: 100%; margin: 18px auto 0;
    grid-row: 1; grid-column: 1;
}
.tstep:last-child .tline { display: none; }
.tstep.done   .tline { background: var(--emerald-soft); }
.tstep.active .tline { background: linear-gradient(to bottom, var(--emerald-soft), var(--hair)); }

.tnode {
    width: 36px; height: 36px; border-radius: 50%;
    border: 2px solid var(--hair); background: var(--bg);
    display: flex; align-items: center; justify-content: center;
    grid-column: 2; grid-row: 1; z-index: 1; transition: all .3s;
}
.tstep.done   .tnode { border-color: var(--emerald-soft); background: rgba(52,215,127,.12); }
.tstep.active .tnode { border-color: var(--emerald-soft); background: var(--emerald-deep); box-shadow: 0 0 0 4px rgba(52,215,127,.18); }
.ticon { font-size: 15px; }

.tinfo    { grid-column: 3; grid-row: 1; padding-top: 6px; }
.tlabel   { font-size: 14px; font-weight: 600; color: var(--muted); }
.tdesc    { font-size: 12px; color: var(--muted); margin-top: 2px; }
.tstep.done   .tlabel { color: var(--emerald-soft); }
.tstep.active .tlabel { color: var(--ink); font-weight: 700; }
.tstep.active .tdesc  { color: var(--ink); }

/* Cancelled */
.cancelled-card { display: flex; gap: 14px; align-items: flex-start; }
.cn-ico   { font-size: 28px; }
.cn-title { font-weight: 700; color: #ff7a6b; margin-bottom: 4px; }
.cn-sub   { font-size: 13px; color: var(--muted); }

/* Delivery */
.dcard  { display: flex; gap: 14px; align-items: flex-start; }
.dico   { font-size: 26px; }
.dlabel { font-weight: 600; margin-bottom: 6px; font-size: 14px; }
.dval   { font-size: 13px; color: var(--muted); }

/* Help */
.help-card { display: flex; gap: 14px; align-items: flex-start; font-size: 22px; }
.hlabel    { font-weight: 600; font-size: 14px; margin-bottom: 4px; }
.hsub      { font-size: 13px; color: var(--muted); }

/* ── Empty / instructions ── */
.nostate {
    text-align: center;
    padding: 50px 20px 20px;
}
.ns-ico   { font-size: 56px; margin-bottom: 14px; }
.ns-title { font-size: 20px; font-weight: 700; margin-bottom: 8px; }
.ns-sub   { color: var(--muted); font-size: 14px; margin-bottom: 28px; }

.ns-methods {
    display: flex; flex-direction: column; gap: 12px;
    text-align: right; max-width: 400px; margin: 0 auto;
}
.ns-method {
    display: flex; align-items: flex-start; gap: 14px;
    background: var(--bg2);
    border: 1.5px solid var(--hair);
    border-radius: 14px; padding: 14px 16px;
}
.ns-mico   { font-size: 24px; flex-shrink: 0; }
.ns-mlabel { font-size: 14px; font-weight: 600; margin-bottom: 3px; }
.ns-msub   { font-size: 12px; color: var(--muted); }

.rmuted { color: var(--muted); font-size: 13px; }
</style>
