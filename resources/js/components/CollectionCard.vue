<template>
    <a class="card-v12 rounded-lg pt-3 shadow-md lg:pt-5" href="#0" aria-label="Link description">
        <div class="relative">
            <figure class="card-v12__figure rounded-sm">
                <img
                    class="block w-full"
                    src="https://images.unsplash.com/photo-1534235261404-7625cd79bdb9?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                    alt="Image description"
                />
            </figure>

            <svg class="icon card-v12__icon inline-block h-[60px] w-[60px] shrink-0 fill-current leading-none text-inherit" viewBox="0 0 60 60">
                <g class="icon-group" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="30" x2="57" y2="30" />
                    <line x1="57" y1="30" x2="43" y2="44" />
                    <line x1="43" y1="16" x2="57" y2="30" />
                </g>
            </svg>
        </div>

        <div class="p-5 text-center lg:p-8">
            <h3 class="text-lg lg:text-2xl">Emily Ewing</h3>

            <div class="card-v12__separator mx-auto my-2 border-t border-gray-900/10 lg:my-3" role="presentation"></div>

            <p class="letter-spacing-lg text-xs text-gray-900/50 uppercase">Developer</p>
        </div>
    </a>
</template>

<script setup lang="ts"></script>

<style scoped></style>
