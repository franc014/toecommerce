<template>
    <section>
        <div class="wrapper space-y-10 py-20">
            <h1>
                Checkout for <span class="text-lg italic">{{ user.name }}</span>
            </h1>
            <div class="checkout-grid">
                <div>
                    <div>
                        <PurchaseInfo
                            :data="{
                                info: billingInfo,
                                isSetup: user.has_billing_info,
                                type: 'billing',
                                title: 'Información para facturación',
                                formTitle: 'Por favor llena tu información de facturación:',
                            }"
                        />
                    </div>
                    <div>
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
                    </div>
                </div>
                <div>
                    <div>
                        <PayphoneButton :gatewayInfo="payphoneInfo" v-if="user.has_billing_info && user.has_shipping_info" />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import PayphoneButton from '@/components/PayphoneButton.vue';
import PurchaseInfo from '@/components/PurchaseInfo.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { checkout } from '@/routes/storefront/';
import { useCartStore } from '@/stores/cartStore';
import { PayphoneInfo, UserInfoEntry } from '@/types';
import { router, usePage } from '@inertiajs/vue3';

defineOptions({ layout: StorefrontLayout });

const page = usePage();

const user = page.props.auth.user;
const billingInfo = page.props.billingInfo as UserInfoEntry;
const shippingInfo = page.props.shippingInfo as UserInfoEntry;
const payphoneInfo = page.props.gatewayInfo as PayphoneInfo;

//todo: liten cart changes and reload page
const cartStore = useCartStore();

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
});
</script>

<style scoped>
.checkout-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 4rem;
}
</style>
