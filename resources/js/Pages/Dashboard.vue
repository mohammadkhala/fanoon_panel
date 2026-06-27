<script setup>
import { Head, Link } from '@inertiajs/vue3';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';

const props = defineProps({
    stats:         Object,
    recent_orders: Array,
    profile:       Object,
});

const statCards = [
    { key: 'total',         label: 'إجمالي الطلبات', icon: '📋', accent: '#8b8b8b', href: 'orders.index' },
    { key: 'pending',       label: 'قيد المراجعة',   icon: '⏳', accent: '#e0a800', href: 'orders.index' },
    { key: 'in_production', label: 'قيد التصنيع',    icon: '⚙️', accent: '#42a5f5', href: 'orders.index' },
    { key: 'delivered',     label: 'تم التسليم',      icon: '✅', accent: '#34d77f', href: 'orders.index' },
    { key: 'cart_items',    label: 'في السلة',        icon: '🛒', accent: '#a78bfa', href: 'cart.index'   },
    { key: 'cancelled',     label: 'ملغاة',           icon: '❌', accent: '#ff7a6b', href: 'orders.index' },
];

const statusClass = {
    pending_review: 'warn',
    approved:       'ok',
    in_production:  'info',
    ready:          'ok',
    delivered:      'ok',
    cancelled:      'bad',
};
</script>

<template>
    <Head title="لوحة التحكم" />
    <CustomerLayout>

        <!-- ── Greeting banner ── -->
        <div class="banner">
            <div>
                <div class="ban-hi">مرحباً، {{ profile.name }} 👋</div>
                <div class="ban-sub">
                    عضو منذ {{ profile.member_since }} ·
                    <span class="ban-tier">{{ profile.tier === 'wholesale' ? '🏢 تسعيرة الجملة' : '👤 تسعيرة التجزئة' }}</span>
                </div>
            </div>
            <Link :href="route('shop')" class="shop-btn">
                تصميم جديد <span class="arr">←</span>
            </Link>
        </div>

        <!-- ── Stats grid ── -->
        <div class="sgrid">
            <Link
                v-for="s in statCards"
                :key="s.key"
                :href="route(s.href)"
                class="scard"
                :style="{ '--ac': s.accent }"
            >
                <div class="scard-row">
                    <span class="s-ico">{{ s.icon }}</span>
                    <span class="s-num">{{ props.stats[s.key] ?? 0 }}</span>
                </div>
                <div class="s-lbl">{{ s.label }}</div>
                <div class="s-bar"></div>
            </Link>
        </div>

        <!-- ── Recent orders ── -->
        <section class="section">
            <div class="sec-head">
                <h2 class="sec-title">آخر الطلبات</h2>
                <Link :href="route('orders.index')" class="sec-link">عرض الكل ←</Link>
            </div>

            <div v-if="!recent_orders.length" class="empty-box">
                <div class="e-ico">📦</div>
                <div class="e-msg">لا توجد طلبات بعد</div>
                <Link :href="route('shop')" class="e-cta">ابدأ بتصميم أول طلب ←</Link>
            </div>

            <div v-else class="orders-table">
                <div class="ot-head">
                    <span>رقم الطلب</span>
                    <span>التاريخ</span>
                    <span>العناصر</span>
                    <span>الإجمالي</span>
                    <span>الحالة</span>
                </div>
                <Link
                    v-for="o in recent_orders"
                    :key="o.id"
                    :href="route('orders.show', o.id)"
                    class="ot-row"
                >
                    <span class="o-ref lat">{{ o.reference }}</span>
                    <span class="o-dim">{{ o.created_at }}</span>
                    <span class="o-dim">{{ o.items_count }} عنصر</span>
                    <span class="o-price">₪{{ o.total }}</span>
                    <span class="status-badge" :class="statusClass[o.status]">{{ o.status_label }}</span>
                </Link>
            </div>
        </section>

        <!-- ── Quick links ── -->
        <section class="section">
            <h2 class="sec-title" style="margin-bottom:14px">روابط سريعة</h2>
            <div class="qgrid">
                <Link :href="route('shop')"         class="qcard"><span class="q-ico">🎨</span><span>تصفّح القوالب</span></Link>
                <Link :href="route('cart.index')"   class="qcard"><span class="q-ico">🛒</span><span>سلة المشتريات</span></Link>
                <Link :href="route('orders.index')" class="qcard"><span class="q-ico">📦</span><span>طلباتي</span></Link>
                <Link :href="route('track')"        class="qcard"><span class="q-ico">🔍</span><span>تتبع طلب</span></Link>
            </div>
        </section>

    </CustomerLayout>
</template>

<style scoped>
/* ── Banner ── */
.banner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    background: linear-gradient(135deg, rgba(52,215,127,.09) 0%, rgba(52,215,127,.02) 100%);
    border: 1.5px solid rgba(52,215,127,.2);
    border-radius: 18px;
    padding: 22px 26px;
    margin-bottom: 24px;
}
.ban-hi   { font-size: 21px; font-weight: 700; margin-bottom: 6px; }
.ban-sub  { font-size: 13px; color: var(--muted); }
.ban-tier { font-weight: 600; }

