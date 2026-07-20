import axios from '@/axios.js'

export const Subscription = {
    async get() {
        const { data } = await axios.get('/api/subscription/me')
        return data
    },

    async checkout() {
        const { data } = await axios.post('/api/subscription/checkout')
        return data
    },

    async cancel() {
        const { data } = await axios.delete('/api/subscription/current')
        return data
    },

    submitCheckoutForm(checkout) {
        const form = document.createElement('form')
        form.method = checkout.form.method
        form.action = checkout.form.action

        Object.entries(checkout.form.fields).forEach(([name, value]) => {
            const input = document.createElement('input')
            input.type = 'hidden'
            input.name = name
            input.value = value
            form.appendChild(input)
        })

        document.body.appendChild(form)
        form.submit()
    },
}

export function isPlanLimitError(error) {
    const message = error?.response?.data?.message ?? error?.message ?? ''
    return (error?.response?.status === 403 && message.startsWith('Free plan is limited'))
        || message.startsWith('Free plan is limited')
}
