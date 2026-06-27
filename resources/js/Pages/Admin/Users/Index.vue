<script setup>
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed, ref } from 'vue';

const props = defineProps({ admins: Array, roles: Array });
const me = computed(() => usePage().props.auth?.user);
const isSuperAdmin = computed(() => me.value?.is_super_admin);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    admin_role: 'orders_manager',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('admin.users.store'), { preserveScroll: true, onSuccess: () => form.reset() });
}

function destroy(u) {
    if (confirm('حذف المدير "' + u.name + '"؟')) {
        router.delete(route('admin.users.destroy', u.id), { preserveScroll: true });
    }
}

// Inline role change
const editingRole = ref(null); // user id being edited
const roleForm = useForm({ admin_role: '' });

function startRoleEdit(u) {
    editingRole.value = u.id;
    roleForm.admin_role = u.admin_role;
}

function saveRole(u) {
    roleForm.patch(route('admin.users.role', u.id), {
        preserveScroll: true,
        onSuccess: () => { editingRole.value = null; },
    });
}

const roleBadgeClass = (role) => ({
    super_admin:        'badge-super',
    orders_manager:     'badge-orders',
    products_manager:   'badge-products',
    general_supervisor: 'badge-general',
})[role] ?? 'badge-super';
</script>

<template>
    <Head title="المستخدمون — الإدارة" />
    <AdminLayout title="المستخدمون" subtitle="إدارة حسابات المدراء وصلاحيات الدخول">
        <div class="cols">

            <!-- ── Add admin form (super_admin only) ── -->
            <form v-if="isSuperAdmin" @submit.prevent="submit" class="panel">
                <h3>إضافة مدير جديد</h3>

                <div class="fgrp">
                    <label>الاسم</label>
                    <input v-model="form.name" required placeholder="محمد أحمد">
                    <div v-if="form.errors.name" class="err">{{ form.errors.name }}</div>
                </div>

                <div class="fgrp">
                    <label>البريد الإلكتروني</label>
                    <input type="email" v-model="form.email" class="lat" required placeholder="admin@example.com">
                    <div v-if="form.errors.email" class="err">{{ form.errors.email }}</div>
                </div>

                <div class="fgrp">
                    <label>الهاتف (اختياري)</label>
                    <input v-model="form.phone" class="lat" placeholder="+970 5xx xxx xxx">
                </div>

                <div class="fgrp">
                    <label>الصلاحية</label>
                    <select v-model="form.admin_role" class="sel">
                        <option v-for="r in roles" :key="r.value" :value="r.value">{{ r.label }}</option>
                    </select>
                    <div v-if="form.errors.admin_role" class="err">{{ form.errors.admin_role }}</div>
                </div>

                <div class="fgrp">
                    <label>كلمة المرور</label>
                    <input type="password" v-model="form.password" required>
                    <div v-if="form.errors.password" class="err">{{ form.errors.password }}</div>
                </div>

                <div class="fgrp">
                    <label>تأكيد كلمة المرور</label>
                    <input type="password" v-model="form.password_confirmation" required>
                </div>

                <button class="submit" :disabled="form.processing">إنشاء الحساب</button>
            </form>

            <!-- ── Permissions legend ── -->
            <div v-else class="panel legend-panel">
                <h3>الصلاحيات المتاحة</h3>
                <div v-for="r in roles" :key="r.value" class="legend-row">
                    <span :class="['badge', roleBadgeClass(r.value)]">{{ r.label }}</span>
                    <span class="legend-desc">{{ roleDesc(r.value) }}</span>
                </div>
            </div>

            <!-- ── Admins table ── -->
            <div class="panel">
                <div class="table">
                    <div class="thead">
                        <span>المدير</span>
                        <span>الصلاحية</span>
                        <span>الهاتف</span>
                        <span>التسجيل</span>
                        <span></span>
                    </div>

                    <div v-for="u in admins" :key="u.id" class="row">
                        <!-- Who -->
                        <div class="who">
                            <div class="av">{{ (u.name || '?').charAt(0) }}</div>
                            <div>
                                <div class="nm">{{ u.name }}</div>
                                <div class="sub lat">{{ u.email }}</div>
                            </div>
                        </div>

                        <!-- Role badge / inline editor -->
                        <div class="role-cell">
                            <template v-if="isSuperAdmin && u.id !== me?.id && editingRole !== u.id">
                                <span :class="['badge', roleBadgeClass(u.admin_role)]">{{ u.role_label }}</span>
                                <button class="edit-role-btn" @click="startRoleEdit(u)" title="تغيير الصلاحية">✏️</button>
                            </template>
                            <template v-else-if="isSuperAdmin && editingRole === u.id">
                                <select v-model="roleForm.admin_role" class="sel-inline">
                                    <option v-for="r in roles" :key="r.value" :value="r.value">{{ r.label }}</option>
                                </select>
                                <button class="save-btn" @click="saveRole(u)" :disabled="roleForm.processing">حفظ</button>
                                <button class="cancel-btn" @click="editingRole = null">✕</button>
                            </template>
                            <template v-else>
                                <span :class="['badge', roleBadgeClass(u.admin_role)]">{{ u.role_label }}</span>
                            </template>
                        </div>

                        <span class="muted lat">{{ u.phone || '—' }}</span>
                        <span class="muted lat">{{ u.created_at }}</span>

                        <div class="rowacts">
                            <span v-if="u.id === me?.id" class="self">أنت</span>
                            <button v-else-if="isSuperAdmin" class="q bad" @click="destroy(u)">حذف</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions reference table -->
        <div class="panel ref-panel">
            <h3>مرجع الصلاحيات</h3>
            <div class="ref-grid">
                <div class="ref-head">الصلاحية</div>
                <div class="ref-head">الطلبات</div>
                <div class="ref-head">المنتجات والقوالب</div>
                <div class="ref-head">التقارير</div>
                <div class="ref-head">الإعدادات</div>
                <div class="ref-head">إدارة المدراء</div>

                <div><span class="badge badge-super">سوبر أدمن</span></div>
                <div class="green">✓ كامل</div><div class="green">✓ كامل</div>
                <div class="green">✓ كامل</div><div class="green">✓ كامل</div><div class="green">✓ كامل</div>

                <div><span class="badge badge-orders">مدير طلبات</span></div>
                <div class="green">✓ كامل</div><div class="red">✗</div>
                <div class="red">✗</div><div class="red">✗</div><div class="red">✗</div>

                <div><span class="badge badge-products">مدير منتجات</span></div>
                <div class="red">✗</div><div class="green">✓ كامل</div>
                <div class="red">✗</div><div class="red">✗</div><div class="red">✗</div>

                <div><span class="badge badge-general">مشرف عام</span></div>
                <div class="yellow">👁 عرض</div><div class="yellow">👁 عرض</div>
                <div class="green">✓ كامل</div><div class="red">✗</div><div class="red">✗</div>
            </div>
        </div>
    </AdminLayout>
