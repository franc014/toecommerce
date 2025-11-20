<template>
    <div>
        <h1>Recent Products</h1>
    </div>
</template>

<script setup lang="ts">
const { content } = defineProps<{
    content: object;
}>();

console.log(content);
</script>

<style scoped></style>
