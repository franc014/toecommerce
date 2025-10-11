<template>
    <section>
        <div class="container mx-auto space-y-10 py-20">
            <h1>
                Checkout for <span class="text-lg italic">{{ user.name }}</span>
            </h1>
            <div class="checkout-grid w-full">
                <div class="space-y-6">
                    <h2>Información para facturación (Principal)</h2>

                    <ul v-if="user.has_billing_info">
                        <li><span class="font-bold">Nombres:</span> {{ billingInfo.first_name }}</li>
                        <li><span class="font-bold">Apellidos:</span> {{ billingInfo.last_name }}</li>
                        <li><span class="font-bold">Email:</span> {{ billingInfo.email }}</li>
                        <li><span class="font-bold">País:</span> {{ billingInfo.country }}</li>
                        <li><span class="font-bold">Ciudad:</span> {{ billingInfo.city }}</li>
                        <li><span class="font-bold">Dirección:</span> {{ billingInfo.address }}</li>
                        <li><span class="font-bold">Código postal:</span> {{ billingInfo.zipcode }}</li>
                        <li><span class="font-bold">Teléfono:</span> {{ billingInfo.phone }}</li>
                    </ul>
                    <div v-if="!user.has_billing_info" class="space-y-4 font-bold">
                        <h3>Por favor llena tu información de facturación:</h3>
                        <UserInfoForm type="billing" />
                    </div>

                    <h2>Información para envío (Principal)</h2>

                    <ul v-if="user.has_shipping_info">
                        <li><span class="font-bold">Nombres:</span> {{ shippingInfo.first_name }}</li>
                        <li><span class="font-bold">Apellidos:</span> {{ shippingInfo.last_name }}</li>
                        <li><span class="font-bold">Email:</span> {{ shippingInfo.email }}</li>
                        <li><span class="font-bold">País:</span> {{ shippingInfo.country }}</li>
                        <li><span class="font-bold">Ciudad:</span> {{ shippingInfo.city }}</li>
                        <li><span class="font-bold">Dirección:</span> {{ shippingInfo.address }}</li>
                        <li><span class="font-bold">Código postal:</span> {{ shippingInfo.zipcode }}</li>
                        <li><span class="font-bold">Teléfono:</span> {{ shippingInfo.phone }}</li>
                    </ul>

                    <div v-if="!user.has_shipping_info" class="space-y-4 font-bold">
                        <h3>Por favor llena tu información de envío:</h3>
                        <UserInfoForm type="shipping" />
                    </div>
                </div>

                <div class="bg-indigo-100">cart and gateway info</div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import UserInfoForm from '@/components/UserInfoForm.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { UserInfoEntry } from '@/types';
import { usePage } from '@inertiajs/vue3';

defineOptions({ layout: StorefrontLayout });

const page = usePage();

const user = page.props.auth.user;
const billingInfo = page.props.billingInfo as UserInfoEntry;
const shippingInfo = page.props.shippingInfo as UserInfoEntry;
</script>

<style scoped>
.checkout-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 4rem;
}
</style>
