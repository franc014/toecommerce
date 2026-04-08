<template>
    <section class="mt-50 min-h-screen md:mt-0">
        <div class="wrapper space-y-10 md:py-20">
            <h1 class="font-serif2">
                Checkout de <span class="text-2xl tracking-wider italic">{{ user.name }}</span>
            </h1>
            <div class="checkout-grid">
                <Accordion :collapsible="true" default-value="item-1">
                    <AccordionItem value="item-1" key="item-1">
                        <AccordionTrigger class="rounded bg-zinc-200/50 px-4 font-bold tracking-wider"
                            >1. Información para Facturación</AccordionTrigger
                        >
                        <AccordionContent class="pb-10 md:px-10">
                            <PurchaseInfo
                                :data="{
                                    info: billingInfo,
                                    isSetup: userHasBillingInfo,
                                    type: 'billing',
                                    title: 'Información para facturación',
                                    formTitle: 'Por favor llena tu información de facturación:',
                                }"
                            />
                        </AccordionContent>
                    </AccordionItem>
                    <AccordionItem value="item-2" v-if="userHasBillingInfo" key="item-2">
                        <AccordionTrigger class="rounded bg-zinc-200/50 px-4 font-bold tracking-wider">2. Información para Envío</AccordionTrigger>
                        <AccordionContent class="pb-10 md:px-10">
                            <PurchaseInfo
                                v-if="userHasBillingInfo"
                                :data="{
                                    info: shippingInfo,
                                    isSetup: userHasShippingInfo,
                                    type: 'shipping',
                                    title: 'Información para envío',
                                    formTitle: 'Por favor llena tu información de envío:',
                                }"
                            />
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
                <Accordion :collapsible="true" default-value="item-1">
                    <AccordionItem value="item-1" key="item-1">
                        <AccordionTrigger class="rounded bg-zinc-200/50 px-4 font-bold tracking-wider">3. Pago</AccordionTrigger>
                        <AccordionContent class="py-5">
                            <div class="space-y-10 md:px-10">
                                <h2 class="text-4xl">Métodos de Pago</h2>
                                <Tabs default-value="payphone" class="w-full">
                                    <TabsList class="grid w-full grid-cols-3">
                                        <TabsTrigger
                                            v-for="method in paymentMethods"
                                            :key="method.value"
                                            :value="method.value"
                                            class="tab-trigger"
                                            :disabled="isPaymentProcessing"
                                        >
                                            {{ method.label }}
                                        </TabsTrigger>
                                    </TabsList>
                                    <TabsContent value="payphone">
                                        <PayphoneButton :gatewayInfo="payphoneInfo" />
                                    </TabsContent>
                                    <TabsContent value="cash_on_delivery">
                                        <CashOnDelivery
                                            :order="order"
                                            @processing="handlePaymentProcessing"
                                            @success="handlePaymentSuccess"
                                            @error="handlePaymentError"
                                        />
                                    </TabsContent>
                                    <TabsContent value="bank_transfer">
                                        <BankTransfer
                                            :order="order"
                                            @processing="handlePaymentProcessing"
                                            @success="handlePaymentSuccess"
                                            @error="handlePaymentError"
                                        />
                                    </TabsContent>
                                </Tabs>
                            </div>
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import BankTransfer from '@/components/BankTransfer.vue';
import CashOnDelivery from '@/components/CashOnDelivery.vue';
import PayphoneButton from '@/components/PayphoneButton.vue';
import PurchaseInfo from '@/components/PurchaseInfo.vue';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { checkout, products } from '@/routes/storefront/';
import { useCartStore } from '@/stores/cartStore';
import { PaymentMethod, PayphoneInfo, UserHasInfoEntry, UserInfoEntry } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: StorefrontLayout });

const page = usePage();

const user = page.props.auth.user;
const userPurchaseInfo = page.props.userPurchaseInfo as UserHasInfoEntry;

const userHasBillingInfo = userPurchaseInfo.user_has_billing_info;
const userHasShippingInfo = userPurchaseInfo.user_has_shipping_info;

const billingInfo = page.props.billingInfo as UserInfoEntry;
const shippingInfo = page.props.shippingInfo as UserInfoEntry;
const payphoneInfo = page.props.gatewayInfo as PayphoneInfo;
const paymentMethods = page.props.paymentMethods as PaymentMethod[];
const order = page.props.order as { id: number; code: string };

const cartStore = useCartStore();

const isPaymentProcessing = ref(false);

const handlePaymentProcessing = () => {
    isPaymentProcessing.value = true;
};

const handlePaymentSuccess = () => {
    isPaymentProcessing.value = true;
};

const handlePaymentError = () => {
    isPaymentProcessing.value = false;
};

cartStore.$onAction(({ name, onError, after }) => {
    if (name === 'addOrUpdateItem') {
        after(() => {
            router.visit(checkout().url);
        });
        onError((error: any) => {
            if (error.response.data.message) {
                console.error(error.response.data.message);
            } else {
                console.error(error.response.data.error.message);
            }
        });
    }

    if (name === 'removeItem') {
        after(() => {
            router.visit(checkout().url);
        });
        onError((error: any) => {
            if (error.response.data.message) {
                console.error(error.response.data.message);
            } else {
                console.error(error.response.data.error.message);
            }
        });
    }

    if (name === 'emptyCart') {
        after(() => {
            router.visit(products().url);
        });
        onError((error: any) => {
            if (error.response.data.message) {
                console.error(error.response.data.message);
            } else {
                console.error(error.response.data.error.message);
            }
        });
    }
});
</script>

<style scoped></style>
