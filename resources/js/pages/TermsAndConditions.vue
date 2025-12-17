<template>
    <TermsAndConditionsContent :content="termsContent" />
</template>

<script setup lang="ts">
import TermsAndConditionsContent from '@/components/terms_and_conditions/TermsAndConditionsContent.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { PageComponentContent, PageComponents } from '@/types';
defineOptions({ layout: StorefrontLayout });

import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
const page = usePage();
const components = page.props.components as PageComponents;

const termsContent = computed(() => {
    return components['TermsAndConditionsContent'].content as PageComponentContent;
});
</script>

<style scoped></style>
