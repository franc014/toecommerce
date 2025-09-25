<template>
    <section>
        <div class="wrapper space-y-6 py-10">
            <h1>Productos</h1>
            <ProductsList :products="products" />
        </div>
    </section>
</template>

<script setup lang="ts">
import ProductsList from '@/components/ProductsList.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';

import { usePage } from '@inertiajs/vue3';
import { Product } from '../types/index';

defineOptions({ layout: StorefrontLayout });

const page = usePage();
const products = page.props.products as Product[];

document.addEventListener('inertia:start', (event) => {
    console.log(`Starting a visit to ${event.detail.visit.url}`);
});

//usePoll(2000);
</script>

<style scoped></style>
