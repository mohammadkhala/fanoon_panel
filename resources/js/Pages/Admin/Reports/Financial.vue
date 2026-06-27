<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';

const props = defineProps({ kpis: Object, tier: Object, recent: Array });

const money = (n) => '₪' + Number(n).toLocaleString('en-US', { maximumFractionDigits: 0 });
const tierTotal = computed(() => Math.max(1, props.tier.retail + props.tier.wholesale));
</script>

<template>
    <Head title="التقارير المالية — الإدارة" />
    <AdminLayout title="التقارير المالية" subtitle="ملخّص الإيرادات والمبالغ المعلّقة والملغاة">
        <div class="kpis">
            <div class="kpi hi"><div class="kl">الإيرادات الفعلية</div><div class="kv">{{ money(kpis.gross) }}</div></div>
            <div class="kpi"><div class="kl">مبالغ مُسلّمة</div><div class="kv">{{ money(kpis.delivered) }}</div></div>
            <div class="kpi"><div class="kl">قيد التنفيذ</div><div class="kv">{{ money(kpis.pending) }}</div></div>
            <div class="kpi"><div class="kl">ملغاة</div><div class="kv bad">{{ money(kpis.cancelled) }}</div></div>
        </div>

        <div class="panel">
            <h3>الإيرادات حسب فئة التسعير</h3>
            <div class="tierbar">
                <div class="seg retail" :style="{ width: (tier.retail / tierTotal * 100) + '%' }"></div>
                <div class="seg whole" :style="{ width: (tier.wholesale / tierTotal * 100) + '%' }"></div>
            </div>
            <div class="legend">
                <span><i class="d retail"></i> تجزئة — {{ money(tier.retail) }}</span>
                <span><i class="d whole"></i> جملة — {{ money(tier.wholesale) }}</span>
            </div>
        </div>

        <div class="panel">
            <h3>أحدث الحركات</h3>
            <div class="table">
                <div class="thead"><span>المرجع</span><span>العميل</span><span>الحالة</span><span>التاريخ</span><span>المبلغ</span></div>
                <div v-for="(o, i) in recent" :key="i" class="row">
                    <span class="lat">{{ o.reference }}</span>
                    <span>{{ o.customer }}</span>
                    <span class="muted">{{ o.status }}</span>
                    <span class="muted lat">{{ o.date }}</span>
                    <span class="amt">{{ money(o.total) }}</span>
                </div>
            </div>
            <p v-if="!recent.length" class="empty">لا توجد حركات بعد.</p>
        </div>
    </AdminLayout>
</template>

<style scoped>
.kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:22px}
.kpi{background:var(--bg2);border:1px solid var(--hair);border-radius:18px;padding:18px}
.kpi.hi{background:linear-gradient(150deg,rgba(52,215,127,.12),transparent);border-color:rgba(52,215,127,.25)}
.kl{font-size:12px;color:var(--muted);margin-bottom:8px}
.kv{font-size:23px;font-weight:700}
.kv.bad{color:#ff7a6b}
.panel{background:var(--bg2);border:1px solid var(--hair);border-radius:20px;padding:22px;margin-bottom:20px}
h3{font-size:15px;font-weight:700;margin-bottom:18px}
.tierbar{display:flex;height:16px;border-radius:999px;overflow:hidden;background:var(--glass)}
.seg{height:100%}
.seg.retail{background:var(--emerald)}
.seg.whole{background:#42a5f5}
.legend{display:flex;gap:24px;margin-top:14px;font-size:13px;color:var(--muted)}
.d{display:inline-block;width:10px;height:10px;border-radius:3px;margin-left:6px}
.d.retail{background:var(--emerald)}.d.whole{background:#42a5f5}
.table{display:flex;flex-direction:column}
.thead,.row{display:grid;grid-template-columns:1.2fr 1.4fr 1fr 1fr .8fr;align-items:center;gap:12px}
.thead{padding:8px 12px 12px;color:var(--muted);font-size:12px;border-bottom:1px solid var(--hair)}
.row{padding:11px 12px;border-radius:10px;font-size:14px}
.row:hover{background:var(--glass)}
.muted{color:var(--muted);font-size:13px}
.amt{font-weight:700;color:var(--emerald-soft)}
.lat{direction:ltr;text-align:right}
.empty{color:var(--muted);text-align:center;padding:30px}
@media(max-width:900px){.kpis{grid-template-columns:1fr 1fr}.thead{display:none}.row{grid-template-columns:1fr auto;row-gap:5px}}
</style>
