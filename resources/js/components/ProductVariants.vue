<template>
    <Dialog>
        <DialogTrigger class="flex cursor-pointer items-center gap-2 rounded-md bg-indigo-500 px-3 py-2 text-sm text-zinc-100 hover:bg-indigo-400">
            <SwatchBook class="h-4 w-4" />
            Variantes
        </DialogTrigger>

        <DialogContent class="max-h-[90dvh] min-w-[800px] sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Variantes de producto</DialogTitle>
                <DialogDescription> Puedes agregar variantes de producto a tu carrito </DialogDescription>
            </DialogHeader>

            <div class="variants-grid overflow-y-auto">
                <VariantCard :variant="variant" v-for="variant in variants" :key="variant.id" />
            </div>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { ProductVariant } from '../types';

import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { SwatchBook } from 'lucide-vue-next';
import VariantCard from './VariantCard.vue';
defineProps<{
    variants: ProductVariant[];
}>();
</script>

<style scoped>
.variants-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}
</style>
