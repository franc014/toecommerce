<template>
    <section class="space-y-20">
        <div class="wrapper space-y-10">
            <Banner :title="bannerTitle" pre-header="Colección" />
            <ProductsList :products="products" />
        </div>
    </section>
</template>

<script setup lang="ts">
import Banner from '@/components/Banner.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { Collection, Product } from '@/types';

import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import ProductsList from '@/components/ProductsList.vue';

defineOptions({ layout: StorefrontLayout });

const page = usePage();
const collection = page.props.collection as Collection;

const bannerTitle = computed(() => {
    return `${collection.title}`;
});

const products = page.props.products as Product[];
</script>

<style scoped></style>
