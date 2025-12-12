<template>
    <div
        class="flex flex-col gap-2 rounded-md bg-zinc-50 p-6 text-inherit text-zinc-800 no-underline shadow-[0_0_0_1px_hsl(221_39%_11%_/_0.05),_0_0.3px_0.4px_hsl(221_39%_11%_/_0.02),_0_0.9px_1.5px_hsl(221_39%_11%_/_0.045),_0_3.5px_6px_hsl(221_39%_11%_/_0.09)] transition-all duration-300 hover:shadow-[0_0_0_1px_hsl(221_39%_11%_/_0.05),_0_0.9px_1.25px_hsl(221_39%_11%_/_0.025),_0_3px_5px_hsl(221_39%_11%_/_0.05),_0_12px_20px_hsl(221_39%_11%_/_0.09)]"
    >
        <h3 class="text-left font-bold">{{ variant.title }}</h3>
        <Accordion type="single" collapsible>
            <AccordionItem value="item-1">
                <AccordionTrigger>Descripción</AccordionTrigger>
                <AccordionContent v-html="variant.description"></AccordionContent>
            </AccordionItem>
        </Accordion>
        <p class="mx-0 mt-3 mb-4 text-left text-[0.9375rem] leading-[1.58] text-zinc-700">
            Precio: <span class="font-bold">{{ variant.price_in_dollars }} </span>
        </p>
        <p>{{ variant.formatted_variation }}</p>
        <QuantityHandler :updateCart="updateCart" v-on:updateQuantity="setQuantity" :quantity="qty" />
    </div>
</template>

<script setup lang="ts">
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { useCartItemQuantity } from '@/composables/useCartItemQuantity';
import { useCartStore } from '@/stores/cartStore';
import { ProductVariant } from '@/types';
import QuantityHandler from './QuantityHandler.vue';
const cartStore = useCartStore();

const { variant } = defineProps<{ variant: ProductVariant }>();
const { qty, setQuantity } = useCartItemQuantity(variant.slug);

function updateCart() {
    cartStore.addOrUpdateItem({
        ui_cart_id: cartStore.id,
        product_id: variant.id,
        quantity: qty.value,
        purchasable_type: 'product-variant',
    });
}
</script>

<style scoped></style>
