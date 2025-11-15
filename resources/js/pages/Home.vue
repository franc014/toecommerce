<template>
    <Hero :heroImage="heroImage" />
    <LatestProducts :latestProducts="latestProducts" />
    <Collections />
    <OurPromise />
</template>

<script setup lang="ts">
import Collections from '@/components/home/Collections.vue';
import Hero from '@/components/home/Hero.vue';
import LatestProducts from '@/components/home/LatestProducts.vue';
import OurPromise from '@/components/home/OurPromise.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { Product } from '@/types';

import { usePage } from '@inertiajs/vue3';

const page = usePage();

const heroImage = page.props.heroImage as string;
const latestProducts = page.props.latestProducts as Product[];

defineOptions({ layout: StorefrontLayout });
</script>

<style scoped></style>
