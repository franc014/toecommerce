<template>
    <section class="section-spacing bg-orange-50">
        <div class="wrapper space-y-20">
            <h2 class="text-center font-serif">Productos recientes</h2>

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

<style scoped>
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 2.75rem;
}
</style>
