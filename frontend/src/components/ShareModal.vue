<template>
    <div class="share-modal-overlay" @click.self="$emit('close')">
        <div class="share-modal">
            <div class="share-modal__header">
                <span class="share-modal__title">Share Diagram</span>
                <button class="share-modal__close" @click="$emit('close')" aria-label="Close">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 3L13 13M13 3L3 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
            <div class="share-modal__body">
                <div class="share-modal__toggle-row">
                    <span class="share-modal__toggle-label">{{ shareAccess ? 'Sharing enabled' : 'Sharing disabled' }}</span>
                    <button class="share-toggle" :class="{ 'share-toggle--on': shareAccess }" @click="toggleShare" :disabled="loading">
                        <span class="share-toggle__knob"></span>
                    </button>
                </div>

                <div v-if="shareAccess" class="share-modal__access-row">
                    <span class="share-modal__toggle-label">Access</span>
                    <div class="share-modal__seg">
                        <button class="share-modal__seg-btn" :class="{ 'share-modal__seg-btn--active': shareAccess === 'read' }" @click="setAccessMode('read')" :disabled="loading">Read-only</button>
                        <button class="share-modal__seg-btn" :class="{ 'share-modal__seg-btn--active': shareAccess === 'write' }" @click="setAccessMode('write')" :disabled="loading">Can edit</button>
                        <button class="share-modal__seg-btn" :class="{ 'share-modal__seg-btn--active': shareAccess === 'per_user' }" @click="setAccessMode('per_user')" :disabled="loading">Per user</button>
                    </div>
                </div>

                <div v-if="shareAccess" class="share-modal__link-row">
                    <input class="share-modal__link-input" :value="shareUrl" readonly />
                    <button class="btn btn-primary share-modal__copy-btn" @click="copyLink">{{ copied ? 'Copied!' : 'Copy' }}</button>
                </div>

                <p v-if="shareAccess" class="share-modal__hint">
                    <template v-if="shareAccess === 'per_user'">Each visitor is assigned their own access level.</template>
                    <template v-else>Anyone with this link can {{ shareAccess === 'write' ? 'edit' : 'view' }} this diagram.</template>
                </p>

                <div v-if="shareAccess" class="share-modal__approval-row">
                    <label class="share-modal__checkbox-label">
                        <input type="checkbox" class="share-modal__checkbox" :checked="requireApproval" @change="toggleApproval" :disabled="loading" />
                        <span>Approve visitors on first visit</span>
                    </label>
                    <span class="share-modal__help-icon">
                        ?
                        <span class="share-modal__tooltip">When enabled, users who open the link are placed in a pending queue. You must approve each one before they can access the diagram.</span>
                    </span>
                </div>

                <div v-if="shareAccess" class="share-modal__visitors">
                    <span class="share-modal__toggle-label">Visitors</span>
                    <div v-if="visitorsLoading" class="share-modal__visitors-empty">Loading…</div>
                    <div v-else-if="visitors.length === 0" class="share-modal__visitors-empty">No visitors yet.</div>
                    <div v-else>
                        <div v-for="visitor in visitors" :key="visitor.id" class="share-modal__visitor">
                            <span class="share-modal__visitor-name">{{ visitor.name }}</span>
                            <div class="share-modal__visitor-actions">
                                <template v-if="visitor.status === 'revoked'">
                                    <button
                                        class="share-modal__vbtn share-modal__vbtn--approve"
                                        @click="approveVisitor(visitor)"
                                        :disabled="loading"
                                    >Approve</button>
                                </template>
                                <template v-else>
                                    <template v-if="shareAccess === 'per_user'">
                                        <button
                                            class="share-modal__vbtn"
                                            :class="{ 'share-modal__vbtn--active': visitor.status === 'approved' && visitor.access === 'write' }"
                                            @click="setVisitorAccess(visitor, 'write')"
                                            :disabled="loading"
                                        >Write</button>
                                        <button
                                            class="share-modal__vbtn"
                                            :class="{ 'share-modal__vbtn--active': visitor.status === 'approved' && visitor.access === 'read' }"
                                            @click="setVisitorAccess(visitor, 'read')"
                                            :disabled="loading"
                                        >Read</button>
                                    </template>
                                    <template v-else>
                                        <button
                                            v-if="visitor.status === 'pending'"
                                            class="share-modal__vbtn share-modal__vbtn--approve"
                                            @click="approveVisitor(visitor)"
                                            :disabled="loading"
                                        >Approve</button>
                                    </template>
                                    <button
                                        class="share-modal__vbtn share-modal__vbtn--revoke"
                                        @click="setVisitorAccess(visitor, 'revoke')"
                                        :disabled="loading"
                                    >Revoke</button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Diagram } from '@/services/Diagram.js'

