<template>
    <section class="section-spacing has-section-divider-bottom relative bg-orange-100">
        <div class="wrapper space-y-20">
            <h2 class="text-center font-serif2">{{ sectionTitle }}</h2>

            <div class="latest-products-grid">
                <div class="products-grid">
                    <ul v-for="product in products" :key="product.id">
                        <ProductCard :product="product" />
                    </ul>
                </div>
                <div class="feature-product-video">
                    <FeatureProductVideo :title="title" :message="message" :video="product.video || ''" :image="product.images[0] || ''" :cta="cta" />
                </div>
            </div>
        </div>
        <SectionDivider backgroundColor="fill-sky-200" />
    </section>
</template>

<script setup lang="ts">
import SectionDivider from '@/components/SectionDivider.vue';
import FeatureProductVideo from '@/components/home/FeatureProductVideo.vue';
import { PageComponentContent } from '@/types';
import ProductCard from '../ProductCard.vue';

const { content } = defineProps<{
    content: PageComponentContent;
}>();

const sectionTitle = content.heading[0].content;
const products = content['new-products'][0].products;
const product = content['featured-product'][0].product;
const title = content['featured-product'][0].title;
const message = content['featured-product'][0].message;
const label = content['featured-product'][0].cta_label;
const cta = {
    label,
    link: `/products/${product.slug}`,
};
console.log(product);
</script>
