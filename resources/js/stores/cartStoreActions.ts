import { show, create, empty } from '@/routes/cart';
import { addOrUpdate, remove } from '@/routes/cart/items';
import { DataForCart } from '@/types';

import axios from "axios";
import { v4 as uuidv4 } from 'uuid';
async function createCartInDB(cartId: string) {

    const cartDB = await axios.post(create().url,{
         id: cartId,
    });

    return cartDB;
}

async function getCartFromDB(cartId: string, store: any) {
    const cartDB = await axios.post(show().url, {
        id: cartId
    });

    store.items = cartDB.data.items;
    store.aggregation['total_without_taxes_in_dollars'] = cartDB.data.cart_aggregation.total_without_taxes_in_dollars;
    store.aggregation['total_with_taxes_in_dollars'] = cartDB.data.cart_aggregation.total_with_taxes_in_dollars;
    store.aggregation['total_computed_taxes_in_dollars'] = cartDB.data.cart_aggregation.total_computed_taxes_in_dollars;
    store.aggregation['total_in_dollars'] = cartDB.data.cart_aggregation.total_in_dollars;
    store.aggregation['items_count'] = cartDB.data.cart_aggregation.items_count;




    return cartDB;
}

export async function init() {
    const cartLS = localStorage.getItem('cart');

        if (cartLS) {
            const cart = JSON.parse(cartLS);
            try {
                const cartDB = await getCartFromDB(cart.id, this);
                console.info('got cart from DB');
                this.id = cartDB.data.ui_cart_id;

            } catch (e: any) {
                // restore cart to DB with LS cart if it has been removed for some reason
                console.error('Sorry. Could not get cart: ', e.message);
                console.info('Trying to restore cart in DB with LS cart...');

                 // todo: restore cart with items function
                /* try {

                     const cartDB = await restorCartToDB(cart.id, cart.items);
                     this.id = cartDB.data.ui_cart_id;
                     this.items = cartDB.data.items;

                 } catch (e: any) {
                     console.error('nope, sorry. could not restore the cart', e.message);
                 } */
            }

            }else {
                try {
                    const uuid = uuidv4();

                    const cartDB = await createCartInDB(uuid);

                    localStorage.setItem('cart', JSON.stringify({
                        id: cartDB.data.ui_cart_id,
                        items: [],
                    }));

                    this.id = cartDB.data.ui_cart_id;

                } catch (e: any) {
                    console.error('Sorry. Could not create cart: ', e.message);
                }
            }
}

export async function addOrUpdateItem(data: DataForCart){
    await axios.post(addOrUpdate().url, data);
    await getCartFromDB(this.id, this);
}
//todo: also type...
export async function removeItem(data: object){
    await axios.post(remove().url, data);
    await getCartFromDB(this.id,this);
}
export async function emptyCart(data: object){

    await axios.post(empty().url, data);
    await getCartFromDB(this.id, this);
}



