<template>
    <h2 class="my-5">{{ title }}</h2>

    <ul v-if="isSetup" class="pb-5">
        <li><span class="font-bold">Nombres:</span> {{ info.first_name }}</li>
        <li><span class="font-bold">Apellidos:</span> {{ info.last_name }}</li>
        <li><span class="font-bold">Email:</span> {{ info.email }}</li>
        <li><span class="font-bold">País:</span> {{ info.country }}</li>
        <li><span class="font-bold">Ciudad:</span> {{ info.city }}</li>
        <li><span class="font-bold">Dirección:</span> {{ info.address }}</li>
        <li><span class="font-bold">Código postal:</span> {{ info.zipcode }}</li>
        <li><span class="font-bold">Teléfono:</span> {{ info.phone }}</li>
    </ul>

    <Dialog v-if="isSetup">
        <DialogTrigger as-child>
            <Button class="text-base tracking-wide">
                <FilePenLine />
                Editar información {{ type === 'billing' ? 'de facturación' : 'de envío' }}
            </Button>
        </DialogTrigger>
        <DialogContent class="min-w-[700px] sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Editar información {{ type === 'billing' ? 'de facturación' : 'de envío' }}</DialogTitle>
                <DialogDescription>
                    <p class="text-base font-bold tracking-wide">Haz cambios en la información de facturación. Click Guardar cuando estés listo.</p>
                </DialogDescription>
            </DialogHeader>
            <UserInfoFormEdit :info="info" :type="type" />
        </DialogContent>
    </Dialog>

    <div v-if="!isSetup" class="space-y-8 font-bold">
        <h3>{{ formTitle }}</h3>
        <CloneBillingInfo @accept-cloning="handleAccpetCloning" />
        <UserInfoForm :type="type" v-if="!acceptsCloningBilling" />
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { FilePenLine } from 'lucide-vue-next';
import { ref } from 'vue';
import CloneBillingInfo from './CloneBillingInfo.vue';
import UserInfoForm from './UserInfoForm.vue';
import UserInfoFormEdit from './UserInfoFormEdit.vue';

const props = defineProps({
    data: Object,
});

const { info, isSetup, type, title, formTitle } = props.data as any;

const acceptsCloningBilling = ref(false);

function handleAccpetCloning(value: boolean) {
    acceptsCloningBilling.value = value;
}
</script>

<style scoped></style>
