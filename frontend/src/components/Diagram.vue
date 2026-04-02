<template>
    <!-- Loading state -->
    <div v-if="loading" class="diagram-status-screen">
        <span class="diagram-status-screen__text">Loading…</span>
    </div>

    <!-- Not available state -->
    <div v-else-if="notAvailable" class="diagram-status-screen">
        <span class="diagram-status-screen__text">This diagram is not available.</span>
        <button class="btn btn-secondary" style="margin-top:1rem" @click="router.push({ name: 'diagrams' })">My Diagrams</button>
    </div>

    <template v-else>
        <DiagramHeader
            :canEdit="canEdit"
            :isOwner="isOwner"
            :isDemo="isDemo"
            :isSaved="isSaved"
            :diagramName="diagramName"
            @add-table="addTable"
            @import="isDemo ? router.push({ name: 'register' }) : showImportModal = true"
            @export="isDemo ? router.push({ name: 'register' }) : openExportModal()"
            @save="saveDiagram"
            @show-share="showShareModal = true"
        />

        <ShareModal
            v-if="showShareModal"
            :diagramId="diagramId"
            :token="token"
            v-model:shareAccess="diagramShareAccess"
            @close="showShareModal = false"
        />

        <div class="diagram-canvas-wrapper" ref="canvasWrapperRef" @mousemove="onCanvasMouseMove">
            <div class="cursor-layer" aria-hidden="true">
                <RemoteCursor
                    v-for="cursor in remoteCursors"
                    :key="cursor.id"
                    :x="cursor.screenX"
                    :y="cursor.screenY"
                    :name="cursor.name"
                    :color="cursor.color"
                />
                <div
                    v-for="ind in offScreenCursors"
                    :key="'edge-' + ind.id"
                    class="cursor-edge-indicator"
                    :style="{ transform: `translate(calc(${ind.x}px - 50%), calc(${ind.y}px - 50%))` }"
                >
                    <svg class="cursor-edge-indicator__arrow" :style="{ transform: `rotate(${ind.angle}deg)` }" width="14" height="14" viewBox="0 0 14 14" :fill="ind.color">
                        <polygon points="1,3 1,11 13,7" />
                    </svg>
                </div>
            </div>
            <VueFlow
                :default-edge-options="{ type: 'chickenFoot' }"
                @edge-update="canEdit && onEdgeUpdate($event)"
                @edge-click="canEdit && openRelationshipModal($event)"
                @connect="canEdit && onConnect($event)"
                @node-drag-start="onNodeDragStart"
                @node-drag="onNodeDrag"
                @node-drag-stop="onNodeDragStop"
                @node-click="({ node }) => elevateTable(node)"
                @pane-click="onPaneClick"
                @node-mouse-enter="onNodeMouseEnter"
                @node-mouse-leave="onNodeMouseLeave"
                :is-valid-connection="isValidConnection"
                v-model="schema"
                fit-view-on-init
                :zoomOnDoubleClick="false"
                :controlled="false"
                :pan-on-drag="!isPlacingTable"
                :nodes-draggable="canEdit"
                :nodes-connectable="canEdit"
                :edges-updatable="canEdit"
                :class="['diagram-canvas', { 'is-placing-table': isPlacingTable }]"
            >
                <Panel position="top-left" class="table-navigator">
                    <button class="table-navigator__toggle" @click.stop="tableNavOpen = !tableNavOpen" title="Tables">
                        <img src="../icons/table-list.svg" alt="Tables" class="icon" style="width:18px;height:18px;">
                    </button>
                    <div v-if="tableNavOpen" class="table-navigator__list">
                        <button
                            v-for="t in schema.filter(el => el.type === 'table')"
                            :key="t.id"
                            class="table-navigator__item"
                            @click.stop="navigateToTable(t.id)"
                        >{{ t.label }}</button>
                        <span v-if="!schema.filter(el => el.type === 'table').length" class="table-navigator__empty">No tables</span>
                    </div>
                </Panel>

                <template #edge-chickenFoot="props">
                    <ChickenFootEdge v-bind="props" />
                </template>

                <Panel position="bottom-left" class="feedback-panel">
                    <button class="feedback-panel__btn" @click.stop="openFeedbackModal" title="Send feedback">
                        <img src="../icons/chat.svg" alt="Feedback" style="width:16px;height:16px;" />
                    </button>
                </Panel>

                <Background :variant="BackgroundVariant.Lines" />

                <template #node-table="nodeProps">
                    <TableNode
                        :id="nodeProps.id"
                        :data="nodeProps.data"
                        :label="nodeProps.label"
                        :canEdit="canEdit"
                        @delete-node="deleteNode"
                        @update-label="updateLabel"
                        @copy-table="copyTable"
                        @add-row="addRow({ id: $event, data: {} })"
                        @resize-start="startTableResize"
                        @update-color="updateTableColor"
                    />
                </template>

                <template #node-row="nodeProps">
                    <RowNode
                        :id="nodeProps.id"
                        :data="nodeProps.data"
                        :label="nodeProps.label"
                        :dbType="diagramDbType"
                        :canEdit="canEdit"
                        @update-label="updateLabel"
                        @toggle-options-modal="toggleOptionsModal"
                        @delete-node="deleteNode"
                        @change="onRowChange($event)"
                        @row-drag-start="startRowDrag"
                    />
                </template>

                <template #node-add-row-button="nodeProps">
                    <AddRowNode
                        @add-row="addRow({ id: nodeProps.data.tableId, data: {} })"
                    />
                </template>
            </VueFlow>
        </div>

        <RelationshipModal
            v-if="showRelationshipModal"
            :position="modalPosition"
            :edge-color="selectedEdge?.data?.color"
            @update-type="updateConnectionLineType"
            @delete="deleteEdge"
            @close="closeRelationshipModal"
            @update-color="updateEdgeColor"
        />

        <SqlModal
            v-if="showImportModal"
            v-model="importContent"
            primaryLabel="Import"
            :loading="importLoading"
            @primary-action="importSql"
            @close="showImportModal = false"
        />

        <SqlModal
            v-if="showExportModal"
            v-model="exportContent"
            primaryLabel="Export"
            :filename="diagramName"
            :jsonContent="exportJsonContent"
            @primary-action="exportSql"
            @close="showExportModal = false"
        />

        <FeedbackModal
            v-if="showFeedbackModal"
            :user-email="feedbackUserEmail"
            @close="showFeedbackModal = false"
        />
    </template>
