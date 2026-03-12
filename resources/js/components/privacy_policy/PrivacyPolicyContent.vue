<template>
    <article v-html="privacyPolicy" class="wrapper prose py-20"></article>
</template>

<script setup lang="ts">
import { PageComponentContent } from '@/types';

const { content } = defineProps<{
    content: PageComponentContent;
}>();

const privacyPolicy = content['rich-editor'][0].content;
</script>

<style scoped></style>
