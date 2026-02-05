<template>
    <section class="mt-52 pb-20 md:mt-20">
        <AppHead :metaTags="metaTags" :company="company" />
        <div class="wrapper">
            <section class="product-grid">
                <div>
                    <ProductGallery :images="product.images" />
                </div>
                <div class="space-y-5">
                    <h1 class="flex items-center font-serif2">
                        {{ product.title }}
                    </h1>

                    <Badge v-if="product.has_discounts" class="-skew-2 bg-orange-700 py-1 tracking-widest text-white shadow">
                        <TicketPercent />
                        Con Descuento!</Badge
                    >

                    <hr class="border-b border-dashed border-zinc-400" />
                    <div class="border-b border-dashed border-zinc-400 pb-8 text-xl tracking-wide" v-if="product.summary">{{ product.summary }}</div>
                    <div class="mb-10 gap-4">
                        <p class="mb-5 flex items-baseline gap-2">
                            <span
                                :class="{ 'has-discount': product.has_discounts }"
                                class="border-dashed border-zinc-600 pr-4 text-3xl font-bold tracking-wide"
                            >
                                {{ product.price_in_dollars }}
                            </span>

                            <span v-if="product.has_discounts" class="border-r border-dashed border-zinc-600 pr-4 text-3xl font-bold tracking-wide">{{
                                product.discounted_price_in_dollars
                            }}</span>
                            <span class="text-2xl"> + Impuestos: </span>
                            <span class="text-xl" v-if="product.has_taxes">{{ product.taxes }}</span>
                            <span class="text-xl" v-else>Sin impuestos</span>
                        </p>
                        <div class="space-y-3">
                            <p class="text-xl font-bold tracking-wide" v-if="product.has_discounts">Descuentos:</p>
                            <Badge
                                class="border border-zinc-400 bg-orange-100 text-sm"
                                v-if="product.has_discounts"
                                v-for="discount in product.discounts"
                                >{{ discount.name }} [{{ discount.percentage }}%]</Badge
                            >
                        </div>
                    </div>
                    <!--  <div class="flex items-center gap-4">
                        <AddToCart :product="product" />
                        <ProductVariants :variants="product.variants" v-if="product.has_variants" />
                    </div> -->

                    <Accordion :collapsible="true" default-value="item-1">
                        <AccordionItem value="item-1">
                            <AccordionTrigger class="rounded-md bg-zinc-100 px-4 text-2xl">Más detalles</AccordionTrigger>
                            <AccordionContent>
                                <div v-html="product.description" class="for-prose"></div>
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>

                    <div class="flex items-center gap-4">
                        <AddToCart :product="product" />
                        <ProductVariants :variants="product.variants" v-if="product.has_variants" />
                    </div>
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
import { Badge } from '@/components/ui/badge';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { Company, Metatags, Product } from '@/types';
import { usePage } from '@inertiajs/vue3';

import BackgroundDecoration from '@/components/BackgroundDecoration.vue';
import { TicketPercent } from 'lucide-vue-next';

defineOptions({ layout: StorefrontLayout });

const page = usePage();

const product = page.props.product as Product;
const relatedProducts = page.props.relatedProducts as Product[];
const metaTags = page.props.metatags as Metatags;
const company = page.props.company as Company;
</script>
