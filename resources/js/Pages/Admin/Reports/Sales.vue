<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';

const props = defineProps({ kpis: Object, months: Array, statusCounts: Array, topCustomers: Array });

const money = (n) => '₪' + Number(n).toLocaleString('en-US', { maximumFractionDigits: 0 });
const maxTotal = computed(() => Math.max(1, ...props.months.map((m) => m.total)));
const maxStatus = computed(() => Math.max(1, ...props.statusCounts.map((s) => s.count)));
</script>

<template>
    <Head title="تقارير المبيعات — الإدارة" />
    <AdminLayout title="تقارير المبيعات" subtitle="أداء المبيعات خلال آخر 12 شهراً">
        <div class="kpis">
            <div class="kpi"><div class="kl">إجمالي الطلبات</div><div class="kv">{{ kpis.orders }}</div></div>
            <div class="kpi"><div class="kl">إجمالي الإيرادات</div><div class="kv">{{ money(kpis.revenue) }}</div></div>
            <div class="kpi"><div class="kl">متوسط قيمة الطلب</div><div class="kv">{{ money(kpis.avg) }}</div></div>
            <div class="kpi hi"><div class="kl">إيرادات هذا الشهر</div><div class="kv">{{ money(kpis.this_month) }}</div></div>
        </div>

        <div class="panel">
            <h3>الإيرادات الشهرية</h3>
            <div class="chart">
                <div v-for="m in months" :key="m.key" class="bar-col">
                    <div class="bar-wrap"><div class="bar" :style="{ height: (m.total / maxTotal * 100) + '%' }" :title="money(m.total)"></div></div>
                    <div class="bar-lbl lat">{{ m.label }}</div>
                </div>
            </div>
        </div>

        <div class="two">
            <div class="panel">
                <h3>الطلبات حسب الحالة</h3>
                <div v-for="s in statusCounts" :key="s.key" class="srow">
                    <span class="sl">{{ s.label }}</span>
                    <div class="strack"><div class="sfill" :style="{ width: (s.count / maxStatus * 100) + '%' }"></div></div>
                    <span class="sc">{{ s.count }}</span>
                </div>
            </div>
            <div class="panel">
                <h3>أفضل العملاء</h3>
                <div v-if="topCustomers.length" class="tc">
                    <div v-for="(c, i) in topCustomers" :key="i" class="tcrow">
                        <span class="rank">{{ i + 1 }}</span>
                        <span class="cn">{{ c.name }}</span>
                        <span class="co muted">{{ c.orders }} طلب</span>
                        <span class="cs">{{ money(c.spent) }}</span>
                    </div>
                </div>
                <p v-else class="empty">لا توجد بيانات بعد.</p>
            </div>
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
.chart{display:flex;align-items:flex-end;gap:8px;height:200px}
.bar-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:8px;height:100%}
.bar-wrap{flex:1;width:100%;display:flex;align-items:flex-end}
.bar{width:100%;background:linear-gradient(to top,var(--emerald-deep),var(--emerald-soft));border-radius:6px 6px 0 0;min-height:3px;transition:height .4s var(--ease)}
.bar-lbl{font-size:10px;color:var(--muted)}
.two{display:grid;grid-template-columns:1fr 1fr;gap:20px}
.srow{display:flex;align-items:center;gap:12px;margin-bottom:12px}
.sl{width:110px;font-size:13px;flex-shrink:0}
.strack{flex:1;height:8px;background:var(--glass);border-radius:999px;overflow:hidden}
.sfill{height:100%;background:var(--emerald);border-radius:999px}
.sc{width:34px;text-align:left;font-size:13px;color:var(--muted)}
.tcrow{display:grid;grid-template-columns:auto 1fr auto auto;align-items:center;gap:12px;padding:9px 0;border-bottom:1px solid var(--hair)}
.tcrow:last-child{border-bottom:none}
.rank{width:24px;height:24px;border-radius:8px;background:var(--glass);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700}
.cn{font-weight:600;font-size:14px}
.co{font-size:12px}
.cs{font-weight:700;font-size:14px;color:var(--emerald-soft)}
.muted{color:var(--muted)}
.empty{color:var(--muted);text-align:center;padding:30px}
@media(max-width:900px){.kpis{grid-template-columns:1fr 1fr}.two{grid-template-columns:1fr}}
</style>
