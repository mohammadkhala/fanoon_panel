<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, onUnmounted } from 'vue';

const props = defineProps({
    template:   Object,   // id, name, preview_image, canva_template_url, product{slug,name,…}
    canvaUrl:   String,   // the Canva copy/edit link
    submitUrl:  String,   // not used; we post directly
});

/* ─────────────────────────────────────────────
   State machine:  idle → editing → uploading
   If no canvaUrl, skip straight to uploading.
   ───────────────────────────────────────────── */
const phase    = ref(props.canvaUrl ? 'idle' : 'uploading'); // 'idle' | 'editing' | 'uploading'
const popupWin = ref(null);
let   pollId   = null;

/* ─── open Canva in a centered popup ─── */
function openCanva() {
    if (!props.canvaUrl) return;

    const w = Math.min(1280, screen.availWidth  - 40);
    const h = Math.min(820,  screen.availHeight - 40);
    const l = Math.round((screen.availWidth  - w) / 2);
    const t = Math.round((screen.availHeight - h) / 2);

    const popup = window.open(
        props.canvaUrl,
        'canva_editor',
        `width=${w},height=${h},left=${l},top=${t},resizable=yes,scrollbars=yes`
    );

    if (!popup) {
        // Popup blocked — open in new tab and jump to upload step
        window.open(props.canvaUrl, '_blank');
        phase.value = 'uploading';
        return;
    }

    popupWin.value = popup;
    phase.value    = 'editing';

    // Poll every 600 ms — when popup closes, move to upload step
    pollId = setInterval(() => {
        if (popup.closed) {
            clearInterval(pollId);
            pollId         = null;
            popupWin.value = null;
            phase.value    = 'uploading';
        }
    }, 600);
}

function reopenCanva() {
    if (popupWin.value && !popupWin.value.closed) {
        popupWin.value.focus();
    } else {
        openCanva();
    }
}

onUnmounted(() => { if (pollId) clearInterval(pollId); });

/* ─── Upload form ─── */
const form       = useForm({ file: null, quantity: 1 });
const dropzone   = ref(null);
const isDragging = ref(false);
const fileName   = computed(() => form.file?.name ?? null);

function pickFile(e) {
    const f = (e.target?.files ?? e.dataTransfer?.files)?.[0];
    if (f) form.file = f;
}
function onDragOver(e) { e.preventDefault(); isDragging.value = true;  }
function onDragLeave()  { isDragging.value = false; }
function onDrop(e)      { e.preventDefault(); isDragging.value = false; pickFile(e); }
function triggerPick()  { dropzone.value?.querySelector('input')?.click(); }

function submit() {
    if (!form.file || form.processing) return;
    form.post(route('canva.submit.store', props.template.id), { forceFormData: true });
}
</script>

