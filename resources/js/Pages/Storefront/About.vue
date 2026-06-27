<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StoreLayout from '@/Layouts/StoreLayout.vue';
</script>

<template>
    <Head title="من نحن" />
    <StoreLayout>
        <div class="wrap">
            <div class="phead">
                <div class="crumb"><Link :href="route('home')">الرئيسية</Link><span class="sep">/</span><span>من نحن</span></div>
                <span class="eyebrow rv">من نحن</span>
                <h1 class="rv d1">نصنع التميّز <span class="grad">منذ التفصيل الأول</span></h1>
                <p class="rv d2">Elite للدعاية والإعلان — بيت طباعة وتصميم في قلب الخليل، متخصّص في الدروع والهدايا التذكارية الفاخرة</p>
            </div>
        </div>

        <div class="wrap"><section style="padding-top:60px">
            <div class="story">
                <div class="rv">
                    <h2>قصتنا</h2>
                    <p>بدأت Elite من شغف بسيط: تحويل لحظات التكريم والإنجاز إلى قطع فنية تبقى للأبد. على مدار سنوات، صنعنا أكثر من 500 درع لمؤسسات وشركات وأفراد، كل واحد منها يحمل قصة ويستحق أدق التفاصيل.</p>
                    <p>نجمع بين الحرفية اليدوية وأحدث تقنيات الطباعة والقص بالليزر، لنقدّم لك منتجاً يليق بالمناسبة. واليوم، نضع بين يديك محرّراً ذكياً يتيح لك تصميم درعك بنفسك من الألف إلى الياء.</p>
                    <div class="trust" style="margin-top:40px">
                        <div><div class="n grad">+500</div><div class="l">درع منجز</div></div>
                        <div><div class="n grad">+120</div><div class="l">شركة عميلة</div></div>
                        <div><div class="n grad">8</div><div class="l">سنوات خبرة</div></div>
                    </div>
                </div>
                <div class="story-art rv d1"><div class="big-emoji">🏆</div></div>
            </div>
        </section></div>

        <div class="wrap" id="how"><section>
            <div class="shead">
                <span class="eyebrow rv">قيمنا</span>
                <h2 class="rv d1">ما الذي يميّزنا</h2>
                <p class="rv d2">ثلاث ركائز نبني عليها كل تصميم نصنعه</p>
            </div>
            <div class="vgrid">
                <div class="vcard rv"><div class="vcard-in"><div class="vic">💎</div><h3>جودة لا تُساوم</h3><p>نختار أرقى الخامات — الكريستال، الأكريليك، الخشب والمعدن — لنضمن منتجاً يدوم ويبهر.</p></div></div>
                <div class="vcard rv d1"><div class="vcard-in"><div class="vic">⚡</div><h3>سرعة في التنفيذ</h3><p>نسلّم طلبات الأفراد خلال 5–7 أيام، وطلبات الشركات خلال 3–5 أيام مع متابعة كاملة.</p></div></div>
                <div class="vcard rv d2"><div class="vcard-in"><div class="vic">🎨</div><h3>حرية إبداعية كاملة</h3><p>محرّرنا المدمج يمنحك التحكم الكامل بالنصوص والألوان والشعارات قبل أن نصنع تصميمك.</p></div></div>
            </div>
        </section></div>

        <div class="wrap"><section style="padding-top:0">
            <div class="ctastrip rv">
                <span class="eyebrow">ابدأ اليوم</span>
                <h2>جاهز لصناعة درعك الخاص؟</h2>
                <p>ابدأ التصميم الآن أو تواصل معنا لطلب خاص</p>
                <Link class="big-btn big-fill" :href="route('editor')" style="margin:0 auto">ابدأ التصميم<span class="bib">←</span></Link>
            </div>
        </section></div>
    </StoreLayout>
</template>
