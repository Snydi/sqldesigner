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

    getByToken: async (token) => {
        try {
            const response = await axios.get(`/api/diagrams/shared/${token}`)
            return response.data.data
        } catch (error) {
            if (error.response?.status === 403 && error.response?.data?.pending_approval) {
                return { pending_approval: true }
            }
            $toast.error(error.response?.data?.message ?? 'Something went wrong!')
            return null
        }
    },

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

    exportJson: (id) =>
        request(async () => {
            const response = await axios.get(`/api/diagrams/json/export/${id}`)
            return JSON.stringify(response.data, null, 2)
        }),

    exportMigration: async (id) => {
        const response = await axios.get(`/api/diagrams/migration/export/${id}`, { responseType: 'blob' })
        return response.data
    },

    save: (id, schema) =>
        request(async () => {
            const response = await axios.put(`/api/diagrams/${id}`, { id, schema: JSON.stringify(schema) })
            $toast.success(response.data.message)
        }),

    share: (id) =>
        request(async () => {
            const response = await axios.post(`/api/diagrams/${id}/share`)
            return response.data.share_token
        }),

    unshare: (id) =>
        request(async () => {
            await axios.delete(`/api/diagrams/${id}/share`)
        }),

    saveByToken: (token, schema) =>
        request(async () => {
            await axios.patch(`/api/diagrams/shared/${token}`, { schema: JSON.stringify(schema) })
        }),

    updateShareAccess: (id, payload) =>
        request(async () => {
            const response = await axios.patch(`/api/diagrams/${id}/share`, payload)
            return response.data
        }),

    updateRequireApproval: (id, requireApproval) =>
        request(async () => {
            const response = await axios.patch(`/api/diagrams/${id}/share`, { require_approval: requireApproval })
            return response.data.require_approval
        }),

    setAccessMode: (id, mode) =>
        request(async () => {
            const response = await axios.patch(`/api/diagrams/${id}/share`, { access: mode })
            return response.data
        }),

    getVisitors: (id) =>
        request(async () => {
            const response = await axios.get(`/api/diagrams/${id}/visitors`)
            return response.data
        }),

    approveVisitor: (diagramId, visitorId) =>
        request(async () => {
            const response = await axios.post(`/api/diagrams/${diagramId}/visitors/${visitorId}/approve`)
            return response.data
        }),

    updateVisitorAccess: (diagramId, visitorId, access) =>
        request(async () => {
            const response = await axios.patch(`/api/diagrams/${diagramId}/visitors/${visitorId}`, { access })
            return response.data
        }),

    getChangelog: (id) =>
        request(async () => {
            const response = await axios.get(`/api/diagrams/${id}/changelog`)
            return response.data
        }),

    addChangelogEntry: (id, action, details = null) =>
        request(async () => {
            await axios.post(`/api/diagrams/${id}/changelog`, { action, details })
        }),

}