.shop-btn {
    display: inline-flex; align-items: center; gap: 10px;
    background: linear-gradient(150deg, var(--emerald-soft), var(--emerald-deep));
    color: var(--on-emerald);
    padding: 11px 12px 11px 22px;
    border-radius: 999px;
    font-size: 14px; font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    transition: opacity .2s;
}
.shop-btn:hover { opacity: .88; }
.arr {
    width: 30px; height: 30px; border-radius: 50%;
    background: rgba(3,26,13,.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
}

/* ── Stats ── */
.sgrid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 28px;
}
.scard {
    position: relative; overflow: hidden;
    background: var(--bg2);
    border: 1.5px solid var(--hair);
    border-radius: 16px;
    padding: 18px 16px 16px;
    text-decoration: none; color: var(--ink);
    display: flex; flex-direction: column; gap: 6px;
    transition: border-color .2s, transform .2s, box-shadow .2s;
}
.scard:hover {
    border-color: var(--ac, var(--hair));
    transform: translateY(-3px);
    box-shadow: 0 8px 22px rgba(0,0,0,.07);
}
.scard-row {
    display: flex; align-items: center; justify-content: space-between;
}
.s-ico { font-size: 22px; }
.s-num { font-size: 34px; font-weight: 900; line-height: 1; color: var(--ac, var(--ink)); }
.s-lbl { font-size: 12px; color: var(--muted); font-weight: 500; }
.s-bar {
    position: absolute; bottom: 0; right: 0; left: 0;
    height: 3px;
    background: var(--ac, var(--hair));
    opacity: .45;
    border-radius: 0 0 14px 14px;
    transition: opacity .2s;
}
.scard:hover .s-bar { opacity: 1; }

/* ── Section ── */
.section    { margin-bottom: 28px; }
.sec-head   { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.sec-title  { font-size: 16px; font-weight: 700; }
.sec-link   { font-size: 13px; color: var(--emerald-soft); text-decoration: none; }

/* Empty state */
.empty-box {
    text-align: center; padding: 44px 20px;
    background: var(--bg2);
    border: 1.5px dashed var(--hair);
    border-radius: 16px;
}
.e-ico  { font-size: 48px; margin-bottom: 12px; }
.e-msg  { font-size: 15px; font-weight: 600; color: var(--muted); margin-bottom: 14px; }
.e-cta  {
    display: inline-block;
    color: var(--emerald-soft); text-decoration: none;
    font-size: 14px; font-weight: 600;
    border: 1.5px solid rgba(52,215,127,.3);
    border-radius: 999px; padding: 8px 20px;
    transition: background .2s;
}
.e-cta:hover { background: rgba(52,215,127,.08); }

/* Orders table */
.orders-table { display: flex; flex-direction: column; gap: 7px; }
.ot-head {
    display: grid;
    grid-template-columns: 1.5fr 1fr 0.7fr 0.8fr auto;
    gap: 12px;
    padding: 0 16px 10px;
    font-size: 11px; color: var(--muted);
    font-weight: 600; letter-spacing: .05em;
    border-bottom: 1px solid var(--hair);
}
.ot-row {
    display: grid;
    grid-template-columns: 1.5fr 1fr 0.7fr 0.8fr auto;
    gap: 12px; align-items: center;
    padding: 13px 16px;
    background: var(--bg2);
    border: 1.5px solid var(--hair);
    border-radius: 13px;
    text-decoration: none; color: var(--ink);
    font-size: 14px;
    transition: border-color .2s, transform .2s;
}
.ot-row:hover { border-color: var(--hair2, #ccc); transform: translateX(-2px); }
.o-ref   { font-weight: 600; font-size: 13px; }
.o-dim   { color: var(--muted); font-size: 13px; }
.o-price { font-weight: 700; }

/* Status badges */
.status-badge {
    padding: 4px 11px; border-radius: 999px;
    font-size: 11px; font-weight: 600; white-space: nowrap;
}
.status-badge.warn { background: rgba(255,193,7,.15);  color: #e0a800; }
.status-badge.ok   { background: rgba(52,215,127,.15); color: var(--emerald-soft); }
.status-badge.info { background: rgba(33,150,243,.15); color: #42a5f5; }
.status-badge.bad  { background: rgba(231,76,60,.15);  color: #ff7a6b; }

/* Quick links */
.qgrid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}
.qcard {
    display: flex; flex-direction: column;
    align-items: center; gap: 9px;
    padding: 20px 10px;
    background: var(--bg2);
    border: 1.5px solid var(--hair);
    border-radius: 15px;
    text-decoration: none; color: var(--ink);
    font-size: 13px; font-weight: 500;
    text-align: center;
    transition: border-color .2s, transform .2s;
}
.qcard:hover { border-color: rgba(52,215,127,.35); transform: translateY(-2px); }
.q-ico { font-size: 28px; }

/* ── Responsive ── */
@media (max-width: 640px) {
    .sgrid  { grid-template-columns: repeat(2, 1fr); }
    .qgrid  { grid-template-columns: repeat(2, 1fr); }
    .ot-head { display: none; }
    .ot-row  {
        grid-template-columns: 1fr auto;
        row-gap: 4px;
    }
    .ot-row > *:nth-child(2),
    .ot-row > *:nth-child(3) { display: none; }
}
</style>
