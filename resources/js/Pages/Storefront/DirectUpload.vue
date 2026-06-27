<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    product: Object,
});

const form = useForm({
    file: null,
    quantity: 1,
    notes: '',
});

/* ── Drag-and-drop ── */
const dragging = ref(false);
const preview  = ref(null);
const fileName = ref('');

function onDrop(e) {
    dragging.value = false;
    const file = e.dataTransfer?.files?.[0] ?? e.target?.files?.[0];
    if (file) setFile(file);
}

function setFile(file) {
    form.file = file;
    fileName.value = file.name;
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => { preview.value = e.target.result; };
        reader.readAsDataURL(file);
    } else {
        preview.value = null; // PDF — no preview
    }
}

function submit() {
    form.post(route('upload.store', props.product.slug), { forceFormData: true });
}

function formatPrice(p) {
    if (!p) return '—';
    return Number(p).toLocaleString('ar-SA') + ' ₪';
}
</script>

<template>
    <Head :title="'رفع تصميم — ' + product.name" />

    <div class="shell" dir="rtl">

        <!-- Top bar -->
        <header class="topbar">
            <Link :href="route('product.show', product.slug)" class="back-btn">← العودة للمنتج</Link>
            <span class="topbar-title">رفع تصميم جاهز</span>
        </header>

        <div class="page">

            <!-- ── Product card ── -->
            <aside class="sidebar">
                <div class="prod-card">
                    <img v-if="product.cover_image" :src="'/storage/' + product.cover_image" :alt="product.name" class="prod-img">
                    <div v-else class="prod-img-placeholder">🖼</div>

                    <div class="prod-info">
                        <div class="crumb-mini">
                            <span>{{ product.category.name }}</span>
                            <span class="sep">/</span>
                            <span>{{ product.subcategory.name }}</span>
                        </div>
                        <h2 class="prod-name">{{ product.name }}</h2>
                        <p v-if="product.description" class="prod-desc">{{ product.description }}</p>
                        <div v-if="product.price" class="prod-price">{{ formatPrice(product.price) }}</div>
                    </div>
                </div>

                <!-- Specs -->
                <div class="specs">
                    <h4>متطلبات الملف</h4>
                    <ul>
                        <li>✅ الصيغ المقبولة: <strong>PDF، PNG، JPG، WEBP</strong></li>
                        <li>✅ الحجم الأقصى: <strong>20 MB</strong></li>
                        <li>✅ الدقة المفضّلة: <strong>300 DPI فأكثر</strong></li>
                        <li>✅ اللون: <strong>CMYK أو RGB</strong></li>
                    </ul>
                </div>
            </aside>

            <!-- ── Upload form ── -->
            <main class="upload-area">
                <div class="upload-card">
                    <h1 class="upload-title">ارفع تصميمك الجاهز</h1>
                    <p class="upload-sub">سيقوم فريقنا بمراجعة الملف وإرسال تأكيد الطلب</p>

                    <!-- Dropzone -->
                    <div
                        class="dropzone"
                        :class="{ dragging, 'has-file': form.file }"
                        @dragover.prevent="dragging = true"
                        @dragleave.prevent="dragging = false"
                        @drop.prevent="onDrop"
                        @click="$refs.fileInput.click()"
                    >
                        <!-- Preview -->
                        <template v-if="form.file">
                            <img v-if="preview" :src="preview" class="dz-preview-img" alt="معاينة">
                            <div v-else class="dz-pdf-icon">📄</div>
                            <div class="dz-file-name">{{ fileName }}</div>
                            <div class="dz-change">اضغط لتغيير الملف</div>
                        </template>

                        <!-- Empty state -->
                        <template v-else>
                            <div class="dz-icon">📤</div>
                            <div class="dz-hint">اسحب الملف هنا أو اضغط للاختيار</div>
                            <div class="dz-sub">PDF · PNG · JPG · WEBP — حتى 20 MB</div>
                        </template>

                        <input
                            ref="fileInput"
                            type="file"
                            accept=".pdf,.png,.jpg,.jpeg,.webp"
                            class="hidden-input"
                            @change="e => setFile(e.target.files[0])"
                        >
                    </div>

                    <div v-if="form.errors.file" class="field-err">{{ form.errors.file }}</div>

                    <!-- Quantity -->
                    <div class="field">
                        <label class="field-label">الكمية</label>
                        <div class="qty-row">
                            <button type="button" class="qty-btn" @click="form.quantity = Math.min(999, form.quantity + 1)">+</button>
                            <input type="number" v-model="form.quantity" min="1" max="999" class="qty-input">
                            <button type="button" class="qty-btn" @click="form.quantity = Math.max(1, form.quantity - 1)">−</button>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="field">
                        <label class="field-label">ملاحظات للفريق <span class="opt">(اختياري)</span></label>
                        <textarea
                            v-model="form.notes"
                            class="notes-input"
                            rows="3"
                            placeholder="أي تفاصيل خاصة بالطلب، حجم معيّن، لون معيّن..."
                        ></textarea>
                    </div>

                    <!-- Submit -->
                    <button
                        class="submit-btn"
                        :disabled="!form.file || form.processing"
                        @click="submit"
                    >
                        <span v-if="form.processing">⏳ جارٍ الرفع...</span>
                        <span v-else>إضافة إلى السلة ←</span>
                    </button>

                    <p v-if="!form.file" class="hint-no-file">اختر ملفاً أولاً لتتمكن من الإرسال</p>
                </div>
            </main>

        </div>
    </div>
</template>

<style scoped>
*, *::before, *::after { box-sizing: border-box; }

