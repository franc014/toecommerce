<template>
    <section class="relative z-[1] bg-zinc-50 py-8 lg:py-20">
        <div class="wrapper">
            <div class="mx-auto w-[calc(100%_-_2.5rem)] max-w-lg -skew-2 md:max-w-3xl lg:w-[calc(100%_-_4rem)] lg:max-w-5xl">
                <article
                    class="t-article-v4 skew-2 border border-dashed border-zinc-400 bg-orange-50 p-5 shadow-lg shadow-zinc-400 lg:my-10 lg:p-8 lg:px-12 lg:py-10"
                >
                    <div class="">
                        <div class="mx-auto max-w-xl text-center">
                            <p class="letter-spacing-lg mb-4 text-xs tracking-widest text-zinc-400 uppercase">Acompáñanos en este viaje</p>
                            <h1 class="font-serif2 text-5xl">Estilo, Comodidad y un Impacto Positivo</h1>
                        </div>

                        <div class="t-article-v4__divider my-8 lg:my-12" aria-hidden="true"><span></span></div>

                        <div class="for-prose mx-auto max-w-3xl prose-a:text-orange-700 prose-figcaption:text-center prose-img:mx-auto">
                            <figure
                                class="mb-5 shadow-lg shadow-zinc-200 md:float-right md:ml-5 md:w-[45%] lg:mb-6 lg:ml-6 xl:mr-[-5.25em] xl:w-1/2 2xl:ml-6"
                            >
                                <img
                                    class="w-full shadow-sm"
                                    src="https://images.unsplash.com/photo-1668934807755-2ab4339cc9e2?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                    alt="Image description here"
                                />
                            </figure>

                            <h2 class="font-serif2">Confeccionado con Amor, Diseñado para la Alegría</h2>

                            <p>
                                Toda mascota merece sentirse cómoda y lucir fantástica, y todo dueño de mascota merece comprar con confianza. Esta
                                creencia es el cimiento (la huella de pata) de Fashion Dogs. Iniciamos este viaje porque vimos la necesidad de ropa
                                para mascotas que no comprometiera la calidad, la comodidad ni el estilo.
                            </p>

                            <p>
                                Demasiado a menudo, la ropa para mascotas es una ocurrencia tardía, pero para nosotros, ¡es una expresión esencial de
                                amor! Ponemos toda nuestra pasión en crear atuendos únicos, duraderos y adorables que permitan a sus peludos miembros
                                de la familia moverse libremente, estar cómodos y expresar sus personalidades individuales.
                            </p>
                            <p>¡Bienvenido al lugar donde la moda se encuentra con los movimientos de cola!</p>
                        </div>
                    </div>
                </article>
            </div>
        </div>
        <SectionDividerPaper backgroundColor="fill-orange-300" />
    </section>
</template>

<script setup lang="ts">
import SectionDividerPaper from '@/components/SectionDividerPaper.vue';
</script>

<style scoped></style>
