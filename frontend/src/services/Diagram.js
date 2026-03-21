import axios from '@/axios'
import { useToast } from 'vue-toast-notification'

const $toast = useToast()

async function request(fn) {
    try {
        return await fn()
    } catch (error) {
        $toast.error(error.response?.data.message ?? 'Something went wrong!')
    }
}

export const Diagram = {
    get: (id) =>
        request(async () => {
            const response = await axios.get(`/api/diagrams/${id}`)
            return response.data.data
        }),

    import: (id, script) =>
        request(async () => {
            const response = await axios.post(`/api/diagrams/sql/import/${id}`, { script: JSON.stringify(script) })
            return JSON.parse(response.data)
        }),

    export: (id) =>
        request(async () => {
            const response = await axios.get(`/api/diagrams/sql/export/${id}`)
            return JSON.parse(response.data)
        }),

    save: (id, schema) =>
        request(async () => {
            const response = await axios.put(`/api/diagrams/${id}`, { id, schema: JSON.stringify(schema) })
            $toast.success(response.data.message)
        }),
}
