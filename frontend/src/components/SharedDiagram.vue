<template>
    <div class="shared-wrapper">
        <header class="shared-header">
            <div class="shared-header__left">
                <template v-if="canEdit">
                    <button class="btn btn-secondary" @click="addTable" title="Add Table">
                        <img src="../icons/table-add.svg" alt="Add Table" class="icon">
                    </button>
                </template>
                <div class="shared-header__name">{{ diagramName }}</div>
            </div>
            <div class="shared-header__right">
                <template v-if="canEdit">
                    <div class="save-button-wrapper">
                        <button class="btn btn-secondary" @click="saveDiagram" title="Save" :disabled="isSaved">
                            <img src="../icons/save.svg" alt="Save" class="icon">
                        </button>
                    </div>
                </template>
                <a href="/register" class="shared-header__cta">Create your own</a>
            </div>
        </header>

        <div v-if="loading" class="shared-loading">Loading...</div>

        <div v-else-if="notFound" class="shared-not-found">
            This diagram is no longer available.
        </div>

        <VueFlow
            v-else
            :default-edge-options="{ type: 'chickenFoot' }"
            v-model="schema"
            fit-view-on-init
            :nodes-draggable="canEdit"
            :nodes-connectable="canEdit"
            :elements-selectable="canEdit"
            :zoom-on-double-click="false"
            @edge-update="canEdit ? onEdgeUpdate($event) : null"
            @edge-click="canEdit ? openRelationshipModal($event) : null"
            @connect="canEdit ? onConnect($event) : null"
            class="shared-canvas"
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
                    @delete-node="canEdit ? deleteNode($event) : null"
                    @update-label="canEdit ? updateLabel($event, arguments[1]) : null"
                />
            </template>

            <template #node-row="nodeProps">
                <RowNode
                    :id="nodeProps.id"
                    :data="nodeProps.data"
                    :label="nodeProps.label"
                    :dbType="diagramDbType"
                    @update-label="canEdit ? updateLabel($event, arguments[1]) : null"
                    @toggle-options-modal="canEdit ? toggleOptionsModal($event) : null"
                    @delete-node="canEdit ? deleteNode($event) : null"
                    @change="canEdit ? (isSaved = false) : null"
                    @row-drag-start="canEdit ? startRowDrag($event) : null"
                />
            </template>

            <template #node-add-row-button="nodeProps">
                <AddRowNode
                    @add-row="canEdit ? addRow({ id: nodeProps.data.tableId, data: {} }) : null"
                />
            </template>
        </VueFlow>

        <RelationshipModal
            v-if="showRelationshipModal"
            :position="modalPosition"
            @update-type="updateConnectionLineType"
            @delete="deleteEdge"
            @close="showRelationshipModal = false"
        />
    </div>
</template>

