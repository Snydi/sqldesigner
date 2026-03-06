<template>
    <Header
        :addTable="addTable"
        :openImportModal="openImportModal"
        :openExportModal="openExportModal"
        :saveDiagram="saveDiagram"
        :isSaved="isSaved"
    >
    </Header>

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
        <template #node-table="{ id, data, label }">
            <button class="table_button" @mousedown.stop @click="addRow({ id, data, label })">
                <img class="table_icon" src="../icons/plus.svg" alt="Add row">
            </button>

            <input
                class="input input_designer_table"
                :value="label"
                @click="data.editing = true"
                @blur="() => { data.editing = false; updateLabel(id, label); }"
                @input="updateLabel(id, $event.target.value)"
                :readonly="!data.editing"
            />

            <button class="table_button" @mousedown.stop @click="deleteNode(id)">
                <img class="table_icon" src="../icons/cancel.svg" alt="Cancel">
            </button>
        </template>
        <!-- Row -->
        <template #node-row="{ id, data, label }">
            <input
                class="input input_designer_row ml-5 mr-5"
                :value="label"
                @click="data.editing = true"
                @blur="() => { data.editing = false; updateLabel(id, label); }"
                @input="updateLabel(id, $event.target.value)"
                :readonly="!data.editing"
            />

            <!-- SQL Type -->
            <div>
                <select v-model="data.sqlType">
                    <option value="TINYINT(1)">TINYINT</option>
                    <option value="BIGINT">BIGINT</option>
                    <option value="CHAR(255)">CHAR</option>
                    <option value="VARCHAR(255)">VARCHAR</option>
                    <option value="TEXT">TEXT</option>
                    <option value="DATE">DATE</option>
                    <option value="DATETIME">DATETIME</option>
                    <option value="TIME">TIME</option>
                    <option value="TIMESTAMP">TIMESTAMP</option>
                    <option v-bind:value="data.sqlType">{{ data.sqlType }}</option>
                </select>
            </div>

            <!-- Options -->
            <button class="table_button" @mousedown.stop @click="toggleOptionsModal(id, $event)">
                <img class="table_icon" src="../icons/dots.svg" alt="More options">
            </button>

            <!-- Options modal -->
            <div v-if="data.showOptionsModal" class="options_modal"
                 :style="{ left: `${data.modalPosition.x}px`, top: `${data.modalPosition.y}px` }">
                <select v-model="data.keyMod" @change="updateKeyMod(id, data.keyMod)">
                    <option selected="selected" value="None">None</option>
                    <option value="PRIMARY KEY">Primary</option>
                    <option value="UNIQUE">Unique</option>
                    <option value="INDEX">Index</option>
                </select>
                <p class="modal_text">Unsigned</p>
                <input type="checkbox" @mousedown.stop :checked="data.unsigned" @change="toggleUnsigned(id)">
                <p class="modal_text">Nullable</p>
                <input type="checkbox" @mousedown.stop :checked="data.nullable" @change="toggleNullable(id)">
            </div>

            <!-- Delete row -->
            <button class="table_button" @mousedown.stop @click="deleteNode(id)">
                <img class="table_icon" src="../icons/cancel.svg" alt="Cancel">
            </button>

            <Handle type="source" position="right" />
            <Handle type="source" position="left" />
        </template>

    </VueFlow>
    <!--Relationship modal-->
    <div v-if="showRelationshipModal" class="relationship_modal" ref="relationshipModal"
         :style="{ left: `${modalPosition.x}px`, top: `${modalPosition.y}px` }">
        <button @click="updateConnectionLineType('one-to-one')">One to one</button>
        <button @click="updateConnectionLineType('one-to-many')">One to many</button>
        <button @click="updateConnectionLineType('many-to-one')">Many to one</button>
        <button @click="updateConnectionLineType('many-to-many')">Many to many</button>
        <button @click="deleteEdge">Delete</button>
    </div>
    <!--Import modal-->
    <div v-if="showImportModal" class="modal flex-centered">
        <div class="sql_modal_content">
            <textarea class="sql_textarea" v-model="importContent"></textarea>
            <button class="btn btn-primary" @click="importSql">Import</button>
            <button class="btn btn-primary" @click="showImportModal = false">Close</button>
        </div>
    </div>
    <!--Export modal-->
    <div v-if="showExportModal" class="modal sql_modal flex-centered">
        <div class="sql_modal_content ">
            <textarea class="sql_textarea" v-model="exportContent"></textarea>
            <button class="btn btn-primary" @click="exportSql">Export</button>
            <button class="btn btn-primary" @click="showExportModal = false">Close</button>
        </div>
    </div>
</template>

<script setup>
import { onBeforeMount, onMounted, onUnmounted, ref } from 'vue'
import { Handle, Position, useVueFlow, VueFlow } from '@vue-flow/core'
import { Background, BackgroundVariant } from '@vue-flow/background'

import { TableActions } from '@/services/TableActions.js'
import { Diagram } from '@/services/Diagram.js'

import Header from './Header.vue'
import ChickenFootEdge from './ChickenFootEdge.vue'
import { useRoute } from 'vue-router'
import { useStore } from 'vuex'
import { onClickOutside } from '@vueuse/core'
import '@/css/diagram.css'

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
    borderRadius: '5px',
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

const updateKeyMod = (id, keyMod) => {
    const element = schema.value.find(el => el.id === id)
    if (element) {
        element.data.keyMod = keyMod
        isSaved.value = false
    }
}

const toggleNullable = (id) => {
    const element = schema.value.find(el => el.id === id)
    if (element) {
        element.data.nullable = !element.data.nullable
        isSaved.value = false
    }
}

const toggleUnsigned = (id) => {
    const element = schema.value.find(el => el.id === id)
    if (element) {
        element.data.unsigned = !element.data.unsigned
        isSaved.value = false
    }
}

const toggleOptionsModal = (id) => {
    const row = schema.value.find(el => el.id === id)
    const offsetX = 350

    const documentX = row.position.x
    const documentY = row.position.y

    const rowHeight = 60
    const rowIndex = schema.value.find(el => el.id === id)
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
    isSaved.value = false
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

const relationshipModal = ref(null)

onClickOutside(relationshipModal, () => {
    showRelationshipModal.value = false
})

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
