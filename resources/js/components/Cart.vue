<template>
    <div class="absolute top-0 right-0">
        <section
            v-if="cartDrawerStore.isOpen"
            class="cart-grid fixed top-0 right-0 z-10 h-full w-1/2 border border-zinc-100 bg-zinc-100 p-10 shadow-lg shadow-zinc-500 dark:border-zinc-500"
        >
            <header class="flex items-center gap-2 px-4">
                <h2 class="flex items-center gap-2">
                    <span>{{ cartStore.aggregation.items_count }} items</span>
                    en tu carrito de compras
                </h2>
                <button @click="closeCart" class="ml-auto cursor-pointer">
                    <CloseIcon />
                </button>
            </header>

            <ul class="space-y-8 overflow-scroll border-t border-zinc-200 py-8">
                <li v-for="item in cartStore.cartItems" :key="item.id">
                    <CartItem :item="item" />
                </li>
            </ul>

            <footer class="border-t border-zinc-200 pt-4">
                <div class="mb-6 flex flex-col gap-4 border-b border-zinc-200 pb-4">
                    <p class="text-xl font-bold">
                        Total sin impuestos: <span class="italic">{{ cartStore.aggregation.total_without_taxes_in_dollars }}</span>
                    </p>
                    <p class="text-xl font-bold">
                        Total con impuestos: <span class="italic">{{ cartStore.aggregation.total_with_taxes_in_dollars }}</span>
                    </p>

                    <p class="text-xl font-bold">
                        Impuestos: <span class="italic">{{ cartStore.aggregation.total_computed_taxes_in_dollars }}</span>
                    </p>
                    <p class="text-xl font-bold">
                        Total: <span class="italic">{{ cartStore.aggregation.total_in_dollars }}</span>
                    </p>
                </div>

                <div class="flex items-center justify-between gap-4" v-if="!cartStore.isEmpty">
                    <Button>
                        <Link href="/checkout" class="w-full">Checkout</Link>
                    </Button>

                    <Button variant="secondary" @click="emptyCart" class="cursor-pointer">Vaciar</Button>
                </div>
            </footer>

            <div class="flex flex-col items-center space-y-4 place-self-center" v-if="cartStore.isEmpty">
                <p class="text-2xl">El carrito esta vacío.</p>
                <a href="/" class="text-2xl">Volver a la tienda</a>
            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
import CartItem from '@/components/CartItem.vue';
import { Button } from '@/components/ui/button';
import { useCartDrawerStore } from '@/stores/cartDrawerStore';
import { useCartStore } from '@/stores/cartStore';
import { Link } from '@inertiajs/vue3';
import { CircleX as CloseIcon } from 'lucide-vue-next';

const cartDrawerStore = useCartDrawerStore();
const cartStore = useCartStore();

function closeCart() {
    cartDrawerStore.toggle();
}

function emptyCart() {
    cartStore.emptyCart({
        id: cartStore.id,
    });
}
</script>

<style scoped>
.cart-grid {
    display: grid;
    grid-template-rows: auto 1fr auto;
    gap: 2rem;
}
</style>