const props = defineProps({
    diagramId: { type: Number, required: true },
    token: { type: String, required: true },
    shareAccess: { type: String, default: null },
    requireApproval: { type: Boolean, default: false },
    hasPendingVisitors: { type: Boolean, default: false },
})
const emit = defineEmits(['close', 'update:shareAccess', 'update:requireApproval', 'update:hasPendingVisitors'])

const loading = ref(false)
const copied = ref(false)
const visitors = ref([])
const visitorsLoading = ref(false)
const shareUrl = computed(() => `${window.location.origin}/diagrams/${props.token}`)

const fetchVisitors = async () => {
    visitorsLoading.value = true
    visitors.value = await Diagram.getVisitors(props.diagramId) ?? []
    visitorsLoading.value = false
}

watch(visitors, (v) => {
    emit('update:hasPendingVisitors', v.some(vis => vis.status === 'pending'))
}, { deep: true })

watch(
    () => props.shareAccess,
    (active) => { if (active) fetchVisitors() },
    { immediate: true }
)

const toggleShare = async () => {
    loading.value = true
    if (props.shareAccess) {
        await Diagram.unshare(props.diagramId)
        emit('update:shareAccess', null)
        copied.value = false
    } else {
        emit('update:shareAccess', await Diagram.share(props.diagramId) ?? 'read')
    }
    loading.value = false
}

const setAccessMode = async (mode) => {
    loading.value = true
    const result = await Diagram.setAccessMode(props.diagramId, mode)
    if (result) emit('update:shareAccess', result.share_access)
    loading.value = false
}

const toggleApproval = async (event) => {
    loading.value = true
    const value = event.target.checked
    const result = await Diagram.updateRequireApproval(props.diagramId, value)
    emit('update:requireApproval', result ?? value)
    loading.value = false
}

const approveVisitor = async (visitor) => {
    loading.value = true
    const result = await Diagram.approveVisitor(props.diagramId, visitor.id)
    if (result) visitor.status = 'approved'
    loading.value = false
}

const setVisitorAccess = async (visitor, access) => {
    loading.value = true
    const result = await Diagram.updateVisitorAccess(props.diagramId, visitor.id, access)
    if (result) {
        visitor.status = result.visitor_status
        visitor.access = result.access
    }
    loading.value = false
}

const copyLink = async () => {
    await navigator.clipboard.writeText(shareUrl.value)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
}
</script>

<style scoped>
.share-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 200;
}

.share-modal {
    background: var(--bg-surface);
    border-radius: 10px;
    padding: 1.5rem;
    width: 380px;
    max-width: calc(100vw - 2rem);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
}

.share-modal__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
}

.share-modal__title {
    color: var(--color-primary);
    font-size: 0.85rem;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.share-modal__close {
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.45);
    border-radius: 6px;
    transition: color 0.15s, background 0.15s;
    flex-shrink: 0;
}

.share-modal__close:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
}

