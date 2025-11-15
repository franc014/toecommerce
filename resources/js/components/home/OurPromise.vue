<template>
    <section class="section-spacing bg-orange-50">
        <div class="wrapper flex flex-col items-center space-y-16">
            <h2 class="pb-2 text-center font-serif5">Algo más que "lindo": <span class="mt-4 block">Comfort, Calidad y Cuidado</span></h2>
            <ul class="hiw-v2__grid">
                <li class="hiw-v2__item">
                    <figure class="hiw-v2__figure mb-5 lg:mb-8">
                        <img
                            class="block w-full rounded-lg shadow-md"
                            src="https://images.unsplash.com/photo-1668934807755-2ab4339cc9e2?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                            alt="Image description"
                        />

                        <svg class="hiw-v2__arrow" viewBox="0 0 40 40">
                            <circle cx="20" cy="20" r="20" class="fill-orange-700" />
                            <polyline
                                points="29 17 20 26 11 17"
                                fill="none"
                                class="stroke-white"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                            />
                        </svg>
                    </figure>

                    <div>
                        <h2 class="mb-1 text-xl">1. Diseñado para el confort</h2>
                        <p class="text-lg text-zinc-700">
                            Los tejidos suaves y transpirables y los cortes a medida garantizan libertad de movimiento para jugar, abrazar y echarse
                            una siesta.
                        </p>
                    </div>
                </li>

                <li class="hiw-v2__item">
                    <figure class="hiw-v2__figure mb-5 lg:mb-8">
                        <img
                            class="block w-full rounded-lg shadow-md"
                            src="https://images.unsplash.com/photo-1645389305110-643a5286db05?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                            alt="Image description"
                        />

                        <svg class="hiw-v2__arrow" viewBox="0 0 40 40">
                            <circle cx="20" cy="20" r="20" class="fill-orange-700" />
                            <polyline
                                points="29 17 20 26 11 17"
                                fill="none"
                                class="stroke-white"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                            />
                        </svg>
                    </figure>

                    <div>
                        <h2 class="mb-1 text-xl">2. Durabilidad y Seguridad</h2>
                        <p class="text-lg text-zinc-700">
                            Utilizamos materiales no tóxicos y seguros para mascotas, diseñados para resistir cada paseo por el barro y cada lavado a
                            máquina.
                        </p>
                    </div>
                </li>

                <li class="hiw-v2__item">
                    <figure class="hiw-v2__figure mb-5 lg:mb-8">
                        <img
                            class="block w-full rounded-lg shadow-md"
                            src="https://images.unsplash.com/photo-1591703257157-da4ba2364b47?q=80&w=1587&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                            alt="Image description"
                        />
                    </figure>

                    <div>
                        <h2 class="mb-1 text-xl">3. Estilo para cada ocasión</h2>
                        <p class="text-lg text-zinc-700">
                            Desde suéteres y impermeables acogedores hasta pañuelos y disfraces divertidos: encuentra el look perfecto para tu peludo
                            miembro de la familia.
                        </p>
                    </div>
                </li>
            </ul>
            <Button class="max-w-fit bg-orange-700 px-8 py-6 text-xl tracking-wider text-zinc-100 hover:bg-orange-600"
                >Ver todos los productos</Button
            >
        </div>
    </section>
</template>

<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
</script>

<style scoped></style>
