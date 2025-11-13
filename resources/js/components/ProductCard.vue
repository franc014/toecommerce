<template>
    <li>
        <div class="prod-card-v2">
            <a class="prod-card-v2__img-link rounded-lg shadow-md" aria-label="Go to product" :href="productUrl">
                <figure>
                    <img class="aspect-square object-cover" :src="featureImage" :alt="product.title" />
                    <img class="aspect-square object-cover" :src="mainImage" :alt="product.title" aria-hidden="true" />
                </figure>
            </a>

            <div class="flex flex-col items-center space-y-8 p-5 text-center">
                <h1 class="text-lg lg:text-2xl">
                    <a :href="productUrl" class="product-card-v2__title">{{ product.title }}</a>
                </h1>

                <div class="my-1 space-y-2 lg:my-1.5">
                    <p class="prod-card-v2__price">{{ product.price_in_dollars }}</p>
                    <Badge variant="secondary" class="border border-zinc-400 bg-orange-200" v-if="product.dropping_stock">
                        <span class="tracking-wider">Se está agotando</span>
                    </Badge>
                </div>

                <div>
                    <div class="flex items-center gap-4">
                        <QuantityHandler :updateCart="updateCart" v-on:updateQuantity="setQuantity" :quantity="qty" />
                        <ProductVariants :variants="product.variants" v-if="product.has_variants" />
                    </div>
                </div>
            </div>
        </div>
    </li>
</template>

<script lang="ts" setup>
import { Badge } from '@/components/ui/badge';
import { useCartItemQuantity } from '@/composables/useCartItemQuantity';
import { useCartStore } from '@/stores/cartStore';
import { Product } from '../types';
import ProductVariants from './ProductVariants.vue';
import QuantityHandler from './QuantityHandler.vue';
const { product } = defineProps<{ product: Product }>();
const { slug } = product;
const { images } = product;

const mainImage = images[0];
const featureImage = images[1];

const { qty, setQuantity } = useCartItemQuantity(product.slug);

const cartStore = useCartStore();

const productUrl = `/products/${slug}`;

function updateCart() {
    cartStore.addOrUpdateItem({
        ui_cart_id: cartStore.id,
        product_id: product.id,
        quantity: qty.value,
        purchasable_type: 'product',
    });
}
</script>

<style scoped></style>
