<template>
    <div class="thumbslide js-thumbslide" ref="carousel">
        <div class="slideshow slideshow--transition-slide">
            <p class="sr-only">Slideshow Items</p>

            <ul class="slideshow__content">
                <li
                    v-for="(image, index) in images.for_gallery"
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
const { images } = defineProps<{ images: { main: string; for_gallery: string[] } }>();

import { ThumbSlideshow } from '@/composables/useCarousel';
import { onMounted, useTemplateRef } from 'vue';

const carousel = useTemplateRef('carousel');

onMounted(() => {
    const thumbslide = new ThumbSlideshow(carousel.value);
});

//console.log(images.main, images.for_gallery[0]);
</script>

<style scoped>
@reference '../../css/app.css';
</style>