<script setup>
import { onBeforeMount, onMounted, onUnmounted, ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { Position, useVueFlow, VueFlow } from '@vue-flow/core'
import { Background, BackgroundVariant } from '@vue-flow/background'
import { TableActions, TABLE_STYLE, ADD_ROW_BUTTON_STYLE, ROW_STYLE } from '@/services/TableActions.js'
import { Diagram } from '@/services/Diagram.js'
import ChickenFootEdge from './ChickenFootEdge.vue'
import TableNode from './TableNode.vue'
import RowNode from './RowNode.vue'
import AddRowNode from './AddRowNode.vue'
import RelationshipModal from './RelationshipModal.vue'
import '@vue-flow/core/dist/style.css'
import '@vue-flow/core/dist/theme-default.css'
import '@/css/diagram.css'
import '@/css/header.css'

const token = useRoute().params.token

const { updateEdge, addEdges } = useVueFlow()

const schema = ref([])
const diagramName = ref('')
const diagramDbType = ref('mysql')
const shareAccess = ref('read')
const loading = ref(true)
const notFound = ref(false)
const isSaved = ref(true)
const modalPosition = ref({ x: 0, y: 0 })
const selectedEdge = ref(null)
const showRelationshipModal = ref(false)
let autoSaveTimer = null

const canEdit = computed(() => shareAccess.value === 'write')

const addTable = () => {
    TableActions.addTable(schema, 'new_table')
    isSaved.value = false
}

const addRow = (nodeProps) => {
    TableActions.addRow(schema, nodeProps, {
        rowName: 'new_row',
        keyMod: 'None',
        sqlType: diagramDbType.value === 'postgresql' ? 'INTEGER' : 'INT(11)',
        nullable: false,
        unsigned: false
    })
    isSaved.value = false
}

const deleteEdge = () => {
    TableActions.deleteEdge(schema, selectedEdge)
    showRelationshipModal.value = false
    isSaved.value = false
}

const deleteNode = (nodeId) => {
    TableActions.deleteNode(schema, nodeId)
    isSaved.value = false
}

const onConnect = (params) => {
    params.updatable = true
    addEdges([params])
    isSaved.value = false
}

const onEdgeUpdate = ({ edge, connection }) => {
    updateEdge(edge, connection)
    isSaved.value = false
}

const updateConnectionLineType = (relationshipType) => {
    TableActions.updateConnectionLineType(schema, selectedEdge, relationshipType)
    showRelationshipModal.value = false
    isSaved.value = false
}

const updateLabel = (id, newLabel) => {
    const element = schema.value.find(el => el.id === id)
    if (element) {
        element.label = newLabel.replace(' ', '_')
        isSaved.value = false
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
    }

    const onMouseUp = () => {
        draggingRowId.value = null
        document.removeEventListener('mousemove', onMouseMove)
        document.removeEventListener('mouseup', onMouseUp)
    }

    document.addEventListener('mousemove', onMouseMove)
    document.addEventListener('mouseup', onMouseUp)
}

const toggleOptionsModal = (id) => {
    const row = schema.value.find(el => el.id === id)
    row.data.modalPosition = { x: 350, y: 0 }
    row.data.showOptionsModal = !row.data.showOptionsModal
}

const openRelationshipModal = ({ edge }) => {
    selectedEdge.value = edge
    const { left, top, width, height } = document.querySelector(`[id="${edge.id}"]`).getBoundingClientRect()
    modalPosition.value = {
        x: left + window.scrollX + width / 2,
        y: top + window.scrollY + height / 2
    }
    showRelationshipModal.value = true
}

const saveDiagram = async () => {
    await Diagram.saveShared(token, schema.value)
    isSaved.value = true
}

onBeforeMount(async () => {
    const data = await Diagram.getShared(token)

    if (!data) {
        notFound.value = true
        loading.value = false
        return
    }

    diagramName.value = data.name
    diagramDbType.value = data.db_type ?? 'mysql'
    shareAccess.value = data.share_access ?? 'read'

    const rawSchema = data.schema ? JSON.parse(data.schema) : []

    if (shareAccess.value === 'write') {
        const buttons = []
        rawSchema.filter(el => el.type === 'table').forEach(table => {
            const hasButton = rawSchema.some(el => el.type === 'add-row-button' && el.parentNode === table.id)
            if (!hasButton) {
                const rows = rawSchema.filter(el => el.parentNode === table.id && el.type === 'row')
                buttons.push({
                    id: Math.floor(Math.random() * 100000).toString(),
                    type: 'add-row-button',
                    label: '',
                    position: { x: 0, y: 40 + 40 * rows.length },
                    style: ADD_ROW_BUTTON_STYLE,
                    draggable: false,
                    parentNode: table.id,
                    data: { tableId: table.id }
                })
            }
        })
        schema.value = [...rawSchema, ...buttons]
    } else {
        schema.value = rawSchema.filter(el => el.type !== 'add-row-button')
    }

    loading.value = false
})

onMounted(() => {
    autoSaveTimer = setInterval(() => {
        if (canEdit.value && !isSaved.value) saveDiagram()
    }, 60000)
})

onUnmounted(() => clearInterval(autoSaveTimer))
</script>

<style scoped>
.shared-wrapper {
    width: 100vw;
    height: 100vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.shared-canvas {
    flex: 1;
    min-height: 0;
}

.shared-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 1rem;
    height: 48px;
    flex-shrink: 0;
    background-color: var(--color-primary);
    border-bottom: 1px solid var(--color-primary-border);
    box-sizing: border-box;
}

.shared-header__left,
.shared-header__right {
    display: flex;
    align-items: center;
    gap: 10px;
}

.shared-header__name {
    color: white;
    font-size: 0.85rem;
    letter-spacing: 1px;
    text-transform: uppercase;
    font-weight: 600;
}

.shared-header__cta {
    color: white;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    opacity: 0.8;
    text-decoration: underline;
    transition: opacity 0.15s;
}

.shared-header__cta:hover {
    opacity: 1;
}

.shared-loading,
.shared-not-found {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 0.85rem;
    letter-spacing: 1px;
    text-transform: uppercase;
}
</style>
