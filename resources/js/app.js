import './bootstrap';
import '../css/app.css';

// Bootstrap JS
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// AdminLTE JS
import 'admin-lte/dist/js/adminlte.min.js';

// Vue 3
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

import App from './App.vue';
import routes from './router/index.js';

// Create router
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guard for authentication
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('access_token');

    if (to.meta.requiresAuth && !token) {
        next('/login');
    } else if (to.path === '/login' && token) {
        next('/dashboard');
    } else {
        next();
    }
});

// Toast options
const toastOptions = {
    position: 'top-right',
    timeout: 3000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: 'button',
    icon: true,
    rtl: false,
};

// Create and mount Vue app
const app = createApp(App);
app.use(router);
app.use(Toast, toastOptions);
app.mount('#app');
