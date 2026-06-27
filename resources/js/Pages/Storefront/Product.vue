<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import StoreLayout from '@/Layouts/StoreLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({ product: Object, authed: Boolean });

const selected = ref(props.product.templates[0]?.id ?? null);
const selectedTemplate = computed(() => props.product.templates.find(t => t.id === selected.value));

const form = useForm({ product_template_id: null });

function proceed() {
    if (!selected.value) return;
    if (!props.authed) {
        window.location.href = route('login');
        return;
    }
    // → Canva external editor (or upload directly if no Canva URL)
    window.location.href = route('canva.start', selected.value);
}
</script>

<template>
    <Head :title="product.name" />
    <StoreLayout>
        <div class="wrap">
            <!-- Breadcrumb -->
            <div class="crumb rv">
                <Link :href="route('home')">الرئيسية</Link>
                <span class="sep">/</span>
                <Link :href="route('categories')">التصنيفات</Link>
                <span class="sep">/</span>
                <Link :href="route('category.show', product.category.slug)">{{ product.category.name }}</Link>
                <span class="sep">/</span>
                <Link :href="route('subcategory.show', { category: product.category.slug, subcategory: product.subcategory.slug })">{{ product.subcategory.name }}</Link>
                <span class="sep">/</span>
                <span>{{ product.name }}</span>
            </div>

            <div class="layout">
                <!-- Left: info + template picker -->
                <div class="info-col">
                    <span v-if="product.badge" class="badge">{{ product.badge }}</span>
                    <h1 class="prod-name rv d1">{{ product.name }}</h1>
                    <p v-if="product.description" class="prod-desc rv d2">{{ product.description }}</p>

                    <div class="price-row">
                        <span class="price">{{ product.price }} ₪</span>
                        <span class="price-note">شامل التصميم والطباعة</span>
                    </div>

                    <!-- Sizes -->
                    <div v-if="product.sizes?.length" class="sizes-section">
                        <div class="sizes-label">📐 المقاسات المتاحة</div>
                        <div class="sizes-row">
                            <span v-for="s in product.sizes" :key="s" class="size-chip">{{ s }}</span>
                        </div>
                    </div>

                    <!-- Template picker -->
                    <div class="picker-section">
                        <h2 class="picker-title">اختر القالب</h2>
                        <p class="picker-sub">اختر قالباً وخصّصه، أو ارفع تصميمك الجاهز مباشرة</p>

                        <div class="templates-row">
                            <button v-for="t in product.templates" :key="t.id"
                                    class="tmpl-btn" :class="{ active: selected === t.id }"
                                    @click="selected = t.id">
                                <div class="tmpl-img-wrap">
                                    <img v-if="t.preview_image" :src="'/storage/' + t.preview_image" :alt="t.name" class="tmpl-img">
                                    <div v-else class="tmpl-placeholder">🎨</div>
                                    <div v-if="selected === t.id" class="tmpl-check">✓</div>
                                </div>
                                <span class="tmpl-label">{{ t.name }}</span>
                                <span v-if="t.has_canva" class="canva-dot" title="متوفر في كانفا">🎨</span>
                            </button>
                        </div>

                        <div v-if="!product.templates.length" class="no-templates">
                            لا توجد قوالب لهذا المنتج بعد. تواصل معنا لمساعدتك.
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="cta-area">
                        <!-- Primary: design from template -->
                        <button v-if="product.templates.length"
                                class="cta-btn" :disabled="!selected" @click="proceed">
                            <span>{{ authed ? 'ابدأ التصميم من القالب' : 'سجّل دخولك للبدء' }}</span>
                            <span class="cta-icon">✏️ ←</span>
                        </button>

                        <!-- Divider -->
                        <div class="or-divider"><span>أو</span></div>

                        <!-- Secondary: upload ready design -->
                        <a v-if="authed"
                           :href="route('upload.page', product.slug)"
                           class="upload-btn">
                            📤 ارفع تصميمك الجاهز
                        </a>
                        <a v-else
                           :href="route('login')"
                           class="upload-btn">
                            📤 ارفع تصميمك الجاهز
                        </a>

                        <p v-if="!authed" class="auth-note">
                            <Link :href="route('login')" class="auth-link">تسجيل الدخول</Link>
                            أو
                            <Link :href="route('register')" class="auth-link">إنشاء حساب مجاني</Link>
                            للمتابعة
                        </p>
                    </div>
                </div>

                <!-- Right: preview of selected template -->
                <div class="preview-col rv d2">
                    <div class="preview-card">
                        <div v-if="selectedTemplate?.preview_image" class="preview-wrap">
                            <img :src="'/storage/' + selectedTemplate.preview_image" :alt="selectedTemplate.name" class="preview-img">
                        </div>
                        <div v-else class="preview-empty">
                            <div class="pe-icon">🖼</div>
                            <p>معاينة القالب</p>
                        </div>
                        <div v-if="selectedTemplate" class="preview-label">
                            <span class="pl-name">{{ selectedTemplate.name }}</span>
                            <span v-if="selectedTemplate.has_canva" class="pl-canva">متوفر في كانفا 🎨</span>
                        </div>
                    </div>

                    <div class="how-card">
                        <h3 class="how-title">كيف يعمل؟</h3>
                        <div class="steps">
                            <div class="step"><span class="step-n">1</span><span>اختر القالب أو ارفع تصميمك</span></div>
                            <div class="step"><span class="step-n">2</span><span>خصّص التصميم كما تريد</span></div>
                            <div class="step"><span class="step-n">3</span><span>أضفه للسلة وأكمل الطلب</span></div>
                            <div class="step"><span class="step-n">4</span><span>نطبع ونوصّل لبابك</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StoreLayout>
