<template>
    <section class="gallery-v2 relative z-[1]">
        <div class="gallery-v2__gallery pb-px">
            <div class="mx-auto w-[calc(100%_-_3rem)] max-w-lg lg:max-w-5xl xl:max-w-7xl">
                <ul class="grid grid-cols-12 gap-y-6 md:gap-6 xl:gap-8">
                    <li class="gallery-v2__item col-span-12 md:col-span-6 xl:col-span-3" v-for="collection in collections">
                        <CollectionCard :collection="collection" />
                    </li>
                </ul>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { Collection } from '@/types';
import CollectionCard from './CollectionCard.vue';

const { collections } = defineProps<{
    collections: Array<Collection>;
}>();
</script>

<style scoped></style>
