<template>
    <header class="header js-header relative py-10">
        <div class="header__container wrapper">
            <div class="header__logo">
                <Link href="/">
                    <svg width="104" height="30" viewBox="0 0 104 30">
                        <title>Go to homepage</title>
                        <path
                            d="M37.54 24.08V3.72h4.92v16.37h8.47v4zM60.47 24.37a7.82 7.82 0 01-5.73-2.25 8.36 8.36 0 01-2-5.62 8.32 8.32 0 012.08-5.71 8 8 0 015.64-2.18 8.07 8.07 0 015.68 2.2 8.49 8.49 0 012 5.69 8.63 8.63 0 01-1.78 5.38 7.6 7.6 0 01-5.89 2.49zm0-3.67c2.42 0 2.73-3 2.73-4.23s-.31-4.26-2.73-4.26-2.79 3-2.79 4.26.32 4.23 2.82 4.23zM95.49 24.37a7.82 7.82 0 01-5.73-2.25 8.36 8.36 0 01-2-5.62 8.32 8.32 0 012.08-5.71 8.4 8.4 0 0111.31 0 8.43 8.43 0 012 5.69 8.6 8.6 0 01-1.77 5.38 7.6 7.6 0 01-5.89 2.51zm0-3.67c2.42 0 2.73-3 2.73-4.23s-.31-4.26-2.73-4.26-2.8 3-2.8 4.26.31 4.23 2.83 4.23zM77.66 30c-5.74 0-7-3.25-7.23-4.52l4.6-.26c.41.91 1.17 1.41 2.76 1.41a2.45 2.45 0 002.82-2.53v-2.68a7 7 0 01-1.7 1.75 6.12 6.12 0 01-5.85-.08c-2.41-1.37-3-4.25-3-6.66 0-.89.12-3.67 1.45-5.42a5.67 5.67 0 014.64-2.4c1.2 0 3 .25 4.46 2.82V8.81h4.85v15.33a5.2 5.2 0 01-2.12 4.32A9.92 9.92 0 0177.66 30zm.15-9.66c2.53 0 2.81-2.69 2.81-3.91s-.31-4-2.81-4-2.81 2.8-2.81 4 .27 3.91 2.81 3.91zM55.56 3.72h9.81v2.41h-9.81z"
                            class="fill-gray-900"
                        />
                        <circle cx="15" cy="15" r="15" class="fill-indigo-700" />
                    </svg>
                </Link>
            </div>

            <button
                class="header__trigger js-header__trigger relative inline-flex cursor-pointer items-center justify-center rounded-md bg-white px-4 py-2 text-[1em] leading-tight whitespace-nowrap text-gray-900 no-underline shadow-md transition-all duration-200 hover:shadow focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-700 lg:hidden"
                aria-label="Toggle menu"
                aria-expanded="false"
                aria-controls="header-nav"
            >
                <i class="header__trigger-icon not-italic" aria-hidden="true"></i>
                <span>Menu</span>
            </button>

            <nav class="header__nav js-header__nav" id="header-nav" role="navigation" aria-label="Main">
                <div class="header__nav-inner">
                    <div class="header__label">Main menu</div>
                    <ul class="header__list">
                        <li class="header__item"><Link href="/products">Productos</Link></li>
                        <li class="header__item header__item--divider" aria-hidden="true"></li>
                        <li class="header__item">
                            <Button class="relative cursor-pointer bg-indigo-800 px-4 py-2 hover:bg-indigo-600" v-on:click="open">
                                <ShoppingCartIcon class="h-6 w-6" />
                                <Badge v-if="!cartStore.isEmpty" class="absolute -top-3 -right-2 bg-fuchsia-300 text-black">{{
                                    cartStore.aggregation.items_count
                                }}</Badge>
                            </Button>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <Cart />
    </header>
</template>

<script setup lang="ts">
import Cart from '@/components/Cart.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useCartDrawerStore } from '@/stores/cartDrawerStore';
import { useCartStore } from '@/stores/cartStore';
import { Link } from '@inertiajs/vue3';
import { ShoppingCart as ShoppingCartIcon } from 'lucide-vue-next';

