<template>
    <QuantityHandler :updateCart="updateCart" v-on:updateQuantity="setQuantity" />
</template>

<script setup lang="ts">
import { useCartStore } from '@/stores/cartStore';
import { Product } from '@/types';
import { ref } from 'vue';
import QuantityHandler from './QuantityHandler.vue';

const { product } = defineProps<{ product: Product }>();
const cartStore = useCartStore();
const qty = ref(1);
function setQuantity(quantity: number) {
    qty.value = quantity;
}

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
