<template>
    <div class="thumbslide thumbslide--vertical thumbslide--left js-thumbslide" ref="carousel">
        <div class="slideshow slideshow--transition-slide">
            <p class="sr-only">Slideshow Items</p>

            <ul class="slideshow__content">
                <li
                    v-for="(image, index) in images"
                    class="slideshow__item js-slideshow__item bg-white"
                    :class="[index == 0 ? 'slideshow__item--selected' : '']"
                    :style="{ backgroundImage: 'url(' + image + ')' }"
                    :data-thumb="image"
                    v-html="index"
                ></li>
            </ul>
        </div>

        <div class="thumbslide__nav-wrapper" aria-hidden="true">
            <nav class="thumbslide__nav">
                <ol class="thumbslide__nav-list">
                    <!-- this content will be created using JavaScript -->
                </ol>
            </nav>
        </div>
    </div>
</template>

<script setup lang="ts">
// @ts-nocheck
const { images } = defineProps<{ images: string[] }>();

import { ThumbSlideshow } from '@/lib/codyhouse/carousel';
import { onMounted, useTemplateRef } from 'vue';

const carousel = useTemplateRef('carousel');

onMounted(() => {
    new ThumbSlideshow(carousel.value);
});
</script>

<style scoped></style>
