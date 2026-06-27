<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    account_type: 'individual',
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    company_name: '',
    trade_license_no: '',
    city: '',
    trade_license: null,
});

function onFile(e) {
    form.trade_license = e.target.files[0] ?? null;
}

function submit() {
    form.post(route('register'), {
        forceFormData: true,
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="إنشاء حساب" />

    <div class="rx">

        <!-- ── Brand panel (right in RTL) ── -->
        <div class="rx-brand">
            <div class="rx-bdots"></div>
            <div class="rx-bglow"></div>
            <div class="rx-binner">
                <img src="/logo.png" alt="دعاية إيليت" class="rx-logo">
                <div class="rx-bsep"></div>
                <p class="rx-btitle">انضم لمجتمع إيليت</p>
                <div class="rx-acards">
                    <div class="rx-acard" :class="{ active: form.account_type === 'individual' }">
                        <div class="rx-acard-ico">👤</div>
                        <div>
                            <div class="rx-acard-lbl">حساب فردي</div>
                            <div class="rx-acard-sub">تصميم وطباعة بأسعار التجزئة</div>
                        </div>
                    </div>
                    <div class="rx-acard" :class="{ active: form.account_type === 'company' }">
                        <div class="rx-acard-ico">🏢</div>
                        <div>
                            <div class="rx-acard-lbl">حساب شركة</div>
                            <div class="rx-acard-sub">أسعار جملة حصرية بعد اعتماد السجل</div>
                        </div>
                    </div>
                </div>
                <p class="rx-bnote">✔ مجاناً تماماً · لا بطاقة ائتمانية</p>
            </div>
            <div class="rx-ring rx-ra"></div>
            <div class="rx-ring rx-rb"></div>
        </div>

        <!-- ── Form panel (left in RTL) ── -->
        <div class="rx-side">
            <div class="rx-scroll">
                <div class="rx-box">

                    <h1 class="rx-h">إنشاء حساب جديد</h1>
                    <p class="rx-p">سجّل الآن وابدأ تصميماتك في دقائق</p>

                    <!-- Account type tabs -->
                    <div class="rx-tabs">
                        <button type="button" class="rx-tab" :class="{ on: form.account_type === 'individual' }"
                                @click="form.account_type = 'individual'">
                            <span>👤</span><span>فرد</span>
                        </button>
                        <button type="button" class="rx-tab" :class="{ on: form.account_type === 'company' }"
                                @click="form.account_type = 'company'">
                            <span>🏢</span><span>شركة</span>
                        </button>
                    </div>

                    <form @submit.prevent="submit" class="rx-form" novalidate>

                        <!-- Name -->
                        <div class="rx-field" :class="{ 'rx-haserr': form.errors.name }">
                            <span class="rx-fic">👤</span>
                            <div class="rx-fbody">
                                <label class="rx-fl">{{ form.account_type === 'company' ? 'اسم المسؤول' : 'الاسم الكامل' }}</label>
                                <input type="text" v-model="form.name" autocomplete="name" autofocus
                                       :placeholder="form.account_type === 'company' ? 'اسم المسؤول' : 'الاسم الكامل'" class="rx-in">
                            </div>
                        </div>
                        <p v-if="form.errors.name" class="rx-em">{{ form.errors.name }}</p>

                        <!-- Email -->
                        <div class="rx-field" :class="{ 'rx-haserr': form.errors.email }">
                            <span class="rx-fic">✉</span>
                            <div class="rx-fbody">
                                <label class="rx-fl">البريد الإلكتروني</label>
                                <input type="email" v-model="form.email" autocomplete="username"
                                       placeholder="example@email.com" class="rx-in">
                            </div>
                        </div>
                        <p v-if="form.errors.email" class="rx-em">{{ form.errors.email }}</p>

                        <!-- Phone -->
                        <div class="rx-field" :class="{ 'rx-haserr': form.errors.phone }">
                            <span class="rx-fic">📱</span>
                            <div class="rx-fbody">
                                <label class="rx-fl">رقم الهاتف</label>
                                <input type="tel" v-model="form.phone" autocomplete="tel"
                                       placeholder="05xxxxxxxx" class="rx-in">
                            </div>
                        </div>
                        <p v-if="form.errors.phone" class="rx-em">{{ form.errors.phone }}</p>

                        <!-- Company-specific fields -->
                        <template v-if="form.account_type === 'company'">

                            <div class="rx-sec">معلومات الشركة</div>

                            <div class="rx-field" :class="{ 'rx-haserr': form.errors.company_name }">
                                <span class="rx-fic">🏢</span>
                                <div class="rx-fbody">
                                    <label class="rx-fl">اسم الشركة</label>
                                    <input type="text" v-model="form.company_name"
                                           placeholder="شركة الأمل للتسويق" class="rx-in">
                                </div>
                            </div>
                            <p v-if="form.errors.company_name" class="rx-em">{{ form.errors.company_name }}</p>

                            <div class="rx-twocols">
                                <div>
                                    <div class="rx-field" :class="{ 'rx-haserr': form.errors.trade_license_no }">
                                        <span class="rx-fic">🗂</span>
                                        <div class="rx-fbody">
                                            <label class="rx-fl">رقم السجل التجاري</label>
                                            <input type="text" v-model="form.trade_license_no"
                                                   placeholder="0000000000" class="rx-in">
                                        </div>
                                    </div>
                                    <p v-if="form.errors.trade_license_no" class="rx-em">{{ form.errors.trade_license_no }}</p>
                                </div>
                                <div>
                                    <div class="rx-field" :class="{ 'rx-haserr': form.errors.city }">
                                        <span class="rx-fic">📍</span>
                                        <div class="rx-fbody">
                                            <label class="rx-fl">المدينة</label>
                                            <input type="text" v-model="form.city"
                                                   placeholder="رام الله" class="rx-in">
                                        </div>
                                    </div>
                                    <p v-if="form.errors.city" class="rx-em">{{ form.errors.city }}</p>
                                </div>
                            </div>

                            <!-- File upload -->
                            <label class="rx-upload" :class="{ uploaded: form.trade_license }">
                                <input type="file" accept=".pdf,.jpg,.jpeg,.png" @change="onFile" class="rx-ffile">
                                <span class="rx-upload-ico">{{ form.trade_license ? '✅' : '📎' }}</span>
                                <span class="rx-upload-txt">{{ form.trade_license ? form.trade_license.name : 'انقر لرفع ملف السجل التجاري' }}</span>
                                <span class="rx-upload-hint">PDF, JPG, PNG — حد أقصى 5MB</span>
                            </label>
                            <p v-if="form.errors.trade_license" class="rx-em">{{ form.errors.trade_license }}</p>

                        </template>

                        <!-- Password section label when company (long form) -->
                        <div v-if="form.account_type === 'company'" class="rx-sec">كلمة المرور</div>

                        <!-- Password -->
                        <div class="rx-field" :class="{ 'rx-haserr': form.errors.password }">
                            <span class="rx-fic">🔑</span>
                            <div class="rx-fbody">
                                <label class="rx-fl">كلمة المرور</label>
                                <input type="password" v-model="form.password" autocomplete="new-password"
                                       placeholder="8 أحرف على الأقل" class="rx-in">
                            </div>
                        </div>
                        <p v-if="form.errors.password" class="rx-em">{{ form.errors.password }}</p>

                        <!-- Confirm password -->
                        <div class="rx-field" :class="{ 'rx-haserr': form.errors.password_confirmation }">
                            <span class="rx-fic">🔒</span>
                            <div class="rx-fbody">
                                <label class="rx-fl">تأكيد كلمة المرور</label>
                                <input type="password" v-model="form.password_confirmation" autocomplete="new-password"
                                       placeholder="أعد كتابة كلمة المرور" class="rx-in">
                            </div>
                        </div>
                        <p v-if="form.errors.password_confirmation" class="rx-em">{{ form.errors.password_confirmation }}</p>

                        <button type="submit" class="rx-btn" :disabled="form.processing">
                            <span>{{ form.processing ? 'جارٍ إنشاء الحساب…' : 'إنشاء الحساب' }}</span>
                            <div class="rx-arr">←</div>
                        </button>

                    </form>

                    <div class="rx-or"><span>لديك حساب بالفعل؟</span></div>

                    <Link :href="route('login')" class="rx-login">
                        تسجيل الدخول ←
                    </Link>

                    <a href="/" class="rx-home">→ العودة إلى المتجر</a>

                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
/* ─── Root ──────────────────────────────── */
.rx {
    min-height: 100vh;
    display: flex;
    direction: rtl;
    background: var(--bg);
    color: var(--ink);
    font-family: 'IBM Plex Sans Arabic', sans-serif;
}

/* ─── Brand panel ───────────────────────── */
.rx-brand {
    flex: 1;
    position: relative;
    overflow: hidden;
    background: linear-gradient(150deg, #021009 0%, #041d0c 55%, #072a12 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.rx-bdots {
    position: absolute;
    inset: 0;
    background-image: radial-gradient(circle, rgba(52,215,127,.22) 1px, transparent 1px);
    background-size: 30px 30px;
}

.rx-bglow {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 65% 55% at 50% 50%, rgba(52,215,127,.12) 0%, transparent 70%);
    animation: rxPulse 4s ease-in-out infinite alternate;
}

@keyframes rxPulse {
    from { opacity: .5; }
    to   { opacity: 1; }
}

.rx-binner {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
    padding: 48px 36px;
}

.rx-logo {
    width: 180px;
    height: auto;
    filter: drop-shadow(0 14px 40px rgba(52,215,127,.28));
    animation: rxFloat 6s ease-in-out infinite;
}

@keyframes rxFloat {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-8px); }
}

.rx-bsep {
    width: 50px;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(52,215,127,.6), transparent);
}

