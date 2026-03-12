<template>
    <p class="text-xs tracking-wide text-red-600">
        {{ props.error }}
    </p>
</template>

<script setup lang="ts">
const props = defineProps<{
    error?: string;
}>();
</script>

<style scoped></style>
