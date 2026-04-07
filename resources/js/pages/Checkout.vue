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
                                        <TabsTrigger v-for="method in paymentMethods" :key="method.value" :value="method.value" class="tab-trigger">
                                            {{ method.label }}
                                        </TabsTrigger>
                                    </TabsList>
                                    <TabsContent value="payphone">
                                        <PayphoneButton :gatewayInfo="payphoneInfo" />
                                    </TabsContent>
                                    <TabsContent value="cash_on_delivery">
                                        <div class="space-y-4 rounded-lg border border-zinc-200 bg-zinc-50 p-6">
                                            <h3 class="mb-4 text-xl font-semibold">Pago contra entrega</h3>
                                            <p class="mb-4 text-zinc-600">
                                                Paga en efectivo cuando recibas tu pedido. Asegúrate de tener el monto exacto disponible.
                                            </p>
                                            <div class="rounded-md bg-amber-50 p-4 text-sm text-amber-800">
                                                <p class="font-medium">Nota importante:</p>
                                                <p>El pago se realizará directamente al mensajero al momento de la entrega.</p>
                                            </div>
                                            <div class="rounded-md border border-zinc-200 bg-green-100 p-4" v-if="CODConfirmed">
                                                <p>Has confirmado pagar contra entrega!!! 🎉</p>
                                                <p>
                                                    Tu Nro. de orden es: <span class="text-lg font-bold tracking-wide">{{ order.code }}</span>
                                                </p>
                                                <p class="mb-4">Guárdalo para cualquier consulta.</p>
                                                <p class="mb-4">
                                                    También hemos enviado un correo de confirmación a tu dirección de correo electrónico.
                                                </p>
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
                                    </TabsContent>
                                    <TabsContent value="bank_transfer">
                                        <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-6">
                                            <h3 class="mb-4 text-xl font-semibold">Transferencia bancaria</h3>
                                            <p class="mb-4 text-zinc-600">
                                                Realiza una transferencia bancaria a nuestra cuenta. Una vez completada la compra, recibirás los datos
                                                bancarios para realizar el pago.
                                            </p>
                                            <div class="rounded-md bg-blue-50 p-4 text-sm text-blue-800">
                                                <p class="font-medium">Instrucciones:</p>
                                                <ul class="mt-2 list-inside list-disc">
                                                    <li>Completa tu orden</li>
                                                    <li>Realiza la transferencia con el número de orden como referencia</li>
                                                    <li>Envía el comprobante a nuestro correo electrónico</li>
                                                    <li>Tu pedido será procesado una vez confirmado el pago</li>
                                                </ul>
                                            </div>
                                        </div>
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
import PayphoneButton from '@/components/PayphoneButton.vue';
import PurchaseInfo from '@/components/PurchaseInfo.vue';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { checkout, products } from '@/routes/storefront/';
import { selectPaymentMethod } from '@/routes/storefront/orders';
///import { useCartDrawerStore } from '@/stores/cartDrawerStore';
import { useCartStore } from '@/stores/cartStore';
import { PaymentMethod, PayphoneInfo, UserHasInfoEntry, UserInfoEntry } from '@/types';
import { router, useHttp, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

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
//const cartDrawerStore = useCartDrawerStore();

const isProcessingCOD = ref(false);
const CODConfirmed = ref(false);

/* onMounted(() => {
    cartDrawerStore.close();
}); */

const handleCashOnDelivery = async () => {
    isProcessingCOD.value = true;
    const payload = {
        order_id: order.id,
        payment_method: 'cash_on_delivery',
    };

    const http = useHttp(payload);
    try {
        await http.post(selectPaymentMethod().url, {
            onSuccess: (response) => {
                console.log({ response });
                toast.success(response.message);
                CODConfirmed.value = true;
                //alert(response.props.flash.message);
                //cartStore.emptyCart();
                /* setTimeout(() => {
                    router.visit('/filament/customer/orders/' + order.code);
                }, 3000); */
            },
        });

        //router.visit(checkout().url);
    } catch (error: any) {
        console.log({ error });
    } finally {
        isProcessingCOD.value = false;
    }
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
