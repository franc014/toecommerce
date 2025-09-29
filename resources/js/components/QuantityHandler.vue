<template>
    <Quantity @update:modelValue="setQuantity" class="w-32" />
    <Button class="bg-emerald-500 tracking-wider text-zinc-100 hover:bg-emerald-400" @click="updateCart">
        <DiamondPlus class="h-5 w-5" /> Añadir
    </Button>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { DiamondPlus } from 'lucide-vue-next';

import Quantity from './Quantity.vue';

defineProps<{ updateCart: Function }>();

const emits = defineEmits<{ updateQuantity: [quantity: number] }>();

function setQuantity(quantity: number) {
    emits('updateQuantity', quantity);
}
</script>

<style scoped></style>
