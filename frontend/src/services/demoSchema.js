import { Position } from '@vue-flow/core'
import { TABLE_STYLE, ROW_STYLE } from './TableActions.js'

const table = (id, label, x, y, color) => ({
    id,
    type: 'table',
    label,
    data: { toolbarPosition: Position.Top, toolbarVisible: true, color },
    position: { x, y },
    style: { ...TABLE_STYLE, background: color, border: `1px solid ${color}`, borderColor: color },
})

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

const edge = (id, source, target, color) => ({
    id,
    source,
    target,
    type: 'chickenFoot',
    updatable: true,
    style: { stroke: color },
    data: { relationshipType: 'one-to-many', markerStart: 'url(#chickenFoot)', markerEnd: 'none', color },
})

const C = {
    users:      '#3b82f6', // blue
    orders:     '#f97316', // orange
    orderItems: '#8b5cf6', // purple
    products:   '#10b981', // green
    categories: '#ef4444', // red
}

export const DEMO_SCHEMA = [
    // users (col 1)
    table('dt1', 'users', 0, 0, C.users),
    row('dr1',  'id',         40,  'dt1', 'PRIMARY KEY', 'INT(11)'),
    row('dr2',  'name',       80,  'dt1', 'None',        'VARCHAR(100)'),
    row('dr3',  'email',      120, 'dt1', 'UNIQUE',      'VARCHAR(255)'),
    row('dr4',  'created_at', 160, 'dt1', 'None',        'TIMESTAMP'),

    // orders (col 2, top)
    table('dt2', 'orders', 430, 0, C.orders),
    row('dr5',  'id',          40,  'dt2', 'PRIMARY KEY', 'INT(11)'),
    row('dr6',  'user_id',     80,  'dt2', 'FOREIGN KEY', 'INT(11)'),
    row('dr7',  'status',      120, 'dt2', 'None',        'VARCHAR(50)'),
    row('dr8',  'total',       160, 'dt2', 'None',        'DECIMAL(10,2)'),
    row('dr9',  'created_at',  200, 'dt2', 'None',        'TIMESTAMP'),

    // order_items (col 3, top)
    table('dt3', 'order_items', 860, 0, C.orderItems),
    row('dr10', 'id',         40,  'dt3', 'PRIMARY KEY', 'INT(11)'),
    row('dr11', 'order_id',   80,  'dt3', 'FOREIGN KEY', 'INT(11)'),
    row('dr12', 'product_id', 120, 'dt3', 'FOREIGN KEY', 'INT(11)'),
    row('dr13', 'quantity',   160, 'dt3', 'None',        'INT(11)'),
    row('dr14', 'price',      200, 'dt3', 'None',        'DECIMAL(10,2)'),

    // categories (col 1, bottom)
    table('dt5', 'categories', 0, 380, C.categories),
    row('dr20', 'id',   40, 'dt5', 'PRIMARY KEY', 'INT(11)'),
    row('dr21', 'name', 80, 'dt5', 'None',        'VARCHAR(100)'),

    // products (col 2, bottom)
    table('dt4', 'products', 430, 380, C.products),
    row('dr15', 'id',          40,  'dt4', 'PRIMARY KEY', 'INT(11)'),
    row('dr16', 'category_id', 80,  'dt4', 'FOREIGN KEY', 'INT(11)'),
    row('dr17', 'name',        120, 'dt4', 'None',        'VARCHAR(255)'),
    row('dr18', 'price',       160, 'dt4', 'None',        'DECIMAL(10,2)'),
    row('dr19', 'stock',       200, 'dt4', 'None',        'INT(11)'),

    // edges
    edge('de1', 'dr6',  'dr1',  C.users),      // users.id → orders.user_id
    edge('de2', 'dr11',  'dr5', C.orders),     // orders.id → order_items.order_id
    edge('de3', 'dr12', 'dr15', C.products),   // products.id → order_items.product_id
    edge('de4', 'dr16', 'dr20', C.categories), // categories.id → products.category_id
]