</template>

<style scoped>
.crumb { font-size: 13px; color: var(--muted); display: flex; align-items: center; gap: 6px; flex-wrap: wrap; padding: 28px 0 0; }
.crumb a { color: var(--muted); text-decoration: none; }
.crumb a:hover { color: var(--ink); }
.sep { opacity: .4; }

.layout { display: grid; grid-template-columns: 1fr 420px; gap: 40px; align-items: start; padding: 32px 0 60px; }
@media (max-width: 900px) { .layout { grid-template-columns: 1fr; } .preview-col { order: -1; } }

.info-col { display: flex; flex-direction: column; gap: 20px; }

.badge { display: inline-flex; background: linear-gradient(150deg, var(--emerald-soft), var(--emerald-deep)); color: var(--on-emerald); border-radius: 999px; padding: 5px 16px; font-size: 12px; font-weight: 600; align-self: flex-start; }
.prod-name { font-size: 32px; font-weight: 800; line-height: 1.2; margin: 0; letter-spacing: -.5px; }
.prod-desc { font-size: 15px; color: var(--muted); margin: 0; line-height: 1.7; }

.price-row { display: flex; align-items: baseline; gap: 12px; }
.price { font-size: 28px; font-weight: 800; color: var(--emerald-soft); }
.price-note { font-size: 13px; color: var(--muted); }

.sizes-section { background: var(--bg2); border: 1px solid var(--hair); border-radius: 14px; padding: 14px 18px; }
.sizes-label { font-size: 13px; font-weight: 600; color: var(--muted); margin-bottom: 10px; }
.sizes-row { display: flex; gap: 8px; flex-wrap: wrap; }
.size-chip { background: var(--glass); border: 1.5px solid var(--hair); border-radius: 8px; padding: 5px 12px; font-size: 13px; font-weight: 600; color: var(--ink); }

.picker-section { background: var(--bg2); border: 1px solid var(--hair); border-radius: 20px; padding: 22px; }
.picker-title { font-size: 17px; font-weight: 700; margin: 0 0 4px; }
.picker-sub { font-size: 13px; color: var(--muted); margin: 0 0 16px; }

