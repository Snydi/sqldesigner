<template>
    <svg
        v-if="tables.length > 0"
        :viewBox="viewBox"
        width="100%"
        height="100%"
        preserveAspectRatio="xMidYMid meet"
    >
        <!-- Edges (drawn behind tables) -->
        <path
            v-for="edge in computedEdges"
            :key="edge.id"
            :d="edge.path"
            fill="none"
            :stroke="edge.color"
            stroke-width="8"
            opacity="0.6"
        />

        <!-- Tables -->
        <g v-for="table in tables" :key="table.id">
            <!-- Body -->
            <rect
                :x="table.x" :y="table.y"
                :width="TABLE_W" :height="table.h"
                fill="#484848" rx="5"
            />
            <!-- Header background -->
            <rect
                :x="table.x" :y="table.y"
                :width="TABLE_W" :height="HEADER_H"
                :fill="table.color" rx="5"
            />
            <!-- Header bottom radius fix -->
            <rect
                :x="table.x" :y="table.y + HEADER_H / 2"
                :width="TABLE_W" :height="HEADER_H / 2"
                :fill="table.color"
            />
            <!-- Table name -->
            <text
                :x="table.x + TABLE_W / 2"
                :y="table.y + HEADER_H * 0.66"
                fill="white"
                text-anchor="middle"
                font-size="14"
                font-family="'Inter', sans-serif"
            >{{ truncate(table.label, 22) }}</text>

            <!-- Rows -->
            <g v-for="(row, i) in table.rows" :key="row.id">
                <line
                    :x1="table.x" :y1="table.y + HEADER_H + ROW_H * i"
                    :x2="table.x + TABLE_W" :y2="table.y + HEADER_H + ROW_H * i"
                    stroke="#3c3c3c" stroke-width="1"
                />
                <text
                    :x="table.x + 12"
                    :y="table.y + HEADER_H + ROW_H * i + ROW_H * 0.6"
                    fill="#e0e0e0"
                    font-size="11"
                    font-family="'Inter', sans-serif"
                >{{ truncate(row.label, 28) }}</text>
            </g>

            <!-- Bottom border for last row -->
            <rect
                :x="table.x" :y="table.y"
                :width="TABLE_W" :height="table.h"
                fill="none"
                stroke="#3c3c3c"
                stroke-width="1"
                rx="5"
            />
        </g>
    </svg>

    <div v-else class="preview-empty">
        <span>Empty</span>
    </div>
</template>

<script>
const TABLE_W = 350
const HEADER_H = 40
const ROW_H = 40
const PADDING = 60

export default {
    props: {
        schema: { type: [Array, String], default: () => [] }
    },
    setup() {
        return { TABLE_W, HEADER_H, ROW_H }
    },
    computed: {
        parsedSchema() {
            if (!this.schema) return []
            if (typeof this.schema === 'string') {
                try { return JSON.parse(this.schema) } catch { return [] }
            }
            return this.schema
        },
        nodeMap() {
            const map = {}
            for (const item of this.parsedSchema) {
                if (item.position !== undefined) map[item.id] = item
            }
            return map
        },
        absPos() {
            const pos = {}
            for (const [id, node] of Object.entries(this.nodeMap)) {
                const p = node.position || { x: 0, y: 0 }
                if (node.parentNode) {
                    const parent = this.nodeMap[node.parentNode]
                    const pp = parent?.position || { x: 0, y: 0 }
                    pos[id] = { x: pp.x + p.x, y: pp.y + p.y }
                } else {
                    pos[id] = { x: p.x, y: p.y }
                }
            }
            return pos
        },
        tables() {
            return this.parsedSchema
                .filter(n => n.type === 'table')
                .map(n => {
                    const rows = this.parsedSchema.filter(r => r.parentNode === n.id && r.type === 'row')
                    const pos = this.absPos[n.id] || { x: 0, y: 0 }
                    return {
                        id: n.id,
                        label: n.label || '',
                        color: n.style?.background || '#3d7a5c',
                        x: pos.x,
                        y: pos.y,
                        h: HEADER_H + rows.length * ROW_H,
                        rows: rows.map(r => ({ id: r.id, label: r.label || '' }))
                    }
                })
        },
        computedEdges() {
            const tableByRow = {}
            for (const table of this.tables) {
                for (const row of table.rows) {
                    tableByRow[row.id] = table
                }
            }
            return this.parsedSchema
                .filter(n => n.source && n.target)
                .map(edge => {
                    const src = tableByRow[edge.source]
                    const tgt = tableByRow[edge.target]
                    if (!src || !tgt || src.id === tgt.id) return null
                    const x1 = src.x + TABLE_W
                    const y1 = src.y + src.h / 2
                    const x2 = tgt.x
                    const y2 = tgt.y + tgt.h / 2
                    const mx = (x1 + x2) / 2
                    return {
                        id: edge.id,
                        color: edge.data?.color || edge.style?.stroke || '#2e5c45',
                        path: `M ${x1} ${y1} C ${mx} ${y1}, ${mx} ${y2}, ${x2} ${y2}`
                    }
                })
                .filter(Boolean)
        },
        viewBox() {
            if (this.tables.length === 0) return '0 0 400 200'
            const xs = this.tables.flatMap(t => [t.x, t.x + TABLE_W])
            const ys = this.tables.flatMap(t => [t.y, t.y + t.h])
            const minX = Math.min(...xs) - PADDING
            const minY = Math.min(...ys) - PADDING
            const w = Math.max(...xs) - minX + PADDING
            const h = Math.max(...ys) - minY + PADDING
            return `${minX} ${minY} ${w} ${h}`
        }
    },
    methods: {
        truncate(str, len = 20) {
            if (!str) return ''
            return str.length > len ? str.slice(0, len - 1) + '\u2026' : str
        }
    }
}
</script>

<style scoped>
.preview-empty {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--border-color);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1px;
}
</style>
