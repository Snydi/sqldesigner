import { Position } from '@vue-flow/core'
import { TABLE_STYLE, ROW_STYLE } from './TableActions.js'

const row = (id, label, y, parentNode, keyMod, sqlType) => ({
    id,
    type: 'row',
    label,
    position: { x: 0, y },
    style: ROW_STYLE,
    draggable: false,
    parentNode,
    data: { editing: false, showModal: false, showOptionsModal: false, keyMod, sqlType, nullable: false, unsigned: false },
})

const table = (id, label, x, y) => ({
    id,
    type: 'table',
    label,
    data: { toolbarPosition: Position.Top, toolbarVisible: true },
    position: { x, y },
    style: TABLE_STYLE,
})

const edge = (id, source, target) => ({
    id,
    source,
    target,
    type: 'chickenFoot',
    updatable: true,
    data: { relationshipType: 'one-to-many', markerStart: 'url(#chickenFoot)', markerEnd: 'none' },
})

export const DEMO_SCHEMA = [
    table('dt1', 'users', 0, 0),
    row('dr1', 'id', 40, 'dt1', 'PRIMARY KEY', 'INT(11)'),
    row('dr2', 'username', 80, 'dt1', 'None', 'VARCHAR(255)'),
    row('dr3', 'email', 120, 'dt1', 'None', 'VARCHAR(255)'),
    row('dr4', 'created_at', 160, 'dt1', 'None', 'TIMESTAMP'),

    table('dt2', 'posts', 450, 0),
    row('dr5', 'id', 40, 'dt2', 'PRIMARY KEY', 'INT(11)'),
    row('dr6', 'user_id', 80, 'dt2', 'FOREIGN KEY', 'INT(11)'),
    row('dr7', 'title', 120, 'dt2', 'None', 'VARCHAR(255)'),
    row('dr8', 'body', 160, 'dt2', 'None', 'TEXT'),
    row('dr9', 'created_at', 200, 'dt2', 'None', 'TIMESTAMP'),

    table('dt3', 'comments', 225, 380),
    row('dr10', 'id', 40, 'dt3', 'PRIMARY KEY', 'INT(11)'),
    row('dr11', 'post_id', 80, 'dt3', 'FOREIGN KEY', 'INT(11)'),
    row('dr12', 'user_id', 120, 'dt3', 'FOREIGN KEY', 'INT(11)'),
    row('dr13', 'body', 160, 'dt3', 'None', 'TEXT'),
    row('dr14', 'created_at', 200, 'dt3', 'None', 'TIMESTAMP'),

    edge('de1', 'dr1', 'dr6'),
    edge('de2', 'dr1', 'dr12'),
    edge('de3', 'dr5', 'dr11'),
]
