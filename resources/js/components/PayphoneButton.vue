<template>
    <div class="my-5 space-y-4">
        <h2>Payphone Checkout</h2>
        <div id="pp-button" ref="payphone-holder"></div>
    </div>
</template>

<script setup lang="ts">
import { PayphoneInfo } from '@/types';
import { v4 as uuidv4 } from 'uuid';
import { onMounted, useTemplateRef } from 'vue';

const payphoneHolder = useTemplateRef('payphone-holder');

onMounted(() => {
    const storeId = props.gatewayInfo.storeId;
    const token = props.gatewayInfo.token;

    new PPaymentButtonBox({
        token,
        storeId,
        clientTransactionId: uuidv4(),
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
