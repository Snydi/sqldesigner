<template>
    <div class="share-modal-overlay" @click.self="$emit('close')">
        <div class="share-modal">
            <div class="share-modal__header">
                <span class="share-modal__title">Share Diagram</span>
                <button class="share-modal__close" @click="$emit('close')">
                    <img src="../icons/close.svg" alt="Close" style="width:14px;height:14px;" />
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
                    <div class="share-modal__access-options">
                        <button class="share-modal__access-btn" :class="{ 'share-modal__access-btn--active': shareAccess === 'read' }" @click="setShareAccess('read')" :disabled="loading">Read-only</button>
                        <button class="share-modal__access-btn" :class="{ 'share-modal__access-btn--active': shareAccess === 'write' }" @click="setShareAccess('write')" :disabled="loading">Can edit</button>
                    </div>
                </div>
                <div v-if="shareAccess" class="share-modal__link-row">
                    <input class="share-modal__link-input" :value="shareUrl" readonly />
                    <button class="btn btn-primary share-modal__copy-btn" @click="copyLink">{{ copied ? 'Copied!' : 'Copy' }}</button>
                </div>
                <p v-if="shareAccess" class="share-modal__hint">
                    Anyone with this link can {{ shareAccess === 'write' ? 'edit' : 'view' }} this diagram.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Diagram } from '@/services/Diagram.js'

const props = defineProps({
    diagramId: { type: Number, required: true },
    token: { type: String, required: true },
    shareAccess: { type: String, default: null },
})
const emit = defineEmits(['close', 'update:shareAccess'])

const loading = ref(false)
const copied = ref(false)
const shareUrl = computed(() => `${window.location.origin}/diagrams/${props.token}`)

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

const setShareAccess = async (access) => {
    loading.value = true
    emit('update:shareAccess', await Diagram.updateShareAccess(props.diagramId, access) ?? access)
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
    padding: 2px;
    display: flex;
    align-items: center;
    opacity: 0.5;
    transition: opacity 0.15s;
}

.share-modal__close:hover {
    opacity: 1;
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
    text-align: left;
}

.share-modal__access-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.share-modal__access-options {
    display: flex;
    gap: 0.4rem;
}

.share-modal__access-btn {
    padding: 0.3rem 0.75rem;
    font-size: 0.75rem;
    font-family: inherit;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    background: var(--bg-surface);
    color: var(--text-subtle);
    cursor: pointer;
    transition: border-color 0.15s, background 0.15s, color 0.15s;
}

.share-modal__access-btn:hover:not(:disabled) {
    border-color: var(--border-strong);
    background: var(--hover-bg-alt);
}

.share-modal__access-btn--active {
    border-color: var(--color-primary) !important;
    background: var(--color-primary) !important;
    color: white !important;
}

.share-modal__access-btn:disabled {
    opacity: 0.5;
    cursor: default;
}
</style>
