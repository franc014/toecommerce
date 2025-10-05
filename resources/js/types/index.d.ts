import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Product {
    id: number;
    title: string;
    slug: string;
    price_in_dollars: string;
    has_variants: boolean;
    variants: Array<ProductVariant>;
    images: Array<string>;
}

export interface ProductVariant {
    id: number;
    title: string;
    slug: string;
    color: string;
    price_in_dollars: string;
    formatted_sizes: string;
    images: Array<string>;
}

export interface Cart {
    id: string;
    items: CartItem[];
}

export interface CartAggregation{
    subtotal_in_dollars: string;
    total_with_taxes_in_dollars: string;
    items_count: number;
}

export interface CartItem {
    id: number;
    title: string;
    slug: string;
    product_id: number;
    price: number;
    tax: number;
    quantity: number;
    total: number;
    total_with_tax: number;

}

export interface CartDrawer {
    open: boolean;
}

export type BreadcrumbItemType = BreadcrumbItem;
