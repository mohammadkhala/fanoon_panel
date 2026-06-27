<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    designs:      Array,
    categories:   Array,
    subcategories: Array,
    products:     Array,
});

/* ── Search ── */
const search = ref('');
const filtered = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return props.designs;
    return props.designs.filter(d =>
        d.name.toLowerCase().includes(q) ||
        d.user_name.toLowerCase().includes(q) ||
        d.user_email.toLowerCase().includes(q) ||
        (d.product_name || '').toLowerCase().includes(q)
    );
});

/* ── Promote modal ── */
const promoting = ref(null);   // the design being promoted
const promoCatId = ref('');
const promoSubId = ref('');

const filteredSubcategories = computed(() =>
    promoCatId.value
        ? props.subcategories.filter(s => s.category_id === promoCatId.value)
        : []
);
const filteredProducts = computed(() =>
    promoSubId.value
        ? props.products.filter(p => p.subcategory_id === promoSubId.value && p.templates_count < 3)
        : []
);

watch(promoCatId, () => { promoSubId.value = ''; promoForm.product_id = ''; });
watch(promoSubId, () => { promoForm.product_id = ''; });

const promoForm = useForm({ product_id: '', name: '' });

function openPromote(design) {
    promoting.value = design;
    promoCatId.value = '';
    promoSubId.value = '';
    promoForm.reset();
}
function closePromote() { promoting.value = null; }

function submitPromote() {
    if (!promoting.value) return;
    promoForm.post(route('admin.designs.promote', promoting.value.id), {
        onSuccess: () => { promoting.value = null; promoForm.reset(); },
    });
}

/* ── Image preview ── */
const previewing = ref(null);
</script>

