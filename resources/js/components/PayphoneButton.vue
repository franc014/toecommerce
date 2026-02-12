<template>
    <div class="space-y-4" id="payphone-box">
        <h2 class="text-4xl">Payphone Checkout</h2>
        <div class="relative">
            <div
                id="pp-button"
                class="rounded-md border-2 border-dashed border-zinc-300"
                :class="{ 'blur-sm': !user.has_billing_info || !user.has_shipping_info }"
                ref="payphone-holder"
            ></div>
            <div
                class="absolute top-0 left-0 flex h-full w-full flex-col items-center justify-center gap-4"
                v-if="!user.has_billing_info || !user.has_shipping_info"
            >
                <OctagonAlertIcon class="h-16 w-16" />
                <p class="mx-auto max-w-fit px-20 text-center text-2xl font-bold tracking-wider">
                    Por favor, registra tu información de facturación y envío para completar tu compra
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { PayphoneInfo } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { OctagonAlertIcon } from 'lucide-vue-next';
import { v7 as uuidv7 } from 'uuid';
import { onMounted, useTemplateRef } from 'vue';

const page = usePage();
const user = page.props.auth.user;

const payphoneHolder = useTemplateRef('payphone-holder');

onMounted(() => {
    const storeId = props.gatewayInfo.storeId;
    const token = props.gatewayInfo.token;

    new PPaymentButtonBox({
        token,
        storeId,
        clientTransactionId: uuidv7(),
        amount: props.gatewayInfo.payment.amount,
        amountWithoutTax: props.gatewayInfo.payment.amountWithoutTax,
        amountWithTax: props.gatewayInfo.payment.amountWithTax,
        tax: props.gatewayInfo.payment.tax,
        currency: 'USD',
        reference: 'Pago via Website',
    }).render(payphoneHolder.value?.id);
});

const props = defineProps<{
    gatewayInfo: PayphoneInfo;
}>();
</script>

<style scoped></style>
