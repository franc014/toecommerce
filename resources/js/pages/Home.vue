<template>
    <Hero />
</template>

<script setup lang="ts">
import Hero from '@/components/Hero.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';

defineOptions({ layout: StorefrontLayout });
</script>

<style scoped></style>
