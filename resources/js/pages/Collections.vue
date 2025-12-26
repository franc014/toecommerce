<template>
    <section class="mt-40 space-y-20 md:mt-0">
        <div class="wrapper space-y-10 pb-30">
            <Banner title="Colecciones" pre-header="Descubre" />
            <CollectionsGallery :collections="collections" />
        </div>
    </section>
</template>

<script setup lang="ts">
import Banner from '@/components/Banner.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { Collection } from '@/types';

import { usePage } from '@inertiajs/vue3';

import CollectionsGallery from '@/components/CollectionsGallery.vue';

defineOptions({ layout: StorefrontLayout });

const page = usePage();
const collections = page.props.collections as Collection[];
</script>

<style scoped></style>
