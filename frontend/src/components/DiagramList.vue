<template>
    <div class="diagrams-page">
        <div class="diagrams-header">
            <h2 class="diagrams-title">Diagrams</h2>
        </div>

        <div class="diagrams-grid-container">
            <div class="diagrams-grid">
                <!-- New diagram card -->
                <div class="diagram-card diagram-card--new" @click="openNewForm">
                    <div class="diagram-card__preview diagram-card__preview--empty">
                        <img src="../icons/plus.svg" class="new-diagram-plus" alt="new diagram" />
                    </div>
                    <div class="diagram-card__footer">
                        <span class="diagram-card__name">New Diagram</span>
                    </div>
                </div>

                <!-- Existing diagrams -->
                <div
                    v-for="diagram in diagrams"
                    :key="diagram.id"
                    class="diagram-card"
                    @click="viewDiagram(diagram.id)"
                >
                    <div class="diagram-card__preview">
                        <DiagramPreview :schema="diagram.schema" />
                        <button
                            class="diagram-card__delete"
                            @click.stop="deleteDiagram(diagram.id)"
                            title="Delete"
                        >
                            <img src="../icons/trash.svg" alt="delete" />
                        </button>
                    </div>
                    <div class="diagram-card__footer" @click.stop>
                        <img
                            :src="diagram.db_type === 'postgresql' ? '../icons/postgresql.svg' : '../icons/mysql.svg'"
                            :alt="diagram.db_type"
                            class="diagram-card__db-icon"
                        />
                        <input
                            v-if="renamingId === diagram.id"
                            ref="renameInput"
                            v-model="diagram.name"
                            class="diagram-card__name-input"
                            @keyup.enter="commitRename(diagram)"
                            @keyup.escape="cancelRename(diagram)"
                            @blur="commitRename(diagram)"
                        />
                        <span
                            v-else
                            class="diagram-card__name"
                            @dblclick="startRename(diagram)"
                            title="Double-click to rename"
                        >{{ diagram.name }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Diagram Modal -->
        <div v-if="showNewForm" class="create-modal-overlay" @click.self="showNewForm = false">
            <div class="create-modal">
                <h3 class="create-modal__title">New Diagram</h3>
                <input
                    ref="newNameInput"
                    v-model="newDiagramName"
                    class="create-modal__input"
                    placeholder="Diagram name"
                    @keyup.enter="addDiagram"
                    @keyup.escape="showNewForm = false"
                />
                <div class="create-modal__db-label">Database</div>
                <div class="create-modal__db-options">
                    <button
                        class="db-option"
                        :class="{ 'db-option--active': newDiagramDbType === 'mysql' }"
                        @click="newDiagramDbType = 'mysql'"
                        title="MySQL"
                    >
                        <img src="../icons/mysql.svg" alt="MySQL" />
                        <span>MySQL</span>
                    </button>
                    <button
                        class="db-option db-option--disabled"
                        disabled
                        title="PostgreSQL (coming soon)"
                    >
                        <img src="../icons/postgresql.svg" alt="PostgreSQL" />
                        <span>PostgreSQL</span>
                    </button>
                </div>
                <div class="create-modal__actions">
                    <button class="btn btn-secondary" @click="showNewForm = false">Cancel</button>
                    <button class="btn btn-primary" @click="addDiagram">Create</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import router from '../router/index.js'
import { useToast } from 'vue-toast-notification'
import DiagramPreview from './DiagramPreview.vue'

const $toast = useToast()

export default {
    components: { DiagramPreview },
    data() {
        return {
            diagrams: [],
            newDiagramName: '',
            newDiagramDbType: 'mysql',
            showNewForm: false,
            renamingId: null,
            originalName: null
        }
    },
    methods: {
        viewDiagram(id) {
            router.push({ path: `/diagrams/${id}` })
        },
        openNewForm() {
            this.showNewForm = true
            this.$nextTick(() => this.$refs.newNameInput?.focus())
        },
        async addDiagram() {
            if (!this.newDiagramName.trim()) return
            const response = await axios.post('/api/diagrams', {
                name: this.newDiagramName,
                db_type: this.newDiagramDbType
            })
            this.newDiagramName = ''
            this.newDiagramDbType = 'mysql'
            this.showNewForm = false
            await this.fetchDiagrams()
            response.status ? $toast.success(response.data.message) : $toast.error(response.data.message)
        },
        async updateDiagram(diagram) {
            const response = await axios.put(`/api/diagrams/${diagram.id}`, { name: diagram.name })
            await this.fetchDiagrams()
            this.originalName = null
            response.status ? $toast.success(response.data.message) : $toast.error(response.data.message)
        },
        async deleteDiagram(id) {
            const response = await axios.delete(`/api/diagrams/${id}`)
            await this.fetchDiagrams()
            response.status ? $toast.success(response.data.message) : $toast.error(response.data.message)
        },
        startRename(diagram) {
            this.originalName = diagram.name
            this.renamingId = diagram.id
            this.$nextTick(() => {
                const input = this.$refs.renameInput
                const el = Array.isArray(input) ? input[0] : input
                el?.focus()
                el?.select()
            })
        },
        commitRename(diagram) {
            if (this.renamingId !== diagram.id) return
            this.renamingId = null
            if (diagram.name !== this.originalName) {
                this.updateDiagram(diagram)
            }
        },
        cancelRename(diagram) {
            diagram.name = this.originalName
            this.renamingId = null
            this.originalName = null
        },
        async fetchDiagrams() {
            try {
                const response = await axios.get('/api/diagrams')
                this.diagrams = response.data.data
            } catch (error) {
                if (error.response) {
                    $toast.error(error.response.data.message)
                } else {
                    $toast.error('Something went wrong!')
                }
            }
        }
    },
    created() {
        this.fetchDiagrams()
    }
}
</script>

<style scoped>
.diagrams-page {
    height: 100vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #f5f5f5;
}

.diagrams-header {
    flex-shrink: 0;
    padding: 1.25rem 2rem;
    background: white;
    border-bottom: 1px solid #e8e8e8;
}

.diagrams-title {
    margin: 0;
    color: var(--color-primary);
    font-size: 1rem;
    letter-spacing: 1px;
}

.diagrams-grid-container {
    flex: 1;
    overflow-y: auto;
}

.diagrams-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1.5rem;
    padding: 2rem;
}

