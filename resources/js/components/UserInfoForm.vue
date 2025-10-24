<template>
    <Form class="w-full space-y-4" :action="store()" method="post" v-slot="{ errors, processing }" @success="handleSuccess">
        <div class="grid grid-cols-2 gap-6">
            <div class="col-span-2 space-y-2">
                <Label for="email" class="tracking-wide">Email <IsRequiredSign /></Label>
                <Input id="email" type="email" name="email" required autofocus :tabindex="1" autocomplete="email" class="form-input" />
                <ValidationError v-if="errors.email" :error="errors.email" />
            </div>

            <div class="col-span-2 space-y-2">
                <Label for="first_name" class="tracking-wide">First Name <IsRequiredSign /></Label>
                <Input id="first_name" type="text" name="first_name" required autofocus :tabindex="1" autocomplete="first_name" class="form-input" />
                <ValidationError v-if="errors.first_name" :error="errors.first_name" />
            </div>

            <div class="col-span-2 space-y-2">
                <Label for="last_name" class="tracking-wide">Last Name <IsRequiredSign /></Label>
                <Input id="last_name" type="text" name="last_name" required autofocus :tabindex="1" autocomplete="last_name" class="form-input" />
                <ValidationError v-if="errors.last_name" :error="errors.last_name" />
            </div>

            <div class="space-y-2">
                <Label for="phone" class="tracking-wide">Phone</Label>
                <Input id="phone" type="phone" name="phone" required autofocus :tabindex="1" autocomplete="phone" class="form-input" />
                <ValidationError v-if="errors.phone" :error="errors.phone" />
            </div>

            <div class="space-y-2">
                <Label for="country" class="tracking-wide">Country </Label>
                <Input id="country" type="text" name="country" required autofocus :tabindex="1" autocomplete="country" class="form-input" />
                <ValidationError v-if="errors.country" :error="errors.country" />
            </div>

            <div class="space-y-2">
                <Label for="state" class="tracking-wide">State </Label>
                <Input id="state" type="text" name="state" required autofocus :tabindex="1" autocomplete="state" class="form-input" />
                <ValidationError v-if="errors.state" :error="errors.state" />
            </div>

            <div class="space-y-2">
                <Label for="city" class="tracking-wide">City <IsRequiredSign /></Label>
                <Input id="city" type="text" name="city" required autofocus :tabindex="1" autocomplete="city" class="form-input" />
                <ValidationError v-if="errors.city" :error="errors.city" />
            </div>

            <div class="col-span-2 space-y-2">
                <Label for="address" class="tracking-wide">Address <IsRequiredSign /></Label>
                <Input id="address" type="text" name="address" required autofocus :tabindex="1" autocomplete="address" class="form-input" />
                <ValidationError v-if="errors.address" :error="errors.address" />
            </div>

            <div class="space-y-2">
                <Label for="zipcode" class="tracking-wide">Postal Code <IsRequiredSign /></Label>
                <Input id="zipcode" type="text" name="zipcode" required autofocus :tabindex="1" autocomplete="zipcode" class="form-input" />
                <ValidationError v-if="errors.zipcode" :error="errors.zipcode" />
            </div>

            <div class="space-y-2">
                <input type="hidden" name="type" :value="props.type" />
                <ValidationError v-if="errors.type" :error="errors.type" />
            </div>

            <div class="col-span-2 space-y-2">
                <Button type="submit" class="mt-4 w-full cursor-pointer" :tabindex="4" :disabled="processing" data-test="login-button">
                    <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                    <Save v-else />
                    Guardar
                </Button>
            </div>
        </div>
    </Form>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { checkout } from '@/routes/storefront';
import { store } from '@/routes/storefront/user-info-entry';
import { Form, router } from '@inertiajs/vue3';
import { LoaderCircle, Save } from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import IsRequiredSign from './IsRequiredSign.vue';
import ValidationError from './ValidationError.vue';

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
