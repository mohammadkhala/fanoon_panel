<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({ canResetPassword: Boolean, status: String });

const form = useForm({ email: '', password: '', remember: false });

function submit() {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="تسجيل الدخول" />

    <div class="lx">

        <!-- ── Brand panel (right in RTL) ── -->
        <div class="lx-brand">
            <div class="lx-bdots"></div>
            <div class="lx-bglow"></div>
            <div class="lx-binner">
                <img src="/logo.png" alt="دعاية إيليت" class="lx-logo">
                <div class="lx-bsep"></div>
                <p class="lx-btag">اطبع تصميمك — نوصّله لبابك</p>
                <div class="lx-feats">
                    <div class="lx-feat"><span>🎨</span><span>محرّر تصميم احترافي</span></div>
                    <div class="lx-feat"><span>💼</span><span>أسعار خاصة للشركات</span></div>
                    <div class="lx-feat"><span>🚀</span><span>توصيل سريع لبابك</span></div>
                </div>
            </div>
            <div class="lx-ring lx-ra"></div>
            <div class="lx-ring lx-rb"></div>
            <div class="lx-ring lx-rc"></div>
        </div>

        <!-- ── Form panel (left in RTL) ── -->
        <div class="lx-side">
            <div class="lx-box">

                <h1 class="lx-h">أهلاً بعودتك 👋</h1>
                <p class="lx-p">سجّل دخولك لمتابعة طلباتك وتصاميمك</p>

                <div v-if="status" class="lx-notice">{{ status }}</div>

                <form @submit.prevent="submit" class="lx-form" novalidate>

                    <div class="lx-field" :class="{ 'lx-haserr': form.errors.email }">
                        <span class="lx-fic">✉</span>
                        <div class="lx-fbody">
                            <label class="lx-fl">البريد الإلكتروني</label>
                            <input type="email" v-model="form.email" autocomplete="username"
                                   autofocus placeholder="example@email.com" class="lx-in">
                        </div>
                    </div>
                    <p v-if="form.errors.email" class="lx-em">{{ form.errors.email }}</p>

                    <div class="lx-field" :class="{ 'lx-haserr': form.errors.password }">
                        <span class="lx-fic">🔑</span>
                        <div class="lx-fbody">
                            <label class="lx-fl">كلمة المرور</label>
                            <input type="password" v-model="form.password" autocomplete="current-password"
                                   placeholder="••••••••" class="lx-in">
                        </div>
                    </div>
                    <p v-if="form.errors.password" class="lx-em">{{ form.errors.password }}</p>

                    <div class="lx-row">
                        <label class="lx-rem">
                            <input type="checkbox" v-model="form.remember" class="lx-cbx">
                            <div class="lx-tog" :class="{ on: form.remember }">
                                <div class="lx-togdot"></div>
                            </div>
                            <span>تذكّرني</span>
                        </label>
                        <Link v-if="canResetPassword" :href="route('password.request')" class="lx-forgot">
                            نسيت كلمة المرور؟
                        </Link>
                    </div>

                    <button type="submit" class="lx-btn" :disabled="form.processing">
                        <span>{{ form.processing ? 'جارٍ الدخول…' : 'تسجيل الدخول' }}</span>
                        <div class="lx-arr">←</div>
                    </button>

                </form>

                <div class="lx-or"><span>أو</span></div>

                <Link :href="route('register')" class="lx-newacct">
                    ليس لديك حساب؟ <strong>سجّل الآن مجاناً</strong>
                </Link>

                <a href="/" class="lx-home">→ العودة إلى المتجر</a>

            </div>
        </div>

    </div>
</template>

<style scoped>
/* ─── Root ──────────────────────────────── */
.lx {
    min-height: 100vh;
    display: flex;
    direction: rtl;
    background: var(--bg);
    color: var(--ink);
    font-family: 'IBM Plex Sans Arabic', sans-serif;
}

/* ─── Brand panel ───────────────────────── */
.lx-brand {
    flex: 1;
    position: relative;
    overflow: hidden;
    background: linear-gradient(150deg, #021009 0%, #041d0c 55%, #072a12 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.lx-bdots {
    position: absolute;
    inset: 0;
    background-image: radial-gradient(circle, rgba(52,215,127,.22) 1px, transparent 1px);
    background-size: 30px 30px;
}

.lx-bglow {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 65% 55% at 50% 50%, rgba(52,215,127,.12) 0%, transparent 70%);
    animation: lxPulse 4s ease-in-out infinite alternate;
}

@keyframes lxPulse {
    from { opacity: .5; }
    to   { opacity: 1; }
}

.lx-binner {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 22px;
    padding: 48px 40px;
}

.lx-logo {
    width: 200px;
    height: auto;
    animation: lxFloat 6s ease-in-out infinite;
    filter: drop-shadow(0 16px 48px rgba(52,215,127,.28));
}

@keyframes lxFloat {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-10px); }
}

.lx-bsep {
    width: 50px;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(52,215,127,.6), transparent);
}

.lx-btag {
    font-size: 15px;
    color: rgba(255,255,255,.5);
    font-weight: 300;
    text-align: center;
    letter-spacing: .02em;
}

.lx-feats {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
    max-width: 260px;
}

.lx-feat {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(52,215,127,.06);
    border: 1px solid rgba(52,215,127,.14);
    border-radius: 14px;
    padding: 11px 16px;
    font-size: 14px;
    color: rgba(255,255,255,.75);
}
.lx-feat > span:first-child { font-size: 18px; }

