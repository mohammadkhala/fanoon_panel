<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';

const props = defineProps({ order: Object, statuses: Object });

const tone = { pending_review: 'warn', approved: 'ok', in_production: 'info', ready: 'ok', delivered: 'ok', cancelled: 'bad' };
const money = (n) => '₪' + Number(n).toLocaleString('en-US');

const form = useForm({ status: props.order.status, admin_notes: props.order.admin_notes || '' });

function save() {
    form.patch(route('admin.orders.update', props.order.id), { preserveScroll: true });
}

// Promote design to template
const promoteForm = useForm({ name: '' });
const showPromote = ref(null); // holds design_id of active modal

function openPromote(item) {
    promoteForm.name = 'تصميم معتمد ' + new Date().toLocaleDateString('ar');
    showPromote.value = item.design_id;
}

function submitPromote(designId) {
    promoteForm.post(route('admin.designs.promote', designId), {
        preserveScroll: true,
        onSuccess: () => { showPromote.value = null; },
    });
}
</script>

<template>
    <Head :title="'الطلب ' + order.reference" />
    <AdminLayout :title="order.reference" :subtitle="order.customer + ' · ' + order.created_at">

        <Link :href="route('admin.orders.index')" class="back">→ كل الطلبات</Link>

        <div class="ogrid">
            <div class="items">
                <div class="status-banner" :class="tone[order.status]">
                    <span class="dot"></span> الحالة الحالية: <b>{{ order.status_label }}</b>
                    <span class="tier">{{ order.tier === 'wholesale' ? 'سعر جملة' : 'سعر تجزئة' }}</span>
                </div>

                <div v-for="(it, i) in order.items" :key="i" class="oitem">
                    <div class="oprev">
                        <img v-if="it.preview" :src="'/storage/' + it.preview" alt="تصميم">
                        <div v-else class="noimg">🛡</div>
                    </div>
                    <div class="omid">
                        <div class="otitle">{{ it.title }}</div>
                        <div class="osub">{{ it.quantity }} × {{ money(it.unit_price) }} = {{ money(it.line_total) }}</div>
                        <a v-if="it.preview" :href="'/storage/' + it.preview" target="_blank" class="dl">فتح التصميم بالحجم الكامل ↗</a>

                        <!-- Promote to template -->
                        <div v-if="it.can_promote" class="promote-wrap">
                            <button
                                v-if="showPromote !== it.design_id"
                                class="promote-btn"
                                :disabled="it.templates_count >= 3"
                                :title="it.templates_count >= 3 ? 'المنتج وصل للحد الأقصى (3 قوالب)' : ''"
                                @click="openPromote(it)"
                            >
                                ✦ جعله قالباً افتراضياً
                            </button>
                            <div v-else class="promote-inline">
                                <input v-model="promoteForm.name" class="pname-inp" placeholder="اسم القالب الجديد">
                                <button class="promote-confirm" :disabled="promoteForm.processing" @click="submitPromote(it.design_id)">
                                    {{ promoteForm.processing ? 'جارٍ…' : 'تأكيد ←' }}
                                </button>
                                <button class="promote-cancel" @click="showPromote = null">إلغاء</button>
                            </div>
                            <span v-if="it.product_name" class="prod-tag">{{ it.product_name }} · {{ it.templates_count }}/3 قوالب</span>
                        </div>
                    </div>
                </div>

                <!-- Promote modal: full-screen bg -->
                <div v-if="false"></div>
            </div>

            <aside class="side">
                <div class="panel">
                    <h3>إدارة الطلب</h3>
                    <div class="srow"><span>الإجمالي</span><b>{{ money(order.total) }}</b></div>
                    <div class="info">
                        <div class="il">جهة الاتصال</div>
                        <div>{{ order.contact_name }}</div>
                        <div class="lat">{{ order.contact_phone }}</div>
                    </div>
                    <div v-if="order.notes" class="info"><div class="il">ملاحظات العميل</div><div>{{ order.notes }}</div></div>

                    <form @submit.prevent="save" style="margin-top:18px">
                        <div class="fgrp"><label>تحديث الحالة</label>
                            <select v-model="form.status">
                                <option v-for="(label, key) in statuses" :key="key" :value="key">{{ label }}</option>
                            </select>
                        </div>
                        <div class="fgrp"><label>ملاحظات الإدارة</label>
                            <textarea v-model="form.admin_notes" rows="4" placeholder="ملاحظات داخلية / للعميل"></textarea>
                        </div>
                        <button class="submit" :disabled="form.processing">حفظ التحديث<span class="bib">←</span></button>
                    </form>
                </div>
            </aside>
        </div>
    </AdminLayout>