const cartDrawer = useCartDrawerStore();

const cartStore = useCartStore();

function open() {
    cartDrawer.open();
}
</script>

<style scoped>
@reference '../../css/app.css';
/* --------------------------------

File#: _1_main-header
Title: Main Header
Descr: Accessible website navigation
Usage: codyhouse.co/license

-------------------------------- */
:root {
    --header-height: 50px;
}
@media (min-width: 64rem) {
    :root {
        --header-height: 10px;
    }
}

.header {
    height: var(--header-height);
    width: 100%;
    @apply bg-zinc-200;
    @apply z-[0];
}

.header__container {
    height: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header__logo {
    position: relative;
    z-index: 2;
    flex-shrink: 0;
}
.header__logo a,
.header__logo svg,
.header__logo img {
    display: block;
}

.header__nav {
    position: absolute;
    z-index: 1;
    top: 0;
    left: 0;
    width: 100%;
    max-height: 100vh;
    @apply bg-white;
    @apply shadow-md;
    overflow: auto;
    overscroll-behavior: contain;
    -webkit-overflow-scrolling: touch;
    display: none;
}
.header__nav::before {
    content: '';
    display: block;
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    height: var(--header-height);
    background: inherit;
    @apply border-b border-gray-300;
}

.header__nav--is-visible {
    display: block;
}

.header__nav-inner {
    @apply p-5 lg:p-8;
}

.header__label {
    @apply text-sm lg:text-base;
    @apply text-gray-500;
    @apply mt-3 lg:mt-5;
}

.header__item {
    /*  @apply mt-3 lg:mt-5; */
}

.header__link {
    @apply text-xl lg:text-3xl;
    @apply text-gray-900;
    text-decoration: none;
}
.header__link:hover,
.header__link[aria-current] {
    @apply text-indigo-700;
}

.header__nav-btn {
    @apply text-xl lg:text-3xl lg:leading-tight;
    width: 100%;
}

.header__item--divider {
    height: 1px;
    width: 100%;
    @apply bg-gray-300;
}

.header__trigger {
    position: relative;
    z-index: 2;
}

.header__trigger-icon {
    position: relative;
    display: block;
    height: 2px;
    width: 1em;
    background-color: currentColor;
    @apply mr-1.5 lg:mr-2;
    transition: 0.2s;
}
.header__trigger-icon::before,
.header__trigger-icon::after {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: inherit;
    height: inherit;
    background-color: currentColor;
    transition: 0.2s;
}
.header__trigger-icon::before {
    -webkit-transform: translateY(-5px);
    transform: translateY(-5px);
}
.header__trigger-icon::after {
    -webkit-transform: translateY(5px);
    transform: translateY(5px);
}

.header__trigger[aria-expanded='true'] .header__trigger-icon {
    background-color: transparent;
}
.header__trigger[aria-expanded='true'] .header__trigger-icon::before {
    transform: rotate(45deg);
}
.header__trigger[aria-expanded='true'] .header__trigger-icon::after {
    transform: rotate(-45deg);
}

@media (min-width: 64rem) {
    .header__nav {
        position: static;
        background-color: transparent;
        width: auto;
        max-height: none;
        box-shadow: none;
        overflow: visible;

        overscroll-behavior: auto;
        display: block;
    }
    .header__nav::before {
        display: none;
    }

    .header__nav-inner {
        padding: 0;
    }

    .header__label {
        position: absolute;
        clip: rect(1px, 1px, 1px, 1px);
        clip-path: inset(50%);
    }

    .header__list {
        display: flex;
        align-items: center;
    }

    .header__item {
        display: inline-block;
        margin-bottom: 0;
        @apply ml-5 lg:ml-8;
    }

    .header__link,
    .header__nav-btn {
        font-size: 1.125rem;
    }

    .header__item--divider {
        height: 1em;
        width: 1px;
    }

    .header__trigger {
        display: none;
    }
}
</style>
