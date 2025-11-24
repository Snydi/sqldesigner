import { createStore } from 'vuex';
import axios from '@/axios';

function initializeAxiosHeaders(token) {
    if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    } else {
        delete axios.defaults.headers.common['Authorization'];
    }
}

export default createStore({
    state: {
        auth_token: localStorage.getItem('auth_token') || null,
        user: null,
    },
    mutations: {
        login(state, token) {
            state.auth_token = token;
            localStorage.setItem('auth_token', token);
            initializeAxiosHeaders(token);
        },
        logout(state) {
            state.auth_token = null;
            localStorage.removeItem('auth_token');
            initializeAxiosHeaders(null);
        },
    },
    getters: {
        authToken(state) {
            return state.auth_token;
        }
    },
    actions: {
        initializeAuth({ state }) {
            initializeAxiosHeaders(state.auth_token);
        }
    }
});
