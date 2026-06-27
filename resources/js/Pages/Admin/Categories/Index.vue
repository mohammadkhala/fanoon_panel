<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({ categories: Array });

// ── Category form ──
const catForm = useForm({ name: '', icon: '📦', description: '' });
const editingCat = ref(null);
const catEditForm = useForm({ name: '', icon: '', description: '', is_active: true });

function startEditCat(cat) {
    editingCat.value = cat.id;
    catEditForm.name = cat.name;
    catEditForm.icon = cat.icon;
    catEditForm.description = cat.description || '';
    catEditForm.is_active = cat.is_active !== false;
}
function saveCat(cat) {
    catEditForm.patch(route('admin.categories.update', cat.id), {
        onSuccess: () => { editingCat.value = null; },
    });
}
function deleteCat(cat) {
    if (!confirm(`حذف تصنيف "${cat.name}"؟`)) return;
    router.delete(route('admin.categories.destroy', cat.id));
}
function addCat() {
    catForm.post(route('admin.categories.store'), { onSuccess: () => catForm.reset() });
}

// ── Subcategory form ──
const selectedCat = ref(props.categories[0] ?? null);
const subForm = useForm({ category_id: selectedCat.value?.id ?? '', name: '', icon: '📁', description: '' });
const editingSub = ref(null);
const subEditForm = useForm({ name: '', icon: '', description: '', is_active: true });

function selectCat(cat) {
    selectedCat.value = cat;
    subForm.category_id = cat.id;
}

function startEditSub(sub) {
    editingSub.value = sub.id;
    subEditForm.name = sub.name;
    subEditForm.icon = sub.icon;
    subEditForm.description = sub.description || '';
    subEditForm.is_active = sub.is_active !== false;
}
function saveSub(sub) {
    subEditForm.patch(route('admin.subcategories.update', sub.id), {
        onSuccess: () => { editingSub.value = null; },
    });
}
function deleteSub(sub) {
    if (!confirm(`حذف "${sub.name}"؟`)) return;
    router.delete(route('admin.subcategories.destroy', sub.id));
}
function addSub() {
    subForm.post(route('admin.subcategories.store'), { onSuccess: () => { subForm.name = ''; subForm.icon = '📁'; } });
}

const currentSubs = computed(() => selectedCat.value?.subcategories ?? []);
</script>

<template>
    <Head title="التصنيفات" />
    <AdminLayout title="التصنيفات" subtitle="إدارة التصنيفات الرئيسية والفرعية">
        <div class="cats-wrap">

            <!-- LEFT: Main categories -->
            <div class="cats-panel">
                <div class="panel-head">
                    <h2 class="panel-title">التصنيفات الرئيسية</h2>
                    <span class="cnt-badge">{{ categories.length }}</span>
                </div>

                <!-- Add category form -->
                <form @submit.prevent="addCat" class="add-form">
                    <input v-model="catForm.icon" class="icon-inp" maxlength="4" placeholder="🏆">
                    <input v-model="catForm.name" class="name-inp" placeholder="اسم التصنيف" required>
                    <button type="submit" class="add-btn" :disabled="catForm.processing">+</button>
                </form>
                <p v-if="catForm.errors.name" class="ferr">{{ catForm.errors.name }}</p>

                <div class="cat-list">
                    <div v-for="cat in categories" :key="cat.id"
                         class="cat-row" :class="{ selected: selectedCat?.id === cat.id }"
                         @click="selectCat(cat)">

                        <template v-if="editingCat === cat.id">
                            <input v-model="catEditForm.icon" class="icon-inp sm" maxlength="4" @click.stop>
                            <input v-model="catEditForm.name" class="name-inp sm" @click.stop>
                            <button class="action-btn ok" @click.stop="saveCat(cat)">✓</button>
                            <button class="action-btn" @click.stop="editingCat = null">✕</button>
                        </template>
                        <template v-else>
                            <span class="cat-icon">{{ cat.icon }}</span>
                            <span class="cat-name">{{ cat.name }}</span>
                            <span class="sub-cnt">{{ cat.subcategories_count }}</span>
                            <button class="action-btn" @click.stop="startEditCat(cat)">✎</button>
                            <button class="action-btn danger" @click.stop="deleteCat(cat)">🗑</button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Subcategories of selected -->
            <div class="cats-panel">
                <div class="panel-head">
                    <h2 class="panel-title">
                        <span v-if="selectedCat">{{ selectedCat.icon }} {{ selectedCat.name }}</span>
                        <span v-else class="muted">اختر تصنيفاً</span>
                        — التصنيفات الفرعية
                    </h2>
                    <span class="cnt-badge">{{ currentSubs.length }}</span>
                </div>

                <form v-if="selectedCat" @submit.prevent="addSub" class="add-form">
                    <input v-model="subForm.icon" class="icon-inp" maxlength="4" placeholder="📁">
                    <input v-model="subForm.name" class="name-inp" placeholder="اسم التصنيف الفرعي" required>
                    <button type="submit" class="add-btn" :disabled="subForm.processing">+</button>
                </form>

                <div class="cat-list">
                    <div v-if="!selectedCat" class="empty-hint">← اختر تصنيفاً من القائمة</div>
                    <div v-for="sub in currentSubs" :key="sub.id" class="cat-row sub-row">

                        <template v-if="editingSub === sub.id">
                            <input v-model="subEditForm.icon" class="icon-inp sm" maxlength="4">
                            <input v-model="subEditForm.name" class="name-inp sm">
                            <button class="action-btn ok" @click="saveSub(sub)">✓</button>
                            <button class="action-btn" @click="editingSub = null">✕</button>
                        </template>
                        <template v-else>
                            <span class="cat-icon">{{ sub.icon }}</span>
                            <span class="cat-name">{{ sub.name }}</span>
                            <span class="sub-cnt">{{ sub.products_count }} منتج</span>
                            <button class="action-btn" @click="startEditSub(sub)">✎</button>
                            <button class="action-btn danger" @click="deleteSub(sub)">🗑</button>
                        </template>
                    </div>
                    <div v-if="selectedCat && !currentSubs.length" class="empty-hint">لا توجد تصنيفات فرعية بعد.</div>
                </div>
            </div>

        </div>
    </AdminLayout>
