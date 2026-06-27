<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    company:       Object,
    contact:       Object,
    stats:         Object,
    recent_orders: Array,
});

/* ── Status helpers ── */
const toneMap  = { pending: 'warn', approved: 'ok', rejected: 'bad' };
const labelMap = { pending: 'بانتظار المراجعة', approved: 'معتمدة', rejected: 'مرفوضة' };
const orderTone = {
    pending_review: 'warn', approved: 'ok',
    in_production: 'info',  ready: 'ok',
    delivered: 'ok',        cancelled: 'bad',
};

/* ── Quick-review form ── */
const reviewForm = useForm({ status: props.company.status, review_notes: props.company.review_notes || '' });

function review(status) {
    reviewForm.status = status;
    reviewForm.patch(route('admin.companies.update', props.company.id), { preserveScroll: true });
}

/* ── Stat cards ── */
const statCards = [
    { key: 'total',         label: 'إجمالي الطلبات',  icon: '📋', accent: '#8b8b8b' },
    { key: 'pending',       label: 'قيد المراجعة',    icon: '⏳', accent: '#e0a800' },
    { key: 'in_production', label: 'قيد التصنيع',     icon: '⚙️', accent: '#42a5f5' },
    { key: 'delivered',     label: 'تم التسليم',       icon: '✅', accent: '#34d77f' },
    { key: 'cancelled',     label: 'ملغاة',            icon: '❌', accent: '#ff7a6b' },
    { key: 'revenue',       label: 'إيرادات مسلّمة',   icon: '💰', accent: '#a78bfa', currency: true },
];
</script>

