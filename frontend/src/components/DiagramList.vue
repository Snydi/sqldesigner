<template>
    <div class="diagrams-page">
        <div class="diagrams-header">
            <h2 class="diagrams-title">{{ activeView === 'mine' ? 'My Diagrams' : 'Shared with Me' }}</h2>
            <span v-if="activeView === 'mine'" class="diagrams-count-badge" title="Diagrams used / plan limit">{{ diagrams.length }} / <span v-if="diagramLimit === null" class="diagrams-count-badge__infinity">∞</span><span v-else>{{ diagramLimit }}</span></span>
            <span v-else class="diagrams-count-badge">{{ diagrams.length }}</span>
        </div>

        <div class="diagrams-content">
            <nav class="diagrams-sidebar" aria-label="Diagram lists">
                <button
                    class="diagrams-sidebar__item"
                    :class="{ 'diagrams-sidebar__item--active': activeView === 'mine' }"
                    @click="switchView('mine')"
                >
                    <SvgIcon name="table-list" :size="17" />
                    <span>My diagrams</span>
                </button>
                <button
                    class="diagrams-sidebar__item"
                    :class="{ 'diagrams-sidebar__item--active': activeView === 'shared' }"
                    @click="switchView('shared')"
                >
                    <SvgIcon name="share" :size="17" />
                    <span>Shared with me</span>
                </button>
            </nav>

            <div class="diagrams-grid-container">
                <div v-if="loading" class="diagrams-empty">Loading diagrams…</div>
                <div v-else-if="activeView === 'shared' && diagrams.length === 0" class="diagrams-empty">
                    <SvgIcon name="share" :size="30" />
                    <strong>No shared diagrams yet</strong>
                    <span>Diagrams you have been granted access to will appear here.</span>
                </div>
                <div v-else class="diagrams-grid">
                <!-- New diagram card -->
                <div v-if="activeView === 'mine'" class="diagram-card diagram-card--new" @click="openNewForm">
                    <div class="diagram-card__preview diagram-card__preview--empty">
                        <SvgIcon name="plus" :size="40" class="new-diagram-plus" />
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
                            v-if="activeView === 'mine'"
                            class="diagram-card__delete"
                            @click.stop="deleteDiagram(diagram.id)"
                            title="Delete"
                        >
                            <SvgIcon name="trash" :size="14" />
                        </button>
                    </div>
                    <div class="diagram-card__footer">
                        <img
                            :src="dbIcons[diagram.db_type] || dbIcons.mysql"
                            :alt="diagram.db_type"
                            class="diagram-card__db-icon"
                        />
                        <input
                            v-if="renamingId === diagram.id"
                            ref="renameInput"
                            v-model="diagram.name"
                            class="diagram-card__name-input"
                            @click.stop
                            @keyup.enter="commitRename(diagram)"
                            @keyup.escape="cancelRename(diagram)"
                            @blur="commitRename(diagram)"
                        />
                        <span
                            v-else
                            class="diagram-card__name"
                            :class="{ 'diagram-card__name--readonly': activeView === 'shared' }"
                            @click.stop="activeView === 'mine' ? startRename(diagram) : viewDiagram(diagram.share_token)"
                            :title="activeView === 'mine' ? 'Click to rename' : diagram.name"
                        >{{ diagram.name }}</span>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <!-- New Diagram Modal -->
        <div v-if="showNewForm" class="create-modal-overlay" @click.self="showNewForm = false">
            <div class="create-modal">

                <div class="create-modal__header">
                    <span class="create-modal__title">New Diagram</span>
                    <button class="create-modal__close" @click="showNewForm = false" title="Close">
                        <SvgIcon name="close" :size="14" />
                    </button>
                </div>

                <div class="create-modal__body">
                    <div class="create-modal__field">
                        <span class="create-modal__label">Name</span>
                        <input
                            ref="newNameInput"
                            v-model="newDiagramName"
                            class="create-modal__input"
                            placeholder="My diagram"
                            @keyup.enter="addDiagram"
                            @keyup.escape="showNewForm = false"
                        />
                    </div>

                    <div class="create-modal__field">
                        <span class="create-modal__label">Database</span>
                        <div class="create-modal__db-options">
                            <button
                                v-for="db in dbOptions"
                                :key="db.type"
                                class="db-option"
                                :class="{ 'db-option--active': newDiagramDbType === db.type }"
                                @click="newDiagramDbType = db.type"
                                :title="db.label"
                            >
                                <img :src="dbIcons[db.type]" :alt="db.label" />
                                <span>{{ db.label }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="create-modal__field">
                        <span class="create-modal__label">Visibility</span>
                        <div class="create-modal__vis-row">
                            <div class="create-modal__vis-chips">
                                <button
                                    class="create-modal__vis-btn"
                                    :class="{ 'create-modal__vis-btn--active': newDiagramPublic }"
                                    @click="newDiagramPublic = true"
                                >Public</button>
                                <button
                                    class="create-modal__vis-btn"
                                    :class="{ 'create-modal__vis-btn--active': !newDiagramPublic }"
                                    @click="newDiagramPublic = false"
                                >Private</button>
                            </div>
                            <template v-if="newDiagramPublic">
                                <label class="create-modal__checkbox-label">
                                    <input type="checkbox" class="create-modal__checkbox" v-model="newDiagramInLibrary" />
                                    <span>Add to Library</span>
                                </label>
                                <span class="create-modal__help-icon">
                                    ?
                                    <span class="create-modal__tooltip">When enabled, this diagram appears in read-only mode in the public <a href="/library" target="_blank" style="color: var(--color-primary-text); cursor: pointer; text-decoration: underline;">Schema Library</a> for anyone to browse.</span>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="create-modal__footer">
                    <button class="create-modal__btn create-modal__btn--cancel" @click="showNewForm = false">Cancel</button>
                    <button class="create-modal__btn create-modal__btn--create" @click="addDiagram">Create</button>
                </div>
            </div>
        </div>
        <UpgradePrompt v-if="upgradeMessage" :message="upgradeMessage" @close="upgradeMessage = ''" />
    </div>
</template>

<script>
import axios from 'axios'
import router from '../router/index.js'
import { useToast } from 'vue-toast-notification'
import { Diagram } from '@/services/Diagram.js'
import { DEMO_SCHEMA_STORAGE_KEY } from '@/services/demoSchema.js'
import DiagramPreview from './Diagram/DiagramPreview.vue'
import SvgIcon from './SvgIcon.vue'
import UpgradePrompt from './UpgradePrompt.vue'
import { isPlanLimitError } from '@/services/Subscription.js'
import mysqlIcon from '../icons/mysql.svg'
import postgresqlIcon from '../icons/postgresql.svg'
import sqliteIcon from '../icons/sqlite.svg'
import oracleIcon from '../icons/oracle.svg'
import sqlserverIcon from '../icons/sqlserver.svg'
import msaccessIcon from '../icons/msaccess.svg'

const $toast = useToast()

export default {
    components: { DiagramPreview, SvgIcon, UpgradePrompt },
    data() {
        return {
            diagrams: [],
            activeView: 'mine',
            loading: false,
            diagramLimit: null,
            newDiagramName: '',
            newDiagramDbType: 'mysql',
            newDiagramPublic: true,
            newDiagramInLibrary: true,
            showNewForm: false,
            renamingId: null,
            originalName: null,
            upgradeMessage: '',
            dbIcons: { mysql: mysqlIcon, postgresql: postgresqlIcon, sqlite: sqliteIcon, oracle: oracleIcon, sqlserver: sqlserverIcon, msaccess: msaccessIcon },
            dbOptions: [
                { type: 'mysql', label: 'MySQL' },
                { type: 'postgresql', label: 'PostgreSQL' },
                { type: 'sqlite', label: 'SQLite' },
                { type: 'oracle', label: 'Oracle' },
                { type: 'sqlserver', label: 'SQL Server' },
                { type: 'msaccess', label: 'MS Access' },
            ]
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
            if (!this.newDiagramName.trim()) {
                $toast.error('Diagram name cannot be empty.')
                return
            }
            try {
                const response = await axios.post('/api/diagrams', {
                    name: this.newDiagramName,
                    db_type: this.newDiagramDbType,
                    share_access: this.newDiagramPublic ? 'read' : null,
                    library: this.newDiagramPublic ? this.newDiagramInLibrary : false
                })
                this.newDiagramName = ''
                this.newDiagramDbType = 'mysql'
                this.newDiagramPublic = true
                this.newDiagramInLibrary = true
                this.showNewForm = false
                await this.fetchDiagrams()
                $toast.success(response.data.message)
            } catch (error) {
                if (isPlanLimitError(error)) {
                    this.upgradeMessage = error.response?.data?.message ?? 'Upgrade to Pro to create more diagrams.'
                    return
                }
                const errors = error.response?.data?.errors
                if (errors?.name) {
                    $toast.error(`A diagram named "${this.newDiagramName}" already exists.`)
                } else {
                    $toast.error(error.response?.data?.message ?? 'Something went wrong!')
                }
            }
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
            const view = this.activeView
            this.loading = true
            try {
                const endpoint = view === 'mine' ? '/api/diagrams' : '/api/diagrams/shared-with-me'
                const response = await axios.get(endpoint)
                if (this.activeView === view) {
                    this.diagrams = response.data.data
                }
            } catch (error) {
                if (error.response) {
                    $toast.error(error.response.data.message)
                } else {
                    $toast.error('Something went wrong!')
                }
            } finally {
                if (this.activeView !== view) return
                this.loading = false
            }
        },
        switchView(view) {
            if (this.activeView === view) return
            this.activeView = view
            this.diagrams = []
            this.renamingId = null
            this.originalName = null
            this.fetchDiagrams()
        },
        async fetchPlanLimits() {
            try {
                const response = await axios.get('/api/plan-limits')
                this.diagramLimit = response.data.diagram_limit
            } catch {
                // silently skip — badge falls back to ∞
            }
        },
        async importDemoDiagram() {
            const stored = localStorage.getItem(DEMO_SCHEMA_STORAGE_KEY)
            if (!stored) return

            localStorage.removeItem(DEMO_SCHEMA_STORAGE_KEY)

            let schema
            try {
                schema = JSON.parse(stored)
            } catch {
                return
            }
            if (!Array.isArray(schema) || !schema.length) return

            const result = await Diagram.create({ name: 'My Demo Diagram', db_type: 'mysql', schema })
            if (result) $toast.success('Your demo diagram was saved to your account')
        }
    },
    created() {
        this.importDemoDiagram()
        this.fetchDiagrams()
        this.fetchPlanLimits()
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
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.diagrams-title {
    margin: 0;
    color: var(--color-primary-text);
    font-size: 1rem;
    letter-spacing: 1px;
}

.diagrams-count-badge {
    font-family: ui-monospace, monospace;
    font-size:$11rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    color: var(--text-muted);
    background: var(--bg-surface-alt);
    border: 1px solid var(--border-color);
    border-radius: 999px;
    padding: 2px 10px;
}

.diagrams-count-badge__infinity {
    font-size: 1.1rem;
    line-height: 1;
    vertical-align: middle;
    display: inline-block;
}

.diagrams-grid-container {
    flex: 1;
    min-width: 0;
    overflow-y: auto;
}

.diagrams-content {
    flex: 1;
    min-height: 0;
    display: flex;
}

.diagrams-sidebar {
    width: 210px;
    flex-shrink: 0;
    padding: 1.25rem 0.75rem;
    background: var(--bg-surface);
    border-right: 1px solid var(--border-light);
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.diagrams-sidebar__item {
    width: 100%;
    border: 1px solid transparent;
    border-radius: 6px;
    padding: 0.65rem 0.75rem;
    background: transparent;
    color: var(--text-muted);
    font: inherit;
    font-size:$11rem;
    text-align: left;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.65rem;
    transition: color 120ms, background 120ms, border-color 120ms;
}

.diagrams-sidebar__item:hover {
    color: var(--text-primary);
    background: var(--bg-surface-alt);
}

.diagrams-sidebar__item--active {
    color: var(--color-primary-text);
    background: var(--bg-surface-alt);
    border-color: var(--border-color);
}

.diagrams-empty {
    min-height: 280px;
    padding: 2rem;
    color: var(--text-muted);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.65rem;
    text-align: center;
}

.diagrams-empty strong {
    color: var(--text-secondary);
    font-size:$11rem;
}

.diagrams-empty span {
    max-width: 360px;
    font-size:$11rem;
    line-height: 1.5;
}

.diagrams-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1.5rem;
    padding: 2rem;
}

@media (max-width: 700px) {
    .diagrams-content {
        flex-direction: column;
    }

    .diagrams-sidebar {
        width: auto;
        padding: 0.65rem 1rem;
        border-right: 0;
        border-bottom: 1px solid var(--border-light);
        flex-direction: row;
    }

    .diagrams-sidebar__item {
        width: auto;
        flex: 1;
        justify-content: center;
    }
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
    opacity: 0.25;
    color: var(--text-primary);
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
    background: var(--bg-surface);
    border: 1px solid var(--border-light);
    border-radius: 50%;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 4px;
    color: var(--text-muted);
    transition: background 120ms, border-color 120ms, color 120ms;
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
    background: rgba(239, 68, 68, 0.15);
    border-color: rgba(239, 68, 68, 0.35);
    color: #ef4444;
}

/* ── Card footer ────────────────────────────────────────────── */
.diagram-card__footer {
    padding: 0.55rem 0.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 48px;
    position: relative;
}

.diagram-card__name {
    font-size:$11rem;
    color: var(--text-secondary);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex: 0 1 50%;
    text-align: center;
    cursor: text;
}

.diagram-card__name--readonly {
    cursor: pointer;
}

.diagram-card__db-icon {
    position: absolute;
    left: 0.8rem;
    width: 32px;
    height: 32px;
    opacity: 0.85;
}

.diagram-card__name-input {
    flex: 0 1 35%;
    font-size:$11rem;
    border: none;
    border-bottom: 1px solid var(--color-primary);
    background: transparent;
    outline: none;
    color: var(--text-secondary);
    font-family: inherit;
    text-align: center;
    padding: 0;
    min-width: 0;
}

/* ── Create modal ───────────────────────────────────────────── */
.create-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 200;
}

.create-modal {
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    width: 400px;
    max-width: calc(100vw - 2rem);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Header */
.create-modal__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    border-bottom: 1px solid var(--border-color);
    flex-shrink: 0;
}

.create-modal__title {
    font-family: ui-monospace, monospace;
    font-size:$11rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-secondary);
}

.create-modal__close {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px;
    background: none;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    color: var(--text-muted);
    transition: color 0.12s, background 0.12s;
}

.create-modal__close:hover {
    color: var(--text-secondary);
    background: var(--hover-bg);
}

/* Body */
.create-modal__body {
    padding: 20px 20px 0;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.create-modal__field {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.create-modal__label {
    font-family: ui-monospace, monospace;
    font-size:$11rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-muted);
}

.create-modal__input {
    width: 100%;
    padding: 9px 12px;
    background: var(--bg-surface-alt);
    border: 1px solid var(--border-color);
    border-radius: 7px;
    font-size:$11rem;
    font-family: inherit;
    color: var(--text-primary);
    outline: none;
    box-sizing: border-box;
    transition: border-color 0.12s;
}

.create-modal__input::placeholder {
    color: var(--text-muted);
}

.create-modal__input:focus {
    border-color: var(--color-primary-text);
}

/* DB chips */
.create-modal__db-options {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
}

.db-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 10px 12px 8px;
    border: 1.5px solid var(--border-color);
    border-radius: 8px;
    background: transparent;
    cursor: pointer;
    transition: border-color 0.12s, background 0.12s;
}

.db-option img {
    width: 22px;
    height: 22px;
    object-fit: contain;
}

.db-option span {
    font-family: ui-monospace, monospace;
    font-size:$11rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--text-muted);
    transition: color 0.12s;
}

