<template>
    <section class="mt-40 space-y-20 md:mt-0">
        <div class="wrapper space-y-10 pb-30">
            <Banner :title="heading" :pre-header="message" />
            <CollectionsGallery :collections="collections" />
        </div>
    </section>
</template>

<script setup lang="ts">
import Banner from '@/components/Banner.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { Collection, PageComponentContent, PageComponents } from '@/types';

import { usePage } from '@inertiajs/vue3';

import CollectionsGallery from '@/components/CollectionsGallery.vue';
import { computed } from 'vue';

defineOptions({ layout: StorefrontLayout });

const page = usePage();
const components = page.props.components as PageComponents;
const collections = page.props.collections as Collection[];

const intro = computed(() => {
    return components['CollectionsIntro'].content as PageComponentContent;
});

const heading = computed(() => {
    return intro.value.heading[0].content;
});
const message = computed(() => {
    return intro.value.paragraph[0].content;
});
</script>

<style scoped></style>
