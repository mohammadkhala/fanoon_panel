<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { ref } from 'vue';

defineProps({ mustVerifyEmail: Boolean, status: String });

const user = usePage().props.auth.user;

/* ── Profile info form ── */
const infoForm = useForm({ name: user.name, email: user.email });
function saveInfo() {
    infoForm.patch(route('profile.update'), { preserveScroll: true });
}

/* ── Password form ── */
const pwForm = useForm({ current_password: '', password: '', password_confirmation: '' });
function savePassword() {
    pwForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => pwForm.reset(),
    });
}

/* ── Delete account ── */
const showDelete = ref(false);
const delForm = useForm({ password: '' });
function deleteAccount() {
    delForm.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => { showDelete.value = false; },
        onError: () => {},
        onFinish: () => delForm.reset(),
    });
}
</script>

<template>
    <Head title="ملفي الشخصي" />
    <CustomerLayout>

        <div class="profile-wrap">

            <!-- ── Card 1: Profile Info ── -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">👤</div>
                    <div>
                        <h2 class="card-title">معلومات الحساب</h2>
                        <p class="card-sub">تحديث اسمك وبريدك الإلكتروني</p>
                    </div>
                </div>

                <form @submit.prevent="saveInfo" class="form">
                    <label class="lbl">
                        الاسم الكامل
                        <input v-model="infoForm.name" class="inp" type="text" required autocomplete="name">
                        <span v-if="infoForm.errors.name" class="ferr">{{ infoForm.errors.name }}</span>
                    </label>

                    <label class="lbl">
                        البريد الإلكتروني
                        <input v-model="infoForm.email" class="inp" type="email" required autocomplete="email">
                        <span v-if="infoForm.errors.email" class="ferr">{{ infoForm.errors.email }}</span>
                    </label>

                    <div v-if="mustVerifyEmail && !user.email_verified_at" class="verify-note">
                        ⚠️ بريدك الإلكتروني غير مُفعَّل بعد.
                    </div>

                    <div class="form-foot">
                        <span v-if="infoForm.recentlySuccessful" class="saved-msg">✓ تم الحفظ</span>
                        <button type="submit" class="save-btn" :disabled="infoForm.processing">
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>

            <!-- ── Card 2: Change Password ── -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">🔒</div>
                    <div>
                        <h2 class="card-title">تغيير كلمة المرور</h2>
                        <p class="card-sub">استخدم كلمة مرور قوية وطويلة</p>
                    </div>
                </div>

                <form @submit.prevent="savePassword" class="form">
                    <label class="lbl">
                        كلمة المرور الحالية
                        <input v-model="pwForm.current_password" class="inp" type="password" autocomplete="current-password">
                        <span v-if="pwForm.errors.current_password" class="ferr">{{ pwForm.errors.current_password }}</span>
                    </label>

                    <label class="lbl">
                        كلمة المرور الجديدة
                        <input v-model="pwForm.password" class="inp" type="password" autocomplete="new-password">
                        <span v-if="pwForm.errors.password" class="ferr">{{ pwForm.errors.password }}</span>
                    </label>

                    <label class="lbl">
                        تأكيد كلمة المرور
                        <input v-model="pwForm.password_confirmation" class="inp" type="password" autocomplete="new-password">
                        <span v-if="pwForm.errors.password_confirmation" class="ferr">{{ pwForm.errors.password_confirmation }}</span>
                    </label>

                    <div class="form-foot">
                        <span v-if="pwForm.recentlySuccessful" class="saved-msg">✓ تم تغيير كلمة المرور</span>
                        <button type="submit" class="save-btn" :disabled="pwForm.processing">
                            تحديث كلمة المرور
                        </button>
                    </div>
                </form>
            </div>

            <!-- ── Card 3: Delete Account ── -->
            <div class="card danger-card">
                <div class="card-header">
                    <div class="card-icon danger-icon">⚠️</div>
                    <div>
                        <h2 class="card-title danger-title">حذف الحساب</h2>
                        <p class="card-sub">بعد الحذف، لا يمكن استرجاع بياناتك</p>
                    </div>
                </div>

                <p class="danger-desc">
                    سيتم حذف جميع طلباتك وتصاميمك وبياناتك بشكل دائم ولا يمكن التراجع عن هذا الإجراء.
                </p>

                <button class="del-btn" @click="showDelete = true">حذف حسابي نهائياً</button>
            </div>

        </div>

        <!-- ── Delete Confirm Modal ── -->
        <div v-if="showDelete" class="modal-bg" @click.self="showDelete = false">
            <div class="modal">
                <h3 class="modal-title">⚠️ تأكيد حذف الحساب</h3>
                <p class="modal-desc">أدخل كلمة مرورك لتأكيد حذف حسابك نهائياً.</p>

                <form @submit.prevent="deleteAccount" class="form" style="gap:12px">
                    <label class="lbl">
                        كلمة المرور
                        <input v-model="delForm.password" class="inp" type="password" required>
                        <span v-if="delForm.errors.password" class="ferr">{{ delForm.errors.password }}</span>
                    </label>

                    <div class="modal-actions">
                        <button type="button" class="cancel-btn" @click="showDelete = false">إلغاء</button>
                        <button type="submit" class="confirm-del-btn" :disabled="delForm.processing">
                            نعم، احذف حسابي
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </CustomerLayout>
</template>

