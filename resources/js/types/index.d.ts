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
    has_billing_info: boolean;
    has_shipping_info: boolean;
    created_at: string;
    updated_at: string;
}

export interface UserInfoEntry {
    id: number;
    email: string;
    first_name: string;
    last_name: string;
    address: string;
    city: string;
    state: string;
    zipcode: string;
    phone: string;
    country: string;
}

export interface Product {
    id: number;
    title: string;
    slug: string;
    summary: string;
    description: string;
    price_in_dollars: string;
    has_variants: boolean;
    variants: Array<ProductVariant>;
    images: Array<string>;
    has_taxes: boolean;
    taxes: string;
    main_image: string;
    dropping_stock: boolean;

}

export interface ProductVariant {
    id: number;
    title: string;
    description: string;
    slug: string;
    color: string;
    price_in_dollars: string;
    formatted_variation: string;
    images: Array<string>;
}



export interface Cart {
    id: string;
    items: CartItem[];
}


export interface CartAggregation{
    total_with_taxes_in_dollars: string;
    total_without_taxes_in_dollars: string;
    total_computed_taxes_in_dollars: string;
    total_in_dollars: string;
    items_count: number;
}

export interface CartItem {
    id: number;
    title: string;
    slug: string;
    image_url: string;
    product_id: number;
    price: number;
    tax: number;
    quantity: number;
    total: number;
    price_in_dollars: string;
    total_in_dollars: number;
    computed_taxes_in_dollars: string;
    purchasable_id: number;
    purchasable_type: string;
    formatted_variation: string;
    purchasable: Product | ProductVariant;
}

export interface Order {
    id: number;
    total_amount: number;
    total_amount_without_tax: number;
    total_amount_with_tax: number;
    total_computed_taxes: number;
    total_with_taxes_in_dollars: string;
    total_without_taxes_in_dollars: string;
    total_computed_taxes_in_dollars: string;
    total_amount_in_dollars: string;
    order_items: Array<OrderItem>;
}

export interface PayphoneInfo {
    storeId: string;
    token: string;
}


export interface DataForCart {
    ui_cart_id: string;
    product_id: number;
    quantity: number;
    purchasable_type: string;
}

export interface CartDrawer {
    open: boolean;
}

export interface PageComponents {
    [key: string]: PageComponent;
}

export interface PageComponent {
    class: string;
    content: Object;
}

export interface Heading {
    content: string,
    level: number
}

export interface Paragraph {
    content: string
}

export interface CTA {
    content: string,
    link: string
}


export interface Collection {
    id: number;
    title: string;
    slug: string;
    featured_image: string;
    products_count: number;
}

export interface PageComponentContent {
    heading: Array<Heading>;
    paragraph: Array<Paragraph>;
    cta: Array<CTA>;
    image: Array;
    "new-products": Array;
    "featured-product": Array;
    collections: Array;
    feature: Array;
    video: Array;
}

export interface Menu {
    title: string;
    slug: string;
    items: Array<MenuItem>;
}

export interface MenuItem {
    id: number;
    label: string;
    slug: string;
    url: string;
}

export interface Company {
    name: string;
    address: string;
    phone: string;
    email: string;
    socialMedia: SocialMedia;
    workingDays: WorkingDays;
}

export interface SocialMedia {
    facebook: string;
    instagram: string;
    twitter: string;
    youtube: string;
}

export interface WorkingDays {
    monday: string;
    tuesday: string;
    wednesday: string;
    thursday: string;
    friday: string;
    saturday: string;
    sunday: string;
}



export type BreadcrumbItemType = BreadcrumbItem;
