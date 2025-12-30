<template>
    <Form
        class="w-full space-y-4"
        :action="sendMessage()"
        method="post"
        #default="{ errors, processing, validate, invalid, validating }"
        @success="handleSuccess"
    >
        <div class="grid grid-cols-2 gap-6">
            <div class="col-span-2 space-y-2">
                <Label for="first_name" class="tracking-wide">Nombre <IsRequiredSign /> </Label>
                <Input
                    id="first_name"
                    type="text"
                    name="first_name"
                    @blur="validate('first_name')"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="first_name"
                    class="form-input"
                />

                <ValidationError v-if="invalid('first_name')" :error="errors.first_name" />
            </div>

            <div class="col-span-2 space-y-2">
                <Label for="last_name" class="tracking-wide">Apellido <IsRequiredSign /></Label>
                <Input
                    id="last_name"
                    required
                    type="text"
                    name="last_name"
                    autofocus
                    :tabindex="2"
                    autocomplete="last_name"
                    class="form-input"
                    @change="validate('last_name')"
                />
                <ValidationError v-if="invalid('last_name')" :error="errors.last_name" />
            </div>

            <div class="col-span-2 space-y-2">
                <Label for="email" class="tracking-wide">Email <IsRequiredSign /></Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="3"
                    @change="validate('email')"
                    autocomplete="email"
                    class="form-input"
                />
                <ValidationError v-if="invalid('email')" :error="errors.email" />
            </div>

            <div class="space-y-2">
                <Label for="phone" class="tracking-wide">Teléfono</Label>
                <Input
                    id="phone"
                    type="phone"
                    name="phone"
                    @change="validate('phone')"
                    autofocus
                    :tabindex="4"
                    autocomplete="phone"
                    class="form-input"
                />
                <ValidationError v-if="invalid('phone')" :error="errors.phone" />
            </div>

            <div class="col-span-2 space-y-2">
                <Label for="message" class="tracking-wide">Mensaje <IsRequiredSign /></Label>
                <Textarea
                    id="message"
                    name="message"
                    required
                    @change="validate('message')"
                    autofocus
                    :tabindex="5"
                    autocomplete="message"
                    class="form-input"
                />
                <ValidationError v-if="invalid('message')" :error="errors.message" />
            </div>
            <!-- <div v-if="honeypot.enabled" :name="`${honeypot.nameFieldName}_wrap`">
                <Input :id="honeypot.nameFieldName" type="text" :name="honeypot.nameFieldName" class="form-input" :value="''" />
                <Input type="text" :name="honeypot.validFromFieldName" class="form-input" :value="honeypot.encryptedValidFrom" />
            </div> -->

            <div class="col-span-2 space-y-2">
                <Button variant="outline" type="submit" class="mt-4 w-full cursor-pointer hover:bg-orange-200" :tabindex="6" :disabled="processing">
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

const handleSuccess = () => {
    toast.success('Mensaje enviado con éxito');
};
</script>

<style scoped></style>
