<template>
    <Header :menu="mainMenu" />
    <main>
        <slot />
    </main>
    <Toaster richColors :closeButton="true" closeButtonPosition="top-left" :duration="5000" theme="light" position="top-right" :expand="true" />
    <Footer :company="company" :footerMenu="footerMenu" :legalMenu="legalMenu" />
</template>

<script lang="ts" setup>
import { Toaster } from '@/components/ui/sonner';
import { Company, Menu } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import 'vue-sonner/style.css';
import Footer from '../components/Footer.vue';
import Header from '../components/Header.vue';
import { useCartStore } from '../stores/cartStore';

const getErrorMessage = (error: any): string | null => {
    try {
        const raw = error.response?.data ?? error.response ?? error;
        const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
        return data?.message || Object.values(data?.errors || {}).flat()[0] || null;
    } catch {
        return null;
    }
};

const cartStore = useCartStore();
const page = usePage();
const mainMenu = page.props.mainMenu as Menu;
const footerMenu = page.props.footerMenu as Menu;
const legalMenu = page.props.legalMenu as Menu;
const company = page.props.company as Company;

cartStore.$onAction(({ name, onError, after }) => {
    if (name === 'addOrUpdateItem') {
        after((result) => {
            if (result?.message) {
                toast.success(result.message);
            }
        });
        onError((error: any) => {
            toast.error(getErrorMessage(error) || 'An error occurred while updating your cart');
        });
    }

    if (name === 'removeItem') {
        after((result) => {
            if (result?.message) {
                toast.success(result.message);
            }
        });
        onError((error: any) => {
            toast.error(getErrorMessage(error) || 'An error occurred while removing the item');
        });
    }

    if (name === 'emptyCart') {
        after((result) => {
            if (result?.message) {
                toast.success(result.message);
            }
        });
        onError((error: any) => {
            toast.error(getErrorMessage(error) || 'An error occurred while emptying your cart');
        });
    }
});
</script>

<style scoped></style>
