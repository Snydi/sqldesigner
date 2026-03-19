<template>
    <header class="header header--diagram">
        <div class="flex-items">
            <button class="btn btn-secondary" @click="addTable" title="Add Table">
                <img src="../icons/table-add.svg" alt="Add Table" class="icon">
            </button>
            <button class="btn btn-secondary" @click="isDemo ? router.push({ name: 'register' }) : showImportModal = true" title="Import">
                <img src="../icons/import.svg" alt="Import" class="icon">
            </button>
            <button class="btn btn-secondary" @click="isDemo ? router.push({ name: 'register' }) : openExportModal()" title="Export">
                <img src="../icons/export.svg" alt="Export" class="icon">
            </button>
        </div>
        <div class="flex-items">
            <div class="save-button-wrapper">
                <button class="btn btn-secondary" @click="saveDiagram" title="Save" :disabled="!isDemo && isSaved">
                    <img src="../icons/save.svg" alt="Save" class="icon">
                </button>
                <div v-if="!isDemo"
                    :title="isSaved ? 'All changes saved' : 'Unsaved changes'"
                ></div>
            </div>
        </div>
    </header>

    <VueFlow
        :default-edge-options="{ type: 'chickenFoot' }"
        @edge-update="onEdgeUpdate"
        @edge-click="openRelationshipModal"
        @connect="onConnect"
        v-model="schema"
        fit-view-on-init
        :zoomOnDoubleClick="false"
        :controlled="false"
        class=".vue-flow"
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
            />
        </template>

        <template #node-row="nodeProps">
            <RowNode
                :id="nodeProps.id"
                :data="nodeProps.data"
                :label="nodeProps.label"
                @update-label="updateLabel"
                @toggle-options-modal="toggleOptionsModal"
                @delete-node="deleteNode"
                @change="isSaved = false"
                @row-drag-start="startRowDrag"
            />
        </template>

        <template #node-add-row-button="nodeProps">
            <AddRowNode
                @add-row="addRow({ id: nodeProps.data.tableId, data: {} })"
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

    <SqlModal
        v-if="showImportModal"
        v-model="importContent"
        primaryLabel="Import"
        @primary-action="importSql"
        @close="showImportModal = false"
    />

    <SqlModal
        v-if="showExportModal"
        v-model="exportContent"
        primaryLabel="Export"
        @primary-action="exportSql"
        @close="showExportModal = false"
    />
</template>

<script setup>
import { onBeforeMount, onMounted, onUnmounted, ref } from 'vue'
import { Position, useVueFlow, VueFlow } from '@vue-flow/core'
import { Background, BackgroundVariant } from '@vue-flow/background'
import { TableActions, TABLE_STYLE, ADD_ROW_BUTTON_STYLE, ROW_STYLE } from '@/services/TableActions.js'
import { Diagram } from '@/services/Diagram.js'
import ChickenFootEdge from './ChickenFootEdge.vue'
import TableNode from './TableNode.vue'
import RowNode from './RowNode.vue'
import AddRowNode from './AddRowNode.vue'
import RelationshipModal from './RelationshipModal.vue'
import SqlModal from './SqlModal.vue'
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex'
import '@vue-flow/core/dist/style.css'
import '@vue-flow/core/dist/theme-default.css'
import '@/css/diagram.css'
import '@/css/header.css'

const props = defineProps({ isDemo: { type: Boolean, default: false } })

const { updateEdge, addEdges } = useVueFlow()
const store = useStore()
store.dispatch('initializeAuth')
const router = useRouter()

const diagramId = useRoute().params.id

