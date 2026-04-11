const MIN_TABLE_WIDTH = 350

export function useTableResize({ schema, viewport, whisper, isSaved }) {
    const startTableResize = (tableId, event, side) => {
        const tableNode = schema.value.find(el => el.id === tableId)
        if (!tableNode) return

        const startX = event.clientX
        const startWidth = parseInt(tableNode.style.width) || MIN_TABLE_WIDTH
        const startPositionX = tableNode.position.x

        let finalWidthPx = `${startWidth}px`
        let finalPositionX = startPositionX
        let lastResizeWhisper = 0

        const buildResizeUpdates = () =>
            schema.value
                .filter(node => node.id === tableId || node.parentNode === tableId)
                .map(node => {
                    const u = { id: node.id, style: { ...node.style } }
                    if (node.id === tableId) u.position = { ...node.position }
                    return u
                })

        const onMouseMove = (e) => {
            const deltaX = (e.clientX - startX) / viewport.value.zoom
            if (side === 'left') {
                const newWidth = Math.max(MIN_TABLE_WIDTH, startWidth - deltaX)
                finalWidthPx = `${newWidth}px`
                finalPositionX = startPositionX + (startWidth - newWidth)
            } else {
                finalWidthPx = `${Math.max(MIN_TABLE_WIDTH, startWidth + deltaX)}px`
                finalPositionX = startPositionX
            }
            schema.value.forEach(node => {
                if (node.id === tableId || node.parentNode === tableId) {
                    node.style = { ...node.style, width: finalWidthPx }
                }
                if (node.id === tableId) {
                    node.position = { ...node.position, x: finalPositionX }
                }
            })
            const now = Date.now()
            if (now - lastResizeWhisper >= 50) {
                lastResizeWhisper = now
                whisper('schema-patch', { update: buildResizeUpdates() })
            }
        }

        const onMouseUp = () => {
            window.removeEventListener('mousemove', onMouseMove)
            window.removeEventListener('mouseup', onMouseUp)
            isSaved.value = false
            whisper('schema-patch', { update: buildResizeUpdates() })
        }

        window.addEventListener('mousemove', onMouseMove)
        window.addEventListener('mouseup', onMouseUp)
    }

    return { startTableResize }
}
