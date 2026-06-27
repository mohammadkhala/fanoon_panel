<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import StoreLayout from '@/Layouts/StoreLayout.vue';

const props = defineProps({
    items:    Array,
    subtotal: Number,
    tier:     String,
    zones:    Array,
    user:     Object,
});

/* Split the user's full name into first/last for initial values */
const nameParts  = (props.user?.name ?? '').trim().split(/\s+/);
const firstInit  = nameParts.slice(0, -1).join(' ') || nameParts[0] || '';
const lastInit   = nameParts.length > 1 ? nameParts[nameParts.length - 1] : '';

const form = useForm({
    first_name:            firstInit,
    last_name:             lastInit,
    country_code:          '+970',
    contact_phone:         props.user?.phone ?? '',
    contact_email:         props.user?.email ?? '',
    delivery_zone_id:      null,
    shipping_city:         '',
    shipping_address:      '',
    shipping_neighborhood: '',
    shipping_building:     '',
    payment_method:        'cod',
    notes:                 '',
});

const countryCodes = [
    { code: '+970', label: '🇵🇸 +970' },
    { code: '+972', label: '🇮🇱 +972' },
];

const selectedZone = computed(() =>
    props.zones.find(z => z.id === form.delivery_zone_id) ?? null
);

const deliveryFee = computed(() =>
    selectedZone.value ? parseFloat(selectedZone.value.fee) : 0
);

const total = computed(() =>
    parseFloat(props.subtotal) + deliveryFee.value
);

/** Strip leading zero if user entered 10-digit local format (e.g. 0599…) */
function normalizePhone(raw) {
    const digits = raw.replace(/\D/g, '');
    return digits.length === 10 && digits[0] === '0' ? digits.slice(1) : digits;
}

function submit() {
    form
        .transform(data => ({
            first_name:            data.first_name,
            last_name:             data.last_name,
            contact_phone:         data.country_code + normalizePhone(data.contact_phone),
            contact_email:         data.contact_email || null,
            delivery_zone_id:      data.delivery_zone_id,
            shipping_city:         data.shipping_city,
            shipping_address:      data.shipping_address,
            shipping_neighborhood: data.shipping_neighborhood || null,
            shipping_building:     data.shipping_building     || null,
            payment_method:        data.payment_method,
            notes:                 data.notes || null,
        }))
        .post(route('orders.store'));
}

