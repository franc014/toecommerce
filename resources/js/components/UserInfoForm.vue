<template>
    <Form class="space-y-4" :action="store()" method="post" v-slot="{ errors, processing }" @success="handleSuccess">
        <div class="space-y-2">
            <Label for="email" class="tracking-wide">Email</Label>
            <Input id="email" type="email" name="email" required autofocus :tabindex="1" autocomplete="email" class="tracking-wide" />
        </div>

        <div class="space-y-2">
            <Label for="first_name" class="tracking-wide">First Name</Label>
            <Input id="first_name" type="text" name="first_name" required autofocus :tabindex="1" autocomplete="first_name" class="tracking-wide" />
        </div>

        <div class="space-y-2">
            <Label for="last_name" class="tracking-wide">Last Name</Label>
            <Input id="last_name" type="text" name="last_name" required autofocus :tabindex="1" autocomplete="last_name" class="tracking-wide" />
        </div>

        <div class="space-y-2">
            <Label for="phone" class="tracking-wide">Phone</Label>
            <Input id="phone" type="phone" name="phone" required autofocus :tabindex="1" autocomplete="phone" class="tracking-wide" />
        </div>

        <div class="space-y-2">
            <Label for="address" class="tracking-wide">Address</Label>
            <Input id="address" type="text" name="address" required autofocus :tabindex="1" autocomplete="address" class="tracking-wide" />
        </div>

        <div class="space-y-2">
            <Label for="country" class="tracking-wide">Country</Label>
            <Input id="country" type="text" name="country" required autofocus :tabindex="1" autocomplete="country" class="tracking-wide" />
        </div>

        <div class="space-y-2">
            <Label for="state" class="tracking-wide">State</Label>
            <Input id="state" type="text" name="state" required autofocus :tabindex="1" autocomplete="state" class="tracking-wide" />
        </div>

        <div class="space-y-2">
            <Label for="city" class="tracking-wide">City</Label>
            <Input id="city" type="text" name="city" required autofocus :tabindex="1" autocomplete="city" class="tracking-wide" />
        </div>

        <div class="space-y-2">
            <Label for="zipcode" class="tracking-wide">Postal Code</Label>
            <Input id="zipcode" type="text" name="zipcode" required autofocus :tabindex="1" autocomplete="zipcode" class="tracking-wide" />
        </div>

        <div class="space-y-2">
            <input type="hidden" name="type" :value="props.type" />
        </div>

        <div class="space-y-2">
            <Button type="submit" class="mt-4 w-full cursor-pointer" :tabindex="4" :disabled="processing" data-test="login-button">
                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                Guardar
            </Button>
        </div>
    </Form>
    <Toaster richColors :duration="3000" />
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Toaster } from '@/components/ui/sonner';
import { store } from '@/routes/user-info-entry';
import { Form, router } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const props = defineProps({
    type: String,
});

function handleSuccess() {
    console.log('suk cess');
    toast.success('Información guardada!');

    setTimeout(() => {
        router.visit('/checkout');
    }, 3000);
}
</script>

<style scoped></style>
