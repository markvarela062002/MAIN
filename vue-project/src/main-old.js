import './assets/main.css';

import { createApp } from 'vue';
import App from './App.vue';

import { createPinia } from "pinia";
const store = createPinia();

import { createMemoryHistory, createRouter } from 'vue-router';
import { setupLayouts } from 'virtual:generated-layouts';
import generatedRoutes  from '~pages';

const routes = setupLayouts(generatedRoutes);

const router = createRouter({
    history: createMemoryHistory(),
    routes,
  });

import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';

const app = createApp(App);
app.use(PrimeVue, {
    theme: {
        preset: Aura
    }
});

app.use(store);

app.use(router);

app.mount('#app')
