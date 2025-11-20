<template>
    <!-- <Hero :heroImage="heroImage" />
    <LatestProducts :latestProducts="latestProducts" />
    <Collections />
    <OurPromise /> -->
    <component v-for="component in components" :key="component.class" :is="component.asyncComponent" :content="component.content" />
</template>

<script setup lang="ts">
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';

import { usePage } from '@inertiajs/vue3';
import { defineAsyncComponent } from 'vue';

const page = usePage();

/* const heroImage = page.props.heroImage as string;
const latestProducts = page.props.latestProducts as Product[]; */

const rawComponents = (page.props.components ?? []) as Array<{ class: string; content?: any }>;
const available = import.meta.glob('../components/pages/**/*.vue');

function resolveAsync(componentClass: string) {
    const fileName = `${componentClass}.vue`;
    const match = Object.keys(available).find((k) => k.endsWith(`/${fileName}`) || k.endsWith(fileName));
    if (match) {
        // each value in `available` is a function that returns a Promise
        return defineAsyncComponent(available[match] as () => Promise<any>);
    }

    // fallback: return a tiny stub component so app doesn't break
    return defineAsyncComponent(() =>
        Promise.resolve({
            name: 'MissingComponentStub',
            props: { content: { type: Object, default: () => ({}) } },
            template: `<div class="text-red-500">Component "${componentClass}" not found</div>`,
        }),
    );
}

const components = rawComponents.map((c) => ({
    ...c,
    asyncComponent: resolveAsync(c.class),
}));

console.log({ components });

//for each component import it dinamically

defineOptions({ layout: StorefrontLayout });
</script>

<style scoped></style>
