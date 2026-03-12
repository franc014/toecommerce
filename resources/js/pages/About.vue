<template>
    <section class="mt-40 md:mt-10" :class="{ 'md:mt-12': discount_display_config.show_message }">
        <AppHead :metaTags="metaTags" :company="company" />
        <Story :content="storyContent" />
        <VideoPresentation :content="videoContent" />
        <Values :content="valuesContent" />
    </section>
</template>

<script setup lang="ts">
import AppHead from '@/components/AppHead.vue';
import Story from '@/components/about/Story.vue';
import Values from '@/components/about/Values.vue';
import VideoPresentation from '@/components/about/VideoPresentation.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { Company, DiscountDisplay, Metatags, PageComponentContent, PageComponents } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const discount_display_config = page.props.discountsDisplayConfig as DiscountDisplay;
const metaTags = page.props.metatags as Metatags;
const company = page.props.company as Company;

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
