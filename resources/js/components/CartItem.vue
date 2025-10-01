<template>
    <li class="flex flex-col gap-4 border-b border-dashed border-zinc-400 px-4 py-4 last:border-b-0">
        <img src="https://dummyimage.com/300" alt="kkk" class="aspect-square w-16 rounded shadow-lg" />
        <div class="space-y-2">
            <h3 class="text-lg font-bold text-zinc-800">{{ item.title }}</h3>
            <!-- @if($this->cartItem->color || $this->cartItem->sizes)
            <div class="flex items-center gap-2">
                <p class="h-10 w-10 rounded-full" style="background-color: {{ $this->cartItem->color }}"></p>
                <p>{{ Arr::join($this->cartItem->sizes, ', ') }}</p>
            </div>
            @endif -->
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
                <span class="flex flex-col gap-1 text-xs font-bold">Con impuestos:</span>
                {{ item.total_with_taxes_in_dollars }}
            </p>

            xderemove
        </div>
    </li>
</template>

<script setup lang="ts">
import { ref, watchEffect } from 'vue';
import { CartItem } from '../types';
import Quantity from './Quantity.vue';

import { useCartStore } from '../stores/cartStore';
const cartStore = useCartStore();

const props = defineProps<{
    item: CartItem;
}>();

const item = ref(props.item);

console.log({ item });

const quantity = ref(item.value.quantity);

const changeQuantity = (value: number) => {
    quantity.value = value;
    console.log(item);

    cartStore.addOrUpdateItem({
        ui_cart_id: cartStore.id,
        product_id: item.value.purchasable_id,
        quantity: quantity.value,
    });
};

watchEffect(() => {
    console.log(props.item);
    item.value = props.item;
    quantity.value = item.value.quantity;
});
</script>

<style scoped></style>
