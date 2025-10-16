<template>
    <div class="space-y-4">
        <h2>Payphone Checkout</h2>
        <div id="pp-button"></div>
    </div>
</template>

<script setup lang="ts">
import { PayphoneInfo } from '@/types';
import { onMounted } from 'vue';

onMounted(() => {
    const storeId = props.gatewayInfo.storeId;
    const token = props.gatewayInfo.token;

    window.addEventListener('DOMContentLoaded', () => {
        new PPaymentButtonBox({
            token,
            clientTransactionId: props.gatewayInfo.clientTransactionId,
            amount: props.gatewayInfo.payment.amount,
            amountWithoutTax: props.gatewayInfo.payment.amountWithoutTax,
            amountWithTax: props.gatewayInfo.payment.amountWithTax,
            tax: props.gatewayInfo.payment.tax,
            currency: 'USD',
            storeId,
            reference: 'Pago via Website',
        }).render('pp-button');
    });
});

const props = defineProps<{
    gatewayInfo: PayphoneInfo;
}>();

console.log(props.gatewayInfo);
</script>

<style scoped></style>
