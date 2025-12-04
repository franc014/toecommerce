<template>
    <section class="hero has-section-divider-bottom relative z-[1] mt-10 bg-cover bg-center bg-no-repeat px-0 py-10 text-center lg:py-10">
        <div>
            <div class="text-md mb-3 tracking-widest text-zinc-800">{{ preHeader }}</div>
            <h1 class="mb-2 font-serif2 text-zinc-800">{{ title }}</h1>
        </div>
    </section>
</template>

<script setup lang="ts">
defineProps<{
    preHeader: string;
    title: string;
}>();
</script>

<style lang="scss" scoped></style>