.db-option:hover:not(.db-option--active) {
    border-color: var(--border-strong);
    background: var(--bg-surface-alt);
}

.db-option--active {
    border-color: var(--color-primary-text);
    background: rgba(93, 181, 131, 0.1);
}

.db-option--active span {
    color: var(--color-primary-text);
}

/* Visibility row */
.create-modal__vis-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.create-modal__vis-chips {
    display: flex;
    gap: 6px;
}

.create-modal__vis-btn {
    padding: 6px 14px;
    font-family: ui-monospace, monospace;
    font-size:$11rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    border: 1.5px solid var(--border-color);
    border-radius: 6px;
    background: transparent;
    color: var(--text-muted);
    cursor: pointer;
    transition: border-color 0.12s, background 0.12s, color 0.12s;
    white-space: nowrap;
}

.create-modal__vis-btn:hover:not(.create-modal__vis-btn--active) {
    border-color: var(--border-strong);
    background: var(--bg-surface-alt);
    color: var(--text-subtle);
}

.create-modal__vis-btn--active {
    border-color: var(--color-primary-text);
    background: rgba(93, 181, 131, 0.1);
    color: var(--color-primary-text);
}

/* Library checkbox */
.create-modal__checkbox-label {
    display: flex;
    align-items: center;
    gap: 7px;
    font-family: ui-monospace, monospace;
    font-size:$11rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--text-muted);
    cursor: pointer;
    white-space: nowrap;
}

