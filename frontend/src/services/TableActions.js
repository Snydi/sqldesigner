import {Position} from "@vue-flow/core";

export const TableActions = {

    addTable(schemaRef, TableStyle, name){
        const schema = schemaRef.value;
        const tableId = Math.random().toString();
        schemaRef.value = [
            ...schema,
            {
                id: tableId,
                type: 'table',
                label: name,
                data: {
                    toolbarPosition: Position.Top,
                    toolbarVisible: true
                },
                position: {x: 0, y: 0},
                style: TableStyle,
            }
        ];
        return tableId;
    },

    addRow(schemaRef, nodeProps, rowProps) {
        const schema = schemaRef.value;
        const RowStyle = {
            display: 'flex',
            border: '1px solid #10b981',
            borderColor: '#898989',
            background: '#ffffff',
            color: '#000000',
            borderRadius: '5px',
            width: '350px',
            height: '40px',
            alignItems: 'center',
            justifyContent: 'space-between',
        }
        const existingRows = schema.filter(el => el.parentNode === nodeProps.id);
        const position = nodeProps.data.position || {x: 0, y: 0};

        const id =  Math. floor(Math. random() * 100000).toString();

        schemaRef.value = [
            ...schema,
            {
                id: id,
                type: 'row',
                label: rowProps.rowName,
                position: {x: position.x, y: position.y + 40 + 40 * existingRows.length},
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
                    unsigned: rowProps.unsigned,
                },
            },
        ];
        return id;
    },

    updateConnectionLineType(schemaRef, selectedEdgeRef, relationshipType) {
        const schema = schemaRef.value;
        const selectedEdge = selectedEdgeRef.value;

        const edgeIndex = schema.findIndex(el => el.id === selectedEdge.id);
        if (edgeIndex !== -1) {
            schema[edgeIndex].data.relationshipType = relationshipType;
            schema[edgeIndex].type = 'chickenFoot';
            if (relationshipType === 'one-to-one') {
                schema[edgeIndex].data.markerStart = 'none';
                schema[edgeIndex].data.markerEnd = 'none';
            } else if (relationshipType === 'one-to-many') {
                schema[edgeIndex].data.markerStart = 'none';
                schema[edgeIndex].data.markerEnd = 'url(#chickenFoot)';
            } else if (relationshipType === 'many-to-many') {
                schema[edgeIndex].data.markerStart = 'url(#chickenFoot)';
                schema[edgeIndex].data.markerEnd = 'url(#chickenFoot)';
            }
        }
    },
    addEdge(schemaRef, sourceId, targetId) {
        schemaRef.value.push({
            id: Math. floor(Math. random() * 100).toString(),
            type: 'chickenFoot',
            source: sourceId,
            target: targetId,
        })
    },

    deleteEdge(schemaRef, selectedEdgeRef) {
        const schema = schemaRef.value;
        const selectedEdge = selectedEdgeRef.value;
        schemaRef.value = schema.filter(el => el.id !== selectedEdge.id);
    },

    deleteNode(schemaRef, nodeId) {
        const schema = schemaRef.value;
        const nodeToDelete = schema.find(el => el.id === nodeId);

        if (nodeToDelete.type === 'table') {
            schemaRef.value = schema.filter(el => el.id !== nodeId && (el.type !== 'row' || el.parentNode !== nodeId));
        } else {
            schemaRef.value = schema.filter(el => el.id !== nodeId);
        }

        if (nodeToDelete.type === 'row') {
            const siblingRows = schema.filter(
                el => el.parentNode === nodeToDelete.parentNode && el.position.y > nodeToDelete.position.y);

            siblingRows.forEach((row, index) => {
                row.position.y = nodeToDelete.position.y + 40 * index;
            });
        }
    },
}
