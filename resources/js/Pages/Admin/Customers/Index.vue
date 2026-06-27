<script setup>
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, watch } from 'vue';

const props = defineProps({ customers: Array, filters: Object, counts: Object });

const search = ref(props.filters.q || '');
let t = null;
watch(search, (v) => {
    clearTimeout(t);
    t = setTimeout(() => apply({ q: v }), 350);
});

function apply(patch) {
    const params = { type: props.filters.type, q: search.value, ...patch };
    Object.keys(params).forEach((k) => { if (!params[k]) delete params[k]; });
    router.get(route('admin.customers.index'), params, { preserveScroll: true, preserveState: true, replace: true });
}

const typeLabel   = { individual: 'فرد', company: 'شركة' };
const compTone    = { approved: 'ok', pending: 'warn', rejected: 'bad' };
const compLabel   = { approved: 'معتمدة', pending: 'قيد المراجعة', rejected: 'مرفوضة' };

function setCompany(c, status) {
    router.patch(route('admin.customers.update', c.id), { company_status: status }, { preserveScroll: true });
}

// Expanded company details
const expanded = ref(null);
function toggle(id) {
    expanded.value = expanded.value === id ? null : id;
}
</script>

<template>
    <Head title="العملاء — الإدارة" />
    <AdminLayout title="العملاء" subtitle="جميع حسابات الأفراد والشركات">

        <!-- Toolbar -->
        <div class="bar">
            <div class="chips">
                <button class="chip" :class="{ on: !filters.type }" @click="apply({ type: '' })">
                    الكل <span class="cc">{{ counts.all }}</span>
                </button>
                <button class="chip" :class="{ on: filters.type === 'individual' }" @click="apply({ type: 'individual' })">
                    أفراد <span class="cc">{{ counts.individual }}</span>
                </button>
                <button class="chip" :class="{ on: filters.type === 'company' }" @click="apply({ type: 'company' })">
                    شركات <span class="cc">{{ counts.company }}</span>
                </button>
            </div>
            <input v-model="search" class="search" placeholder="بحث بالاسم أو البريد أو الهاتف…">
        </div>

        <!-- Empty -->
        <p v-if="!customers.length" class="empty">لا يوجد عملاء مطابقون.</p>

        <!-- Customer cards -->
        <div v-else class="clist">
            <div v-for="c in customers" :key="c.id" class="ccard">

                <!-- Main row -->
                <div class="crow">

                    <!-- Avatar + identity -->
                    <div class="cid">
                        <div class="av" :class="c.account_type">{{ (c.name || '?').charAt(0) }}</div>
                        <div class="cinfo">
                            <div class="cnm">
                                {{ c.name }}
                                <span v-if="c.company_name" class="company-tag">{{ c.company_name }}</span>
                            </div>
                            <div class="cmeta lat">
                                {{ c.email }}
                                <template v-if="c.phone">
                                    <span class="dot">·</span>
                                    {{ c.phone }}
                                </template>
                            </div>
                            <div class="cbottom">
                                <span class="atype-pill" :class="c.account_type">{{ typeLabel[c.account_type] }}</span>
                                <span class="cmuted">تسجيل {{ c.created_at }}</span>
                                <span class="cmuted">· {{ c.orders_count }} طلب</span>
                            </div>
                        </div>
                    </div>

                    <!-- Company status / actions -->
                    <div class="cactions">
                        <template v-if="c.account_type === 'company'">
                            <span class="badge" :class="compTone[c.company_status] || 'warn'">
                                {{ compLabel[c.company_status] || 'قيد المراجعة' }}
                            </span>
                            <div class="abtnrow">
                                <button
                                    v-if="c.company_status !== 'approved'"
                                    class="abtn ok"
                                    @click="setCompany(c, 'approved')"
                                >✓ اعتماد</button>
                                <button
                                    v-if="c.company_status !== 'rejected'"
                                    class="abtn bad"
                                    @click="setCompany(c, 'rejected')"
                                >✕ رفض</button>
                                <button
                                    v-if="c.trade_license_no || c.company_city"
                                    class="abtn neutral"
                                    @click="toggle(c.id)"
                                >
                                    {{ expanded === c.id ? '▲ إخفاء' : '▼ السجل' }}
                                </button>
                            </div>
                        </template>
                        <span v-else class="cmuted">—</span>
                    </div>
                </div>

                <!-- Expanded company details -->
                <div v-if="expanded === c.id && c.account_type === 'company'" class="cexpand">
                    <div class="exgrid">
                        <div v-if="c.company_name" class="exfield">
                            <span class="exlabel">اسم الشركة</span>
                            <span>{{ c.company_name }}</span>
                        </div>
                        <div v-if="c.trade_license_no" class="exfield">
                            <span class="exlabel">رقم السجل التجاري</span>
                            <span class="lat">{{ c.trade_license_no }}</span>
                        </div>
                        <div v-if="c.company_city" class="exfield">
                            <span class="exlabel">المدينة</span>
                            <span>{{ c.company_city }}</span>
                        </div>
                        <div v-if="c.review_notes" class="exfield">
                            <span class="exlabel">ملاحظات المراجعة</span>
                            <span>{{ c.review_notes }}</span>
                        </div>
                    </div>
                    <div v-if="c.trade_license_path" class="exfile">
                        <a
                            :href="'/storage/' + c.trade_license_path"
                            target="_blank"
                            rel="noopener"
                            class="file-btn"
                        >
                            📄 عرض السجل التجاري
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
/* Toolbar */
.bar    { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-bottom: 20px; flex-wrap: wrap; }
.chips  { display: flex; gap: 8px; flex-wrap: wrap; }
.chip   {
    display: flex; align-items: center; gap: 7px;
    padding: 8px 16px; border-radius: 999px;
    border: 1px solid var(--hair); background: var(--bg2);
    color: var(--muted); font-size: 13px; cursor: pointer;
    font-family: inherit; transition: all .25s;
}
.chip:hover { border-color: var(--hair2); color: var(--ink); }
.chip.on    { background: var(--emerald); color: var(--on-emerald); border-color: var(--emerald); }
.cc {
    min-width: 20px; height: 20px; padding: 0 5px;
    border-radius: 999px; background: rgba(0,0,0,.12);
    font-size: 11px; display: flex; align-items: center; justify-content: center;
}
.chip:not(.on) .cc { background: var(--glass); }

