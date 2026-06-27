<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';

const props = defineProps({ kpis: Object, days: Array, topPages: Array });

const num = (n) => Number(n).toLocaleString('en-US');
const maxDay = computed(() => Math.max(1, ...props.days.map((d) => d.count)));
const maxHits = computed(() => Math.max(1, ...props.topPages.map((p) => p.hits)));
</script>

<template>
    <Head title="الزوار والإحصائيات — الإدارة" />
    <AdminLayout title="الزوار والإحصائيات" subtitle="حركة الزوار على المتجر خلال آخر 14 يوماً">
        <div class="kpis">
            <div class="kpi hi"><div class="kl">إجمالي الزيارات</div><div class="kv">{{ num(kpis.total) }}</div></div>
            <div class="kpi"><div class="kl">زيارات اليوم</div><div class="kv">{{ num(kpis.today) }}</div></div>
            <div class="kpi"><div class="kl">آخر 7 أيام</div><div class="kv">{{ num(kpis.week) }}</div></div>
            <div class="kpi"><div class="kl">عناوين IP فريدة</div><div class="kv">{{ num(kpis.unique_ips) }}</div></div>
        </div>

        <div class="panel">
            <h3>الزيارات اليومية</h3>
            <div class="chart">
                <div v-for="(d, i) in days" :key="i" class="bar-col">
                    <div class="bar-wrap"><div class="bar" :style="{ height: (d.count / maxDay * 100) + '%' }" :title="d.count"></div></div>
                    <div class="bar-lbl lat">{{ d.label }}</div>
                </div>
            </div>
        </div>

        <div class="panel">
            <h3>أكثر الصفحات زيارة</h3>
            <div v-if="topPages.length">
                <div v-for="(p, i) in topPages" :key="i" class="prow">
                    <span class="purl lat">/{{ p.url }}</span>
                    <div class="ptrack"><div class="pfill" :style="{ width: (p.hits / maxHits * 100) + '%' }"></div></div>
                    <span class="phits">{{ num(p.hits) }}</span>
                </div>
            </div>
            <p v-else class="empty">لا توجد بيانات زيارة بعد.</p>
        </div>
    </AdminLayout>
</template>

<style scoped>
.kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:22px}
.kpi{background:var(--bg2);border:1px solid var(--hair);border-radius:18px;padding:18px}
.kpi.hi{background:linear-gradient(150deg,rgba(52,215,127,.12),transparent);border-color:rgba(52,215,127,.25)}
.kl{font-size:12px;color:var(--muted);margin-bottom:8px}
.kv{font-size:24px;font-weight:700}
.panel{background:var(--bg2);border:1px solid var(--hair);border-radius:20px;padding:22px;margin-bottom:20px}
h3{font-size:15px;font-weight:700;margin-bottom:18px}
.chart{display:flex;align-items:flex-end;gap:6px;height:190px}
.bar-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;height:100%}
.bar-wrap{flex:1;width:100%;display:flex;align-items:flex-end}
.bar{width:100%;background:linear-gradient(to top,var(--emerald-deep),var(--emerald-soft));border-radius:5px 5px 0 0;min-height:3px;transition:height .4s var(--ease)}
.bar-lbl{font-size:10px;color:var(--muted)}
.prow{display:flex;align-items:center;gap:12px;margin-bottom:11px}
.purl{width:160px;font-size:13px;flex-shrink:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ptrack{flex:1;height:8px;background:var(--glass);border-radius:999px;overflow:hidden}
.pfill{height:100%;background:var(--emerald);border-radius:999px}
.phits{width:50px;text-align:left;font-size:13px;color:var(--muted)}
.lat{direction:ltr;text-align:left}
.empty{color:var(--muted);text-align:center;padding:30px}
@media(max-width:900px){.kpis{grid-template-columns:1fr 1fr}.purl{width:100px}}
</style>
