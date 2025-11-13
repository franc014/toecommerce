<template>
    <Hero :heroImage="heroImage" />
    <LatestProducts :latestProducts="latestProducts" />
</template>

<script setup lang="ts">
import Hero from '@/components/home/Hero.vue';
import LatestProducts from '@/components/home/LatestProducts.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { Product } from '@/types';

import { usePage } from '@inertiajs/vue3';

const page = usePage();

const heroImage = page.props.heroImage as string;
const latestProducts = page.props.latestProducts as Product[];

defineOptions({ layout: StorefrontLayout });
</script>

<style scoped></style>
