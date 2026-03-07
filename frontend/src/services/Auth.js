import { useToast } from 'vue-toast-notification'
import router from '../router/index.js'
import store from '@/store/index.js'
import axios from '@/axios'

const $toast = useToast()

export const Auth = {

    async register(userData) {
        await axios.get('/sanctum/csrf-cookie')
        const response = await axios.post('/api/register', {
            email: userData.email,
            password: userData.password
        })
        store.commit('login', response.data.token)
        response.status ? $toast.success(response.data.message) : $toast.error(response.data.message)
        await router.push({ name: 'diagrams' })
    },

    async login(userData) {
        await axios.get('/sanctum/csrf-cookie')
        const response = await axios.post('/api/login', {
            email: userData.email,
            password: userData.password
        })
        store.commit('login', response.data.token)
        response.status ? $toast.success(response.data.message) : $toast.error(response.data.message)
        await router.push({ name: 'diagrams' })
    },

    async logout() {
        const response = await axios.post('/api/logout')
        store.commit('logout')
        response.status ? $toast.success(response.data.message) : $toast.error(response.data.message)
        window.location.href = '/' //TODO potentially will prevent SSR in the future and gives 1 sec of white screen
    }
}
