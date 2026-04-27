<template>
    <!-- Loading state -->
    <div v-if="loading" class="diagram-status-screen">
        <span class="diagram-status-screen__text">Loading…</span>
    </div>

    <!-- Not available state -->
    <div v-else-if="notAvailable" class="diagram-status-screen">
        <div class="diagram-status-screen__icon-wrap">
            <span class="diagram-status-screen__logo-mark">S</span>
        </div>
        <span class="diagram-status-screen__text">This diagram is not available.</span>
        <div style="display:flex;gap:0.5rem;margin-top:1.25rem">
            <button class="btn btn-secondary" @click="router.push({ name: 'diagrams' })">My Diagrams</button>
        </div>
    </div>

    <!-- Pending approval state -->
    <div v-else-if="pendingApproval" class="diagram-status-screen">
        <div class="diagram-status-screen__icon-wrap">
            <span class="diagram-status-screen__logo-mark">S</span>
        </div>
        <span class="diagram-status-screen__text">Access requires approval.</span>
        <span class="diagram-status-screen__subtext">Your request has been sent to the diagram owner. Check back once they've approved you.</span>
        <div style="display:flex;gap:0.5rem;margin-top:1.25rem">
            <button class="btn btn-primary" @click="retryAccess" :disabled="loading">{{ loading ? 'Checking…' : 'Retry' }}</button>
            <button class="btn btn-secondary" @click="router.push({ name: 'diagrams' })">My Diagrams</button>
        </div>
    </div>

    <template v-else>
        <DiagramHeader
            :canEdit="canEdit"
            :isOwner="isOwner"
            :isDemo="isDemo"
            :isSaved="isSaved"
            :diagramName="diagramName"
            :hasPendingVisitors="hasPendingVisitors"
            @add-table="addTable"
            @import="isDemo ? router.push({ name: 'register' }) : showImportModal = true"
            @export="isDemo ? router.push({ name: 'register' }) : openExportModal()"
            @save="saveDiagram"
            @show-share="showShareModal = true"
            @show-changelog="showChangelogModal = true"
        />

        <ShareModal
            v-if="showShareModal"
            :diagramId="diagramId"
            :token="token"
            v-model:shareAccess="diagramShareAccess"
            v-model:requireApproval="diagramRequireApproval"
            v-model:inLibrary="diagramInLibrary"
            v-model:hasPendingVisitors="hasPendingVisitors"
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
                @connect-start="isConnecting = true"
                @connect-end="isConnecting = false"
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
                :class="['diagram-canvas', { 'is-placing-table': isPlacingTable, 'is-connecting': isConnecting }]"
            >
                <Panel position="top-left" class="table-navigator">
                    <button class="table-navigator__toggle" @click.stop="tableNavOpen = !tableNavOpen" title="Tables">
                        <SvgIcon name="table-list" :size="18" />
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
                        <SvgIcon name="chat" :size="16" />
                    </button>
                </Panel>

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
                        :tableColumns="schema.filter(el => el.type === 'row' && el.parentNode === nodeProps.parentNodeId).sort((a, b) => a.position.y - b.position.y).map(el => el.label)"
                        :tableUniqueTogether="schema.find(el => el.id === nodeProps.parentNodeId)?.data?.uniqueTogether ?? []"
                        :tableFulltextIndexes="schema.find(el => el.id === nodeProps.parentNodeId)?.data?.fulltextIndexes ?? []"
                        @update-label="updateLabel"
                        @toggle-options-modal="toggleOptionsModal"
                        @delete-node="deleteNode"
                        @change="onRowChange($event)"
                        @row-drag-start="startRowDrag"
                        @update-table-constraints="onTableConstraintsChange(nodeProps.parentNodeId, $event)"
                        @update-table-fulltext="onTableFulltextChange(nodeProps.parentNodeId, $event)"
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

        <ExportModal
            v-if="showExportModal"
            :sqlContent="exportContent"
            :filename="diagramName"
            :jsonContent="exportJsonContent"
            :diagramId="diagramId"
            @close="showExportModal = false"
        />

        <FeedbackModal
            v-if="showFeedbackModal"
            :user-email="feedbackUserEmail"
            @close="showFeedbackModal = false"
        />

        <ChangelogModal
            v-if="showChangelogModal"
            :diagramId="diagramId"
            @close="showChangelogModal = false"
        />

    </template>
</template>

