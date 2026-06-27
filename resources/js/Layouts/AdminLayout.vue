<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

defineProps({ title: { type: String, default: '' }, subtitle: { type: String, default: '' } });

const page = usePage();
const user = computed(() => page.props.auth?.user);
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

const isDark = ref(true);
function applyTheme() {
    if (isDark.value) document.documentElement.setAttribute('data-theme', 'dark');
    else document.documentElement.removeAttribute('data-theme');
    localStorage.setItem('elite-theme', isDark.value ? 'dark' : 'light');
}
function toggleTheme() { isDark.value = !isDark.value; applyTheme(); }

const mobileOpen = ref(false);

function isActive(pattern) {
    try { return route().current(pattern); } catch (e) { return false; }
}

/* ── Role-based nav visibility ── */
const ROLE_PERMISSIONS = {
    orders_manager:    ['admin.dashboard', 'admin.orders.', 'admin.customers.', 'admin.zones.'],
    products_manager:  ['admin.dashboard', 'admin.products.', 'admin.templates.', 'admin.categories.', 'admin.subcategories.', 'admin.designs.'],
    general_supervisor:['admin.dashboard', 'admin.orders.index', 'admin.orders.show', 'admin.customers.index',
                        'admin.products.index', 'admin.products.edit', 'admin.templates.index',
                        'admin.categories.index', 'admin.designs.index',
                        'admin.reports.', 'admin.reviews.', 'admin.companies.'],
};

function canSeeRoute(routeName) {
    const role = user.value?.admin_role;
    if (!role || role === 'super_admin') return true;
    const allowed = ROLE_PERMISSIONS[role] ?? [];
    return allowed.some(entry =>
        entry.endsWith('.') ? routeName.startsWith(entry) : routeName === entry
    );
}

const allNavGroups = [
    {
        title: null,
        items: [
            { label: 'لوحة التحكم', icon: '◈', route: 'admin.dashboard', pattern: 'admin.dashboard' },
        ],
    },
    {
        title: 'المتجر',
        items: [
            { label: 'الطلبات',        icon: '🧾', route: 'admin.orders.index',    pattern: 'admin.orders.*' },
            { label: 'التصنيفات',      icon: '🗂', route: 'admin.categories.index', pattern: 'admin.categories.*' },
            { label: 'المنتجات',       icon: '📦', route: 'admin.products.index',   pattern: 'admin.products.*' },
            { label: 'القوالب',        icon: '▦',  route: 'admin.templates.index',  pattern: 'admin.templates.*' },
            { label: 'تصاميم الزبائن', icon: '🎨', route: 'admin.designs.index',    pattern: 'admin.designs.*' },
            { label: 'العملاء',        icon: '👥', route: 'admin.customers.index',  pattern: 'admin.customers.*' },
            { label: 'الشركات',        icon: '🏢', route: 'admin.companies.index',  pattern: 'admin.companies.*' },
        ],
    },
    {
        title: 'التقارير',
        items: [
            { label: 'تقارير المبيعات',     icon: '📊', route: 'admin.reports.sales',    pattern: 'admin.reports.sales' },
            { label: 'التقارير المالية',     icon: '💵', route: 'admin.reports.financial', pattern: 'admin.reports.financial' },
            { label: 'الزوار والإحصائيات', icon: '📈', route: 'admin.reports.visitors',  pattern: 'admin.reports.visitors' },
        ],
    },
    {
        title: 'الإعدادات',
        items: [
            { label: 'إعدادات الموقع', icon: '⚙', route: 'admin.settings.edit', pattern: 'admin.settings.*' },
            { label: 'مناطق التوصيل', icon: '📍', route: 'admin.zones.index',   pattern: 'admin.zones.*' },
            { label: 'المستخدمون',    icon: '🛡', route: 'admin.users.index',   pattern: 'admin.users.*' },
            { label: 'التقييمات',    icon: '⭐', route: 'admin.reviews.index',  pattern: 'admin.reviews.*' },
        ],
    },
];

// Only show groups/items the current user is allowed to see
const navGroups = computed(() =>
    allNavGroups
        .map(g => ({ ...g, items: g.items.filter(n => canSeeRoute(n.route)) }))
        .filter(g => g.items.length > 0)
);

onMounted(() => {
    isDark.value = localStorage.getItem('elite-theme') !== 'light';
    applyTheme();
});
</script>

