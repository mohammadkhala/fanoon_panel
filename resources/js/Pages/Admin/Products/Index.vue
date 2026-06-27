<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed, watch } from 'vue';

const props = defineProps({ products: Array, subcategories: Array, categories: Array });

const search = ref('');
const filterCatId = ref('');

const filtered = computed(() => {
    return props.products.filter(p => {
        const q = search.value.trim().toLowerCase();
        const matchSearch = !q || p.name.toLowerCase().includes(q) || p.subcategory.toLowerCase().includes(q);
        const matchCat = !filterCatId.value || p.category_id === filterCatId.value;
        return matchSearch && matchCat;
    });
});

// Add product modal — cascading category selector
const showAdd = ref(false);
const addCategoryId = ref('');
const filteredAddSubs = computed(() =>
    addCategoryId.value
        ? props.subcategories.filter(s => s.category_id === addCategoryId.value)
        : []
);
watch(addCategoryId, () => { addForm.subcategory_id = ''; });

const addForm = useForm({
    subcategory_id: '',
    name: '',
    description: '',
    retail_price: '',
    wholesale_price: '',
    badge: '',
    cover_image: null,
    sizes: [],
});

function submitAdd() {
    addForm.post(route('admin.products.store'), {
        forceFormData: true,
        onSuccess: () => { showAdd.value = false; addForm.reset(); addCategoryId.value = ''; },
    });
}

function deleteProduct(p) {
    if (!confirm(`حذف "${p.name}"؟`)) return;
    router.delete(route('admin.products.destroy', p.id));
}

function toggleStatus(p) {
    router.patch(route('admin.products.update', p.id), { is_active: !p.is_active }, { preserveScroll: true });
}
</script>

