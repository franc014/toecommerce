<template>
    <section class="section-spacing has-section-divider-bottom relative bg-orange-100">
        <div class="wrapper space-y-20">
            <h2 class="text-center font-serif2">Productos recientes</h2>

            <div class="latest-products-grid">
                <div class="products-grid">
                    recent products
                    <!--  <ul v-for="product in latestProducts">
                        <ProductCard :product="product" />
                    </ul> -->
                </div>
                <div class="feature-product-video">
                    <FeatureProductVideo />
                </div>
            </div>
        </div>
        <SectionDivider backgroundColor="fill-sky-200" />
    </section>
</template>

<script setup lang="ts">
import SectionDivider from '@/components/SectionDivider.vue';
import FeatureProductVideo from '@/components/home/FeatureProductVideo.vue';
</script>