.templates-row { display: flex; gap: 12px; flex-wrap: wrap; }
.tmpl-btn { border: 2px solid var(--hair); border-radius: 16px; background: var(--bg); cursor: pointer; padding: 0; overflow: hidden; transition: all .3s; display: flex; flex-direction: column; width: calc(33.333% - 8px); min-width: 90px; }
.tmpl-btn:hover { border-color: rgba(52,215,127,.4); transform: translateY(-2px); }
.tmpl-btn.active { border-color: var(--emerald-soft); box-shadow: 0 0 0 3px rgba(52,215,127,.15); }
.tmpl-img-wrap { aspect-ratio: 1; overflow: hidden; background: var(--glass); position: relative; }
.tmpl-img { width: 100%; height: 100%; object-fit: cover; }
.tmpl-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 28px; opacity: .3; }
.tmpl-check { position: absolute; inset: 0; background: rgba(16,180,106,.2); display: flex; align-items: center; justify-content: center; font-size: 22px; color: var(--emerald-soft); font-weight: 700; }
.tmpl-label { font-size: 12px; font-weight: 600; padding: 8px 10px 6px; text-align: center; color: var(--ink); }
.canva-dot { font-size: 11px; padding: 0 10px 8px; text-align: center; }
.no-templates { text-align: center; color: var(--muted); padding: 20px; font-size: 13px; }

.cta-area { display: flex; flex-direction: column; gap: 10px; }
.cta-btn { width: 100%; display: flex; align-items: center; justify-content: center; gap: 14px; background: linear-gradient(150deg, var(--emerald-soft), var(--emerald-deep)); color: var(--on-emerald); border: none; border-radius: 18px; padding: 18px 24px; font-size: 16px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all .3s; box-shadow: 0 8px 28px rgba(52,215,127,.25); }
.cta-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 14px 40px rgba(52,215,127,.35); }
.cta-btn:disabled { opacity: .45; cursor: default; transform: none; box-shadow: none; }
.cta-icon { font-size: 18px; }

.or-divider { display: flex; align-items: center; gap: 12px; color: var(--muted); font-size: 13px; }
.or-divider::before, .or-divider::after { content: ''; flex: 1; height: 1px; background: var(--hair); }

.upload-btn { width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px; background: var(--bg2); color: var(--ink); border: 1.5px solid var(--hair); border-radius: 18px; padding: 15px 24px; font-size: 15px; font-weight: 600; cursor: pointer; font-family: inherit; transition: all .3s; text-decoration: none; }
.upload-btn:hover { border-color: var(--emerald-soft); color: var(--emerald-soft); background: rgba(52,215,127,.04); transform: translateY(-1px); }

.auth-note { font-size: 13px; color: var(--muted); text-align: center; }
.auth-link { color: var(--emerald-soft); text-decoration: none; font-weight: 600; }

/* preview column */
.preview-card { background: var(--bg2); border: 1px solid var(--hair); border-radius: 20px; overflow: hidden; }
.preview-wrap { aspect-ratio: 1; overflow: hidden; }
.preview-img { width: 100%; height: 100%; object-fit: cover; }
.preview-empty { aspect-ratio: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; background: var(--glass); color: var(--muted); }
.pe-icon { font-size: 48px; opacity: .3; }
.preview-empty p { font-size: 14px; }
.preview-label { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-top: 1px solid var(--hair); }
.pl-name { font-size: 14px; font-weight: 600; }
.pl-canva { font-size: 12px; color: var(--emerald-soft); }

.how-card { background: var(--bg2); border: 1px solid var(--hair); border-radius: 20px; padding: 20px; margin-top: 16px; }
.how-title { font-size: 15px; font-weight: 700; margin: 0 0 14px; }
.steps { display: flex; flex-direction: column; gap: 10px; }
.step { display: flex; align-items: center; gap: 12px; font-size: 13px; color: var(--muted); }
.step-n { width: 24px; height: 24px; border-radius: 50%; background: rgba(52,215,127,.12); border: 1px solid rgba(52,215,127,.25); color: var(--emerald-soft); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
</style>