<template>
    <Head :title="company.company_name + ' — الإدارة'" />
    <AdminLayout :title="company.company_name" subtitle="صفحة الشركة والإحصائيات">

        <!-- ── Breadcrumb ── -->
        <div class="crumb">
            <Link :href="route('admin.companies.index')">طلبات الشركات</Link>
            <span class="sep">›</span>
            <span>{{ company.company_name }}</span>
        </div>

        <!-- ── Hero card ── -->
        <div class="hero-card">
            <div class="hero-left">
                <div class="hav">{{ (company.company_name || '?').charAt(0) }}</div>
                <div class="hinfo">
                    <h1 class="hname">{{ company.company_name }}</h1>
                    <div class="hmeta">
                        <span v-if="company.city">📍 {{ company.city }}</span>
                        <span v-if="company.trade_license_no">🔢 {{ company.trade_license_no }}</span>
                        <span>📅 منذ {{ company.created_at }}</span>
                    </div>
                    <div class="hcontact" v-if="contact">
                        <span>👤 {{ contact.name }}</span>
                        <span class="lat">✉️ {{ contact.email }}</span>
                        <span class="lat" v-if="contact.phone">📞 {{ contact.phone }}</span>
                    </div>
                </div>
            </div>

            <div class="hero-right">
                <!-- Status badge -->
                <span class="badge lg" :class="toneMap[company.status]">
                    {{ labelMap[company.status] }}
                </span>

                <!-- Quick actions -->
                <div class="hacts">
                    <button
                        v-if="company.status !== 'approved'"
                        class="abtn ok"
                        :disabled="reviewForm.processing"
                        @click="review('approved')"
                    >✓ اعتماد</button>
                    <button
                        v-if="company.status !== 'rejected'"
                        class="abtn bad"
                        :disabled="reviewForm.processing"
                        @click="review('rejected')"
                    >✕ رفض</button>
                    <button
                        v-if="company.status !== 'pending'"
                        class="abtn neutral"
                        :disabled="reviewForm.processing"
                        @click="review('pending')"
                    >⏸ إعادة للمراجعة</button>
                </div>

                <!-- Trade license -->
                <a
                    v-if="company.trade_license_url"
                    :href="company.trade_license_url"
                    target="_blank"
                    rel="noopener"
                    class="lic-btn"
                >📄 عرض السجل التجاري ↗</a>
                <span v-else class="no-lic">⚠️ لم يُرفع سجل تجاري</span>
            </div>
        </div>

        <!-- ── Stats grid ── -->
        <div class="sgrid">
            <div
                v-for="s in statCards"
                :key="s.key"
                class="scard"
                :style="{ '--accent': s.accent }"
            >
                <div class="scard-top">
                    <div class="sico">{{ s.icon }}</div>
                    <div class="sval">
                        <template v-if="s.currency">₪{{ stats[s.key].toLocaleString('ar') }}</template>
                        <template v-else>{{ stats[s.key] }}</template>
                    </div>
                </div>
                <div class="slbl">{{ s.label }}</div>
                <div class="sbar"></div>
            </div>
        </div>

        <!-- ── Bottom grid: notes + orders ── -->
        <div class="bgrid">

            <!-- Left: company details + notes -->
            <div class="bside">

                <!-- Company details -->
                <div class="panel">
                    <h3 class="ptitle">معلومات الشركة</h3>
                    <div class="dlist">
                        <div class="drow" v-if="company.company_name">
                            <span class="dlbl">اسم الشركة</span>
                            <span>{{ company.company_name }}</span>
                        </div>
                        <div class="drow" v-if="company.trade_license_no">
                            <span class="dlbl">رقم السجل التجاري</span>
                            <span class="lat">{{ company.trade_license_no }}</span>
                        </div>
                        <div class="drow" v-if="company.city">
                            <span class="dlbl">المدينة</span>
                            <span>{{ company.city }}</span>
                        </div>
                        <div class="drow" v-if="company.address">
                            <span class="dlbl">العنوان</span>
                            <span>{{ company.address }}</span>
                        </div>
                        <div class="drow" v-if="contact?.name">
                            <span class="dlbl">المسؤول</span>
                            <span>{{ contact.name }}</span>
                        </div>
                        <div class="drow" v-if="contact?.email">
                            <span class="dlbl">البريد</span>
                            <span class="lat">{{ contact.email }}</span>
                        </div>
                        <div class="drow" v-if="contact?.phone">
                            <span class="dlbl">الهاتف</span>
                            <span class="lat">{{ contact.phone }}</span>
                        </div>
                    </div>
                </div>

                <!-- Review notes -->
                <div class="panel">
                    <h3 class="ptitle">ملاحظات المراجعة</h3>
                    <textarea
                        v-model="reviewForm.review_notes"
                        rows="4"
                        placeholder="أضف ملاحظاتك هنا…"
                        class="ntarea"
                    ></textarea>
                    <button
                        class="save-btn"
                        :disabled="reviewForm.processing"
                        @click="review(company.status)"
                    >
                        {{ reviewForm.processing ? 'جاري الحفظ…' : '💾 حفظ الملاحظات' }}
                    </button>
                    <div v-if="company.review_notes" class="prev-notes">
                        <div class="pn-lbl">آخر ملاحظة محفوظة</div>
                        <p>{{ company.review_notes }}</p>
                    </div>
                </div>

            </div>

            <!-- Right: recent orders -->
            <div class="bmain">
                <div class="panel">
                    <div class="phead">
                        <h3 class="ptitle">الطلبات ({{ stats.total }})</h3>
                        <Link
                            v-if="contact"
                            :href="route('admin.orders.index') + '?user_id=' + contact.id"
                            class="see-all"
                        >عرض الكل ←</Link>
                    </div>

                    <p v-if="!recent_orders.length" class="empty">لا توجد طلبات بعد.</p>

                    <div v-else class="otable">
                        <div class="ohead">
                            <span>رقم الطلب</span>
                            <span>التاريخ</span>
                            <span>العناصر</span>
                            <span>الإجمالي</span>
                            <span>الحالة</span>
                        </div>
                        <Link
                            v-for="o in recent_orders"
                            :key="o.id"
                            :href="route('admin.orders.show', o.id)"
                            class="orow"
                        >
                            <span class="oref lat">{{ o.reference }}</span>
                            <span class="omuted">{{ o.created_at }}</span>
                            <span class="omuted">{{ o.items_count }} عنصر</span>
                            <span class="ofont">₪{{ o.total }}</span>
                            <span class="badge sm" :class="orderTone[o.status]">{{ o.status_label }}</span>
                        </Link>
                    </div>
                </div>
            </div>

        </div>

    </AdminLayout>
