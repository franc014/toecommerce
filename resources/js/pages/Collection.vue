<template>
    <section class="mt-40 space-y-20 md:mt-0">
        <div class="wrapper space-y-10">
            <Banner :title="bannerTitle" pre-header="Colección" />
            <ProductsList :products="products" :paginationLinks="paginationLinks" />
        </div>
    </section>
</template>

<script setup lang="ts">
import Banner from '@/components/Banner.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { Collection } from '@/types';

import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import ProductsList from '@/components/ProductsList.vue';

defineOptions({ layout: StorefrontLayout });

const page = usePage();
const collection = page.props.collection as Collection;

const bannerTitle = computed(() => {
    return `${collection.title}`;
});

const paginated = page.props.products as { data: any[]; links: string[] };

const products = paginated.data;
const paginationLinks = paginated.links;
</script>

<style scoped></style>
