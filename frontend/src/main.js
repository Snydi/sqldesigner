import 'vue-toast-notification/dist/theme-sugar.css';
import '@/js/site-language.js';
import { createApp } from 'vue';
import Clarity from '@microsoft/clarity';
import ToastPlugin from 'vue-toast-notification';
import { useToast } from 'vue-toast-notification';
import axios from '@/axios';

import store from '@/store/index.js'
import router from '@/router/index.js';
import App from '@/App.vue';

if (import.meta.env.PROD) {
    Clarity.init('wndxp2jbej');
}

store.dispatch('initializeAuth');

const publicAuthEndpoints = ['/api/login', '/api/register'];

const requestAuthorization = (config) => {
    const headers = config?.headers;
    if (typeof headers?.get === 'function') return headers.get('Authorization');
    return headers?.Authorization;
};

const redirectToLogin = () => {
    const currentRoute = router.currentRoute.value;
    if (['login', 'register', 'auth.callback'].includes(currentRoute.name)) return;

    router.replace({
        name: 'login',
        query: { redirect: currentRoute.fullPath },
    });
};

window.addEventListener('storage', (event) => {
    if (event.key !== 'auth_token') return;

    if (event.newValue) {
        store.commit('login', event.newValue);
        return;
    }

    store.commit('logout');
    redirectToLogin();
});

// Keep authentication consistent across tabs and recover cleanly from expired tokens.
axios.interceptors.response.use(
    response => response,
    async error => {
        const config = error.config;

        if (error.response?.status === 401 &&
            !publicAuthEndpoints.some(endpoint => config?.url?.endsWith(endpoint))) {
            const storedToken = localStorage.getItem('auth_token');
            const staleAuthorization = requestAuthorization(config);
            const currentAuthorization = storedToken ? `Bearer ${storedToken}` : null;

            // Another tab may have logged in since this request was created. Retry once
            // with that tab's current token instead of clearing the valid session.
            if (storedToken && staleAuthorization !== currentAuthorization && !config.__authRetried) {
                store.commit('login', storedToken);
                config.__authRetried = true;
                config.headers.Authorization = currentAuthorization;
                return axios(config);
            }

            if (!storedToken || staleAuthorization === currentAuthorization) {
                store.commit('logout');
                redirectToLogin();
            }
        }

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