</template>

<style scoped>
/* Breadcrumb */
.crumb { font-size: 13px; color: var(--muted); margin-bottom: 20px; }
.crumb a { color: var(--muted); text-decoration: none; transition: color .2s; }
.crumb a:hover { color: var(--emerald-soft); }
.sep { margin: 0 7px; }

/* ── Hero card ── */
.hero-card {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    background: var(--bg2);
    border: 1.5px solid var(--hair);
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 22px;
    flex-wrap: wrap;
}
.hero-left {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    flex: 1;
    min-width: 0;
}
.hav {
    width: 62px; height: 62px;
    border-radius: 16px; flex-shrink: 0;
    background: linear-gradient(145deg, var(--emerald-soft), var(--emerald-deep));
    color: var(--on-emerald);
    font-size: 26px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 16px rgba(52,215,127,.28);
}
.hinfo { min-width: 0; }
.hname { font-size: 22px; font-weight: 800; margin-bottom: 8px; }
.hmeta {
    display: flex; gap: 14px; flex-wrap: wrap;
    font-size: 13px; color: var(--muted); margin-bottom: 8px;
}
.hcontact {
    display: flex; gap: 14px; flex-wrap: wrap;
    font-size: 13px; color: var(--muted);
}

.hero-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 12px;
    flex-shrink: 0;
}
.hacts { display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }

/* ── Stats grid ── */
.sgrid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 12px;
    margin-bottom: 22px;
}
.scard {
    position: relative;
    background: var(--bg2);
    border: 1.5px solid var(--hair);
    border-radius: 14px;
    padding: 14px 12px 12px;
    overflow: hidden;
    transition: all .25s;
}
.scard:hover {
    transform: translateY(-2px);
    border-color: var(--accent, var(--hair2));
    box-shadow: 0 6px 20px rgba(0,0,0,.07);
}
.scard-top {
    display: flex; align-items: flex-start;
    justify-content: space-between; margin-bottom: 4px;
}
.sico { font-size: 18px; }
.sval { font-size: 22px; font-weight: 900; line-height: 1; color: var(--accent, var(--ink)); }
.slbl { font-size: 11px; color: var(--muted); font-weight: 500; }
.sbar {
    position: absolute; bottom: 0; right: 0; left: 0;
    height: 3px; background: var(--accent, var(--hair));
    opacity: .45; border-radius: 0 0 12px 12px; transition: opacity .25s;
}
.scard:hover .sbar { opacity: 1; }

/* ── Bottom grid ── */
.bgrid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 18px;
    align-items: start;
}
.bside { display: flex; flex-direction: column; gap: 16px; }
.bmain { }