function fmt(n) {
    return Number(n).toLocaleString('ar-SA', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
</script>

<template>
    <Head title="إتمام الطلب" />
    <StoreLayout>
        <div class="co-wrap">

            <!-- Breadcrumb -->
            <div class="co-crumb rv">
                <Link :href="route('home')">الرئيسية</Link>
                <span class="sep">/</span>
                <Link :href="route('cart.index')">السلة</Link>
                <span class="sep">/</span>
                <span>إتمام الطلب</span>
            </div>

            <h1 class="co-title rv">إتمام الطلب</h1>

            <div class="co-grid">

                <!-- ======== MAIN FORM ======== -->
                <form class="co-form" @submit.prevent="submit" novalidate>

                    <!-- ─── Section 1: البيانات الشخصية ─── -->
                    <div class="co-card">
                        <div class="co-section-hd">
                            <span class="step-badge">1</span>
                            <span class="section-title">البيانات الشخصية</span>
                        </div>

                        <!-- First name + Last name -->
                        <div class="frow-2">
                            <div class="fgrp">
                                <label>الاسم الأول <span class="req">*</span></label>
                                <input
                                    v-model="form.first_name"
                                    type="text"
                                    placeholder="محمد"
                                    :class="{ 'has-err': form.errors.first_name }"
                                    required
                                >
                                <span v-if="form.errors.first_name" class="ferr">{{ form.errors.first_name }}</span>
                            </div>
                            <div class="fgrp">
                                <label>الاسم الأخير <span class="req">*</span></label>
                                <input
                                    v-model="form.last_name"
                                    type="text"
                                    placeholder="أحمد"
                                    :class="{ 'has-err': form.errors.last_name }"
                                    required
                                >
                                <span v-if="form.errors.last_name" class="ferr">{{ form.errors.last_name }}</span>
                            </div>
                        </div>

                        <!-- Phone + Email -->
                        <div class="frow-2">
                            <div class="fgrp">
                                <label>رقم الهاتف <span class="req">*</span></label>
                                <div class="phone-row" :class="{ 'has-err': form.errors.contact_phone }">
                                    <select v-model="form.country_code" class="cc-select">
                                        <option v-for="c in countryCodes" :key="c.code" :value="c.code">
                                            {{ c.label }}
                                        </option>
                                    </select>
                                    <input
                                        v-model="form.contact_phone"
                                        type="tel"
                                        placeholder="0599 000 000"
                                        class="phone-input"
                                        required
                                        dir="ltr"
                                    >
                                </div>
                                <span v-if="form.errors.contact_phone" class="ferr">{{ form.errors.contact_phone }}</span>
                            </div>
                            <div class="fgrp">
                                <label>البريد الإلكتروني <span class="opt">(اختياري)</span></label>
                                <input
                                    v-model="form.contact_email"
                                    type="email"
                                    placeholder="example@mail.com"
                                    :class="{ 'has-err': form.errors.contact_email }"
                                    dir="ltr"
                                >
                                <span v-if="form.errors.contact_email" class="ferr">{{ form.errors.contact_email }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- ─── Section 2: عنوان التوصيل ─── -->
                    <div class="co-card">
                        <div class="co-section-hd">
                            <span class="step-badge">2</span>
                            <span class="section-title">عنوان التوصيل</span>
                        </div>

                        <!-- Zone select + fee display -->
                        <div class="frow-2">
                            <div class="fgrp">
                                <label>منطقة التوصيل</label>
                                <select
                                    v-model="form.delivery_zone_id"
                                    :class="{ 'has-err': form.errors.delivery_zone_id }"
                                >
                                    <option :value="null">— اختر منطقة التوصيل —</option>
                                    <option v-for="z in zones" :key="z.id" :value="z.id">
                                        {{ z.name }}{{ z.eta ? '  (' + z.eta + ')' : '' }}
                                    </option>
                                </select>
                                <span v-if="form.errors.delivery_zone_id" class="ferr">{{ form.errors.delivery_zone_id }}</span>
                            </div>
                            <div class="fgrp">
                                <label>رسوم التوصيل</label>
                                <div class="fee-badge" :class="{ 'fee-active': selectedZone }">
                                    <span v-if="selectedZone">₪{{ fmt(selectedZone.fee) }}</span>
                                    <span v-else class="fee-placeholder">تحدد بعد اختيار المنطقة</span>
                                </div>
                            </div>
                        </div>

                        <!-- City -->
                        <div class="frow-1">
                            <div class="fgrp">
                                <label>المدينة <span class="req">*</span></label>
                                <input
                                    v-model="form.shipping_city"
                                    type="text"
                                    placeholder="رام الله"
                                    :class="{ 'has-err': form.errors.shipping_city }"
                                    required
                                >
                                <span v-if="form.errors.shipping_city" class="ferr">{{ form.errors.shipping_city }}</span>
                            </div>
                        </div>

                        <!-- Full address (full width) -->
                        <div class="frow-1">
                            <div class="fgrp">
                                <label>العنوان بالتفصيل <span class="req">*</span></label>
                                <input
                                    v-model="form.shipping_address"
                                    type="text"
                                    placeholder="اسم الشارع، رقم المبنى..."
                                    :class="{ 'has-err': form.errors.shipping_address }"
                                    required
                                >
                                <span v-if="form.errors.shipping_address" class="ferr">{{ form.errors.shipping_address }}</span>
                            </div>
                        </div>

                        <!-- Neighborhood + Building/Floor -->
                        <div class="frow-2">
                            <div class="fgrp">
                                <label>المنطقة / الحي <span class="opt">(اختياري)</span></label>
                                <input
                                    v-model="form.shipping_neighborhood"
                                    type="text"
                                    placeholder="البيرة، المنارة..."
                                    :class="{ 'has-err': form.errors.shipping_neighborhood }"
                                >
                                <span v-if="form.errors.shipping_neighborhood" class="ferr">{{ form.errors.shipping_neighborhood }}</span>
                            </div>
                            <div class="fgrp">
                                <label>رقم المبنى / الطابق <span class="opt">(اختياري)</span></label>
                                <input
                                    v-model="form.shipping_building"
                                    type="text"
                                    placeholder="مبنى 12، الطابق 3"
                                    :class="{ 'has-err': form.errors.shipping_building }"
                                >
                                <span v-if="form.errors.shipping_building" class="ferr">{{ form.errors.shipping_building }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- ─── Section 3: طريقة الدفع ─── -->
                    <div class="co-card">
                        <div class="co-section-hd">
                            <span class="step-badge">3</span>
                            <span class="section-title">طريقة الدفع</span>
                        </div>

                        <div class="pay-option selected">
                            <div class="pay-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                    <line x1="1" y1="10" x2="23" y2="10"/>
                                </svg>
                            </div>
                            <div class="pay-text">
                                <div class="pay-name">الدفع عند الاستلام (Cash on Delivery)</div>
                                <div class="pay-desc">ادفع نقداً عند استلام طلبك — لا توجد رسوم إضافية</div>
                            </div>
                            <div class="pay-radio">
                                <div class="radio-dot active"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="co-card">
                        <div class="fgrp" style="margin-bottom:0">
                            <label>ملاحظات إضافية <span class="opt">(اختياري)</span></label>
                            <textarea
                                v-model="form.notes"
                                rows="3"
                                placeholder="أي تعليمات خاصة بالطلب أو التوصيل..."
                                :class="{ 'has-err': form.errors.notes }"
                            ></textarea>
                            <span v-if="form.errors.notes" class="ferr">{{ form.errors.notes }}</span>
                        </div>
                    </div>

                </form>

                <!-- ======== SIDEBAR SUMMARY ======== -->
                <aside class="co-sidebar">
                    <div class="sidebar-card">
                        <div class="sidebar-title rv">ملخص طلبك</div>

                        <!-- Items list -->
                        <div class="si-items">
                            <div v-for="it in items" :key="it.id" class="si-item rv">
                                <div class="si-img">
                                    <img v-if="it.preview" :src="'/storage/' + it.preview" :alt="it.title">
                                    <div v-else class="si-noimg">🛡</div>
                                </div>
                                <div class="si-info">
                                    <div class="si-name">{{ it.title }}</div>
                                    <div class="si-qty">{{ it.quantity }} × ₪{{ fmt(it.unit_price) }}</div>
                                </div>
                                <div class="si-line">₪{{ fmt(it.line_total) }}</div>
                            </div>
                        </div>

                        <div class="si-divider"></div>

                        <!-- Subtotal -->
                        <div class="si-row">
                            <span class="si-label">المجموع الجزئي</span>
                            <span class="si-val">₪{{ fmt(subtotal) }}</span>
                        </div>

                        <!-- Delivery fee -->
                        <div class="si-row">
                            <span class="si-label">رسوم التوصيل</span>
                            <span v-if="selectedZone" class="si-val">₪{{ fmt(deliveryFee) }}</span>
                            <span v-else class="si-val muted">اختر المنطقة</span>
                        </div>

                        <div class="si-divider"></div>

                        <!-- Total -->
                        <div class="si-row total-row rv">
                            <span class="total-label">الإجمالي</span>
                            <span class="total-val">₪{{ fmt(total) }}</span>
                        </div>

                        <!-- Confirm button -->
                        <button
                            class="confirm-btn"
                            :disabled="form.processing"
                            @click.prevent="submit"
                        >
                            <svg v-if="!form.processing" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <svg v-else class="spin-ico" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="12" cy="12" r="10" stroke-dasharray="40 20"/>
                            </svg>
                            {{ form.processing ? 'جارٍ الإرسال...' : 'تأكيد الطلب' }}
                        </button>

                        <!-- Security note -->
                        <p class="secure-note">بياناتك آمنة ومحمية تماماً 🔒</p>
                    </div>
                </aside>

            </div>
        </div>
    </StoreLayout>
</template>

<style scoped>
/* ───────────── Layout wrapper ───────────── */
.co-wrap {
    max-width: 1180px;
    margin: 0 auto;
    padding: 28px 20px 80px;
}

.co-crumb {
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.co-crumb a {
    color: var(--muted);
    text-decoration: none;
    transition: color 0.2s;
}
.co-crumb a:hover { color: var(--emerald-deep); }
.co-crumb .sep { opacity: 0.4; }

.co-title {
    font-size: 26px;
    font-weight: 700;
    color: var(--ink);
    margin: 0 0 28px;
}

/* ───────────── Two-column grid ───────────── */
.co-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 24px;
    align-items: start;
}

/* ───────────── Form column ───────────── */
.co-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.co-card {
    background: var(--bg2, #fff);
    border: 1px solid var(--hair, #e8e8e8);
    border-radius: 18px;
    padding: 22px 24px;
}

/* Section header with step badge */
.co-section-hd {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 22px;
}
.step-badge {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #1a2e20;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.section-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--ink);
}

/* Field rows */
.frow-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
}
.frow-2:last-child,
.frow-1:last-child { margin-bottom: 0; }
.frow-1 {
    margin-bottom: 14px;
}

.fgrp {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.fgrp label {
    font-size: 12px;
    font-weight: 600;
    color: var(--muted);
    letter-spacing: 0.02em;
}
.req { color: #e54d4d; }
.opt { font-weight: 400; opacity: 0.7; }

.fgrp input,
.fgrp select,
.fgrp textarea {
    background: var(--bg, #f9f9f9);
    border: 1.5px solid var(--hair, #e8e8e8);
    border-radius: 10px;
    padding: 11px 13px;
    color: var(--ink);
    font-family: inherit;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    width: 100%;
    box-sizing: border-box;
}
.fgrp input:focus,
.fgrp select:focus,
.fgrp textarea:focus {
    border-color: var(--emerald-deep, #2d6a4f);
    box-shadow: 0 0 0 3px var(--emerald-soft, rgba(45,106,79,0.12));
}
.fgrp input.has-err,
.fgrp select.has-err,
.fgrp textarea.has-err,
.phone-row.has-err {
    border-color: #e54d4d;
}
.fgrp textarea { resize: vertical; min-height: 88px; }

/* Phone composite row
   DOM order: <select> first → appears RIGHT in RTL
              <input>  second → appears LEFT  in RTL
   No CSS `order` needed — DOM order is already correct for RTL flex. */
.phone-row {
    display: flex;
    flex-direction: row;
    border: 1.5px solid var(--hair, #e8e8e8);
    border-radius: 10px;
    overflow: visible;          /* don't clip native select dropdown */
    transition: border-color 0.2s, box-shadow 0.2s;
}
.phone-row:focus-within {
    border-color: var(--emerald-deep, #2d6a4f);
    box-shadow: 0 0 0 3px var(--emerald-soft, rgba(45,106,79,0.12));
}
/* .fgrp select has specificity (0,1,1) which beats (0,1,0) of .cc-select,
   so we need !important to override width:100% from .fgrp select */
.cc-select {
    background: var(--glass, #f0f4f0);
    border: none !important;
    border-left: 1.5px solid var(--hair, #e8e8e8) !important; /* separator between select & input */
    border-radius: 0 !important;
    border-top-right-radius: 8px !important;
    border-bottom-right-radius: 8px !important;
    padding: 11px 10px 11px 8px;
    font-size: 13px;
    font-weight: 600;
    color: var(--ink);
    outline: none;
    cursor: pointer;
    width: auto !important;     /* override .fgrp select { width:100% } */
    min-width: fit-content;
    flex-shrink: 0;
    box-shadow: none !important;
}
/* Same issue: .fgrp input (0,1,1) beats .phone-input (0,1,0) */
.phone-input {
    flex: 1 1 0% !important;
    min-width: 0 !important;
    width: auto !important;     /* override .fgrp input { width:100% } */
    border: none !important;
    border-radius: 0 !important;
    border-top-left-radius: 8px !important;
    border-bottom-left-radius: 8px !important;
    background: var(--bg, #f9f9f9) !important;
    box-shadow: none !important;
    text-align: left;
    direction: ltr;
}

/* Fee badge */
.fee-badge {
    background: var(--bg, #f9f9f9);
    border: 1.5px solid var(--hair, #e8e8e8);
    border-radius: 10px;
    padding: 11px 13px;
    font-size: 14px;
    color: var(--muted);
    min-height: 44px;
    display: flex;
    align-items: center;
}
.fee-badge.fee-active {
    color: var(--emerald-deep, #2d6a4f);
    font-weight: 700;
    border-color: var(--emerald-soft, rgba(45,106,79,0.3));
    background: var(--emerald-soft, rgba(45,106,79,0.06));
}
.fee-placeholder { opacity: 0.55; font-size: 12px; }

/* Validation error messages */
.ferr {
    font-size: 12px;
    color: #e54d4d;
    margin-top: 2px;
}

/* ───────────── Payment option card ───────────── */
.pay-option {
    display: flex;
    align-items: center;
    gap: 14px;
    border: 2px solid var(--hair, #e8e8e8);
    border-radius: 14px;
    padding: 16px 18px;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
}
.pay-option.selected {
    border-color: var(--emerald-deep, #2d6a4f);
    background: var(--emerald-soft, rgba(45,106,79,0.05));
}
.pay-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #1a2e20;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.pay-text { flex: 1; }
.pay-name {
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 4px;
}
.pay-desc {
    font-size: 12px;
    color: var(--muted);
    line-height: 1.5;
}
.pay-radio { flex-shrink: 0; }
.radio-dot {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid var(--hair, #e8e8e8);
    display: flex;
    align-items: center;
    justify-content: center;
}
.radio-dot.active {
    border-color: var(--emerald-deep, #2d6a4f);
    background: var(--emerald-deep, #2d6a4f);
    box-shadow: inset 0 0 0 4px #fff;
}

/* ───────────── Sidebar ───────────── */
.co-sidebar {
    position: sticky;
    top: 24px;
}
.sidebar-card {
    background: var(--bg2, #fff);
    border: 1px solid var(--hair, #e8e8e8);
    border-radius: 20px;
    padding: 22px;
    display: flex;
    flex-direction: column;
    gap: 0;
}
.sidebar-title {
    font-size: 17px;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 18px;
}

/* Item list in sidebar */
.si-items {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 16px;
}
.si-item {
    display: flex;
    align-items: center;
    gap: 10px;
}
.si-img {
    width: 50px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    background: var(--glass, #f0f4f0);
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.si-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.si-noimg { font-size: 22px; }
.si-info { flex: 1; min-width: 0; }
.si-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--ink);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 3px;
}
.si-qty {
    font-size: 12px;
    color: var(--muted);
}
.si-line {
    font-size: 13px;
    font-weight: 700;
    color: var(--ink);
    white-space: nowrap;
}

/* Summary rows */
.si-divider {
    height: 1px;
    background: var(--hair, #e8e8e8);
    margin: 14px 0;
}
.si-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 0;
}
.si-label {
    font-size: 14px;
    color: var(--muted);
}
.si-val {
    font-size: 14px;
    font-weight: 600;
    color: var(--ink);
}
.si-val.muted {
    font-weight: 400;
    color: var(--muted);
    font-size: 12px;
}
.total-row {
    padding: 8px 0 0;
}
.total-label {
    font-size: 17px;
    font-weight: 700;
    color: var(--ink);
}
.total-val {
    font-size: 22px;
    font-weight: 800;
    color: var(--emerald-deep, #2d6a4f);
}

/* Confirm button */
.confirm-btn {
    margin-top: 18px;
    width: 100%;
    padding: 14px 20px;
    background: linear-gradient(135deg, #2d6a4f 0%, #1a3d2b 100%);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-family: inherit;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: opacity 0.2s, transform 0.15s;
}
.confirm-btn:hover:not(:disabled) {
    opacity: 0.92;
    transform: translateY(-1px);
}
.confirm-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Spinner icon */
.spin-ico {
    animation: spin 0.9s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Security note */
.secure-note {
    text-align: center;
    font-size: 12px;
    color: var(--muted);
    margin-top: 14px;
    line-height: 1.6;
}

/* ───────────── Responsive ───────────── */
@media (max-width: 860px) {
    .co-grid {
        grid-template-columns: 1fr;
    }
    .co-sidebar {
        position: static;
        order: -1; /* show sidebar above form on mobile */
    }
    .frow-2 {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 480px) {
    .co-card { padding: 16px; }
    .co-title { font-size: 20px; }
}
</style>
