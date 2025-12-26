<template>
    <section class="mt-40 md:mt-0">
        <div class="wrapper mr-2 space-y-10">
            <Banner title="Nuestros Productos" pre-header="Compra" />
            <ProductsList :products="products" :paginationLinks="paginationLinks" />
        </div>
    </section>
</template>

<script setup lang="ts">
import Banner from '@/components/Banner.vue';
import ProductsList from '@/components/ProductsList.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';

import { usePage } from '@inertiajs/vue3';

defineOptions({ layout: StorefrontLayout });

const page = usePage();
const paginated = page.props.products as { data: any[]; links: string[] };
const products = paginated.data;
const paginationLinks = paginated.links;
</script>

<style scoped></style>
