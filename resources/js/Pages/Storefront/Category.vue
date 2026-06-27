<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StoreLayout from '@/Layouts/StoreLayout.vue';

defineProps({ category: Object, subcategories: Array });
</script>

<template>
    <Head :title="category.name" />
    <StoreLayout>
        <div class="wrap">
            <section class="pg-head">
                <div class="crumb rv">
                    <Link :href="route('home')">الرئيسية</Link>
                    <span class="sep">/</span>
                    <Link :href="route('categories')">التصنيفات</Link>
                    <span class="sep">/</span>
                    <span>{{ category.name }}</span>
                </div>
                <div class="hero-row">
                    <span class="cat-icon">{{ category.icon }}</span>
                    <div>
                        <span class="eyebrow rv">تصنيف رئيسي</span>
                        <h1 class="rv d1">{{ category.name }}</h1>
                        <p v-if="category.description" class="rv d2">{{ category.description }}</p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Subcategories -->
        <div class="wrap">
            <div v-if="!subcategories.length" class="empty">لا توجد تصنيفات فرعية بعد.</div>
            <div class="subs-grid">
                <Link v-for="(sub, i) in subcategories" :key="sub.id"
                      :href="route('subcategory.show', { category: category.slug, subcategory: sub.slug })"
                      class="sub-card rv" :class="'d' + (i % 3)">
                    <div class="sub-head">
                        <span class="sub-icon">{{ sub.icon }}</span>
                        <div>
                            <h2 class="sub-name">{{ sub.name }}</h2>
                            <p class="sub-count">{{ sub.products_count }} منتج</p>
                        </div>
                        <span class="arrow">←</span>
                    </div>
                    <p v-if="sub.description" class="sub-desc">{{ sub.description }}</p>
                    <div v-if="sub.sample_products?.length" class="sample-list">
                        <span v-for="p in sub.sample_products" :key="p.name" class="sample-chip">
                            {{ p.name }}
                            <span v-if="p.badge" class="sample-badge">{{ p.badge }}</span>
                        </span>
                    </div>
                </Link>
            </div>
        </div>
    </StoreLayout>
</template>

<style scoped>
.pg-head { padding: 40px 0 32px; }
.crumb { font-size: 13px; color: var(--muted); display: flex; align-items: center; gap: 6px; margin-bottom: 20px; flex-wrap: wrap; }
.crumb a { color: var(--muted); text-decoration: none; }
.crumb a:hover { color: var(--ink); }
.sep { opacity: .4; }

.hero-row { display: flex; align-items: center; gap: 20px; }
.cat-icon { font-size: 52px; line-height: 1; flex-shrink: 0; }

.subs-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px; padding-bottom: 60px; }

.sub-card {
    display: flex; flex-direction: column; gap: 12px;
    background: var(--bg2); border: 1px solid var(--hair); border-radius: 22px;
    padding: 24px; text-decoration: none; color: var(--ink);
    transition: all .35s var(--ease);
}
.sub-card:hover { transform: translateY(-3px); border-color: rgba(52,215,127,.3); box-shadow: 0 12px 32px rgba(0,0,0,.07); }

.sub-head { display: flex; align-items: center; gap: 14px; }
.sub-icon { font-size: 28px; flex-shrink: 0; }
.sub-name { font-size: 17px; font-weight: 700; margin: 0; }
.sub-count { font-size: 12px; color: var(--muted); margin: 3px 0 0; }
.arrow { font-size: 18px; color: var(--emerald-soft); margin-right: auto; transition: transform .3s; }
.sub-card:hover .arrow { transform: translateX(-4px); }
.sub-desc { font-size: 13px; color: var(--muted); margin: 0; line-height: 1.6; }

.sample-list { display: flex; flex-wrap: wrap; gap: 6px; }
.sample-chip { background: var(--glass); border: 1px solid var(--hair); border-radius: 999px; padding: 4px 12px; font-size: 12px; color: var(--muted); display: flex; align-items: center; gap: 6px; }
.sample-badge { background: rgba(52,215,127,.1); color: var(--emerald-soft); border-radius: 999px; padding: 1px 7px; font-size: 10px; border: 1px solid rgba(52,215,127,.25); }

.empty { text-align: center; color: var(--muted); padding: 60px; }
</style>
