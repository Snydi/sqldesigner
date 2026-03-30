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
        <header class="header header--diagram">
            <div class="flex-items">
                <button v-if="canEdit" class="btn btn-secondary" @click="addTable" title="Add Table">
                    <img src="../icons/plus.svg" alt="Add Table" class="icon" style="width:26px;height:26px;">
                </button>
                <button v-if="isOwner || isDemo" class="btn btn-secondary" @click="isDemo ? router.push({ name: 'register' }) : showImportModal = true" title="Import">
                    <img src="../icons/import.svg" alt="Import" class="icon">
                </button>
                <button v-if="isOwner || isDemo" class="btn btn-secondary" @click="isDemo ? router.push({ name: 'register' }) : openExportModal()" title="Export">
                    <img src="../icons/export.svg" alt="Export" class="icon">
                </button>
                <span v-if="!isOwner && !isDemo" class="diagram-name-label">{{ diagramName }}</span>
            </div>
            <div class="flex-items">
                <div v-if="canEdit" class="save-button-wrapper">
                    <button class="btn btn-secondary" @click="saveDiagram" title="Save" :disabled="!isDemo && isSaved">
                        <img src="../icons/save.svg" alt="Save" class="icon">
                    </button>
                    <div v-if="!isDemo"
                        :title="isSaved ? 'All changes saved' : 'Unsaved changes'"
                    ></div>
                </div>
                <button v-if="isOwner" class="btn btn-secondary" @click="showShareModal = true" title="Share">
                    <img src="../icons/share.svg" alt="Share" class="icon">
                </button>
            </div>
        </header>

        <!-- Share Modal -->
        <div v-if="showShareModal" class="share-modal-overlay" @click.self="showShareModal = false">
            <div class="share-modal">
                <div class="share-modal__header">
                    <span class="share-modal__title">Share Diagram</span>
                    <button class="share-modal__close" @click="showShareModal = false">
                        <img src="../icons/close.svg" alt="Close" style="width:14px;height:14px;" />
                    </button>
                </div>

                <div class="share-modal__body">
                    <div class="share-modal__toggle-row">
                        <span class="share-modal__toggle-label">{{ diagramShareAccess ? 'Sharing enabled' : 'Sharing disabled' }}</span>
                        <button
                            class="share-toggle"
                            :class="{ 'share-toggle--on': diagramShareAccess }"
                            @click="toggleShare"
                            :disabled="shareLoading"
                        >
                            <span class="share-toggle__knob"></span>
                        </button>
                    </div>

                    <div v-if="diagramShareAccess" class="share-modal__access-row">
                        <span class="share-modal__toggle-label">Access</span>
                        <div class="share-modal__access-options">
                            <button
                                class="share-modal__access-btn"
                                :class="{ 'share-modal__access-btn--active': diagramShareAccess === 'read' }"
                                @click="setShareAccess('read')"
                                :disabled="shareLoading"
                            >Read-only</button>
                            <button
                                class="share-modal__access-btn"
                                :class="{ 'share-modal__access-btn--active': diagramShareAccess === 'write' }"
                                @click="setShareAccess('write')"
                                :disabled="shareLoading"
                            >Can edit</button>
                        </div>
                    </div>

                    <div v-if="diagramShareAccess" class="share-modal__link-row">
                        <input
                            class="share-modal__link-input"
                            :value="shareUrl"
                            readonly
                            ref="shareLinkInput"
                        />
                        <button class="btn btn-primary share-modal__copy-btn" @click="copyShareLink">
                            {{ copied ? 'Copied!' : 'Copy' }}
                        </button>
                    </div>

                    <p v-if="diagramShareAccess" class="share-modal__hint">
                        Anyone with this link can {{ diagramShareAccess === 'write' ? 'edit' : 'view' }} this diagram.
                    </p>
                </div>
            </div>
        </div>

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
            </div>
            <VueFlow
                :default-edge-options="{ type: 'chickenFoot' }"
                @edge-update="onEdgeUpdate"
                @edge-click="openRelationshipModal"
                @connect="onConnect"
                @node-drag-stop="onNodeDragStop"
                @pane-click="onPaneClick"
                v-model="schema"
                fit-view-on-init
                :zoomOnDoubleClick="false"
                :controlled="false"
                :class="['diagram-canvas', { 'is-placing-table': isPlacingTable }]"
            >
                <template #edge-chickenFoot="props">
                    <ChickenFootEdge v-bind="props" />
                </template>

                <Background :variant="BackgroundVariant.Lines" />

                <template #node-table="nodeProps">
                    <TableNode
                        :id="nodeProps.id"
                        :data="nodeProps.data"
                        :label="nodeProps.label"
                        @delete-node="deleteNode"
                        @update-label="updateLabel"
                        @copy-table="copyTable"
                        @add-row="addRow({ id: $event, data: {} })"
                        @resize-start="startTableResize"
                    />
                </template>

                <template #node-row="nodeProps">
                    <RowNode
                        :id="nodeProps.id"
                        :data="nodeProps.data"
                        :label="nodeProps.label"
                        :dbType="diagramDbType"
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
            @update-type="updateConnectionLineType"
            @delete="deleteEdge"
            @close="closeRelationshipModal"
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
    </template>
