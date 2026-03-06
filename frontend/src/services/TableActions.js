import { Position } from '@vue-flow/core'

export const TableActions = {

    addTable(schemaRef, TableStyle, name) {
        const schema = schemaRef.value
        const tableId = Math.random().toString()

        let tableName = name
        const existingTableNames = schema
            .filter(el => el.type === 'table')
            .map(table => table.label)

        if (existingTableNames.includes(tableName)) {
            let suffix = 1
            const nameRegex = new RegExp(`^${tableName}_(\\d+)$`)

            existingTableNames.forEach(name => {
                const match = name.match(nameRegex)
                if (match) {
                    const num = parseInt(match[1], 10)
                    if (num >= suffix) {
                        suffix = num + 1
                    }
                }
            })

            tableName = `${tableName}_${suffix}`
        }
        const existingTables = schema.filter(el => el.type === 'table')

        let newX = 0
        let newY = 0

        if (existingTables.length > 0) {
            const rightmostTable = existingTables.reduce((rightmost, table) => {
                return (table.position.x > rightmost.position.x) ? table : rightmost
            }, existingTables[0])
            newX = rightmostTable.position.x + 400
            newY = rightmostTable.position.y
        }

        schemaRef.value = [...schema, {
            id: tableId,
            type: 'table',
            label: tableName,
            data: {
                toolbarPosition: Position.Top,
                toolbarVisible: true
            },
            position: { x: newX, y: newY },
            style: TableStyle
        }]

        this.addRow(schemaRef, {
            id: tableId,
            data: {
                'toolbarPosition': 'top',
                'toolbarVisible': true
            },
            label: null
        }, {
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
        const RowStyle = {
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
        const existingRows = schema.filter(el => el.parentNode === nodeProps.id)
        const position = nodeProps.data.position || { x: 0, y: 0 }

        let rowName = rowProps.rowName
        const existingRowNames = existingRows.map(row => row.label)
        if (existingRowNames.includes(rowName)) {
            let suffix = 1
            const nameRegex = new RegExp(`^${rowName}_(\\d+)$`)

            existingRowNames.forEach(name => {
                const match = name.match(nameRegex)
                if (match) {
                    const num = parseInt(match[1], 10)
                    if (num >= suffix) {
                        suffix = num + 1
                    }
                }
            })

            rowName = `${rowName}_${suffix}`
        }

        const id = Math.floor(Math.random() * 100000).toString()

        schemaRef.value = [...schema, {
            id: id,
            type: 'row',
            label: rowName,
            position: { x: position.x, y: position.y + 40 + 40 * existingRows.length },
            style: RowStyle,
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
        const selectedEdge = selectedEdgeRef.value

        const edgeIndex = schema.findIndex(el => el.id === selectedEdge.id)
        if (edgeIndex !== -1) {
            schema[edgeIndex].data.relationshipType = relationshipType
            schema[edgeIndex].type = 'chickenFoot'

            const markerConfig = {
                'one-to-one': { markerStart: 'none', markerEnd: 'none' },
                'one-to-many': { markerStart: 'url(#chickenFoot)', markerEnd: 'none' },
                'many-to-one': { markerStart: 'none', markerEnd: 'url(#chickenFoot)' },
                'many-to-many': { markerStart: 'url(#chickenFoot)', markerEnd: 'url(#chickenFoot)' }
            }

            const config = markerConfig[relationshipType]
            if (config) {
                schema[edgeIndex].data.markerStart = config.markerStart
                schema[edgeIndex].data.markerEnd = config.markerEnd
            }
        }
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
            const siblingRows = schema.filter(el => el.parentNode === nodeToDelete.parentNode && el.position.y > nodeToDelete.position.y)

            siblingRows.forEach((row, index) => {
                row.position.y = nodeToDelete.position.y + 40 * index
            })
        }
    }
}
