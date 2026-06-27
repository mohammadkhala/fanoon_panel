<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StoreLayout from '@/Layouts/StoreLayout.vue';

const props = defineProps({
    categories: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="المتجر" />
    <StoreLayout>

        <!-- Page header -->
        <div class="wrap">
            <div class="phead">
                <div class="crumb">
                    <Link :href="route('home')">الرئيسية</Link>
                    <span class="sep">/</span>
                    <span>المتجر</span>
                </div>
                <span class="eyebrow rv">المتجر</span>
                <h1 class="rv d1">تسوّق حسب التصنيف</h1>
                <p class="rv d2">اختر التصنيف المناسب وابدأ تصميم طلبك الخاص</p>
            </div>
        </div>

        <!-- Categories grid -->
        <div class="wrap">
            <section class="cats-section">
                <div v-if="categories.length" class="cats-grid">
                    <Link
                        v-for="cat in categories"
                        :key="cat.id"
                        :href="route('category.show', cat.slug)"
                        class="cat-card"
                    >
                        <div class="cat-icon">{{ cat.icon }}</div>
                        <div class="cat-body">
                            <h2 class="cat-name">{{ cat.name }}</h2>
                            <p v-if="cat.description" class="cat-desc">{{ cat.description }}</p>
                            <div v-if="cat.subcategories?.length" class="cat-subs">
                                <span v-for="sub in cat.subcategories" :key="sub.id" class="sub-chip">{{ sub.name }}</span>
                            </div>
                            <span class="cat-meta">
                                {{ cat.subcategories_count }} {{ cat.subcategories_count === 1 ? 'تصنيف فرعي' : 'تصنيفات فرعية' }}
                            </span>
                        </div>
                        <span class="cat-arrow">←</span>
                    </Link>
                </div>

                <p v-else class="empty-msg">لا توجد تصنيفات متاحة حالياً.</p>
            </section>
        </div>

    </StoreLayout>
</template>

<style scoped>
.cats-section {
    padding: 40px 0 80px;
}

.cats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.cat-card {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 22px 20px;
    text-decoration: none;
    color: inherit;
    transition: box-shadow .18s, border-color .18s, transform .18s;
    position: relative;
}

.cat-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,.09);
    border-color: #10b981;
    transform: translateY(-2px);
}

.cat-icon {
    font-size: 2.2rem;
    line-height: 1;
    flex-shrink: 0;
    margin-top: 2px;
}

.cat-body {
    flex: 1;
    min-width: 0;
    text-align: right;
}

.cat-name {
    font-size: 1.08rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 6px;
}

.cat-desc {
    font-size: .85rem;
    color: #6b7280;
    margin: 0 0 10px;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.cat-subs {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-bottom: 10px;
    justify-content: flex-end;
}

.sub-chip {
    background: #f0fdf4;
    color: #15803d;
    font-size: .73rem;
    padding: 2px 9px;
    border-radius: 99px;
    border: 1px solid #bbf7d0;
}

.cat-meta {
    font-size: .78rem;
    color: #9ca3af;
}

.cat-arrow {
    color: #10b981;
    font-size: 1.1rem;
    align-self: center;
    flex-shrink: 0;
    transition: transform .18s;
}

.cat-card:hover .cat-arrow {
    transform: translateX(-4px);
}

.empty-msg {
    text-align: center;
    color: var(--muted, #9ca3af);
    padding: 80px 0;
    font-size: 1rem;
}

/* RTL */
[dir="rtl"] .cat-arrow {
    transform: scaleX(-1);
}
[dir="rtl"] .cat-card:hover .cat-arrow {
    transform: scaleX(-1) translateX(-4px);
}
</style>