</template>

<script setup>
import { computed, onBeforeMount, onMounted, onUnmounted, reactive, ref, watch, nextTick } from 'vue'
import { Position, useVueFlow, VueFlow } from '@vue-flow/core'
import { Background, BackgroundVariant } from '@vue-flow/background'
import { useThrottleFn } from '@vueuse/core'
import { TableActions, TABLE_STYLE, ROW_STYLE } from '@/services/TableActions.js'
import { Diagram } from '@/services/Diagram.js'
import { createEcho } from '@/echo.js'
import ChickenFootEdge from './ChickenFootEdge.vue'
import TableNode from './TableNode.vue'
import RowNode from './RowNode.vue'
import AddRowNode from './AddRowNode.vue'
import RelationshipModal from './RelationshipModal.vue'
import SqlModal from './SqlModal.vue'
import RemoteCursor from './RemoteCursor.vue'
import { useToast } from 'vue-toast-notification'
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex'
import axios from '@/axios'
import '@vue-flow/core/dist/style.css'
import '@vue-flow/core/dist/theme-default.css'
import '@/css/diagram.css'
import '@/css/header.css'

const props = defineProps({ isDemo: { type: Boolean, default: false } })

const { updateEdge, addEdges, viewport, screenToFlowCoordinate } = useVueFlow()
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

