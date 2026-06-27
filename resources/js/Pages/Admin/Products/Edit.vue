<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({ product: Object, subcategories: Array, categories: Array });

// Cascading category → subcategory
const selectedCategoryId = ref(
    props.subcategories.find(s => s.id === props.product.subcategory_id)?.category_id ?? ''
);
const filteredSubs = computed(() =>
    selectedCategoryId.value
        ? props.subcategories.filter(s => s.category_id === selectedCategoryId.value)
        : []
);

function onCategoryChange() {
    form.subcategory_id = '';
}

const form = useForm({
    subcategory_id:  props.product.subcategory_id,
    name:            props.product.name,
    description:     props.product.description ?? '',
    retail_price:    props.product.retail_price,
    wholesale_price: props.product.wholesale_price,
    badge:           props.product.badge ?? '',
    is_active:       props.product.is_active,
    cover_image:     null,
    sizes:           [...(props.product.sizes ?? [])],
});

const coverPreview = ref(props.product.cover_image ? '/storage/' + props.product.cover_image : null);
const newSize = ref('');

function onCoverChange(e) {
    const file = e.target.files[0];
    if (!file) return;
    form.cover_image = file;
    const reader = new FileReader();
    reader.onload = ev => { coverPreview.value = ev.target.result; };
    reader.readAsDataURL(file);
}

function addSize() {
    const s = newSize.value.trim();
    if (s && !form.sizes.includes(s)) {
        form.sizes.push(s);
    }
    newSize.value = '';
}

function removeSize(s) {
    form.sizes = form.sizes.filter(x => x !== s);
}

function saveProduct() {
    form.patch(route('admin.products.update', props.product.id), { forceFormData: true });
}

// Template management
const tmplForm = useForm({ name: '', preview_image: null, canva_template_url: '' });
const tmplPreview = ref(null);

function onTmplImage(e) {
    const file = e.target.files[0];
    tmplForm.preview_image = file;
    if (file) {
        const reader = new FileReader();
        reader.onload = ev => { tmplPreview.value = ev.target.result; };
        reader.readAsDataURL(file);
    }
}

function addTemplate() {
    tmplForm.post(route('admin.products.templates.store', props.product.id), {
        forceFormData: true,
        onSuccess: () => { tmplForm.reset(); tmplPreview.value = null; },
    });
}

function deleteTemplate(t) {
    if (!confirm(`حذف قالب "${t.name}"؟`)) return;
    router.delete(route('admin.products.templates.destroy', t.id));
}
</script>

