<template>
    <section class="mt-40 md:mt-0">
        <Story :content="storyContent" />
        <VideoPresentation :content="videoContent" />
        <Values :content="valuesContent" />
    </section>
</template>

<script setup lang="ts">
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { usePage } from '@inertiajs/vue3';

import Story from '@/components/about/Story.vue';
import Values from '@/components/about/Values.vue';
import VideoPresentation from '@/components/about/VideoPresentation.vue';
import { PageComponentContent, PageComponents } from '@/types';
import { computed } from 'vue';

const page = usePage();

const components = page.props.components as PageComponents;

defineOptions({ layout: StorefrontLayout });

const storyContent = computed(() => {
    return components['AboutIntro'].content as PageComponentContent;
});

const videoContent = computed(() => {
    return components['VideoPresentation'].content as PageComponentContent;
});

const valuesContent = computed(() => {
    return components['Values'].content as PageComponentContent;
});
</script>

<style scoped></style>
