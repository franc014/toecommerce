<template>
    <header class="f-header js-f-header relative">
        <PromotionBar v-if="discount_display_config.show_message" :message="discount_display_config.message" />
        <div class="f-header__mobile-content wrapper">
            <Link href="/" class="f-header__logo flex items-center gap-2">
                <img src="/images/logo3.png" alt="Logo" />
                <span class="mt-8 text-lg leading-tight tracking-wide text-zinc-800">Fashion Dogs</span>
            </Link>
            <!-- <button class="anim-menu-btn js-anim-menu-btn f-header__nav-control js-tab-focus" aria-label="Toggle menu">
                <i class="anim-menu-btn__icon anim-menu-btn__icon--close" aria-hidden="true"></i>
            </button> -->

            <div class="mt-5 md:mt-0 md:hidden">
                <Cart />
            </div>
        </div>

        <div class="f-header__nav" role="navigation">
            <div class="f-header__nav-grid wrapper border-b border-dashed border-zinc-400">
                <div class="f-header__nav-logo-wrapper">
                    <Link href="/" class="f-header__logo flex items-center gap-2" view-transition prefetch>
                        <img src="/images/logo3.png" alt="Logo" />
                        <span class="mt-8 text-lg leading-tight tracking-wide text-zinc-800">Fashion Dogs</span>
                    </Link>
                </div>

                <ul class="f-header__list mt-5 flex flex-wrap items-center gap-0 space-x-6 md:mt-0 lg:justify-center">
                    <li class="f-header__item" v-for="item in menu.items" :key="item.id">
                        <Link
                            prefetch
                            :href="item.url"
                            class="f-header__link"
                            :aria-current="$page.url.startsWith(item.url) ? 'page' : ''"
                            view-transition
                            >{{ item.label }}</Link
                        >
                    </li>
                </ul>

                <ul class="f-header__list pb-10 md:pb-0 lg:justify-end">
                    <!-- <li class="f-header__item"><a href="#0" class="f-header__link">Login</a></li> -->
                    <li class="f-header__item hidden md:block">
                        <Cart />
                    </li>
                </ul>
            </div>
        </div>
    </header>
</template>

<script setup lang="ts">
import Cart from '@/components/Cart.vue';

import { DiscountDisplay, Menu } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import PromotionBar from './PromotionBar.vue';

const page = usePage();

const discount_display_config = page.props.discountsDisplayConfig as DiscountDisplay;

defineProps<{
    menu: Menu;
}>();
</script>

<style scoped></style>
