<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StoreLayout from '@/Layouts/StoreLayout.vue';

defineProps({ categories: Array });
</script>

<template>
    <Head title="مطبعة إيليت — تصميم وطباعة احترافية" />
    <StoreLayout>
        <!-- HERO -->
        <div class="wrap"><section class="hero" id="s0">
            <div>
                <span class="eyebrow rv">تصميم وطباعة احترافية</span>
                <h1 class="rv d1">صمّم مطبوعاتك<br>وحوّلها إلى <span class="grad">واقع مبهر</span></h1>
                <p class="rv d2">من الدروع التذكارية إلى البطاقات والشوادر — خصّص تصميمك، نطبع ونوصّل لبابك بأرقى الخامات وأدق التفاصيل.</p>
                <div class="hero-cta rv d3">
                    <Link class="big-btn big-fill" :href="route('categories')">تصفّح التصنيفات<span class="bib">←</span></Link>
                    <Link class="big-btn big-ghost" :href="route('register')">أنشئ حسابك مجاناً</Link>
                </div>
                <div class="trust rv d3">
                    <div><div class="n grad">+1000</div><div class="l">طلب منجز</div></div>
                    <div><div class="n grad">+120</div><div class="l">شركة عميلة</div></div>
                    <div><div class="n grad">4.9★</div><div class="l">تقييم العملاء</div></div>
                </div>
            </div>
            <div class="stage rv d2">
                <div class="tray">
                    <div class="core">
                        <div class="orb"></div>
                        <div class="shield3d"><div class="shield"><div class="plate">
                            <div class="c">🖨️</div><div class="t1">مطبعة إيليت</div><div class="t2">جودة لا تُضاهى</div><div class="t3">2024</div>
                        </div></div></div>
                    </div>
                    <div class="chip chip1"><div class="l">الطلبات هذا الشهر</div><div class="v">+120 طلب</div></div>
                    <div class="chip chip2"><div class="l">وقت التسليم</div><div class="v">3–5 أيام</div></div>
                </div>
            </div>
        </section></div>

        <!-- CATEGORIES -->
        <div class="wrap"><section id="s2" style="padding-top:70px">
            <div class="shead">
                <span class="eyebrow rv">تصنيفاتنا</span>
                <h2 class="rv d1">ماذا تريد أن <span class="grad">تطبع؟</span></h2>
                <p class="rv d2">اختر من بين ثمانية تصنيفات رئيسية — لكل منتج قوالب جاهزة تعدّلها كما تريد</p>
            </div>

            <div class="cat-grid">
                <Link
                    v-for="(cat, i) in categories"
                    :key="cat.id"
                    :href="route('category.show', cat.slug)"
                    class="cat-card rv"
                    :class="'d' + (i % 3)"
                >
                    <div class="cat-icon">{{ cat.icon }}</div>
                    <div class="cat-name">{{ cat.name }}</div>
                    <div v-if="cat.description" class="cat-desc">{{ cat.description }}</div>
                    <div class="cat-arrow">←</div>
                </Link>
            </div>

            <div class="center-cta rv">
                <Link class="big-btn big-fill" :href="route('categories')" style="margin:0 auto">
                    عرض جميع التصنيفات<span class="bib">←</span>
                </Link>
            </div>
        </section></div>

        <!-- HOW -->
        <div class="wrap"><section id="s1">
            <div class="shead">
                <span class="eyebrow rv">كيف يعمل</span>
                <h2 class="rv d1">من الفكرة إلى يديك في أربع خطوات</h2>
                <p class="rv d2">عملية سلسة ومدروسة بعناية للحصول على مطبوعاتك المخصّصة بالكامل</p>
            </div>
            <div class="bento">
                <div class="cell rv"><div class="cell-in"><div class="stepnum">١</div><h3>اختر المنتج</h3><p>تصفّح التصنيفات واختر المنتج الذي يناسب احتياجك</p></div></div>
                <div class="cell rv d1"><div class="cell-in"><div class="stepnum">٢</div><h3>خصّص تصميمك</h3><p>اختر قالباً وعدّل النصوص والألوان والشعارات كما تريد</p></div></div>
                <div class="cell rv d2"><div class="cell-in"><div class="stepnum">٣</div><h3>ارفع ملفك</h3><p>حمّل ملفك النهائي وأضفه إلى سلة مشترياتك بنقرة واحدة</p></div></div>
                <div class="cell rv d3"><div class="cell-in"><div class="stepnum">٤</div><h3>استلم طلبك</h3><p>نطبع بجودة عالية ونوصّل مباشرة إلى بابك</p></div></div>
            </div>
        </section></div>

        <!-- CTA -->
        <div class="wrap"><section style="padding-top:0">
            <div class="ctastrip rv">
                <span class="eyebrow">ابدأ اليوم</span>
                <h2>جاهز لطباعة تصميمك؟</h2>
                <p>انضم إلى أكثر من 120 شركة تثق بنا في تكريم إنجازاتها وترويج علامتها التجارية</p>
                <Link class="big-btn big-fill" :href="route('register')" style="margin:0 auto">أنشئ حسابك مجاناً<span class="bib">←</span></Link>
            </div>
        </section></div>
    </StoreLayout>
</template>

<style scoped>
.cat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 48px;
}

@media (max-width: 1024px) { .cat-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 700px)  { .cat-grid { grid-template-columns: repeat(2, 1fr); } }

.cat-card {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: var(--bg2);
    border: 1px solid var(--hair);
    border-radius: 22px;
    padding: 28px 22px 22px;
    text-decoration: none;
    color: var(--ink);
    transition: all .35s var(--ease);
    overflow: hidden;
}
.cat-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 50% 0, rgba(52,215,127,.07), transparent 65%);
    opacity: 0;
    transition: opacity .35s;
}
.cat-card:hover {
    transform: translateY(-5px);
    border-color: rgba(52,215,127,.35);
    box-shadow: 0 18px 48px rgba(0,0,0,.1);
}
.cat-card:hover::before { opacity: 1; }

.cat-icon { font-size: 40px; line-height: 1; }
.cat-name { font-size: 16px; font-weight: 700; }
.cat-desc { font-size: 12px; color: var(--muted); line-height: 1.5; flex: 1; }
.cat-arrow {
    font-size: 18px;
    color: var(--emerald-soft);
    font-weight: 700;
    margin-top: 6px;
    transition: transform .3s;
}
.cat-card:hover .cat-arrow { transform: translateX(-4px); }

.center-cta { text-align: center; margin-top: 8px; }
</style>