</template>

<style scoped>
.cats-wrap { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
@media (max-width: 860px) { .cats-wrap { grid-template-columns: 1fr; } }

.cats-panel { background: var(--bg2); border: 1px solid var(--hair); border-radius: 20px; padding: 24px; }

.panel-head { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; }
.panel-title { font-size: 16px; font-weight: 700; flex: 1; }
.cnt-badge { background: var(--glass); border: 1px solid var(--hair); border-radius: 999px; padding: 2px 10px; font-size: 12px; color: var(--muted); }

.add-form { display: flex; gap: 8px; margin-bottom: 14px; }
.icon-inp { width: 44px; text-align: center; background: var(--bg); border: 1.5px solid var(--hair); border-radius: 10px; color: var(--ink); font-size: 16px; padding: 8px 4px; }
.icon-inp.sm { width: 36px; font-size: 14px; padding: 6px 2px; }
.name-inp { flex: 1; background: var(--bg); border: 1.5px solid var(--hair); border-radius: 10px; color: var(--ink); font-size: 14px; padding: 8px 12px; font-family: inherit; }
.name-inp.sm { padding: 6px 10px; font-size: 13px; }
.name-inp:focus, .icon-inp:focus { outline: none; border-color: var(--emerald); }
.add-btn { background: var(--emerald-soft); color: var(--on-emerald); border: none; border-radius: 10px; width: 38px; font-size: 20px; cursor: pointer; font-weight: 700; }
.add-btn:disabled { opacity: .5; }

.cat-list { display: flex; flex-direction: column; gap: 6px; }
.cat-row { display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 13px; border: 1px solid transparent; cursor: pointer; transition: all .2s; }
.cat-row:hover { background: var(--glass); }
.cat-row.selected { background: rgba(52,215,127,.08); border-color: rgba(52,215,127,.25); }
.sub-row { cursor: default; padding-right: 10px; }
.cat-icon { font-size: 18px; width: 24px; text-align: center; flex-shrink: 0; }
.cat-name { flex: 1; font-size: 14px; font-weight: 500; }
.sub-cnt { font-size: 12px; color: var(--muted); white-space: nowrap; }
.action-btn { background: var(--glass); border: 1px solid var(--hair); border-radius: 8px; padding: 4px 8px; font-size: 13px; cursor: pointer; color: var(--ink); }
.action-btn.ok { background: rgba(52,215,127,.12); border-color: rgba(52,215,127,.3); color: var(--emerald-soft); }
.action-btn.danger:hover { background: rgba(231,76,60,.1); border-color: rgba(231,76,60,.3); color: #ff7a6b; }
.ferr { font-size: 12px; color: #ff7a6b; margin-top: -8px; margin-bottom: 8px; }
.empty-hint { text-align: center; color: var(--muted); font-size: 13px; padding: 24px; opacity: .6; }
.muted { color: var(--muted); }
</style>
