import { Position } from '@vue-flow/core'

export const TABLE_STYLE = {
    display: 'flex',
    border: '1px solid #898989',
    background: '#898989',
    borderColor: '#898989',
    color: 'white',
    width: '350px',
    height: '40px',
    alignItems: 'center',
    justifyContent: 'space-between',
    borderRadius: '6px 6px 0 0',
}

export const ROW_STYLE = {
    display: 'flex',
    border: '1px solid #10b981',
    borderColor: '#898989',
    background: '#ffffff',
    color: '#000000',
    width: '350px',
    height: '40px',
    alignItems: 'center',
    justifyContent: 'space-between',
}

const MARKER = {
    'one-to-one':   { markerStart: 'none',              markerEnd: 'none' },
    'one-to-many':  { markerStart: 'url(#chickenFoot)', markerEnd: 'none' },
    'many-to-one':  { markerStart: 'none',              markerEnd: 'url(#chickenFoot)' },
    'many-to-many': { markerStart: 'url(#chickenFoot)', markerEnd: 'url(#chickenFoot)' },
}

function uniqueName(name, existingNames) {
    if (!existingNames.includes(name)) return name

    const regex = new RegExp(`^${name}_(\\d+)$`)
    let suffix = 1

    existingNames.forEach(n => {
        const match = n.match(regex)
        if (match) {
            const num = parseInt(match[1], 10)
            if (num >= suffix) suffix = num + 1
        }
    })

    return `${name}_${suffix}`
}

