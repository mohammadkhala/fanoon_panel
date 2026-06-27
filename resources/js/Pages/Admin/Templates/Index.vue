<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ templates: Array });

function toggle(t) {
    router.patch(route('admin.templates.toggle', t.id), {}, { preserveScroll: true });
}
function destroy(t) {
    if (confirm('حذف القالب "' + t.name + '"؟ لا يمكن التراجع.')) {
        router.delete(route('admin.templates.destroy', t.id), { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="القوالب — الإدارة" />
    <AdminLayout title="إدارة القوالب" subtitle="قوالب التصميم المرتبطة بالمنتجات وروابط كانفا">

        <div class="head">
            <div class="count">{{ templates.length }} قالب</div>
            <Link :href="route('admin.templates.create')" class="add">＋ قالب جديد</Link>
        </div>

        <p v-if="!templates.length" class="empty">لا توجد قوالب بعد. ابدأ بإضافة أول قالب.</p>

        <div v-else class="tgrid">
            <div v-for="t in templates" :key="t.id" class="tcard" :class="{ off: !t.is_active }">
                <!-- preview image -->
                <div class="tprev">
                    <img v-if="t.preview_image" :src="'/storage/' + t.preview_image" :alt="t.name">
                    <div v-else class="noimg">▦</div>
                    <!-- canva badge -->
                    <span v-if="t.canva_template_url" class="tbadge canva">كانفا ✓</span>
                    <span class="tstate" :class="t.is_active ? 'on' : 'hidden'">
                        {{ t.is_active ? 'مفعّل' : 'مخفي' }}
                    </span>
                </div>

                <!-- info -->
                <div class="tbody">
                    <div class="tname">{{ t.name }}</div>
                    <div class="tpath">
                        <span v-if="t.category_name" class="crumb">{{ t.category_name }}</span>
                        <span v-if="t.subcategory_name" class="sep">›</span>
                        <span v-if="t.subcategory_name" class="crumb">{{ t.subcategory_name }}</span>
                        <span v-if="t.product_name" class="sep">›</span>
                        <span v-if="t.product_name" class="crumb prod">{{ t.product_name }}</span>
                    </div>
                    <div v-if="!t.product_name" class="warn">⚠ غير مرتبط بمنتج</div>
                </div>

                <!-- actions -->
                <div class="tacts">
                    <Link :href="route('admin.templates.edit', t.id)" class="act edit">تعديل</Link>
                    <button class="act" @click="toggle(t)">{{ t.is_active ? 'إخفاء' : 'تفعيل' }}</button>
                    <button class="act del" @click="destroy(t)">حذف</button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
.count{color:var(--muted);font-size:14px}
.add{background:linear-gradient(150deg,var(--emerald-soft),var(--emerald-deep));color:var(--on-emerald);border:none;border-radius:12px;padding:11px 20px;font-size:14px;font-weight:600;text-decoration:none}

.tgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:18px}

.tcard{background:var(--bg2);border:1px solid var(--hair);border-radius:18px;overflow:hidden;transition:transform .3s var(--ease)}
.tcard:hover{transform:translateY(-3px)}
.tcard.off{opacity:.6}

.tprev{position:relative;aspect-ratio:4/3;background:var(--glass);display:flex;align-items:center;justify-content:center;overflow:hidden}
.tprev img{width:100%;height:100%;object-fit:cover}
.noimg{font-size:40px;color:var(--muted)}

.tbadge{position:absolute;top:10px;right:10px;font-size:11px;padding:4px 10px;border-radius:999px;backdrop-filter:blur(6px);font-weight:600}
.tbadge.canva{background:rgba(0,180,120,.85);color:#fff}

.tstate{position:absolute;top:10px;left:10px;font-size:11px;padding:4px 10px;border-radius:999px;font-weight:600}
.tstate.on{background:rgba(52,215,127,.85);color:#031a0d}
.tstate.hidden{background:rgba(0,0,0,.55);color:#fff}

.tbody{padding:14px 16px}
.tname{font-weight:700;font-size:15px}

.tpath{display:flex;align-items:center;flex-wrap:wrap;gap:3px;margin-top:6px}
.crumb{font-size:11px;color:var(--muted)}
.crumb.prod{color:var(--emerald-soft);font-weight:600}
.sep{font-size:11px;color:var(--muted);opacity:.5}

.warn{font-size:12px;color:#ff7a6b;margin-top:6px}

.tacts{display:flex;border-top:1px solid var(--hair)}
.act{flex:1;background:none;border:none;border-left:1px solid var(--hair);padding:11px;font-size:13px;color:var(--muted);cursor:pointer;font-family:inherit;text-decoration:none;text-align:center;transition:all .25s var(--ease)}
.act:last-child{border-left:none}
.act:hover{background:var(--glass);color:var(--ink)}
.act.edit{color:var(--emerald-soft)}
.act.del:hover{color:#ff7a6b}

.empty{color:var(--muted);text-align:center;padding:50px;font-size:14px}
</style>
