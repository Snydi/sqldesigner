import { ref, nextTick } from 'vue'
import { TableActions } from '@/services/TableActions.js'

export function useSchemaActions({ schema, isSaved, whisper, diagramDbType, addEdges, updateEdge, findNode, screenToFlowCoordinate, snapshot, logAction = () => {} }) {
    const isPlacingTable = ref(false)
    const isConnecting = ref(false)
    const copyingTableId = ref(null)
    const selectedEdge = ref(null)
    const showRelationshipModal = ref(false)
    const modalPosition = ref({ x: 0, y: 0 })

    // --- Add/copy/place table ---

    const addTable = () => {
        copyingTableId.value = null
        isPlacingTable.value = true
    }

    const copyTable = (tableId) => {
        copyingTableId.value = tableId
        isPlacingTable.value = true
    }

    const onPaneClick = (event) => {
        if (!isPlacingTable.value) return
        snapshot()
        isPlacingTable.value = false
        const position = screenToFlowCoordinate({ x: event.clientX, y: event.clientY })
        let tableId
        const sourceTableId = copyingTableId.value
        const sourceTable = sourceTableId ? schema.value.find(el => el.id === sourceTableId) : null
        if (sourceTableId) {
            tableId = TableActions.copyTable(schema, sourceTableId, position)
            copyingTableId.value = null
        } else {
            tableId = TableActions.addTable(schema, 'new_table', position)
        }
        isSaved.value = false
        if (sourceTableId) {
            logAction('table_copied', { table_name: sourceTable?.label })
        } else {
            const tableNode = schema.value.find(el => el.id === tableId)
            logAction('table_created', { table_name: tableNode?.label })
        }
        const nodes = schema.value.filter(el => el.id === tableId || el.parentNode === tableId)
        whisper('schema-patch', { add: nodes })
    }

    // --- Row operations ---

    const addRow = (nodeProps) => {
        snapshot()
        const parentTable = schema.value.find(el => el.id === nodeProps.id)
        const rowId = TableActions.addRow(schema, nodeProps, {
            rowName: 'new_row',
            keyMod: 'None',
            sqlType: diagramDbType.value === 'postgresql' ? 'INTEGER' : 'INT(11)',
            nullable: false,
            unsigned: false,
        })
        isSaved.value = false
        logAction('column_added', { table_name: parentTable?.label })
        const newRow = schema.value.find(el => el.id === rowId)
        const button = schema.value.find(el => el.type === 'add-row-button' && el.parentNode === nodeProps.id)
        const patch = { add: [newRow] }
        if (button) patch.update = [{ id: button.id, position: button.position }]
        whisper('schema-patch', patch)
    }

    const deleteEdge = () => {
        snapshot()
        const edgeId = selectedEdge.value.id
        const srcRow = schema.value.find(el => el.id === selectedEdge.value.source)
        const tgtRow = schema.value.find(el => el.id === selectedEdge.value.target)
        const srcTable = schema.value.find(el => el.id === srcRow?.parentNode)
        const tgtTable = schema.value.find(el => el.id === tgtRow?.parentNode)
        TableActions.deleteEdge(schema, selectedEdge)
        showRelationshipModal.value = false
        isSaved.value = false
        logAction('connection_deleted', { from_table: srcTable?.label, from_column: srcRow?.label, to_table: tgtTable?.label, to_column: tgtRow?.label })
        whisper('schema-patch', { remove: [edgeId] })
    }

    const deleteNode = (nodeId) => {
        snapshot()
        const nodeToDelete = schema.value.find(el => el.id === nodeId)
        const childIds = nodeToDelete?.type === 'table'
            ? schema.value.filter(el => el.parentNode === nodeId).map(el => el.id)
            : []
        const affectedSiblingIds = nodeToDelete?.type === 'row'
            ? [
                ...schema.value.filter(el => el.parentNode === nodeToDelete.parentNode && el.type === 'row' && el.position.y > nodeToDelete.position.y).map(el => el.id),
                ...schema.value.filter(el => el.type === 'add-row-button' && el.parentNode === nodeToDelete.parentNode).map(el => el.id),
              ]
            : []

        if (nodeToDelete?.type === 'table') {
            logAction('table_deleted', { table_name: nodeToDelete.label })
        } else if (nodeToDelete?.type === 'row') {
            const parentTable = schema.value.find(el => el.id === nodeToDelete.parentNode)
            logAction('column_deleted', { table_name: parentTable?.label, column_name: nodeToDelete.label })
        }

        TableActions.deleteNode(schema, nodeId)
        isSaved.value = false

        const updates = affectedSiblingIds
            .map(id => schema.value.find(el => el.id === id))
            .filter(Boolean)
            .map(el => ({ id: el.id, position: el.position }))
        whisper('schema-patch', { remove: [nodeId, ...childIds], update: updates })
    }

    const onConnect = (params) => {
        snapshot()
        params.updatable = true
        const srcRow = schema.value.find(el => el.id === params.source)
        const tgtRow = schema.value.find(el => el.id === params.target)
        const srcTable = schema.value.find(el => el.id === srcRow?.parentNode)
        const tgtTable = schema.value.find(el => el.id === tgtRow?.parentNode)
        const tableColor = tgtTable?.data?.color
        if (tableColor) {
            params.style = { stroke: tableColor }
            params.data = { ...params.data, color: tableColor }
        }
        addEdges([params])
        isSaved.value = false
        logAction('connection_created', { from_table: srcTable?.label, from_column: srcRow?.label, to_table: tgtTable?.label, to_column: tgtRow?.label })
        nextTick(() => {
            const newEdge = schema.value.find(el =>
                el.source === params.source && el.target === params.target &&
                el.sourceHandle === params.sourceHandle && el.targetHandle === params.targetHandle
            )
            if (newEdge) whisper('schema-patch', { add: [newEdge] })
        })
    }

    const onEdgeUpdate = ({ edge, connection }) => {
        snapshot()
        const oldEdgeId = edge.id
        updateEdge(edge, connection)
        isSaved.value = false
        nextTick(() => {
            const newEdge = schema.value.find(el =>
                el.source === connection.source && el.target === connection.target &&
                el.sourceHandle === connection.sourceHandle && el.targetHandle === connection.targetHandle
            )
            whisper('schema-patch', { remove: [oldEdgeId], add: newEdge ? [newEdge] : [] })
        })
    }

    const updateConnectionLineType = (relationshipType) => {
        snapshot()
        if (relationshipType === 'many-to-many') {
            const result = TableActions.createPivotTable(schema, selectedEdge.value)
            showRelationshipModal.value = false
            isSaved.value = false
            if (result) {
                const pivotTable = schema.value.find(el => el.id === result.pivotTableId)
                logAction('table_created', { table_name: pivotTable?.label })
                const addedNodes = schema.value.filter(el =>
                    el.id === result.pivotTableId ||
                    el.parentNode === result.pivotTableId ||
                    result.addedEdgeIds.includes(el.id)
                )
                whisper('schema-patch', { add: addedNodes, remove: [result.removedEdgeId] })
            }
            return
        }

        TableActions.updateConnectionLineType(schema, selectedEdge, relationshipType)
        showRelationshipModal.value = false
        isSaved.value = false
        const srcRow = schema.value.find(el => el.id === selectedEdge.value.source)
        const tgtRow = schema.value.find(el => el.id === selectedEdge.value.target)
        const srcTable = schema.value.find(el => el.id === srcRow?.parentNode)
        const tgtTable = schema.value.find(el => el.id === tgtRow?.parentNode)
        logAction('relationship_changed', { type: relationshipType, from_table: srcTable?.label, from_column: srcRow?.label, to_table: tgtTable?.label, to_column: tgtRow?.label })
        const edge = schema.value.find(el => el.id === selectedEdge.value.id)
        if (edge) whisper('schema-patch', { update: [{ id: edge.id, data: { ...edge.data } }] })
    }

    const onRowChange = (id) => {
        snapshot()
        isSaved.value = false
        const node = schema.value.find(el => el.id === id)
        if (node) whisper('schema-patch', {
            update: [{ id, data: { sqlType: node.data.sqlType, keyMod: node.data.keyMod, nullable: node.data.nullable, unsigned: node.data.unsigned } }]
        })
    }

    const updateLabel = (id, newLabel) => {
        snapshot()
        const element = schema.value.find(el => el.id === id)
        if (element) {
            element.label = newLabel.replace(' ', '_')
            isSaved.value = false
            whisper('schema-patch', { update: [{ id, label: element.label }] })
        }
    }

    const updateEdgeColor = (color) => {
        snapshot(`edge-color-${selectedEdge.value?.id}`)
        const edge = schema.value.find(el => el.id === selectedEdge.value?.id)
        if (!edge) return
        edge.style = { ...edge.style, stroke: color }
        edge.data = { ...edge.data, color }
        isSaved.value = false
        whisper('schema-patch', { update: [{ id: edge.id, style: { ...edge.style }, data: { color } }] })
    }

    const updateTableColor = (tableId, color) => {
        snapshot(`table-color-${tableId}`)
        const tableNode = schema.value.find(el => el.id === tableId)
        if (!tableNode) return
        tableNode.style = { ...tableNode.style, background: color, borderColor: color }
        tableNode.data = { ...tableNode.data, color }

        const childIds = schema.value.filter(el => el.parentNode === tableId).map(el => el.id)
        const connectedEdges = schema.value.filter(el => el.source && childIds.includes(el.source))
        connectedEdges.forEach(edge => {
            edge.style = { ...edge.style, stroke: color }
            edge.data = { ...edge.data, color }
        })

        isSaved.value = false
        const edgeUpdates = connectedEdges.map(edge => ({ id: edge.id, style: { ...edge.style }, data: { color } }))
        whisper('schema-patch', { update: [{ id: tableId, style: { ...tableNode.style }, data: { color } }, ...edgeUpdates] })
    }

    // --- Options modal ---

    const closeAllOptionsModals = () => {
        schema.value.filter(el => el.type === 'row' && el.data.showOptionsModal).forEach(row => {
            row.data.showOptionsModal = false
        })
    }

    const toggleOptionsModal = (id) => {
        const row = schema.value.find(el => el.id === id)
        const willOpen = !row.data.showOptionsModal
        closeAllOptionsModals()
        if (willOpen) {
            row.data.modalPosition = { x: 350, y: 0 }
            row.data.showOptionsModal = true
        }
    }

    // --- Table constraints ---

    const onTableConstraintsChange = (tableId, newConstraints) => {
        snapshot()
        const tableNode = schema.value?.find(el => el.id === tableId)
        if (!tableNode) return
        tableNode.data.uniqueTogether = newConstraints
        isSaved.value = false
        whisper('schema-patch', { update: [{ id: tableId, data: { uniqueTogether: newConstraints } }] })
    }

    const onTableFulltextChange = (tableId, newIndexes) => {
        snapshot()
        const tableNode = schema.value?.find(el => el.id === tableId)
        if (!tableNode) return
        tableNode.data.fulltextIndexes = newIndexes
        isSaved.value = false
        whisper('schema-patch', { update: [{ id: tableId, data: { fulltextIndexes: newIndexes } }] })
    }

    // --- Relationship modal ---

    const openRelationshipModal = ({ edge }) => {
        selectedEdge.value = edge
        const { left, top, width, height } = document.querySelector(`[id="${edge.id}"]`).getBoundingClientRect()
        modalPosition.value = { x: left + window.scrollX + width / 2, y: top + window.scrollY + height / 2 }
        showRelationshipModal.value = true
    }

    const closeRelationshipModal = () => {
        showRelationshipModal.value = false
    }

    return {
        isPlacingTable, isConnecting, copyingTableId,
        selectedEdge, showRelationshipModal, modalPosition,
        addTable, copyTable, onPaneClick,
        addRow, deleteEdge, deleteNode, onConnect, onEdgeUpdate,
        updateConnectionLineType, onRowChange, updateLabel, updateEdgeColor, updateTableColor,
        onTableConstraintsChange, onTableFulltextChange, toggleOptionsModal,
        openRelationshipModal, closeRelationshipModal,
    }
}
