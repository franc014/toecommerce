<script lang="ts" setup>
import Header from '../components/Header.vue';
</script>
<template>
    <Header />
    <main class="bg-indigo-50">
        <slot />
    </main>
</template>

<style scoped></style>
