<script lang="ts" setup>
import Pagination from '@/components/Pagination.vue';
import ProductCard from '@/components/ProductCard.vue';
import { Product } from '../types';

const props = defineProps<{
    products: Product[];
    paginationLinks: string[];
}>();

const { products, paginationLinks } = props;

console.log(paginationLinks);
</script>
<template>
    <ul class="products-grid pb-10">
        <li v-for="product in products" :key="product.id">
            <ProductCard :product="product" />
        </li>
    </ul>
    <div class="mx-auto flex flex-col items-center pb-30">
        <Pagination :links="paginationLinks" />
    </div>
</template>
