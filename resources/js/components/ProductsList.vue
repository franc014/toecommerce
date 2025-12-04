<script lang="ts" setup>
import { Product } from '../types';
import ProductCard from './ProductCard.vue';

const props = defineProps<{
    products: Product[];
}>();

const { products } = props;
</script>
<template>
    <ul class="products-grid pb-20">
        <li v-for="product in products" :key="product.id">
            <ProductCard :product="product" />
        </li>
    </ul>
</template>
