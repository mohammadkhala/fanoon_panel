<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ orders: Array, statuses: Object, filter: String });

const tone = { pending_review: 'warn', approved: 'ok', in_production: 'info', ready: 'ok', delivered: 'ok', cancelled: 'bad' };
const money = (n) => '₪' + Number(n).toLocaleString('en-US');

function setFilter(key) {
    router.get(route('admin.orders.index'), key ? { status: key } : {}, { preserveScroll: true });
}
</script>

<template>
    <Head title="الطلبات — الإدارة" />
    <AdminLayout title="الطلبات" subtitle="راجع التصاميم وحدّث حالة كل طلب">

        <div class="filterbar">
            <button class="fchip" :class="{ on: !filter }" @click="setFilter('')">الكل</button>
            <button v-for="(label, key) in statuses" :key="key" class="fchip" :class="{ on: filter === key }" @click="setFilter(key)">{{ label }}</button>
        </div>

        <div class="panel">
            <div v-if="orders.length" class="otable">
                <div class="othead">
                    <span>المرجع</span><span>العميل</span><span>التاريخ</span><span>الإجمالي</span><span>التسعيرة</span><span>الحالة</span>
                </div>
                <Link v-for="o in orders" :key="o.id" :href="route('admin.orders.show', o.id)" class="orow">
                    <span class="oref lat">{{ o.reference }}</span>
                    <span>{{ o.customer }}<span class="sub lat">{{ o.phone }}</span></span>
                    <span class="muted">{{ o.created_at }}</span>
                    <span class="ototal">{{ money(o.total) }}</span>
                    <span class="muted">{{ o.tier === 'wholesale' ? 'جملة' : 'تجزئة' }}</span>
                    <span class="badge" :class="tone[o.status]">{{ o.status_label }}</span>
                </Link>
            </div>
            <p v-else class="empty">لا توجد طلبات في هذا التصنيف.</p>
        </div>
    </AdminLayout>
</template>

<style scoped>
.filterbar{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px}
.fchip{padding:9px 16px;border-radius:999px;border:1px solid var(--hair);background:var(--bg2);color:var(--muted);font-size:13px;cursor:pointer;font-family:inherit;transition:all .3s var(--ease)}
.fchip:hover{border-color:var(--hair2)}
.fchip.on{background:var(--emerald);color:var(--on-emerald);border-color:var(--emerald)}
.panel{background:var(--bg2);border:1px solid var(--hair);border-radius:20px;padding:16px}
.otable{display:flex;flex-direction:column}
.othead,.orow{display:grid;grid-template-columns:1.2fr 1.4fr 1fr 0.9fr 0.8fr auto;align-items:center;gap:14px}
.othead{padding:8px 14px 12px;color:var(--muted);font-size:12px;border-bottom:1px solid var(--hair)}
.orow{padding:14px;border-radius:12px;text-decoration:none;color:var(--ink);transition:background .25s var(--ease)}
.orow:hover{background:var(--glass)}
.oref{font-weight:600}.ototal{font-weight:700}.muted{color:var(--muted);font-size:13px}
.sub{display:block;color:var(--muted);font-size:12px;margin-top:3px}
.badge{padding:5px 11px;border-radius:999px;font-size:12px;font-weight:600;justify-self:start;white-space:nowrap}
.badge.warn{background:rgba(255,193,7,.15);color:#e0a800}
.badge.ok{background:rgba(52,215,127,.15);color:var(--emerald-soft)}
.badge.info{background:rgba(33,150,243,.15);color:#42a5f5}
.badge.bad{background:rgba(231,76,60,.15);color:#ff7a6b}
.empty{color:var(--muted);text-align:center;padding:40px;font-size:14px}
@media(max-width:760px){.othead{display:none}.orow{grid-template-columns:1fr auto;row-gap:6px}}
</style>