<template>
    <Head title="تصاميم الزبائن" />
    <AdminLayout title="تصاميم الزبائن" subtitle="استعرض تصاميم الزبائن وأضف المميزة منها كقوالب في المتجر">

        <!-- Toolbar -->
        <div class="toolbar">
            <input v-model="search" class="search-inp" placeholder="🔍  بحث باسم الزبون أو المنتج…">
            <div class="stat-chip">
                <span class="stat-n">{{ designs.length }}</span>
                <span class="stat-l">تصميم</span>
            </div>
        </div>

        <!-- Empty state -->
        <div v-if="!filtered.length" class="empty">
            <div class="e-ico">🎨</div>
            <div class="e-msg">{{ search ? 'لا توجد نتائج للبحث' : 'لا توجد تصاميم حتى الآن' }}</div>
        </div>

        <!-- Design grid -->
        <div v-else class="dgrid">
            <div v-for="d in filtered" :key="d.id" class="dcard">

                <!-- Preview image -->
                <div class="dimg" @click="previewing = d">
                    <img
                        v-if="d.preview_path"
                        :src="'/storage/' + d.preview_path"
                        :alt="d.name"
                        class="dimg-img"
                    >
                    <div v-else class="dimg-empty">🖼</div>
                    <div class="dimg-hover">🔍 عرض</div>
                </div>

                <!-- Info -->
                <div class="dinfo">
                    <div class="dname">{{ d.name }}</div>
                    <div class="dmeta">
                        <span class="duser">👤 {{ d.user_name }}</span>
                    </div>
                    <div class="dmeta">
                        <span class="ddate">📅 {{ d.created_at }}</span>
                    </div>
                    <div v-if="d.product_name" class="dprod">
                        <span class="dprod-cat">{{ d.category_name }}</span>
                        <span class="dprod-arrow">›</span>
                        <span class="dprod-name">{{ d.product_name }}</span>
                    </div>
                    <div v-else class="dprod-none">غير مرتبط بمنتج</div>
                </div>

                <!-- Action -->
                <div class="dfoot">
                    <button class="promote-btn" @click="openPromote(d)">
                        ⭐ ترقية كقالب
                    </button>
                </div>

            </div>
        </div>

    </AdminLayout>

    <!-- ── Image preview lightbox ── -->
    <div v-if="previewing" class="lightbox" @click.self="previewing = null">
        <div class="lb-box">
            <button class="lb-close" @click="previewing = null">✕</button>
            <img
                v-if="previewing.preview_path"
                :src="'/storage/' + previewing.preview_path"
                class="lb-img"
            >
            <div v-else class="lb-empty">لا توجد صورة معاينة</div>
            <div class="lb-meta">
                <strong>{{ previewing.name }}</strong> — {{ previewing.user_name }}
            </div>
        </div>
    </div>

    <!-- ── Promote modal ── -->
    <div v-if="promoting" class="modal-bg" @click.self="closePromote">
        <div class="modal">
            <h3 class="modal-title">⭐ ترقية التصميم كقالب</h3>
            <p class="modal-sub">سيُضاف هذا التصميم كقالب افتراضي يظهر للزبائن في صفحة المنتج.</p>

            <!-- Design thumbnail -->
            <div class="modal-preview">
                <img
                    v-if="promoting.preview_path"
                    :src="'/storage/' + promoting.preview_path"
                    class="modal-thumb"
                >
                <div v-else class="modal-thumb-empty">🖼</div>
                <div class="modal-pname">{{ promoting.name }} — {{ promoting.user_name }}</div>
            </div>

            <form @submit.prevent="submitPromote" class="mform">

                <!-- Category -->
                <label class="lbl">التصنيف الرئيسي
                    <select v-model="promoCatId" class="inp" required>
                        <option value="">اختر التصنيف…</option>
                        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </label>

                <!-- Subcategory -->
                <label class="lbl">التصنيف الفرعي
                    <select v-model="promoSubId" class="inp" :disabled="!promoCatId" required>
                        <option value="">{{ promoCatId ? 'اختر التصنيف الفرعي…' : 'اختر التصنيف الرئيسي أولاً' }}</option>
                        <option v-for="s in filteredSubcategories" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </label>

                <!-- Product -->
                <label class="lbl">
                    المنتج
                    <select v-model="promoForm.product_id" class="inp" :disabled="!promoSubId" required>
                        <option value="">{{ promoSubId ? 'اختر المنتج…' : 'اختر التصنيف الفرعي أولاً' }}</option>
                        <option v-for="p in filteredProducts" :key="p.id" :value="p.id">
                            {{ p.name }} ({{ p.templates_count }}/3 قوالب)
                        </option>
                    </select>
                    <span v-if="promoSubId && !filteredProducts.length" class="hint-warn">
                        ⚠️ جميع منتجات هذا التصنيف وصلت للحد الأقصى (3 قوالب)
                    </span>
                    <span v-if="promoForm.errors.product_id" class="ferr">{{ promoForm.errors.product_id }}</span>
                </label>

                <!-- Template name -->
                <label class="lbl">اسم القالب (اختياري)
                    <input v-model="promoForm.name" class="inp" placeholder="مثال: تصميم احترافي ذهبي">
                    <span class="hint">اتركه فارغاً لاستخدام الاسم التلقائي</span>
                </label>

                <div class="mactions">
                    <button type="button" class="cancel-btn" @click="closePromote">إلغاء</button>
                    <button
                        type="submit"
                        class="submit-btn"
                        :disabled="promoForm.processing || !promoForm.product_id"
                    >
                        {{ promoForm.processing ? 'جارٍ الإضافة…' : '⭐ إضافة كقالب' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
/* Toolbar */
.toolbar {
    display: flex; align-items: center; gap: 12px;
    margin-bottom: 20px; flex-wrap: wrap;
}
.search-inp {
    flex: 1; min-width: 220px;
    background: var(--bg2); border: 1px solid var(--hair);
    border-radius: 12px; color: var(--ink);
    font-size: 14px; padding: 10px 14px; font-family: inherit;
}
.search-inp:focus { outline: none; border-color: var(--emerald); }
.stat-chip {
    background: var(--bg2); border: 1px solid var(--hair);
    border-radius: 12px; padding: 8px 16px;
    display: flex; align-items: baseline; gap: 6px;
}
.stat-n { font-size: 20px; font-weight: 700; color: var(--emerald-soft); }
.stat-l { font-size: 12px; color: var(--muted); }

/* Empty */
.empty { text-align: center; padding: 60px 20px; color: var(--muted); }
.e-ico  { font-size: 48px; margin-bottom: 12px; }
.e-msg  { font-size: 15px; }

/* Design grid */
.dgrid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 16px;
}

.dcard {
    background: var(--bg2);
    border: 1.5px solid var(--hair);
    border-radius: 18px;
    overflow: hidden;
    display: flex; flex-direction: column;
    transition: border-color .2s, transform .2s;
}
.dcard:hover { border-color: var(--emerald-soft); transform: translateY(-2px); }

/* Image area */
.dimg {
    position: relative;
    aspect-ratio: 1;
    background: var(--glass);
    overflow: hidden; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
}
.dimg-img   { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .3s; }
.dcard:hover .dimg-img { transform: scale(1.04); }
.dimg-empty { font-size: 40px; color: var(--muted); opacity: .4; }
.dimg-hover {
    position: absolute; inset: 0;
    background: rgba(0,0,0,.45);
    color: #fff; font-size: 14px; font-weight: 600;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity .2s;
}
.dimg:hover .dimg-hover { opacity: 1; }

/* Info */
.dinfo { padding: 14px 14px 8px; flex: 1; }
.dname  { font-size: 14px; font-weight: 700; margin-bottom: 7px; }
.dmeta  { display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
.duser  { font-size: 12px; color: var(--muted); }
.ddate  { font-size: 11px; color: var(--muted); }
.dprod  { display: flex; align-items: center; gap: 4px; margin-top: 8px; flex-wrap: wrap; }
.dprod-cat   { font-size: 11px; background: var(--glass); border: 1px solid var(--hair); border-radius: 6px; padding: 2px 7px; color: var(--muted); }
.dprod-arrow { color: var(--muted); font-size: 12px; }
.dprod-name  { font-size: 11px; font-weight: 600; color: var(--emerald-soft); }
.dprod-none  { font-size: 11px; color: var(--muted); margin-top: 8px; font-style: italic; }

/* Footer */
.dfoot { padding: 10px 14px 14px; }
.promote-btn {
    width: 100%;
    background: rgba(52,215,127,.1);
    border: 1.5px solid rgba(52,215,127,.25);
    border-radius: 10px;
    color: var(--emerald-soft);
    font-size: 13px; font-weight: 600;
    padding: 9px;
    cursor: pointer; font-family: inherit;
    transition: all .2s;
}
.promote-btn:hover { background: rgba(52,215,127,.2); }

/* ── Lightbox ── */
.lightbox {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.8);
    z-index: 600;
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
}
.lb-box {
    position: relative;
    background: var(--bg2);
    border-radius: 20px;
    overflow: hidden;
    max-width: 600px; max-height: 80vh;
    display: flex; flex-direction: column;
}
.lb-close {
    position: absolute; top: 12px; left: 12px;
    background: rgba(0,0,0,.5); border: none;
    color: #fff; width: 32px; height: 32px;
    border-radius: 50%; cursor: pointer; font-size: 14px;
    display: flex; align-items: center; justify-content: center;
    z-index: 1;
}
.lb-img   { width: 100%; max-height: 70vh; object-fit: contain; display: block; }
.lb-empty { padding: 60px; text-align: center; font-size: 40px; color: var(--muted); }
.lb-meta  { padding: 14px 18px; font-size: 13px; color: var(--muted); }

/* ── Promote modal ── */
.modal-bg {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.55); z-index: 500;
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
}
.modal {
    background: var(--bg2);
    border: 1px solid var(--hair);
    border-radius: 22px; padding: 28px;
    width: 100%; max-width: 480px;
    max-height: 90vh; overflow-y: auto;
}
.modal-title { font-size: 18px; font-weight: 700; margin-bottom: 6px; }
.modal-sub   { font-size: 13px; color: var(--muted); margin-bottom: 18px; }

