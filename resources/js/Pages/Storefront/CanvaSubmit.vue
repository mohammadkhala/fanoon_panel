<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import StoreLayout from '@/Layouts/StoreLayout.vue';
import { ref } from 'vue';

const props = defineProps({ template: Object });

const form = useForm({ file: null, quantity: 1 });
const dragOver = ref(false);
const filePreview = ref(null);
const fileName = ref('');

function onFile(file) {
    if (!file) return;
    form.file = file;
    fileName.value = file.name;
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => { filePreview.value = e.target.result; };
        reader.readAsDataURL(file);
    } else {
        filePreview.value = null;
    }
}

function onDrop(e) {
    dragOver.value = false;
    onFile(e.dataTransfer.files[0]);
}

function submit() {
    form.post(route('canva.submit.store', props.template.id), {
        forceFormData: true,
    });
}
</script>

<template>
    <Head title="رفع تصميمك" />
    <StoreLayout>
        <div class="wrap">
            <div class="center-box rv d1">
                <div class="step-header">
                    <div class="step-icon">📤</div>
                    <div>
                        <h1 class="title">ارفع تصميمك من كانفا</h1>
                        <p class="sub">حمّل الملف الذي صمّمته في كانفا، وسيُضاف مباشرة إلى سلتك.</p>
                    </div>
                </div>

                <!-- Template info -->
                <div class="tmpl-info">
                    <div class="tmpl-img-wrap">
                        <img v-if="template.preview_image" :src="'/storage/' + template.preview_image" :alt="template.name" class="tmpl-img">
                        <div v-else class="tmpl-ph">🎨</div>
                    </div>
                    <div class="tmpl-meta">
                        <div class="tmpl-name">{{ template.name }}</div>
                        <div class="tmpl-prod">{{ template.product.name }}</div>
                        <div class="tmpl-path">{{ template.product.category }} ← {{ template.product.subcategory }}</div>
                    </div>
                    <a v-if="template.canva_template_url"
                       :href="template.canva_template_url"
                       target="_blank" rel="noopener noreferrer"
                       class="reopen-btn" title="فتح كانفا مجدداً">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                            <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                        كانفا
                    </a>
                </div>

                <form @submit.prevent="submit" class="upload-form">

                    <!-- Drop zone -->
                    <div class="drop-zone"
                         :class="{ active: dragOver, 'has-file': form.file }"
                         @dragover.prevent="dragOver = true"
                         @dragleave="dragOver = false"
                         @drop.prevent="onDrop"
                         @click="$refs.fileInput.click()">

                        <img v-if="filePreview" :src="filePreview" class="preview-img">
                        <div v-else-if="form.file" class="file-info">
                            <span class="file-icon">📄</span>
                            <span class="file-name">{{ fileName }}</span>
                        </div>
                        <div v-else class="drop-hint">
                            <div class="drop-icon">📎</div>
                            <div class="drop-text">اسحب ملفك هنا أو انقر للاختيار</div>
                            <div class="drop-sub">PNG, JPG, PDF, WEBP — حد أقصى 20MB</div>
                        </div>
                        <input ref="fileInput" type="file" accept="image/*,.pdf" class="hidden-input" @change="e => onFile(e.target.files[0])">
                    </div>
                    <p v-if="form.errors.file" class="ferr">{{ form.errors.file }}</p>

                    <!-- Quantity -->
                    <div class="qty-row">
                        <label class="qty-lbl">الكمية المطلوبة</label>
                        <div class="qty-ctrl">
                            <button type="button" class="qty-btn" @click="form.quantity = Math.max(1, form.quantity - 1)">−</button>
                            <input v-model.number="form.quantity" type="number" min="1" max="999" class="qty-inp">
                            <button type="button" class="qty-btn" @click="form.quantity = Math.min(999, form.quantity + 1)">+</button>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn" :disabled="!form.file || form.processing">
                        <span>{{ form.processing ? 'جارٍ الرفع…' : 'أضف إلى السلة ←' }}</span>
                    </button>

                </form>
            </div>
        </div>
    </StoreLayout>
