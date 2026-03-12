<template>
    <h2 class="my-5 text-4xl">{{ title }}</h2>

    <ul v-if="isSetup" class="mb-8 space-y-2 text-lg">
        <li class="tracking-wider"><span class="font-bold tracking-wide">Nombres:</span> {{ info.first_name }}</li>
        <li class="tracking-wider"><span class="font-bold tracking-wide">Apellidos:</span> {{ info.last_name }}</li>
        <li class="tracking-wider"><span class="font-bold tracking-wide">Email:</span> {{ info.email }}</li>
        <li class="tracking-wider"><span class="font-bold tracking-wide">País:</span> {{ info.country }}</li>
        <li class="tracking-wider"><span class="font-bold tracking-wide">Estado o Provincia:</span> {{ info.state }}</li>
        <li class="tracking-wider"><span class="font-bold tracking-wide">Ciudad:</span> {{ info.city }}</li>
        <li class="tracking-wider"><span class="font-bold tracking-wide">Dirección:</span> {{ info.address }}</li>
        <li class="tracking-wider"><span class="font-bold tracking-wide">Teléfono:</span> {{ info.phone }}</li>
        <li class="tracking-wider"><span class="font-bold tracking-wide">Código postal:</span> {{ info.zipcode }}</li>
    </ul>

    <Dialog v-if="isSetup" :modal="true">
        <DialogTrigger as-child>
            <Button class="cursor-pointer text-base tracking-wide hover:bg-orange-200" variant="outline">
                <FilePenLine />
                Editar información {{ type === 'billing' ? 'de facturación' : 'de envío' }}
            </Button>
        </DialogTrigger>
        <DialogContent class="min-w-[700px] bg-zinc-50 sm:max-w-[425px]">
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
        <h3 class="text-lg font-normal tracking-wider">{{ formTitle }}</h3>
        <CloneBillingInfo @accept-cloning="handleAccpetCloning" v-if="type === 'shipping'" />
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