.rx-btitle {
    font-size: 16px;
    font-weight: 600;
    color: rgba(255,255,255,.8);
    text-align: center;
}

/* account type showcase cards */
.rx-acards {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
    max-width: 270px;
}

.rx-acard {
    display: flex;
    align-items: center;
    gap: 14px;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 16px;
    padding: 14px 16px;
    transition: all .35s;
}

.rx-acard.active {
    background: rgba(52,215,127,.1);
    border-color: rgba(52,215,127,.3);
    box-shadow: 0 4px 20px rgba(52,215,127,.1);
}

.rx-acard-ico { font-size: 24px; flex-shrink: 0; }
.rx-acard-lbl { font-size: 14px; font-weight: 600; color: rgba(255,255,255,.85); margin-bottom: 3px; }
.rx-acard-sub { font-size: 12px; color: rgba(255,255,255,.38); font-weight: 300; line-height: 1.4; }
.rx-acard.active .rx-acard-sub { color: rgba(92,240,155,.65); }

.rx-bnote {
    font-size: 12px;
    color: rgba(255,255,255,.28);
    font-weight: 300;
    text-align: center;
}

/* rings */
.rx-ring { position: absolute; border-radius: 50%; border: 1px solid rgba(52,215,127,.1); pointer-events: none; }
.rx-ra { width: 280px; height: 280px; top: -80px; right: -80px; }
.rx-rb { width: 480px; height: 480px; bottom: -160px; left: -160px; border-color: rgba(52,215,127,.05); }

