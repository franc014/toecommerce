import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';
import { createPinia } from 'pinia';
import { useCartStore } from './stores/cartStore';

const appName = import.meta.env.VITE_APP_NAME || 'ToEcommerce';

const pinia = createPinia();

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup: ({ el, App, props, plugin }) => {
        const app = createApp({ render: () => h(App, props) })
            .use(pinia)
            .use(plugin)
            .mount(el);
        const cartPinia = useCartStore(pinia);
        cartPinia.init(props.initialPage.props.shoppingCart as string);

        return app;
    },
    progress: {
        color: '#e94f0dff',
    },
});

// This will set light / dark mode on page load...
//initializeTheme();

//console.log(Util);

