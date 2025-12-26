<template>
    <section class="feature-v4 has-section-divider-bottom bg-zinc-50">
        <div class="wrapper">
            <div class="feature-v4__grid grid grid-cols-12 items-center gap-5 lg:gap-8">
                <transition appear @enter="enter" @beforeEnter="beforeEnter">
                    <div class="relative z-[1] col-span-12 mt-52 md:mt-0 lg:col-span-5">
                        <div
                            class="mb-1.5 max-w-fit border-b border-dashed border-b-orange-500 pb-1 text-xs font-semibold tracking-widest text-orange-700 uppercase lg:mb-3 lg:text-sm"
                        >
                            {{ featureText }}
                        </div>

                        <div class="space-y-6 md:space-y-4">
                            <h1 class="feature-v4__text-offset@md font-serif2 text-5xl md:text-7xl">{{ heading }}</h1>

                            <p class="text-2xl tracking-wide">{{ message }}</p>
                        </div>

                        <div class="mt-6 lg:mt-8">
                            <div class="flex flex-wrap items-center gap-3 lg:gap-5">
                                <Link
                                    prefetch
                                    :href="ctaA.link"
                                    class="relative inline-flex cursor-pointer items-center justify-center rounded-md bg-orange-700 px-4 py-2 text-xl leading-tight whitespace-nowrap text-white no-underline shadow-md transition-all duration-200 hover:bg-orange-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-700"
                                    >{{ ctaA.content }}
                                </Link>
                                <Link prefetch :href="ctaB.link" class="text-inherit">{{ ctaB.content }}</Link>
                            </div>
                        </div>
                    </div>
                </transition>

                <div class="col-span-12 lg:col-span-7">
                    <figure>
                        <img class="block h-[450px] w-full object-cover md:h-[850px]" :src="image[0]['image']" alt="Image description" />
                    </figure>
                </div>
            </div>
        </div>
        <SectionDivider backgroundColor="fill-orange-100" />
    </section>
</template>

<script setup lang="ts">
// @ts-nocheck
import { PageComponentContent } from '@/types';
import { Link } from '@inertiajs/vue3';
import SectionDivider from '../SectionDivider.vue';

import { SplitText } from 'gsap/all';

import { gsap } from 'gsap';
import { onUnmounted } from 'vue';

const { content } = defineProps<{
    content: PageComponentContent;
}>();

const heading = content.heading[0].content;
const message = content.paragraph[1].content;
const featureText = content.paragraph[0].content;
const ctaA = content.cta[0];
const ctaB = content.cta[1];
const image = content.image;

let tween;

function beforeEnter() {
    let tween = gsap.timeline();
}

function enter(el) {
    const text1 = el.childNodes[1].childNodes[0];
    const text2 = el.childNodes[1].childNodes[1];

    tween = SplitText.create(text1, {
        type: 'chars',
        autoSplit: true,
        smartWrap: true,

        onSplit: (self) => {
            return gsap.from(self.chars, {
                duration: 0.2,
                autoAlpha: 0,
                stagger: 0.08,
                delay: 0.1,
                ease: 'power2.inOut',
                onComplete: () => self.revert(),
            });
        },
    });

    tween = SplitText.create(text2, {
        type: 'chars',
        autoSplit: true,
        smartWrap: true,
        onSplit: (self) => {
            return gsap.from(
                self.chars,
                {
                    duration: 0.2,
                    autoAlpha: 0,
                    stagger: 0.08,
                    ease: 'power2.inOut',
                    onComplete: () => self.revert(),
                },
                '+=0.1',
            );
        },
    });
}

onUnmounted(() => {
    tween.revert();
});
</script>