</template>

<script setup>
import { computed, onBeforeMount, onMounted, onUnmounted, ref, nextTick, watch } from 'vue'
import { Panel, Position, useVueFlow, VueFlow } from '@vue-flow/core'
import { Background, BackgroundVariant } from '@vue-flow/background'
import { TableActions, TABLE_STYLE } from '@/services/TableActions.js'
import { Diagram } from '@/services/Diagram.js'
import { DEMO_SCHEMA } from '@/services/demoSchema.js'
import { useDiagramPresence, CURSOR_COLORS } from '@/composables/useDiagramPresence.js'
import DiagramHeader from './DiagramHeader.vue'
import ShareModal from './ShareModal.vue'
import ChickenFootEdge from './ChickenFootEdge.vue'
import TableNode from './TableNode.vue'
import RowNode from './RowNode.vue'
import AddRowNode from './AddRowNode.vue'
import RelationshipModal from './RelationshipModal.vue'
import SqlModal from './SqlModal.vue'
import RemoteCursor from './RemoteCursor.vue'
import FeedbackModal from './FeedbackModal.vue'
import { useElementSize } from '@vueuse/core'
import { useToast } from 'vue-toast-notification'
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex'
import axios from '@/axios'
import '@vue-flow/core/dist/style.css'
import '@vue-flow/core/dist/theme-default.css'
import '@/css/diagram.css'
import '@/css/header.css'

const props = defineProps({ isDemo: { type: Boolean, default: false } })

