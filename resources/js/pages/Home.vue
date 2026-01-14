<template>
    <div>
        <AppHead :metaTags="metaTags" :company="company" />
        <Hero :content="heroContent" />
        <RecentProducts :content="recentProductsContent" />
        <Collections :content="collectionsContent" />
        <OurPromise :content="ourPromiseContent" />
    </div>
</template>

<script setup lang="ts">
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';

import Collections from '@/components/home/Collections.vue';
import Hero from '@/components/home/Hero.vue';
import RecentProducts from '@/components/home/RecentProducts.vue';

import OurPromise from '@/components/home/OurPromise.vue';

import AppHead from '@/components/AppHead.vue';
import { Company, Metatags, PageComponentContent, PageComponents } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const metaTags = page.props.metatags as Metatags;
const company = page.props.company as Company;

const components = page.props.components as PageComponents;

const heroContent = computed(() => {
    return components['Hero'].content as PageComponentContent;
});

const recentProductsContent = computed(() => {
    return components['RecentProducts'].content as PageComponentContent;
});

const collectionsContent = computed(() => {
    return components['Collections'].content as PageComponentContent;
});

const ourPromiseContent = computed(() => {
    return components['OurPromise'].content as PageComponentContent;
});

defineOptions({ layout: StorefrontLayout });
</script>

<style scoped></style>
