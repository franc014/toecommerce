<template>
    <div class="flex items-center justify-center bg-linear-to-l from-orange-600 via-orange-100 to-sky-400 py-3">
        <p class="font-bold tracking-wider">{{ message }} ❤️</p>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    message: string;
}>();
</script>

<style scoped></style>
