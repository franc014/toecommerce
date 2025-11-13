<template>
    <section class="bg-orange-50 py-20">
        <div class="wrapper space-y-20">
            <h1 class="text-center font-serif">Productos recientes</h1>

            <div class="latest-products-grid">
                <div class="products-grid">
                    <ul v-for="product in latestProducts">
                        <ProductCard :product="product" />
                    </ul>
                </div>
                <div class="feature-product-video">
                    <FeatureProductVideo />
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import ProductCard from '@/components/ProductCard.vue';
import FeatureProductVideo from '@/components/home/FeatureProductVideo.vue';
import { Product } from '@/types';

const { latestProducts } = defineProps<{ latestProducts: Product[] }>();
</script>

<style scoped></style>
