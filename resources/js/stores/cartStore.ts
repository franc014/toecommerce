import { defineStore } from "pinia";
import { CartItem } from "../types/index";

import axios from "axios";
import {init} from './cartStoreActions';
import { addOrUpdate } from '@/routes/cart/items';


export const useCartStore = defineStore('cart', {
    state: () => ({
        id: '' ,
        items: [] as CartItem[],

    }),
    actions: {
        init,
        async addOrUpdateItem(data: object) {

            console.log({ data });

            try {
                const resp = await axios.post(addOrUpdate().url, data);
                console.log('data', resp.data.items);
            } catch (e: any) {
                console.log({ e });
            }




            /* //todo tests...

            const cartDB = await axios.post(route('cart.items.store', { cart: this.id }), data);

            this.items = cartDB.data.items;

            localStorage.setItem('cart', JSON.stringify({
                id: this.id,
                items: this.items,
            })); */

        },

        updateItemQuantity(slug: string, quantity: number) {
            const index = this.items.findIndex(i => i.slug === slug);
            const item = this.items[index];
            this.items[index].quantity = quantity;
            this.items[index].total = item.price * quantity;
            this.items[index].total_with_tax = this.items[index].total * (1 + item.tax);
        },

        removeItem(slug: string) {
            const index = this.items.findIndex(i => i.slug === slug);
            this.items.splice(index, 1);
        },




    },
    /* getters: {
        is
    } */


});
