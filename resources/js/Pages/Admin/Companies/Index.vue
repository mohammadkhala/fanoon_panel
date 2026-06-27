<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';

defineProps({ companies: Array });

const openNotes = ref(null);   // which card has notes expanded
const notes     = ref({});

const tone  = { pending: 'warn', approved: 'ok', rejected: 'bad' };
const label = { pending: 'بانتظار المراجعة', approved: 'معتمدة', rejected: 'مرفوضة' };

function review(c, status) {
    router.patch(
        route('admin.companies.update', c.id),
        { status, review_notes: notes.value[c.id] || '' },
        { preserveScroll: true }
    );
    openNotes.value = null;
}

function toggleNotes(id) {
    openNotes.value = openNotes.value === id ? null : id;
}
</script>

<template>
    <Head title="طلبات الشركات — الإدارة" />
    <AdminLayout title="طلبات الشركات" subtitle="راجع السجلّات التجارية واعتمد الشركات لأسعار الجملة">

        <p v-if="!companies.length" class="empty">لا توجد طلبات شركات حتى الآن.</p>

        <div v-else class="clist">
            <div v-for="c in companies" :key="c.id" class="ccard">

                <!-- ── Main row ── -->
                <div class="crow">

                    <!-- Identity -->
                    <div class="cid">
                        <div class="cav">{{ (c.company_name || '?').charAt(0) }}</div>
                        <div class="ctext">
                            <div class="cname">{{ c.company_name }}</div>
                            <div class="cmeta">{{ c.contact }}<span v-if="c.city"> · {{ c.city }}</span></div>
                        </div>
                    </div>

                    <!-- Status badge -->
                    <div class="cstatus">
                        <span class="badge" :class="tone[c.status]">{{ label[c.status] }}</span>
                    </div>

                    <!-- Details -->
                    <div class="cdetails">
                        <div class="drow">
                            <span class="dl">البريد</span>
                            <span class="lat">{{ c.email || '—' }}</span>
                        </div>
                        <div class="drow">
                            <span class="dl">الهاتف</span>
                            <span class="lat">{{ c.phone || '—' }}</span>
                        </div>
                        <div class="drow">
                            <span class="dl">رقم السجل</span>
                            <span class="lat">{{ c.trade_license_no || '—' }}</span>
                        </div>
                        <div class="drow">
                            <span class="dl">تاريخ التسجيل</span>
                            <span class="lat">{{ c.created_at }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="cacts">
                        <Link :href="route('admin.companies.show', c.id)" class="abtn view">🔍 عرض</Link>
                        <button class="abtn ok"      @click="review(c, 'approved')">✓ اعتماد</button>
                        <button class="abtn bad"     @click="review(c, 'rejected')">✕ رفض</button>
                        <button class="abtn neutral" @click="review(c, 'pending')">⏸ مراجعة</button>
                    </div>
                </div>

                <!-- License + notes row -->
                <div class="cfoot">
                    <a
                        v-if="c.trade_license_url"
                        :href="c.trade_license_url"
                        target="_blank"
                        rel="noopener"
                        class="lic-btn"
                    >📄 عرض السجل التجاري ↗</a>
                    <span v-else class="no-lic">⚠️ لم يُرفع سجل تجاري</span>

                    <button class="note-toggle" @click="toggleNotes(c.id)">
                        {{ openNotes === c.id ? '▲ إخفاء الملاحظات' : '✏️ إضافة ملاحظة' }}
                    </button>
                </div>

                <!-- Notes textarea (expandable) -->
                <div v-if="openNotes === c.id" class="cnotes">
                    <textarea
                        v-model="notes[c.id]"
                        rows="3"
                        placeholder="أضف ملاحظات المراجعة (اختياري)…"
                    ></textarea>
                    <div class="note-acts">
                        <button class="abtn ok"  @click="review(c, c.status)">💾 حفظ الملاحظة</button>
                        <button class="abtn bad" @click="review(c, 'rejected')">✕ رفض مع الملاحظة</button>
                        <button class="abtn ok"  @click="review(c, 'approved')">✓ اعتماد مع الملاحظة</button>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.empty { color: var(--muted); text-align: center; padding: 60px; font-size: 14px; }

/* Card list — single column, full width */
.clist {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Card */
.ccard {
    background: var(--bg2);
    border: 1.5px solid var(--hair);
    border-radius: 18px;
    overflow: hidden;
    transition: border-color .25s;
}
.ccard:hover { border-color: var(--hair2); }

/* Main row — horizontal flex, full width */
.crow {
    display: flex;
    align-items: center;
    gap: 0;
    padding: 18px 20px;
    min-width: 0;
}

/* ── Identity (right side in RTL) ── */
.cid {
    display: flex;
    align-items: center;
    gap: 13px;
    min-width: 220px;
    flex-shrink: 0;
    padding-left: 20px;
    border-left: 1px solid var(--hair);
}
.cav {
    width: 46px; height: 46px;
    border-radius: 13px;
    background: linear-gradient(145deg, var(--emerald-soft), var(--emerald-deep));
    color: var(--on-emerald);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; font-weight: 800;
    flex-shrink: 0;
}
.ctext { min-width: 0; }
.cname { font-weight: 700; font-size: 15px; margin-bottom: 3px; }
.cmeta { font-size: 12px; color: var(--muted); }

/* ── Status ── */
.cstatus {
    flex-shrink: 0;
    padding: 0 20px;
    border-left: 1px solid var(--hair);
}

/* ── Details (takes remaining space) ── */
.cdetails {
    flex: 1;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px 24px;
    padding: 0 20px;
    min-width: 0;
    border-left: 1px solid var(--hair);
}
.drow { display: flex; flex-direction: column; gap: 2px; }
.dl   { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; }
.drow span:not(.dl) { font-size: 13px; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* ── Action buttons ── */
.cacts {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding-right: 20px;
    flex-shrink: 0;
}
.abtn {
    padding: 7px 16px;
    border-radius: 9px;
    border: 1px solid transparent;
    font-size: 12px; font-weight: 700;
    cursor: pointer; font-family: inherit;
    white-space: nowrap;
    transition: all .2s;
}
.abtn.ok      { background: rgba(52,215,127,.12); color: var(--emerald-soft); border-color: rgba(52,215,127,.25); }
.abtn.ok:hover{ background: rgba(52,215,127,.22); }
.abtn.bad     { background: rgba(231,76,60,.1);  color: #ff7a6b; border-color: rgba(231,76,60,.25); }
.abtn.bad:hover{ background: rgba(231,76,60,.2); }
.abtn.neutral { background: var(--glass); color: var(--muted); border-color: var(--hair); }
.abtn.neutral:hover { border-color: var(--hair2); color: var(--ink); }
.abtn.view  { background: var(--glass); color: var(--ink); border-color: var(--hair); text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
.abtn.view:hover { background: var(--hair); }

/* Badges */
.badge { padding: 5px 13px; border-radius: 999px; font-size: 12px; font-weight: 600; white-space: nowrap; }
.badge.warn { background: rgba(255,193,7,.15);  color: #e0a800; }
.badge.ok   { background: rgba(52,215,127,.15); color: var(--emerald-soft); }
.badge.bad  { background: rgba(231,76,60,.15);  color: #ff7a6b; }

/* Footer row */
.cfoot {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 10px 20px;
    border-top: 1px solid var(--hair);
    background: var(--bg);
    flex-wrap: wrap;
}
.lic-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px;
    border-radius: 9px;
    background: rgba(33,150,243,.1);
    color: #42a5f5;
    font-size: 13px; font-weight: 600;
    text-decoration: none;
    transition: background .2s;
}
.lic-btn:hover { background: rgba(33,150,243,.2); }
.no-lic {
    font-size: 13px; color: var(--muted);
}
.note-toggle {
    margin-right: auto;
    background: none; border: none;
    color: var(--muted); font-size: 12px;
    cursor: pointer; font-family: inherit;
    padding: 4px 8px; border-radius: 6px;
    transition: all .2s;
}
.note-toggle:hover { background: var(--glass); color: var(--ink); }

/* Notes expand */
.cnotes {
    padding: 14px 20px;
    border-top: 1px solid var(--hair);
    background: var(--bg);
}
.cnotes textarea {
    width: 100%;
    background: var(--bg2);
    border: 1px solid var(--hair);
    border-radius: 10px;
    padding: 10px 13px;
    color: var(--ink);
    font-family: inherit; font-size: 13px;
    outline: none; resize: vertical;
    margin-bottom: 10px;
    box-sizing: border-box;
}
.cnotes textarea:focus { border-color: var(--emerald-soft); }
.note-acts { display: flex; gap: 8px; flex-wrap: wrap; }

/* Responsive */
@media (max-width: 900px) {
    .crow { flex-wrap: wrap; gap: 14px; }
    .cid  { min-width: unset; border-left: none; padding-left: 0; border-bottom: 1px solid var(--hair); padding-bottom: 12px; width: 100%; }
    .cstatus { padding: 0; border-left: none; }
    .cdetails { border-left: none; padding: 0; grid-template-columns: repeat(2, 1fr); border-top: 1px solid var(--hair); padding-top: 12px; width: 100%; }
    .cacts { flex-direction: row; padding-right: 0; border-top: 1px solid var(--hair); padding-top: 12px; width: 100%; }
}
</style>