.search {
    flex: 1; min-width: 200px; max-width: 320px;
    background: var(--bg2); border: 1px solid var(--hair);
    border-radius: 12px; padding: 10px 14px;
    color: var(--ink); font-family: inherit; font-size: 14px; outline: none;
}
.search:focus { border-color: var(--emerald); }

.empty { color: var(--muted); text-align: center; padding: 50px; font-size: 14px; }

/* Card list */
.clist { display: flex; flex-direction: column; gap: 10px; }

.ccard {
    background: var(--bg2);
    border: 1px solid var(--hair);
    border-radius: 16px;
    overflow: hidden;
    transition: border-color .25s;
}
.ccard:hover { border-color: var(--hair2); }

/* Main row */
.crow {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
    flex-wrap: wrap;
}

/* Identity */
.cid   { display: flex; align-items: flex-start; gap: 12px; flex: 1; min-width: 0; }
.av    {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; font-weight: 700; flex-shrink: 0;
}
.av.individual { background: var(--glass); color: var(--muted); }
.av.company    { background: linear-gradient(140deg, var(--emerald-soft), var(--emerald-deep)); color: var(--on-emerald); }

.cinfo { min-width: 0; }
.cnm   { font-weight: 700; font-size: 15px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 3px; }
.company-tag {
    font-size: 12px; font-weight: 500; color: #42a5f5;
    background: rgba(33,150,243,.1); padding: 2px 8px; border-radius: 999px;
}
.cmeta  { font-size: 12px; color: var(--muted); margin-bottom: 6px; }
.dot    { margin: 0 5px; color: var(--hair2); }
.cbottom{ display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.cmuted { font-size: 12px; color: var(--muted); }

/* Type pills */
.atype-pill { padding: 3px 9px; border-radius: 999px; font-size: 11px; font-weight: 600; }
.atype-pill.individual { background: var(--glass); color: var(--muted); }
.atype-pill.company    { background: rgba(33,150,243,.12); color: #42a5f5; }

/* Action area */
.cactions { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0; }
.abtnrow  { display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end; }

.abtn {
    padding: 6px 14px; border-radius: 10px; border: none;
    font-size: 12px; font-weight: 700; cursor: pointer;
    font-family: inherit; transition: all .2s; white-space: nowrap;
}
.abtn.ok      { background: rgba(52,215,127,.14); color: var(--emerald-soft); }
.abtn.ok:hover{ background: rgba(52,215,127,.25); }
.abtn.bad     { background: rgba(231,76,60,.1); color: #ff7a6b; }
.abtn.bad:hover{ background: rgba(231,76,60,.2); }
.abtn.neutral { background: var(--glass); color: var(--muted); }
.abtn.neutral:hover { background: var(--hair); }

/* Badges */
.badge { padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
.badge.ok   { background: rgba(52,215,127,.15); color: var(--emerald-soft); }
.badge.warn { background: rgba(255,193,7,.15);  color: #e0a800; }
.badge.bad  { background: rgba(231,76,60,.15);  color: #ff7a6b; }

/* Expanded section */
.cexpand {
    border-top: 1px solid var(--hair);
    padding: 14px 18px;
    background: var(--bg);
}
.exgrid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
    margin-bottom: 12px;
}
.exfield { display: flex; flex-direction: column; gap: 3px; }
.exlabel { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; }
.exfield span:not(.exlabel) { font-size: 14px; font-weight: 500; }

.exfile { }
.file-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 16px; border-radius: 10px;
    background: rgba(33,150,243,.1); color: #42a5f5;
    font-size: 13px; font-weight: 600; text-decoration: none;
    transition: background .2s;
}
.file-btn:hover { background: rgba(33,150,243,.2); }

@media (max-width: 640px) {
    .crow     { flex-direction: column; align-items: flex-start; }
    .cactions { align-items: flex-start; }
}
</style>