const DEMO_SCHEMA = [
    { id: 'dt1', type: 'table', label: 'users', data: { toolbarPosition: Position.Top, toolbarVisible: true }, position: { x: 0, y: 0 }, style: TABLE_STYLE },
    { id: 'dr1', type: 'row', label: 'id', position: { x: 0, y: 40 }, style: ROW_STYLE, draggable: false, parentNode: 'dt1', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'PRIMARY KEY', sqlType: 'INT(11)', nullable: false, unsigned: false } },
    { id: 'dr2', type: 'row', label: 'username', position: { x: 0, y: 80 }, style: ROW_STYLE, draggable: false, parentNode: 'dt1', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'None', sqlType: 'VARCHAR(255)', nullable: false, unsigned: false } },
    { id: 'dr3', type: 'row', label: 'email', position: { x: 0, y: 120 }, style: ROW_STYLE, draggable: false, parentNode: 'dt1', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'None', sqlType: 'VARCHAR(255)', nullable: false, unsigned: false } },
    { id: 'dr4', type: 'row', label: 'created_at', position: { x: 0, y: 160 }, style: ROW_STYLE, draggable: false, parentNode: 'dt1', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'None', sqlType: 'TIMESTAMP', nullable: false, unsigned: false } },

    { id: 'dt2', type: 'table', label: 'posts', data: { toolbarPosition: Position.Top, toolbarVisible: true }, position: { x: 450, y: 0 }, style: TABLE_STYLE },
    { id: 'dr5', type: 'row', label: 'id', position: { x: 0, y: 40 }, style: ROW_STYLE, draggable: false, parentNode: 'dt2', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'PRIMARY KEY', sqlType: 'INT(11)', nullable: false, unsigned: false } },
    { id: 'dr6', type: 'row', label: 'user_id', position: { x: 0, y: 80 }, style: ROW_STYLE, draggable: false, parentNode: 'dt2', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'FOREIGN KEY', sqlType: 'INT(11)', nullable: false, unsigned: false } },
    { id: 'dr7', type: 'row', label: 'title', position: { x: 0, y: 120 }, style: ROW_STYLE, draggable: false, parentNode: 'dt2', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'None', sqlType: 'VARCHAR(255)', nullable: false, unsigned: false } },
    { id: 'dr8', type: 'row', label: 'body', position: { x: 0, y: 160 }, style: ROW_STYLE, draggable: false, parentNode: 'dt2', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'None', sqlType: 'TEXT', nullable: false, unsigned: false } },
    { id: 'dr9', type: 'row', label: 'created_at', position: { x: 0, y: 200 }, style: ROW_STYLE, draggable: false, parentNode: 'dt2', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'None', sqlType: 'TIMESTAMP', nullable: false, unsigned: false } },

    { id: 'dt3', type: 'table', label: 'comments', data: { toolbarPosition: Position.Top, toolbarVisible: true }, position: { x: 225, y: 380 }, style: TABLE_STYLE },
    { id: 'dr10', type: 'row', label: 'id', position: { x: 0, y: 40 }, style: ROW_STYLE, draggable: false, parentNode: 'dt3', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'PRIMARY KEY', sqlType: 'INT(11)', nullable: false, unsigned: false } },
    { id: 'dr11', type: 'row', label: 'post_id', position: { x: 0, y: 80 }, style: ROW_STYLE, draggable: false, parentNode: 'dt3', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'FOREIGN KEY', sqlType: 'INT(11)', nullable: false, unsigned: false } },
    { id: 'dr12', type: 'row', label: 'user_id', position: { x: 0, y: 120 }, style: ROW_STYLE, draggable: false, parentNode: 'dt3', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'FOREIGN KEY', sqlType: 'INT(11)', nullable: false, unsigned: false } },
    { id: 'dr13', type: 'row', label: 'body', position: { x: 0, y: 160 }, style: ROW_STYLE, draggable: false, parentNode: 'dt3', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'None', sqlType: 'TEXT', nullable: false, unsigned: false } },
    { id: 'dr14', type: 'row', label: 'created_at', position: { x: 0, y: 200 }, style: ROW_STYLE, draggable: false, parentNode: 'dt3', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'None', sqlType: 'TIMESTAMP', nullable: false, unsigned: false } },

    { id: 'de1', source: 'dr1', target: 'dr6', type: 'chickenFoot', updatable: true, data: { relationshipType: 'one-to-many', markerStart: 'url(#chickenFoot)', markerEnd: 'none' } },
    { id: 'de2', source: 'dr1', target: 'dr12', type: 'chickenFoot', updatable: true, data: { relationshipType: 'one-to-many', markerStart: 'url(#chickenFoot)', markerEnd: 'none' } },
    { id: 'de3', source: 'dr5', target: 'dr11', type: 'chickenFoot', updatable: true, data: { relationshipType: 'one-to-many', markerStart: 'url(#chickenFoot)', markerEnd: 'none' } },
]

const isSaved = ref(true)
let autoSaveTimer = null

const remoteCursors = reactive({})
const canvasWrapperRef = ref(null)
const ownerIdentity = ref(null)
let echo = null
let presenceChannel = null

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
const shareLoading = ref(false)
const copied = ref(false)
const shareLinkInput = ref(null)

