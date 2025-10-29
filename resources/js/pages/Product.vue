<template>
    <section>
        <div class="wrapper space-y-6 py-10">
            <h1>{{ product.title }}</h1>
            <p>{{ product.description }}</p>
        </div>
    </section>
</template>

<script setup lang="ts">
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { Product } from '@/types';
import { usePage } from '@inertiajs/vue3';

defineOptions({ layout: StorefrontLayout });

const page = usePage();

const product = page.props.product as Product;
</script>

<style scoped></style>
