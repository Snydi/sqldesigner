import { Position } from '@vue-flow/core'

export const TABLE_STYLE = {
    display: 'flex',
    border: '1px solid #10b981',
    background: '#6c757d',
    borderColor: '#6c757d',
    color: 'white',
    width: '350px',
    height: '40px',
    alignItems: 'center',
    justifyContent: 'space-between'
}

const ROW_STYLE = {
    display: 'flex',
    border: '1px solid #10b981',
    borderColor: '#898989',
    background: '#ffffff',
    color: '#000000',
    width: '350px',
    height: '40px',
    alignItems: 'center',
    justifyContent: 'space-between'
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

    addTable(schemaRef, name) {
        const schema = schemaRef.value
        const tableId = Math.random().toString()
        const existingTables = schema.filter(el => el.type === 'table')

        const tableName = uniqueName(name, existingTables.map(t => t.label))

        // Use x: -400 as sentinel so the first table lands at x: 0 without a separate if-check
        const rightmost = existingTables.reduce(
            (best, t) => t.position.x > best.position.x ? t : best,
            { position: { x: -400, y: 0 } }
        )
        const position = { x: rightmost.position.x + 400, y: rightmost.position.y }

        schemaRef.value = [...schema, {
            id: tableId,
            type: 'table',
            label: tableName,
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
        const existingRows = schema.filter(el => el.parentNode === nodeProps.id)
        const position = nodeProps.data.position || { x: 0, y: 0 }
        const rowName = uniqueName(rowProps.rowName, existingRows.map(r => r.label))
        const id = Math.floor(Math.random() * 100000).toString()

        schemaRef.value = [...schema, {
            id,
            type: 'row',
            label: rowName,
            position: { x: position.x, y: position.y + 40 + 40 * existingRows.length },
            style: ROW_STYLE,
            draggable: false,
            parentNode: nodeProps.id,
            data: {
                editing: false,
                showModal: false,
                showOptionsModal: false,
                keyMod: rowProps.keyMod,
                sqlType: rowProps.sqlType,
                nullable: rowProps.nullable,
                unsigned: rowProps.unsigned
            }
        }]

        return id
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
            schemaRef.value = schema.filter(el => el.id !== nodeId && (el.type !== 'row' || el.parentNode !== nodeId))
        } else if (nodeToDelete.type === 'row') {
            schemaRef.value = schema.filter(el => el.id !== nodeId)
            schema
                .filter(el => el.parentNode === nodeToDelete.parentNode && el.position.y > nodeToDelete.position.y)
                .forEach((row, index) => { row.position.y = nodeToDelete.position.y + 40 * index })
        }
    }
}
