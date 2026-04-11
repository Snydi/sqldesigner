import { watch } from 'vue'

export function useTableInteraction({ findNode, schema, whisper, isSaved, broadcastCursor }) {
    // Keep last-row border-radius in sync whenever row positions change
    watch(
        () => schema.value?.filter(n => n.type === 'row').map(n => `${n.parentNode}:${n.id}:${n.position.y}`).sort().join(','),
        () => {
            if (!schema.value) return
            const best = {}
            schema.value.filter(n => n.type === 'row').forEach(n => {
                if (!best[n.parentNode] || n.position.y > best[n.parentNode].position.y)
                    best[n.parentNode] = n
            })
            const lastIds = new Set(Object.values(best).map(n => n.id))
            schema.value.forEach(n => {
                if (n.type !== 'row' || !n.style) return
                if (lastIds.has(n.id)) n.style.borderRadius = '0 0 6px 6px'
                else delete n.style.borderRadius
            })
        },
        { immediate: true }
    )

    let hoverLeaveTimer = null

    const setTableHovered = (tableId, hovered) => {
        document.querySelectorAll('.vue-flow__node-row').forEach(el => {
            const n = findNode(el.getAttribute('data-id'))
            if (n?.parentNode === tableId) el.classList.toggle('table-hovered', hovered)
        })
    }

    const onNodeMouseEnter = ({ node }) => {
        clearTimeout(hoverLeaveTimer)
        const tableId = node.type === 'table' ? node.id : node.parentNode
        if (tableId) setTableHovered(tableId, true)
    }

    const onNodeMouseLeave = ({ node }) => {
        const tableId = node.type === 'table' ? node.id : node.parentNode
        hoverLeaveTimer = setTimeout(() => setTableHovered(tableId, false), 50)
    }

    const elevateTable = (node) => {
        const tableId = node.type === 'table' ? node.id : node.parentNode
        if (!tableId) return
        const maxZ = schema.value.reduce((m, el) => (el.zIndex > m ? el.zIndex : m), 0)
        const newZ = maxZ + 1
        schema.value.forEach(el => {
            if (el.id === tableId || el.parentNode === tableId) el.zIndex = newZ
        })
    }

    const onNodeDragStart = ({ node }) => elevateTable(node)

    let lastNodeDragWhisper = 0
    const onNodeDrag = ({ node, event }) => {
        const now = Date.now()
        if (now - lastNodeDragWhisper < 50) return
        lastNodeDragWhisper = now
        whisper('schema-patch', { update: [{ id: node.id, position: node.position }] })
        broadcastCursor(event)
    }

    const onNodeDragStop = ({ node }) => {
        isSaved.value = false
        whisper('schema-patch', { update: [{ id: node.id, position: node.position }] })
    }

    return { onNodeMouseEnter, onNodeMouseLeave, elevateTable, onNodeDragStart, onNodeDrag, onNodeDragStop }
}