/* ── Cards ─────────────────────────────────────────────────── */
.diagram-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    cursor: pointer;
    transition: box-shadow 0.2s, transform 0.2s;
    position: relative;
}

.diagram-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.diagram-card--new {
    border: 2px dashed #ddd;
    box-shadow: none;
    background: transparent;
}

.diagram-card--new:hover {
    border-color: var(--color-primary);
    background: white;
    box-shadow: 0 2px 8px rgba(192, 82, 82, 0.12);
    transform: translateY(-2px);
}

/* ── Card preview area ──────────────────────────────────────── */
.diagram-card__preview {
    height: 160px;
    background: #fafafa;
    border-bottom: 1px solid #f0f0f0;
    padding: 8px;
    position: relative;
    overflow: hidden;
}

.diagram-card__preview--empty {
    display: flex;
    align-items: center;
    justify-content: center;
}

.new-diagram-plus {
    width: 40px;
    height: 40px;
    opacity: 0.25;
    transition: opacity 0.2s;
}

.diagram-card--new:hover .new-diagram-plus {
    opacity: 0.55;
}

/* ── Delete button (visible on hover) ───────────────────────── */
.diagram-card__delete {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 26px;
    height: 26px;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid #eee;
    border-radius: 50%;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 4px;
    transition: background 0.15s, border-color 0.15s;
}

.diagram-card__delete img {
    width: 14px;
    height: 14px;
}

.diagram-card:hover .diagram-card__delete {
    display: flex;
}

.diagram-card__delete:hover {
    background: #fee;
    border-color: #f5c0c0;
}

/* ── Card footer ────────────────────────────────────────────── */
.diagram-card__footer {
    padding: 0.55rem 0.8rem;
    display: flex;
    align-items: center;
    min-height: 36px;
}

.diagram-card__name {
    font-size: 0.72rem;
    color: #444;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex: 1;
    cursor: text;
}

.diagram-card__db-icon {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    margin-right: 6px;
    opacity: 0.8;
}

.diagram-card__name-input {
    flex: 1;
    font-size: 0.72rem;
    border: none;
    border-bottom: 1px solid var(--color-primary);
    background: transparent;
    outline: none;
    color: #444;
    font-family: inherit;
    text-transform: uppercase;
    padding: 0;
    min-width: 0;
}

/* ── Create modal ───────────────────────────────────────────── */
.create-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 200;
}

.create-modal {
    background: white;
    border-radius: 10px;
    padding: 2rem;
    width: 340px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.create-modal__title {
    margin: 0;
    color: var(--color-primary);
    font-size: 0.9rem;
    letter-spacing: 1px;
}

.create-modal__input {
    border: none;
    border-bottom: 1px solid #ccc;
    padding: 0.4rem 0;
    font-size: 0.9rem;
    font-family: inherit;
    text-transform: uppercase;
    outline: none;
    color: #333;
    background: transparent;
    transition: border-color 0.15s;
}

.create-modal__input:focus {
    border-bottom-color: var(--color-primary);
}

.create-modal__db-label {
    font-size: 0.7rem;
    color: #999;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.create-modal__db-options {
    display: flex;
    gap: 0.75rem;
}

.db-option {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.4rem;
    padding: 0.75rem 0.5rem;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    transition: border-color 0.15s, background 0.15s;
}

.db-option img {
    width: 32px;
    height: 32px;
}

.db-option span {
    font-size: 0.65rem;
    color: #555;
    font-family: inherit;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.db-option:hover:not(:disabled) {
    border-color: #bbb;
    background: #fafafa;
}

.db-option--active {
    border-color: #4479A1 !important;
    background: #f0f5fa !important;
}

.db-option--active span {
    color: #4479A1;
}

.db-option--disabled {
    opacity: 0.35;
    cursor: not-allowed;
}

.create-modal__actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}
</style>
