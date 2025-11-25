<template>
    <section class="section-spacing bg-orange-50">
        <div class="wrapper flex flex-col items-center space-y-4">
            <h2 class="pb-2 text-center font-serif2">{{ title }}</h2>
            <p class="mb-16 font-serif2 text-2xl font-normal text-black/80">{{ message }}</p>
            <ul class="hiw-v2__grid">
                <li class="hiw-v2__item" v-for="feature in features" :key="feature.title">
                    <figure class="hiw-v2__figure mb-5 lg:mb-8">
                        <img class="block w-full rounded-lg shadow-md" :src="feature.image" :alt="feature.title" />

                        <svg class="hiw-v2__arrow" viewBox="0 0 40 40">
                            <circle cx="20" cy="20" r="20" class="fill-orange-700" />
                            <polyline
                                points="29 17 20 26 11 17"
                                fill="none"
                                class="stroke-white"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                            />
                        </svg>
                    </figure>

                    <div>
                        <h2 class="mb-1 text-xl">{{ feature.title }}</h2>
                        <p class="text-lg text-zinc-700">
                            {{ feature.message }}
                        </p>
                    </div>
                </li>
            </ul>
            <Button class="max-w-fit bg-orange-700 px-8 py-6 text-xl tracking-wider text-zinc-100 hover:bg-orange-600">
                <Link :href="cta.link">{{ cta.content }}</Link>
            </Button>
        </div>
    </section>
</template>

<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { PageComponentContent } from '@/types';
import { Link } from '@inertiajs/vue3';

const { content } = defineProps<{
    content: PageComponentContent;
}>();

const title = content.heading[0].content;
const message = content.paragraph[0].content;
const cta = content.cta[0];
const features = content.feature;

console.log(features);
</script>

<style scoped></style>
