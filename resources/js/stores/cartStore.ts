import { defineStore } from "pinia";
import { CartAggregation, CartItem } from "../types/index";


import {init, addOrUpdateItem, removeItem} from './cartStoreActions';




export const useCartStore = defineStore('cart', {
    state: () => ({
        id: '' as string,
        aggregation: {} as CartAggregation,
        items: [] as CartItem[],

    }),
    actions: {
        init,
        addOrUpdateItem,
        removeItem
    },
    getters: {
        cartItems: (state) => state.items.sort(function(a,b){
            return a.id - b.id
        }),
    }


});
