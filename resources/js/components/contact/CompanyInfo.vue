<template>
    <dl class="details-list details-list--rows w-11/12 md:w-full">
        <div class="details-list__item py-5 lg:py-8">
            <dt class="mb-1.5 font-bold lg:mb-2">Dirección</dt>
            <dd class="leading-snug">{{ company.address }}</dd>
        </div>

        <div class="details-list__item py-5 lg:py-8">
            <dt class="mb-1.5 font-bold lg:mb-2">Email</dt>
            <dd>
                <a href="mailto:webmaster@example.com" class="text-indigo-700 underline">{{ company.email }}</a>
            </dd>
        </div>

        <div class="details-list__item py-5 lg:py-8">
            <dt class="mb-1.5 font-bold lg:mb-2">Teléfono</dt>
            <dd class="leading-snug">
                <p>
                    <a href="tel:+44 7656 6455" class="text-indigo-700 underline">{{ company.phone }}</a>
                </p>
            </dd>
        </div>
    </dl>
</template>

<script setup lang="ts">
import { Company } from '@/types';

defineProps<{
    company: Company;
}>();
</script>

<style scoped></style>
