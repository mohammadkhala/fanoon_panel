<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';

const props = defineProps({ stats: Object, statusCounts: Array, recentOrders: Array, pendingCompanies: Array });

const tone = { pending_review: 'warn', approved: 'ok', in_production: 'info', ready: 'ok', delivered: 'ok', cancelled: 'bad' };

const maxCount = computed(() => Math.max(1, ...props.statusCounts.map(s => s.count)));

const cards = computed(() => [
    { label: 'طلبات قيد المراجعة', value: props.stats.pending_orders, icon: '⏳', accent: 'warn' },
    { label: 'إجمالي الطلبات', value: props.stats.total_orders, icon: '🧾', accent: 'emerald' },
    { label: 'شركات بانتظار الموافقة', value: props.stats.pending_companies, icon: '🏢', accent: 'info' },
    { label: 'إجمالي العملاء', value: props.stats.customers, icon: '👥', accent: 'muted' },
]);

const money = (n) => '₪' + Number(n).toLocaleString('en-US');
</script>

<template>
    <Head title="لوحة التحكم — الإدارة" />
    <AdminLayout title="لوحة التحكم" subtitle="نظرة عامة على نشاط المتجر">

        <!-- stat cards -->
        <div class="cards">
            <div v-for="c in cards" :key="c.label" class="card" :class="c.accent">
                <div class="card-ico">{{ c.icon }}</div>
                <div class="card-val">{{ c.value }}</div>
                <div class="card-lbl">{{ c.label }}</div>
            </div>
        </div>

        <!-- revenue banner -->
        <div class="rev">
            <div>
                <div class="rev-lbl">إجمالي الإيرادات (الطلبات غير الملغاة)</div>
                <div class="rev-val">{{ money(stats.revenue) }}</div>
            </div>
            <div class="rev-ico">📈</div>
        </div>

        <div class="grid2">
            <!-- status breakdown -->
            <section class="panel">
                <div class="panel-head"><h3>الطلبات حسب الحالة</h3></div>
                <div class="bars">
                    <div v-for="s in statusCounts" :key="s.key" class="bar-row">
                        <div class="bar-lbl">{{ s.label }}</div>
                        <div class="bar-track"><div class="bar-fill" :class="tone[s.key]" :style="{ width: (s.count / maxCount * 100) + '%' }"></div></div>
                        <div class="bar-num">{{ s.count }}</div>
                    </div>
                </div>
            </section>

            <!-- pending companies -->
            <section class="panel">
                <div class="panel-head">
                    <h3>شركات بانتظار المراجعة</h3>
                    <Link :href="route('admin.companies.index')" class="more">عرض الكل ←</Link>
                </div>
                <div v-if="pendingCompanies.length" class="clist">
                    <Link v-for="c in pendingCompanies" :key="c.id" :href="route('admin.companies.index')" class="crow">
                        <div class="cav">{{ (c.company_name || '?').charAt(0) }}</div>
                        <div class="cmeta">
                            <div class="cname">{{ c.company_name }}</div>
                            <div class="csub">{{ c.contact }} · {{ c.city || '—' }}</div>
                        </div>
                        <span class="badge warn">جديدة</span>
                    </Link>
                </div>
                <p v-else class="empty">لا توجد طلبات شركات معلّقة 🎉</p>
            </section>
        </div>

        <!-- recent orders -->
        <section class="panel" style="margin-top:20px">
            <div class="panel-head">
                <h3>أحدث الطلبات</h3>
                <Link :href="route('admin.orders.index')" class="more">كل الطلبات ←</Link>
            </div>
            <div v-if="recentOrders.length" class="otable">
                <div class="othead">
                    <span>المرجع</span><span>العميل</span><span>التاريخ</span><span>الإجمالي</span><span>الحالة</span>
                </div>
                <Link v-for="o in recentOrders" :key="o.id" :href="route('admin.orders.show', o.id)" class="orow">
                    <span class="oref lat">{{ o.reference }}</span>
                    <span>{{ o.customer }}</span>
                    <span class="muted">{{ o.created_at }}</span>
                    <span class="ototal">{{ money(o.total) }}</span>
                    <span class="badge" :class="tone[o.status]">{{ o.status_label }}</span>
                </Link>
            </div>
            <p v-else class="empty">لا توجد طلبات بعد.</p>
        </section>
    </AdminLayout>