<template>
    <Head :title="'تصميم: ' + template.name" />

    <div class="ce-shell">

        <!-- ══════════════════════════════════════
             TOP BAR
        ══════════════════════════════════════ -->
        <header class="ce-bar">
            <div class="ce-bar-r">
                <Link :href="route('product.show', template.product.slug)" class="ce-back">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
                         stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                    رجوع
                </Link>
                <div class="ce-info">
                    <span class="ce-tname">{{ template.name }}</span>
                    <span class="ce-pname">{{ template.product.name }}</span>
                </div>
            </div>

            <div class="ce-bar-l">
                <!-- if no canvaUrl: only 1 step (upload directly) -->
                <div v-if="!canvaUrl" class="steps-mini">
                    <span class="sm-step active">
                        <span class="sm-n">1</span>
                        <span class="sm-lbl">رفع التصميم</span>
                    </span>
                </div>
                <!-- full 3-step bar -->
                <div v-else class="steps-mini">
                    <span class="sm-step" :class="{ done: phase !== 'idle', active: phase === 'idle' }">
                        <span class="sm-n">{{ phase !== 'idle' ? '✓' : '1' }}</span>
                        <span class="sm-lbl">فتح كانفا</span>
                    </span>
                    <span class="sm-line"></span>
                    <span class="sm-step" :class="{ done: phase === 'uploading', active: phase === 'editing' }">
                        <span class="sm-n">{{ phase === 'uploading' ? '✓' : '2' }}</span>
                        <span class="sm-lbl">التصميم</span>
                    </span>
                    <span class="sm-line"></span>
                    <span class="sm-step" :class="{ active: phase === 'uploading' }">
                        <span class="sm-n">3</span>
                        <span class="sm-lbl">الرفع</span>
                    </span>
                </div>
            </div>
        </header>

        <!-- ══════════════════════════════════════
             MAIN CONTENT
        ══════════════════════════════════════ -->
        <div class="ce-body">

            <!-- ── PHASE: idle ── -->
            <transition name="fade" mode="out-in">
            <div v-if="phase === 'idle'" class="ce-idle" key="idle">

                <div class="preview-card">
                    <div class="preview-img-wrap">
                        <img v-if="template.preview_image"
                             :src="'/storage/' + template.preview_image"
                             :alt="template.name" class="preview-img">
                        <div v-else class="preview-placeholder">🎨</div>
                    </div>
                    <div class="preview-label">{{ template.name }}</div>
                </div>

                <div class="idle-content">
                    <div class="idle-badge">✨ تصميم احترافي مع كانفا</div>
                    <h1 class="idle-title">جاهز للبدء؟</h1>
                    <p class="idle-sub">
                        سيفتح محرر كانفا في نافذة جانبية — عدّل التصميم كما تشاء،
                        ثم حمّل الملف هنا لإضافته للسلة.
                    </p>

                    <div class="how-steps">
                        <div class="how-step">
                            <span class="hs-n">1</span>
                            <span>اضغط «افتح كانفا» أدناه</span>
                        </div>
                        <div class="how-step">
                            <span class="hs-n">2</span>
                            <span>عدّل النصوص والصور في كانفا</span>
                        </div>
                        <div class="how-step">
                            <span class="hs-n">3</span>
                            <span>نزّل الملف من كانفا (PNG أو PDF)</span>
                        </div>
                        <div class="how-step">
                            <span class="hs-n">4</span>
                            <span>ارفع الملف هنا — نطبع ونوصّل 🚀</span>
                        </div>
                    </div>

                    <button class="open-btn" @click="openCanva">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                        افتح كانفا للتصميم
                    </button>

                    <p class="tip">💡 إذا حجبت النافذة اضغط «السماح» في إشعار المتصفح</p>
                </div>
            </div>

            <!-- ── PHASE: editing (Canva popup open) ── -->
            <div v-else-if="phase === 'editing'" class="ce-editing" key="editing">
                <div class="editing-anim">
                    <div class="pulse-ring"></div>
                    <div class="pulse-ring delay"></div>
                    <div class="pulse-icon">✏️</div>
                </div>
                <h2 class="editing-title">كانفا مفتوح الآن</h2>
                <p class="editing-sub">
                    عدّل تصميمك في نافذة كانفا، ثم نزّله.
                    سنكتشف تلقائياً عند إغلاق النافذة.
                </p>

                <div class="editing-actions">
                    <button class="refocus-btn" @click="reopenCanva">
                        🔍 إظهار نافذة كانفا
                    </button>
                    <button class="skip-btn" @click="phase = 'uploading'">
                        جاهز للرفع ←
                    </button>
                </div>

                <div class="editing-preview" v-if="template.preview_image">
                    <img :src="'/storage/' + template.preview_image" :alt="template.name">
                    <span>{{ template.name }}</span>
                </div>
            </div>

            <!-- ── PHASE: uploading ── -->
            <div v-else class="ce-upload" key="uploading">

                <div class="upload-header">
                    <div class="upload-check" :class="{ direct: !canvaUrl }">
                        {{ canvaUrl ? '✓' : '📤' }}
                    </div>
                    <h2>{{ canvaUrl ? 'ممتاز! الآن ارفع تصميمك' : 'ارفع ملف تصميمك' }}</h2>
                    <p>{{ canvaUrl
                        ? 'ارفع الملف الذي نزّلته من كانفا (PNG، JPG أو PDF)'
                        : 'ارفع ملف التصميم الجاهز بأي تنسيق للإضافة إلى السلة' }}</p>
                    <!-- tip when no canva url -->
                    <div v-if="!canvaUrl" class="no-canva-tip">
                        💡 للتصميم عبر كانفا، طلب منك رابط القالب من المتجر مسبقاً.<br>
                        لا يوجد قالب كانفا مخصص لهذا المنتج بعد.
                    </div>
                </div>

                <div class="dropzone" :class="{ over: isDragging, filled: !!fileName }"
                     ref="dropzone"
                     @dragover="onDragOver" @dragleave="onDragLeave" @drop="onDrop"
                     @click="triggerPick">
                    <input type="file" accept=".jpg,.jpeg,.png,.pdf,.webp"
                           class="hidden-input" @change="pickFile">

                    <div v-if="!fileName" class="dz-empty">
                        <svg class="dz-icon" viewBox="0 0 48 48" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <path d="M28 8H12a4 4 0 0 0-4 4v24a4 4 0 0 0 4 4h24a4 4 0 0 0 4-4V20"/>
                            <polyline points="34 14 40 8 34 2"/>
                            <line x1="40" y1="8" x2="20" y2="28"/>
                        </svg>
                        <p class="dz-label">اسحب الملف هنا أو <span class="dz-link">اختر من جهازك</span></p>
                        <p class="dz-hint">PNG · JPG · PDF · WEBP — حتى 20 ميغابايت</p>
                    </div>

                    <div v-else class="dz-filled">
                        <div class="dz-file-icon">📄</div>
                        <p class="dz-file-name">{{ fileName }}</p>
                        <p class="dz-change">اضغط لتغيير الملف</p>
                    </div>
                </div>

                <div class="upload-bottom">
                    <div class="qty-row">
                        <label class="qty-label">الكمية</label>
                        <div class="qty-ctrl">
                            <button type="button" class="qb"
                                    @click="form.quantity = Math.min(999, form.quantity + 1)">+</button>
                            <input v-model.number="form.quantity" type="number"
                                   min="1" max="999" class="qi">
                            <button type="button" class="qb"
                                    @click="form.quantity = Math.max(1, form.quantity - 1)">−</button>
                        </div>
                    </div>

                    <button class="submit-btn"
                            :disabled="!form.file || form.processing"
                            @click="submit">
                        <svg v-if="form.processing" class="spin" viewBox="0 0 24 24" width="18"
                             height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="9" stroke-dasharray="30 10"/>
                        </svg>
                        <svg v-else viewBox="0 0 24 24" width="18" height="18" fill="none"
                             stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                            <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                        {{ form.processing ? 'جارٍ الإضافة…' : 'أضف إلى السلة' }}
                    </button>

                    <button v-if="canvaUrl" class="reopen-link" @click="openCanva">↩ فتح كانفا مجدداً</button>
                </div>
            </div>
            </transition>

        </div>
    </div>
