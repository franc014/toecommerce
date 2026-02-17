<template>
    <Form class="w-full" :action="store()" method="post" #default="{ errors, processing, validate, invalid, validating }" @success="handleSuccess">
        <div v-if="honeypot.enabled" :name="`${honeypot.nameFieldName}_wrap`" style="display: none">
            <input type="text" :name="honeypot.nameFieldName" :id="honeypot.nameFieldName" />
            <input type="text" :name="honeypot.validFromFieldName" :value="honeypot.encryptedValidFrom" />
        </div>
        <div class="grid grid-cols-2 gap-6">
            <div class="col-span-2 space-y-2">
                <Label for="email" class="flex items-center tracking-wide">Email <IsRequiredSign /></Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    @blur="validate('email')"
                    :tabindex="1"
                    autocomplete="email"
                    class="form-input"
                />
                <ValidationError v-if="invalid('email')" :error="errors.email" />
            </div>

            <div class="col-span-2 space-y-2">
                <Label for="first_name" class="tracking-wide">Nombres <IsRequiredSign /></Label>
                <Input
                    id="first_name"
                    @blur="validate('first_name')"
                    type="text"
                    name="first_name"
                    required
                    autofocus
                    :tabindex="2"
                    autocomplete="first_name"
                    class="form-input"
                />
                <ValidationError v-if="invalid('first_name')" :error="errors.first_name" />
            </div>

            <div class="col-span-2 space-y-2">
                <Label for="last_name" class="tracking-wide">Apellidos <IsRequiredSign /></Label>
                <Input
                    id="last_name"
                    type="text"
                    name="last_name"
                    @blur="validate('last_name')"
                    required
                    autofocus
                    :tabindex="3"
                    autocomplete="last_name"
                    class="form-input"
                />
                <ValidationError v-if="invalid('last_name')" :error="errors.last_name" />
            </div>

            <div class="space-y-2">
                <Label for="phone" class="tracking-wide">Teléfono</Label>
                <Input
                    id="phone"
                    type="phone"
                    @blur="validate('phone')"
                    name="phone"
                    required
                    autofocus
                    :tabindex="4"
                    autocomplete="phone"
                    class="form-input"
                />
                <ValidationError v-if="invalid('phone')" :error="errors.phone" />
            </div>

            <div class="space-y-2">
                <Label for="country" class="tracking-wide">País </Label>
                <Input
                    id="country"
                    @blur="validate('country')"
                    type="text"
                    name="country"
                    required
                    autofocus
                    :tabindex="5"
                    autocomplete="country"
                    class="form-input"
                />
                <ValidationError v-if="invalid('country')" :error="errors.country" />
            </div>

            <div class="space-y-2">
                <Label for="state" class="tracking-wide">Provincia/Estado </Label>
                <Input
                    id="state"
                    type="text"
                    @blur="validate('state')"
                    name="state"
                    required
                    autofocus
                    :tabindex="6"
                    autocomplete="state"
                    class="form-input"
                />
                <ValidationError v-if="invalid('state')" :error="errors.state" />
            </div>

            <div class="space-y-2">
                <Label for="city" class="tracking-wide">Ciudad <IsRequiredSign /></Label>
                <Input
                    id="city"
                    type="text"
                    @blur="validate('city')"
                    name="city"
                    required
                    autofocus
                    :tabindex="7"
                    autocomplete="city"
                    class="form-input"
                />
                <ValidationError v-if="invalid('city')" :error="errors.city" />
            </div>

            <div class="col-span-2 space-y-2">
                <Label for="address" class="tracking-wide">Dirección <IsRequiredSign /></Label>
                <Input
                    id="address"
                    type="text"
                    @blur="validate('address')"
                    name="address"
                    required
                    autofocus
                    :tabindex="8"
                    autocomplete="address"
                    class="form-input"
                />
                <ValidationError v-if="invalid('address')" :error="errors.address" />
            </div>

            <div class="space-y-2">
                <Label for="zipcode" class="tracking-wide">Código Postal</Label>
                <Input
                    id="zipcode"
                    type="text"
                    @blur="validate('zipcode')"
                    name="zipcode"
                    required
                    autofocus
                    :tabindex="9"
                    autocomplete="zipcode"
                    class="form-input"
                />
                <ValidationError v-if="invalid('zipcode')" :error="errors.zipcode" />
            </div>

            <div class="space-y-2">
                <input type="hidden" name="type" :value="props.type" />
                <ValidationError v-if="errors.type" :error="errors.type" />
            </div>

            <div class="col-span-2 space-y-2">
                <Button type="submit" class="mt-4 w-full cursor-pointer hover:bg-orange-200" :tabindex="10" :disabled="processing" variant="outline">
                    <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                    <Save v-else />
                    Guardar
                </Button>
            </div>
        </div>
    </Form>
</template>

<script setup lang="ts">
import IsRequiredSign from '@/components/IsRequiredSign.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { checkout } from '@/routes/storefront';
import { store } from '@/routes/storefront/user-info-entry';
import { Form, router, usePage } from '@inertiajs/vue3';
import { LoaderCircle, Save } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import ValidationError from './ValidationError.vue';

const page = usePage();

const honeypot = page.props.honeypot as any;

const props = defineProps({
    type: String,
});

function handleSuccess() {
    toast.success('Información guardada!');
    setTimeout(() => {
        router.visit(checkout().url);
    }, 1000);
}
</script>

<style scoped></style>
