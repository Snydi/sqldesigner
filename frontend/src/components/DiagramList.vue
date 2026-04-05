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
                    @click="viewDiagram(diagram.share_token)"
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
                            :src="diagram.db_type === 'postgresql' ? postgresqlIcon : mysqlIcon"
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
                        class="db-option"
                        :class="{ 'db-option--active': newDiagramDbType === 'postgresql' }"
                        @click="newDiagramDbType = 'postgresql'"
                        title="PostgreSQL"
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
import mysqlIcon from '../icons/mysql.svg'
import postgresqlIcon from '../icons/postgresql.svg'

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
            originalName: null,
            mysqlIcon,
            postgresqlIcon
        }
    },
    methods: {
        viewDiagram(token) {
            router.push({ name: 'diagram.show', params: { token } })
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
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: var(--bg-elevated);
}

.diagrams-header {
    flex-shrink: 0;
    padding: 1.25rem 2rem;
    background: var(--bg-surface);
    border-bottom: 1px solid var(--border-light);
}

.diagrams-title {
    margin: 0;
    color: var(--color-primary-text);
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

@media (max-width: 480px) {
    .diagrams-grid {
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        padding: 1rem;
    }

    .diagrams-header {
        padding: 1rem;
    }
}

/* ── Cards ─────────────────────────────────────────────────── */
.diagram-card {
    background: var(--bg-surface-alt);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
    overflow: hidden;
    cursor: pointer;
    transition: box-shadow 0.2s, transform 0.2s;
    position: relative;
}

.diagram-card:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.35);
    transform: translateY(-2px);
}

.diagram-card--new {
    border: 2px dashed var(--border-color);
    box-shadow: none;
    background: transparent;
}

.diagram-card--new:hover {
    border-color: var(--color-primary);
    background: var(--bg-surface);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

/* ── Card preview area ──────────────────────────────────────── */
.diagram-card__preview {
    height: 160px;
    background: var(--bg-page);
    border-bottom: 1px solid var(--border-color);
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
    filter: brightness(0) invert(1);
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
    background: var(--bg-surface);
    border: 1px solid var(--border-light);
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
    filter: brightness(0) invert(0.3);
}

.diagram-card:hover .diagram-card__delete {
    display: flex;
}

@media (hover: none) {
    .diagram-card__delete {
        display: flex;
    }
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
    min-height: 48px;
}

.diagram-card__name {
    font-size: 0.875rem;
    color: var(--text-secondary);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex: 1;
    cursor: text;
}

.diagram-card__db-icon {
    width: 32px;
    height: 32px;
    flex-shrink: 0;
    margin-right: 10px;
    opacity: 0.85;
}

.diagram-card__name-input {
    flex: 1;
    font-size: 0.875rem;
    border: none;
    border-bottom: 1px solid var(--color-primary);
    background: transparent;
    outline: none;
    color: var(--text-secondary);
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
    background: var(--bg-surface);
    border-radius: 10px;
    padding: 2rem;
    width: 340px;
    max-width: calc(100vw - 2rem);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.create-modal__title {
    margin: 0;
    color: var(--color-primary-text);
    font-size: 0.9rem;
    letter-spacing: 1px;
}

.create-modal__input {
    border: none;
    border-bottom: 1px solid var(--border-color);
    padding: 0.4rem 0;
    font-size: 0.9rem;
    font-family: inherit;
    text-transform: uppercase;
    outline: none;
    color: var(--text-primary);
    background: transparent;
    transition: border-color 0.15s;
}

.create-modal__input:focus {
    border-bottom-color: var(--color-primary);
}

.create-modal__db-label {
    font-size: 0.875rem;
    color: var(--text-muted);
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
    border: 2px solid var(--border-color);
    border-radius: 8px;
    background: var(--bg-surface);
    cursor: pointer;
    transition: border-color 0.15s, background 0.15s;
}

.db-option img {
    width: 32px;
    height: 32px;
}

.db-option span {
    font-size: 0.875rem;
    color: var(--text-subtle);
    font-family: inherit;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.db-option:hover:not(:disabled) {
    border-color: var(--border-strong);
    background: var(--bg-surface-alt);
}

.db-option--active {
    border-color: var(--color-primary-text) !important;
    background: var(--bg-elevated) !important;
}

.db-option--active span {
    color: var(--color-primary-text);
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
