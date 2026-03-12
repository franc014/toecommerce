<template>
    <div class="card-v13">
        <span class="card-v13__figure-wrapper" href="#0">
            <figure class="card-v13__figure border border-dashed border-zinc-400">
                <img :src="feature.image" :alt="feature.title" class="h-64 w-42 object-cover" aria-hidden="true" />
            </figure>
        </span>

        <div class="mt-3 text-center lg:mt-5">
            <h3 class="text-lg lg:text-2xl">
                <a class="text-zinc-900" href="#0">{{ feature.title }}</a>
            </h3>

            <div class="card-v13__separator mx-auto my-3 lg:my-5" role="presentation"></div>

            <p class="text-sm leading-normal text-zinc-500 lg:text-lg">
                {{ feature.message }}
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    feature: any;
}>();
</script>

<style scoped></style>
