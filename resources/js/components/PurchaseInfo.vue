<template>
    <h2 class="mt-10">{{ title }}</h2>

    <ul v-if="isSetup" class="pb-72">
        <li><span class="font-bold">Nombres:</span> {{ info.first_name }}</li>
        <li><span class="font-bold">Apellidos:</span> {{ info.last_name }}</li>
        <li><span class="font-bold">Email:</span> {{ info.email }}</li>
        <li><span class="font-bold">País:</span> {{ info.country }}</li>
        <li><span class="font-bold">Ciudad:</span> {{ info.city }}</li>
        <li><span class="font-bold">Dirección:</span> {{ info.address }}</li>
        <li><span class="font-bold">Código postal:</span> {{ info.zipcode }}</li>
        <li><span class="font-bold">Teléfono:</span> {{ info.phone }}</li>
    </ul>
    <div v-if="!isSetup" class="space-y-8 font-bold">
        <h3>{{ formTitle }}</h3>
        <UserInfoForm :type="type" />
    </div>
</template>

<script setup lang="ts">
import UserInfoForm from './UserInfoForm.vue';

const props = defineProps({
    data: Object,
});

const { info, isSetup, type, title, formTitle } = props.data as any;
</script>

<style scoped></style>