/* ─── Form panel ────────────────────────── */
.rx-side {
    width: 500px;
    flex-shrink: 0;
    background: var(--bg2);
    border-right: 1px solid var(--hair);
    overflow-y: auto;
}

.rx-scroll {
    display: flex;
    justify-content: center;
    min-height: 100%;
    padding: 48px 36px;
}

.rx-box {
    width: 100%;
    max-width: 380px;
    animation: rxUp .45s cubic-bezier(0.32,0.72,0,1) both;
}

@keyframes rxUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
}

.rx-h {
    font-size: 28px;
    font-weight: 700;
    letter-spacing: -.4px;
    color: var(--ink);
    margin: 0 0 6px;
    line-height: 1.2;
}

.rx-p {
    font-size: 13px;
    color: var(--muted);
    font-weight: 300;
    margin: 0 0 22px;
}

/* account type tabs */
.rx-tabs {
    display: flex;
    gap: 8px;
    background: var(--bg);
    border: 1px solid var(--hair);
    border-radius: 16px;
    padding: 5px;
    margin-bottom: 20px;
}

.rx-tab {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 12px;
    border: none;
    border-radius: 12px;
    background: none;
    color: var(--muted);
    font-family: 'IBM Plex Sans Arabic', sans-serif;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all .25s;
}

.rx-tab.on {
    background: var(--bg2);
    color: var(--emerald-soft);
    box-shadow: 0 2px 10px rgba(0,0,0,.08);
}

/* form */
.rx-form { display: flex; flex-direction: column; gap: 10px; }

.rx-sec {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--emerald-soft);
    margin-top: 6px;
    padding-right: 4px;
    opacity: .85;
}

.rx-field {
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--bg);
    border: 1.5px solid var(--hair);
    border-radius: 16px;
    padding: 0 16px;
    transition: border-color .25s, box-shadow .25s;
    cursor: text;
}