</template>

<style scoped>
.back{display:inline-block;margin-bottom:18px;color:var(--muted);font-size:14px;text-decoration:none}
.back:hover{color:var(--ink)}
.ogrid{display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start}
.items{display:flex;flex-direction:column;gap:12px}
.status-banner{display:flex;align-items:center;gap:10px;border-radius:16px;padding:14px 18px;font-size:14px;border:1px solid var(--hair);background:var(--bg2)}
.status-banner b{font-weight:700}
.status-banner .dot{width:9px;height:9px;border-radius:999px;background:currentColor}
.status-banner.warn{color:#e0a800}.status-banner.ok{color:var(--emerald-soft)}.status-banner.info{color:#42a5f5}.status-banner.bad{color:#ff7a6b}
.tier{margin-right:auto;color:var(--muted);font-size:12px}
.oitem{display:flex;gap:16px;align-items:center;background:var(--bg2);border:1px solid var(--hair);border-radius:16px;padding:14px}
.oprev{width:90px;height:112px;border-radius:10px;overflow:hidden;background:var(--glass);flex-shrink:0;display:flex;align-items:center;justify-content:center}
.oprev img{width:100%;height:100%;object-fit:cover}
.noimg{font-size:32px}
.omid{flex:1}
.otitle{font-weight:600;margin-bottom:4px}
.osub{color:var(--muted);font-size:13px}
.dl{display:inline-block;margin-top:8px;color:var(--emerald-soft);font-size:13px;text-decoration:none}
.promote-wrap{margin-top:10px;display:flex;flex-wrap:wrap;align-items:center;gap:8px}
.promote-btn{background:rgba(52,215,127,.1);border:1px solid rgba(52,215,127,.3);color:var(--emerald-soft);border-radius:8px;padding:5px 12px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .25s}
.promote-btn:hover:not(:disabled){background:rgba(52,215,127,.2)}
.promote-btn:disabled{opacity:.4;cursor:default}
.promote-inline{display:flex;gap:6px;align-items:center;flex-wrap:wrap}
.pname-inp{background:var(--glass);border:1px solid var(--hair);border-radius:8px;padding:5px 10px;font-size:12px;color:var(--ink);font-family:inherit;outline:none;min-width:180px}
.promote-confirm{background:var(--emerald-soft);border:none;color:#fff;border-radius:8px;padding:5px 12px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit}
.promote-cancel{background:var(--glass);border:1px solid var(--hair);border-radius:8px;padding:5px 10px;font-size:12px;cursor:pointer;color:var(--muted);font-family:inherit}
.prod-tag{font-size:11px;color:var(--muted);background:var(--glass);border:1px solid var(--hair);border-radius:999px;padding:2px 8px}
.panel{background:var(--bg2);border:1px solid var(--hair);border-radius:20px;padding:22px;position:sticky;top:90px}
.panel h3{font-size:16px;margin-bottom:16px}
.srow{display:flex;justify-content:space-between;padding:9px 0;font-size:15px;font-weight:700;border-bottom:1px solid var(--hair)}
.info{margin-top:16px;font-size:14px;line-height:1.9}
.il{font-size:11px;color:var(--muted);letter-spacing:.08em;text-transform:uppercase;margin-bottom:5px}
.fgrp{margin-bottom:12px}
.fgrp label{display:block;font-size:12px;color:var(--muted);margin-bottom:6px}
.fgrp select,.fgrp textarea{width:100%;background:var(--glass);border:1px solid var(--hair);border-radius:10px;padding:10px 12px;color:var(--ink);font-family:inherit;font-size:13px;outline:none}
.submit{width:100%;display:flex;align-items:center;justify-content:center;gap:9px;background:linear-gradient(150deg,var(--emerald-soft),var(--emerald-deep));color:var(--on-emerald);border:none;border-radius:13px;padding:13px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit}
.submit:disabled{opacity:.6}
.bib{width:24px;height:24px;border-radius:999px;background:rgba(3,26,13,.2);display:flex;align-items:center;justify-content:center}
@media(max-width:860px){.ogrid{grid-template-columns:1fr}.panel{position:static}}
</style>
