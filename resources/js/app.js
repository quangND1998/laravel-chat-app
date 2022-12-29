require('./bootstrap');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import 'vue3-emoji-picker/css'
import mitt from 'mitt';
const emitter = mitt();

createInertiaApp({
    title: (title) => `${title} `,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app: inertiaApp, props, plugin }) {
        const app = createApp({ render: () => h(inertiaApp, props) })
            .use(plugin)
            .mixin({ methods: { route } })
        app.config.globalProperties.emitter = emitter
        app.mount(el)

    },
});

InertiaProgress.init({ color: '#4B5563' });