<template>
    <Sheet>
        <SheetTrigger as-child>
            <Button class="relative cursor-pointer bg-orange-300 px-4 py-2 hover:bg-orange-600">
                <ShoppingCartIcon class="h-6 w-6" />
                <Badge v-if="!cartStore.isEmpty" class="absolute -top-3 -right-2 bg-sky-600 text-zinc-50">{{
                    cartStore.aggregation.items_count
                }}</Badge>
            </Button>
        </SheetTrigger>

        <SheetOverlay class="sheet-overlay">
            <SheetContent class="min-w-1/2 bg-zinc-50">
                <SheetHeader>
                    <header class="flex items-center gap-2 px-4">
                        <SheetTitle>
                            <h2 class="mt-4 flex items-center gap-2 text-2xl">
                                <ShoppingCartIcon />
                                <span v-if="cartStore.aggregation.items_count > 0"
                                    >{{ cartStore.aggregation.items_count }} {{ cartStore.aggregation.items_count > 1 ? 'ítems' : 'ítem' }}</span
                                >
                                <span v-else>Ningún ítem</span>
                                en tu carrito de compras
                            </h2>
                        </SheetTitle>
                    </header>
                </SheetHeader>

                <ul class="sheet-content space-y-8 border-t border-zinc-200 px-4 py-8">
                    <li v-for="item in cartStore.cartItems" :key="item.id">
                        <CartItem :item="item" />
                    </li>
                </ul>

                <SheetFooter>
                    <CartTally />

                    <div class="flex items-center justify-between gap-4" v-if="!cartStore.isEmpty">
                        <Button>
                            <CreditCard />
                            <Link href="/checkout" class="w-full">Checkout</Link>
                        </Button>

                        <!-- <Button variant="secondary" @click="emptyCart" class="cursor-pointer">
                        <Trash />
                        Vaciar
                    </Button> -->
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
                    <div class="flex flex-col items-center space-y-4 place-self-center" v-if="cartStore.isEmpty">
                        <p class="text-2xl">El carrito esta vacío.</p>
                        <a href="/" class="text-2xl">Volver a la tienda</a>
                    </div>
                </SheetFooter>
            </SheetContent>
        </SheetOverlay>
    </Sheet>
</template>

<script setup lang="ts">
import CartItem from '@/components/CartItem.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetFooter, SheetHeader, SheetOverlay, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { useCartStore } from '@/stores/cartStore';
import { Link } from '@inertiajs/vue3';
import { CreditCard, ShoppingCart as ShoppingCartIcon, Trash2 } from 'lucide-vue-next';
import CartTally from './CartTally.vue';
import Confirm from './Confirm.vue';

const cartStore = useCartStore();

function emptyCart() {
    cartStore.emptyCart({
        id: cartStore.id,
    });
}

console.log(cartStore.isEmpty);
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
