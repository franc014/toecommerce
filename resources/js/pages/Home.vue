<template>
    <div>
        <Hero :content="heroContent" />
        <RecentProducts />
    </div>
    <!--    <LatestProducts />
    <Collections />
    <OurPromise /> -->
</template>

<script setup lang="ts">
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';

//import Collections from '@/components/home/Collections.vue';
import Hero from '@/components/home/Hero.vue';
import RecentProducts from '@/components/home/RecentProducts.vue';
//import LatestProducts from '@/components/home/LatestProducts.vue';
//import OurPromise from '@/components/home/OurPromise.vue';

import { PageComponentContent, PageComponents } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const components = page.props.components as PageComponents;

const heroContent = computed(() => {
    return components['Hero'].content as PageComponentContent;
});

console.log(components);

defineOptions({ layout: StorefrontLayout });
</script>

<style scoped></style>
