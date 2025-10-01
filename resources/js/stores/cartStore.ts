import { defineStore } from "pinia";
import { CartItem } from "../types/index";


import {init, addOrUpdateItem} from './cartStoreActions';



export const useCartStore = defineStore('cart', {
    state: () => ({
        id: '' ,
        items: [] as CartItem[],

    }),
    actions: {
        init,
        addOrUpdateItem
    },
    getters: {
        cartItems: (state) => state.items.sort(function(a,b){
            return a.id - b.id
        })
    }


});
