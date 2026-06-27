<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ settings: Object });

const s = props.settings;
const form = useForm({
    store_name: s.store_name ?? '',
    phone: s.phone ?? '',
    whatsapp: s.whatsapp ?? '',
    email: s.email ?? '',
    address: s.address ?? '',
    working_hours: s.working_hours ?? '',
    delivery_individual: s.delivery_individual ?? '',
    delivery_company: s.delivery_company ?? '',
    instagram: s.instagram ?? '',
    facebook: s.facebook ?? '',
    tiktok: s.tiktok ?? '',
    announcement: s.announcement ?? '',
    store_open: s.store_open === '1' || s.store_open === true,
});

function submit() {
    form.patch(route('admin.settings.update'), { preserveScroll: true });
}
</script>

<template>
    <Head title="إعدادات المتجر — الإدارة" />
    <AdminLayout title="إعدادات المتجر" subtitle="بيانات التواصل وأوقات التسليم والظهور العام">

        <form @submit.prevent="submit" class="cols">
            <section class="panel">
                <h3>الهوية والتواصل</h3>
                <div class="fgrp"><label>اسم المتجر</label><input v-model="form.store_name" required></div>
                <div class="frow">
                    <div class="fgrp"><label>الهاتف</label><input v-model="form.phone"></div>
                    <div class="fgrp"><label>واتساب (دولي بدون +)</label><input v-model="form.whatsapp" placeholder="970598500721"></div>
                </div>
                <div class="fgrp"><label>البريد الإلكتروني</label><input type="email" v-model="form.email"></div>
                <div class="fgrp"><label>العنوان</label><input v-model="form.address"></div>
                <div class="fgrp"><label>ساعات العمل</label><input v-model="form.working_hours"></div>
            </section>

            <section class="panel">
                <h3>التسليم والظهور</h3>
                <div class="frow">
                    <div class="fgrp"><label>تسليم الأفراد</label><input v-model="form.delivery_individual" placeholder="5–7 أيام"></div>
                    <div class="fgrp"><label>تسليم الشركات</label><input v-model="form.delivery_company" placeholder="3–5 أيام"></div>
                </div>
                <div class="fgrp"><label>شريط إعلان أعلى الموقع (اختياري)</label><input v-model="form.announcement" placeholder="عرض خاص هذا الأسبوع…"></div>
                <label class="toggle">
                    <input type="checkbox" v-model="form.store_open">
                    <span>المتجر مفتوح لاستقبال الطلبات</span>
                </label>

                <h3 style="margin-top:24px">روابط التواصل الاجتماعي</h3>
                <div class="fgrp"><label>Instagram</label><input v-model="form.instagram" class="lat" placeholder="https://instagram.com/…"></div>
                <div class="fgrp"><label>Facebook</label><input v-model="form.facebook" class="lat" placeholder="https://facebook.com/…"></div>
                <div class="fgrp"><label>TikTok</label><input v-model="form.tiktok" class="lat" placeholder="https://tiktok.com/@…"></div>
            </section>

            <div class="save">
                <button class="submit" :disabled="form.processing">
                    {{ form.processing ? 'جارٍ الحفظ…' : 'حفظ الإعدادات' }}<span class="bib">←</span>
                </button>
                <transition name="fade"><span v-if="form.recentlySuccessful" class="ok">✓ تم الحفظ</span></transition>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
.cols{display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start}
.panel{background:var(--bg2);border:1px solid var(--hair);border-radius:20px;padding:24px}
h3{font-size:15px;font-weight:700;margin-bottom:18px}
.fgrp{margin-bottom:15px}
.fgrp label{display:block;font-size:12px;color:var(--muted);margin-bottom:7px}
.fgrp input{width:100%;background:var(--glass);border:1px solid var(--hair);border-radius:11px;padding:11px 13px;color:var(--ink);font-family:inherit;font-size:14px;outline:none;transition:border .3s var(--ease)}
.fgrp input:focus{border-color:var(--emerald)}
.frow{display:flex;gap:14px}
.frow .fgrp{flex:1}
.toggle{display:flex;align-items:center;gap:10px;font-size:14px;cursor:pointer;background:var(--glass);border:1px solid var(--hair);border-radius:12px;padding:13px 15px}
.toggle input{accent-color:var(--emerald);width:18px;height:18px}
.save{grid-column:1 / -1;display:flex;align-items:center;gap:16px}
.submit{display:inline-flex;align-items:center;gap:10px;background:linear-gradient(150deg,var(--emerald-soft),var(--emerald-deep));color:var(--on-emerald);border:none;border-radius:13px;padding:14px 28px;font-size:15px;font-weight:600;cursor:pointer;font-family:inherit}
.submit:disabled{opacity:.6}
.bib{width:24px;height:24px;border-radius:999px;background:rgba(3,26,13,.2);display:flex;align-items:center;justify-content:center}
.ok{color:var(--emerald-soft);font-weight:600;font-size:14px}
.fade-enter-active,.fade-leave-active{transition:opacity .4s var(--ease)}
.fade-enter-from,.fade-leave-to{opacity:0}
@media(max-width:860px){.cols{grid-template-columns:1fr}}
</style>
