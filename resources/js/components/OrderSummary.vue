<template>
    <h2>Resumen de la orden</h2>
    <div class="flex flex-col gap-4">
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead class="w-[100px]"> Producto </TableHead>
                    <TableHead>Precio</TableHead>
                    <TableHead>Cantidad</TableHead>
                    <TableHead class="text-right"> Total </TableHead>
                    <TableHead class="text-right"> Total con impuestos </TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="item in order.order_items" :key="item.id">
                    <TableCell class="font-medium"> {{ item.title }} </TableCell>
                    <TableCell>{{ item.price_ }}</TableCell>
                    <TableCell>{{ item.quantity }}</TableCell>
                    <TableCell class="text-right"> {{ item.total_in_dollars }} </TableCell>
                    <TableCell class="text-right"> {{ item.computed_taxes_in_dollars }} </TableCell>
                </TableRow>
            </TableBody>
        </Table>
        <div class="flex items-end justify-between">
            <ul class="spacy-y-3 tracking-wider">
                <li>Subtotal con impuestos: {{ order.total_with_taxes_in_dollars }}</li>
                <li>Subtotal sin impuestos: {{ order.total_without_taxes_in_dollars }}</li>
                <li>Impuestos: {{ order.total_computed_taxes_in_dollars }}</li>
                <li class="mt-4 text-xl font-bold">Total: {{ order.total_amount_in_dollars }}</li>
            </ul>
            <div class="flex space-x-3">
                <Button class="block" v-if="user.has_billing_info && user.has_shipping_info">
                    <a href="#payphone-box"> Pagar </a>
                </Button>
                <form @submit.prevent="cancelOrder()">
                    <Button variant="secondary" type="submit">Cancelar</Button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { cancel } from '@/routes/storefront/orders';
import { Order, User } from '@/types';
import { useForm } from '@inertiajs/vue3';

const { order } = defineProps<{
    order: Order;
    user: User;
}>();

const form = useForm({
    order: order.id,
});

function cancelOrder() {
    form.post(cancel().url, {
        preserveScroll: true,
        onSuccess: () => {
            console.log('success cancel');
        },
    });
}
</script>

<style scoped></style>
