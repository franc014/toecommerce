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
                                    isSetup: user.has_billing_info,
                                    type: 'billing',
                                    title: 'Información para facturación',
                                    formTitle: 'Por favor llena tu información de facturación:',
                                }"
                            />
                        </AccordionContent>
                    </AccordionItem>
                    <AccordionItem value="item-2" v-if="user.has_billing_info" key="item-2">
                        <AccordionTrigger class="rounded bg-zinc-200/50 px-4 font-bold tracking-wider">2. Información para Envío</AccordionTrigger>
                        <AccordionContent class="pb-10 md:px-10">
                            <PurchaseInfo
                                v-if="user.has_billing_info"
                                :data="{
                                    info: shippingInfo,
                                    isSetup: user.has_shipping_info,
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
                                <!-- <OrderSummary :order="order" :user="user" /> -->
                                <PayphoneButton :gatewayInfo="payphoneInfo" />
                            </div>
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import PayphoneButton from '@/components/PayphoneButton.vue';
import PurchaseInfo from '@/components/PurchaseInfo.vue';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { checkout, products } from '@/routes/storefront/';
import { useCartDrawerStore } from '@/stores/cartDrawerStore';
import { useCartStore } from '@/stores/cartStore';
import { Order, PayphoneInfo, UserInfoEntry } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';

defineOptions({ layout: StorefrontLayout });

const page = usePage();

const user = page.props.auth.user;
const billingInfo = page.props.billingInfo as UserInfoEntry;
const shippingInfo = page.props.shippingInfo as UserInfoEntry;
const payphoneInfo = page.props.gatewayInfo as PayphoneInfo;
const order = page.props.order as Order;

const cartStore = useCartStore();
const cartDrawerStore = useCartDrawerStore();

onMounted(() => {
    cartDrawerStore.close();
});

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
        after((result) => {
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
        after((result) => {
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
