import { useToast } from 'vue-toast-notification'
import router from '../router/index.js'
import store from '@/store/index.js'
import axios from '@/axios'
import { navigateAfterAuth } from '@/js/auth-redirect.js'

async function authenticate(endpoint, userData, redirectTo) {
    const $toast = useToast({ position: 'bottom-right' })
    try {
        const response = await axios.post(endpoint, { email: userData.email, password: userData.password })
        $toast.success(response.data.message)
        store.commit('login', response.data.token)
        if (typeof redirectTo === 'string' && redirectTo.startsWith('/')) {
            await navigateAfterAuth(router, redirectTo)
        } else {
            await router.push({ name: redirectTo })
        }
    } catch (error) {
        $toast.error(error.response?.data?.message ?? 'An error occurred')
    }
}

export const Auth = {
    register: (userData) => authenticate('/api/register', userData, 'verify-email'),
    login: (userData, redirectTo = '/diagrams') => authenticate('/api/login', userData, redirectTo),

    async logout() {
        const $toast = useToast({ position: 'bottom-right' })
        try {
            const response = await axios.post('/api/logout')
            $toast.success(response.data.message)
        } catch (error) {
            if (error.response?.status !== 401) {
                $toast.error(error.response?.data?.message ?? 'An error occurred')
            }
        } finally {
            store.commit('logout')
            window.location.href = '/' //TODO potentially will prevent SSR in the future and gives 1 sec of white screen
        }
    }
}
