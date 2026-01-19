<template>
    <section class="mb-6 flex flex-col gap-4 border-b border-dashed border-zinc-400 px-4 py-4" v-if="!cartStore.isEmpty">
        <p class="flex items-baseline gap-4 text-base font-bold">
            Total sin impuestos: <span class="text-xl tracking-wider">{{ cartStore.aggregation.total_without_taxes_in_dollars }}</span>
        </p>
        <p class="flex items-baseline gap-4 text-base font-bold">
            Total con impuestos: <span class="text-xl tracking-wider">{{ cartStore.aggregation.total_with_taxes_in_dollars }}</span>
        </p>

        <p class="text-bas4 flex items-baseline gap-6 font-bold">
            Impuestos: <span class="text-xl tracking-wider">{{ cartStore.aggregation.total_computed_taxes_in_dollars }}</span>
        </p>
        <p class="text-bas4 flex items-baseline gap-6 font-bold">
            Total: <span class="text-xl tracking-wider">{{ cartStore.aggregation.total_in_dollars }}</span>
        </p>
    </section>
</template>

<script setup lang="ts">
import { useCartStore } from '@/stores/cartStore';
const cartStore = useCartStore();
</script>

<style scoped></style>