.shell {
    min-height: 100vh;
    background: var(--bg, #f4f6f5);
    color: var(--ink, #111);
    font-family: 'Tajawal', 'Cairo', system-ui, sans-serif;
}

/* Top bar */
.topbar {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 14px 32px;
    background: var(--bg2, #fff);
    border-bottom: 1px solid var(--hair, #e5e7eb);
    position: sticky;
    top: 0;
    z-index: 10;
}
.back-btn {
    color: var(--muted, #6b7280);
    text-decoration: none;
    font-size: 14px;
    transition: color .2s;
}
.back-btn:hover { color: var(--ink, #111); }
.topbar-title { font-size: 15px; font-weight: 700; }

/* Layout */
.page {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 28px;
    max-width: 1080px;
    margin: 0 auto;
    padding: 32px 24px 80px;
}

/* Sidebar */
.sidebar { display: flex; flex-direction: column; gap: 16px; }

.prod-card {
    background: var(--bg2, #fff);
    border: 1px solid var(--hair, #e5e7eb);
    border-radius: 16px;
    overflow: hidden;
}
.prod-img { width: 100%; aspect-ratio: 4/3; object-fit: cover; display: block; }
.prod-img-placeholder {
    width: 100%; aspect-ratio: 4/3;
    display: flex; align-items: center; justify-content: center;
    font-size: 3rem; background: var(--glass, #f9fafb);
}
.prod-info { padding: 16px; }
.crumb-mini { font-size: 11px; color: var(--muted, #9ca3af); display: flex; gap: 5px; margin-bottom: 6px; }
.sep { opacity: .4; }
.prod-name { font-size: 17px; font-weight: 700; margin: 0 0 6px; }
.prod-desc { font-size: 13px; color: var(--muted, #6b7280); margin: 0 0 10px; line-height: 1.5; }
.prod-price { font-size: 20px; font-weight: 800; color: #10b981; }

.specs {
    background: var(--bg2, #fff);
    border: 1px solid var(--hair, #e5e7eb);
    border-radius: 16px;
    padding: 16px;
}
.specs h4 { font-size: 13px; font-weight: 700; margin: 0 0 10px; color: var(--muted, #6b7280); text-transform: uppercase; letter-spacing: .05em; }
.specs ul { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 7px; }
.specs li { font-size: 13px; color: var(--ink, #374151); }

/* Upload card */
.upload-card {
    background: var(--bg2, #fff);
    border: 1px solid var(--hair, #e5e7eb);
    border-radius: 20px;
    padding: 32px;
}
.upload-title { font-size: 22px; font-weight: 800; margin: 0 0 6px; }
.upload-sub { font-size: 14px; color: var(--muted, #6b7280); margin: 0 0 24px; }

/* Dropzone */
.dropzone {
    border: 2px dashed var(--hair, #d1d5db);
    border-radius: 16px;
    padding: 48px 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: all .2s;
    text-align: center;
    position: relative;
    min-height: 220px;
}
.dropzone:hover, .dropzone.dragging {
    border-color: #10b981;
    background: rgba(16,185,129,.04);
}
.dropzone.has-file { border-style: solid; border-color: #10b981; background: rgba(16,185,129,.03); }
.hidden-input { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
.dz-icon { font-size: 3rem; }
.dz-hint { font-size: 16px; font-weight: 600; }
.dz-sub { font-size: 13px; color: var(--muted, #9ca3af); }
.dz-preview-img { max-height: 140px; max-width: 100%; border-radius: 10px; object-fit: contain; }
.dz-pdf-icon { font-size: 4rem; }
.dz-file-name { font-size: 14px; font-weight: 600; color: #10b981; word-break: break-all; }
.dz-change { font-size: 12px; color: var(--muted, #9ca3af); }

.field-err { color: #ef4444; font-size: 12px; margin-top: 6px; }

/* Fields */
.field { margin-top: 20px; }
.field-label { display: block; font-size: 13px; font-weight: 600; color: var(--muted, #374151); margin-bottom: 8px; }
.opt { font-weight: 400; color: var(--muted, #9ca3af); }

.qty-row { display: flex; align-items: center; gap: 0; width: fit-content; }
.qty-btn {
    width: 42px; height: 42px;
    background: var(--glass, #f3f4f6);
    border: 1px solid var(--hair, #e5e7eb);
    font-size: 20px; font-weight: 600; cursor: pointer;
    color: var(--ink, #111); transition: background .15s;
}
.qty-btn:first-child { border-radius: 12px 0 0 12px; }
.qty-btn:last-child  { border-radius: 0 12px 12px 0; }
.qty-btn:hover { background: var(--hair, #e5e7eb); }
.qty-input {
    width: 64px; height: 42px;
    border: 1px solid var(--hair, #e5e7eb); border-left: none; border-right: none;
    text-align: center; font-size: 15px; font-weight: 600;
    background: var(--bg2, #fff); color: var(--ink, #111);
    font-family: inherit; outline: none;
}

.notes-input {
    width: 100%;
    background: var(--glass, #f9fafb);
    border: 1px solid var(--hair, #e5e7eb);
    border-radius: 12px;
    padding: 12px 14px;
    font-size: 14px;
    font-family: inherit;
    color: var(--ink, #111);
    resize: vertical;
    outline: none;
    transition: border-color .2s;
}
.notes-input:focus { border-color: #10b981; }

/* Submit */
.submit-btn {
    margin-top: 24px;
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
    border: none;
    border-radius: 14px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: opacity .2s, transform .15s;
}
.submit-btn:hover:not(:disabled) { opacity: .93; transform: translateY(-1px); }
.submit-btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }

.hint-no-file { text-align: center; font-size: 12px; color: var(--muted, #9ca3af); margin-top: 8px; }

/* Responsive */
@media (max-width: 720px) {
    .page { grid-template-columns: 1fr; }
    .topbar { padding: 12px 16px; }
    .upload-card { padding: 20px; }
}
</style>
