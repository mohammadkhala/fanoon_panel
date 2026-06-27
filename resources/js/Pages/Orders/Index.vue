<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';

const props = defineProps({ orders: Array });

const tone = {
    pending_review: 'warn',
    approved:       'ok',
    in_production:  'info',
    ready:          'ok',
    delivered:      'ok',
    cancelled:      'bad',
};

const filters = [
    { key: '',                label: 'الكل' },
    { key: 'pending_review',  label: 'قيد المراجعة' },
    { key: 'in_production',   label: 'قيد التصنيع' },
    { key: 'delivered',       label: 'تم التسليم' },
    { key: 'cancelled',       label: 'ملغاة' },
];

const activeFilter = ref('');

const filtered = computed(() =>
    activeFilter.value
        ? props.orders.filter(o => o.status === activeFilter.value)
        : props.orders
);
</script>

<template>
    <Head title="طلباتي" />
    <CustomerLayout>
        <div class="page-head rv">
            <h1 class="ph-title">طلباتي</h1>
            <p  class="ph-sub">تابع حالة طلباتك ومراحل التصنيع</p>
        </div>

        <!-- Filter pills -->
        <div class="filters rv">
            <button
                v-for="f in filters"
                :key="f.key"
                class="fpill"
                :class="{ active: activeFilter === f.key }"
                @click="activeFilter = f.key"
            >
                {{ f.label }}
                <span class="fcount">
                    {{ f.key ? orders.filter(o => o.status === f.key).length : orders.length }}
                </span>
            </button>
        </div>

        <!-- Empty -->
        <p v-if="!filtered.length" class="empty rv">
            <span v-if="activeFilter">لا توجد طلبات بهذه الحالة.</span>
            <span v-else>لا توجد طلبات بعد. <Link :href="route('shop')" style="color:var(--emerald-soft)">ابدأ بتصميم أول طلب</Link>.</span>
        </p>

        <!-- Table -->
        <div v-else class="otable rv">
            <div class="ohead">
                <span>رقم الطلب</span>
                <span>التاريخ</span>
                <span>العناصر</span>
                <span>الإجمالي</span>
                <span>الحالة</span>
                <span></span>
            </div>
            <Link
                v-for="o in filtered"
                :key="o.id"
                :href="route('orders.show', o.id)"
                class="orow"
            >
                <span class="oref lat">{{ o.reference }}</span>
                <span class="omuted">{{ o.created_at }}</span>
                <span class="omuted">{{ o.items_count }} عنصر</span>
                <span class="ofont">₪{{ o.total }}</span>
                <span class="badge" :class="tone[o.status]">{{ o.status_label }}</span>
                <span class="ochev">←</span>
            </Link>
        </div>
    </CustomerLayout>
</template>

<style scoped>
.page-head  { margin-bottom: 20px; }
.ph-title   { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
.ph-sub     { color: var(--muted); font-size: 14px; }

/* Filters */
.filters {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 18px;
}
.fpill {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 999px;
    border: 1px solid var(--hair);
    background: none;
    color: var(--muted);
    font-size: 13px;
    cursor: pointer;
    transition: all .2s;
}
.fpill:hover  { border-color: var(--hair2); color: var(--ink); }
.fpill.active { background: rgba(52,215,127,.1); border-color: rgba(52,215,127,.3); color: var(--emerald-soft); font-weight: 600; }
.fcount {
    min-width: 20px;
    height: 20px;
    padding: 0 5px;
    border-radius: 999px;
    background: var(--glass);
    font-size: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--muted);
}

/* Table */
.empty { text-align: center; color: var(--muted); padding: 40px 0; font-size: 14px; }
.otable { display: flex; flex-direction: column; gap: 7px; }
.ohead {
    display: grid;
    grid-template-columns: 1.4fr 1fr 0.8fr 0.8fr auto 24px;
    gap: 12px;
    padding: 0 16px 10px;
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .06em;
    border-bottom: 1px solid var(--hair);
}
.orow {
    display: grid;
    grid-template-columns: 1.4fr 1fr 0.8fr 0.8fr auto 24px;
    gap: 12px;
    align-items: center;
    padding: 14px 16px;
    background: var(--bg2);
    border: 1px solid var(--hair);
    border-radius: 14px;
    text-decoration: none;
    color: var(--ink);
    font-size: 14px;
    transition: all .25s;
}
.orow:hover { border-color: var(--hair2); transform: translateX(-3px); }
.oref   { font-weight: 600; font-size: 13px; }
.omuted { color: var(--muted); font-size: 13px; }
.ofont  { font-weight: 700; }
.ochev  { color: var(--muted); font-size: 16px; }

.badge { padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; white-space: nowrap; }
.badge.warn { background: rgba(255,193,7,.15);  color: #e0a800; }
.badge.ok   { background: rgba(52,215,127,.15); color: var(--emerald-soft); }
.badge.info { background: rgba(33,150,243,.15); color: #42a5f5; }
.badge.bad  { background: rgba(231,76,60,.15);  color: #ff7a6b; }

@media (max-width: 640px) {
    .ohead { display: none; }
    .orow  { grid-template-columns: 1fr auto; row-gap: 4px; }
    .orow > *:nth-child(2),
    .orow > *:nth-child(3) { display: none; }
}
</style>