<script setup>
import { computed, onBeforeMount, onMounted, onUnmounted, ref, nextTick } from 'vue'
import { Panel, Position, useVueFlow, VueFlow } from '@vue-flow/core'
import { TABLE_STYLE } from '@/services/TableActions.js'
import { Diagram } from '@/services/Diagram.js'
import { DEMO_SCHEMA } from '@/services/demoSchema.js'
import { useDiagramPresence, CURSOR_COLORS } from '@/composables/useDiagramPresence.js'
import { useDiagramPolling } from '@/composables/useDiagramPolling.js'
import { useOffScreenCursors } from '@/composables/useOffScreenCursors.js'
import { useTableInteraction } from '@/composables/useTableInteraction.js'
import { useTableResize } from '@/composables/useTableResize.js'
import { useRowDrag } from '@/composables/useRowDrag.js'
import { useSchemaActions } from '@/composables/useSchemaActions.js'
import { useUndoHistory } from '@/composables/useUndoHistory.js'
import SvgIcon from '../SvgIcon.vue'
import DiagramHeader from './DiagramHeader.vue'
import ShareModal from '../Modal/ShareModal.vue'
import ChickenFootEdge from '../ChickenFootEdge.vue'
import TableNode from './TableNode.vue'
import RowNode from '../RowNode.vue'
import RelationshipModal from '../Modal/RelationshipModal.vue'
import SqlModal from '../Modal/SqlModal.vue'
import ExportModal from '../Modal/ExportModal.vue'
import RemoteCursor from '../RemoteCursor.vue'
import FeedbackModal from '../Modal/FeedbackModal.vue'
import ChangelogModal from '../Modal/ChangelogModal.vue'
import { useElementSize } from '@vueuse/core'
import { useToast } from 'vue-toast-notification'
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex'
import axios from '@/axios.js'
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
const pendingApproval = ref(false)
const loading = ref(false)
const isSaved = ref(true)
const schema = ref()
const diagramName = ref('schema')
const diagramDbType = ref('mysql')
const showShareModal = ref(false)
const showChangelogModal = ref(false)
const diagramShareAccess = ref(null)
const diagramRequireApproval = ref(false)
const diagramInLibrary = ref(false)
const ownerIdentity = ref(null)
const canvasWrapperRef = ref(null)

const canEdit = computed(() => props.isDemo || isOwner.value || diagramShareAccess.value === 'write')

const { width: canvasWidth, height: canvasHeight } = useElementSize(canvasWrapperRef)

const { snapshot, undo, redo } = useUndoHistory(schema)

const { remoteCursors, whisper, initEcho, cleanupEcho, onCanvasMouseMove, broadcastCursor } = useDiagramPresence({
    token, ownerIdentity, viewport, schema, canvasWrapperRef,
    onDiagramSaved: () => $toast.success('Diagram saved'),
})

const { hasPendingVisitors, startVisitorPolling, stopVisitorPolling, startGuestAccessPolling, stopGuestAccessPolling } = useDiagramPolling({
    diagramId, isOwner, diagramRequireApproval, token, diagramShareAccess, pendingApproval, notAvailable,
})

const { offScreenCursors } = useOffScreenCursors({ remoteCursors, canvasWidth, canvasHeight })

const { onNodeMouseEnter, onNodeMouseLeave, elevateTable, onNodeDragStart, onNodeDrag, onNodeDragStop, lastInteractedTableId } = useTableInteraction({
    findNode, schema, whisper, isSaved, broadcastCursor, snapshot,
})

const { startTableResize } = useTableResize({ schema, viewport, whisper, isSaved, snapshot })

const { startRowDrag } = useRowDrag({ schema, isSaved, whisper, snapshot })

const logAction = (action, details = null) => {
    if (props.isDemo || !diagramId.value) return
    Diagram.addChangelogEntry(diagramId.value, action, details)
}

const {
    isPlacingTable, isConnecting, copyingTableId,
    selectedEdge, showRelationshipModal, modalPosition,
    addTable, copyTable, onPaneClick,
    addRow, deleteEdge, deleteNode, onConnect, onEdgeUpdate,
    updateConnectionLineType, onRowChange, updateLabel, updateEdgeColor, updateTableColor,
    onTableConstraintsChange, onTableFulltextChange, toggleOptionsModal,
    openRelationshipModal, closeRelationshipModal,
} = useSchemaActions({ schema, isSaved, whisper, diagramDbType, addEdges, updateEdge, findNode, screenToFlowCoordinate, snapshot, logAction })

const isValidConnection = ({ source, target }) => {
    const sourceNode = findNode(source)
    const targetNode = findNode(target)
    return sourceNode?.parentNode !== targetNode?.parentNode
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

// --- Import / Export ---

const showImportModal = ref(false)
const importContent = ref('')
const importLoading = ref(false)
const showExportModal = ref(false)
const exportContent = ref('')
const exportJsonContent = ref('')

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
        showImportModal.value = false
        whisper('schema-sync', { schema: schema.value })
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
        await router.push({ name: 'register' })
        return
    }
    await (isOwner.value ? Diagram.save(diagramId.value, schema.value) : Diagram.saveByToken(token, schema.value))
    isSaved.value = true
    whisper('diagram-saved', {})
}

// --- Load ---

