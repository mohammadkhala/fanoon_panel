<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StoreLayout from '@/Layouts/StoreLayout.vue';

defineProps({ category: Object, subcategory: Object, products: Array });
</script>

<template>
    <Head :title="subcategory.name" />
    <StoreLayout>
        <div class="wrap">
            <section class="pg-head">
                <div class="crumb rv">
                    <Link :href="route('home')">الرئيسية</Link>
                    <span class="sep">/</span>
                    <Link :href="route('categories')">التصنيفات</Link>
                    <span class="sep">/</span>
                    <Link :href="route('category.show', category.slug)">{{ category.name }}</Link>
                    <span class="sep">/</span>
                    <span>{{ subcategory.name }}</span>
                </div>
                <div class="hero-row">
                    <span class="cat-icon">{{ subcategory.icon }}</span>
                    <div>
                        <span class="eyebrow rv">{{ category.icon }} {{ category.name }}</span>
                        <h1 class="rv d1">{{ subcategory.name }}</h1>
                        <p v-if="subcategory.description" class="rv d2">{{ subcategory.description }}</p>
                    </div>
                </div>
            </section>
        </div>

        <div class="wrap">
            <div v-if="!products.length" class="empty">لا توجد منتجات في هذا التصنيف بعد.</div>
            <div class="prods-grid">
                <Link v-for="(p, i) in products" :key="p.id"
                      :href="route('product.show', p.slug)"
                      class="prod-card rv" :class="'d' + (i % 3)">
                    <div class="prod-img-wrap">
                        <img v-if="p.cover_image" :src="'/storage/' + p.cover_image" :alt="p.name" class="prod-img">
                        <div v-else class="prod-placeholder">🖼</div>
                        <span v-if="p.badge" class="prod-badge">{{ p.badge }}</span>
                    </div>
                    <div class="prod-body">
                        <h2 class="prod-name">{{ p.name }}</h2>
                        <p v-if="p.description" class="prod-desc">{{ p.description }}</p>
                        <div class="prod-footer">
                            <div class="prod-price">
                                <span class="price-num">{{ p.price }} ₪</span>
                                <span class="tmpl-info">{{ p.templates_count }} قوالب</span>
                            </div>
                            <span class="cta">اختر التصميم ←</span>
                        </div>
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
.cat-icon { font-size: 48px; flex-shrink: 0; }

.prods-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; padding-bottom: 60px; }

.prod-card {
    background: var(--bg2); border: 1px solid var(--hair); border-radius: 22px;
    overflow: hidden; text-decoration: none; color: var(--ink);
    transition: all .35s var(--ease); display: flex; flex-direction: column;
}
.prod-card:hover { transform: translateY(-4px); border-color: rgba(52,215,127,.3); box-shadow: 0 16px 40px rgba(0,0,0,.08); }

.prod-img-wrap { aspect-ratio: 4/3; position: relative; overflow: hidden; background: var(--glass); }
.prod-img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }
.prod-card:hover .prod-img { transform: scale(1.04); }
.prod-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 48px; opacity: .25; }
.prod-badge { position: absolute; top: 12px; right: 12px; background: linear-gradient(150deg, var(--emerald-soft), var(--emerald-deep)); color: var(--on-emerald); border-radius: 999px; padding: 4px 12px; font-size: 11px; font-weight: 600; }

.prod-body { padding: 18px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
.prod-name { font-size: 16px; font-weight: 700; margin: 0; }
.prod-desc { font-size: 13px; color: var(--muted); margin: 0; line-height: 1.5; flex: 1; }
.prod-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 4px; }
.price-num { font-size: 18px; font-weight: 700; color: var(--emerald-soft); }
.tmpl-info { font-size: 11px; color: var(--muted); display: block; margin-top: 2px; }
.cta { font-size: 13px; font-weight: 600; color: var(--emerald-soft); }

.empty { text-align: center; color: var(--muted); padding: 80px; }
</style>