<template>
    <Head :title="'تعديل: ' + product.name" />
    <AdminLayout :title="'تعديل: ' + product.name" subtitle="تعديل بيانات المنتج وقوالبه">
        <div class="edit-wrap">

            <!-- LEFT: Product details -->
            <div class="left-col">

                <!-- Product form -->
                <div class="card">
                    <h2 class="card-title">بيانات المنتج</h2>
                    <form @submit.prevent="saveProduct" class="prod-form">

                        <label class="lbl">التصنيف الرئيسي
                            <select v-model="selectedCategoryId" class="inp" @change="onCategoryChange">
                                <option value="">اختر التصنيف…</option>
                                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </label>

                        <label class="lbl">التصنيف الفرعي
                            <select v-model="form.subcategory_id" class="inp" :disabled="!selectedCategoryId" required>
                                <option value="">{{ selectedCategoryId ? 'اختر التصنيف الفرعي…' : 'اختر التصنيف الرئيسي أولاً' }}</option>
                                <option v-for="s in filteredSubs" :key="s.id" :value="s.id">{{ s.name }}</option>
                            </select>
                        </label>

                        <label class="lbl">اسم المنتج
                            <input v-model="form.name" class="inp" required>
                        </label>
                        <p v-if="form.errors.name" class="ferr">{{ form.errors.name }}</p>

                        <div class="two-col">
                            <label class="lbl">سعر التجزئة (₪)
                                <input v-model="form.retail_price" class="inp" type="number" min="0" step="0.01">
                            </label>
                            <label class="lbl">سعر الجملة (₪)
                                <input v-model="form.wholesale_price" class="inp" type="number" min="0" step="0.01">
                            </label>
                        </div>

                        <label class="lbl">شارة (اختياري)
                            <input v-model="form.badge" class="inp" placeholder="الأكثر طلباً">
                        </label>

                        <label class="lbl">الوصف
                            <textarea v-model="form.description" class="inp" rows="3"></textarea>
                        </label>

                        <label class="lbl toggle-lbl">
                            <input type="checkbox" v-model="form.is_active" class="cbx">
                            <span class="tog" :class="{ on: form.is_active }"><span class="tog-dot"></span></span>
                            <span>مفعّل</span>
                        </label>

                        <div class="form-actions">
                            <button type="submit" class="save-btn" :disabled="form.processing">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>

                <!-- Cover image card -->
                <div class="card">
                    <h2 class="card-title">صورة الغلاف</h2>
                    <div class="cover-zone" @click="$refs.coverInput.click()">
                        <img v-if="coverPreview" :src="coverPreview" class="cover-preview">
                        <div v-else class="cover-hint">
                            <div class="cover-icon">🖼</div>
                            <div>انقر لرفع صورة الغلاف</div>
                            <div class="cover-sub">JPG / PNG / WEBP — حد أقصى 4MB</div>
                        </div>
                    </div>
                    <input ref="coverInput" type="file" accept="image/*" class="hidden-file" @change="onCoverChange">
                    <p v-if="form.errors.cover_image" class="ferr">{{ form.errors.cover_image }}</p>
                    <p v-if="coverPreview && !form.cover_image" class="current-note">✓ الصورة الحالية — انقر لتغييرها</p>
                </div>

                <!-- Sizes card -->
                <div class="card">
                    <h2 class="card-title">المقاسات المتاحة</h2>
                    <p class="card-sub">أضف مقاسات المنتج (A4, 10×15cm, 50×70cm…) لعرضها للزبائن</p>
                    <div class="sizes-list">
                        <div v-for="s in form.sizes" :key="s" class="size-chip">
                            <span>{{ s }}</span>
                            <button type="button" class="size-del" @click="removeSize(s)">×</button>
                        </div>
                        <div v-if="!form.sizes.length" class="no-sizes">لا توجد مقاسات مضافة بعد</div>
                    </div>
                    <div class="size-add-row">
                        <input
                            v-model="newSize"
                            class="inp size-inp"
                            placeholder="أدخل مقاساً مثل: A4 أو 10×15cm"
                            @keydown.enter.prevent="addSize"
                        >
                        <button type="button" class="add-size-btn" @click="addSize">+ إضافة</button>
                    </div>
                    <p class="size-hint">💡 اضغط Enter أو "إضافة" لحفظ كل مقاس. ثم احفظ التغييرات أعلاه.</p>
                </div>
            </div>

            <!-- RIGHT: Templates -->
            <div class="card">
                <div class="card-head">
                    <h2 class="card-title">القوالب ({{ product.templates.length }}/3)</h2>
                </div>

                <!-- Existing templates -->
                <div class="tmpl-grid">
                    <div v-for="t in product.templates" :key="t.id" class="tmpl-card">
                        <div class="tmpl-img-wrap">
                            <img v-if="t.preview_image" :src="'/storage/' + t.preview_image" class="tmpl-img" :alt="t.name">
                            <div v-else class="tmpl-placeholder">🖼</div>
                        </div>
                        <div class="tmpl-info">
                            <div class="tmpl-name">{{ t.name }}</div>
                            <div v-if="t.canva_template_url" class="tmpl-canva">🎨 رابط كانفا مُعيَّن</div>
                            <div v-else class="tmpl-no-canva">لا يوجد رابط كانفا</div>
                        </div>
                        <button class="del-tmpl" @click="deleteTemplate(t)">🗑</button>
                    </div>

                    <!-- Empty slots -->
                    <div v-for="n in (3 - product.templates.length)" :key="'empty-' + n" class="tmpl-card empty">
                        <div class="tmpl-placeholder big">+</div>
                        <div class="empty-label">خانة فارغة</div>
                    </div>
                </div>

                <!-- Add template form -->
                <div v-if="product.templates.length < 3" class="add-tmpl-form">
                    <h3 class="sub-title">إضافة قالب جديد</h3>
                    <form @submit.prevent="addTemplate" class="tmpl-add-grid">

                        <label class="lbl">اسم القالب
                            <input v-model="tmplForm.name" class="inp" required placeholder="كلاسيكي">
                        </label>

                        <label class="lbl">رابط Canva (اختياري)
                            <input v-model="tmplForm.canva_template_url" class="inp" placeholder="https://www.canva.com/design/XXX/copy">
                        </label>
                        <p v-if="tmplForm.errors.canva_template_url" class="ferr">{{ tmplForm.errors.canva_template_url }}</p>

                        <label class="lbl">صورة معاينة
                            <div class="file-zone" @click="$refs.fileInput.click()">
                                <img v-if="tmplPreview" :src="tmplPreview" class="preview-img">
                                <div v-else class="file-hint">📎 انقر لرفع صورة (JPG/PNG)</div>
                            </div>
                            <input ref="fileInput" type="file" accept="image/*" class="hidden-file" @change="onTmplImage">
                        </label>

                        <button type="submit" class="save-btn" :disabled="tmplForm.processing">إضافة القالب</button>
                    </form>
                </div>
                <p v-else class="full-note">✓ وصلت للحد الأقصى (3 قوالب)</p>
            </div>

        </div>
    </AdminLayout>