/* Panel */
.panel {
    background: var(--bg2);
    border: 1.5px solid var(--hair);
    border-radius: 18px;
    padding: 18px;
}
.phead { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.ptitle { font-size: 14px; font-weight: 700; margin-bottom: 14px; }
.phead .ptitle { margin-bottom: 0; }
.see-all { font-size: 12px; color: var(--emerald-soft); text-decoration: none; }

/* Detail list */
.dlist { display: flex; flex-direction: column; gap: 0; }
.drow {
    display: flex; flex-direction: column; gap: 2px;
    padding: 9px 0;
    border-bottom: 1px solid var(--hair);
    font-size: 13px;
}
.drow:last-child { border-bottom: none; }
.dlbl { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; }

/* Notes textarea */
.ntarea {
    width: 100%; box-sizing: border-box;
    background: var(--bg);
    border: 1px solid var(--hair);
    border-radius: 10px;
    padding: 10px 13px;
    color: var(--ink); font-family: inherit; font-size: 13px;
    outline: none; resize: vertical; margin-bottom: 10px;
}
.ntarea:focus { border-color: var(--emerald-soft); }
.save-btn {
    width: 100%;
    padding: 10px;
    border-radius: 10px;
    background: var(--emerald-deep);
    color: var(--on-emerald);
    border: none; font-size: 13px; font-weight: 700;
    cursor: pointer; font-family: inherit;
    transition: opacity .2s;
    margin-bottom: 12px;
}
.save-btn:hover { opacity: .88; }
.save-btn:disabled { opacity: .55; cursor: not-allowed; }
.prev-notes { font-size: 12px; color: var(--muted); line-height: 1.6; }
.pn-lbl { font-size: 10px; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; }

/* Orders table */
.empty { color: var(--muted); font-size: 13px; text-align: center; padding: 30px; }
.otable { display: flex; flex-direction: column; gap: 5px; }
.ohead {
    display: grid;
    grid-template-columns: 1.3fr 1fr .7fr .8fr auto;
    gap: 10px;
    padding: 0 12px 8px;
    font-size: 10px; color: var(--muted);
    text-transform: uppercase; letter-spacing: .06em;
    border-bottom: 1px solid var(--hair);
}
.orow {
    display: grid;
    grid-template-columns: 1.3fr 1fr .7fr .8fr auto;
    gap: 10px;
    align-items: center;
    padding: 10px 12px;
    background: var(--bg);
    border: 1px solid var(--hair);
    border-radius: 10px;
    text-decoration: none; color: var(--ink); font-size: 13px;
    transition: all .2s;
}
.orow:hover { border-color: var(--hair2); background: var(--bg2); }
.oref  { font-weight: 600; font-size: 12px; }
.omuted{ color: var(--muted); font-size: 12px; }
.ofont { font-weight: 700; }

/* Badges */
.badge {
    padding: 4px 10px; border-radius: 999px;
    font-size: 12px; font-weight: 600; white-space: nowrap;
}
.badge.lg  { font-size: 13px; padding: 6px 14px; }
.badge.sm  { font-size: 10px; padding: 3px 8px; }
.badge.warn { background: rgba(255,193,7,.15);  color: #e0a800; }
.badge.ok   { background: rgba(52,215,127,.15); color: var(--emerald-soft); }
.badge.info { background: rgba(33,150,243,.15); color: #42a5f5; }
.badge.bad  { background: rgba(231,76,60,.15);  color: #ff7a6b; }

/* Action buttons */
.abtn {
    padding: 8px 18px; border-radius: 10px;
    border: 1px solid transparent;
    font-size: 13px; font-weight: 700;
    cursor: pointer; font-family: inherit;
    white-space: nowrap; transition: all .2s;
}
.abtn:disabled { opacity: .5; cursor: not-allowed; }
.abtn.ok      { background: rgba(52,215,127,.12); color: var(--emerald-soft); border-color: rgba(52,215,127,.25); }
.abtn.ok:hover:not(:disabled) { background: rgba(52,215,127,.22); }
.abtn.bad     { background: rgba(231,76,60,.1);  color: #ff7a6b; border-color: rgba(231,76,60,.25); }
.abtn.bad:hover:not(:disabled){ background: rgba(231,76,60,.2); }
.abtn.neutral { background: var(--glass); color: var(--muted); border-color: var(--hair); }
.abtn.neutral:hover:not(:disabled) { border-color: var(--hair2); color: var(--ink); }

/* License */
.lic-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 10px;
    background: rgba(33,150,243,.1); color: #42a5f5;
    font-size: 13px; font-weight: 600; text-decoration: none;
    transition: background .2s;
}
.lic-btn:hover { background: rgba(33,150,243,.2); }
.no-lic { font-size: 12px; color: var(--muted); }

/* Responsive */
@media (max-width: 1100px) {
    .sgrid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 800px) {
    .bgrid { grid-template-columns: 1fr; }
    .hero-card { flex-direction: column; }
    .hero-right { align-items: flex-start; width: 100%; }
    .sgrid { grid-template-columns: repeat(2, 1fr); }
    .ohead { display: none; }
    .orow  { grid-template-columns: 1fr auto; row-gap: 3px; }
    .orow > *:nth-child(2), .orow > *:nth-child(3) { display: none; }
}
</style>
