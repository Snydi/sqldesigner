import axios from '@/axios';
import store from '@/store/index.js';
import { createRouter, createWebHistory } from 'vue-router';

const Layout = () => import('../components/Layout.vue');
const Register = () => import('../components/Auth/Register.vue');
const Login = () => import('../components/Auth/Login.vue');
const Logout = () => import('../components/Auth/Logout.vue');
const VerifyEmail = () => import('../components/Auth/VerifyEmail.vue');
const GoogleCallback = () => import('../components/Auth/GoogleCallback.vue');
const DiagramList = () => import('../components/DiagramList.vue');
const Diagram = () => import('../components/Diagram.vue');

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
    {
        path: '/',
        component: Layout,
        children: [
            { path: 'register', name: 'register', component: Register },
            { path: 'login', name: 'login', component: Login },
            { path: 'logout', name: 'logout', component: Logout },
            { path: 'verify-email', name: 'verify-email', component: VerifyEmail },
            { path: 'auth/callback', name: 'auth.callback', component: GoogleCallback },
            { path: 'diagrams', name: 'diagrams', component: DiagramList, beforeEnter: requireAuth },
            { path: 'diagrams/:token', name: 'diagram.show', component: Diagram },
            { path: 'demo', name: 'demo', component: Diagram, props: { isDemo: true } },
            { path: 'shared/:token', redirect: to => ({ name: 'diagram.show', params: { token: to.params.token } }) },
        ]
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

const pageTitles = {
    'register': 'Register — SQL Designer',
    'login': 'Login — SQL Designer',
    'verify-email': 'Verify Email — SQL Designer',
    'diagrams': 'My Diagrams — SQL Designer',
    'diagram.show': 'Diagram Editor — SQL Designer',
    'demo': 'Try Demo — SQL Designer',
    'shared.diagram': 'Shared Diagram — SQL Designer',
};

router.afterEach((to) => {
    document.title = pageTitles[to.name] || 'SQL Designer — MySQL & PostgreSQL Schema Designer';
});

export default router;
