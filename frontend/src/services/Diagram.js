import axios from '@/axios'
import { useToast } from 'vue-toast-notification'
import store from '@/store/index.js'

const $toast = useToast()
store.dispatch('initializeAuth')

export const Diagram = {
    async get(id) {
        try {
            const response = await axios.get(`/api/diagrams/${id}`)
            return JSON.parse(response.data.data.schema)
        } catch (error) {
            if (error.response) {
                $toast.error(error.response.data.message)
            } else {
                $toast.error('Something went wrong!')
            }
        }
    },
    async import(id, script) {
        try {
            const response = await axios.post(`/api/diagrams/sql/import/${id}`,
                {
                    script: JSON.stringify(script)
                })
            return JSON.parse(response.data)
        } catch (error) {
            if (error.response) {
                $toast.error(error.response.data.message)
            } else {
                $toast.error('Something went wrong!')
            }
        }
    },
    async export(id) {
        try {
            const response = await axios.get(`/api/diagrams/sql/export/${id}`)
            return JSON.parse(response.data)
        } catch (error) {
            if (error.response) {
                $toast.error(error.response.data.message)
            } else {
                $toast.error('Something went wrong!')
            }
        }
    },
    async save(id, schema) {
        try {
            const response = await axios.put(`/api/diagrams/${id}`, {
                id: id,
                schema: JSON.stringify(schema)
            })
            $toast.success(response.data.message)
        } catch (error) {
            if (error.response) {
                $toast.error(error.response.data.message)
            } else {
                $toast.error('Something went wrong!')
            }
        }
    }
}
