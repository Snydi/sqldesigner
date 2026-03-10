import { useToast } from 'vue-toast-notification'
import router from '../router/index.js'
import store from '@/store/index.js'
import axios from '@/axios'

async function authenticate(endpoint, userData) {
    const $toast = useToast({ position: 'bottom-right' })
    try {
        const response = await axios.post(endpoint, { email: userData.email, password: userData.password })
        $toast.success(response.data.message)
        store.commit('login', response.data.token)
        await router.push({ name: 'diagrams' })
    } catch (error) {
        $toast.error(error.response?.data?.message ?? 'An error occurred')
    }
}

export const Auth = {
    register: (userData) => authenticate('/api/register', userData),
    login: (userData) => authenticate('/api/login', userData),

    async logout() {
        const $toast = useToast({ position: 'bottom-right' })
        const response = await axios.post('/api/logout')
        store.commit('logout')
        response.status ? $toast.success(response.data.message) : $toast.error(response.data.message)
        window.location.href = '/' //TODO potentially will prevent SSR in the future and gives 1 sec of white screen
    }
}
