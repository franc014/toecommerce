<template>
    <NumberField id="quantity" :default-value="1" :min="1">
        <NumberFieldContent>
            <NumberFieldDecrement />
            <NumberFieldInput />
            <NumberFieldIncrement />
        </NumberFieldContent>
    </NumberField>
</template>

<script setup lang="ts">
import { NumberField, NumberFieldContent, NumberFieldDecrement, NumberFieldIncrement, NumberFieldInput } from '@/components/ui/number-field';
/* import { ref } from 'vue';
const quantity = ref(1); */
</script>

<style scoped></style>