.rx-field:focus-within {
    border-color: var(--emerald);
    box-shadow: 0 0 0 3px rgba(16,180,106,.1);
}

.rx-haserr { border-color: #e74c3c !important; box-shadow: 0 0 0 3px rgba(231,76,60,.07) !important; }

.rx-fic { font-size: 15px; opacity: .4; flex-shrink: 0; user-select: none; }

.rx-fbody { flex: 1; display: flex; flex-direction: column; padding: 12px 0; }

.rx-fl {
    font-size: 9px;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 4px;
    user-select: none;
    opacity: .7;
}

.rx-in {
    background: none;
    border: none;
    outline: none;
    color: var(--ink);
    font-family: 'IBM Plex Sans Arabic', sans-serif;
    font-size: 14px;
    width: 100%;
    padding: 0;
}

.rx-em { font-size: 12px; color: #e74c3c; margin: -2px 0 4px; padding-right: 4px; }

/* two-column grid */
.rx-twocols { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

/* file upload */
.rx-upload {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    border: 2px dashed var(--hair);
    border-radius: 16px;
    padding: 20px 16px;
    cursor: pointer;
    transition: all .25s;
    text-align: center;
    position: relative;
    background: var(--bg);
}

.rx-upload:hover { border-color: var(--emerald); background: rgba(16,180,106,.03); }
.rx-upload.uploaded { border-style: solid; border-color: var(--emerald); background: rgba(16,180,106,.04); }

.rx-ffile { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }

.rx-upload-ico { font-size: 22px; }
.rx-upload-txt { font-size: 13px; color: var(--ink); font-weight: 500; word-break: break-all; max-width: 100%; }
.rx-upload-hint { font-size: 11px; color: var(--muted); opacity: .6; }

/* submit button */
.rx-btn {
    width: 100%;
    display: flex; align-items: center; justify-content: center; gap: 12px;
    background: linear-gradient(150deg, var(--emerald-soft), var(--emerald-deep));
    color: var(--on-emerald);
    border: none; border-radius: 16px; padding: 16px;
    font-size: 15px; font-weight: 700; cursor: pointer;
    font-family: 'IBM Plex Sans Arabic', sans-serif;
    transition: all .3s;
    box-shadow: 0 6px 22px rgba(16,180,106,.22);
    margin-top: 8px;
}
.rx-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 12px 36px rgba(16,180,106,.3); }
.rx-btn:active:not(:disabled) { transform: translateY(0); }
.rx-btn:disabled { opacity: .5; cursor: default; transform: none; box-shadow: none; }

.rx-arr {
    width: 28px; height: 28px; border-radius: 50%;
    background: rgba(0,0,0,.12);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 700;
}

/* divider */
.rx-or {
    display: flex; align-items: center; gap: 12px;
    margin: 22px 0 0;
    color: var(--muted); font-size: 12px; opacity: .6;
}
.rx-or::before, .rx-or::after { content: ''; flex: 1; height: 1px; background: var(--hair); }

.rx-login {
    display: block; text-align: center; margin-top: 12px;
    font-size: 13px; color: var(--emerald-soft); text-decoration: none; font-weight: 600;
    transition: opacity .2s;
}
.rx-login:hover { opacity: .75; }

.rx-home {
    display: block; text-align: center; margin-top: 10px;
    font-size: 12px; color: var(--muted); text-decoration: none; opacity: .5; transition: opacity .2s;
}
.rx-home:hover { opacity: 1; }

/* ─── Responsive ──────────────────────── */
@media (max-width: 860px) {
    .rx { flex-direction: column; }
    .rx-brand { flex: none; min-height: 220px; }
    .rx-logo { width: 130px; animation: none; }
    .rx-btitle { font-size: 14px; }
    .rx-acards { flex-direction: row; max-width: 100%; }
    .rx-acard { flex: 1; flex-direction: column; text-align: center; gap: 6px; }
    .rx-bnote { display: none; }
    .rx-side { width: 100%; overflow-y: visible; border-right: none; border-top: 1px solid var(--hair); }
    .rx-scroll { padding: 28px 20px; }
    .rx-ra, .rx-rb { display: none; }
    .rx-h { font-size: 22px; }
    .rx-twocols { grid-template-columns: 1fr; }
}

@media (max-width: 480px) {
    .rx-acards { flex-direction: column; }
}
</style>
