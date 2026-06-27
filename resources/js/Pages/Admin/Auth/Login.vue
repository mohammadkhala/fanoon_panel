<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';

defineProps({ status: String });

const form = useForm({ email: '', password: '', remember: false });

function submit() {
    form.post(route('admin.login.store'), {
        onFinish: () => form.reset('password'),
    });
}

onMounted(() => { document.documentElement.setAttribute('data-theme', 'dark'); });
</script>

<template>
    <Head title="دخول الإدارة" />

    <div class="lp">

        <!-- ─── Brand panel (right in RTL) ─── -->
        <div class="lp-brand">
            <div class="lp-dots"></div>
            <div class="lp-glow"></div>

            <div class="lp-brand-inner">
                <img src="/logo.png" alt="دعاية إيليت" class="lp-logo">
                <div class="lp-sep"></div>
                <p class="lp-tagline">نظام إدارة المتجر المتكامل</p>
                <div class="lp-chips">
                    <span class="lp-chip">⚡ سريع</span>
                    <span class="lp-chip">🔒 آمن</span>
                    <span class="lp-chip">📊 شامل</span>
                </div>
            </div>

            <div class="lp-ring lp-r1"></div>
            <div class="lp-ring lp-r2"></div>
            <div class="lp-ring lp-r3"></div>
        </div>

        <!-- ─── Form panel (left in RTL) ─── -->
        <div class="lp-side">
            <div class="lp-box">

                <div class="lp-top">
                    <div class="lp-ico">🛡</div>
                    <span class="lp-tag">وصول مقيّد</span>
                </div>

                <h1 class="lp-h">أهلاً بك</h1>
                <p class="lp-p">سجّل دخولك للوصول إلى لوحة التحكم</p>

                <div v-if="status" class="lp-status">{{ status }}</div>

                <form @submit.prevent="submit" class="lp-form" novalidate>

                    <!-- Email -->
                    <div class="lp-field" :class="{ 'lp-err-field': form.errors.email }">
                        <span class="lp-fic">✉</span>
                        <div class="lp-finput">
                            <label class="lp-flabel">البريد الإلكتروني</label>
                            <input
                                type="email"
                                v-model="form.email"
                                autocomplete="username"
                                autofocus
                                placeholder="admin@elite.ps"
                                class="lp-input"
                            >
                        </div>
                    </div>
                    <p v-if="form.errors.email" class="lp-ferr">{{ form.errors.email }}</p>

                    <!-- Password -->
                    <div class="lp-field" :class="{ 'lp-err-field': form.errors.password }">
                        <span class="lp-fic">🔑</span>
                        <div class="lp-finput">
                            <label class="lp-flabel">كلمة المرور</label>
                            <input
                                type="password"
                                v-model="form.password"
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="lp-input"
                            >
                        </div>
                    </div>
                    <p v-if="form.errors.password" class="lp-ferr">{{ form.errors.password }}</p>

                    <!-- Remember me (custom toggle) -->
                    <label class="lp-rem">
                        <input type="checkbox" v-model="form.remember" class="lp-cb">
                        <div class="lp-tog" :class="{ on: form.remember }">
                            <div class="lp-dot"></div>
                        </div>
                        <span>تذكّرني على هذا الجهاز</span>
                    </label>

                    <!-- Submit -->
                    <button type="submit" class="lp-btn" :disabled="form.processing">
                        <span>{{ form.processing ? 'جارٍ الدخول…' : 'دخول لوحة التحكم' }}</span>
                        <div class="lp-arr">←</div>
                    </button>

                </form>

                <a href="/" class="lp-back">→ العودة إلى المتجر</a>
                <p class="lp-enc">🔒 اتصال مشفّر — Elite للدعاية والإعلان</p>

            </div>
        </div>

    </div>
</template>

<style scoped>
/* ── root ─────────────────────────────────── */
.lp {
    min-height: 100vh;
    display: flex;
    background: #030504;
    color: #f2f6f3;
    font-family: 'IBM Plex Sans Arabic', sans-serif;
    direction: rtl;
}

