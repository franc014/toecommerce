<template>
    <Header />
    <main class="bg-indigo-50">
        <slot />
    </main>
    <Toaster richColors />
</template>

<script lang="ts" setup>
import { Toaster } from '@/components/ui/sonner';
import { toast } from 'vue-sonner';
import 'vue-sonner/style.css';
import Header from '../components/Header.vue';
import { useCartStore } from '../stores/cartStore';
const cartStore = useCartStore();

cartStore.$onAction(({ name, onError, after }) => {
    if (name === 'addOrUpdateItem') {
        after((result) => {
            toast.success('Item added to cart');
        });
        onError((error) => {
            if (error.response.data.message) {
                toast.error(error.response.data.message);
            } else {
                toast.error(error.response.data.error.message);
            }
        });
    }
});
</script>

<style scoped></style>
