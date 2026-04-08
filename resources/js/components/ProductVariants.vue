<template>
    <Dialog :modal="true">
        <DialogTrigger
            class="flex cursor-pointer items-center justify-center gap-2 rounded-md bg-sky-500 px-3 py-2 text-center text-sm text-zinc-100 hover:bg-sky-400"
        >
            <SwatchBook class="h-4 w-4" />
            Variantes
        </DialogTrigger>

        <DialogScrollContent class="dialog-content bg-zinc-300 sm:max-w-[425px] md:min-w-[800px]">
            <DialogHeader class="mb-4">
                <DialogTitle class="tracking-wide">Variantes de producto</DialogTitle>
                <DialogDescription class="tracking-wider">Puedes agregar variantes de producto a tu carrito </DialogDescription>
            </DialogHeader>

            <div class="variants-grid overflow-y-auto">
                <VariantCard :variant="variant" v-for="variant in variants" :key="variant.id" />
            </div>
        </DialogScrollContent>
    </Dialog>
</template>

<script setup lang="ts">
import { ProductVariant } from '../types';

import { Dialog, DialogDescription, DialogHeader, DialogScrollContent, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { SwatchBook } from 'lucide-vue-next';
import VariantCard from './VariantCard.vue';
defineProps<{
    variants: ProductVariant[];
}>();
</script>

<style scoped>
.dialog-overlay {
    background: rgba(232, 169, 81, 0.2);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: grid;
    place-items: center;
    overflow-y: auto;
}

.dialog-content {
    min-width: 300px;
    background: white;
    padding: 30px;
    border-radius: 4px;
}
</style>
