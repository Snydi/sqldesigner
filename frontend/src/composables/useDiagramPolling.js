import { ref, watch } from 'vue'
import { Diagram } from '@/services/Diagram.js'
import axios from '@/axios.js'

export function useDiagramPolling({ diagramId, isOwner, diagramRequireApproval, token, diagramShareAccess, pendingApproval, notAvailable }) {
    const hasPendingVisitors = ref(false)
    let visitorPollInterval = null
    let guestAccessPollInterval = null

    const stopVisitorPolling = () => {
        clearInterval(visitorPollInterval)
        visitorPollInterval = null
    }

    const startVisitorPolling = () => {
        stopVisitorPolling()
        visitorPollInterval = setInterval(async () => {
            const visitors = await Diagram.getVisitors(diagramId.value)
            hasPendingVisitors.value = visitors?.some(v => v.status === 'pending') ?? false
        }, 8000)
    }

    watch([isOwner, diagramRequireApproval], ([owner, approval]) => {
        if (owner && approval) startVisitorPolling()
        else stopVisitorPolling()
    })

    const stopGuestAccessPolling = () => {
        clearInterval(guestAccessPollInterval)
        guestAccessPollInterval = null
    }

    const startGuestAccessPolling = () => {
        stopGuestAccessPolling()
        guestAccessPollInterval = setInterval(async () => {
            try {
                const response = await axios.get(`/api/diagrams/shared/${token}`)
                const info = response.data.data
                if (info?.share_access !== diagramShareAccess.value) {
                    diagramShareAccess.value = info.share_access
                }
            } catch (error) {
                const data = error.response?.data
                if (data?.pending_approval) {
                    pendingApproval.value = true
                    stopGuestAccessPolling()
                } else if (error.response?.status === 403) {
                    diagramShareAccess.value = null
                    notAvailable.value = true
                    stopGuestAccessPolling()
                }
            }
        }, 3000)
    }

    const cleanup = () => {
        stopVisitorPolling()
        stopGuestAccessPolling()
    }

    return { hasPendingVisitors, startVisitorPolling, stopVisitorPolling, startGuestAccessPolling, stopGuestAccessPolling, cleanup }
}