const { updateEdge, addEdges, viewport, screenToFlowCoordinate, findNode, fitView } = useVueFlow()
const store = useStore()
store.dispatch('initializeAuth')
const router = useRouter()
const $toast = useToast()

const token = useRoute().params.token
const diagramId = ref(null)
const isOwner = ref(false)
const notAvailable = ref(false)
const loading = ref(false)

const canEdit = computed(() => isOwner.value || diagramShareAccess.value === 'write')

const isSaved = ref(true)
let autoSaveTimer = null

const schema = ref()
const diagramName = ref('schema')
const diagramDbType = ref('mysql')
const modalPosition = ref({ x: 0, y: 0 })
const selectedEdge = ref(null)
const showRelationshipModal = ref(false)
const showImportModal = ref(false)
const importContent = ref('')
const importLoading = ref(false)
const showExportModal = ref(false)
const exportContent = ref('')
const exportJsonContent = ref('')
const showShareModal = ref(false)
const diagramShareAccess = ref(null)

const ownerIdentity = ref(null)
const canvasWrapperRef = ref(null)
const { width: canvasWidth, height: canvasHeight } = useElementSize(canvasWrapperRef)

const EDGE_PADDING = 44

watch(
    () => schema.value?.filter(n => n.type === 'row').map(n => `${n.parentNode}:${n.id}:${n.position.y}`).sort().join(','),
    () => {
        if (!schema.value) return
        const best = {}
        schema.value.filter(n => n.type === 'row').forEach(n => {
            if (!best[n.parentNode] || n.position.y > best[n.parentNode].position.y)
                best[n.parentNode] = n
        })
        const lastIds = new Set(Object.values(best).map(n => n.id))
        schema.value.forEach(n => {
            if (n.type !== 'row' || !n.style) return
            if (lastIds.has(n.id)) n.style.borderRadius = '0 0 6px 6px'
            else delete n.style.borderRadius
        })
    },
    { immediate: true }
)

const offScreenCursors = computed(() => {
    const w = canvasWidth.value
    const h = canvasHeight.value
    if (!w || !h) return []
    return Object.values(remoteCursors).filter(c => {
        if (c.flowX === undefined) return false
        return c.screenX < 0 || c.screenX > w || c.screenY < 0 || c.screenY > h
    }).map(c => {
        const cx = w / 2
        const cy = h / 2
        const dx = c.screenX - cx
        const dy = c.screenY - cy
        let t = Infinity
        if (dx > 0) t = Math.min(t, (w - EDGE_PADDING - cx) / dx)
        if (dx < 0) t = Math.min(t, (EDGE_PADDING - cx) / dx)
        if (dy > 0) t = Math.min(t, (h - EDGE_PADDING - cy) / dy)
        if (dy < 0) t = Math.min(t, (EDGE_PADDING - cy) / dy)
        return {
            id: c.id,
            name: c.name,
            color: c.color,
            x: Math.round(cx + t * dx),
            y: Math.round(cy + t * dy),
            angle: Math.atan2(dy, dx) * (180 / Math.PI),
        }
    })
})

const { remoteCursors, whisper, initEcho, cleanupEcho, onCanvasMouseMove, broadcastCursor } = useDiagramPresence({
    token,
    ownerIdentity,
    viewport,
    schema,
    canvasWrapperRef,
    onDiagramSaved: () => $toast.success('Diagram saved'),
})

// --- Table hover ---

let hoverLeaveTimer = null

const setTableHovered = (tableId, hovered) => {
    document.querySelectorAll('.vue-flow__node-row').forEach(el => {
        const n = findNode(el.getAttribute('data-id'))
        if (n?.parentNode === tableId) el.classList.toggle('table-hovered', hovered)
    })
}

const isValidConnection = ({ source, target }) => {
    const sourceNode = findNode(source)
    const targetNode = findNode(target)
    return sourceNode?.parentNode !== targetNode?.parentNode
}

