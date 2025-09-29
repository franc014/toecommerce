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


    }


});
