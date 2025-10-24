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

        <Button v-if="accepts" class="cursor-pointer">
            <Copy />
            Utilizar información de facturación para envío
        </Button>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Copy } from 'lucide-vue-next';
import { ref } from 'vue';

const emits = defineEmits<{ acceptCloning: [clone: boolean] }>();

const accepts = ref(false);

function handleAccept(clone: boolean) {
    accepts.value = clone;
    emits('acceptCloning', clone);
}
</script>

<style scoped></style>