const shareUrl = computed(() => `${window.location.origin}/diagrams/${token}`)

const CURSOR_COLORS = ['#E53935', '#D81B60', '#8E24AA', '#3949AB', '#1E88E5', '#00ACC1', '#43A047', '#FB8C00']

const broadcastCursor = useThrottleFn((event) => {
    if (!presenceChannel || !ownerIdentity.value) return
    const rect = canvasWrapperRef.value?.getBoundingClientRect()
    if (!rect) return
    const vp = viewport.value
    const x = (event.clientX - rect.left - vp.x) / vp.zoom
    const y = (event.clientY - rect.top - vp.y) / vp.zoom
    presenceChannel.whisper('cursor-moved', { id: ownerIdentity.value.id, x, y })
}, 40)

const onCanvasMouseMove = (event) => broadcastCursor(event)

const initEcho = () => {
    if (!ownerIdentity.value) return
    echo = createEcho()
    presenceChannel = echo.join(`diagram.${token}`)
        .here((users) => {
            for (const u of users) {
                if (u.id !== ownerIdentity.value.id) {
                    remoteCursors[u.id] = { ...u, screenX: -999, screenY: -999 }
                }
            }
        })
        .joining((user) => {
            if (user.id !== ownerIdentity.value.id) {
                remoteCursors[user.id] = { ...user, screenX: -999, screenY: -999 }
            }
        })
        .leaving((user) => {
            delete remoteCursors[user.id]
        })
        .listenForWhisper('cursor-moved', ({ id, x, y }) => {
            if (!id || id === ownerIdentity.value?.id || !remoteCursors[id]) return
            remoteCursors[id].flowX = x
            remoteCursors[id].flowY = y
            remoteCursors[id].screenX = x * viewport.value.zoom + viewport.value.x
            remoteCursors[id].screenY = y * viewport.value.zoom + viewport.value.y
        })
        .listenForWhisper('schema-patch', ({ add, remove, update }) => {
            if (remove?.length) {
                schema.value = schema.value.filter(el => !remove.includes(el.id))
            }
            if (add?.length) {
                schema.value = [...schema.value, ...add]
            }
            if (update?.length) {
                for (const change of update) {
                    const el = schema.value.find(el => el.id === change.id)
                    if (!el) continue
                    const { data, ...rest } = change
                    Object.assign(el, rest)
                    if (data) Object.assign(el.data, data)
                }
            }
        })
        .listenForWhisper('modal-update', ({ type, edgeId, open }) => {
            if (type === 'relationship') {
                if (open && edgeId) {
                    const edge = schema.value.find(el => el.id === edgeId)
                    if (!edge) return
                    selectedEdge.value = edge
                    nextTick(() => {
                        const el = document.querySelector(`[id="${edgeId}"]`)
                        if (el) {
                            const { left, top, width, height } = el.getBoundingClientRect()
                            modalPosition.value = {
                                x: left + window.scrollX + width / 2,
                                y: top + window.scrollY + height / 2
                            }
                        }
                        showRelationshipModal.value = true
                    })
                } else {
                    showRelationshipModal.value = false
                    selectedEdge.value = null
                }
            }
        })
}

watch(viewport, (vp) => {
    for (const id in remoteCursors) {
        const c = remoteCursors[id]
        if (c.flowX !== undefined) {
            c.screenX = c.flowX * vp.zoom + vp.x
            c.screenY = c.flowY * vp.zoom + vp.y
        }
    }
}, { deep: true })

const cleanupEcho = () => {
    if (echo) {
        echo.leave(`diagram.${token}`)
        echo.disconnect()
    }
    echo = null
    presenceChannel = null
    Object.keys(remoteCursors).forEach(k => delete remoteCursors[k])
}

