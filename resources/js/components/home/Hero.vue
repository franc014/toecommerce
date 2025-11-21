<template>
    <section class="feature-v4 has-section-divider-bottom bg-zinc-50">
        <div class="wrapper">
            <div class="feature-v4__grid grid grid-cols-12 items-center gap-5 lg:gap-8">
                <div class="relative z-[1] col-span-12 lg:col-span-5">
                    <div
                        class="mb-1.5 max-w-fit border-b border-dashed border-b-orange-500 pb-1 text-xs font-semibold tracking-widest text-orange-700 uppercase lg:mb-3 lg:text-sm"
                    >
                        {{ featureText }}
                    </div>

                    <div class="space-y-4">
                        <h1 class="feature-v4__text-offset@md font-serif2 text-6xl">{{ heading }}</h1>
                        <p class="text-lg tracking-wide">{{ message }}</p>
                    </div>

                    <div class="mt-3 lg:mt-5">
                        <div class="flex flex-wrap items-center gap-3 lg:gap-5">
                            <a
                                :href="ctaA.link"
                                class="relative inline-flex cursor-pointer items-center justify-center rounded-md bg-orange-700 px-4 py-2 text-[1em] leading-tight whitespace-nowrap text-white no-underline shadow-md transition-all duration-200 hover:bg-orange-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-700"
                                >{{ ctaA.content }}</a
                            >
                            <a :href="ctaB.link" class="text-inherit">{{ ctaB.content }}</a>
                        </div>
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-7">
                    <figure>
                        <img class="block h-[650px] w-full object-cover" :src="image[0]" alt="Image description" />
                    </figure>
                </div>
            </div>
        </div>
        <SectionDivider backgroundColor="fill-orange-100" />
    </section>
</template>

<script setup lang="ts">
import { PageComponentContent } from '@/types';
import SectionDivider from '../SectionDivider.vue';

const { content } = defineProps<{
    content: PageComponentContent;
}>();

const heading = content.heading[0].content;
const message = content.paragraph[1].content;
const featureText = content.paragraph[0].content;
const ctaA = content.cta[0];
const ctaB = content.cta[1];
const image = content.image;
</script>
