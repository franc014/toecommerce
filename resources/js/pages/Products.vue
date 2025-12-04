<template>
    <section class="space-y-20">
        <div class="wrapper space-y-10">
            <Banner title="Nuestros Productos" pre-header="Compra" />
            <ProductsList :products="products" />
        </div>
    </section>
</template>

<script setup lang="ts">
import Banner from '@/components/Banner.vue';
import ProductsList from '@/components/ProductsList.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';

import { usePage } from '@inertiajs/vue3';
import { Product } from '../types/index';

defineOptions({ layout: StorefrontLayout });

const page = usePage();
const products = page.props.products as Product[];
</script>

<style scoped></style>
