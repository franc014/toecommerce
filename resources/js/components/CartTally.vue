<template>
    <section class="mb-6 flex flex-col gap-4 border-t border-b border-dashed border-zinc-400 py-4" v-if="!cartStore.isEmpty">
        <p class="text-base font-bold">
            Total sin impuestos: <span class="italic">{{ cartStore.aggregation.total_without_taxes_in_dollars }}</span>
        </p>
        <p class="text-base font-bold">
            Total con impuestos: <span class="italic">{{ cartStore.aggregation.total_with_taxes_in_dollars }}</span>
        </p>

        <p class="text-base font-bold">
            Impuestos: <span class="italic">{{ cartStore.aggregation.total_computed_taxes_in_dollars }}</span>
        </p>
        <p class="text-base font-bold">
            Total: <span class="italic">{{ cartStore.aggregation.total_in_dollars }}</span>
        </p>
    </section>
</template>

<script setup lang="ts">
import { useCartStore } from '@/stores/cartStore';
const cartStore = useCartStore();
</script>

<style scoped></style>
