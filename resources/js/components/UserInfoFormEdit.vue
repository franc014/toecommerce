<template>
    <Form class="space-y-4" :action="update(parseInt(info.id))" method="put" v-slot="{ errors, processing }" @success="onSuccess">
        <div class="grid grid-cols-2 gap-6">
            <div class="col-span-2 space-y-2">
                <Label for="email" class="tracking-wide">Email <IsRequiredSign /></Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    v-model="info.email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    class="tracking-wide"
                />
                <ValidationError v-if="errors.email" :error="errors.email" />
            </div>

            <div class="col-span-2 space-y-2">
                <Label for="first_name" class="tracking-wide">Nombres <IsRequiredSign /></Label>
                <Input
                    id="first_name"
                    type="text"
                    name="first_name"
                    v-model="info.first_name"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="first_name"
                    class="tracking-wide"
                />
                <ValidationError v-if="errors.first_name" :error="errors.first_name" />
            </div>

            <div class="col-span-2 space-y-2">
                <Label for="last_name" class="tracking-wide">Apellidos <IsRequiredSign /></Label>
                <Input
                    id="last_name"
                    type="text"
                    name="last_name"
                    v-model="info.last_name"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="last_name"
                    class="tracking-wide"
                />
                <ValidationError v-if="errors.last_name" :error="errors.last_name" />
            </div>

            <div class="space-y-2">
                <Label for="phone" class="tracking-wide">Teléfono</Label>
                <Input
                    id="phone"
                    type="phone"
                    name="phone"
                    v-model="info.phone"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="phone"
                    class="tracking-wide"
                />
                <ValidationError v-if="errors.phone" :error="errors.phone" />
            </div>
            <div class="space-y-2">
                <Label for="country" class="tracking-wide">País </Label>
                <Input
                    id="country"
                    type="text"
                    name="country"
                    v-model="info.country"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="country"
                    class="tracking-wide"
                />
                <ValidationError v-if="errors.country" :error="errors.country" />
            </div>

            <div class="space-y-2">
                <Label for="state" class="tracking-wide">Provincia </Label>
                <Input
                    id="state"
                    type="text"
                    name="state"
                    v-model="info.state"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="state"
                    class="tracking-wide"
                />
                <ValidationError v-if="errors.state" :error="errors.state" />
            </div>

            <div class="space-y-2">
                <Label for="city" class="tracking-wide">Ciudad <IsRequiredSign /></Label>
                <Input
                    id="city"
                    type="text"
                    name="city"
                    v-model="info.city"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="city"
                    class="tracking-wide"
                />
                <ValidationError v-if="errors.city" :error="errors.city" />
            </div>

            <div class="col-span-2 space-y-2">
                <Label for="address" class="tracking-wide">Dirección <IsRequiredSign /></Label>
                <Input
                    id="address"
                    type="text"
                    name="address"
                    v-model="info.address"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="address"
                    class="tracking-wide"
                />
                <ValidationError v-if="errors.address" :error="errors.address" />
            </div>

            <div class="space-y-2">
                <Label for="zipcode" class="tracking-wide">Código Postal <IsRequiredSign /></Label>
                <Input
                    id="zipcode"
                    type="text"
                    name="zipcode"
                    v-model="info.zipcode"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="zipcode"
                    class="tracking-wide"
                />
                <ValidationError v-if="errors.zipcode" :error="errors.zipcode" />
            </div>

            <div class="space-y-2">
                <input type="hidden" name="type" :value="props.type" />
                <ValidationError v-if="errors.type" :error="errors.type" />
            </div>

            <div class="space-y-2">
                <Button type="submit" class="mt-4 w-full cursor-pointer" :tabindex="4" :disabled="processing" data-test="login-button">
                    <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                    <Save v-else />
                    Guardar
                </Button>
            </div>
        </div>
    </Form>
    <Toaster richColors :duration="3000" />
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Toaster } from '@/components/ui/sonner';
import { update } from '@/routes/storefront/user-info-entry';
import { Form } from '@inertiajs/vue3';
import { LoaderCircle, Save } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import IsRequiredSign from './IsRequiredSign.vue';
import ValidationError from './ValidationError.vue';

import type { UserInfoEntry } from '@/types';
import { onMounted, ref } from 'vue';

const props = defineProps({
    type: String,
    info: Object as () => UserInfoEntry,
});

const info = ref({} as UserInfoEntry);

onMounted(() => {
    info.value = props.info;
});

function onSuccess() {
    toast.success('Información actualizada!');
    /* setTimeout(() => {
        router.visit(checkout().url);
    }, 1000); */
}
</script>

<style scoped></style>