const onNodeDragStop = ({ node }) => {
    isSaved.value = false
    presenceChannel?.whisper('schema-patch', {
        update: [{ id: node.id, position: node.position }]
    })
}

const MIN_TABLE_WIDTH = 350

const startTableResize = (tableId, event, side) => {
    const tableNode = schema.value.find(el => el.id === tableId)
    if (!tableNode) return

    const startX = event.clientX
    const startWidth = parseInt(tableNode.style.width) || MIN_TABLE_WIDTH
    const startPositionX = tableNode.position.x

    const onMouseMove = (e) => {
        const deltaX = (e.clientX - startX) / viewport.value.zoom
        let newWidth, newPositionX

        if (side === 'left') {
            newWidth = Math.max(MIN_TABLE_WIDTH, startWidth - deltaX)
            const appliedDelta = startWidth - newWidth
            newPositionX = startPositionX + appliedDelta
        } else {
            newWidth = Math.max(MIN_TABLE_WIDTH, startWidth + deltaX)
            newPositionX = startPositionX
        }

        const widthPx = `${newWidth}px`
        schema.value.forEach(node => {
            if (node.id === tableId) {
                node.style = { ...node.style, width: widthPx }
                node.position = { ...node.position, x: newPositionX }
            } else if (node.parentNode === tableId) {
                node.style = { ...node.style, width: widthPx }
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
    if (presenceChannel) {
        const nodes = schema.value.filter(el => el.id === tableId || el.parentNode === tableId)
        presenceChannel.whisper('schema-patch', { add: nodes })
    }
}

const onEscapeKey = (event) => {
    if (event.key === 'Escape') {
        isPlacingTable.value = false
        copyingTableId.value = null
    }
}

const addRow = (nodeProps) => {
    const rowId = TableActions.addRow(schema, nodeProps, {
        rowName: 'new_row',
        keyMod: 'None',
        sqlType: diagramDbType.value === 'postgresql' ? 'INTEGER' : 'INT(11)',
        nullable: false,
        unsigned: false
    })
    isSaved.value = false
    if (presenceChannel) {
        const newRow = schema.value.find(el => el.id === rowId)
        const button = schema.value.find(el => el.type === 'add-row-button' && el.parentNode === nodeProps.id)
        const patch = { add: [newRow] }
        if (button) patch.update = [{ id: button.id, position: button.position }]
        presenceChannel.whisper('schema-patch', patch)
    }
}

const deleteEdge = () => {
    const edgeId = selectedEdge.value.id
    TableActions.deleteEdge(schema, selectedEdge)
    showRelationshipModal.value = false
    isSaved.value = false
    presenceChannel?.whisper('schema-patch', { remove: [edgeId] })
    presenceChannel?.whisper('modal-update', { type: 'relationship', open: false })
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

    if (presenceChannel) {
        const updates = affectedSiblingIds
            .map(id => schema.value.find(el => el.id === id))
            .filter(Boolean)
            .map(el => ({ id: el.id, position: el.position }))
        presenceChannel.whisper('schema-patch', { remove: [nodeId, ...childIds], update: updates })
    }
}

const onConnect = (params) => {
    params.updatable = true
    addEdges([params])
    isSaved.value = false
    if (presenceChannel) {
        nextTick(() => {
            const newEdge = schema.value.find(el =>
                el.source === params.source && el.target === params.target &&
                el.sourceHandle === params.sourceHandle && el.targetHandle === params.targetHandle
            )
            if (newEdge) presenceChannel.whisper('schema-patch', { add: [newEdge] })
        })
    }
}

const onEdgeUpdate = ({ edge, connection }) => {
    const oldEdgeId = edge.id
    updateEdge(edge, connection)
    isSaved.value = false
    if (presenceChannel) {
        nextTick(() => {
            const newEdge = schema.value.find(el =>
                el.source === connection.source && el.target === connection.target &&
                el.sourceHandle === connection.sourceHandle && el.targetHandle === connection.targetHandle
            )
            presenceChannel.whisper('schema-patch', { remove: [oldEdgeId], add: newEdge ? [newEdge] : [] })
        })
    }
}

const updateConnectionLineType = (relationshipType) => {
    TableActions.updateConnectionLineType(schema, selectedEdge, relationshipType)
    showRelationshipModal.value = false
    isSaved.value = false
    if (presenceChannel) {
        const edge = schema.value.find(el => el.id === selectedEdge.value.id)
        if (edge) presenceChannel.whisper('schema-patch', { update: [{ id: edge.id, data: { ...edge.data } }] })
        presenceChannel.whisper('modal-update', { type: 'relationship', open: false })
    }
}

const onRowChange = (id) => {
    isSaved.value = false
    if (presenceChannel) {
        const node = schema.value.find(el => el.id === id)
        if (node) presenceChannel.whisper('schema-patch', {
            update: [{ id, data: { sqlType: node.data.sqlType, keyMod: node.data.keyMod, nullable: node.data.nullable, unsigned: node.data.unsigned } }]
        })
    }
}

const updateLabel = (id, newLabel) => {
    const element = schema.value.find(el => el.id === id)
    if (element) {
        element.label = newLabel.replace(' ', '_')
        isSaved.value = false
        presenceChannel?.whisper('schema-patch', { update: [{ id, label: element.label }] })
    }
}

const draggingRowId = ref(null)

const startRowDrag = (id) => {
    draggingRowId.value = id

    const onMouseMove = (e) => {
        const rowNodeEl = document.elementsFromPoint(e.clientX, e.clientY)
            .find(el => el.classList.contains('vue-flow__node-row') && el.getAttribute('data-id') !== draggingRowId.value)

        if (!rowNodeEl) return
        const targetId = rowNodeEl.getAttribute('data-id')

        const sourceNode = schema.value.find(el => el.id === draggingRowId.value)
        const targetNode = schema.value.find(el => el.id === targetId)

        if (!sourceNode || !targetNode || sourceNode.type !== 'row' || targetNode.type !== 'row' || sourceNode.parentNode !== targetNode.parentNode) return

        const tempY = sourceNode.position.y
        sourceNode.position.y = targetNode.position.y
        targetNode.position.y = tempY

        isSaved.value = false
        presenceChannel?.whisper('schema-patch', {
            update: [
                { id: sourceNode.id, position: { ...sourceNode.position } },
                { id: targetNode.id, position: { ...targetNode.position } },
            ]
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
    presenceChannel?.whisper('schema-patch', {
        update: [{ id, data: { showOptionsModal: row.data.showOptionsModal, modalPosition: row.data.modalPosition } }]
    })
}

const openRelationshipModal = ({ edge }) => {
    selectedEdge.value = edge
    const { left, top, width, height } = document.querySelector(`[id="${edge.id}"]`).getBoundingClientRect()
    modalPosition.value = {
        x: left + window.scrollX + width / 2,
        y: top + window.scrollY + height / 2
    }
    showRelationshipModal.value = true
    presenceChannel?.whisper('modal-update', { type: 'relationship', edgeId: edge.id, open: true })
}

const closeRelationshipModal = () => {
    showRelationshipModal.value = false
    presenceChannel?.whisper('modal-update', { type: 'relationship', open: false })
}

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
    const [sql, json] = await Promise.all([
        Diagram.export(diagramId.value),
        Diagram.exportJson(diagramId.value),
    ])
    exportContent.value = sql
    exportJsonContent.value = json
    showExportModal.value = true
}

const exportSql = async () => {
    await Diagram.save(diagramId.value, schema.value)
    const [sql, json] = await Promise.all([
        Diagram.export(diagramId.value),
        Diagram.exportJson(diagramId.value),
    ])
    exportContent.value = sql
    exportJsonContent.value = json
}

const saveDiagram = async () => {
    if (props.isDemo) {
        router.push({ name: 'register' })
        return
    }
    if (isOwner.value) {
        await Diagram.save(diagramId.value, schema.value)
    } else {
        await Diagram.saveByToken(token, schema.value)
    }
    isSaved.value = true
}

const toggleShare = async () => {
    shareLoading.value = true
    if (diagramShareAccess.value) {
        await Diagram.unshare(diagramId.value)
        diagramShareAccess.value = null
        copied.value = false
    } else {
        diagramShareAccess.value = await Diagram.share(diagramId.value) ?? 'read'
    }
    shareLoading.value = false
}

const copyShareLink = async () => {
    await navigator.clipboard.writeText(shareUrl.value)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
}

const setShareAccess = async (access) => {
    shareLoading.value = true
    diagramShareAccess.value = await Diagram.updateShareAccess(diagramId.value, access) ?? access
    shareLoading.value = false
}

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

    const rawSchema = diagramInfo.schema ? JSON.parse(diagramInfo.schema) : [{
        id: '1',
        type: 'table',
        label: 'users',
        data: { toolbarPosition: Position.Top, toolbarVisible: true },
        position: { x: 0, y: -100 },
        style: TABLE_STYLE
    }]

    schema.value = rawSchema
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

.share-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 200;
}

.share-modal {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    width: 380px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
}

.share-modal__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
}

.share-modal__title {
    color: var(--color-primary);
    font-size: 0.85rem;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.share-modal__close {
    background: none;
    border: none;
    cursor: pointer;
    padding: 2px;
    display: flex;
    align-items: center;
    opacity: 0.5;
    transition: opacity 0.15s;
}

.share-modal__close:hover {
    opacity: 1;
}

.share-modal__body {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.share-modal__toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.share-modal__toggle-label {
    font-size: 0.8rem;
    color: #555;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.share-toggle {
    width: 44px;
    height: 24px;
    background: #ddd;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    position: relative;
    transition: background 0.2s;
    padding: 0;
    flex-shrink: 0;
}

.share-toggle--on {
    background: var(--color-primary);
}

.share-toggle:disabled {
    opacity: 0.5;
    cursor: default;
}

.share-toggle__knob {
    position: absolute;
    top: 3px;
    left: 3px;
    width: 18px;
    height: 18px;
    background: white;
    border-radius: 50%;
    transition: transform 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.share-toggle--on .share-toggle__knob {
    transform: translateX(20px);
}

.share-modal__link-row {
    display: flex;
    gap: 0.5rem;
}

.share-modal__link-input {
    flex: 1;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 0.4rem 0.6rem;
    font-size: 0.75rem;
    font-family: inherit;
    color: #555;
    background: #f9f9f9;
    outline: none;
    min-width: 0;
    text-transform: none;
}

.share-modal__copy-btn {
    font-size: 0.75rem;
    padding: 0.4rem 0.75rem;
    flex-shrink: 0;
    font-family: inherit;
    letter-spacing: 0.5px;
}

.share-modal__hint {
    margin: 0;
    font-size: 0.72rem;
    color: #999;
    text-transform: none;
    letter-spacing: 0;
    line-height: 1.4;
    text-align: left;
}

.share-modal__access-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.share-modal__access-options {
    display: flex;
    gap: 0.4rem;
}

.share-modal__access-btn {
    padding: 0.3rem 0.75rem;
    font-size: 0.75rem;
    font-family: inherit;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
    color: #666;
    cursor: pointer;
    transition: border-color 0.15s, background 0.15s, color 0.15s;
}

.share-modal__access-btn:hover:not(:disabled) {
    border-color: #bbb;
    background: #f5f5f5;
}

.share-modal__access-btn--active {
    border-color: var(--color-primary) !important;
    background: var(--color-primary) !important;
    color: white !important;
}

.share-modal__access-btn:disabled {
    opacity: 0.5;
    cursor: default;
}
</style>