export const TableActions = {

    _nextZIndex(schema) {
        const max = schema.reduce((m, el) => (el.zIndex > m ? el.zIndex : m), 0)
        return max + 1
    },

    copyTable(schemaRef, tableId, position) {
        const schema = schemaRef.value
        const original = schema.find(el => el.id === tableId)
        if (!original) return null

        const existingTables = schema.filter(el => el.type === 'table')
        const newTableId = Math.random().toString()
        const newLabel = uniqueName(original.label, existingTables.map(t => t.label))
        const zIndex = this._nextZIndex(schema)

        const children = schema.filter(el => el.parentNode === tableId && el.type === 'row')
        const newChildren = children.map(child => ({
            id: Math.random().toString(),
            type: child.type,
            label: child.label,
            parentNode: newTableId,
            zIndex,
            position: { x: child.position.x, y: child.position.y },
            style: { ...child.style },
            draggable: child.draggable,
            data: {
                ...child.data,
                editing: false,
                showModal: false,
                showOptionsModal: false,
            },
        }))

        schemaRef.value = [...schema,
            {
                id: newTableId,
                type: original.type,
                label: newLabel,
                zIndex,
                data: { ...original.data },
                position,
                style: { ...original.style },
            },
            ...newChildren,
        ]

        return newTableId
    },

    addTable(schemaRef, name, position) {
        const schema = schemaRef.value
        const tableId = Math.random().toString()
        const existingTables = schema.filter(el => el.type === 'table')
        const zIndex = this._nextZIndex(schema)

        const tableName = uniqueName(name, existingTables.map(t => t.label))

        schemaRef.value = [...schema, {
            id: tableId,
            type: 'table',
            label: tableName,
            zIndex,
            data: { toolbarPosition: Position.Top, toolbarVisible: true },
            position,
            style: TABLE_STYLE
        }]

        this.addRow(schemaRef, { id: tableId, data: {} }, {
            rowName: 'id',
            keyMod: 'PRIMARY KEY',
            sqlType: 'INT(11)',
            nullable: false,
            unsigned: false
        })

        return tableId
    },

    addRow(schemaRef, nodeProps, rowProps) {
        const schema = schemaRef.value
        const existingRows = schema.filter(el => el.parentNode === nodeProps.id && el.type === 'row')
        const position = nodeProps.data.position || { x: 0, y: 0 }
        const rowName = uniqueName(rowProps.rowName, existingRows.map(r => r.label))
        const id = Math.floor(Math.random() * 100000).toString()

        const tableNode = schema.find(el => el.id === nodeProps.id)
        const rowStyle = tableNode?.style?.width
            ? { ...ROW_STYLE, width: tableNode.style.width }
            : ROW_STYLE

        const newRowY = position.y + 40 + 40 * existingRows.length
        schemaRef.value = [...schema, {
            id,
            type: 'row',
            label: rowName,
            zIndex: tableNode?.zIndex ?? 1,
            position: { x: position.x, y: newRowY },
            style: rowStyle,
            draggable: false,
            parentNode: nodeProps.id,
            data: {
                editing: false,
                showModal: false,
                showOptionsModal: false,
                keyMod: rowProps.keyMod,
                sqlType: rowProps.sqlType,
                nullable: rowProps.nullable,
                unsigned: rowProps.unsigned,
                defaultValue: rowProps.defaultValue ?? '',
                comment: rowProps.comment ?? ''
            }
        }]

        const button = schemaRef.value.find(el => el.type === 'add-row-button' && el.parentNode === nodeProps.id)
        if (button) button.position = { x: position.x, y: newRowY + 40 }

        return id
    },

    createPivotTable(schemaRef, edge) {
        const schema = schemaRef.value

        const sourceRow = schema.find(el => el.id === edge.source)
        const targetRow = schema.find(el => el.id === edge.target)
        if (!sourceRow || !targetRow) return null

        const sourceTable = schema.find(el => el.id === sourceRow.parentNode)
        const targetTable = schema.find(el => el.id === targetRow.parentNode)
        if (!sourceTable || !targetTable) return null

        const position = {
            x: (sourceTable.position.x + targetTable.position.x) / 2,
            y: Math.max(sourceTable.position.y, targetTable.position.y) + 200
        }

        const pivotName = `${sourceTable.label}_${targetTable.label}`
        const pivotTableId = this.addTable(schemaRef, pivotName, position)

        const sourceFkRowId = this.addRow(schemaRef, { id: pivotTableId, data: {} }, {
            rowName: `${sourceTable.label}_id`,
            keyMod: 'FOREIGN KEY',
            sqlType: 'INT(11)',
            nullable: false,
            unsigned: false
        })

        const targetFkRowId = this.addRow(schemaRef, { id: pivotTableId, data: {} }, {
            rowName: `${targetTable.label}_id`,
            keyMod: 'FOREIGN KEY',
            sqlType: 'INT(11)',
            nullable: false,
            unsigned: false
        })

        // Remove the original many-to-many edge
        schemaRef.value = schemaRef.value.filter(el => el.id !== edge.id)

        // Add two one-to-many edges: FK row (many) → original PK row (one)
        const edge1Id = Math.random().toString()
        const edge2Id = Math.random().toString()
        schemaRef.value = [...schemaRef.value,
            {
                id: edge1Id,
                source: edge.source,
                target: sourceFkRowId,
                sourceHandle: 'source-left',
                targetHandle: 'target-right',
                type: 'chickenFoot',
                updatable: true,
                data: { relationshipType: 'many-to-one', ...MARKER['many-to-one'] }
            },
            {
                id: edge2Id,
                source: edge.target,
                target: targetFkRowId,
                sourceHandle: 'source-right',
                targetHandle: 'target-left',
                type: 'chickenFoot',
                updatable: true,
                data: { relationshipType: 'many-to-one', ...MARKER['many-to-one'] }
            }
        ]

        return { pivotTableId, removedEdgeId: edge.id, addedEdgeIds: [edge1Id, edge2Id] }
    },

    updateConnectionLineType(schemaRef, selectedEdgeRef, relationshipType) {
        const schema = schemaRef.value
        const edgeIndex = schema.findIndex(el => el.id === selectedEdgeRef.value.id)
        if (edgeIndex === -1) return

        const edge = schema[edgeIndex]
        edge.type = 'chickenFoot'
        Object.assign(edge.data, { relationshipType, ...MARKER[relationshipType] })
    },

    deleteEdge(schemaRef, selectedEdgeRef) {
        schemaRef.value = schemaRef.value.filter(el => el.id !== selectedEdgeRef.value.id)
    },

    deleteNode(schemaRef, nodeId) {
        const schema = schemaRef.value
        const nodeToDelete = schema.find(el => el.id === nodeId)

        if (nodeToDelete.type === 'table') {
            schemaRef.value = schema.filter(el => el.id !== nodeId && el.parentNode !== nodeId)
        } else if (nodeToDelete.type === 'row') {
            schemaRef.value = schema.filter(el => el.id !== nodeId)
            schema
                .filter(el => el.parentNode === nodeToDelete.parentNode && el.type === 'row' && el.position.y > nodeToDelete.position.y)
                .forEach((row, index) => { row.position.y = nodeToDelete.position.y + 40 * index })

            const remainingRows = schemaRef.value.filter(el => el.parentNode === nodeToDelete.parentNode && el.type === 'row')
            const button = schemaRef.value.find(el => el.type === 'add-row-button' && el.parentNode === nodeToDelete.parentNode)
            if (button) button.position = { x: 0, y: 40 + 40 * remainingRows.length }
        }
    }
}
