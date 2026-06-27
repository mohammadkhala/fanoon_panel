<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import StoreLayout from '@/Layouts/StoreLayout.vue';
import { ref } from 'vue';

// Static demo form (no backend endpoint yet — shows confirmation locally)
const form = useForm({ name: '', email: '', phone: '', subject: '', message: '' });
const sent = ref(false);
function submit() { sent.value = true; form.reset(); }
</script>

<template>
    <Head title="اتصل بنا" />
    <StoreLayout>
        <div class="wrap">
            <div class="phead">
                <div class="crumb"><Link :href="route('home')">الرئيسية</Link><span class="sep">/</span><span>اتصل بنا</span></div>
                <span class="eyebrow rv">اتصل بنا</span>
                <h1 class="rv d1">نحن هنا لمساعدتك</h1>
                <p class="rv d2">تواصل معنا لأي استفسار أو طلب خاص — فريقنا جاهز للرد</p>
            </div>
        </div>

        <div class="wrap"><section style="padding-top:50px">
            <div class="cgrid">
                <div class="cinfo rv">
                    <a class="ccard" href="tel:0598500721"><div class="cicon">📞</div><div><div class="cl">اتصل بنا مباشرة</div><div class="cv">0598500721</div></div></a>
                    <a class="ccard" href="mailto:Eliteagency2024@gmail.com"><div class="cicon">📧</div><div><div class="cl">البريد الإلكتروني</div><div class="cv">Eliteagency2024@gmail.com</div></div></a>
                    <div class="ccard"><div class="cicon">📍</div><div><div class="cl">موقعنا</div><div class="cv">الخليل — الحاووز الأول</div></div></div>
                    <div class="ccard"><div class="cicon">🕐</div><div><div class="cl">ساعات العمل</div><div class="cv">السبت – الخميس · 9ص – 7م</div></div></div>
                    <a class="ccard" href="https://wa.me/970598500721"><div class="cicon">💬</div><div><div class="cl">واتساب</div><div class="cv">راسلنا على واتساب</div></div></a>
                </div>
                <div class="cformwrap rv d1"><div class="cform">
                    <h3>أرسل لنا رسالة</h3>
                    <p class="sub">سنردّ عليك في أقرب وقت ممكن</p>
                    <div v-if="sent" class="note" style="display:block;margin-bottom:16px">✓ تم استلام رسالتك، سنتواصل معك قريباً.</div>
                    <div class="fgrp"><label>الاسم الكامل</label><input v-model="form.name" placeholder="محمد أحمد"></div>
                    <div class="frow">
                        <div class="fgrp" style="flex:1"><label>البريد الإلكتروني</label><input v-model="form.email" type="email" placeholder="example@email.com"></div>
                        <div class="fgrp" style="flex:1"><label>رقم الهاتف</label><input v-model="form.phone" placeholder="05XXXXXXXX"></div>
                    </div>
                    <div class="fgrp"><label>الموضوع</label><input v-model="form.subject" placeholder="طلب خاص / استفسار"></div>
                    <div class="fgrp"><label>رسالتك</label><textarea v-model="form.message" placeholder="اكتب رسالتك هنا..."></textarea></div>
                    <div class="mfoot"><span style="color:var(--muted);font-size:13px;font-weight:300">سنتواصل معك خلال 24 ساعة</span><button class="bsub" @click="submit">إرسال الرسالة<span class="bib">←</span></button></div>
                </div></div>
            </div>
            <div class="map rv"><div class="pin"><div class="big">📍</div><div style="font-weight:600;margin-top:8px">الخليل — الحاووز الأول</div><div style="color:var(--muted);font-size:13px;font-weight:300;margin-top:4px">Elite للدعاية والإعلان</div></div></div>
        </section></div>
    </StoreLayout>
</template>
