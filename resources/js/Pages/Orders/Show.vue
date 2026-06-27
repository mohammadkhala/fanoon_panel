<script setup>
import { Head, Link } from '@inertiajs/vue3';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';

const props = defineProps({ order: Object });

const tone = {
    pending_review: 'warn',
    approved:       'ok',
    in_production:  'info',
    ready:          'ok',
    delivered:      'ok',
    cancelled:      'bad',
};

// Timeline steps in order
const timelineSteps = [
    { status: 'pending_review', label: 'قيد المراجعة',     icon: '📋', desc: 'تم استلام طلبك وهو قيد المراجعة' },
    { status: 'approved',       label: 'تمت الموافقة',      icon: '✅', desc: 'وافقت الإدارة على تصميمك' },
    { status: 'in_production',  label: 'قيد التصنيع',       icon: '⚙️', desc: 'يتم تصنيع طلبك الآن' },
    { status: 'ready',          label: 'جاهز للاستلام',     icon: '📦', desc: 'طلبك جاهز للاستلام أو الشحن' },
    { status: 'delivered',      label: 'تم التسليم',         icon: '🎉', desc: 'تم تسليم طلبك بنجاح' },
];

const statusOrder = timelineSteps.map(s => s.status);

function stepState(step) {
    if (props.order.status === 'cancelled') return 'cancelled';
    const cur = statusOrder.indexOf(props.order.status);
    const idx = statusOrder.indexOf(step.status);
    if (idx < cur)  return 'done';
    if (idx === cur) return 'active';
    return 'future';
}
</script>

<template>
    <Head :title="'الطلب ' + order.reference" />
    <CustomerLayout>

        <!-- Page header -->
        <div class="page-head rv">
            <div class="crumb">
                <Link :href="route('orders.index')">طلباتي</Link>
                <span class="sep">/</span>
                <span class="lat">{{ order.reference }}</span>
            </div>
            <div class="ph-row">
                <div>
                    <h1 class="ph-title lat">{{ order.reference }}</h1>
                    <p  class="ph-sub">{{ order.created_at }}</p>
                </div>
                <span class="badge" :class="tone[order.status]">{{ order.status_label }}</span>
            </div>
        </div>

        <!-- Layout grid -->
        <div class="ogrid">

            <!-- LEFT: timeline + items -->
            <div class="oleft">

                <!-- Status timeline -->
                <section class="card rv" v-if="order.status !== 'cancelled'">
                    <h3 class="card-title">مراحل الطلب</h3>
                    <div class="timeline">
                        <div
                            v-for="step in timelineSteps"
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
                </section>

                <section class="card rv" v-else>
                    <div class="cancelled-note">
                        <span class="cn-ico">❌</span>
                        <div>
                            <div class="cn-title">تم إلغاء الطلب</div>
                            <div class="cn-sub">تم إلغاء هذا الطلب. تواصل معنا إذا كان لديك سؤال.</div>
                        </div>
                    </div>
                </section>

                <!-- Order items -->
                <section class="card rv">
                    <h3 class="card-title">العناصر ({{ order.items.length }})</h3>
                    <div class="items">
                        <div v-for="(it, i) in order.items" :key="i" class="oitem">
                            <div class="oprev">
                                <img v-if="it.preview" :src="'/storage/' + it.preview" alt="تصميم">
                                <div v-else class="noimg">🛡</div>
                            </div>
                            <div class="omid">
                                <div class="otitle">{{ it.title }}</div>
                                <div class="osub">{{ it.quantity }} قطعة × ₪{{ it.unit_price }}</div>
                            </div>
                            <div class="oline">₪{{ it.line_total }}</div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- RIGHT: sidebar summary -->
            <aside class="oside rv">

                <!-- Totals -->
                <section class="card">
                    <h3 class="card-title">ملخّص المبالغ</h3>
                    <div class="srow"><span>المجموع الفرعي</span><b>₪{{ order.subtotal }}</b></div>
                    <div class="srow" v-if="order.delivery_fee > 0">
                        <span>رسوم التوصيل</span><b>₪{{ order.delivery_fee }}</b>
                    </div>
                    <div class="srow total"><span>الإجمالي</span><b>₪{{ order.total }}</b></div>
                </section>

                <!-- Contact info -->
                <section class="card">
                    <h3 class="card-title">معلومات التواصل</h3>
                    <div class="info-row">
                        <span class="il">الاسم</span>
                        <span>{{ order.contact_name }}</span>
                    </div>
                    <div class="info-row" v-if="order.contact_phone">
                        <span class="il">الهاتف</span>
                        <span class="lat">{{ order.contact_phone }}</span>
                    </div>
                    <div class="info-row" v-if="order.contact_email">
                        <span class="il">البريد</span>
                        <span class="lat">{{ order.contact_email }}</span>
                    </div>
                </section>

                <!-- Shipping info -->
                <section class="card" v-if="order.shipping_city || order.shipping_address">
                    <h3 class="card-title">عنوان التوصيل</h3>
                    <div class="info-row" v-if="order.shipping_city">
                        <span class="il">المدينة</span>
                        <span>{{ order.shipping_city }}</span>
                    </div>
                    <div class="info-row" v-if="order.shipping_neighborhood">
                        <span class="il">الحي</span>
                        <span>{{ order.shipping_neighborhood }}</span>
                    </div>
                    <div class="info-row" v-if="order.shipping_address">
                        <span class="il">الشارع</span>
                        <span>{{ order.shipping_address }}</span>
                    </div>
                    <div class="info-row" v-if="order.shipping_building">
                        <span class="il">المبنى</span>
                        <span>{{ order.shipping_building }}</span>
                    </div>
                </section>

                <!-- Notes -->
                <section class="card" v-if="order.notes">
                    <h3 class="card-title">ملاحظاتك</h3>
                    <p class="notes-txt">{{ order.notes }}</p>
                </section>

                <!-- Payment -->
                <section class="card">
                    <h3 class="card-title">طريقة الدفع</h3>
                    <div class="pay-row">
                        <span class="pay-ico">💵</span>
                        <span>الدفع عند الاستلام</span>
                    </div>
                </section>

            </aside>
        </div>
    </CustomerLayout>
