<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    template:      Object,   // null on create, filled on edit
    categories:    Array,
    subcategories: Array,
    products:      Array,
});

const isEdit = computed(() => !!props.template);

/* ── form ── */
const form = useForm({
    product_id:         props.template?.product_id         ?? '',
    name:               props.template?.name               ?? '',
    canva_template_url: props.template?.canva_template_url ?? '',
    sort_order:         props.template?.sort_order         ?? 0,
    is_active:          props.template?.is_active          ?? true,
    image:              null,
});

/* ── cascading state ── */
const selCatId  = ref(props.template?.category_id    ?? '');
const selSubId  = ref(props.template?.subcategory_id ?? '');

const filteredSubs = computed(() =>
    selCatId.value
        ? props.subcategories.filter(s => s.category_id == selCatId.value)
        : props.subcategories
);

const filteredProds = computed(() =>
    selSubId.value
        ? props.products.filter(p => p.subcategory_id == selSubId.value)
        : (selCatId.value
            ? props.products.filter(p =>
                filteredSubs.value.some(s => s.id == p.subcategory_id))
            : props.products)
);

watch(selCatId, () => { selSubId.value = ''; form.product_id = ''; });
watch(selSubId, () => { form.product_id = ''; });

const previewUrl = ref(
    props.template?.preview_image ? '/storage/' + props.template.preview_image : null
);

function onFile(e) {
    const f = e.target.files[0];
    form.image = f ?? null;
    if (f) previewUrl.value = URL.createObjectURL(f);
}

function submit() {
    if (isEdit.value) {
        form.transform(d => ({ ...d, _method: 'PUT' }))
            .post(route('admin.templates.update', props.template.id), { forceFormData: true });
    } else {
        form.post(route('admin.templates.store'), { forceFormData: true });
    }
}
</script>

<template>
    <Head :title="(isEdit ? 'تعديل' : 'إضافة') + ' قالب'" />
    <AdminLayout :title="isEdit ? 'تعديل القالب' : 'قالب جديد'" subtitle="اربط القالب بالمنتج المناسب وأضف رابط كانفا">

        <Link :href="route('admin.templates.index')" class="back">→ كل القوالب</Link>

        <form @submit.prevent="submit" class="grid">
            <!-- ─── left panel ─── -->
            <div class="panel">

                <!-- cascading selectors -->
                <div class="frow">
                    <div class="fgrp" style="flex:1">
                        <label>التصنيف الرئيسي</label>
                        <select v-model="selCatId">
                            <option value="">— اختر تصنيفاً —</option>
                            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>
                    <div class="fgrp" style="flex:1">
                        <label>التصنيف الفرعي</label>
                        <select v-model="selSubId" :disabled="!selCatId">
                            <option value="">— اختر تصنيفاً فرعياً —</option>
                            <option v-for="s in filteredSubs" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </div>
                </div>

                <div class="fgrp">
                    <label>المنتج <span class="req">*</span></label>
                    <select v-model="form.product_id" required>
                        <option value="">— اختر منتجاً —</option>
                        <option
                            v-for="p in filteredProds" :key="p.id" :value="p.id"
                            :disabled="!isEdit && p.templates_count >= 3"
                        >
                            {{ p.name }}{{ (!isEdit && p.templates_count >= 3) ? ' (اكتمل 3/3)' : ` (${p.templates_count}/3)` }}
                        </option>
                    </select>
                    <div v-if="form.errors.product_id" class="err">{{ form.errors.product_id }}</div>
                </div>

                <!-- template details -->
                <div class="fgrp">
                    <label>اسم القالب <span class="req">*</span></label>
                    <input v-model="form.name" required placeholder="مثال: تصميم فاخر A">
                    <div v-if="form.errors.name" class="err">{{ form.errors.name }}</div>
                </div>

                <div class="fgrp">
                    <label>رابط قالب كانفا (اختياري)</label>
                    <input v-model="form.canva_template_url" type="url"
                           placeholder="https://www.canva.com/design/XXXXX/copy">
                    <div class="hint">رابط النسخ من كانفا يُعرض للزبون عند اختيار هذا القالب</div>
                    <div v-if="form.errors.canva_template_url" class="err">{{ form.errors.canva_template_url }}</div>
                </div>

                <div class="frow">
                    <div class="fgrp" style="flex:1">
                        <label>الترتيب</label>
                        <input type="number" min="0" v-model="form.sort_order">
                    </div>
                    <label class="toggle">
                        <input type="checkbox" v-model="form.is_active">
                        <span>مفعّل ومعروض في المتجر</span>
                    </label>
                </div>
            </div>

            <!-- ─── right panel (image) ─── -->
            <aside class="panel">
                <label class="up" :class="{ has: previewUrl }">
                    <img v-if="previewUrl" :src="previewUrl" alt="معاينة">
                    <div v-else class="upph">
                        <div class="big">🖼</div>
                        اضغط لرفع صورة المعاينة
                    </div>
                    <input type="file" accept=".jpg,.jpeg,.png,.webp" hidden @change="onFile">
                </label>
                <div v-if="form.errors.image" class="err">{{ form.errors.image }}</div>
                <p class="hint">JPG / PNG / WEBP — حتى 5MB. يُفضّل نسبة 4:3.</p>

                <button class="submit" :disabled="form.processing">
                    {{ form.processing ? 'جارٍ الحفظ…' : (isEdit ? 'حفظ التعديلات' : 'إضافة القالب') }}
                    <span class="bib">←</span>
                </button>
            </aside>
        </form>
    </AdminLayout>
