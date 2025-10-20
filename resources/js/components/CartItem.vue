<template>
    <li class="flex flex-col gap-4 border-b border-dashed border-zinc-400 px-4 py-4 last:border-b-0">
        <img :src="item.image" :alt="item.title" class="aspect-square w-16 rounded shadow-lg" />

        <div class="space-y-2">
            <h3 class="text-lg font-bold text-zinc-800">{{ item.title }}</h3>
            <div class="flex items-center gap-2" v-if="item.formatted_variation">
                <p>{{ item.formatted_variation }}</p>
            </div>
        </div>

        <div class="flex w-5/6 items-center justify-between gap-3">
            <p class="flex flex-col gap-1">
                <span class="text-xs font-bold">Precio:</span>
                {{ item.price_in_dollars }}
            </p>

            <div class="flex items-center justify-between gap-2">
                <Quantity class="w-28 p-1 text-xs" :modelValue="quantity" @update:modelValue="changeQuantity" />
            </div>
            <p>
                <span class="flex flex-col gap-1 text-xs font-bold">Total:</span>
                {{ item.total_in_dollars }}
            </p>
            <p>
                <span class="flex flex-col gap-1 text-xs font-bold">Impuesto:</span>
                {{ item.computed_taxes_in_dollars }}
            </p>

            <button @click="remove" class="cursor-pointer">
                <CloseIcon class="text-red-500 hover:text-red-700" />
            </button>
        </div>
    </li>
</template>

<script setup lang="ts">
import { CircleX as CloseIcon } from 'lucide-vue-next';
import { ref, watchEffect } from 'vue';
import { CartItem } from '../types';
import Quantity from './Quantity.vue';

import { useCartStore } from '../stores/cartStore';
const cartStore = useCartStore();

const props = defineProps<{
    item: CartItem;
}>();

const item = ref(props.item);

console.log(item);

const quantity = ref(item.value.quantity);

const changeQuantity = (value: number) => {
    quantity.value = value;

    const type = item.value.purchasable_type === 'App\\Models\\Product' ? 'product' : 'product-variant';

    cartStore.addOrUpdateItem({
        ui_cart_id: cartStore.id,
        product_id: item.value.purchasable_id,
        quantity: quantity.value,
        purchasable_type: type,
    });
};

function remove() {
    cartStore.removeItem({
        ui_cart_id: cartStore.id,
        item_id: item.value.id,
    });
}

watchEffect(() => {
    item.value = props.item;
    quantity.value = item.value.quantity;
});
</script>

<style scoped></style>
