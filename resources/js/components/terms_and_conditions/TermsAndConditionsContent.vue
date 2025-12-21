<template>
    <article v-html="terms" class="wrapper prose py-20"></article>
</template>

<script setup lang="ts">
import { PageComponentContent } from '@/types';

const { content } = defineProps<{
    content: PageComponentContent;
}>();

const terms = (content as any)['rich-editor'][0].content;
</script>

<style scoped></style>
