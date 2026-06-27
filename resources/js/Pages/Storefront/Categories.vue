<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StoreLayout from '@/Layouts/StoreLayout.vue';

defineProps({ categories: Array });
</script>

<template>
    <Head title="التصنيفات" />
    <StoreLayout>
        <!-- Hero -->
        <div class="wrap">
            <section class="pg-head">
                <div class="crumb rv"><Link :href="route('home')">الرئيسية</Link><span class="sep">/</span><span>التصنيفات</span></div>
                <span class="eyebrow rv">متجرنا</span>
                <h1 class="rv d1">تصفّح تصنيفاتنا</h1>
                <p class="rv d2">اختر التصنيف المناسب وابدأ تصميمك الفريد بسهولة</p>
            </section>
        </div>

        <!-- Categories grid -->
        <div class="wrap">
            <div class="cats-grid">
                <Link v-for="(cat, i) in categories" :key="cat.id"
                      :href="route('category.show', cat.slug)"
                      class="cat-card rv"
                      :class="'d' + (i % 3)">
                    <div class="cat-icon">{{ cat.icon }}</div>
                    <h2 class="cat-name">{{ cat.name }}</h2>
                    <p class="cat-desc">{{ cat.description }}</p>
                    <div class="cat-footer">
                        <span class="sub-list">
                            <span v-for="sub in cat.subcategories.slice(0,3)" :key="sub.id" class="sub-chip">{{ sub.name }}</span>
                            <span v-if="cat.subcategories_count > 3" class="sub-chip more">+{{ cat.subcategories_count - 3 }}</span>
                        </span>
                        <span class="arrow">←</span>
                    </div>
                </Link>
            </div>
        </div>
    </StoreLayout>
</template>

<style scoped>
.pg-head { padding: 48px 0 36px; }
.crumb { font-size: 13px; color: var(--muted); display: flex; align-items: center; gap: 6px; margin-bottom: 16px; }
.crumb a { color: var(--muted); text-decoration: none; }
.crumb a:hover { color: var(--ink); }
.sep { opacity: .4; }

.cats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; padding-bottom: 60px; }

.cat-card {
    display: flex; flex-direction: column; gap: 12px;
    background: var(--bg2); border: 1px solid var(--hair); border-radius: 24px;
    padding: 28px; text-decoration: none; color: var(--ink);
    transition: all .35s var(--ease);
    cursor: pointer;
}
.cat-card:hover { transform: translateY(-4px); border-color: rgba(52,215,127,.3); box-shadow: 0 16px 40px rgba(0,0,0,.08); }

.cat-icon { font-size: 40px; line-height: 1; }
.cat-name { font-size: 20px; font-weight: 700; margin: 0; }
.cat-desc { font-size: 13px; color: var(--muted); margin: 0; line-height: 1.6; flex: 1; }
.cat-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 8px; }
.sub-list { display: flex; flex-wrap: wrap; gap: 6px; }
.sub-chip { background: var(--glass); border: 1px solid var(--hair); border-radius: 999px; padding: 3px 10px; font-size: 11px; color: var(--muted); }
.sub-chip.more { background: rgba(52,215,127,.08); border-color: rgba(52,215,127,.2); color: var(--emerald-soft); }
.arrow { font-size: 18px; color: var(--emerald-soft); transition: transform .3s; }
.cat-card:hover .arrow { transform: translateX(-4px); }

@media (max-width: 640px) { .cats-grid { grid-template-columns: 1fr; } }
</style>
