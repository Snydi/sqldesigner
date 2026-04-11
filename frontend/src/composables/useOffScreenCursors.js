import { computed } from 'vue'

const EDGE_PADDING = 44

export function useOffScreenCursors({ remoteCursors, canvasWidth, canvasHeight }) {
    const offScreenCursors = computed(() => {
        const w = canvasWidth.value
        const h = canvasHeight.value
        if (!w || !h) return []
        return Object.values(remoteCursors).filter(c => {
            if (c.flowX === undefined) return false
            return c.screenX < 0 || c.screenX > w || c.screenY < 0 || c.screenY > h
        }).map(c => {
            const cx = w / 2
            const cy = h / 2
            const dx = c.screenX - cx
            const dy = c.screenY - cy
            let t = Infinity
            if (dx > 0) t = Math.min(t, (w - EDGE_PADDING - cx) / dx)
            if (dx < 0) t = Math.min(t, (EDGE_PADDING - cx) / dx)
            if (dy > 0) t = Math.min(t, (h - EDGE_PADDING - cy) / dy)
            if (dy < 0) t = Math.min(t, (EDGE_PADDING - cy) / dy)
            return {
                id: c.id,
                name: c.name,
                color: c.color,
                x: Math.round(cx + t * dx),
                y: Math.round(cy + t * dy),
                angle: Math.atan2(dy, dx) * (180 / Math.PI),
            }
        })
    })

    return { offScreenCursors }
}
