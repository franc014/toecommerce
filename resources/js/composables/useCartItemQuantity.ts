import { useCartStore } from "@/stores/cartStore";
import { ref, watchEffect } from "vue"




export function useCartItemQuantity(productSlug: string) {
    const qty = ref<number>(1);

    watchEffect(() => {
        const currentQuantity = useCartStore().productInItem(productSlug);
        if (currentQuantity) {
            qty.value = currentQuantity;
        }
    });

    function setQuantity(quantity: number) {
        qty.value = quantity;
    }

    return { qty, setQuantity };
}