<style scoped>
.profile-wrap {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* ── Card ── */
.card {
    background: var(--bg2);
    border: 1.5px solid var(--hair);
    border-radius: 20px;
    padding: 28px;
}
.danger-card {
    border-color: rgba(231,76,60,.2);
    background: rgba(231,76,60,.02);
}

.card-header {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--hair);
}
.card-icon {
    width: 46px; height: 46px;
    border-radius: 13px;
    background: rgba(52,215,127,.1);
    border: 1px solid rgba(52,215,127,.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.danger-icon {
    background: rgba(231,76,60,.08);
    border-color: rgba(231,76,60,.2);
}
.card-title {
    font-size: 16px; font-weight: 700;
    margin-bottom: 4px;
}
.danger-title { color: #e74c3c; }
.card-sub { font-size: 13px; color: var(--muted); }

/* ── Form ── */
.form {
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-width: 480px;
}
.lbl {
    display: flex;
    flex-direction: column;
    gap: 7px;
    font-size: 13px;
    color: var(--muted);
    font-weight: 500;
}
.inp {
    background: var(--bg);
    border: 1.5px solid var(--hair);
    border-radius: 12px;
    color: var(--ink);
    font-size: 14px;
    padding: 11px 14px;
    font-family: inherit;
    width: 100%;
    box-sizing: border-box;
    transition: border-color .2s;
}
.inp:focus { outline: none; border-color: var(--emerald); }
.ferr { font-size: 12px; color: #ff7a6b; }

.form-foot {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 14px;
    margin-top: 4px;
}
.saved-msg {
    font-size: 13px;
    color: var(--emerald-soft);
    font-weight: 600;
}
.save-btn {
    background: linear-gradient(150deg, var(--emerald-soft), var(--emerald-deep));
    color: var(--on-emerald);
    border: none;
    border-radius: 12px;
    padding: 11px 26px;
    font-size: 14px; font-weight: 600;
    cursor: pointer; font-family: inherit;
    transition: opacity .2s;
}
.save-btn:disabled { opacity: .5; }
.save-btn:hover:not(:disabled) { opacity: .88; }

/* ── Verify note ── */
.verify-note {
    font-size: 13px;
    color: #e0a800;
    background: rgba(255,193,7,.08);
    border: 1px solid rgba(255,193,7,.2);
    border-radius: 10px;
    padding: 10px 14px;
}

/* ── Danger zone ── */
.danger-desc {
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 20px;
    line-height: 1.7;
}
.del-btn {
    background: rgba(231,76,60,.1);
    color: #e74c3c;
    border: 1.5px solid rgba(231,76,60,.25);
    border-radius: 12px;
    padding: 11px 22px;
    font-size: 14px; font-weight: 600;
    cursor: pointer; font-family: inherit;
    transition: all .2s;
}
.del-btn:hover {
    background: rgba(231,76,60,.18);
    border-color: rgba(231,76,60,.4);
}

/* ── Delete modal ── */
.modal-bg {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.55);
    z-index: 500;
    display: flex; align-items: center; justify-content: center;
    padding: 20px;
}
.modal {
    background: var(--bg2);
    border: 1.5px solid var(--hair);
    border-radius: 20px;
    padding: 30px;
    width: 100%; max-width: 420px;
}
.modal-title {
    font-size: 18px; font-weight: 700;
    margin-bottom: 10px;
    color: #e74c3c;
}
.modal-desc {
    font-size: 13px; color: var(--muted);
    margin-bottom: 20px; line-height: 1.7;
}
.modal-actions {
    display: flex; gap: 10px; justify-content: flex-end;
    margin-top: 8px;
}
.cancel-btn {
    background: var(--glass);
    border: 1px solid var(--hair);
    border-radius: 12px;
    padding: 10px 20px;
    font-size: 14px; cursor: pointer;
    color: var(--ink); font-family: inherit;
}
.confirm-del-btn {
    background: #e74c3c;
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 10px 22px;
    font-size: 14px; font-weight: 600;
    cursor: pointer; font-family: inherit;
    transition: opacity .2s;
}
.confirm-del-btn:disabled { opacity: .5; }
.confirm-del-btn:hover:not(:disabled) { opacity: .88; }
</style>
