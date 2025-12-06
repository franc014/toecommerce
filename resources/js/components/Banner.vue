<template>
    <section class="hero has-section-divider-bottom relative z-[1] mt-10 bg-cover bg-center bg-no-repeat px-0 py-10 text-center lg:py-10">
        <div>
            <div class="text-md mb-3 tracking-widest text-zinc-800">{{ preHeader }}</div>
            <h1 class="mx-auto flex max-w-fit flex-col gap-2 text-center font-serif2 text-zinc-800">
                {{ title }}
                <span class="inline-flex justify-around text-zinc-800"> <Paws /> </span>
            </h1>
        </div>
        <!--  <div class="section-decoration absolute inset-0 opacity-5"></div> -->
    </section>
</template>

<script setup lang="ts">
import Paws from '@/components/Paws.vue';
defineProps<{
    preHeader: string;
    title: string;
}>();
</script>

<style scoped>
/* h1::after {
    content: '\00A0';
    background: url('/svg/paws.svg');
    width: 100%;
    max-width: 100%;
    height: 200px;
    margin-top: 10px;
    opacity: 50%;
} */
/* .section-decoration {
    background-image: url('/svg/bubbles.svg');
    background-size: 3rem;
    background-repeat: repeat;
} */
</style>