</template>

<style scoped>
/* Breadcrumb + header */
.crumb        { font-size: 13px; color: var(--muted); margin-bottom: 8px; }
.crumb a      { color: var(--muted); text-decoration: none; }
.crumb a:hover{ color: var(--emerald-soft); }
.sep          { margin: 0 6px; }
.ph-row       { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
.ph-title     { font-size: 22px; font-weight: 700; margin-bottom: 2px; }
.ph-sub       { color: var(--muted); font-size: 13px; }
.page-head    { margin-bottom: 24px; }

/* Grid */
.ogrid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    align-items: start;
}
.oleft { display: flex; flex-direction: column; gap: 16px; }
.oside { display: flex; flex-direction: column; gap: 16px; position: sticky; top: 24px; }

/* Card */
.card {
    background: var(--bg2);
    border: 1px solid var(--hair);
    border-radius: 16px;
    padding: 18px;
}
.card-title { font-size: 14px; font-weight: 700; margin-bottom: 14px; letter-spacing: .02em; }

/* Timeline */
.timeline { display: flex; flex-direction: column; gap: 0; position: relative; }
.tstep {
    display: grid;
    grid-template-columns: 2px 36px 1fr;
    gap: 0 12px;
    padding-bottom: 20px;
    position: relative;
}
.tstep:last-child { padding-bottom: 0; }

/* Vertical line */
.tline {
    width: 2px;
    background: var(--hair);
    height: calc(100% - 0px);
    margin: 18px auto 0;
    grid-row: 1;
    grid-column: 1;
}
.tstep:last-child .tline { display: none; }
.tstep.done  .tline { background: var(--emerald-soft); }
.tstep.active .tline{ background: linear-gradient(to bottom, var(--emerald-soft), var(--hair)); }

/* Node */
.tnode {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid var(--hair);
    background: var(--bg);
    display: flex;
    align-items: center;
    justify-content: center;
    grid-column: 2;
    grid-row: 1;
    z-index: 1;
    transition: all .3s;
}
.tstep.done   .tnode { border-color: var(--emerald-soft); background: rgba(52,215,127,.12); }
.tstep.active .tnode { border-color: var(--emerald-soft); background: var(--emerald-deep); box-shadow: 0 0 0 4px rgba(52,215,127,.2); }
.ticon { font-size: 15px; }

/* Info */
.tinfo { grid-column: 3; grid-row: 1; padding-top: 6px; }
.tlabel { font-size: 14px; font-weight: 600; color: var(--muted); }
.tdesc  { font-size: 12px; color: var(--muted); margin-top: 2px; }
.tstep.done   .tlabel { color: var(--emerald-soft); }
.tstep.active .tlabel { color: var(--ink); font-weight: 700; }
.tstep.active .tdesc  { color: var(--ink); }

/* Cancelled note */
.cancelled-note { display: flex; gap: 14px; align-items: flex-start; }
.cn-ico   { font-size: 28px; }
.cn-title { font-weight: 700; margin-bottom: 4px; color: #ff7a6b; }
.cn-sub   { font-size: 13px; color: var(--muted); }

/* Items */
.items { display: flex; flex-direction: column; gap: 10px; }
.oitem {
    display: flex;
    gap: 14px;
    align-items: center;
    padding: 12px;
    background: var(--bg);
    border: 1px solid var(--hair);
    border-radius: 12px;
}
.oprev {
    width: 72px; height: 88px;
    border-radius: 8px;
    overflow: hidden;
    background: var(--glass);
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.oprev img { width: 100%; height: 100%; object-fit: cover; }
.noimg     { font-size: 26px; }
.omid      { flex: 1; }
.otitle    { font-weight: 600; font-size: 14px; margin-bottom: 4px; }
.osub      { color: var(--muted); font-size: 13px; }
.oline     { font-weight: 700; font-size: 15px; white-space: nowrap; }

/* Summary rows */
.srow {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    font-size: 13px;
    color: var(--muted);
    border-bottom: 1px solid var(--hair);
}
.srow:last-child { border-bottom: none; }
.srow b    { color: var(--ink); }
.srow.total{ font-size: 15px; font-weight: 700; color: var(--ink); }

/* Info rows */
.info-row { display: flex; flex-direction: column; gap: 2px; margin-bottom: 10px; }
.info-row:last-child { margin-bottom: 0; }
.il        { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; }
.info-row span:not(.il) { font-size: 14px; font-weight: 500; }

/* Notes */
.notes-txt { font-size: 13px; color: var(--muted); line-height: 1.7; }

/* Payment */
.pay-row { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 500; }
.pay-ico { font-size: 20px; }

/* Badges */
.badge { padding: 5px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
.badge.warn { background: rgba(255,193,7,.15);  color: #e0a800; }
.badge.ok   { background: rgba(52,215,127,.15); color: var(--emerald-soft); }
.badge.info { background: rgba(33,150,243,.15); color: #42a5f5; }
.badge.bad  { background: rgba(231,76,60,.15);  color: #ff7a6b; }

/* Responsive */
@media (max-width: 720px) {
    .ogrid { grid-template-columns: 1fr; }
    .oside { position: static; }
}
</style>
