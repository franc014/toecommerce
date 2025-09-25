<script lang="ts" setup>
import ProductCard from './ProductCard.vue';
//import { products } from '@/routes/storefront';
import { Product } from '../types';

const props = defineProps<{
    products: Product[];
}>();

const { products } = props;
</script>
<template>
    <ul class="products-grid">
        <li v-for="product in products" :key="product.id">
            <ProductCard :product="product" />
        </li>
    </ul>
</template>
