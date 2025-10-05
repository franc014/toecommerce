<template>
    <div
        class="flex flex-col gap-2 rounded-md bg-zinc-100 p-6 text-inherit no-underline shadow-[0_0_0_1px_hsl(221_39%_11%_/_0.05),_0_0.3px_0.4px_hsl(221_39%_11%_/_0.02),_0_0.9px_1.5px_hsl(221_39%_11%_/_0.045),_0_3.5px_6px_hsl(221_39%_11%_/_0.09)] transition-all duration-300 hover:shadow-[0_0_0_1px_hsl(221_39%_11%_/_0.05),_0_0.9px_1.25px_hsl(221_39%_11%_/_0.025),_0_3px_5px_hsl(221_39%_11%_/_0.05),_0_12px_20px_hsl(221_39%_11%_/_0.09)] dark:bg-zinc-800"
    >
        <div class="h-10 w-10 rounded-full" :style="{ backgroundColor: variant.color }"></div>
        <h3 class="text-left">{{ variant.title }}</h3>
        <p class="mx-0 mt-3 mb-4 text-left text-[0.9375rem] leading-[1.58] text-zinc-700">
            Precio: <span class="font-bold">{{ variant.price_in_dollars }} </span>
        </p>
        <p class="mt-auto mb-4 text-left text-[0.9375rem] text-zinc-700">Tallas/Tamaños: {{ variant.formatted_sizes }}</p>
        <QuantityHandler :updateCart="updateCart" v-on:updateQuantity="setQuantity" />
    </div>
</template>

<script setup lang="ts">
import { ProductVariant } from '@/types';
import QuantityHandler from './QuantityHandler.vue';

const props = defineProps<{ variant: ProductVariant }>();
const { variant } = props;

function setQuantity(quantity: number) {}

function updateCart() {}
</script>

<style scoped></style>
