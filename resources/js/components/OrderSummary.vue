<template>
    <section class="border-b border-dashed border-zinc-400 pb-8">
        <h2 class="text-4xl">Resumen de la orden</h2>
        <div class="flex flex-col gap-4 py-4 md:p-4">
            <Table class="overflow-scroll rounded-md border border-dashed border-zinc-500">
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[100px]"> Producto </TableHead>
                        <TableHead>Precio</TableHead>
                        <TableHead>Cantidad</TableHead>
                        <TableHead class="text-right"> Total </TableHead>
                        <TableHead class="text-right"> Total con impuestos </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody class="tracking-wide">
                    <TableRow v-for="item in order.order_items" :key="item.id">
                        <TableCell class="font-medium"> {{ item.title }} </TableCell>
                        <TableCell>{{ item.price_in_dollars }}</TableCell>
                        <TableCell>{{ item.quantity }}</TableCell>
                        <TableCell class="text-right"> {{ item.total_in_dollars }} </TableCell>
                        <TableCell class="text-right"> {{ item.computed_taxes_in_dollars }} </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <ul class="space-y-3 tracking-wider">
                    <li>Subtotal con impuestos: {{ order.total_with_taxes_in_dollars }}</li>
                    <li>Subtotal sin impuestos: {{ order.total_without_taxes_in_dollars }}</li>
                    <li>Impuestos: {{ order.total_computed_taxes_in_dollars }}</li>
                    <li class="mt-4 text-xl font-bold">Total: {{ order.total_amount_in_dollars }}</li>
                </ul>
                <div class="flex space-x-3">
                    <Button class="block cursor-pointer hover:bg-orange-200" variant="outline" v-if="user.has_billing_info && user.has_shipping_info">
                        <a href="#payphone-box"> Pagar </a>
                    </Button>

                    <Confirm
                        v-if="user.has_billing_info && user.has_shipping_info"
                        :handleAction="cancelOrder"
                        buttonLabel="Anular Orden"
                        cancelLabelAction="Cancelar"
                        acceptLabelAction="Continuar"
                        title="¿Confirmas Anular tu Orden?"
                        description="Volverás a la tienda donde podrás seguir comprando."
                    >
                        <template v-slot:icon>
                            <Ban />
                        </template>
                    </Confirm>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Order, User } from '@/types';
import { useForm } from '@inertiajs/vue3';
import Confirm from './Confirm.vue';

import { Button } from '@/components/ui/button';
import { cancel } from '@/routes/storefront/orders';
import { Ban } from 'lucide-vue-next';

const { order } = defineProps<{
    order: Order;
    user: User;
}>();

const form = useForm({
    order: order.id,
});

function cancelOrder() {
    //confirm
    console.log('hey');

    form.post(cancel().url, {
        preserveScroll: true,
        onSuccess: () => {
            console.log('success cancel');
        },
    });
}
</script>

<style scoped></style>
