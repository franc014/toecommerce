<template>
    <div class="video-feature rounded-lg px-3 pt-56 pb-3 lg:px-5 lg:pt-80 lg:pb-5">
        <div class="video-feature__box-wrapper flex md:justify-end">
            <div class="video-feature__box max-w-full basis-full rounded">
                <div class="p-5 lg:p-8">
                    <h3 class="mb-2 text-2xl font-bold">{{ title }}</h3>
                    <p class="text-base opacity-60">{{ message }}</p>
                </div>

                <a class="video-feature__link p-5 leading-tight lg:p-8" :href="cta.link">
                    <span>{{ cta.label }}</span>

                    <svg
                        class="video-feature__link-icon icon inline-block h-[16px] w-[16px] shrink-0 fill-current leading-none text-inherit"
                        viewBox="0 0 16 16"
                        aria-hidden="true"
                        fill="none"
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                    >
                        <line x1="5" y1="2" x2="11" y2="8" />
                        <line x1="11" y1="8" x2="5" y2="14" />
                    </svg>
                </a>
            </div>
        </div>

        <figure class="video-feature__video-wrapper" aria-hidden="true">
            <img :src="image" :alt="title" />
            <video autoplay loop muted playsInline :src="video"></video>
        </figure>
    </div>
</template>

<script setup lang="ts">
const { title, message, image, video } = defineProps<{
    title: string;
    message: string;
    image: string;
    video: string;
    cta: object | any;
}>();
</script>

<style scoped></style>
