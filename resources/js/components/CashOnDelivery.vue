<template>
    <div class="space-y-4 rounded-lg border border-zinc-200 bg-zinc-50 p-6">
        <h3 class="mb-4 text-xl font-semibold">Pago contra entrega</h3>
        <p class="mb-4 text-zinc-600">Paga en efectivo cuando recibas tu pedido. Asegúrate de tener el monto exacto disponible.</p>
        <div class="rounded-md bg-amber-50 p-4 text-sm text-amber-800">
            <p class="font-medium">Nota importante:</p>
            <p>El pago se realizará directamente al mensajero al momento de la entrega.</p>
        </div>
        <div class="rounded-md border border-zinc-200 bg-green-100 p-4" v-if="CODConfirmed">
            <p>Has confirmado pagar contra entrega!!! 🎉</p>
            <p>
                Tu Código de Orden es: <span class="text-lg font-bold tracking-wide">{{ order.code }}</span>
            </p>
            <p class="mb-4">Guárdalo para cualquier consulta.</p>
            <p class="mb-4">También hemos enviado un correo de confirmación a tu dirección de correo electrónico.</p>
            <p>Recuerda que puedes preguntarnos acerca de tu orden al WhatsApp +57 300 123 4567</p>
        </div>
        <div class="mt-6">
            <Button
                @click="handleCashOnDelivery"
                :disabled="isProcessingCOD"
                class="w-full cursor-pointer bg-green-300 font-bold tracking-widest hover:bg-green-400"
                size="lg"
                v-if="!CODConfirmed"
            >
                <span v-if="isProcessingCOD">Procesando...</span>
                <span v-else>Confirmar Pago contra Entrega</span>
            </Button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { selectPaymentMethod } from '@/routes/storefront/orders';
import { useHttp } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
    order: { id: number; code: string };
}>();

const emit = defineEmits<{
    processing: [];
    success: [];
    error: [];
}>();

const isProcessingCOD = ref(false);
const CODConfirmed = ref(false);

const handleCashOnDelivery = async () => {
    isProcessingCOD.value = true;
    emit('processing');

    const payload = {
        order_id: props.order.id,
        payment_method: 'cash_on_delivery',
    };

    const http = useHttp(payload);
    try {
        await http.post(selectPaymentMethod().url, {
            onSuccess: (response: any) => {
                toast.success(response.message);
                CODConfirmed.value = true;
                emit('success');
            },
        });
    } catch (error: any) {
        console.error({ error });
        emit('error');
    } finally {
        isProcessingCOD.value = false;
    }
};
</script>

<style scoped></style>
