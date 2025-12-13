<template>
    <section class="has-section-divider-bottom relative bg-orange-300 py-20">
        <div class="wrapper">
            <div
                class="boxed-feature bg-white shadow-[0_0.9px_1.25px_hsl(221_39%_11%/0.025),0_3px_5px_hsl(221_39%_11%/0.05),0_12px_20px_hsl(221_39%_11%/0.09)]"
            >
                <div class="grid lg:grid-cols-2">
                    <div class="grid min-w-0 auto-rows-min content-center items-center gap-4 p-6 lg:order-2 xl:p-12">
                        <h2 class="font-serif2 text-5xl">{{ title }}</h2>

                        <p class="text-xl text-zinc-500">{{ message }}</p>
                    </div>

                    <div class="video-feature rounded-lg px-3 pt-56 pb-3 lg:px-5 lg:pt-80 lg:pb-5">
                        <div class="video-feature__box-wrapper flex h-40 md:justify-end"></div>

                        <figure class="video-feature__video-wrapper" aria-hidden="true">
                            <img src="" alt="Video Preview" />
                            <video controls muted playsInline :src="link"></video>
                        </figure>
                    </div>
                </div>
            </div>
        </div>
        <SectionDividerPaper backgroundColor="fill-orange-300" />
    </section>
</template>

<script setup lang="ts">
import SectionDividerPaper from '@/components/SectionDividerPaper.vue';
import { PageComponentContent } from '@/types';

const { content } = defineProps<{
    content: PageComponentContent;
}>();

const title = content.video[0].title;
const message = content.video[0].message;
const link = content.video[0].link;
</script>

<style scoped></style>
