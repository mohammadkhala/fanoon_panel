<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch, nextTick } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const cartCount = computed(() => page.props.cartCount ?? 0);
const settings = computed(() => page.props.settings ?? {});
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

const isDark = ref(false);
const socialOpen = ref(false);

/** Build a WhatsApp URL from the store phone setting */
const whatsappUrl = computed(() => {
    const raw = settings.value.phone ?? '';
    const digits = raw.replace(/\D/g, '');
    return digits ? `https://wa.me/${digits}` : 'https://wa.me/970599814758';
});

function applyTheme() {
    if (isDark.value) {
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('elite-theme', 'dark');
    } else {
        document.documentElement.removeAttribute('data-theme');
        localStorage.setItem('elite-theme', 'light');
    }
}
function toggleTheme() {
    isDark.value = !isDark.value;
    applyTheme();
}

function goCart() {
    router.visit(user.value ? route('cart.index') : route('login'));
}

// Scroll reveal
function initReveal() {
    const io = new IntersectionObserver(
        (entries) => entries.forEach((e) => { if (e.isIntersecting) e.target.classList.add('in'); }),
        { threshold: 0.14 }
    );
    document.querySelectorAll('.rv').forEach((el) => io.observe(el));
}

onMounted(() => {
    isDark.value = localStorage.getItem('elite-theme') === 'dark';
    nextTick(initReveal);
});

// Re-run reveal on Inertia navigation
watch(() => page.component, () => nextTick(initReveal));
</script>

<template>
    <div>
        <div class="mesh"></div>
        <div class="grain"></div>

        <!-- NAV -->
        <nav>
            <div class="nav-pill">
                <Link :href="route('home')" class="logo"><img src="/logo.png" alt="Elite" class="logo-img"></Link>
                <div class="nlinks">
                    <Link :href="route('home')">الرئيسية</Link>
                    <Link :href="route('shop')">المتجر</Link>
                    <Link :href="route('track')">تتبع طلبي</Link>
                    <Link :href="route('about')">من نحن</Link>
                    <Link :href="route('contact')">اتصل بنا</Link>
                </div>
                <div class="nav-cta">
                    <template v-if="user">
                        <Link v-if="user.is_admin" :href="route('admin.dashboard')" class="pill-btn pill-ghost">لوحة الإدارة</Link>
                        <Link :href="route('dashboard')" class="pill-btn pill-ghost">حسابي</Link>
                        <Link :href="route('logout')" method="post" as="button" class="pill-btn pill-fill">خروج</Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="pill-btn pill-ghost">دخول</Link>
                        <Link :href="route('register')" class="pill-btn pill-fill">حساب جديد<span class="bib">←</span></Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- flash -->
        <transition name="fade">
            <div v-if="flashSuccess" class="flash flash-ok">{{ flashSuccess }}</div>
        </transition>
        <transition name="fade">
            <div v-if="flashError" class="flash flash-err">{{ flashError }}</div>
        </transition>

        <slot />

        <!-- FOOTER -->
        <div class="wrap">
            <footer>
                <div class="fg">
                    <div class="fb">
                        <div class="logo"><img src="/logo.png" alt="Elite" class="logo-img logo-img-lg"></div>
                        <p>متخصصون في تصنيع الدروع والهدايا التذكارية الفاخرة. نحوّل أفكارك إلى قطع فنية خالدة بأدق التفاصيل.</p>
                        <div class="fsocial">
                            <a v-if="settings.instagram" :href="settings.instagram" target="_blank" rel="noopener">Instagram</a>
                            <a v-if="settings.facebook" :href="settings.facebook" target="_blank" rel="noopener">Facebook</a>
                            <a v-if="settings.tiktok" :href="settings.tiktok" target="_blank" rel="noopener">TikTok</a>
                        </div>
                    </div>
                    <div class="fc">
                        <h4>روابط</h4>
                        <Link :href="route('home')">الرئيسية</Link>
                        <Link :href="route('shop')">المتجر</Link>
                    </div>
                    <div class="fc">
                        <h4>الشركة</h4>
                        <Link :href="route('about')">من نحن</Link>
                        <Link :href="route('faq')">الأسئلة الشائعة</Link>
                        <Link :href="route('contact')">اتصل بنا</Link>
                    </div>
                    <div class="fc">
                        <h4>تواصل</h4>
                        <div class="ci"><span class="ico">📞</span> {{ settings.phone }}</div>
                        <div class="ci"><span class="ico">📧</span> {{ settings.email }}</div>
                        <div class="ci"><span class="ico">📍</span> {{ settings.address }}</div>
                        <div class="ci"><span class="ico">🕐</span> {{ settings.working_hours }}</div>
                    </div>
                </div>
                <div class="fbot">
                    <div>© 2024 {{ settings.store_name }} — جميع الحقوق محفوظة</div>
                    <div style="display:flex;gap:20px"><a href="#">الخصوصية</a><a href="#">الشروط</a></div>
                </div>
                <div class="fattr">
                    برمجة وتصميم
                    <a href="https://wa.me/970599814758" target="_blank" rel="noopener" class="fattr-link">
                        🏠 بيت البرمجيات وتكنولوجيا المعلومات
                    </a>
                </div>
            </footer>
        </div>

        <!-- FLOATING DOCK (top-left: theme + cart) -->
        <div class="fab-dock">
            <button class="fab-btn" @click="toggleTheme" :title="isDark ? 'الوضع النهاري' : 'الوضع الليلي'">{{ isDark ? '☀️' : '🌙' }}</button>
            <button class="fab-btn" @click="goCart" title="السلة">🛒<span class="cart-count">{{ cartCount }}</span></button>
        </div>

        <!-- SOCIAL SPEED-DIAL (bottom-left) -->
        <div class="soc-dock" :class="{ open: socialOpen }">
            <!-- sub-buttons (visible when open) -->
            <transition-group name="soc-pop" tag="div" class="soc-items">
                <a v-if="socialOpen"
                   key="wa" :href="whatsappUrl" target="_blank" rel="noopener"
                   class="soc-btn wa" title="واتساب">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </a>
                <a v-if="socialOpen && settings.instagram"
                   key="ig" :href="settings.instagram" target="_blank" rel="noopener"
                   class="soc-btn ig" title="إنستغرام">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                    </svg>
                </a>
                <a v-if="socialOpen && settings.facebook"
                   key="fb" :href="settings.facebook" target="_blank" rel="noopener"
                   class="soc-btn fb" title="فيسبوك">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>
                <a v-if="socialOpen && settings.tiktok"
                   key="tt" :href="settings.tiktok" target="_blank" rel="noopener"
                   class="soc-btn tt" title="تيك توك">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                        <path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.27 8.27 0 004.83 1.55V6.79a4.85 4.85 0 01-1.06-.1z"/>
                    </svg>
                </a>
            </transition-group>

            <!-- main toggle button -->
            <button class="soc-main" @click="socialOpen = !socialOpen" :title="socialOpen ? 'إغلاق' : 'تواصل معنا'">
                <svg v-if="!socialOpen" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                <svg v-else viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
    </div>
