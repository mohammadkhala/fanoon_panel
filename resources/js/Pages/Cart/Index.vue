<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import StoreLayout from '@/Layouts/StoreLayout.vue';

const props = defineProps({ items: Array, subtotal: Number, tier: String });

function setQty(item, qty) {
    qty = Math.max(1, Math.min(999, +qty || 1));
    router.patch(route('cart.update', item.id), { quantity: qty }, { preserveScroll: true });
}
function remove(item) {
    router.delete(route('cart.destroy', item.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="السلة" />
    <StoreLayout>
        <div class="wrap">
            <div class="phead">
                <div class="crumb"><Link :href="route('home')">الرئيسية</Link><span class="sep">/</span><span>السلة</span></div>
                <span class="eyebrow rv">سلة المشتريات</span>
                <h1 class="rv d1">تصاميمك الجاهزة للطلب</h1>
                <p class="rv d2">راجع تصاميمك ثم أكمل الطلب — ستراجع الإدارة كل تصميم قبل التصنيع</p>
            </div>
        </div>

        <div class="wrap"><section style="padding-top:40px">
            <p v-if="!items.length" style="text-align:center;color:var(--muted);padding:60px 0">
                سلتك فارغة. <Link :href="route('shop')" style="color:var(--emerald)">تصفّح القوالب</Link> وابدأ التصميم.
            </p>

            <div v-else class="cartgrid">
                <div class="citems">
                    <div v-for="it in items" :key="it.id" class="citem rv">
                        <div class="cprev">
                            <img v-if="it.preview" :src="'/storage/' + it.preview" alt="تصميم">
                            <div v-else class="noimg">🛡</div>
                        </div>
                        <div class="cmid">
                            <div class="ctitle">{{ it.title }}</div>
                            <div class="cprice">₪{{ it.unit_price }} / قطعة</div>
                            <div class="qty">
                                <button @click="setQty(it, it.quantity - 1)">−</button>
                                <input type="number" :value="it.quantity" min="1" @change="setQty(it, $event.target.value)">
                                <button @click="setQty(it, it.quantity + 1)">+</button>
                            </div>
                        </div>
                        <div class="cend">
                            <div class="cline">₪{{ it.line_total }}</div>
                            <button class="crm" @click="remove(it)">إزالة</button>
                        </div>
                    </div>
                </div>

                <aside class="csum rv d1">
                    <h3>ملخّص الطلب</h3>
                    <div class="srow"><span>المجموع الفرعي</span><b>₪{{ subtotal }}</b></div>
                    <div class="srow"><span>التسعيرة</span><b>{{ tier === 'wholesale' ? 'جملة (شركات)' : 'تجزئة' }}</b></div>
                    <div class="srow total"><span>الإجمالي</span><b>₪{{ subtotal }}</b></div>

                    <Link :href="route('checkout')" class="bsub" style="width:100%;justify-content:center;margin-top:18px;display:flex;text-decoration:none">
                        إتمام الطلب<span class="bib">←</span>
                    </Link>

                    <p class="note-sm">سنراجع تصميمك ونتواصل معك خلال 24 ساعة لإتمام الصنع.</p>
                </aside>
            </div>
        </section></div>
    </StoreLayout>
</template>

<style scoped>
.cartgrid{display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start}
.citems{display:flex;flex-direction:column;gap:14px}
.citem{display:flex;gap:16px;align-items:center;background:var(--bg2);border:1px solid var(--hair);border-radius:18px;padding:14px}
.cprev{width:90px;height:110px;border-radius:12px;overflow:hidden;background:var(--glass);flex-shrink:0;display:flex;align-items:center;justify-content:center}
.cprev img{width:100%;height:100%;object-fit:cover}
.noimg{font-size:34px}
.cmid{flex:1}
.ctitle{font-weight:600;font-size:15px;margin-bottom:4px}
.cprice{color:var(--muted);font-size:13px;margin-bottom:10px}
.qty{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--hair);border-radius:999px;padding:3px}
.qty button{width:28px;height:28px;border-radius:999px;background:var(--glass);border:none;color:var(--ink);font-size:16px;cursor:pointer}
.qty input{width:44px;text-align:center;background:none;border:none;color:var(--ink);font-size:14px;outline:none}
.cend{text-align:left}
.cline{font-weight:700;font-size:16px;margin-bottom:8px}
.crm{background:none;border:none;color:#ff7a6b;font-size:13px;cursor:pointer}
.csum{background:var(--bg2);border:1px solid var(--hair);border-radius:20px;padding:22px;position:sticky;top:20px}
.csum h3{font-size:17px;margin-bottom:16px}
.srow{display:flex;justify-content:space-between;padding:9px 0;color:var(--muted);font-size:14px;border-bottom:1px solid var(--hair)}
.srow b{color:var(--ink)}
.srow.total{font-size:16px;border-bottom:none;color:var(--ink);font-weight:700}
.note-sm{color:var(--muted);font-size:12px;font-weight:300;margin-top:14px;line-height:1.7}
</style>
