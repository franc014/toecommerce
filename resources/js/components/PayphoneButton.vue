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
    const storeId = '9dcfb52a-4975-4f48-a21c-6df92b37e28d';
    const token =
        'OF8eMoqkBm-eD5MHm_qcbnRdI97AJi4Mni8tycmMmsQls30Wp4vQrnhbjEBr-n5QlEpqrcubM6TFYjbcYptFg82Nt2Div68_wAutiyWvPoX6u4Aqgy_H2bVGeSN-Vc78LD2RrNCrpCRBaDq4HcHbxEoaCcR6KTMT7EBd2Z_m1RqONIMLArIeJozzg02weXayqbCnp6Fh1muokO9PBjdIxGWMhxW8iSW_hyJ5K4M31k7eTojhudwEDpDW-sA9n24cpO_HYybGtlU1Q0vypMdnz_hxp8NgctzCFqNz8KKcYtFMsZXfB18T5V_Ink7-GhBeGqIS3w';

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
