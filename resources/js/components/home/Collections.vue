<template>
    <section class="section-spacing has-section-divider-bottom relative bg-sky-200">
        <div class="space-y-5">
            <h2 class="text-center font-serif2">Nuestras colecciones te maravillarán</h2>
            <p class="mb-10 text-center text-xl tracking-wider">Encuentra el fit perfecto para tu mascota</p>
            <CollectionsGallery />
        </div>
        <SectionDivider backgroundColor="fill-orange-50" />
    </section>
</template>

<script setup lang="ts">
import SectionDivider from '@/components/SectionDivider.vue';
import CollectionsGallery from '../CollectionsGallery.vue';
</script>

<style scoped></style>
