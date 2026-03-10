<template>
    <header class="header header--diagram">
        <div class="flex-items">
            <button class="btn btn-secondary" @click="addTable" title="Add Table">
                <img src="../icons/table-add.svg" alt="Add Table" class="icon">
            </button>
            <button class="btn btn-secondary" @click="showImportModal = true" title="Import">
                <img src="../icons/import.svg" alt="Import" class="icon">
            </button>
            <button class="btn btn-secondary" @click="showExportModal = true" title="Export">
                <img src="../icons/export.svg" alt="Export" class="icon">
            </button>
        </div>
        <div class="flex-items">
            <div class="save-button-wrapper">
                <button class="btn btn-secondary" @click="saveDiagram" title="Save" :disabled="isSaved">
                    <img src="../icons/save.svg" alt="Save" class="icon">
                </button>
                <div
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
import { TableActions, TABLE_STYLE, ADD_ROW_BUTTON_STYLE } from '@/services/TableActions.js'
import { Diagram } from '@/services/Diagram.js'
import ChickenFootEdge from './ChickenFootEdge.vue'
import TableNode from './TableNode.vue'
import RowNode from './RowNode.vue'
import AddRowNode from './AddRowNode.vue'
import RelationshipModal from './RelationshipModal.vue'
import SqlModal from './SqlModal.vue'
import { useRoute } from 'vue-router'
import { useStore } from 'vuex'
import '@/css/diagram.css'
import '@/css/header.css'

const { updateEdge, addEdges } = useVueFlow()
const store = useStore()
store.dispatch('initializeAuth')

const diagramId = useRoute().params.id

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

const toggleOptionsModal = (id) => {
    const row = schema.value.find(el => el.id === id)
    const rowIndex = schema.value.findIndex(el => el.id === id)
    row.data.modalPosition = { x: row.position.x + 350, y: row.position.y - rowIndex * 40 }
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

const exportSql = async () => {
    await Diagram.save(diagramId, schema.value)
    exportContent.value = await Diagram.export(diagramId)
}

const saveDiagram = async () => {
    await Diagram.save(diagramId, schema.value)
    isSaved.value = true
}

const getDiagram = async () => {
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
    autoSaveTimer = setInterval(() => {
        if (!isSaved.value) saveDiagram()
    }, 60000)
})

onUnmounted(() => clearInterval(autoSaveTimer))
</script>
