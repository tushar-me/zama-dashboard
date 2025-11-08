import '../css/app.css';
import 'primeicons/primeicons.css'
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import PrimeVue from 'primevue/config';
import Noir from '@/presets/Noir';
import AnimateOnScroll from 'primevue/animateonscroll';
import ConfirmationService from 'primevue/confirmationservice';
import AppState from '@/plugins/appState';
import Icon from "@/components/Icon.vue";
import ToastService from 'primevue/toastservice';
import "vue-draggable-resizable/style.css";

// Extend ImportMeta interface for Vite...
declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;
        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(PrimeVue, {
                theme: {
                    preset: Noir,
                    options: {
                        prefix: 'amar',
                        darkModeSelector: '.amar-dark',
                        cssLayer: false,
                    }
                },
                ripple: true
            })
            .use(ToastService)
            .use(AppState)
            .use(plugin)
            .use(ZiggyVue)
            .component('Icon', Icon)
            .directive('animateonscroll', AnimateOnScroll)
            .use(ConfirmationService)
            .mount(el);
    },
    progress: {
        color: '#0A0A0A',
    },
});
