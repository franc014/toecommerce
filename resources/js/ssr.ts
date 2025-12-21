import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createSSRApp, DefineComponent, h } from 'vue';
import { renderToString } from 'vue/server-renderer';

const appName = import.meta.env.VITE_APP_NAME || 'ToEcommerce';
import { createPinia } from 'pinia';
import { useCartStore } from './stores/cartStore';

const pinia = createPinia();

createServer(
    (page) =>
        createInertiaApp({
            page,
            render: renderToString,
            title: (title) => (title ? `${title} - ${appName}` : appName),
            resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
            setup: ({ App, props, plugin }) => {
                const app = createSSRApp({ render: () => h(App, props) })
                .use(pinia)
                .use(plugin);

                const cartPinia = useCartStore(pinia);
                cartPinia.init(props.initialPage.props.shoppingCart as string);

                return app;
            },

            }),
    { cluster: true },
);
