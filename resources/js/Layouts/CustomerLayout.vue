<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import StoreLayout from '@/Layouts/StoreLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const initials = computed(() => {
    if (!user.value?.name) return '؟';
    return user.value.name.trim().split(/\s+/).slice(0, 2)
        .map(w => w[0]).join('').toUpperCase();
});

const nav = [
    { label: 'لوحة التحكم', icon: '🏠', r: 'dashboard',   m: 'Dashboard' },
    { label: 'طلباتي',      icon: '📦', r: 'orders.index', m: 'Orders/' },
    { label: 'السلة',       icon: '🛒', r: 'cart.index',   m: 'Cart/' },
    { label: 'ملفي الشخصي', icon: '👤', r: 'profile.edit', m: 'Profile/' },
];

const cur = computed(() => page.component ?? '');
function isOn(n) {
    return n.m === 'Dashboard'
        ? cur.value === 'Dashboard'
        : cur.value.startsWith(n.m);
}

function logout() { router.post(route('logout')); }
</script>

<template>
    <StoreLayout>
        <!--
            Fixed nav height ≈ 100px  → padding-top: 120px clears it safely.
            RTL flex rule: first child → RIGHT, second child → LEFT.
            sidebar FIRST in DOM → RIGHT side ✓
            main    SECOND in DOM → LEFT side ✓
        -->
        <div class="cl-wrap">
            <div class="cl">

                <!-- SIDEBAR (RIGHT in RTL — first in DOM) -->
                <aside class="cl-side">
                    <div class="cl-top">
                        <div class="cl-av">{{ initials }}</div>
                        <div class="cl-name">{{ user?.name }}</div>
                        <span class="cl-tag" :class="user?.account_type === 'company' ? 'co' : 'rt'">
                            {{ user?.account_type === 'company' ? 'حساب شركة' : 'حساب فردي' }}
                        </span>
                    </div>

                    <div class="cl-nav">
                        <Link
                            v-for="n in nav" :key="n.r"
                            :href="route(n.r)"
                            class="cl-lnk"
                            :class="{ on: isOn(n) }"
                        >
                            <span class="cl-ico">{{ n.icon }}</span>
                            <span>{{ n.label }}</span>
                        </Link>
                    </div>

                    <button class="cl-out" @click="logout">
                        <span>🚪</span> تسجيل الخروج
                    </button>
                </aside>

                <!-- MAIN (LEFT in RTL — second in DOM) -->
                <div class="cl-main">
                    <slot />
                </div>

            </div>
        </div>
    </StoreLayout>
</template>

<style scoped>
/* ── Outer wrapper — pushes ALL content below the fixed nav (~100px) ── */
.cl-wrap {
    padding-top: 120px;
    padding-bottom: 60px;
}

/* ── Inner flex row ── */
.cl {
    display: flex;
    align-items: flex-start;
    gap: 24px;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* ── Main content (LEFT in RTL = second child) ── */
.cl-main {
    flex: 1;
    min-width: 0;
}

/* ── Sidebar (RIGHT in RTL = first child) ── */
.cl-side {
    width: 230px;
    flex-shrink: 0;
    border: 1.5px solid var(--hair);
    border-radius: 20px;
    background: var(--bg2);
    display: flex;
    flex-direction: column;
    /* NO position:sticky — causes RTL paint bugs */
}

/* ── Avatar section ── */
.cl-top {
    padding: 22px 16px 18px;
    text-align: center;
    border-bottom: 1px solid var(--hair);
    border-radius: 18px 18px 0 0;
    background: linear-gradient(160deg, rgba(52,215,127,.07) 0%, transparent 70%);
}
.cl-av {
    width: 58px; height: 58px; border-radius: 50%;
    background: var(--emerald-deep); color: var(--on-emerald);
    font-size: 22px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 12px;
    box-shadow: 0 4px 16px rgba(52,215,127,.35);
}
.cl-name { font-size: 14px; font-weight: 700; margin-bottom: 8px; }
.cl-tag {
    display: inline-block; padding: 3px 12px;
    border-radius: 999px; font-size: 11px; font-weight: 600;
}
.cl-tag.rt { background: rgba(52,215,127,.1);  color: var(--emerald-soft); }
.cl-tag.co { background: rgba(33,150,243,.1);  color: #42a5f5; }

/* ── Nav links ── */
.cl-nav {
    padding: 10px 8px;
    display: flex; flex-direction: column; gap: 3px;
    flex: 1;
}
.cl-lnk {
    display: flex; align-items: center; gap: 10px;
    padding: 11px 13px; border-radius: 12px;
    text-decoration: none; color: var(--muted);
    font-size: 14px; font-weight: 500;
    transition: background .15s, color .15s;
    cursor: pointer;
    position: relative;
    z-index: 1;
}
.cl-lnk:hover { background: var(--glass); color: var(--ink); }
.cl-lnk.on    { background: rgba(52,215,127,.1); color: var(--emerald-soft); font-weight: 700; }
.cl-ico       { font-size: 17px; flex-shrink: 0; }

/* ── Logout ── */
.cl-out {
    width: 100%; padding: 13px 18px;
    border: none; border-top: 1px solid var(--hair);
    background: none; color: #ff7a6b;
    font-size: 13px; cursor: pointer;
    font-family: inherit;
    display: flex; align-items: center; gap: 9px;
    border-radius: 0 0 18px 18px;
    transition: background .15s;
    text-align: right;
    position: relative;
    z-index: 1;
}
.cl-out:hover { background: rgba(255,122,107,.06); }

/* ── Responsive ── */
@media (max-width: 880px) {
    .cl-wrap { padding-top: 110px; padding-bottom: 40px; }
    .cl {
        flex-direction: column;
        padding: 0 14px;
        gap: 16px;
    }
    .cl-side {
        width: 100%;
        flex-direction: row;
        align-items: center;
        border-radius: 16px;
    }
    .cl-top {
        display: flex; align-items: center; gap: 13px;
        text-align: right; padding: 12px 16px;
        flex-shrink: 0;
        background: none;
        border-radius: 16px 0 0 16px;
        border-bottom: none;
        border-left: 1px solid var(--hair);
    }
    .cl-av    { width: 40px; height: 40px; font-size: 15px; margin: 0; }
    .cl-name  { font-size: 13px; margin-bottom: 4px; }
    .cl-tag   { font-size: 10px; padding: 2px 8px; }
    .cl-nav   { flex-direction: row; flex-wrap: wrap; padding: 8px; gap: 4px; flex: 1; }
    .cl-lnk   { padding: 7px 11px; font-size: 12px; gap: 6px; }
    .cl-ico   { font-size: 14px; }
    .cl-out   { display: none; }
}
</style>
