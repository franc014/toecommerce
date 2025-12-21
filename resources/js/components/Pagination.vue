<template>
    <div class="flex items-center gap-6">
        <component
            class="pagination-link text-xl font-semibold"
            v-for="link in links"
            :key="link.url"
            :is="link.url ? Link : 'span'"
            :href="link.url"
            v-html="link.label"
            :class="link.active ? 'text-orange-600' : 'text-sky-600'"
        />
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
const props = defineProps<{
    links: Array<any>;
}>();

const { links } = props;
</script>

<style scoped></style>
