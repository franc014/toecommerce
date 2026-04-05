import { create, empty, show } from '@/routes/cart';
import { addOrUpdate, remove } from '@/routes/cart/items';
import { useHttp } from '@inertiajs/vue3';
import axios from 'axios';
import { defineStore } from 'pinia';
import { v7 as uuidv7 } from 'uuid';
import { CartAggregation, CartItem, DataForCart } from '../types/index';

export const useCartStore = defineStore('cart', {
    state: () => ({
        id: '' as string,
        aggregation: {} as CartAggregation,
        items: [] as CartItem[],
    }),
    actions: {
        async init(cookieCart: string) {
            if (cookieCart) {
                try {
                    const cartDB = await this.getCartFromDB(cookieCart);
                    console.info('got cart from DB');
                    this.id = cartDB.ui_cart_id;
                } catch (e: any) {
                    console.error('Sorry. Could not get cart: ', e.message);
                }
            } else {
                try {
                    const uuid = uuidv7();
                    const cartDB = (await this.createCartInDB(uuid)) as { ui_cart_id: string };
                    this.id = cartDB.ui_cart_id;
                } catch (e: any) {
                    console.error('Sorry. Could not create cart: ', e.message);
                }
            }
        },

        async addOrUpdateItem(data: DataForCart) {
            const response = await axios.post(addOrUpdate().url, data);
            await this.getCartFromDB(this.id);
            return response.data;
        },

        async removeItem(data: { ui_cart_id: string; item_id: number }) {
            const response = await axios.post(remove().url, data);
            await this.getCartFromDB(this.id);
            return response.data;
        },

        async emptyCart(data: { id: string }) {
            const response = await axios.post(empty().url, data);
            await this.getCartFromDB(this.id);
            return response.data;
        },

        productInItem(productSlug: string) {
            const item = this.items.find((item: CartItem) => item.slug === productSlug);
            return item?.quantity;
        },

        async createCartInDB(cartId: string) {
            try {
                console.info('creating cart url...', create().url);
                const http = useHttp({
                    id: cartId,
                });

                const cartDB = await http.post(create().url);

                return cartDB;
            } catch (e: any) {
                console.error('hey...', e.message);
                throw e;
            }
        },

        async getCartFromDB(cartId: string) {
            const http = useHttp({
                id: cartId,
            });
            const cartDB = (await http.post(show().url)) as { items: CartItem[]; cart_aggregation: CartAggregation; ui_cart_id: string };

            this.items = cartDB.items;
            this.aggregation['total_without_taxes_in_dollars'] = cartDB.cart_aggregation.total_without_taxes_in_dollars;
            this.aggregation['total_with_taxes_in_dollars'] = cartDB.cart_aggregation.total_with_taxes_in_dollars;
            this.aggregation['total_computed_taxes_in_dollars'] = cartDB.cart_aggregation.total_computed_taxes_in_dollars;
            this.aggregation['total_in_dollars'] = cartDB.cart_aggregation.total_in_dollars;
            this.aggregation['items_count'] = cartDB.cart_aggregation.items_count;

            return cartDB;
        },
    },
    getters: {
        cartItems: (state) =>
            state.items.sort(function (a, b) {
                return a.id - b.id;
            }),
        isEmpty: (state) => state.items.length === 0,
    },
});
