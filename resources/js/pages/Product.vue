<template>
    <section class="mt-52 pb-20 md:mt-20">
        <AppHead :metaTags="metaTags" :company="company" />
        <div class="wrapper">
            <section class="product-grid">
                <div>
                    <ProductGallery :images="product.images" />
                </div>
                <div class="space-y-10">
                    <h1 class="font-serif2">{{ product.title }}</h1>
                    <hr class="border-b border-dashed border-zinc-400" />
                    <div class="border-b border-dashed border-zinc-400 pb-8 text-xl tracking-wide" v-if="product.summary">{{ product.summary }}</div>
                    <p class="flex items-baseline gap-4 space-x-1">
                        <span class="border-r border-dashed border-zinc-600 pr-4 text-3xl font-bold tracking-wide">{{
                            product.price_in_dollars
                        }}</span>
                        <span class="text-2xl"> + Impuestos: </span>
                        <span class="text-xl" v-if="product.has_taxes">{{ product.taxes }}</span>
                        <span class="text-xl" v-else>Sin impuestos</span>
                    </p>
                    <div class="flex items-center gap-4">
                        <AddToCart :product="product" />
                        <ProductVariants :variants="product.variants" v-if="product.has_variants" />
                    </div>

                    <Accordion :collapsible="true" default-value="item-1">
                        <AccordionItem value="item-1">
                            <AccordionTrigger class="rounded-md bg-zinc-100 px-4 text-2xl">Más detalles</AccordionTrigger>
                            <AccordionContent>
                                <div v-html="product.description" class="for-prose"></div>
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>

                    <AddToCart :product="product" />
                </div>
            </section>
        </div>
    </section>
    <section class="has-section-divider-top relative z-[1] bg-orange-100 pt-30 pb-10">
        <SectionDivider backgroundColor="fill-zinc-50" />
        <div class="wrapper relative z-[2]">
            <h2 class="separator-border mb-20 max-w-fit space-y-20 font-serif2">Productos relacionados</h2>
            <ProductsList :products="relatedProducts" />
        </div>
        <BackgroundDecoration />
    </section>
</template>

<script setup lang="ts">
import AddToCart from '@/components/AddToCart.vue';
import AppHead from '@/components/AppHead.vue';
import ProductGallery from '@/components/ProductGallery.vue';
import ProductsList from '@/components/ProductsList.vue';
import ProductVariants from '@/components/ProductVariants.vue';
import SectionDivider from '@/components/SectionDivider.vue';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { Company, Product } from '@/types';
import { usePage } from '@inertiajs/vue3';

import BackgroundDecoration from '@/components/BackgroundDecoration.vue';

defineOptions({ layout: StorefrontLayout });

const page = usePage();

const product = page.props.product as Product;
const relatedProducts = page.props.relatedProducts as Product[];
const metaTags = page.props.metatags as any;
const company = page.props.company as Company;

console.log(metaTags);
</script>

<style scoped></style>
