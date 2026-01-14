<template>
    <section class="relative z-[1] bg-sky-700 py-8 lg:py-20">
        <div class="wrapper">
            <div class="mx-auto w-[calc(100%_-_2.5rem)] max-w-lg rotate-1 md:max-w-3xl md:rotate-2 lg:w-[calc(100%_-_4rem)] lg:max-w-5xl">
                <article
                    class="t-article-v4 border border-dashed border-zinc-400 bg-orange-50 p-5 shadow-md shadow-zinc-100/50 lg:my-10 lg:p-8 lg:px-12 lg:py-10"
                >
                    <div class="-rotate-1 py-10 md:-rotate-2">
                        <div class="mx-auto max-w-xl text-center">
                            <p class="letter-spacing-lg mb-4 text-xs tracking-widest text-zinc-700 uppercase">{{ message }}</p>
                            <h1 class="font-serif2 text-5xl">{{ heading }}</h1>
                        </div>

                        <div class="t-article-v4__divider my-8 lg:my-12" aria-hidden="true"><span></span></div>

                        <div class="for-prose mx-auto max-w-3xl tracking-wide prose-a:text-orange-700 prose-figcaption:text-center prose-img:mx-auto">
                            <figure
                                class="mb-5 shadow-lg shadow-zinc-100/50 md:float-right md:ml-5 md:w-[45%] md:-skew-2 lg:mb-6 lg:ml-6 xl:mr-[-5.25em] xl:w-1/2 2xl:ml-6"
                            >
                                <img class="w-full shadow-sm" :src="image" />
                            </figure>

                            <div v-html="storyBody" />
                        </div>
                    </div>
                </article>
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

const heading = content.heading[0].content;
const message = content.paragraph[0].content;
const image = content.image[0]['image'];
const storyBody = content['rich-editor'][0].content;
</script>

<style scoped></style>
