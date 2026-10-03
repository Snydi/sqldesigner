<template>
    <div class="schema-doctor-overlay" @click.self="$emit('close')">
        <section class="schema-doctor-modal" role="dialog" aria-modal="true" aria-labelledby="schema-doctor-title">
            <header class="schema-doctor-modal__header">
                <div>
                    <h2 id="schema-doctor-title">Schema Doctor</h2>
                    <p>Check the saved diagram for structural and design problems.</p>
                </div>
                <button class="schema-doctor-modal__close" type="button" aria-label="Close" @click="$emit('close')">
                    <SvgIcon name="close" :size="20" />
                </button>
            </header>

            <div class="schema-doctor-modal__quota">
                <span v-if="allowance?.limited">
                    {{ allowance.used }} / {{ allowance.limit }} scans used today
                    <span v-if="allowance.resets_at">· resets {{ formattedReset }}</span>
                </span>
                <span v-else>Unlimited scans</span>
            </div>

            <div class="schema-doctor-modal__body">
                <div v-if="saveStatus === 'saving'" class="schema-doctor-state">
                    <span class="schema-doctor-spinner" aria-hidden="true"></span>
                    <p>Saving the current diagram…</p>
                </div>

                <div v-else-if="saveStatus === 'failed'" class="schema-doctor-state schema-doctor-state--error">
                    <p>The diagram could not be saved. Schema Doctor will not scan an older version.</p>
                    <button class="btn btn-primary schema-doctor-action" type="button" @click="$emit('retry-save')">Retry save</button>
                </div>

                <div v-else-if="saveStatus === 'dirty'" class="schema-doctor-state">
                    <p>The diagram changed after it was saved. Save the latest version before scanning.</p>
                    <button class="btn btn-primary schema-doctor-action" type="button" @click="$emit('retry-save')">Save changes</button>
                </div>

                <template v-else>
                    <div v-if="scanning" class="schema-doctor-state">
                        <span class="schema-doctor-spinner" aria-hidden="true"></span>
                        <p>Checking the saved schema…</p>
                    </div>

                    <div v-else-if="limitMessage" class="schema-doctor-state schema-doctor-state--limit">
                        <p>{{ limitMessage }}</p>
                        <a class="btn btn-primary schema-doctor-action" href="/pricing">View Pro plan</a>
                    </div>

                    <div v-else-if="requestError" class="schema-doctor-state schema-doctor-state--error">
                        <p>{{ requestError }}</p>
                        <button class="btn btn-primary schema-doctor-action" type="button" @click="scan">Try again</button>
                    </div>

                    <template v-else-if="hasScanned">
                        <div v-if="diagnostics.length === 0" class="schema-doctor-empty">
                            <SvgIcon name="check-circle" :size="30" />
                            <div>
                                <strong>No issues found</strong>
                                <p>Schema Doctor checked the saved diagram using the current rule set.</p>
                            </div>
                        </div>

                        <template v-else>
                            <div class="schema-doctor-summary">
                                <span class="schema-doctor-summary__item schema-doctor-summary__item--error">{{ summary.errors }} errors</span>
                                <span class="schema-doctor-summary__item schema-doctor-summary__item--warning">{{ summary.warnings }} warnings</span>
                                <span class="schema-doctor-summary__item">{{ summary.suggestions }} suggestions</span>
                            </div>
                            <ul class="schema-doctor-results">
                                <li
                                    v-for="(diagnostic, index) in diagnostics"
                                    :key="`${diagnostic.rule_id}-${diagnostic.table_id}-${diagnostic.row_id}-${index}`"
                                    class="schema-doctor-result"
                                    :class="`schema-doctor-result--${diagnostic.severity}`"
                                >
                                    <div class="schema-doctor-result__heading">
                                        <span class="schema-doctor-result__severity">{{ diagnostic.severity }}</span>
                                        <strong>{{ diagnostic.title }}</strong>
                                    </div>
                                    <p>{{ diagnostic.message }}</p>
                                    <p class="schema-doctor-result__recommendation">{{ diagnostic.recommendation }}</p>
                                    <code>{{ diagnostic.rule_id }}</code>
                                </li>
                            </ul>
                        </template>
                    </template>

                    <div v-else class="schema-doctor-ready">
                        <p>Schema Doctor is ready to analyze the version that was just saved.</p>
                    </div>

                    <div class="schema-doctor-footer">
                        <button
                            class="btn btn-primary schema-doctor-action"
                            type="button"
                            :disabled="scanning || saveStatus !== 'ready' || allowance?.remaining === 0"
                            @click="scan"
                        >
                            {{ hasScanned ? 'Check again' : 'Check schema' }}
                        </button>
                    </div>
                </template>
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import axios from '@/axios.js'
import { Diagram } from '@/services/Diagram.js'
import SvgIcon from '@/components/SvgIcon.vue'

const props = defineProps({
    diagramId: { type: Number, required: true },
    saveStatus: { type: String, required: true },
})

defineEmits(['close', 'retry-save'])

const diagnostics = ref([])
const summary = ref({ errors: 0, warnings: 0, suggestions: 0 })
const allowance = ref(null)
const scanning = ref(false)
const hasScanned = ref(false)
const requestError = ref('')
const limitMessage = ref('')

const formattedReset = computed(() => {
    if (!allowance.value?.resets_at) return ''
    return new Intl.DateTimeFormat('en-GB', {
        timeZone: 'Europe/Moscow',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
        timeZoneName: 'short',
    }).format(new Date(allowance.value.resets_at))
})