.share-modal__body {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.share-modal__toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.share-modal__toggle-label {
    font-size: 0.8rem;
    color: var(--text-subtle);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.share-toggle {
    width: 44px;
    height: 24px;
    background: var(--border-color);
    border: none;
    border-radius: 12px;
    cursor: pointer;
    position: relative;
    transition: background 0.2s;
    padding: 0;
    flex-shrink: 0;
}

.share-toggle--on {
    background: var(--color-primary);
}

.share-toggle:disabled {
    opacity: 0.5;
    cursor: default;
}

.share-toggle__knob {
    position: absolute;
    top: 3px;
    left: 3px;
    width: 18px;
    height: 18px;
    background: white;
    border-radius: 50%;
    transition: transform 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.share-toggle--on .share-toggle__knob {
    transform: translateX(20px);
}

/* Access mode selector */
.share-modal__access-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.share-modal__seg {
    display: flex;
    gap: 0.35rem;
    flex-shrink: 0;
}

.share-modal__seg-btn {
    padding: 0.28rem 0.65rem;
    font-size: 0.72rem;
    font-family: inherit;
    letter-spacing: 0.4px;
    text-transform: uppercase;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    background: var(--bg-surface-alt);
    color: var(--text-muted);
    cursor: pointer;
    transition: border-color 0.15s, background 0.15s, color 0.15s;
    white-space: nowrap;
}

.share-modal__seg-btn:hover:not(:disabled):not(.share-modal__seg-btn--active) {
    border-color: var(--border-strong);
    background: var(--hover-bg-alt);
    color: var(--text-subtle);
}

.share-modal__seg-btn--active {
    border-color: var(--color-primary);
    background: var(--color-primary);
    color: white;
}

.share-modal__seg-btn:disabled {
    opacity: 0.5;
    cursor: default;
}

/* Link row */
.share-modal__link-row {
    display: flex;
    gap: 0.5rem;
}

.share-modal__link-input {
    flex: 1;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    padding: 0.4rem 0.6rem;
    font-size: 0.75rem;
    font-family: inherit;
    color: var(--text-subtle);
    background: var(--bg-surface-alt);
    outline: none;
    min-width: 0;
    text-transform: none;
}

.share-modal__copy-btn {
    font-size: 0.75rem;
    padding: 0.4rem 0.75rem;
    flex-shrink: 0;
    font-family: inherit;
    letter-spacing: 0.5px;
}

.share-modal__hint {
    margin: 0;
    font-size: 0.72rem;
    color: var(--text-muted);
    text-transform: none;
    letter-spacing: 0;
    line-height: 1.4;
}

/* Approval checkbox row */
.share-modal__approval-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    border-top: 1px solid var(--border-color);
    padding-top: 1rem;
}

.share-modal__checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.8rem;
    color: var(--text-subtle);
    cursor: pointer;
    flex: 1;
}

.share-modal__checkbox {
    accent-color: var(--color-primary);
    width: 14px;
    height: 14px;
    cursor: pointer;
    flex-shrink: 0;
}

.share-modal__help-icon {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 1px solid var(--border-color);
    font-size: 0.65rem;
    color: var(--text-muted);
    cursor: default;
    flex-shrink: 0;
}

.share-modal__help-icon:hover .share-modal__tooltip {
    opacity: 1;
    pointer-events: auto;
}

.share-modal__tooltip {
    position: absolute;
    bottom: calc(100% + 6px);
    right: 0;
    width: 220px;
    background: var(--bg-surface-alt);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 0.5rem 0.65rem;
    font-size: 0.72rem;
    color: var(--text-subtle);
    line-height: 1.45;
    text-transform: none;
    letter-spacing: 0;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.15s;
    z-index: 10;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Visitors list */
.share-modal__visitors {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    border-top: 1px solid var(--border-color);
    padding-top: 1rem;
}

.share-modal__visitors-empty {
    font-size: 0.75rem;
    color: var(--text-muted);
    text-transform: none;
    letter-spacing: 0;
}

.share-modal__visitor {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.3rem 0;
}

.share-modal__visitor-name {
    flex: 1;
    font-size: 0.75rem;
    color: var(--text-subtle);
    text-transform: none;
    letter-spacing: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    min-width: 0;
}

.share-modal__visitor-actions {
    display: flex;
    gap: 0.25rem;
    flex-shrink: 0;
}

/* Visitor inline buttons */
.share-modal__vbtn {
    padding: 0.18rem 0.45rem;
    font-size: 0.68rem;
    font-family: inherit;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    border: 1px solid var(--border-color);
    border-radius: 3px;
    background: var(--bg-surface-alt);
    color: var(--text-muted);
    cursor: pointer;
    transition: border-color 0.15s, background 0.15s, color 0.15s;
    white-space: nowrap;
}

.share-modal__vbtn:hover:not(:disabled):not(.share-modal__vbtn--active) {
    border-color: var(--border-strong);
    background: var(--hover-bg-alt);
    color: var(--text-subtle);
}

.share-modal__vbtn--active {
    border-color: var(--color-primary);
    background: color-mix(in srgb, var(--color-primary) 18%, transparent);
    color: var(--color-primary);
}

.share-modal__vbtn:disabled {
    opacity: 0.5;
    cursor: default;
}

.share-modal__vbtn--approve {
    border-color: color-mix(in srgb, var(--color-primary) 50%, var(--border-color));
    color: var(--color-primary-text);
}

.share-modal__vbtn--approve:hover:not(:disabled) {
    border-color: var(--color-primary);
    background: color-mix(in srgb, var(--color-primary) 15%, transparent);
}

.share-modal__vbtn--revoke {
    border-color: color-mix(in srgb, #ef4444 40%, var(--border-color));
    color: color-mix(in srgb, #ef4444 60%, var(--text-muted));
}

.share-modal__vbtn--revoke:hover:not(:disabled):not(.share-modal__vbtn--active) {
    border-color: #ef4444;
    background: color-mix(in srgb, #ef4444 10%, transparent);
    color: #ef4444;
}

</style>
