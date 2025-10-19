<template>
    <section>
        <div class="wrapper space-y-10 py-20">
            <h1>
                Checkout for <span class="text-lg italic">{{ user.name }}</span>
            </h1>

            <Stepper v-slot="{ isNextDisabled, isPrevDisabled, nextStep, prevStep }" v-model="stepIndex" class="block w-full">
                <div class="flex-start flex w-full gap-2">
                    <StepperItem v-for="step in steps" :key="step.step" v-slot="{ state }" :step="step.step">
                        <StepperSeparator v-if="step.step !== steps[steps.length - 1].step" />
                        <StepperTrigger>
                            <Button
                                :variant="state === 'completed' || state === 'active' ? 'default' : 'outline'"
                                size="icon"
                                class="z-10 shrink-0 rounded-full"
                                :class="[state === 'active' && 'ring-2 ring-ring ring-offset-2 ring-offset-background']"
                                :disabled="state !== 'completed' && !user.has_billing_info"
                            >
                                <Check v-if="state === 'completed'" class="size-5" />
                                <Circle v-if="state === 'active'" />
                                <Dot v-if="state === 'inactive'" />
                            </Button>
                        </StepperTrigger>
                        <div class="mt-5 flex flex-col items-center text-center">
                            <StepperTitle :class="[state === 'active' && 'text-primary']" class="text-sm font-semibold transition lg:text-base">
                                {{ step.title }}
                            </StepperTitle>
                            <StepperDescription
                                :class="[state === 'active' && 'text-primary']"
                                class="sr-only text-xs text-muted-foreground transition md:not-sr-only lg:text-sm"
                            >
                                {{ step.description }}
                            </StepperDescription>
                        </div>
                    </StepperItem>
                </div>
                <div class="mt-4 flex flex-col gap-4">
                    <template v-if="stepIndex === 1">
                        <PurchaseInfo
                            :data="{
                                info: billingInfo,
                                isSetup: user.has_billing_info,
                                type: 'billing',
                                title: 'Información para facturación',
                                formTitle: 'Por favor llena tu información de facturación:',
                            }"
                        />
                    </template>
                    <template v-if="stepIndex === 2">
                        <PurchaseInfo
                            :data="{
                                info: shippingInfo,
                                isSetup: user.has_shipping_info,
                                type: 'shipping',
                                title: 'Información para envío',
                                formTitle: 'Por favor llena tu información de envío:',
                            }"
                        />
                    </template>
                    <template v-if="stepIndex === 3">
                        <PayphoneButton :gatewayInfo="payphoneInfo" />
                    </template>
                </div>
            </Stepper>
        </div>
    </section>
</template>

<script setup lang="ts">
import PayphoneButton from '@/components/PayphoneButton.vue';
import PurchaseInfo from '@/components/PurchaseInfo.vue';
import { Button } from '@/components/ui/button';
import { Stepper, StepperDescription, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { PayphoneInfo, UserInfoEntry } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { Check, Circle, Dot } from 'lucide-vue-next';
import { ref } from 'vue';

const steps = [
    {
        step: 1,
        title: 'Información de facturación',
        description: 'Ingresa tu información de facturación',
    },
    {
        step: 2,
        title: 'Información de envío',
        description: 'Ingresa tu información de envío',
    },
    {
        step: 3,
        title: 'Pago',
        description: 'Realiza el pago',
    },
];

defineOptions({ layout: StorefrontLayout });

const page = usePage();

const user = page.props.auth.user;
const billingInfo = page.props.billingInfo as UserInfoEntry;
const shippingInfo = page.props.shippingInfo as UserInfoEntry;
const payphoneInfo = page.props.gatewayInfo as PayphoneInfo;

const stepIndex = ref(1);

user.has_billing_info ? (stepIndex.value = 2) : (stepIndex.value = 1);
user.has_shipping_info ? (stepIndex.value = 3) : (stepIndex.value = 2);

//todo: liten cart changes and reload page
</script>

<style scoped>
.checkout-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 4rem;
}
</style>
