<template>
    <header class="header">
        <div class="flex-items">
            <button class="btn btn-secondary" @click="addTable">Add Table</button>
            <button class="btn btn-secondary" @click="openImportModal">Import</button>
            <button class="btn btn-secondary" @click="openExportModal">Export</button>
        </div>
        <div class="flex-items">
            <div class="save-button-wrapper">
                <button class="btn btn-secondary" @click="saveDiagram">Save</button>
                <div
                    class="save-indicator"
                    :class="{ 'saved': isSaved, 'unsaved': !isSaved }"
                    :title="isSaved ? 'All changes saved' : 'Unsaved changes'"
                ></div>
            </div>
        </div>
    </header>

    <VueFlow
        :default-edge-options="{ type:'chickenFoot' }"
        @edge-update="onEdgeUpdate"
        @edge-click="openRelationshipModal"
        @connect="onConnect"
        v-model="schema"
        fit-view-on-init
        :zoomOnDoubleClick=false
        :controlled=false
        class=".vue-flow"
    >
        <!--Chicken foot custom edge component -->
        <template #edge-chickenFoot="props">
            <ChickenFootEdge v-bind="props"></ChickenFootEdge>
        </template>

        <Background :variant="BackgroundVariant.Lines" />

        <!-- Table -->
        <template #node-table="nodeProps">
            <TableNode
                :id="nodeProps.id"
                :data="nodeProps.data"
                :label="nodeProps.label"
                @add-row="addRow(nodeProps)"
                @delete-node="deleteNode"
                @update-label="updateLabel"
            />
        </template>

        <!-- Row -->
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
    </VueFlow>

    <!--Relationship modal-->
    <RelationshipModal
        v-if="showRelationshipModal"
        :position="modalPosition"
        @update-type="updateConnectionLineType"
        @delete="deleteEdge"
        @close="showRelationshipModal = false"
    />

    <!--Import modal-->
    <SqlModal
        v-if="showImportModal"
        v-model="importContent"
        primaryLabel="Import"
        @primary-action="importSql"
        @close="showImportModal = false"
    />

    <!--Export modal-->
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

import { TableActions } from '@/services/TableActions.js'
import { Diagram } from '@/services/Diagram.js'

import ChickenFootEdge from './ChickenFootEdge.vue'
import TableNode from './TableNode.vue'
import RowNode from './RowNode.vue'
import RelationshipModal from './RelationshipModal.vue'
import SqlModal from './SqlModal.vue'
import { useRoute } from 'vue-router'
import { useStore } from 'vuex'
import '@/css/diagram.css'
import '@/css/header.css'

const { updateEdge, addEdges } = useVueFlow()

const store = useStore()
store.dispatch('initializeAuth')
const route = useRoute()
const diagramId = route.params.id

const isSaved = ref(true)
const autoSaveTimer = ref(null)

const modalPosition = ref({ x: 0, y: 0 })
const selectedEdge = ref(null)
const showRelationshipModal = ref(false)

const showImportModal = ref(false)
const importContent = ref('')

const showExportModal = ref(false)
const exportContent = ref('')

const schema = ref()

const TableStyle = {
    display: 'flex',
    border: '1px solid #10b981',
    background: '#6c757d',
    borderColor: '#6c757d',
    color: 'white',
    width: '350px',
    height: '40px',
    alignItems: 'center',
    justifyContent: 'space-between'
}

const addTable = () => {
    TableActions.addTable(schema, TableStyle, 'new_table')
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

function onConnect(params) {
    params.updatable = true
    addEdges([params])
    isSaved.value = false
}

function onEdgeUpdate({ edge, connection }) {
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
    const offsetX = 350

    const documentX = row.position.x
    const documentY = row.position.y

    const rowHeight = 60
    const rowIndex = schema.value.findIndex(el => el.id === id)
    const offsetY = rowIndex * (rowHeight - 20)

    row.data.modalPosition = { x: documentX + offsetX, y: documentY - offsetY }
    row.data.showOptionsModal = !row.data.showOptionsModal
}

const openRelationshipModal = (params) => {
    selectedEdge.value = params.edge
    const edgeElement = document.querySelector(`[id="${params.edge.id}"]`)
    const edgeRect = edgeElement.getBoundingClientRect()
    modalPosition.value = {
        x: edgeRect.left + window.scrollX + edgeRect.width / 2,
        y: edgeRect.top + window.scrollY + edgeRect.height / 2
    }
    showRelationshipModal.value = true
}

const openImportModal = () => {
    showImportModal.value = true
}

const importSql = async () => {
    schema.value = await Diagram.import(diagramId, importContent.value)
    isSaved.value = false
}

const openExportModal = () => {
    showExportModal.value = true
}

const exportSql = async () => {
    await Diagram.save(diagramId, schema.value)
    exportContent.value = await Diagram.export(diagramId)
}

const saveDiagram = async () => {
    await Diagram.save(diagramId, schema.value)
    isSaved.value = true
}

const getDiagram = async (diagramId) => {
    schema.value = await Diagram.get(diagramId)
    if (schema.value == null) {
        schema.value = [
            {
                id: '1',
                type: 'table',
                label: 'users',
                data: { toolbarPosition: Position.Top, toolbarVisible: true },
                position: { x: 0, y: -100 },
                style: TableStyle
            }
        ]
    }
    isSaved.value = true
}

onBeforeMount(() => {
    getDiagram(diagramId)
})

onMounted(() => {
    if (autoSaveTimer.value) {
        clearInterval(autoSaveTimer.value)
    }
    autoSaveTimer.value = setInterval(() => {
        if (!isSaved.value) {
            saveDiagram()
        }
    }, 60000)
})

onUnmounted(() => {
    if (autoSaveTimer.value) {
        clearInterval(autoSaveTimer.value)
    }
})
</script>