const DEMO_SCHEMA = [
    { id: 'dt1', type: 'table', label: 'users', data: { toolbarPosition: Position.Top, toolbarVisible: true }, position: { x: 0, y: 0 }, style: TABLE_STYLE },
    { id: 'dr1', type: 'row', label: 'id', position: { x: 0, y: 40 }, style: ROW_STYLE, draggable: false, parentNode: 'dt1', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'PRIMARY KEY', sqlType: 'INT(11)', nullable: false, unsigned: false } },
    { id: 'dr2', type: 'row', label: 'username', position: { x: 0, y: 80 }, style: ROW_STYLE, draggable: false, parentNode: 'dt1', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'None', sqlType: 'VARCHAR(255)', nullable: false, unsigned: false } },
    { id: 'dr3', type: 'row', label: 'email', position: { x: 0, y: 120 }, style: ROW_STYLE, draggable: false, parentNode: 'dt1', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'None', sqlType: 'VARCHAR(255)', nullable: false, unsigned: false } },
    { id: 'dr4', type: 'row', label: 'created_at', position: { x: 0, y: 160 }, style: ROW_STYLE, draggable: false, parentNode: 'dt1', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'None', sqlType: 'TIMESTAMP', nullable: false, unsigned: false } },
    { id: 'dbtn1', type: 'add-row-button', label: '', position: { x: 0, y: 200 }, style: ADD_ROW_BUTTON_STYLE, draggable: false, parentNode: 'dt1', data: { tableId: 'dt1' } },

    { id: 'dt2', type: 'table', label: 'posts', data: { toolbarPosition: Position.Top, toolbarVisible: true }, position: { x: 450, y: 0 }, style: TABLE_STYLE },
    { id: 'dr5', type: 'row', label: 'id', position: { x: 0, y: 40 }, style: ROW_STYLE, draggable: false, parentNode: 'dt2', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'PRIMARY KEY', sqlType: 'INT(11)', nullable: false, unsigned: false } },
    { id: 'dr6', type: 'row', label: 'user_id', position: { x: 0, y: 80 }, style: ROW_STYLE, draggable: false, parentNode: 'dt2', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'FOREIGN KEY', sqlType: 'INT(11)', nullable: false, unsigned: false } },
    { id: 'dr7', type: 'row', label: 'title', position: { x: 0, y: 120 }, style: ROW_STYLE, draggable: false, parentNode: 'dt2', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'None', sqlType: 'VARCHAR(255)', nullable: false, unsigned: false } },
    { id: 'dr8', type: 'row', label: 'body', position: { x: 0, y: 160 }, style: ROW_STYLE, draggable: false, parentNode: 'dt2', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'None', sqlType: 'TEXT', nullable: false, unsigned: false } },
    { id: 'dr9', type: 'row', label: 'created_at', position: { x: 0, y: 200 }, style: ROW_STYLE, draggable: false, parentNode: 'dt2', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'None', sqlType: 'TIMESTAMP', nullable: false, unsigned: false } },
    { id: 'dbtn2', type: 'add-row-button', label: '', position: { x: 0, y: 240 }, style: ADD_ROW_BUTTON_STYLE, draggable: false, parentNode: 'dt2', data: { tableId: 'dt2' } },

    { id: 'dt3', type: 'table', label: 'comments', data: { toolbarPosition: Position.Top, toolbarVisible: true }, position: { x: 225, y: 380 }, style: TABLE_STYLE },
    { id: 'dr10', type: 'row', label: 'id', position: { x: 0, y: 40 }, style: ROW_STYLE, draggable: false, parentNode: 'dt3', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'PRIMARY KEY', sqlType: 'INT(11)', nullable: false, unsigned: false } },
    { id: 'dr11', type: 'row', label: 'post_id', position: { x: 0, y: 80 }, style: ROW_STYLE, draggable: false, parentNode: 'dt3', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'FOREIGN KEY', sqlType: 'INT(11)', nullable: false, unsigned: false } },
    { id: 'dr12', type: 'row', label: 'user_id', position: { x: 0, y: 120 }, style: ROW_STYLE, draggable: false, parentNode: 'dt3', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'FOREIGN KEY', sqlType: 'INT(11)', nullable: false, unsigned: false } },
    { id: 'dr13', type: 'row', label: 'body', position: { x: 0, y: 160 }, style: ROW_STYLE, draggable: false, parentNode: 'dt3', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'None', sqlType: 'TEXT', nullable: false, unsigned: false } },
    { id: 'dr14', type: 'row', label: 'created_at', position: { x: 0, y: 200 }, style: ROW_STYLE, draggable: false, parentNode: 'dt3', data: { editing: false, showModal: false, showOptionsModal: false, keyMod: 'None', sqlType: 'TIMESTAMP', nullable: false, unsigned: false } },
    { id: 'dbtn3', type: 'add-row-button', label: '', position: { x: 0, y: 240 }, style: ADD_ROW_BUTTON_STYLE, draggable: false, parentNode: 'dt3', data: { tableId: 'dt3' } },

    { id: 'de1', source: 'dr1', target: 'dr6', type: 'chickenFoot', updatable: true, data: { relationshipType: 'one-to-many', markerStart: 'url(#chickenFoot)', markerEnd: 'none' } },
    { id: 'de2', source: 'dr1', target: 'dr12', type: 'chickenFoot', updatable: true, data: { relationshipType: 'one-to-many', markerStart: 'url(#chickenFoot)', markerEnd: 'none' } },
    { id: 'de3', source: 'dr5', target: 'dr11', type: 'chickenFoot', updatable: true, data: { relationshipType: 'one-to-many', markerStart: 'url(#chickenFoot)', markerEnd: 'none' } },
]

const isSaved = ref(true)
let autoSaveTimer = null

const schema = ref()
const modalPosition = ref({ x: 0, y: 0 })
const selectedEdge = ref(null)
const showRelationshipModal = ref(false)
const showImportModal = ref(false)
const importContent = ref('')
const showExportModal = ref(false)
const exportContent = ref('')

const addTable = () => {
    TableActions.addTable(schema, 'new_table')
    isSaved.value = false
}

const addRow = (nodeProps) => {
    TableActions.addRow(schema, nodeProps, {
        rowName: 'new_row',
        keyMod: 'None',
        sqlType: 'INT(11)',
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

const importSql = async () => {
    schema.value = await Diagram.import(diagramId, importContent.value)
    isSaved.value = false
}

const openExportModal = async () => {
    await saveDiagram()
    showExportModal.value = true
}

const exportSql = async () => {
    await Diagram.save(diagramId, schema.value)
    exportContent.value = await Diagram.export(diagramId)
}

const saveDiagram = async () => {
    if (props.isDemo) {
        router.push({ name: 'register' })
        return
    }
    await Diagram.save(diagramId, schema.value)
    isSaved.value = true
}

const getDiagram = async () => {
    if (props.isDemo) {
        schema.value = DEMO_SCHEMA
        return
    }

    const rawSchema = await Diagram.get(diagramId) ?? [{
        id: '1',
        type: 'table',
        label: 'users',
        data: { toolbarPosition: Position.Top, toolbarVisible: true },
        position: { x: 0, y: -100 },
        style: TABLE_STYLE
    }]

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
    isSaved.value = true
}

onBeforeMount(getDiagram)

onMounted(() => {
    if (!props.isDemo) {
        autoSaveTimer = setInterval(() => {
            if (!isSaved.value) saveDiagram()
        }, 60000)
    }
})

onUnmounted(() => clearInterval(autoSaveTimer))
</script>