const onNodeMouseEnter = ({ node }) => {
    clearTimeout(hoverLeaveTimer)
    const tableId = node.type === 'table' ? node.id : node.parentNode
    if (tableId) setTableHovered(tableId, true)
}

const onNodeMouseLeave = ({ node }) => {
    const tableId = node.type === 'table' ? node.id : node.parentNode
    hoverLeaveTimer = setTimeout(() => setTableHovered(tableId, false), 50)
}

// --- Table elevation ---

const elevateTable = (node) => {
    const tableId = node.type === 'table' ? node.id : node.parentNode
    if (!tableId) return
    const maxZ = schema.value.reduce((m, el) => (el.zIndex > m ? el.zIndex : m), 0)
    const newZ = maxZ + 1
    schema.value.forEach(el => {
        if (el.id === tableId || el.parentNode === tableId) el.zIndex = newZ
    })
}

const onNodeDragStart = ({ node }) => elevateTable(node)

let lastNodeDragWhisper = 0
const onNodeDrag = ({ node, event }) => {
    const now = Date.now()
    if (now - lastNodeDragWhisper < 50) return
    lastNodeDragWhisper = now
    whisper('schema-patch', { update: [{ id: node.id, position: node.position }] })
    broadcastCursor(event)
}

const onNodeDragStop = ({ node }) => {
    isSaved.value = false
    whisper('schema-patch', { update: [{ id: node.id, position: node.position }] })
}

// --- Table resize ---

const MIN_TABLE_WIDTH = 350

const startTableResize = (tableId, event, side) => {
    const tableNode = schema.value.find(el => el.id === tableId)
    if (!tableNode) return

    const startX = event.clientX
    const startWidth = parseInt(tableNode.style.width) || MIN_TABLE_WIDTH
    const startPositionX = tableNode.position.x

    let finalWidthPx = `${startWidth}px`
    let finalPositionX = startPositionX

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
    }

    const onMouseUp = () => {
        window.removeEventListener('mousemove', onMouseMove)
        window.removeEventListener('mouseup', onMouseUp)
        isSaved.value = false
    }

    window.addEventListener('mousemove', onMouseMove)
    window.addEventListener('mouseup', onMouseUp)
}

// --- Table navigator & feedback ---

const tableNavOpen = ref(false)
const showFeedbackModal = ref(false)
const feedbackUserEmail = ref('')

const openFeedbackModal = async () => {
    if (!feedbackUserEmail.value) {
        try {
            const { data } = await axios.get('/api/user')
            feedbackUserEmail.value = data.email ?? ''
        } catch { /* guest */ }
    }
    showFeedbackModal.value = true
}

const navigateToTable = (tableId) => {
    tableNavOpen.value = false
    const tableNode = schema.value.find(el => el.id === tableId)
    if (!tableNode) return
    const childIds = schema.value.filter(el => el.parentNode === tableId).map(el => el.id)
    fitView({ nodes: [tableId, ...childIds], duration: 300, padding: 0.3 })
}

// --- Add/copy/place table ---

const isPlacingTable = ref(false)
const copyingTableId = ref(null)

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
    isPlacingTable.value = false
    const position = screenToFlowCoordinate({ x: event.clientX, y: event.clientY })
    let tableId
    if (copyingTableId.value) {
        tableId = TableActions.copyTable(schema, copyingTableId.value, position)
        copyingTableId.value = null
    } else {
        tableId = TableActions.addTable(schema, 'new_table', position)
    }
    isSaved.value = false
    const nodes = schema.value.filter(el => el.id === tableId || el.parentNode === tableId)
    whisper('schema-patch', { add: nodes })
}

const onEscapeKey = (event) => {
    if (event.key === 'Escape') {
        isPlacingTable.value = false
        copyingTableId.value = null
    }
}

// --- Row operations ---

