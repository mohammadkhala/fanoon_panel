<script setup>
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ reviews: Array, filters: Object, counts: Object });

function filter(f) {
    router.get(route('admin.reviews.index'), f ? { filter: f } : {}, { preserveScroll: true, preserveState: true, replace: true });
}
function toggle(r) { router.patch(route('admin.reviews.toggle', r.id), {}, { preserveScroll: true }); }
function destroy(r) { if (confirm('حذف التقييم؟')) router.delete(route('admin.reviews.destroy', r.id), { preserveScroll: true }); }
const stars = (n) => '★'.repeat(n) + '☆'.repeat(5 - n);
</script>

<template>
    <Head title="التقييمات — الإدارة" />
    <AdminLayout title="التقييمات" subtitle="مراجعة تقييمات العملاء واعتمادها للنشر">
        <div class="chips">
            <button class="chip" :class="{ on: !filters.filter }" @click="filter('')">الكل <span>{{ counts.all }}</span></button>
            <button class="chip" :class="{ on: filters.filter === 'pending' }" @click="filter('pending')">قيد المراجعة <span>{{ counts.pending }}</span></button>
            <button class="chip" :class="{ on: filters.filter === 'approved' }" @click="filter('approved')">معتمدة <span>{{ counts.approved }}</span></button>
        </div>

        <div class="panel">
            <div v-if="reviews.length" class="list">
                <div v-for="r in reviews" :key="r.id" class="ritem">
                    <div class="rtop">
                        <div>
                            <span class="rating">{{ stars(r.rating) }}</span>
                            <span class="cust">{{ r.customer }}</span>
                            <span v-if="r.template" class="tpl">· {{ r.template }}</span>
                        </div>
                        <span class="badge" :class="r.is_approved ? 'ok' : 'warn'">{{ r.is_approved ? 'معتمد' : 'قيد المراجعة' }}</span>
                    </div>
                    <p v-if="r.comment" class="comment">{{ r.comment }}</p>
                    <div class="racts">
                        <span class="date lat">{{ r.created_at }}</span>
                        <div class="btns">
                            <button class="q ok" @click="toggle(r)">{{ r.is_approved ? 'إلغاء الاعتماد' : 'اعتماد' }}</button>
                            <button class="q bad" @click="destroy(r)">حذف</button>
                        </div>
                    </div>
                </div>
            </div>
            <p v-else class="empty">لا توجد تقييمات.</p>
        </div>
    </AdminLayout>
</template>

<style scoped>
.chips{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px}
.chip{display:flex;align-items:center;gap:8px;padding:9px 16px;border-radius:999px;border:1px solid var(--hair);background:var(--bg2);color:var(--muted);font-size:13px;cursor:pointer;font-family:inherit}
.chip span{background:var(--glass);border-radius:999px;padding:1px 8px;font-size:11px}
.chip.on{background:var(--emerald);color:var(--on-emerald);border-color:var(--emerald)}
.panel{background:var(--bg2);border:1px solid var(--hair);border-radius:20px;padding:16px}
.list{display:flex;flex-direction:column;gap:12px}
.ritem{border:1px solid var(--hair);border-radius:14px;padding:16px}
.rtop{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.rating{color:#e0a800;font-size:15px;letter-spacing:2px}
.cust{font-weight:600;font-size:14px;margin-right:8px}
.tpl{color:var(--muted);font-size:13px}
.comment{font-size:14px;line-height:1.6}
.badge{padding:4px 11px;border-radius:999px;font-size:12px;font-weight:600}
.badge.ok{background:rgba(52,215,127,.15);color:var(--emerald-soft)}
.badge.warn{background:rgba(255,193,7,.15);color:#e0a800}
.racts{display:flex;align-items:center;justify-content:space-between;margin-top:12px}
.date{color:var(--muted);font-size:12px}
.btns{display:flex;gap:6px}
.q{border:none;border-radius:8px;padding:6px 12px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit}
.q.ok{background:rgba(52,215,127,.15);color:var(--emerald-soft)}
.q.bad{background:rgba(231,76,60,.1);color:#ff7a6b}
.empty{color:var(--muted);text-align:center;padding:40px}
</style>
