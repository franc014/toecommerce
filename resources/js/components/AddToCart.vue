<template>
    <QuantityHandler :updateCart="updateCart" v-on:updateQuantity="setQuantity" :quantity="qty" />
</template>

<script setup lang="ts">
import { useCartItemQuantity } from '@/composables/useCartItemQuantity';
import { useCartStore } from '@/stores/cartStore';
import { Product } from '@/types';
import QuantityHandler from './QuantityHandler.vue';

const { product } = defineProps<{ product: Product }>();
const cartStore = useCartStore();
const { qty, setQuantity } = useCartItemQuantity(product.slug);

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
