<template>
    <div class="flex items-center gap-2">
        <Quantity @update:modelValue="setQuantity" class="w-32" :modelValue="quantity" />
        <Button class="bg-orange-500 tracking-wider text-zinc-100 hover:bg-orange-400" @click="updateCart">
            <CirclePlus class="h-5 w-5" /> Añadir
        </Button>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { CirclePlus } from 'lucide-vue-next';

import Quantity from './Quantity.vue';

defineProps<{ updateCart: Function; quantity: number }>();

const emits = defineEmits<{ updateQuantity: [quantity: number] }>();

function setQuantity(quantity: number) {
    emits('updateQuantity', quantity);
}
</script>

<style scoped></style>
