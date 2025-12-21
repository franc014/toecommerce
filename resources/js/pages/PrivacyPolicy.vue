<template>
    <PrivacyPolicyContent :content="privacyContent" />
</template>

<script setup lang="ts">
import PrivacyPolicyContent from '@/components/privacy_policy/PrivacyPolicyContent.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { PageComponentContent, PageComponents } from '@/types';
defineOptions({ layout: StorefrontLayout });

import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
const page = usePage();
const components = page.props.components as PageComponents;

const privacyContent = computed(() => {
    return components['PrivacyPolicyContent'].content as PageComponentContent;
});
</script>

<style scoped></style>