const retryAccess = async () => {
    pendingApproval.value = false
    await getDiagram()
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
        await router.push({ name: 'login' })
        return
    }

    const diagramInfo = await Diagram.getByToken(token)

    if (!diagramInfo) {
        loading.value = false
        notAvailable.value = true
        return
    }

    if (diagramInfo.pending_approval) {
        loading.value = false
        pendingApproval.value = true
        return
    }

    diagramId.value = diagramInfo.id
    isOwner.value = diagramInfo.is_owner ?? false
    diagramShareAccess.value = diagramInfo.share_access ?? null
    diagramRequireApproval.value = diagramInfo.require_approval ?? false
    diagramInLibrary.value = diagramInfo.library ?? false

    if (isOwner.value && diagramRequireApproval.value) {
        const visitors = await Diagram.getVisitors(diagramId.value)
        hasPendingVisitors.value = visitors?.some(v => v.status === 'pending') ?? false
        startVisitorPolling()
    }
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
    if (!isOwner.value) startGuestAccessPolling()
}

const onKeyDown = (event) => {
    if (event.key === 'Escape') {
        isPlacingTable.value = false
        copyingTableId.value = null
    }
    if ((event.ctrlKey || event.metaKey) && event.key === 'd') {
        event.preventDefault()
        if (canEdit.value && lastInteractedTableId.value) copyTable(lastInteractedTableId.value)
    }
    if ((event.ctrlKey || event.metaKey) && event.key === 'a') {
        event.preventDefault()
        if (canEdit.value) addTable()
    }
    if ((event.ctrlKey || event.metaKey) && event.key === 's') {
        event.preventDefault()
        if (canEdit.value) saveDiagram()
    }
    if ((event.ctrlKey || event.metaKey) && event.key === 'z') {
        event.preventDefault()
        if (!canEdit.value) return
        const prev = undo()
        if (prev !== null) {
            schema.value = prev
            isSaved.value = false
            whisper('schema-sync', { schema: schema.value })
        }
    }
    if ((event.ctrlKey || event.metaKey) && event.key === 'y') {
        event.preventDefault()
        if (!canEdit.value) return
        const next = redo()
        if (next !== null) {
            schema.value = next
            isSaved.value = false
            whisper('schema-sync', { schema: schema.value })
        }
    }
}

let autoSaveTimer = null

onBeforeMount(getDiagram)

onMounted(() => {
    if (!props.isDemo) {
        autoSaveTimer = setInterval(() => {
            if (!isSaved.value && canEdit.value) saveDiagram()
        }, 60000)
    } else {
        nextTick(() => fitView({ padding: 0.15, duration: 0 }))
    }
    document.addEventListener('keydown', onKeyDown)
})

onUnmounted(() => {
    clearInterval(autoSaveTimer)
    stopVisitorPolling()
    stopGuestAccessPolling()
    if (!isSaved.value && canEdit.value && !props.isDemo) saveDiagram()
    cleanupEcho()
    document.removeEventListener('keydown', onKeyDown)
})
</script>

<style scoped>
.is-placing-table * {
    cursor: crosshair !important;
}

.diagram-status-screen {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: var(--bg-page);
}

.diagram-status-screen__icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    margin-bottom: 16px;
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    display: grid;
    place-items: center;
}

.diagram-status-screen__logo-mark {
    width: 22px;
    height: 22px;
    border-radius: 5px;
    background: linear-gradient(135deg, var(--color-primary-text), var(--color-primary));
    display: grid;
    place-items: center;
    color: #0c0c0c;
    font-family: monospace;
    font-weight: 700;
    font-size: 11px;
}

.diagram-status-screen__text {
    font-size: 0.92rem;
    color: var(--text-secondary);
    font-family: monospace;
}

.diagram-status-screen__subtext {
    font-size: 0.78rem;
    color: var(--text-muted);
    text-align: center;
    max-width: 320px;
    line-height: 1.5;
    margin-top: 0.4rem;
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

/* ── Feedback panel ──────────────────────────────────────────── */
.feedback-panel {
    margin: 0 0 12px 12px;
}

.feedback-panel__btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: var(--bg-surface);
    font-size: 13px;
    color: var(--text-primary);
    cursor: pointer;
    box-shadow: 0 1px 4px rgba(0,0,0,0.12);
    white-space: nowrap;
}

.feedback-panel__btn:hover {
    background: var(--hover-bg-alt);
}

.feedback-panel__btn {
    color: var(--text-secondary);
}

/* ── Table navigator ─────────────────────────────────────────── */
.table-navigator {
    display: flex;
    flex-direction: column;
    margin: 12px;
}

.table-navigator__toggle {
    width: 32px;
    height: 32px;
    padding: 6px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: var(--bg-surface);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 1px 4px rgba(0,0,0,0.12);
}

.table-navigator__toggle {
    color: var(--text-secondary);
}

.table-navigator__toggle:hover {
    background: var(--hover-bg-alt);
}

.table-navigator__list {
    margin-top: 4px;
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    min-width: 160px;
    max-height: 280px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.table-navigator__item {
    padding: 6px 12px;
    background: none;
    border: none;
    color: var(--text-primary);
    font-size: 13px;
    cursor: pointer;
    text-align: left;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.table-navigator__item:hover {
    background: var(--hover-bg-alt);
}

.table-navigator__empty {
    padding: 8px 12px;
    font-size: 12px;
    color: var(--text-muted);
}
</style>
