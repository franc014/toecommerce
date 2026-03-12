<template>
    <Head>
        <title>{{ metaTags.title }}</title>
        <meta name="description" :content="metaTags.description" />
        <meta name="author" :content="company.name" />
        <meta property="og:site_name" :content="company.name" />
        <meta property="og:locale" content="es_ES" />
        <meta property="og:title" :content="metaTags.og_title" />
        <meta property="og:description" :content="metaTags.og_description" />
        <meta property="og:image" :content="metaTags.og_image" />
        <meta property="twitter:card" :content="metaTags.twitter_card" />
        <meta property="twitter:title" :content="metaTags.twitter_title" />
        <meta property="twitter:description" :content="metaTags.twitter_description" />
        <meta property="twitter:image" :content="metaTags.twitter_image" />

        <component is="script" type="application/ld+json" v-if="metaTags.schemaOrg">
            {{ metaTags.schemaOrg }}
        </component>
    </Head>
</template>

<script setup lang="ts">
import { Company } from '@/types';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    metaTags: any;
    company: Company;
}>();
</script>

<style scoped></style>