</template>

<style scoped>
.flash{position:fixed;top:26px;right:50%;transform:translateX(50%);z-index:300;padding:12px 24px;border-radius:999px;font-weight:600;font-size:14px;box-shadow:0 18px 50px var(--shadow)}
.flash-ok{background:var(--emerald);color:var(--on-emerald)}
.flash-err{background:#e74c3c;color:#fff}
.fade-enter-active,.fade-leave-active{transition:opacity .4s var(--ease)}
.fade-enter-from,.fade-leave-to{opacity:0}
.fsocial{display:flex;gap:14px;margin-top:14px}
.fsocial a{color:var(--muted);font-size:13px;text-decoration:none;transition:color .3s var(--ease)}
.fsocial a:hover{color:var(--emerald)}
.fattr{text-align:center;padding:12px 0 4px;font-size:12px;color:var(--muted);border-top:1px solid var(--hair);margin-top:2px}
.fattr-link{color:var(--emerald-soft);text-decoration:none;font-weight:600;transition:color .3s var(--ease)}
.fattr-link:hover{color:var(--emerald)}

/* ── Social speed-dial ── */
.soc-dock{position:fixed;bottom:24px;left:24px;z-index:300;display:flex;flex-direction:column;align-items:center;gap:10px}
.soc-items{display:flex;flex-direction:column;align-items:center;gap:10px}
.soc-main{width:54px;height:54px;border-radius:999px;background:linear-gradient(150deg,#25d366,#128c7e);color:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 28px rgba(18,140,126,.45);transition:all .3s var(--ease)}
.soc-main:hover{transform:translateY(-2px) scale(1.06);box-shadow:0 14px 36px rgba(18,140,126,.55)}
.soc-btn{width:44px;height:44px;border-radius:999px;display:flex;align-items:center;justify-content:center;color:#fff;text-decoration:none;box-shadow:0 6px 20px rgba(0,0,0,.2);transition:all .3s var(--ease)}
.soc-btn:hover{transform:scale(1.12)}
.soc-btn.wa{background:linear-gradient(150deg,#25d366,#128c7e)}
.soc-btn.ig{background:linear-gradient(150deg,#f58529,#dd2a7b,#8134af,#515bd4)}
.soc-btn.fb{background:#1877f2}
.soc-btn.tt{background:#010101}
/* pop animation */
.soc-pop-enter-active{transition:all .25s var(--ease)}
.soc-pop-leave-active{transition:all .18s var(--ease)}
.soc-pop-enter-from{opacity:0;transform:translateY(16px) scale(.7)}
.soc-pop-leave-to{opacity:0;transform:translateY(10px) scale(.8)}
@media(max-width:600px){.soc-dock{bottom:16px;left:14px}}
</style>