<template>
    <div class="admin-shell">
        <!-- SIDEBAR -->
        <aside class="side" :class="{ open: mobileOpen }">
            <div class="brand">
                <img src="/logo.png" alt="Elite" class="brand-img">
            </div>
            <div class="side-tag">لوحة الإدارة</div>

            <nav class="snav">
                <template v-for="(g, gi) in navGroups" :key="gi">
                    <div v-if="g.title" class="sgroup">{{ g.title }}</div>
                    <Link v-for="n in g.items" :key="n.route" :href="route(n.route)" class="slink" :class="{ on: isActive(n.pattern) }" @click="mobileOpen = false">
                        <span class="sico">{{ n.icon }}</span>{{ n.label }}
                    </Link>
                </template>
            </nav>

            <div class="side-foot">
                <Link :href="route('home')" class="slink ghost"><span class="sico">↗</span>عرض المتجر</Link>
                <Link :href="route('logout')" method="post" as="button" class="slink ghost danger"><span class="sico">⎋</span>تسجيل الخروج</Link>
            </div>
        </aside>

        <div v-if="mobileOpen" class="scrim" @click="mobileOpen = false"></div>

        <!-- MAIN -->
        <div class="main">
            <header class="topbar">
                <div class="tb-l">
                    <button class="burger" @click="mobileOpen = !mobileOpen">☰</button>
                    <div>
                        <h1 class="tb-title">{{ title }}</h1>
                        <p v-if="subtitle" class="tb-sub">{{ subtitle }}</p>
                    </div>
                </div>
                <div class="tb-r">
                    <button class="ticon" @click="toggleTheme" :title="isDark ? 'الوضع النهاري' : 'الوضع الليلي'">{{ isDark ? '☀️' : '🌙' }}</button>
                    <div class="who">
                        <div class="avatar">{{ (user?.name || 'A').charAt(0) }}</div>
                        <div class="who-meta">
                            <div class="who-name">{{ user?.name }}</div>
                            <div class="who-role">{{ user?.admin_role_label || 'مدير النظام' }}</div>
                        </div>
                    </div>
                </div>
            </header>

            <transition name="fade">
                <div v-if="flashSuccess" class="flash ok">{{ flashSuccess }}</div>
            </transition>
            <transition name="fade">
                <div v-if="flashError" class="flash err">{{ flashError }}</div>
            </transition>

            <main class="content">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.admin-shell{display:flex;min-height:100vh;background:var(--bg);color:var(--ink)}

/* sidebar */
.side{width:248px;flex-shrink:0;background:var(--bg2);border-left:1px solid var(--hair);display:flex;flex-direction:column;padding:22px 16px;position:sticky;top:0;height:100vh}
.brand{padding:6px 8px 0}
.brand-img{height:38px;width:auto;display:block}
.side-tag{font-size:11px;color:var(--muted);letter-spacing:.18em;text-transform:uppercase;margin:14px 8px 20px}
.snav{position:static;inset:auto;z-index:auto;padding:0;pointer-events:auto;display:flex;flex-direction:column;justify-content:flex-start;gap:4px;flex:1;overflow-y:auto}
.sgroup{font-size:11px;color:var(--muted);letter-spacing:.12em;margin:14px 14px 6px;opacity:.7}
.slink{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:13px;color:var(--muted);text-decoration:none;font-size:14px;font-weight:500;border:1px solid transparent;background:none;cursor:pointer;font-family:inherit;text-align:right;width:100%;transition:all .3s var(--ease)}
.slink:hover{color:var(--ink);background:var(--glass)}
.slink.on{background:rgba(52,215,127,.12);color:var(--emerald-soft);border-color:rgba(52,215,127,.25)}
.sico{font-size:16px;width:22px;text-align:center}
.side-foot{display:flex;flex-direction:column;gap:6px;border-top:1px solid var(--hair);padding-top:14px;margin-top:14px}
.slink.ghost{font-weight:400;font-size:13px}
.slink.danger:hover{color:#ff7a6b;background:rgba(231,76,60,.08)}

.scrim{display:none}

/* main */
.main{flex:1;display:flex;flex-direction:column;min-width:0}
.topbar{position:sticky;top:0;z-index:20;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:16px 28px;background:color-mix(in srgb,var(--bg) 82%,transparent);backdrop-filter:blur(14px);border-bottom:1px solid var(--hair)}
.tb-l{display:flex;align-items:center;gap:14px}
.burger{display:none;background:var(--glass);border:1px solid var(--hair);color:var(--ink);border-radius:10px;width:38px;height:38px;font-size:17px;cursor:pointer}
.tb-title{font-size:20px;font-weight:700;line-height:1.2}
.tb-sub{font-size:13px;color:var(--muted);font-weight:300;margin-top:3px}
.tb-r{display:flex;align-items:center;gap:14px}
.ticon{background:var(--glass);border:1px solid var(--hair);color:var(--ink);border-radius:11px;width:40px;height:40px;font-size:16px;cursor:pointer;transition:all .3s var(--ease)}
.ticon:hover{border-color:var(--hair2)}
.who{display:flex;align-items:center;gap:10px}
.avatar{width:40px;height:40px;border-radius:12px;background:linear-gradient(150deg,var(--emerald-soft),var(--emerald-deep));color:var(--on-emerald);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px}
.who-name{font-size:14px;font-weight:600}
.who-role{font-size:12px;color:var(--muted)}

.content{padding:28px;flex:1}

.flash{margin:16px 28px 0;padding:13px 20px;border-radius:14px;font-weight:600;font-size:14px}
.flash.ok{background:rgba(52,215,127,.14);color:var(--emerald-soft);border:1px solid rgba(52,215,127,.3)}
.flash.err{background:rgba(231,76,60,.12);color:#ff7a6b;border:1px solid rgba(231,76,60,.3)}
.fade-enter-active,.fade-leave-active{transition:opacity .4s var(--ease)}
.fade-enter-from,.fade-leave-to{opacity:0}

@media(max-width:860px){
    .side{position:fixed;right:0;top:0;z-index:60;transform:translateX(100%);transition:transform .35s var(--ease)}
    .side.open{transform:none}
    .scrim{display:block;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:50}
    .burger{display:block}
    .who-meta{display:none}
    .content{padding:18px}
}
</style>
