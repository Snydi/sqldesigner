import { ref } from 'vue'

export function useRowDrag({ schema, isSaved, whisper }) {
    const draggingRowId = ref(null)

    const startRowDrag = (id) => {
        draggingRowId.value = id

        const onMouseMove = (e) => {
            const rowNodeEl = document.elementsFromPoint(e.clientX, e.clientY)
                .find(el => el.classList.contains('vue-flow__node-row') && el.getAttribute('data-id') !== draggingRowId.value)
            if (!rowNodeEl) return

            const sourceNode = schema.value.find(el => el.id === draggingRowId.value)
            const targetNode = schema.value.find(el => el.id === rowNodeEl.getAttribute('data-id'))

            if (!sourceNode || !targetNode || sourceNode.type !== 'row' || targetNode.type !== 'row' || sourceNode.parentNode !== targetNode.parentNode) return

            const tempY = sourceNode.position.y
            sourceNode.position.y = targetNode.position.y
            targetNode.position.y = tempY

            isSaved.value = false
            const siblingRows = schema.value
                .filter(el => el.type === 'row' && el.parentNode === sourceNode.parentNode)
                .sort((a, b) => a.position.y - b.position.y)
            whisper('schema-patch', {
                update: siblingRows.map(r => ({ id: r.id, position: { ...r.position } }))
            })
        }

        const onMouseUp = () => {
            draggingRowId.value = null
            document.removeEventListener('mousemove', onMouseMove)
            document.removeEventListener('mouseup', onMouseUp)
        }

        document.addEventListener('mousemove', onMouseMove)
        document.addEventListener('mouseup', onMouseUp)
    }

    return { draggingRowId, startRowDrag }
}