const loadAllowance = async () => {
    try {
        const { data } = await axios.get('/api/plan-limits')
        allowance.value = {
            limited: data.schema_doctor_limit !== null,
            limit: data.schema_doctor_limit,
            used: data.schema_doctor_scans_used_today,
            remaining: data.schema_doctor_limit === null
                ? null
                : Math.max(0, data.schema_doctor_limit - data.schema_doctor_scans_used_today),
            resets_at: data.schema_doctor_resets_at,
        }
        if (allowance.value.remaining === 0) {
            limitMessage.value = `Your ${allowance.value.limit} free Schema Doctor scans have been used today. Upgrade to Pro for unlimited scans.`
        }
    } catch {
        // The scan endpoint remains authoritative; quota display can stay unavailable.
    }
}

const scan = async () => {
    if (props.saveStatus !== 'ready' || scanning.value) return

    scanning.value = true
    requestError.value = ''
    limitMessage.value = ''

    try {
        const result = await Diagram.scanSchema(props.diagramId)
        diagnostics.value = result.diagnostics ?? []
        summary.value = result.summary ?? { errors: 0, warnings: 0, suggestions: 0 }
        allowance.value = result.allowance ?? allowance.value
        hasScanned.value = true
    } catch (error) {
        if (error.response?.status === 403 && error.response?.data?.allowance) {
            allowance.value = error.response.data.allowance
            limitMessage.value = error.response.data.message
        } else {
            requestError.value = error.response?.data?.message ?? 'Schema Doctor could not complete the scan.'
        }
    } finally {
        scanning.value = false
    }
}

onMounted(loadAllowance)
</script>

<style scoped>
.schema-doctor-overlay {
    position: fixed;
    inset: 0;
    z-index: 1100;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(0, 0, 0, 0.65);
}

.schema-doctor-modal {
    width: min(720px, 100%);
    max-height: min(780px, calc(100vh - 2rem));
    overflow: hidden;
    display: flex;
    flex-direction: column;
    color: var(--text-primary);
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    box-shadow: 0 24px 70px rgba(0, 0, 0, 0.5);
}

.schema-doctor-modal,
.schema-doctor-modal button,
.schema-doctor-modal a,
.schema-doctor-modal code {
    font-size: 16px;
}

.schema-doctor-modal__header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    background: var(--color-primary);
}

.schema-doctor-modal__header h2 {
    margin: 0;
    color: white;
    font-size: 22px;
    text-transform: none;
}

.schema-doctor-modal__header p {
    margin: 0.35rem 0 0;
    color: rgba(255, 255, 255, 0.82);
    line-height: 1.45;
}

.schema-doctor-modal__close {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: 0;
    border-radius: 50%;
    color: white;
    background: rgba(255, 255, 255, 0.14);
    cursor: pointer;
}

.schema-doctor-modal__close:hover {
    background: rgba(255, 255, 255, 0.25);
}

.schema-doctor-modal__quota {
    padding: 0.75rem 1.5rem;
    color: var(--text-secondary);
    background: var(--bg-surface-alt);
    border-bottom: 1px solid var(--border-color);
}

.schema-doctor-modal__body {
    min-height: 230px;
    padding: 1.5rem;
    overflow-y: auto;
}

.schema-doctor-state,
.schema-doctor-ready,
.schema-doctor-empty {
    min-height: 150px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    text-align: center;
}

.schema-doctor-state p,
.schema-doctor-ready p,
.schema-doctor-empty p {
    margin: 0;
    color: var(--text-secondary);
    line-height: 1.55;
}

.schema-doctor-state--error p {
    color: #f0b6b6;
}

.schema-doctor-state--limit p {
    color: #f6c978;
}

.schema-doctor-spinner {
    width: 28px;
    height: 28px;
    border: 3px solid var(--border-color);
    border-top-color: var(--color-primary-text);
    border-radius: 50%;
    animation: schema-doctor-spin 0.7s linear infinite;
}

.schema-doctor-summary {
    display: flex;
    flex-wrap: wrap;
    gap: 0.65rem;
    margin-bottom: 1rem;
}

.schema-doctor-summary__item {
    padding: 0.35rem 0.7rem;
    color: var(--text-secondary);
    background: var(--bg-surface-alt);
    border: 1px solid var(--border-color);
    border-radius: 999px;
}

.schema-doctor-summary__item--error {
    color: #f0b6b6;
}

.schema-doctor-summary__item--warning {
    color: #f6c978;
}

.schema-doctor-results {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
    margin: 0;
    padding: 0;
}

.schema-doctor-result {
    padding: 1rem;
    background: var(--bg-surface-alt);
    border: 1px solid var(--border-color);
    border-left-width: 4px;
    border-radius: 8px;
}

.schema-doctor-result--error {
    border-left-color: #dc6262;
}

.schema-doctor-result--warning {
    border-left-color: #d9a441;
}

.schema-doctor-result--suggestion {
    border-left-color: var(--color-primary-text);
}

.schema-doctor-result__heading {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.6rem;
}

.schema-doctor-result__severity {
    font-weight: 700;
    text-transform: uppercase;
    color: var(--text-muted);
}

.schema-doctor-result p {
    margin: 0.65rem 0 0;
    line-height: 1.5;
    color: var(--text-secondary);
}

.schema-doctor-result .schema-doctor-result__recommendation {
    color: var(--text-primary);
}

.schema-doctor-result code {
    display: inline-block;
    margin-top: 0.75rem;
    color: var(--text-muted);
}

.schema-doctor-footer {
    display: flex;
    justify-content: flex-end;
    margin-top: 1.25rem;
}

.schema-doctor-action {
    min-height: 42px;
    text-decoration: none;
}

.schema-doctor-action:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

@keyframes schema-doctor-spin {
    to { transform: rotate(360deg); }
}
</style>
