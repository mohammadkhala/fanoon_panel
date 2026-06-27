<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StoreLayout from '@/Layouts/StoreLayout.vue';
</script>

<template>
    <Head title="الأسعار" />
    <StoreLayout>
        <div class="wrap">
            <div class="phead">
                <div class="crumb"><Link :href="route('home')">الرئيسية</Link><span class="sep">/</span><span>الأسعار</span></div>
                <span class="eyebrow rv">الأسعار</span>
                <h1 class="rv d1">أسعار شفافة للجميع</h1>
                <p class="rv d2">أسعار تنافسية للأفراد وخصومات مميّزة للشركات والكميات الكبيرة</p>
            </div>
        </div>
        <div class="wrap"><section style="padding-top:50px">
            <div class="pgrid">
                <div class="pcard rv"><div class="pcard-in"><div class="pic">👤</div><h3>الأفراد</h3><div class="amt">₪85</div><div class="per">لكل درع</div>
                    <ul class="pf"><li>تصميم مخصّص بالكامل</li><li>خامات عالية الجودة</li><li>تسليم 5–7 أيام</li><li>مراجعة تصميم واحدة</li></ul>
                    <Link class="pbtn pbtn-o" :href="route('register')">ابدأ الآن</Link></div></div>
                <div class="pcard feat rv d1"><div class="flbl">الأوفر</div><div class="pcard-in"><div class="pic">🏢</div><h3>الشركات</h3><div class="amt">₪55</div><div class="per">لكل درع (من 10 قطع)</div>
                    <ul class="pf"><li>خصم الكميات تلقائي</li><li>مراجعات غير محدودة</li><li>تسليم 3–5 أيام</li><li>مدير حساب مخصّص</li><li>فاتورة ضريبية</li></ul>
                    <Link class="pbtn pbtn-f" :href="route('register')">سجّل كشركة</Link></div></div>
                <div class="pcard rv d2"><div class="pcard-in"><div class="pic">🎯</div><h3>طلبات خاصة</h3><div class="amt">تواصل</div><div class="per">أسعار مخصّصة</div>
                    <ul class="pf"><li>كميات كبيرة (+100)</li><li>تصاميم فريدة</li><li>خامات فاخرة</li><li>أولوية في التنفيذ</li></ul>
                    <Link class="pbtn pbtn-o" :href="route('contact')" style="display:flex;align-items:center;justify-content:center;text-decoration:none">تواصل معنا</Link></div></div>
            </div>
            <div class="center-cta"><Link class="big-btn big-ghost" :href="route('faq')">لديك أسئلة؟ تصفّح الأسئلة الشائعة</Link></div>
        </section></div>
    </StoreLayout>
</template>
