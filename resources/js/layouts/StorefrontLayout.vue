<template>
    <Header />
    <main>
        <slot />
    </main>
    <Toaster richColors :closeButton="true" closeButtonPosition="top-left" :duration="5000" theme="light" :expand="true" />
    <Footer />
</template>

<script lang="ts" setup>
import { Toaster } from '@/components/ui/sonner';
import { toast } from 'vue-sonner';
import 'vue-sonner/style.css';
import Footer from '../components/Footer.vue';
import Header from '../components/Header.vue';
import { useCartStore } from '../stores/cartStore';
const cartStore = useCartStore();

cartStore.$onAction(({ name, onError, after }) => {
    if (name === 'addOrUpdateItem') {
        after((result) => {
            toast.success('Item added to cart');
        });
        onError((error: any) => {
            if (error.response.data.message) {
                toast.error(error.response.data.message);
            } else {
                toast.error(error.response.data.error.message);
            }
        });
    }

    if (name === 'removeItem') {
        after((result) => {
            toast.success('Item removed from cart');
        });
        onError((error: any) => {
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