/* ── brand panel ──────────────────────────── */
.lp-brand {
    flex: 1;
    position: relative;
    overflow: hidden;
    background: linear-gradient(150deg, #021009 0%, #041d0c 55%, #072a12 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.lp-dots {
    position: absolute;
    inset: 0;
    background-image: radial-gradient(circle, rgba(52,215,127,.22) 1px, transparent 1px);
    background-size: 30px 30px;
}

.lp-glow {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 70% 60% at 50% 50%, rgba(52,215,127,.1) 0%, transparent 70%);
    animation: lpPulse 4s ease-in-out infinite alternate;
}

@keyframes lpPulse {
    from { opacity: .55; }
    to   { opacity: 1; }
}

.lp-brand-inner {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
    padding: 40px;
}

.lp-logo {
    width: 210px;
    height: auto;
    animation: lpFloat 6s ease-in-out infinite;
    filter: drop-shadow(0 16px 48px rgba(52,215,127,.3));
}

@keyframes lpFloat {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-10px); }
}

.lp-sep {
    width: 52px;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(52,215,127,.65), transparent);
}

.lp-tagline {
    font-size: 14px;
    color: rgba(255,255,255,.38);
    font-weight: 300;
    letter-spacing: .03em;
    text-align: center;
}

.lp-chips {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: center;
}

.lp-chip {
    font-size: 11px;
    font-weight: 500;
    color: rgba(92,240,155,.85);
    background: rgba(52,215,127,.07);
    border: 1px solid rgba(52,215,127,.18);
    border-radius: 999px;
    padding: 5px 13px;
}

/* decorative rings */
.lp-ring {
    position: absolute;
    border-radius: 50%;
    border: 1px solid rgba(52,215,127,.1);
    pointer-events: none;
}
.lp-r1 { width: 320px; height: 320px; top: -100px; right: -100px; }
.lp-r2 { width: 520px; height: 520px; bottom: -190px; left: -190px; border-color: rgba(52,215,127,.05); }
.lp-r3 {
    width: 140px; height: 140px;
    top: 42%; right: 8%;
    animation: lpSpin 22s linear infinite;
}

@keyframes lpSpin { to { transform: rotate(360deg); } }

/* ── form panel ───────────────────────────── */
.lp-side {
    width: 460px;
    flex-shrink: 0;
    background: #07090a;
    border-right: 1px solid rgba(255,255,255,.04);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px 36px;
}

.lp-box {
    width: 100%;
    max-width: 340px;
    animation: lpSlideUp .5s cubic-bezier(0.32,0.72,0,1) both;
}

@keyframes lpSlideUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* header row */
.lp-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
}

.lp-ico {
    font-size: 34px;
    line-height: 1;
    filter: drop-shadow(0 4px 16px rgba(52,215,127,.35));
}

.lp-tag {
    font-size: 10px;
    letter-spacing: .2em;
    text-transform: uppercase;
    font-weight: 600;
    color: rgba(92,240,155,.9);
    background: rgba(52,215,127,.08);
    border: 1px solid rgba(52,215,127,.2);
    border-radius: 999px;
    padding: 5px 13px;
}

/* title */
.lp-h {
    font-size: 32px;
    font-weight: 700;
    letter-spacing: -.6px;
    color: #f0f5f1;
    margin: 0 0 8px;
    line-height: 1.2;
}

.lp-p {
    font-size: 13px;
    color: rgba(255,255,255,.3);
    font-weight: 300;
    margin: 0 0 28px;
    line-height: 1.7;
}

/* status msg */
.lp-status {
    background: rgba(52,215,127,.1);
    border: 1px solid rgba(52,215,127,.25);
    color: rgba(92,240,155,.9);
    border-radius: 12px;
    padding: 11px 14px;
    font-size: 13px;
    margin-bottom: 18px;
}

/* form */
.lp-form { display: flex; flex-direction: column; gap: 10px; }

