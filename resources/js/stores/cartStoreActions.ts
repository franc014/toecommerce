//import { route } from "ziggy-js";
import { show, create } from '@/routes/cart';

import axios from "axios";
import { v4 as uuidv4 } from 'uuid';
async function createCartInDB(cartId: string) {

    const cartDB = await axios.post(create().url,{
         id: cartId,
    });

    return cartDB;
}

async function getCartFromDB(cartId: string) {
    const cartDB = await axios.post(show().url, {
        id: cartId
    });
    return cartDB;
}

export async function init() {
    const cartLS = localStorage.getItem('cart');

        if (cartLS) {
            const cart = JSON.parse(cartLS);
            try {
                const cartDB = await getCartFromDB(cart.id);
                console.info('got cart from DB', cartDB);
                this.id = cartDB.data.ui_cart_id;
                this.items = cartDB.data.items;
            } catch (e: any) {
                // restore cart to DB with LS cart if it has been removed for some reason
                console.error('Nope, sorry. could not get cart: ', e.message);
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
                    console.error('nope, sorry. could not create cart: ', e.message);
                }

            }
}