</template>

<style scoped>
.center-box { max-width: 560px; margin: 50px auto 80px; display: flex; flex-direction: column; gap: 22px; }

.step-header { display: flex; align-items: flex-start; gap: 16px; }
.step-icon { font-size: 44px; flex-shrink: 0; }
.title { font-size: 26px; font-weight: 800; margin: 0 0 6px; }
.sub { font-size: 14px; color: var(--muted); margin: 0; line-height: 1.6; }

.tmpl-info { display: flex; align-items: center; gap: 14px; background: var(--bg2); border: 1px solid var(--hair); border-radius: 16px; padding: 14px 18px; }
.tmpl-img-wrap { width: 56px; height: 56px; border-radius: 12px; overflow: hidden; background: var(--glass); flex-shrink: 0; }
.tmpl-img { width: 100%; height: 100%; object-fit: cover; }
.tmpl-ph { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 24px; }
.tmpl-meta { flex: 1; min-width: 0; }
.tmpl-name { font-weight: 700; font-size: 15px; }
.tmpl-prod { font-size: 13px; color: var(--muted); margin-top: 2px; }
.tmpl-path { font-size: 11px; color: var(--muted); opacity: .6; margin-top: 2px; }
.reopen-btn { display: inline-flex; align-items: center; gap: 6px; flex-shrink: 0;
              background: linear-gradient(150deg,#7c3aed,#4f46e5); color: #fff;
              border-radius: 10px; padding: 8px 14px; font-size: 12px; font-weight: 600;
              text-decoration: none; transition: all .3s; white-space: nowrap; }
.reopen-btn:hover { transform: translateY(-1px); opacity: .9; }

.upload-form { display: flex; flex-direction: column; gap: 16px; }

.drop-zone {
    border: 2px dashed var(--hair); border-radius: 18px;
    min-height: 180px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden; transition: all .3s;
    background: var(--bg2);
}
.drop-zone:hover, .drop-zone.active { border-color: rgba(52,215,127,.5); background: rgba(52,215,127,.03); }
.drop-zone.has-file { border-color: rgba(52,215,127,.4); }
.preview-img { width: 100%; max-height: 280px; object-fit: contain; }
.file-info { display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 30px; }
.file-icon { font-size: 40px; }
.file-name { font-size: 14px; color: var(--emerald-soft); font-weight: 600; }
.drop-hint { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 30px; text-align: center; }
.drop-icon { font-size: 36px; opacity: .4; }
.drop-text { font-size: 15px; font-weight: 600; color: var(--ink); }
.drop-sub { font-size: 12px; color: var(--muted); }
.hidden-input { display: none; }
.ferr { font-size: 12px; color: #ff7a6b; margin-top: -8px; }

.qty-row { display: flex; align-items: center; justify-content: space-between; background: var(--bg2); border: 1px solid var(--hair); border-radius: 14px; padding: 14px 18px; }
.qty-lbl { font-size: 14px; font-weight: 600; }
.qty-ctrl { display: flex; align-items: center; gap: 12px; }
.qty-btn { width: 34px; height: 34px; border-radius: 10px; background: var(--glass); border: 1px solid var(--hair); color: var(--ink); font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 600; }
.qty-inp { width: 56px; text-align: center; background: none; border: none; font-size: 18px; font-weight: 700; color: var(--ink); font-family: inherit; outline: none; }

.submit-btn { width: 100%; background: linear-gradient(150deg, var(--emerald-soft), var(--emerald-deep)); color: var(--on-emerald); border: none; border-radius: 16px; padding: 17px; font-size: 16px; font-weight: 700; cursor: pointer; font-family: inherit; transition: all .3s; box-shadow: 0 8px 28px rgba(52,215,127,.25); }
.submit-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 14px 40px rgba(52,215,127,.35); }
.submit-btn:disabled { opacity: .45; cursor: default; transform: none; box-shadow: none; }
</style>
