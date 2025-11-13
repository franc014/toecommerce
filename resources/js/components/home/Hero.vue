<template>
    <section class="feature-v4">
        <div class="mx-auto w-[calc(100%_-_2.5rem)] max-w-lg md:max-w-3xl lg:w-[calc(100%_-_4rem)] lg:max-w-5xl xl:max-w-7xl">
            <div class="feature-v4__grid grid grid-cols-12 items-center gap-5 lg:gap-8">
                <div class="relative z-[1] col-span-12 lg:col-span-5">
                    <div
                        class="mb-1.5 max-w-fit border-b border-dashed border-b-orange-500 pb-1 text-xs font-bold tracking-widest text-orange-700 uppercase lg:mb-3 lg:text-sm"
                    >
                        Ropa para tu mascota
                    </div>

                    <div class="space-y-4">
                        <h1 class="feature-v4__text-offset@md font-serif text-5xl">Porque su estilo sí que importa...</h1>
                        <p class="text-lg tracking-wide">Deleítate y deleita a tu mascota con los mejores looks de moda</p>
                    </div>

                    <div class="mt-3 lg:mt-5">
                        <div class="flex flex-wrap items-center gap-3 lg:gap-5">
                            <a
                                href="#0"
                                class="relative inline-flex cursor-pointer items-center justify-center rounded-md bg-orange-700 px-4 py-2 text-[1em] leading-tight whitespace-nowrap text-white no-underline shadow-md transition-all duration-200 hover:bg-orange-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-700"
                                >Todos los productos</a
                            >
                            <a href="#0" class="text-inherit">Descubre más...</a>
                        </div>
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-7">
                    <figure>
                        <img class="block h-[650px] w-full object-cover" :src="heroImage" alt="Image description" />
                    </figure>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
const { heroImage } = defineProps<{
    heroImage: string;
}>();
</script>

<style scoped></style>