</template>

<style scoped>
.cards{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
.card{background:var(--bg2);border:1px solid var(--hair);border-radius:20px;padding:22px;position:relative;overflow:hidden;transition:transform .3s var(--ease)}
.card:hover{transform:translateY(-3px)}
.card::after{content:'';position:absolute;inset:0 auto auto 0;width:80px;height:80px;border-radius:50%;filter:blur(40px);opacity:.5}
.card.warn::after{background:#e0a800}.card.emerald::after{background:var(--emerald)}.card.info::after{background:#42a5f5}.card.muted::after{background:var(--muted)}
.card-ico{font-size:24px;margin-bottom:14px}
.card-val{font-size:34px;font-weight:700;line-height:1}
.card-lbl{color:var(--muted);font-size:13px;margin-top:8px}

.rev{margin-top:18px;display:flex;align-items:center;justify-content:space-between;background:linear-gradient(135deg,rgba(52,215,127,.16),rgba(52,215,127,.04));border:1px solid rgba(52,215,127,.28);border-radius:20px;padding:24px 28px}
.rev-lbl{color:var(--muted);font-size:13px;margin-bottom:8px}
.rev-val{font-size:32px;font-weight:700;color:var(--emerald-soft)}
.rev-ico{font-size:38px}

.grid2{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px}
.panel{background:var(--bg2);border:1px solid var(--hair);border-radius:20px;padding:22px}
.panel-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
.panel-head h3{font-size:16px;font-weight:700}
.more{color:var(--emerald-soft);font-size:13px;text-decoration:none}

.bars{display:flex;flex-direction:column;gap:14px}
.bar-row{display:grid;grid-template-columns:120px 1fr 34px;align-items:center;gap:12px}
.bar-lbl{font-size:13px;color:var(--muted)}
.bar-track{height:9px;background:var(--glass);border-radius:999px;overflow:hidden}
.bar-fill{height:100%;border-radius:999px;min-width:3px;transition:width .6s var(--ease)}
.bar-fill.warn{background:#e0a800}.bar-fill.ok{background:var(--emerald)}.bar-fill.info{background:#42a5f5}.bar-fill.bad{background:#e74c3c}
.bar-num{font-size:14px;font-weight:700;text-align:left}

.clist{display:flex;flex-direction:column;gap:10px}
.crow{display:flex;align-items:center;gap:12px;padding:11px;border-radius:13px;text-decoration:none;color:var(--ink);border:1px solid var(--hair);transition:all .3s var(--ease)}
.crow:hover{border-color:var(--hair2)}
.cav{width:38px;height:38px;border-radius:11px;background:var(--glass);display:flex;align-items:center;justify-content:center;font-weight:700;flex-shrink:0}
.cmeta{flex:1}
.cname{font-weight:600;font-size:14px}
.csub{color:var(--muted);font-size:12px;margin-top:2px}

.otable{display:flex;flex-direction:column}
.othead,.orow{display:grid;grid-template-columns:1.2fr 1.3fr 1fr 0.9fr auto;align-items:center;gap:14px}
.othead{padding:0 14px 12px;color:var(--muted);font-size:12px;border-bottom:1px solid var(--hair)}
.orow{padding:14px;border-radius:12px;text-decoration:none;color:var(--ink);transition:background .25s var(--ease)}
.orow:hover{background:var(--glass)}
.oref{font-weight:600}.ototal{font-weight:700}.muted{color:var(--muted);font-size:13px}

.badge{padding:5px 11px;border-radius:999px;font-size:12px;font-weight:600;justify-self:start;white-space:nowrap}
.badge.warn{background:rgba(255,193,7,.15);color:#e0a800}
.badge.ok{background:rgba(52,215,127,.15);color:var(--emerald-soft)}
.badge.info{background:rgba(33,150,243,.15);color:#42a5f5}
.badge.bad{background:rgba(231,76,60,.15);color:#ff7a6b}

.empty{color:var(--muted);text-align:center;padding:30px;font-size:14px}

@media(max-width:1000px){.cards{grid-template-columns:1fr 1fr}.grid2{grid-template-columns:1fr}}
@media(max-width:620px){.othead{display:none}.orow{grid-template-columns:1fr auto;row-gap:6px}}
</style>
