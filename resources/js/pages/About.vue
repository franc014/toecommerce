<template>
    <div>
        <Story />
        <VideoPresentation />
        <Values />
    </div>
</template>

<script setup lang="ts">
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { usePage } from '@inertiajs/vue3';

import Story from '@/components/about/Story.vue';
import Values from '@/components/about/Values.vue';
import VideoPresentation from '@/components/about/VideoPresentation.vue';

const page = usePage();

defineOptions({ layout: StorefrontLayout });
</script>

<style scoped></style>
