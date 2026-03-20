import 'vue-toast-notification/dist/theme-sugar.css';
import { createApp } from 'vue';
import ToastPlugin from 'vue-toast-notification';
import { useToast } from 'vue-toast-notification';
import axios from '@/axios';

import store from '@/store/index.js'
import router from '@/router/index.js';
import App from '@/App.vue';

store.dispatch('initializeAuth');

// Redirect to verify-email page when diagrams API returns 403 email not verified
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 403 &&
            error.response?.data?.message === 'Your email address is not verified.') {
            router.push({ name: 'verify-email' });
        }
        return Promise.reject(error);
    }
);

// Show success toast when returning from email verification link
router.afterEach((to) => {
    if (to.query.verified === '1') {
        useToast({ position: 'bottom-right' }).success('Email verified successfully!');
        router.replace({ path: to.path });
    }
});

const app = createApp(App);

app.config.globalProperties.$http = axios;

app.use(router);
app.use(store);
app.use(ToastPlugin, {
  position: 'top-right'
});

app.mount('#app');