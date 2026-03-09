import { useToast } from 'vue-toast-notification'
import router from '../router/index.js'
import store from '@/store/index.js'
import axios from '@/axios'

const $toast = useToast()

async function authenticate(endpoint, userData) {
    await axios.get('/sanctum/csrf-cookie')
    const response = await axios.post(endpoint, { email: userData.email, password: userData.password })
    store.commit('login', response.data.token)
    $toast.success(response.data.message)
    await router.push({ name: 'diagrams' })
}

export const Auth = {
    register: (userData) => authenticate('/api/register', userData),
    login: (userData) => authenticate('/api/login', userData),

    async logout() {
        const response = await axios.post('/api/logout')
        store.commit('logout')
        response.status ? $toast.success(response.data.message) : $toast.error(response.data.message)
        window.location.href = '/' //TODO potentially will prevent SSR in the future and gives 1 sec of white screen
    }
}