</template>

<style scoped>
.edit-wrap { display: grid; grid-template-columns: 1fr 400px; gap: 24px; align-items: start; }
@media (max-width: 1000px) { .edit-wrap { grid-template-columns: 1fr; } }

.left-col { display: flex; flex-direction: column; gap: 20px; }

.card { background: var(--bg2); border: 1px solid var(--hair); border-radius: 20px; padding: 24px; }
.card-head { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; }
.card-title { font-size: 17px; font-weight: 700; margin-bottom: 18px; }
.card-sub { font-size: 13px; color: var(--muted); margin-top: -12px; margin-bottom: 14px; }

.prod-form { display: flex; flex-direction: column; gap: 14px; }
.lbl { font-size: 13px; color: var(--muted); display: flex; flex-direction: column; gap: 6px; }
.inp { background: var(--bg); border: 1.5px solid var(--hair); border-radius: 12px; color: var(--ink); font-size: 14px; padding: 10px 14px; font-family: inherit; width: 100%; box-sizing: border-box; }
.inp:focus { outline: none; border-color: var(--emerald); }
.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.ferr { font-size: 12px; color: #ff7a6b; margin-top: -8px; }

.toggle-lbl { flex-direction: row; align-items: center; gap: 10px; cursor: pointer; }
.cbx { position: absolute; opacity: 0; width: 0; height: 0; }
.tog { width: 36px; height: 20px; border-radius: 999px; background: var(--hair); border: 1px solid var(--hair); position: relative; flex-shrink: 0; transition: all .3s; }
.tog.on { background: rgba(16,180,106,.2); border-color: rgba(16,180,106,.4); }
.tog-dot { position: absolute; width: 14px; height: 14px; border-radius: 50%; background: var(--muted); top: 2px; right: 2px; transition: all .3s; }
.tog.on .tog-dot { right: auto; left: 2px; background: var(--emerald-soft); }

.form-actions { display: flex; justify-content: flex-end; }
.save-btn { background: linear-gradient(150deg, var(--emerald-soft), var(--emerald-deep)); color: var(--on-emerald); border: none; border-radius: 12px; padding: 11px 24px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: inherit; }
.save-btn:disabled { opacity: .5; }

/* Cover image */
.cover-zone { border: 2px dashed var(--hair); border-radius: 16px; min-height: 160px; cursor: pointer; overflow: hidden; display: flex; align-items: center; justify-content: center; transition: border-color .3s; }
.cover-zone:hover { border-color: rgba(52,215,127,.5); }
.cover-preview { width: 100%; max-height: 240px; object-fit: cover; }
.cover-hint { text-align: center; color: var(--muted); padding: 30px; display: flex; flex-direction: column; align-items: center; gap: 8px; }
.cover-icon { font-size: 36px; opacity: .5; }
.cover-sub { font-size: 12px; }
.current-note { font-size: 12px; color: var(--emerald-soft); margin-top: 8px; }

/* Sizes */
.sizes-list { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; min-height: 36px; }
.size-chip { display: flex; align-items: center; gap: 6px; background: rgba(52,215,127,.1); border: 1px solid rgba(52,215,127,.3); border-radius: 999px; padding: 4px 12px; font-size: 13px; font-weight: 600; color: var(--emerald-soft); }
.size-del { background: none; border: none; color: var(--emerald-soft); cursor: pointer; font-size: 16px; line-height: 1; padding: 0; font-weight: 700; }
.no-sizes { font-size: 13px; color: var(--muted); font-style: italic; padding: 6px 0; }
.size-add-row { display: flex; gap: 8px; }
.size-inp { flex: 1; }
.add-size-btn { background: var(--glass); border: 1px solid var(--hair); border-radius: 12px; padding: 10px 16px; font-size: 13px; font-weight: 600; cursor: pointer; color: var(--ink); font-family: inherit; white-space: nowrap; }
.add-size-btn:hover { border-color: rgba(52,215,127,.4); color: var(--emerald-soft); }
.size-hint { font-size: 12px; color: var(--muted); margin-top: 8px; }
.hidden-file { display: none; }

/* Templates */
.tmpl-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px; }
.tmpl-card { background: var(--bg); border: 1.5px solid var(--hair); border-radius: 14px; overflow: hidden; position: relative; }
.tmpl-card.empty { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; opacity: .4; }
.tmpl-img-wrap { aspect-ratio: 1; overflow: hidden; background: var(--glass); }
.tmpl-img { width: 100%; height: 100%; object-fit: cover; }
.tmpl-placeholder { display: flex; align-items: center; justify-content: center; aspect-ratio: 1; font-size: 28px; color: var(--muted); opacity: .4; }
.tmpl-placeholder.big { font-size: 36px; }
.tmpl-info { padding: 10px 12px; }
.tmpl-name { font-size: 13px; font-weight: 600; }
.tmpl-canva { font-size: 11px; color: var(--emerald-soft); margin-top: 4px; }
.tmpl-no-canva { font-size: 11px; color: var(--muted); margin-top: 4px; opacity: .6; }
.empty-label { font-size: 12px; color: var(--muted); margin-top: 6px; }
.del-tmpl { position: absolute; top: 8px; left: 8px; background: rgba(0,0,0,.5); border: none; border-radius: 8px; color: #fff; width: 28px; height: 28px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; }

.sub-title { font-size: 15px; font-weight: 600; margin-bottom: 14px; }
.add-tmpl-form { border-top: 1px solid var(--hair); padding-top: 20px; }
.tmpl-add-grid { display: flex; flex-direction: column; gap: 14px; }
.file-zone { border: 2px dashed var(--hair); border-radius: 12px; min-height: 100px; display: flex; align-items: center; justify-content: center; cursor: pointer; overflow: hidden; transition: border-color .25s; }
.file-zone:hover { border-color: var(--emerald); }
.file-hint { color: var(--muted); font-size: 13px; padding: 20px; text-align: center; }
.preview-img { width: 100%; height: 120px; object-fit: cover; }
.full-note { text-align: center; color: var(--emerald-soft); font-size: 13px; border-top: 1px solid var(--hair); padding-top: 16px; }
</style>
