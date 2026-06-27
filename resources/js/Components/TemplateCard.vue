<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    template: { type: Object, required: true },
    delay: { type: String, default: '' },
});

const stars = computed(() => {
    const full = Math.round(props.template.rating);
    return '★★★★★☆☆☆☆☆'.slice(5 - full, 10 - full);
});
</script>

<template>
    <div class="tcard rv" :class="delay"><div class="tcard-in">
        <div class="tprev tp1">
            <div v-if="template.badge" class="tpop">{{ template.badge }}</div>
            <img class="tprev-img" :src="'/' + template.preview_image" :alt="template.name" loading="lazy">
        </div>
        <div class="tinfo">
            <div class="rate">{{ stars }} <span>({{ template.reviews_count }})</span></div>
            <h3>{{ template.name }}</h3>
            <p>{{ template.description }}</p>
            <div class="tfoot">
                <div><span class="pfrom">يبدأ من</span><span class="ptag">₪{{ template.price }}</span></div>
            </div>
            <div class="cardbtns">
                <Link class="addcart" :href="route('editor', { template: template.id })">
                    خصّص الدرع وأضفه للسلة <span class="bib">✎</span>
                </Link>
            </div>
        </div>
    </div></div>
</template>