/* field */
.lp-field {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255,255,255,.026);
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 16px;
    padding: 0 16px;
    transition: border-color .25s, background .25s, box-shadow .25s;
    cursor: text;
}

.lp-field:focus-within {
    border-color: rgba(52,215,127,.45);
    background: rgba(52,215,127,.025);
    box-shadow: 0 0 0 3px rgba(52,215,127,.07);
}

.lp-err-field {
    border-color: rgba(255,100,80,.4) !important;
    box-shadow: 0 0 0 3px rgba(255,100,80,.05) !important;
}

.lp-fic {
    font-size: 15px;
    opacity: .38;
    flex-shrink: 0;
    user-select: none;
}

.lp-finput {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 14px 0;
}

.lp-flabel {
    font-size: 9px;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: rgba(255,255,255,.28);
    margin-bottom: 4px;
    user-select: none;
}

.lp-input {
    background: none;
    border: none;
    outline: none;
    color: #e8ede9;
    font-family: 'IBM Plex Sans Arabic', sans-serif;
    font-size: 14px;
    width: 100%;
    padding: 0;
}

.lp-input::placeholder { color: rgba(255,255,255,.17); }

.lp-ferr {
    font-size: 12px;
    color: #ff7a6b;
    margin: -2px 0 4px;
    padding-right: 4px;
}

/* remember toggle */
.lp-rem {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    color: rgba(255,255,255,.35);
    cursor: pointer;
    user-select: none;
    margin-top: 4px;
}

.lp-cb { position: absolute; opacity: 0; width: 0; height: 0; }

.lp-tog {
    width: 36px;
    height: 20px;
    border-radius: 999px;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.1);
    position: relative;
    flex-shrink: 0;
    transition: all .3s;
}

.lp-tog.on {
    background: rgba(52,215,127,.22);
    border-color: rgba(52,215,127,.45);
}

.lp-dot {
    position: absolute;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: rgba(255,255,255,.42);
    top: 2px;
    right: 2px;
    transition: all .3s;
}

.lp-tog.on .lp-dot {
    right: auto;
    left: 2px;
    background: rgba(92,240,155,1);
    box-shadow: 0 2px 10px rgba(52,215,127,.55);
}

/* submit */
.lp-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    background: linear-gradient(150deg, #5cf09b, #0c7a45);
    color: #031a0d;
    border: none;
    border-radius: 16px;
    padding: 16px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    font-family: 'IBM Plex Sans Arabic', sans-serif;
    transition: all .3s;
    box-shadow: 0 8px 28px rgba(52,215,127,.22), inset 0 1px 1px rgba(255,255,255,.22);
    margin-top: 8px;
}

.lp-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 14px 42px rgba(52,215,127,.32), inset 0 1px 1px rgba(255,255,255,.22);
}

.lp-btn:active:not(:disabled) { transform: translateY(0); }

.lp-btn:disabled { opacity: .5; cursor: default; transform: none; box-shadow: none; }

.lp-arr {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: rgba(3,26,13,.22);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
}

/* back link */
.lp-back {
    display: block;
    text-align: center;
    margin-top: 22px;
    font-size: 12px;
    color: rgba(255,255,255,.22);
    text-decoration: none;
    transition: color .3s;
}

.lp-back:hover { color: rgba(255,255,255,.55); }

/* footer note */
.lp-enc {
    text-align: center;
    margin-top: 18px;
    font-size: 11px;
    color: rgba(255,255,255,.14);
    font-weight: 300;
}

/* ── responsive ───────────────────────────── */
@media (max-width: 780px) {
    .lp { flex-direction: column; }

    .lp-brand {
        flex: none;
        min-height: 210px;
    }

    .lp-logo { width: 130px; }
    .lp-tagline, .lp-chips { display: none; }

    .lp-side {
        width: 100%;
        flex: 1;
        border-right: none;
        border-top: 1px solid rgba(255,255,255,.05);
        padding: 32px 20px;
    }

    .lp-r1, .lp-r2, .lp-r3 { display: none; }

    .lp-h { font-size: 26px; }
}
</style>