</template>

<style scoped>
.ce-shell {
    display: flex; flex-direction: column; height: 100vh;
    background: var(--bg, #f5f5f5); overflow: hidden; direction: rtl;
}

/* ── top bar ── */
.ce-bar {
    display: flex; align-items: center; justify-content: space-between;
    gap: 14px; padding: 0 20px; height: 58px; flex-shrink: 0;
    background: var(--bg2, #fff); border-bottom: 1px solid var(--hair, #e8e8e8); z-index: 20;
}
.ce-bar-r, .ce-bar-l { display: flex; align-items: center; gap: 14px; }

.ce-back {
    display: inline-flex; align-items: center; gap: 6px;
    color: var(--muted); font-size: 13px; text-decoration: none;
    background: var(--glass, #f5f5f5); border: 1px solid var(--hair);
    border-radius: 9px; padding: 7px 12px; transition: color .2s;
}
.ce-back svg { transform: rotate(180deg); }
.ce-back:hover { color: var(--ink); }
.ce-info { display: flex; flex-direction: column; }
.ce-tname { font-size: 14px; font-weight: 700; color: var(--ink); line-height: 1.2; }
.ce-pname { font-size: 11px; color: var(--muted); }

.steps-mini { display: flex; align-items: center; gap: 6px; }
.sm-step { display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--muted); }
.sm-n {
    width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0;
    background: var(--glass, #f0f0f0); border: 1px solid var(--hair);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700;
}
.sm-step.active .sm-n,
.sm-step.done   .sm-n { background: var(--emerald-soft, #34d77f); color: var(--on-emerald, #031a0d); border-color: transparent; }
.sm-lbl { display: none; }
@media (min-width: 600px) { .sm-lbl { display: block; } }
.sm-line { width: 24px; height: 1px; background: var(--hair); }

/* ── body ── */
.ce-body {
    flex: 1; overflow-y: auto;
    display: flex; align-items: center; justify-content: center; padding: 32px 24px;
}

/* ═══ IDLE ═══ */
.ce-idle {
    display: grid; grid-template-columns: 340px 1fr;
    gap: 48px; align-items: center; max-width: 860px; width: 100%;
}
@media (max-width: 760px) { .ce-idle { grid-template-columns: 1fr; } }

.preview-card {
    background: var(--bg2, #fff); border: 1px solid var(--hair);
    border-radius: 20px; overflow: hidden;
}
.preview-img-wrap { aspect-ratio: 4/3; overflow: hidden; background: var(--glass); }
.preview-img { width: 100%; height: 100%; object-fit: cover; }
.preview-placeholder {
    width: 100%; height: 100%; display: flex; align-items: center;
    justify-content: center; font-size: 60px; opacity: .2;
}
.preview-label { padding: 14px 18px; font-size: 14px; font-weight: 600; border-top: 1px solid var(--hair); }

.idle-content { display: flex; flex-direction: column; gap: 18px; }
.idle-badge {
    display: inline-flex; align-self: flex-start;
    background: linear-gradient(135deg, var(--emerald-soft, #34d77f), var(--emerald-deep, #1a6b40));
    color: var(--on-emerald, #031a0d); border-radius: 999px;
    padding: 5px 14px; font-size: 12px; font-weight: 700;
}
.idle-title { font-size: 28px; font-weight: 800; margin: 0; letter-spacing: -.5px; }
.idle-sub   { font-size: 14px; color: var(--muted); line-height: 1.7; margin: 0; }

.how-steps { display: flex; flex-direction: column; gap: 10px; }
.how-step  { display: flex; align-items: center; gap: 12px; font-size: 13px; color: var(--muted); }
.hs-n {
    width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0;
    background: rgba(52,215,127,.12); border: 1.5px solid rgba(52,215,127,.3);
    color: var(--emerald-soft, #34d77f);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700;
}

.open-btn {
    display: inline-flex; align-items: center; gap: 10px;
    background: linear-gradient(150deg, var(--emerald-soft, #34d77f), var(--emerald-deep, #1a6b40));
    color: var(--on-emerald, #031a0d); border: none; border-radius: 14px;
    padding: 14px 28px; font-size: 16px; font-weight: 700;
    cursor: pointer; font-family: inherit;
    box-shadow: 0 8px 28px rgba(52,215,127,.3); transition: all .3s;
}
.open-btn:hover { transform: translateY(-2px); box-shadow: 0 14px 40px rgba(52,215,127,.4); }
.tip { font-size: 12px; color: var(--muted); margin: 0; }

/* ═══ EDITING ═══ */
.ce-editing {
    display: flex; flex-direction: column; align-items: center;
    gap: 20px; text-align: center; max-width: 480px;
}
.editing-anim { position: relative; width: 80px; height: 80px; }
.pulse-ring {
    position: absolute; inset: 0; border-radius: 50%;
    border: 3px solid var(--emerald-soft, #34d77f);
    animation: pulse 2s ease-out infinite;
}
.pulse-ring.delay { animation-delay: .7s; }
.pulse-icon {
    position: absolute; inset: 0; display: flex; align-items: center;
    justify-content: center; font-size: 32px;
}
@keyframes pulse {
    0%   { opacity: .8; transform: scale(1); }
    100% { opacity: 0;  transform: scale(1.9); }
}
.editing-title { font-size: 24px; font-weight: 800; margin: 0; }
.editing-sub   { font-size: 14px; color: var(--muted); line-height: 1.7; margin: 0; }
.editing-actions { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; }

.refocus-btn {
    background: var(--glass); border: 1.5px solid var(--hair);
    border-radius: 12px; padding: 10px 20px; font-size: 14px; font-weight: 600;
    cursor: pointer; font-family: inherit; color: var(--ink); transition: all .2s;
}
.refocus-btn:hover { border-color: var(--emerald-soft); }
.skip-btn {
    background: linear-gradient(150deg, var(--emerald-soft, #34d77f), var(--emerald-deep, #1a6b40));
    color: var(--on-emerald, #031a0d); border: none; border-radius: 12px;
    padding: 10px 24px; font-size: 14px; font-weight: 700;
    cursor: pointer; font-family: inherit;
}
.editing-preview {
    display: flex; flex-direction: column; align-items: center; gap: 8px; margin-top: 6px;
}
.editing-preview img {
    width: 160px; height: 110px; object-fit: cover;
    border-radius: 12px; border: 1px solid var(--hair);
}
.editing-preview span { font-size: 12px; color: var(--muted); }

/* ═══ UPLOADING ═══ */
.ce-upload {
    display: flex; flex-direction: column; align-items: center;
    gap: 22px; max-width: 520px; width: 100%;
}
.upload-header { text-align: center; display: flex; flex-direction: column; align-items: center; gap: 8px; }
.upload-check {
    width: 56px; height: 56px; border-radius: 50%; margin-bottom: 4px;
    background: linear-gradient(135deg, var(--emerald-soft), var(--emerald-deep));
    color: var(--on-emerald, #031a0d);
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; font-weight: 700;
}
.upload-check.direct { background: linear-gradient(135deg, #6366f1, #4f46e5); font-size: 26px; }
.upload-header h2 { font-size: 22px; font-weight: 800; margin: 0; }
.upload-header p  { font-size: 13px; color: var(--muted); margin: 0; }
.no-canva-tip {
    font-size: 12px; color: var(--muted); line-height: 1.7;
    background: var(--glass); border: 1px solid var(--hair);
    border-radius: 10px; padding: 10px 16px;
    max-width: 380px; text-align: center;
}

.dropzone {
    width: 100%; border: 2px dashed var(--hair, #e0e0e0);
    border-radius: 18px; padding: 36px 24px; text-align: center;
    cursor: pointer; background: var(--glass, #f9f9f9); transition: all .25s;
}
.dropzone:hover, .dropzone.over { border-color: var(--emerald-soft); background: rgba(52,215,127,.04); }
.dropzone.filled { border-style: solid; border-color: var(--emerald-soft); background: rgba(52,215,127,.06); }
.hidden-input { display: none; }
.dz-empty  { display: flex; flex-direction: column; align-items: center; gap: 10px; }
.dz-icon   { width: 48px; height: 48px; color: var(--muted); opacity: .4; }
.dz-label  { font-size: 15px; font-weight: 600; color: var(--ink); margin: 0; }
.dz-link   { color: var(--emerald-soft); text-decoration: underline; }
.dz-hint   { font-size: 12px; color: var(--muted); margin: 0; }
.dz-filled { display: flex; flex-direction: column; align-items: center; gap: 8px; }
.dz-file-icon { font-size: 36px; }
.dz-file-name { font-size: 14px; font-weight: 600; color: var(--ink); margin: 0; word-break: break-all; }
.dz-change    { font-size: 12px; color: var(--muted); margin: 0; }

.upload-bottom {
    width: 100%; display: flex; flex-direction: column; align-items: center; gap: 12px;
}
.qty-row  { display: flex; align-items: center; gap: 14px; }
.qty-label { font-size: 14px; font-weight: 600; color: var(--ink); }
.qty-ctrl {
    display: flex; align-items: center; gap: 6px;
    background: var(--bg2); border: 1.5px solid var(--hair);
    border-radius: 10px; padding: 4px 8px;
}
.qb {
    width: 30px; height: 30px; border-radius: 8px;
    background: var(--glass); border: 1px solid var(--hair);
    color: var(--ink); font-size: 18px; font-weight: 600;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
}
.qi {
    width: 48px; text-align: center; background: none; border: none;
    font-size: 15px; font-weight: 700; color: var(--ink);
    font-family: inherit; outline: none;
}
.submit-btn {
    width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 10px;
    background: linear-gradient(150deg, var(--emerald-soft, #34d77f), var(--emerald-deep, #1a6b40));
    color: var(--on-emerald, #031a0d); border: none; border-radius: 14px;
    padding: 15px 24px; font-size: 16px; font-weight: 700;
    cursor: pointer; font-family: inherit;
    box-shadow: 0 8px 24px rgba(52,215,127,.25); transition: all .3s;
}
.submit-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 14px 36px rgba(52,215,127,.35); }
.submit-btn:disabled { opacity: .5; cursor: default; transform: none; }
.reopen-link {
    background: none; border: none; color: var(--muted); font-size: 13px;
    cursor: pointer; font-family: inherit; text-decoration: underline; padding: 0;
}
.reopen-link:hover { color: var(--ink); }

/* transitions */
.fade-enter-active, .fade-leave-active { transition: opacity .3s, transform .3s; }
.fade-enter-from { opacity: 0; transform: translateY(16px); }
.fade-leave-to   { opacity: 0; transform: translateY(-8px); }

.spin { animation: spin .8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
