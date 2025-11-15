<template>
    <section class="feature-v4 has-section-divider-bottom bg-white">
        <div class="wrapper">
            <div class="feature-v4__grid grid grid-cols-12 items-center gap-5 lg:gap-8">
                <div class="relative z-[1] col-span-12 lg:col-span-5">
                    <div
                        class="mb-1.5 max-w-fit border-b border-dashed border-b-orange-500 pb-1 text-xs font-bold tracking-widest text-orange-700 uppercase lg:mb-3 lg:text-sm"
                    >
                        Ropa para tu mascota
                    </div>

                    <div class="space-y-4">
                        <h1 class="feature-v4__text-offset@md font-serif5 text-5xl">Porque su estilo sí que importa...</h1>
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
        <div class="section-divider">
            <svg viewBox="0 0 1920 60" aria-hidden="true">
                <polygon
                    class="fill-orange-50"
                    points="0 60 1920 60 1920 38.088 1919.861 38.146 1894.973 27.749 1870.364 35.233 1847.312 23.643 1823.857 24.446 1801.143 22.647 1778.462 20.738 1755.602 16.688 1732.438 17.902 1712.26 31.218 1691.051 36.7 1667.302 35.409 1647.36 50.53 1626.272 37.863 1605.23 35.851 1578.004 35.813 1553.283 31.227 1532.159 26.809 1510.259 28.285 1487.055 25.788 1461.236 35.114 1434.118 33.718 1407.318 34.937 1380.533 37.763 1356.528 42.856 1332.308 41.745 1308.433 33.231 1284.013 36.371 1259.249 36.572 1238.823 29.916 1220.674 14.934 1198.661 12.497 1178.485 24.023 1150.635 24.665 1126.019 31.516 1104.888 39.342 1085.026 28.173 1063.185 30.259 1039.737 22.928 1015.849 18.098 991.641 18.261 974.272 30.456 951.915 35.575 932.894 47.595 910.354 42.136 889.519 33.62 868.93 24.075 846.138 17.376 823.131 24.784 799.758 19.712 777.282 25.844 754.4 27.573 732.357 30.137 709.741 28.944 688.247 35.347 666.955 46.012 644.157 42.421 620.339 35.823 597.124 27.61 571.956 21.713 549.497 30.45 528.032 42.843 503.477 41.269 484.295 31.227 465.523 23.519 443.885 23.384 429.772 41.461 407.882 42.054 383.327 41.274 356.14 39.569 332.509 27.463 309.233 25.457 285.994 25.283 262.969 25.258 239.626 20.239 216.674 24.717 193.218 26.311 167.942 35.4 141.82 44.829 117.009 32.186 92.19 30.164 67.355 22.816 42.779 23.256 18.26 22.076 0 24.579 0 60"
                />
            </svg>
        </div>
    </section>
</template>

<script setup lang="ts">
const { heroImage } = defineProps<{
    heroImage: string;
}>();
</script>

<style scoped>
/* --------------------------------

File#: _1_section-divider
Title: Section divider
Descr: A collection of SVG section dividers.
Usage: codyhouse.co/license

-------------------------------- */
:root {
    --section-divider-width: 1920;
    --section-divider-height: 60;
    --section-divider-ratio: calc(100% * var(--section-divider-height) / var(--section-divider-width));
}

[class*='has-section-divider'] {
    position: relative;
}

.has-section-dividers {
    padding-top: var(--section-divider-ratio);
    padding-bottom: var(--section-divider-ratio);
}
.has-section-dividers .section-divider + .section-divider {
    bottom: auto;
    top: -1px;
    -webkit-transform: scaleY(-1);
    transform: scaleY(-1);
}

.has-section-divider-top {
    padding-top: var(--section-divider-ratio);
}
.has-section-divider-top .section-divider {
    bottom: auto;
    top: -1px;
    -webkit-transform: scaleY(-1);
    transform: scaleY(-1);
}

.has-section-divider-bottom {
    padding-bottom: var(--section-divider-ratio);
}

.section-divider {
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    overflow: hidden;
}
.section-divider svg {
    position: relative;
    display: block;
    height: auto;
    max-width: none;
    width: 102%;
    left: -1%;
}
</style>
