<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StoreLayout from '@/Layouts/StoreLayout.vue';
import { ref } from 'vue';

const open = ref(null);
function toggle(i) { open.value = open.value === i ? null : i; }

const faqs = [
    { q: 'كيف أصمّم درعي الخاص؟', a: 'اختر قالباً من المتجر ثم اضغط "خصّص" لتنتقل إلى المحرّر المدمج، حيث يمكنك تعديل النصوص والألوان وإضافة الشعارات والصور. عند الانتهاء أرسل تصميمك وسنقوم بتصنيعه.' },
    { q: 'ما مدة التنفيذ والتسليم؟', a: 'طلبات الأفراد تُسلّم خلال 5–7 أيام عمل، وطلبات الشركات خلال 3–5 أيام. الطلبات الكبيرة قد تحتاج وقتاً إضافياً نُحدّده عند التأكيد.' },
    { q: 'هل تختلف الأسعار بين الأفراد والشركات؟', a: 'نعم. الأفراد يحصلون على أسعار التجزئة، بينما تحصل الشركات المسجّلة (بعد رفع السجل التجاري والموافقة) على أسعار الجملة وخصومات الكميات.' },
    { q: 'كيف أسجّل كشركة للحصول على أسعار الجملة؟', a: 'اضغط "حساب جديد" واختر "شركة"، ثم أدخل بيانات الشركة وارفع السجل التجاري. بعد مراجعة الإدارة والموافقة، ستظهر لك أسعار الشركات تلقائياً.' },
    { q: 'ما الخامات المتوفرة للدروع؟', a: 'نوفّر الكريستال، الأكريليك، الخشب، والمعدن، بأحجام وتشطيبات متعددة. يمكنك مناقشة خيارات خاصة عبر صفحة "اتصل بنا".' },
    { q: 'هل يمكنني طلب كمية كبيرة بتصميم موحّد؟', a: 'بالتأكيد. للطلبات الكبيرة (+100 قطعة) لدينا أسعار وخدمة مخصّصة — تواصل معنا وسيتابع معك مدير حساب مخصّص.' },
    { q: 'هل أستطيع تعديل التصميم بعد إرساله؟', a: 'نتواصل معك لتأكيد التصميم النهائي قبل التصنيع، ويمكنك إجراء التعديلات في تلك المرحلة. الأفراد لديهم مراجعة واحدة، والشركات مراجعات غير محدودة.' },
];
</script>

<template>
    <Head title="الأسئلة الشائعة" />
    <StoreLayout>
        <div class="wrap">
            <div class="phead">
                <div class="crumb"><Link :href="route('home')">الرئيسية</Link><span class="sep">/</span><span>الأسئلة الشائعة</span></div>
                <span class="eyebrow rv">الأسئلة الشائعة</span>
                <h1 class="rv d1">كل ما تريد معرفته</h1>
                <p class="rv d2">إجابات على أكثر الأسئلة شيوعاً حول خدماتنا</p>
            </div>
        </div>

        <div class="wrap"><section style="padding-top:50px">
            <div class="faq rv">
                <div v-for="(f, i) in faqs" :key="i" class="acc" :class="{ open: open === i }">
                    <div class="acc-q" @click="toggle(i)">{{ f.q }}<span class="pm">+</span></div>
                    <div class="acc-a" :style="{ maxHeight: open === i ? '300px' : '0' }"><p>{{ f.a }}</p></div>
                </div>
            </div>
            <div class="center-cta"><Link class="big-btn big-fill" :href="route('contact')" style="margin:0 auto">تواصل معنا<span class="bib">←</span></Link></div>
        </section></div>
    </StoreLayout>
</template>
