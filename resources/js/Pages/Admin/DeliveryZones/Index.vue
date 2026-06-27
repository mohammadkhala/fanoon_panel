<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';

const props = defineProps({ zones: Array });

const editingId = ref(null);
const form = useForm({ name: '', fee: 0, eta: '', sort_order: 0, is_active: true });

function resetForm() { editingId.value = null; form.reset(); form.clearErrors(); }
function edit(z) { editingId.value = z.id; form.name = z.name; form.fee = z.fee; form.eta = z.eta; form.sort_order = z.sort_order; form.is_active = z.is_active; }
function submit() {
    if (editingId.value) form.patch(route('admin.zones.update', editingId.value), { preserveScroll: true, onSuccess: resetForm });
    else form.post(route('admin.zones.store'), { preserveScroll: true, onSuccess: resetForm });
}
function toggle(z) { router.patch(route('admin.zones.toggle', z.id), {}, { preserveScroll: true }); }
function destroy(z) { if (confirm('حذف منطقة "' + z.name + '"؟')) router.delete(route('admin.zones.destroy', z.id), { preserveScroll: true }); }
const money = (n) => '₪' + Number(n).toLocaleString('en-US');
</script>

<template>
    <Head title="مناطق التوصيل — الإدارة" />
    <AdminLayout title="مناطق التوصيل" subtitle="حدّد المناطق ورسوم ومدة التوصيل لكل منها">
        <div class="cols">
            <form @submit.prevent="submit" class="panel">
                <h3>{{ editingId ? 'تعديل المنطقة' : 'منطقة جديدة' }}</h3>
                <div class="fgrp"><label>اسم المنطقة</label><input v-model="form.name" placeholder="الخليل" required>
                    <div v-if="form.errors.name" class="err">{{ form.errors.name }}</div></div>
                <div class="frow">
                    <div class="fgrp"><label>الرسوم (₪)</label><input type="number" step="0.01" min="0" v-model="form.fee" required></div>
                    <div class="fgrp"><label>الترتيب</label><input type="number" min="0" v-model="form.sort_order"></div>
                </div>
                <div class="fgrp"><label>مدة التوصيل</label><input v-model="form.eta" placeholder="3–5 أيام"></div>
                <label class="toggle"><input type="checkbox" v-model="form.is_active"><span>مُفعّلة</span></label>
                <div class="acts">
                    <button class="submit" :disabled="form.processing">{{ editingId ? 'حفظ' : 'إضافة' }}</button>
                    <button v-if="editingId" type="button" class="ghost" @click="resetForm">إلغاء</button>
                </div>
            </form>

            <div class="panel">
                <div v-if="zones.length" class="table">
                    <div class="thead"><span>المنطقة</span><span>الرسوم</span><span>المدة</span><span></span></div>
                    <div v-for="z in zones" :key="z.id" class="row" :class="{ off: !z.is_active }">
                        <span class="nm">{{ z.name }}</span>
                        <span>{{ money(z.fee) }}</span>
                        <span class="muted">{{ z.eta || '—' }}</span>
                        <div class="rowacts">
                            <button class="q" @click="edit(z)">تعديل</button>
                            <button class="q" @click="toggle(z)">{{ z.is_active ? 'إيقاف' : 'تفعيل' }}</button>
                            <button class="q bad" @click="destroy(z)">حذف</button>
                        </div>
                    </div>
                </div>
                <p v-else class="empty">لا توجد مناطق توصيل بعد.</p>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.cols{display:grid;grid-template-columns:320px 1fr;gap:20px;align-items:start}
.panel{background:var(--bg2);border:1px solid var(--hair);border-radius:20px;padding:22px}
h3{font-size:15px;font-weight:700;margin-bottom:16px}
.fgrp{margin-bottom:13px}
.fgrp label{display:block;font-size:12px;color:var(--muted);margin-bottom:6px}
.fgrp input{width:100%;background:var(--glass);border:1px solid var(--hair);border-radius:11px;padding:10px 12px;color:var(--ink);font-family:inherit;font-size:14px;outline:none}
.fgrp input:focus{border-color:var(--emerald)}
.frow{display:flex;gap:12px}.frow .fgrp{flex:1}
.toggle{display:flex;align-items:center;gap:9px;font-size:13px;cursor:pointer;margin:6px 0 14px}
.toggle input{accent-color:var(--emerald);width:17px;height:17px}
.acts{display:flex;gap:10px}
.submit{background:linear-gradient(150deg,var(--emerald-soft),var(--emerald-deep));color:var(--on-emerald);border:none;border-radius:12px;padding:11px 22px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit}
.ghost{background:var(--glass);border:1px solid var(--hair);color:var(--muted);border-radius:12px;padding:11px 18px;cursor:pointer;font-family:inherit;font-size:14px}
.err{color:#ff7a6b;font-size:12px;margin-top:5px}
.table{display:flex;flex-direction:column}
.thead,.row{display:grid;grid-template-columns:1.4fr .8fr 1fr auto;align-items:center;gap:12px}
.thead{padding:8px 12px 12px;color:var(--muted);font-size:12px;border-bottom:1px solid var(--hair)}
.row{padding:12px;border-radius:11px;font-size:14px}
.row:hover{background:var(--glass)}.row.off{opacity:.5}
.nm{font-weight:600}.muted{color:var(--muted);font-size:13px}
.rowacts{display:flex;gap:6px}
.q{border:none;background:var(--glass);border-radius:8px;padding:5px 10px;font-size:12px;cursor:pointer;color:var(--muted);font-family:inherit}
.q:hover{color:var(--ink)}.q.bad:hover{color:#ff7a6b}
.empty{color:var(--muted);text-align:center;padding:40px}
@media(max-width:900px){.cols{grid-template-columns:1fr}.thead{display:none}.row{grid-template-columns:1fr auto;row-gap:6px}}
</style>