<template>
    <Head title="المنتجات" />
    <AdminLayout title="المنتجات" subtitle="إدارة المنتجات وقوالبها">

        <!-- Toolbar -->
        <div class="toolbar">
            <input v-model="search" class="search-inp" placeholder="🔍  بحث في المنتجات…">

            <!-- Category filter as pills — avoids native dropdown overlap bug -->
            <div class="cat-pills">
                <button
                    class="pill" :class="{ active: !filterCatId }"
                    @click="filterCatId = ''"
                >الكل</button>
                <button
                    v-for="c in categories" :key="c.id"
                    class="pill" :class="{ active: filterCatId === c.id }"
                    @click="filterCatId = filterCatId === c.id ? '' : c.id"
                >{{ c.name }}</button>
            </div>

            <button class="add-btn" @click="showAdd = true">+ منتج جديد</button>
        </div>

        <!-- Stats row -->
        <div class="stats-row">
            <div class="stat">
                <div class="stat-n">{{ products.length }}</div>
                <div class="stat-l">إجمالي المنتجات</div>
            </div>
            <div class="stat">
                <div class="stat-n">{{ products.filter(p => p.is_active).length }}</div>
                <div class="stat-l">مفعّلة</div>
            </div>
            <div class="stat">
                <div class="stat-n">{{ filtered.length }}</div>
                <div class="stat-l">نتائج الفلتر</div>
            </div>
        </div>

        <!-- Table -->
        <div class="tbl-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>التصنيف</th>
                        <th>سعر التجزئة</th>
                        <th>سعر الجملة</th>
                        <th>القوالب</th>
                        <th>الحالة</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="p in filtered" :key="p.id">
                        <td>
                            <div class="prod-row">
                                <div v-if="p.cover_image" class="prod-thumb">
                                    <img :src="'/storage/' + p.cover_image" :alt="p.name">
                                </div>
                                <div v-else class="prod-thumb no-img">🖼</div>
                                <div>
                                    <div class="prod-name">{{ p.name }}</div>
                                    <div v-if="p.badge" class="badge">{{ p.badge }}</div>
                                    <div v-if="p.sizes?.length" class="sizes-tags">
                                        <span v-for="s in p.sizes.slice(0,3)" :key="s" class="size-tag">{{ s }}</span>
                                        <span v-if="p.sizes.length > 3" class="size-tag muted">+{{ p.sizes.length - 3 }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="sub-label">{{ p.category }}</div>
                            <div class="cat-label">{{ p.subcategory }}</div>
                        </td>
                        <td class="price">{{ p.retail_price ? p.retail_price + ' ₪' : '—' }}</td>
                        <td class="price muted">{{ p.wholesale_price ? p.wholesale_price + ' ₪' : '—' }}</td>
                        <td>
                            <span class="tmpl-cnt" :class="{ full: p.templates_count >= 3 }">
                                {{ p.templates_count }}/3
                            </span>
                        </td>
                        <td>
                            <button class="status" :class="p.is_active ? 'on' : 'off'" @click="toggleStatus(p)">
                                {{ p.is_active ? 'مفعّل' : 'معطّل' }}
                            </button>
                        </td>
                        <td>
                            <div class="actions">
                                <Link :href="route('admin.products.edit', p.id)" class="act-btn edit">تعديل</Link>
                                <button class="act-btn danger" @click="deleteProduct(p)">حذف</button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!filtered.length">
                        <td colspan="7" class="empty">لا توجد منتجات.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Add modal -->
        <div v-if="showAdd" class="modal-bg" @click.self="showAdd = false">
            <div class="modal">
                <h3 class="modal-title">إضافة منتج جديد</h3>
                <form @submit.prevent="submitAdd" class="modal-form">
                    <label class="lbl">التصنيف الرئيسي
                        <select v-model="addCategoryId" class="inp" required>
                            <option value="">اختر التصنيف…</option>
                            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </label>

                    <label class="lbl">التصنيف الفرعي
                        <select v-model="addForm.subcategory_id" class="inp" :disabled="!addCategoryId" required>
                            <option value="">{{ addCategoryId ? 'اختر التصنيف الفرعي…' : 'اختر التصنيف الرئيسي أولاً' }}</option>
                            <option v-for="s in filteredAddSubs" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </label>
                    <p v-if="addForm.errors.subcategory_id" class="ferr">{{ addForm.errors.subcategory_id }}</p>

                    <label class="lbl">اسم المنتج
                        <input v-model="addForm.name" class="inp" required placeholder="درع خشبي كلاسيكي">
                    </label>
                    <p v-if="addForm.errors.name" class="ferr">{{ addForm.errors.name }}</p>

                    <div class="two-col">
                        <label class="lbl">سعر التجزئة (₪)
                            <input v-model="addForm.retail_price" class="inp" type="number" min="0" step="0.01" required>
                        </label>
                        <label class="lbl">سعر الجملة (₪)
                            <input v-model="addForm.wholesale_price" class="inp" type="number" min="0" step="0.01" required>
                        </label>
                    </div>

                    <label class="lbl">شارة (اختياري)
                        <input v-model="addForm.badge" class="inp" placeholder="الأكثر طلباً">
                    </label>

                    <label class="lbl">الوصف (اختياري)
                        <textarea v-model="addForm.description" class="inp" rows="3"></textarea>
                    </label>

                    <div class="modal-actions">
                        <button type="button" class="cancel-btn" @click="showAdd = false">إلغاء</button>
                        <button type="submit" class="submit-btn" :disabled="addForm.processing">حفظ</button>
                    </div>
                </form>
            </div>
        </div>

    </AdminLayout>
</template>

<style scoped>
.toolbar { display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; align-items: flex-start; }
.search-inp { flex: 1; min-width: 200px; background: var(--bg2); border: 1px solid var(--hair); border-radius: 12px; color: var(--ink); font-size: 14px; padding: 10px 14px; font-family: inherit; }
.search-inp:focus { outline: none; border-color: var(--emerald); }

/* Category pills — replaces native <select> to avoid dropdown overlap */
.cat-pills { display: flex; gap: 6px; flex-wrap: wrap; }
.pill { background: var(--bg2); border: 1px solid var(--hair); border-radius: 999px; padding: 7px 14px; font-size: 12px; font-weight: 500; cursor: pointer; color: var(--muted); font-family: inherit; transition: all .2s; white-space: nowrap; }
.pill:hover { border-color: rgba(52,215,127,.4); color: var(--ink); }
.pill.active { background: rgba(52,215,127,.12); border-color: rgba(52,215,127,.4); color: var(--emerald-soft); font-weight: 700; }

.add-btn { background: linear-gradient(150deg, var(--emerald-soft), var(--emerald-deep)); color: var(--on-emerald); border: none; border-radius: 12px; padding: 10px 20px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: inherit; white-space: nowrap; }

/* Stats */
.stats-row { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
.stat { background: var(--bg2); border: 1px solid var(--hair); border-radius: 14px; padding: 12px 20px; text-align: center; min-width: 100px; }
.stat-n { font-size: 22px; font-weight: 700; color: var(--emerald-soft); }
.stat-l { font-size: 11px; color: var(--muted); margin-top: 2px; }

/* Table */
.tbl-wrap { background: var(--bg2); border: 1px solid var(--hair); border-radius: 20px; overflow: hidden; }
.tbl { width: 100%; border-collapse: collapse; font-size: 14px; }
.tbl th { background: var(--bg); padding: 12px 16px; text-align: right; font-size: 12px; color: var(--muted); font-weight: 600; letter-spacing: .05em; border-bottom: 1px solid var(--hair); }
.tbl td { padding: 12px 16px; border-bottom: 1px solid var(--hair); vertical-align: middle; }
.tbl tr:last-child td { border-bottom: none; }
.tbl tr:hover td { background: var(--glass); }

.prod-row { display: flex; align-items: center; gap: 10px; }
.prod-thumb { width: 44px; height: 44px; border-radius: 10px; overflow: hidden; background: var(--glass); flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 20px; }
.prod-thumb img { width: 100%; height: 100%; object-fit: cover; }
.prod-thumb.no-img { border: 1px solid var(--hair); opacity: .5; }
.prod-name { font-weight: 600; }
.badge { display: inline-block; margin-top: 3px; font-size: 10px; background: rgba(52,215,127,.1); color: var(--emerald-soft); border: 1px solid rgba(52,215,127,.25); border-radius: 999px; padding: 2px 8px; }
.sizes-tags { display: flex; gap: 4px; margin-top: 4px; flex-wrap: wrap; }
.size-tag { font-size: 10px; background: var(--glass); border: 1px solid var(--hair); border-radius: 6px; padding: 1px 6px; color: var(--muted); }
.size-tag.muted { opacity: .6; }
.sub-label { font-weight: 500; }
.cat-label { font-size: 12px; color: var(--muted); margin-top: 2px; }
.price { font-weight: 600; font-variant-numeric: tabular-nums; }
.muted { color: var(--muted) !important; }
.tmpl-cnt { background: var(--glass); border: 1px solid var(--hair); border-radius: 999px; padding: 3px 10px; font-size: 12px; font-weight: 600; }
.tmpl-cnt.full { background: rgba(52,215,127,.1); border-color: rgba(52,215,127,.3); color: var(--emerald-soft); }

/* Status as clickable toggle button */
.status { border-radius: 999px; padding: 4px 12px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; font-family: inherit; transition: all .2s; }
.status.on { background: rgba(52,215,127,.1); color: var(--emerald-soft); border: 1px solid rgba(52,215,127,.25); }
.status.off { background: rgba(231,76,60,.08); color: #ff7a6b; border: 1px solid rgba(231,76,60,.2); }

.actions { display: inline-flex; gap: 6px; align-items: center; white-space: nowrap; }
.act-btn { background: var(--glass); border: 1px solid var(--hair); border-radius: 8px; padding: 5px 13px; font-size: 12px; cursor: pointer; color: var(--ink); text-decoration: none; font-family: inherit; transition: all .18s; line-height: 1.4; }
.act-btn.edit:hover  { background: rgba(52,215,127,.1); border-color: rgba(52,215,127,.3); color: var(--emerald-soft); }
.act-btn.danger:hover { background: rgba(231,76,60,.1); border-color: rgba(231,76,60,.3); color: #ff7a6b; }
.empty { text-align: center; color: var(--muted); padding: 40px; }

/* Modal */
.modal-bg { position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 100; display: flex; align-items: center; justify-content: center; padding: 20px; }
.modal { background: var(--bg2); border: 1px solid var(--hair); border-radius: 20px; padding: 28px; width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto; }
.modal-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; }
.modal-form { display: flex; flex-direction: column; gap: 14px; }
.lbl { font-size: 13px; color: var(--muted); display: flex; flex-direction: column; gap: 6px; }
.inp { background: var(--bg); border: 1.5px solid var(--hair); border-radius: 12px; color: var(--ink); font-size: 14px; padding: 10px 14px; font-family: inherit; width: 100%; box-sizing: border-box; }
.inp:focus { outline: none; border-color: var(--emerald); }
.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.ferr { font-size: 12px; color: #ff7a6b; margin-top: -8px; }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 8px; }
.cancel-btn { background: var(--glass); border: 1px solid var(--hair); border-radius: 12px; padding: 10px 20px; font-size: 14px; cursor: pointer; color: var(--ink); font-family: inherit; }
.submit-btn { background: linear-gradient(150deg, var(--emerald-soft), var(--emerald-deep)); color: var(--on-emerald); border: none; border-radius: 12px; padding: 10px 24px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: inherit; }
.submit-btn:disabled { opacity: .5; }
</style>
