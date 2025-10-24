<template>
    <div class="flex flex-col gap-y-2">
        <div class="items-top flex gap-x-2">
            <Checkbox id="terms1" class="border border-zinc-400" @update:model-value="handleAccept" />
            <div class="grid gap-1.5 leading-none">
                <label for="terms1" class="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                    Utilizar mis datos de facturación
                </label>
                <p class="text-sm text-muted-foreground">Los datos de facturación serán utilizados para los datos de envío.</p>
            </div>
        </div>
        <Form :action="useBillingAsShipping()" method="post" v-slot="{ errors, processing }" @success="handleSuccess">
            <Button v-if="accepts" class="cursor-pointer">
                <Copy />
                Utilizar información de facturación para envío
            </Button>
        </Form>
        <Toaster richColors :duration="3000" />
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Toaster } from '@/components/ui/sonner';
import { checkout } from '@/routes/storefront';
import { useBillingAsShipping } from '@/routes/storefront/user-info-entry';
import { Form, router } from '@inertiajs/vue3';
import { Copy } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const emits = defineEmits<{ acceptCloning: [clone: boolean] }>();

const accepts = ref(false);

function handleAccept(clone: boolean | any): void {
    accepts.value = clone;
    emits('acceptCloning', clone);
}

function handleSuccess(): void {
    toast.success('Información guardada!');
    setTimeout(() => {
        router.visit(checkout().url);
    }, 2000);
}
</script>

<style scoped></style>
