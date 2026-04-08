<template>
    <Sheet>
        <SheetTrigger as-child>
            <Button class="relative cursor-pointer bg-orange-300 px-4 py-2 hover:bg-orange-600">
                <ShoppingCartIcon class="h-6 w-6" />
                <Badge v-if="cart" class="absolute -top-3 -right-2 bg-sky-600 text-zinc-50">{{ cart.cart_aggregation.items_count }}</Badge>
            </Button>
        </SheetTrigger>

        <SheetOverlay class="sheet-overlay">
            <SheetContent class="min-w-full bg-zinc-50 md:min-w-1/2">
                <SheetHeader>
                    <header class="flex items-center gap-2 px-4">
                        <SheetTitle>
                            <h2 class="mt-4 flex items-baseline gap-2 text-2xl tracking-wide">
                                <ShoppingCartIcon class="mr-4 h-10 w-10" />
                                <span v-if="cart.cart_aggregation.items_count > 0"
                                    >{{ cart.cart_aggregation.items_count }} {{ cart.cart_aggregation.items_count > 1 ? 'ítems' : 'ítem' }}</span
                                >
                                <span v-else>Ningún ítem</span>
                                en tu carrito de compras
                            </h2>
                        </SheetTitle>
                    </header>
                </SheetHeader>

                <ul class="sheet-content space-y-8 border-t border-zinc-200 px-4 py-8">
                    <li v-for="item in cart.items" :key="item.id">
                        <CartItem :item="item" />
                    </li>
                </ul>
                <SheetFooter>
                    <CartTally :cart="cart" />

                    <div class="flex items-center justify-between gap-4" v-if="!cart.isEmpty">
                        <Button variant="outline" class="hover:bg-orange-200">
                            <CreditCard />
                            <a href="/checkout" class="w-full">Checkout</a>
                        </Button>

                        <Button variant="secondary" @click="emptyCart" class="cursor-pointer">
                            <Trash />
                            Vaciar
                        </Button>
                        <Confirm
                            :handleAction="emptyCart"
                            buttonLabel="Vaciar Carrito"
                            cancelLabelAction="Cancelar"
                            acceptLabelAction="Continuar"
                            title="¿Confirmas Vaciar tu Carrito?"
                            description="Todos tus productos serán eliminados del carrito y cualquier orden pendiente será cancelada."
                        >
                            <template v-slot:icon>
                                <Trash2 />
                            </template>
                        </Confirm>
                    </div>
                    <div class="my-4 flex flex-col items-center space-y-4 place-self-center tracking-wider" v-if="cart.isEmpty">
                        <p class="text-3xl font-bold">El carrito esta vacío.</p>
                        <Ban class="h-12 w-12" />
                    </div>
                </SheetFooter>
            </SheetContent>
        </SheetOverlay>
    </Sheet>
</template>

<script setup lang="ts">
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetOverlay, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { usePage } from '@inertiajs/vue3';
import { ShoppingCartIcon } from 'lucide-vue-next';

import CartTally from '@/components/CartTally.vue';

import CartItem from '@/components/CartItem.vue';
import Confirm from '@/components/Confirm.vue';
import { Cart } from '@/types';
import { Ban, CreditCard, Trash, Trash2 } from 'lucide-vue-next';

const page = usePage();

const cart = page.props.shoppingCart as Cart;

console.log('shoppingCart', cart);

//const cart = ref('') as any;

/* onMounted(() => {
    cart.value = JSON.parse(shoppingCart);
    console.log('cart', cart.value);
}); */

/* console.log('cart_aggr', cart.cart_aggregation.items_count); */

/* const cartStore = useCartStore();

function emptyCart() {
    cartStore.emptyCart({
        id: cartStore.id,
    });
} */

function emptyCart() {
    /* cartStore.emptyCart({
        id: cartStore.id,
    }); */
}
</script>

<style scoped>
.sheet-overlay {
    background-color: rgba(0, 0, 0, 0.5);
    background: rgba(0 0 0 / 0.5);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: grid;
    place-items: center;
    overflow-y: auto;
}
.sheet-content {
    overflow-y: auto;
}
</style>
