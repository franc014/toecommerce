<template>
    <div class="flex flex-col gap-y-2 rounded border border-zinc-300 p-4">
        <div class="items-top flex gap-x-2">
            <Checkbox id="terms1" class="border border-zinc-400" @update:model-value="handleAccept" />
            <div class="grid gap-1.5 leading-none">
                <label for="terms1" class="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                    Utilizar mis datos de facturación
                </label>
                <p class="text-sm text-muted-foreground" v-if="accepts">Los datos de facturación serán utilizados para los datos de envío.</p>
            </div>
        </div>
        <Form :action="useBillingAsShipping()" method="post" v-slot="{ errors, processing }" @success="handleSuccess">
            <Button v-if="accepts" class="cursor-pointer hover:bg-orange-200" variant="outline">
                <Copy />
                Utilizar información de facturación
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
import { Form, router, usePage } from '@inertiajs/vue3';
import { Copy } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const page = usePage();

const emits = defineEmits<{ acceptCloning: [clone: boolean] }>();

const accepts = ref(false);

function handleAccept(clone: boolean | any): void {
    accepts.value = clone;
    emits('acceptCloning', clone);
}

function handleSuccess(): void {
    toast.success(page.flash.success);
    setTimeout(() => {
        router.visit(checkout().url);
    }, 2000);
}
</script>

<style scoped></style>