/* rings */
.lx-ring { position: absolute; border-radius: 50%; border: 1px solid rgba(52,215,127,.1); pointer-events: none; }
.lx-ra { width: 280px; height: 280px; top: -80px; right: -80px; }
.lx-rb { width: 480px; height: 480px; bottom: -160px; left: -160px; border-color: rgba(52,215,127,.05); }
.lx-rc { width: 120px; height: 120px; top: 40%; right: 10%; animation: lxSpin 20s linear infinite; }

@keyframes lxSpin { to { transform: rotate(360deg); } }

/* ─── Form panel ────────────────────────── */
.lx-side {
    width: 460px;
    flex-shrink: 0;
    background: var(--bg2);
    border-right: 1px solid var(--hair);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px 36px;
}

.lx-box {
    width: 100%;
    max-width: 340px;
    animation: lxUp .45s cubic-bezier(0.32,0.72,0,1) both;
}

@keyframes lxUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
}

.lx-h {
    font-size: 30px;
    font-weight: 700;
    letter-spacing: -.5px;
    color: var(--ink);
    margin: 0 0 8px;
    line-height: 1.2;
}

.lx-p {
    font-size: 13px;
    color: var(--muted);
    font-weight: 300;
    margin: 0 0 28px;
    line-height: 1.7;
}

.lx-notice {
    background: rgba(16,180,106,.1);
    border: 1px solid rgba(16,180,106,.25);
    color: var(--emerald-soft);
    border-radius: 12px;
    padding: 11px 14px;
    font-size: 13px;
    margin-bottom: 18px;
}

/* form */
.lx-form { display: flex; flex-direction: column; gap: 10px; }

.lx-field {
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

.lx-field:focus-within {
    border-color: var(--emerald);
    box-shadow: 0 0 0 3px rgba(16,180,106,.1);
}

.lx-haserr { border-color: #e74c3c !important; box-shadow: 0 0 0 3px rgba(231,76,60,.07) !important; }

.lx-fic { font-size: 15px; opacity: .4; flex-shrink: 0; user-select: none; }

.lx-fbody { flex: 1; display: flex; flex-direction: column; padding: 13px 0; }

.lx-fl {
    font-size: 9px;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 4px;
    user-select: none;
    opacity: .75;
}

.lx-in {
    background: none;
    border: none;
    outline: none;
    color: var(--ink);
    font-family: 'IBM Plex Sans Arabic', sans-serif;
    font-size: 14px;
    width: 100%;
    padding: 0;
}

.lx-em { font-size: 12px; color: #e74c3c; margin: -2px 0 4px; padding-right: 4px; }

/* remember + forgot row */
.lx-row { display: flex; align-items: center; justify-content: space-between; margin-top: 4px; }

.lx-rem { display: flex; align-items: center; gap: 9px; font-size: 12px; color: var(--muted); cursor: pointer; user-select: none; }

.lx-cbx { position: absolute; opacity: 0; width: 0; height: 0; }

.lx-tog {
    width: 34px; height: 19px;
    border-radius: 999px;
    background: var(--hair);
    border: 1px solid var(--hair);
    position: relative; flex-shrink: 0;
    transition: all .3s;
}
.lx-tog.on { background: rgba(16,180,106,.18); border-color: rgba(16,180,106,.4); }

.lx-togdot {
    position: absolute;
    width: 13px; height: 13px;
    border-radius: 50%;
    background: var(--muted);
    top: 2px; right: 2px;
    transition: all .3s;
}
.lx-tog.on .lx-togdot { right: auto; left: 2px; background: var(--emerald-soft); box-shadow: 0 2px 8px rgba(16,180,106,.4); }

.lx-forgot { font-size: 12px; color: var(--emerald-soft); text-decoration: none; font-weight: 500; }
.lx-forgot:hover { opacity: .75; }

/* button */
.lx-btn {
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
.lx-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 12px 36px rgba(16,180,106,.3); }
.lx-btn:active:not(:disabled) { transform: translateY(0); }
.lx-btn:disabled { opacity: .5; cursor: default; transform: none; box-shadow: none; }

.lx-arr {
    width: 28px; height: 28px; border-radius: 50%;
    background: rgba(0,0,0,.12);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 700;
}

/* divider */
.lx-or {
    display: flex; align-items: center; gap: 12px;
    margin: 22px 0 0;
    color: var(--muted); font-size: 12px; opacity: .6;
}
.lx-or::before, .lx-or::after { content: ''; flex: 1; height: 1px; background: var(--hair); }

.lx-newacct {
    display: block; text-align: center; margin-top: 14px;
    font-size: 13px; color: var(--muted); text-decoration: none; transition: color .2s;
}
.lx-newacct:hover { color: var(--ink); }
.lx-newacct strong { color: var(--emerald-soft); font-weight: 600; }

.lx-home {
    display: block; text-align: center; margin-top: 10px;
    font-size: 12px; color: var(--muted); text-decoration: none; opacity: .5; transition: opacity .2s;
}
.lx-home:hover { opacity: 1; }

/* ─── Responsive ──────────────────────── */
@media (max-width: 780px) {
    .lx { flex-direction: column; }
    .lx-brand { flex: none; min-height: 200px; }
    .lx-logo { width: 130px; animation: none; }
    .lx-btag, .lx-feats { display: none; }
    .lx-side { width: 100%; flex: 1; border-right: none; border-top: 1px solid var(--hair); padding: 32px 20px; }
    .lx-ra, .lx-rb, .lx-rc { display: none; }
    .lx-h { font-size: 24px; }
}
</style>