const addRow = (nodeProps) => {
    const rowId = TableActions.addRow(schema, nodeProps, {
        rowName: 'new_row',
        keyMod: 'None',
        sqlType: diagramDbType.value === 'postgresql' ? 'INTEGER' : 'INT(11)',
        nullable: false,
        unsigned: false,
    })
    isSaved.value = false
    const newRow = schema.value.find(el => el.id === rowId)
    const button = schema.value.find(el => el.type === 'add-row-button' && el.parentNode === nodeProps.id)
    const patch = { add: [newRow] }
    if (button) patch.update = [{ id: button.id, position: button.position }]
    whisper('schema-patch', patch)
}

const deleteEdge = () => {
    const edgeId = selectedEdge.value.id
    TableActions.deleteEdge(schema, selectedEdge)
    showRelationshipModal.value = false
    isSaved.value = false
    whisper('schema-patch', { remove: [edgeId] })
}

const deleteNode = (nodeId) => {
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

    TableActions.deleteNode(schema, nodeId)
    isSaved.value = false

    const updates = affectedSiblingIds
        .map(id => schema.value.find(el => el.id === id))
        .filter(Boolean)
        .map(el => ({ id: el.id, position: el.position }))
    whisper('schema-patch', { remove: [nodeId, ...childIds], update: updates })
}

const onConnect = (params) => {
    params.updatable = true
    const parentNode = findNode(findNode(params.target)?.parentNode)
    const tableColor = parentNode?.data?.color
    if (tableColor) {
        params.style = { stroke: tableColor }
        params.data = { ...params.data, color: tableColor }
    }
    addEdges([params])
    isSaved.value = false
    nextTick(() => {
        const newEdge = schema.value.find(el =>
            el.source === params.source && el.target === params.target &&
            el.sourceHandle === params.sourceHandle && el.targetHandle === params.targetHandle
        )
        if (newEdge) whisper('schema-patch', { add: [newEdge] })
    })
}

