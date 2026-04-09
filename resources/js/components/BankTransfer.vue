<template>
    <div class="space-y-4 rounded-lg border border-zinc-200 bg-zinc-50 p-6">
        <h3 class="mb-4 text-xl font-semibold">Transferencia bancaria</h3>
        <p class="mb-4 text-zinc-600">Realiza una transferencia bancaria a nuestra cuenta y sube el comprobante de pago.</p>

        <!-- Success State -->
        <div class="rounded-md border border-zinc-200 bg-green-100 p-4" v-if="bankTransferConfirmed">
            <p>¡Has confirmado tu pago por transferencia bancaria! 🎉</p>
            <p>
                Tu Código de Orden es: <span class="text-lg font-bold tracking-wide">{{ order.code }}</span>
            </p>
            <p class="mb-4">Guárdalo para cualquier consulta.</p>
            <p class="mb-4">También hemos enviado un correo de confirmación a tu dirección de correo electrónico.</p>
            <p>Recuerda que puedes preguntarnos acerca de tu orden al WhatsApp +57 300 123 4567</p>
        </div>

        <!-- Upload Form -->
        <div v-else class="space-y-4">
            <div class="rounded-md bg-blue-50 p-4 text-sm text-blue-800">
                <p class="font-medium">Instrucciones:</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    <li>Realiza la transferencia a nuestra cuenta bancaria</li>
                    <li>
                        Usa el número de orden como referencia: <span class="font-bold">{{ order.code }}</span>
                    </li>
                    <li>Toma una foto o captura de pantalla del comprobante</li>
                    <li>Sube el archivo aquí (máximo 50KB, formato imagen)</li>
                </ul>
            </div>

            <div class="space-y-2">
                <Label for="payment_receipt" class="tracking-wide">Comprobante de pago <span class="text-red-500">*</span></Label>
                <Input
                    id="payment_receipt"
                    type="file"
                    name="payment_receipt"
                    accept="image/*"
                    required
                    class="form-input cursor-pointer file:mr-4 file:rounded-md file:border-0 file:bg-orange-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-orange-700 hover:file:bg-orange-200"
                    @change="handleFileChange"
                />
                <p class="text-xs text-zinc-500">Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 50KB</p>
                <p v-if="fileError" class="text-sm text-red-600">{{ fileError }}</p>
            </div>

            <div class="mt-6">
                <Button
                    @click="handleBankTransfer"
                    :disabled="isProcessing || fileError"
                    class="w-full cursor-pointer bg-green-300 font-bold tracking-widest hover:bg-green-400"
                    size="lg"
                >
                    <span v-if="isProcessing">Procesando...</span>
                    <span v-else>Confirmar Transferencia Bancaria</span>
                </Button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { selectPaymentMethod } from '@/routes/storefront/orders';
import { useHttp } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
    order: { id: number; code: string };
}>();

const emit = defineEmits<{
    processing: [];
    success: [];
    error: [];
}>();

const isProcessing = ref(false);
const bankTransferConfirmed = ref(false);
const selectedFile = ref<File | null>(null);
const fileError = ref<string | null>(null);

const http = useHttp({
    payment_receipt: null,
    payment_method: 'bank_transfer',
    order_id: props.order.id,
});

const handleFileChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] || null;
    fileError.value = null;

    if (file) {
        // Validate file size (50KB = 51200 bytes)
        if (file.size > 51200) {
            fileError.value = 'El archivo no debe superar los 50KB';
            selectedFile.value = null;
            return;
        }

        // Validate file type
        if (!file.type.startsWith('image/')) {
            fileError.value = 'El archivo debe ser una imagen';
            selectedFile.value = null;
            return;
        }

        selectedFile.value = file;

        http.payment_receipt = file;
    }
};

const handleBankTransfer = async () => {
    if (!selectedFile.value) {
        fileError.value = 'Por favor selecciona un archivo';
        return;
    }

    isProcessing.value = true;
    emit('processing');

    selectedFile.value = http.payment_receipt as File | null;

    console.log(http);

    try {
        await http.post(selectPaymentMethod().url, {
            onSuccess: (response: any) => {
                toast.success(response.message);
                bankTransferConfirmed.value = true;
                emit('success');
            },
            onError: (errors: any) => {
                console.error({ errors });
                if (errors.payment_receipt) {
                    fileError.value = errors.payment_receipt;
                }
                emit('error');
            },
        });
    } catch (error: any) {
        console.error({ error });
        toast.error('Ocurrió un error al procesar tu pago');
        emit('error');
    } finally {
        isProcessing.value = false;
    }
};
</script>

<style scoped></style>