</template>

<script>
function roleDesc(role) {
    const map = {
        super_admin:        'صلاحية كاملة على كل شيء بما فيها إدارة المدراء',
        orders_manager:     'الطلبات والعملاء ومناطق التوصيل فقط',
        products_manager:   'المنتجات والقوالب والتصنيفات والتصاميم فقط',
        general_supervisor: 'عرض الكل + التقارير والتقييمات والشركات — بدون حذف أو إعدادات',
    };
    return map[role] ?? '';
}
</script>

<style scoped>
.cols { display: grid; grid-template-columns: 340px 1fr; gap: 20px; align-items: start; }
.panel { background: var(--bg2); border: 1px solid var(--hair); border-radius: 20px; padding: 22px; }
h3 { font-size: 15px; font-weight: 700; margin-bottom: 16px; }

/* Form */
.fgrp { margin-bottom: 13px; }
.fgrp label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; }
.fgrp input, .sel { width: 100%; background: var(--glass); border: 1px solid var(--hair); border-radius: 11px; padding: 10px 12px; color: var(--ink); font-family: inherit; font-size: 14px; outline: none; }
.fgrp input:focus, .sel:focus { border-color: var(--emerald); }
.sel { cursor: pointer; }
.lat { direction: ltr; text-align: left; }
.submit { background: linear-gradient(150deg, var(--emerald-soft), var(--emerald-deep)); color: var(--on-emerald); border: none; border-radius: 12px; padding: 11px 22px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: inherit; margin-top: 6px; width: 100%; }
.err { color: #ff7a6b; font-size: 12px; margin-top: 5px; }

/* Legend panel */
.legend-panel { }
.legend-row { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 12px; }
.legend-desc { font-size: 12px; color: var(--muted); line-height: 1.5; }

/* Table */
.table { display: flex; flex-direction: column; }
.thead, .row { display: grid; grid-template-columns: 2.2fr 1.6fr 1fr 1fr auto; align-items: center; gap: 12px; }
.thead { padding: 8px 12px 12px; color: var(--muted); font-size: 12px; border-bottom: 1px solid var(--hair); }
.row { padding: 12px; border-radius: 11px; }
.row:hover { background: var(--glass); }
.who { display: flex; align-items: center; gap: 11px; }
.av { width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(150deg, var(--emerald-soft), var(--emerald-deep)); color: var(--on-emerald); display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; }
.nm { font-weight: 600; font-size: 14px; }
.sub { color: var(--muted); font-size: 12px; margin-top: 2px; }
.muted { color: var(--muted); font-size: 13px; }
.rowacts { justify-self: end; }
.self { font-size: 12px; color: var(--emerald-soft); font-weight: 600; }
.q { border: none; border-radius: 8px; padding: 6px 12px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: inherit; }
.bad { background: rgba(231,76,60,.1); color: #ff7a6b; }

/* Role cell */
.role-cell { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.edit-role-btn { background: none; border: none; cursor: pointer; font-size: 13px; opacity: .5; transition: opacity .2s; }
.edit-role-btn:hover { opacity: 1; }
.sel-inline { background: var(--glass); border: 1px solid var(--hair); border-radius: 8px; padding: 4px 8px; color: var(--ink); font-family: inherit; font-size: 12px; outline: none; }
.save-btn { background: var(--emerald); color: var(--on-emerald); border: none; border-radius: 7px; padding: 4px 10px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: inherit; }
.cancel-btn { background: none; border: none; color: var(--muted); cursor: pointer; font-size: 13px; }

/* Badges */
.badge { display: inline-block; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 99px; white-space: nowrap; }
.badge-super    { background: rgba(139,92,246,.15); color: #8b5cf6; border: 1px solid rgba(139,92,246,.3); }
.badge-orders   { background: rgba(59,130,246,.15); color: #3b82f6; border: 1px solid rgba(59,130,246,.3); }
.badge-products { background: rgba(16,185,129,.15); color: var(--emerald-soft); border: 1px solid rgba(16,185,129,.3); }
.badge-general  { background: rgba(245,158,11,.15); color: #f59e0b; border: 1px solid rgba(245,158,11,.3); }

/* Reference table */
.ref-panel { margin-top: 20px; }
.ref-grid { display: grid; grid-template-columns: 1.4fr repeat(5, 1fr); gap: 0; font-size: 13px; }
.ref-head { font-size: 11px; font-weight: 700; color: var(--muted); padding: 8px 10px; border-bottom: 1px solid var(--hair); }
.ref-grid > div:not(.ref-head) { padding: 10px 10px; border-bottom: 1px solid var(--hair); display: flex; align-items: center; }
.green { color: #10b981; font-weight: 600; }
.red   { color: var(--muted); }
.yellow { color: #f59e0b; font-weight: 600; }

@media (max-width: 1000px) {
    .cols { grid-template-columns: 1fr; }
    .thead { display: none; }
    .row { grid-template-columns: 1fr auto; row-gap: 6px; }
    .ref-grid { grid-template-columns: 1fr 1fr; }
}
</style>