.modal-preview {
    display: flex; align-items: center; gap: 14px;
    background: var(--glass);
    border: 1px solid var(--hair);
    border-radius: 14px; padding: 12px;
    margin-bottom: 20px;
}
.modal-thumb {
    width: 64px; height: 64px; border-radius: 10px;
    object-fit: cover; flex-shrink: 0;
}
.modal-thumb-empty {
    width: 64px; height: 64px; border-radius: 10px;
    background: var(--bg); display: flex;
    align-items: center; justify-content: center;
    font-size: 24px; color: var(--muted);
}
.modal-pname { font-size: 13px; font-weight: 600; color: var(--ink); }

.mform { display: flex; flex-direction: column; gap: 14px; }
.lbl {
    display: flex; flex-direction: column; gap: 6px;
    font-size: 13px; color: var(--muted); font-weight: 500;
}
.inp {
    background: var(--bg); border: 1.5px solid var(--hair);
    border-radius: 12px; color: var(--ink);
    font-size: 14px; padding: 10px 14px;
    font-family: inherit; width: 100%; box-sizing: border-box;
}
.inp:focus { outline: none; border-color: var(--emerald); }
.inp:disabled { opacity: .5; cursor: not-allowed; }
.hint       { font-size: 11px; color: var(--muted); }
.hint-warn  { font-size: 12px; color: #ff7a6b; }
.ferr       { font-size: 12px; color: #ff7a6b; }

.mactions {
    display: flex; gap: 10px; justify-content: flex-end;
    margin-top: 6px;
}
.cancel-btn {
    background: var(--glass); border: 1px solid var(--hair);
    border-radius: 12px; padding: 10px 20px;
    font-size: 14px; cursor: pointer;
    color: var(--ink); font-family: inherit;
}
.submit-btn {
    background: linear-gradient(150deg, var(--emerald-soft), var(--emerald-deep));
    color: var(--on-emerald); border: none;
    border-radius: 12px; padding: 10px 24px;
    font-size: 14px; font-weight: 600;
    cursor: pointer; font-family: inherit;
    transition: opacity .2s;
}
.submit-btn:disabled { opacity: .5; cursor: not-allowed; }
.submit-btn:hover:not(:disabled) { opacity: .88; }
</style>
