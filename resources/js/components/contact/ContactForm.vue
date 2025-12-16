<template>
    <Form class="w-full space-y-4" :action="sendMessage()" method="post" v-slot="{ errors, processing }" @success="handleSuccess">
        <div class="grid grid-cols-2 gap-6">
            <div class="col-span-2 space-y-2">
                <Label for="first_name" class="tracking-wide">Nombre <IsRequiredSign /> </Label>
                <Input id="first_name" type="text" name="first_name" required autofocus :tabindex="1" autocomplete="first_name" class="form-input" />
                <ValidationError v-if="errors.first_name" :error="errors.first_name" />
            </div>

            <div class="col-span-2 space-y-2">
                <Label for="last_name" class="tracking-wide">Apellido <IsRequiredSign /></Label>
                <Input id="last_name" type="text" name="last_name" required autofocus :tabindex="1" autocomplete="last_name" class="form-input" />
                <ValidationError v-if="errors.last_name" :error="errors.last_name" />
            </div>

            <div class="col-span-2 space-y-2">
                <Label for="email" class="tracking-wide">Email <IsRequiredSign /></Label>
                <Input id="email" type="email" name="email" required autofocus :tabindex="1" autocomplete="email" class="form-input" />
                <ValidationError v-if="errors.email" :error="errors.email" />
            </div>

            <div class="space-y-2">
                <Label for="phone" class="tracking-wide">Teléfono</Label>
                <Input id="phone" type="phone" name="phone" autofocus :tabindex="1" autocomplete="phone" class="form-input" />
                <ValidationError v-if="errors.phone" :error="errors.phone" />
            </div>

            <div class="col-span-2 space-y-2">
                <Label for="message" class="tracking-wide">Mensaje <IsRequiredSign /></Label>
                <Textarea id="message" name="message" required autofocus :tabindex="1" autocomplete="message" class="form-input" />
                <ValidationError v-if="errors.message" :error="errors.message" />
            </div>

            <div class="col-span-2 space-y-2">
                <Button
                    variant="outline"
                    type="submit"
                    class="mt-4 w-full cursor-pointer hover:bg-orange-200"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                    <SendHorizonal v-else />
                    Enviar
                </Button>
            </div>
        </div>
    </Form>
</template>

<script setup lang="ts">
import IsRequiredSign from '@/components/IsRequiredSign.vue';
import ValidationError from '@/components/ValidationError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { sendMessage } from '@/routes/storefront';
import { Form } from '@inertiajs/vue3';
import { LoaderCircle, SendHorizonal } from 'lucide-vue-next';

import { toast } from 'vue-sonner';
function handleSuccess() {
    toast.success('Mensaje enviado!');
}
</script>

<style scoped></style>
