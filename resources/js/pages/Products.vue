<template>
    <section class="mt-40 md:mt-0">
        <div class="wrapper mr-2 space-y-10">
            <Banner :title="heading" :pre-header="message" />
            <ProductsList :products="products" :paginationLinks="paginationLinks" />
        </div>
    </section>
</template>

<script setup lang="ts">
import Banner from '@/components/Banner.vue';
import ProductsList from '@/components/ProductsList.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { PageComponentContent, PageComponents } from '@/types';

import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({ layout: StorefrontLayout });

const page = usePage();
const paginated = page.props.products as { data: any[]; links: string[] };
const products = paginated.data;
const paginationLinks = paginated.links;

const components = page.props.components as PageComponents;

const intro = computed(() => {
    return components['ProductsIntro'].content as PageComponentContent;
});

const heading = computed(() => {
    return intro.value.heading[0].content;
});
const message = computed(() => {
    return intro.value.paragraph[0].content;
});
</script>

<style scoped></style>
