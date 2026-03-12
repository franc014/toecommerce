<template>
    <section class="section-spacing has-section-divider-bottom relative bg-sky-200">
        <div class="space-y-5">
            <h2 class="text-center font-serif2">{{ title }}</h2>
            <p class="mb-10 text-center text-xl tracking-wider">{{ message }}</p>
            <CollectionsGallery :collections="collections" />
        </div>
        <SectionDivider backgroundColor="fill-orange-50" />
    </section>
</template>

<script setup lang="ts">
import SectionDivider from '@/components/SectionDivider.vue';
import { PageComponentContent } from '@/types';
import CollectionsGallery from '../CollectionsGallery.vue';

const { content } = defineProps<{
    content: PageComponentContent;
}>();

const title = content.heading[0].content;
const message = content.paragraph[0].content;
const collections = content.collections[0].collections;
</script>

<style scoped></style>