.create-modal__checkbox {
    accent-color: var(--color-primary-text);
    width: 15px;
    height: 15px;
    cursor: pointer;
    flex-shrink: 0;
}

/* Help icon + tooltip */
.create-modal__help-icon {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 1px solid var(--border-color);
    font-size:$11rem;
    color: var(--text-muted);
    cursor: default;
    flex-shrink: 0;
}

.create-modal__help-icon:hover .create-modal__tooltip,
.create-modal__tooltip:hover {
    opacity: 1;
    pointer-events: auto;
}

.create-modal__tooltip {
    position: absolute;
    bottom: calc(100% + 6px);
    right: 0;
    width: 220px;
    background: var(--bg-surface-alt);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 0.5rem 0.65rem;
    font-size:$11rem;
    font-family: inherit;
    color: var(--text-subtle);
    line-height: 1.45;
    text-transform: none;
    letter-spacing: 0;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.15s;
    z-index: 10;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Footer */
.create-modal__footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    padding: 16px 20px;
    margin-top: 20px;
    border-top: 1px solid var(--border-color);
}

.create-modal__btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 8px 14px;
    border-radius: 7px;
    font-family: ui-monospace, monospace;
    font-size:$11rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    cursor: pointer;
    transition: background 0.12s, border-color 0.12s;
    white-space: nowrap;
}

.create-modal__btn--cancel {
    border: 1px solid var(--border-color);
    background: var(--bg-surface-alt);
    color: var(--text-secondary);
}

.create-modal__btn--cancel:hover {
    border-color: var(--border-strong);
    background: var(--hover-bg-alt);
}

.create-modal__btn--create {
    border: none;
    background: var(--color-primary-text);
    color: #0c1f15;
}

.create-modal__btn--create:hover {
    background: #6ec994;
}
</style>
