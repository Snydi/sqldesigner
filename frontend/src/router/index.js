import axios from '@/axios';
import Register from '../components/Auth/Register.vue';
import Login from '../components/Auth/Login.vue';
import Logout from "../components/Auth/Logout.vue";
import DiagramList from '../components/DiagramList.vue';
import Diagram from '../components/Diagram.vue';
import store from '@/store/index.js';
import { createRouter, createWebHistory } from 'vue-router';

function requireAuth(to, from, next) {
    if (!store.state.auth_token) {
        next({ name: 'login' });
    } else {
        axios.get('/api/user', {
            headers: {
                Authorization: `Bearer ${store.state.auth_token}`
            }
        })
            .then(() => {
                next();
            })
            .catch(() => {
                store.commit('logout');
                next({ name: 'login' });
            });
    }
}

const routes = [
    { path: '/register', name: 'register', component: Register },
    { path: '/login', name: 'login', component: Login },
    { path: '/logout', name: 'logout', component: Logout },
    { path: '/diagrams', name: 'diagrams', component: DiagramList, beforeEnter: requireAuth },
    { path: '/diagrams/:id', name: 'diagram.show', component: Diagram, beforeEnter: requireAuth, props: true },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