</template>

<style scoped>
.back{display:inline-block;margin-bottom:18px;color:var(--muted);font-size:14px;text-decoration:none}
.back:hover{color:var(--ink)}
.grid{display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start}
.panel{background:var(--bg2);border:1px solid var(--hair);border-radius:20px;padding:22px}
.fgrp{margin-bottom:16px}
.fgrp label{display:block;font-size:12px;color:var(--muted);margin-bottom:7px}
.fgrp input,.fgrp select,.fgrp textarea{width:100%;background:var(--glass);border:1px solid var(--hair);border-radius:11px;padding:11px 13px;color:var(--ink);font-family:inherit;font-size:14px;outline:none;transition:border .3s var(--ease)}
.fgrp input:focus,.fgrp select:focus,.fgrp textarea:focus{border-color:var(--emerald)}
.fgrp select:disabled{opacity:.5;cursor:not-allowed}
.frow{display:flex;gap:14px}
.toggle{display:flex;align-items:center;gap:9px;font-size:13px;color:var(--ink);cursor:pointer;align-self:flex-end;padding-bottom:13px}
.toggle input{accent-color:var(--emerald);width:17px;height:17px}
.req{color:#ff7a6b}
.err{color:#ff7a6b;font-size:12px;margin-top:6px}
.hint{color:var(--muted);font-size:12px;font-weight:300;margin-top:8px;line-height:1.7}
.up{display:block;border:1.5px dashed var(--hair2);border-radius:16px;overflow:hidden;cursor:pointer;aspect-ratio:4/3;transition:border .3s}
.up:hover{border-color:var(--emerald)}
.up.has{border-style:solid}
.up img{width:100%;height:100%;object-fit:cover;display:block}
.upph{height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--muted);font-size:13px;gap:6px}
.upph .big{font-size:34px}
.submit{width:100%;display:flex;align-items:center;justify-content:center;gap:9px;background:linear-gradient(150deg,var(--emerald-soft),var(--emerald-deep));color:var(--on-emerald);border:none;border-radius:13px;padding:14px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;margin-top:8px}
.submit:disabled{opacity:.6}
.bib{width:24px;height:24px;border-radius:999px;background:rgba(3,26,13,.2);display:flex;align-items:center;justify-content:center}
@media(max-width:860px){.grid{grid-template-columns:1fr}}
</style>
