import { useToast } from 'vue-toast-notification';
import router from '../router/index.js';
import store from '@/store/index.js';

const $toast = useToast();

export const Auth = {

    async register(userData) {//TODO add mail verification
        try {
            await axios.get('/sanctum/csrf-cookie');
            const response = await axios.post('/api/register', {
                email: userData.email,
                password: userData.password
            });
            $toast.success(response.data.message);
            await router.push({ name: 'login' });
        } catch (error) {
            if (error.response) {
                $toast.error(error.response.data.message);
            } else {
                $toast.error('Something went wrong!');
            }
        }
    },

    async login(userData) {
        try {
            await axios.get('/sanctum/csrf-cookie');
            const response = await axios.post('/api/login', {
                email: userData.email,
                password: userData.password
            });
            store.commit('login', response.data.token);
            $toast.success(response.data.message);
            await router.push({ name: 'diagrams' });
        } catch (error) {
            if (error.response) {
                $toast.error(error.response.data.message);
            } else {
                $toast.error('Something went wrong!');
            }
        }
    },

    async logout() {
        try {
            const response = await axios.post('/api/logout');
            store.commit('logout');
            $toast.success(response.data.message);
            window.location.href = '/'; //TODO potentially will prevent SSR in the future and gives 1 sec of white screen
        } catch (error) {
            if (error.response) {
                $toast.error(error.response.data.message);
            } else {
                $toast.error('Something went wrong!');
            }
        }
    },
};