const onEdgeUpdate = ({ edge, connection }) => {
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
    if (relationshipType === 'many-to-many') {
        const result = TableActions.createPivotTable(schema, selectedEdge.value)
        showRelationshipModal.value = false
        isSaved.value = false
        if (result) {
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
    const edge = schema.value.find(el => el.id === selectedEdge.value.id)
    if (edge) whisper('schema-patch', { update: [{ id: edge.id, data: { ...edge.data } }] })
}

const onRowChange = (id) => {
    isSaved.value = false
    const node = schema.value.find(el => el.id === id)
    if (node) whisper('schema-patch', {
        update: [{ id, data: { sqlType: node.data.sqlType, keyMod: node.data.keyMod, nullable: node.data.nullable, unsigned: node.data.unsigned } }]
    })
}

const updateLabel = (id, newLabel) => {
    const element = schema.value.find(el => el.id === id)
    if (element) {
        element.label = newLabel.replace(' ', '_')
        isSaved.value = false
        whisper('schema-patch', { update: [{ id, label: element.label }] })
    }
}

const updateEdgeColor = (color) => {
    const edge = schema.value.find(el => el.id === selectedEdge.value?.id)
    if (!edge) return
    edge.style = { ...edge.style, stroke: color }
    edge.data = { ...edge.data, color }
    isSaved.value = false
    whisper('schema-patch', { update: [{ id: edge.id, style: { ...edge.style }, data: { color } }] })
}

const updateTableColor = (tableId, color) => {
    const tableNode = schema.value.find(el => el.id === tableId)
    if (!tableNode) return
    tableNode.style = { ...tableNode.style, background: color, borderColor: color }
    tableNode.data = { ...tableNode.data, color }
    isSaved.value = false
    whisper('schema-patch', { update: [{ id: tableId, style: { ...tableNode.style }, data: { color } }] })
}

// --- Row drag ---

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
    whisper('schema-patch', {
        update: [{ id, data: { showOptionsModal: row.data.showOptionsModal, modalPosition: row.data.modalPosition } }]
    })
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

// --- Import / Export ---

const importSql = async () => {
    if (!importContent.value.trim()) {
        $toast.error('Cannot import empty SQL')
        return
    }
    importLoading.value = true
    schema.value = await Diagram.import(diagramId.value, importContent.value)
    importLoading.value = false
    if (schema.value) {
        $toast.success('Imported successfully')
        isSaved.value = false
    }
}

const openExportModal = async () => {
    await saveDiagram()
    const [sql, json] = await Promise.all([Diagram.export(diagramId.value), Diagram.exportJson(diagramId.value)])
    exportContent.value = sql
    exportJsonContent.value = json
    showExportModal.value = true
}

const exportSql = async () => {
    await Diagram.save(diagramId.value, schema.value)
    const [sql, json] = await Promise.all([Diagram.export(diagramId.value), Diagram.exportJson(diagramId.value)])
    exportContent.value = sql
    exportJsonContent.value = json
}

// --- Save ---

const saveDiagram = async () => {
    if (props.isDemo) {
        router.push({ name: 'register' })
        return
    }
    await (isOwner.value ? Diagram.save(diagramId.value, schema.value) : Diagram.saveByToken(token, schema.value))
    isSaved.value = true
    whisper('diagram-saved', {})
}

// --- Load ---

const getDiagram = async () => {
    if (props.isDemo) {
        schema.value = DEMO_SCHEMA
        return
    }

    loading.value = true

    try {
        const { data: user } = await axios.get('/api/user')
        ownerIdentity.value = {
            id: String(user.id),
            name: user.email,
            color: CURSOR_COLORS[user.id % CURSOR_COLORS.length],
        }
    } catch {
        loading.value = false
        router.push({ name: 'login' })
        return
    }

    const diagramInfo = await Diagram.getByToken(token)

    if (!diagramInfo) {
        loading.value = false
        notAvailable.value = true
        return
    }

    diagramId.value = diagramInfo.id
    isOwner.value = diagramInfo.is_owner ?? false
    diagramShareAccess.value = diagramInfo.share_access ?? null
    diagramDbType.value = diagramInfo.db_type ?? 'mysql'
    diagramName.value = diagramInfo.name ?? ''

    schema.value = diagramInfo.schema ? JSON.parse(diagramInfo.schema) : [{
        id: '1',
        type: 'table',
        label: 'users',
        data: { toolbarPosition: Position.Top, toolbarVisible: true },
        position: { x: 0, y: -100 },
        style: TABLE_STYLE,
    }]

    isSaved.value = true
    loading.value = false

    initEcho()
}

onBeforeMount(getDiagram)

onMounted(() => {
    if (!props.isDemo) {
        autoSaveTimer = setInterval(() => {
            if (!isSaved.value && canEdit.value) saveDiagram()
        }, 60000)
    }
    document.addEventListener('keydown', onEscapeKey)
})

onUnmounted(() => {
    clearInterval(autoSaveTimer)
    if (!isSaved.value && canEdit.value && !props.isDemo) saveDiagram()
    cleanupEcho()
    document.removeEventListener('keydown', onEscapeKey)
})
</script>

<style scoped>
.is-placing-table,
.is-placing-table * {
    cursor: crosshair !important;
}

.diagram-status-screen {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.diagram-status-screen__text {
    font-size: 0.9rem;
    color: #888;
    letter-spacing: 0.5px;
}

.diagram-name-label {
    font-size: 0.85rem;
    color: white;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.diagram-canvas-wrapper {
    flex: 1;
    min-height: 0;
    position: relative;
    overflow: hidden;
}

.diagram-canvas {
    width: 100%;
    height: 100%;
}

.cursor-layer {
    position: absolute;
    inset: 0;
    pointer-events: none;
    z-index: 100;
    overflow: hidden;
}

.cursor-edge-indicator {
    position: absolute;
    top: 0;
    left: 0;
    display: flex;
    align-items: center;
    gap: 3px;
    pointer-events: none;
}

.cursor-edge-indicator__arrow {
    flex-shrink: 0;
    filter: drop-shadow(0 1px 3px rgba(0, 0, 0, 0.35));
}

</style>
