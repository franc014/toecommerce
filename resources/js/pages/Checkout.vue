<template>
    <section>
        <div class="container mx-auto py-10">
            <h1>
                Checkout for <span class="text-lg italic">{{ user.name }}</span>
            </h1>
        </div>
    </section>
</template>

<script setup lang="ts">
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { usePage } from '@inertiajs/vue3';

defineOptions({ layout: StorefrontLayout });

const page = usePage();

const user = page.props.auth.user;

console.log(user);
</script>

<style scoped></style>
