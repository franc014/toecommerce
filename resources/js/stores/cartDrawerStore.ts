import { defineStore } from "pinia";

export const useCartDrawerStore = defineStore('cartDrawer', {
    state: () => ({
        isOpen: false,
    }),
    actions: {
        toggle() {
            this.isOpen = !this.isOpen;
        }
    }
});
