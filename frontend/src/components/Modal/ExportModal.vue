<template>
    <div class="modal flex-centered" @click.self="$emit('close')">
        <div class="export-modal">
            <div class="export-modal__header">
                <div class="export-modal__header-left">
                    <span class="export-modal__title">Export</span>
                    <span class="export-modal__count-badge" title="Exports used today / plan limit">{{ exportCount }} / <span v-if="exportLimit === null">∞</span><span v-else>{{ exportLimit }}</span></span>
                </div>
                <button class="export-modal__close" @click="$emit('close')">
                    <img src="../../icons/close.svg" alt="Close">
                </button>
            </div>
            <div v-if="exportLimit !== null" class="export-modal__quota-row">
                Resets in {{ resetCountdown }} <span class="export-modal__quota-tz">(00:00 UTC+3)</span>
            </div>
            <div v-if="isExporting" class="export-modal__status">
                <span class="export-modal__status-spinner"></span>
                <span class="export-modal__status-text">Export in progress…</span>
            </div>

            <div class="export-modal__body">
                <button class="export-card" :class="{ 'export-card--active': activeExport === 'copy' }" :disabled="isExporting" @click="copyText">
                    <div class="export-card__icon">
                        <svg v-if="activeExport !== 'copy'" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="6" y="10" width="26" height="32" rx="3" fill="var(--bg-surface-alt)" stroke="var(--border-color)" stroke-width="2"/>
                            <rect x="16" y="6" width="26" height="32" rx="3" fill="var(--bg-surface)" stroke="var(--color-primary)" stroke-width="2"/>
                            <line x1="22" y1="16" x2="36" y2="16" stroke="var(--color-primary)" stroke-width="2" stroke-linecap="round"/>
                            <line x1="22" y1="22" x2="36" y2="22" stroke="var(--color-primary)" stroke-width="2" stroke-linecap="round"/>
                            <line x1="22" y1="28" x2="30" y2="28" stroke="var(--color-primary)" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span v-else class="export-spinner"></span>
                    </div>
                    <span class="export-card__label">Copy SQL</span>
                    <span class="export-card__desc">{{ activeExport === 'copy' ? 'Copying…' : 'Copy to clipboard' }}</span>
                </button>

                <button class="export-card" :class="{ 'export-card--active': activeExport === 'sql' }" :disabled="isExporting" @click="downloadSql('sql')">
                    <div class="export-card__icon">
                        <svg v-if="activeExport !== 'sql'" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6h20l10 10v26a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z" fill="var(--bg-surface-alt)" stroke="var(--color-primary)" stroke-width="2"/>
                            <path d="M30 6v10h10" stroke="var(--color-primary)" stroke-width="2" stroke-linejoin="round"/>
                            <text x="24" y="35" text-anchor="middle" font-size="10" font-weight="700" font-family="monospace" fill="var(--color-primary)">.sql</text>
                        </svg>
                        <span v-else class="export-spinner"></span>
                    </div>
                    <span class="export-card__label">.sql</span>
                    <span class="export-card__desc">{{ activeExport === 'sql' ? 'Downloading…' : 'SQL script file' }}</span>
                </button>

                <button class="export-card" :class="{ 'export-card--active': activeExport === 'json' }" :disabled="isExporting" @click="downloadJson">
                    <div class="export-card__icon">
                        <svg v-if="activeExport !== 'json'" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6h20l10 10v26a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z" fill="var(--bg-surface-alt)" stroke="var(--border-color)" stroke-width="2"/>
                            <path d="M30 6v10h10" stroke="var(--border-color)" stroke-width="2" stroke-linejoin="round"/>
                            <text x="24" y="35" text-anchor="middle" font-size="9" font-weight="700" font-family="monospace" fill="var(--text-secondary)">.json</text>
                        </svg>
                        <span v-else class="export-spinner"></span>
                    </div>
                    <span class="export-card__label">.json</span>
                    <span class="export-card__desc">{{ activeExport === 'json' ? 'Downloading…' : 'Diagram JSON backup' }}</span>
                </button>

                <button class="export-card export-card--png" :class="{ 'export-card--active': activeExport === 'png' }" :disabled="isExporting" @click="capturePng">
                    <div class="export-card__icon">
                        <svg v-if="activeExport !== 'png'" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="4" y="8" width="40" height="32" rx="4" fill="var(--bg-surface-alt)" stroke="var(--color-primary)" stroke-width="2"/>
                            <circle cx="16" cy="19" r="4" fill="var(--color-primary)" opacity="0.7"/>
                            <path d="M4 34l10-10 8 8 6-6 16 12" stroke="var(--color-primary)" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
                            <text x="24" y="44" text-anchor="middle" font-size="9" font-weight="700" font-family="monospace" fill="var(--color-primary)">.png</text>
                        </svg>
                        <span v-else class="export-spinner"></span>
                    </div>
                    <span class="export-card__label">.png</span>
                    <span class="export-card__desc">{{ activeExport === 'png' ? 'Capturing…' : 'Image snapshot' }}</span>
                </button>

                <button class="export-card export-card--laravel" :class="{ 'export-card--active': activeExport === 'laravel' }" :disabled="isExporting" @click="downloadLaravelMigrations">
                    <div class="export-card__icon">
                        <svg v-if="activeExport !== 'laravel'" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- ZIP archive body -->
                            <rect x="8" y="14" width="32" height="28" rx="3" fill="var(--bg-surface-alt)" stroke="#FF2D20" stroke-width="2"/>
                            <!-- ZIP top flap -->
                            <path d="M8 20h32" stroke="#FF2D20" stroke-width="2"/>
                            <!-- Zipper teeth -->
                            <rect x="21" y="8" width="6" height="5" rx="1" fill="#FF2D20"/>
                            <rect x="21" y="13" width="6" height="3" rx="1" fill="#FF2D20" opacity="0.5"/>
                            <!-- Laravel L inside -->
                            <text x="24" y="36" text-anchor="middle" font-size="11" font-weight="800" font-family="sans-serif" fill="#FF2D20">php</text>
                        </svg>
                        <span v-else class="export-spinner export-spinner--laravel"></span>
                    </div>
                    <span class="export-card__label">Laravel</span>
                    <span class="export-card__desc">{{ activeExport === 'laravel' ? 'Generating…' : 'Migration files (.zip)' }}</span>
                </button>
            </div>

            <div v-if="!reviewDismissed" class="review-section">
                <template v-if="!reviewSubmitted">
                    <div class="review-section__header">
                        <span class="review-section__label">Enjoying sql-designer.com?</span>
                        <button class="review-section__dismiss" @click="reviewDismissed = true" aria-label="Dismiss">
                            <img src="../../icons/close.svg" alt="Close">
                        </button>
                    </div>
                    <div class="review-section__stars" @mouseleave="hoveredStars = 0">
                        <button
                            v-for="n in 5"
                            :key="n"
                            class="star-btn"
                            @click="selectedStars = n"
                            @mouseenter="hoveredStars = n"
                            :aria-label="`${n} star`"
                        >
                            <svg viewBox="0 0 24 24" width="28" height="28">
                                <polygon
                                    points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"
                                    :fill="n <= (hoveredStars || selectedStars) ? 'var(--color-primary)' : 'none'"
                                    :stroke="n <= (hoveredStars || selectedStars) ? 'var(--color-primary)' : 'var(--border-color)'"
                                    stroke-width="1.5"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </button>
                    </div>
                    <div v-if="selectedStars > 0" class="review-section__message-wrap">
                        <textarea
                            v-model="reviewMessage"
                            class="review-section__textarea"
                            placeholder="Optional message..."
                            rows="2"
                            maxlength="1000"
                        ></textarea>
                    </div>
                    <div v-if="selectedStars > 0" class="review-section__actions">
                        <button
                            class="review-section__submit"
                            :disabled="reviewLoading"
                            @click="submitReview"
                        >{{ reviewLoading ? 'Sending…' : 'Submit' }}</button>
                        <button class="review-section__skip" @click="reviewDismissed = true">Maybe later</button>
                    </div>
                </template>
                <template v-else>
                    <div class="review-section__thanks">
                        Thank you for your feedback!
                    </div>
                </template>
            </div>
        </div>
    </div>
    <UpgradePrompt v-if="upgradeMessage" :message="upgradeMessage" @close="upgradeMessage = ''" />
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from '@/axios.js'
import { useToast } from 'vue-toast-notification'
import { Diagram } from '@/services/Diagram.js'
import UpgradePrompt from '@/components/UpgradePrompt.vue'
import { isPlanLimitError } from '@/services/Subscription.js'

const $toast = useToast()

const props = defineProps({
    filename:  { type: String, default: 'schema' },
    diagramId: { type: Number, default: null },
})

const isExporting  = ref(false)
const activeExport = ref(null)
const sqlCache     = ref(null)
const upgradeMessage = ref('')


const UTC_PLUS_3_OFFSET_MS = 3 * 60 * 60 * 1000

const msUntilUtcPlus3Reset = () => {
    const utcPlus3Now = Date.now() + UTC_PLUS_3_OFFSET_MS
    const utcPlus3Date = new Date(utcPlus3Now)
    const y = utcPlus3Date.getUTCFullYear()
    const m = utcPlus3Date.getUTCMonth()
    const d = utcPlus3Date.getUTCDate()
    const nextUtcPlus3Midnight = Date.UTC(y, m, d + 1)
    return nextUtcPlus3Midnight - utcPlus3Now
}

const exportLimit    = ref(null)
const exportCount    = ref(0)
const resetCountdown = ref('')
let quotaTimer = null

const updateCountdown = () => {
    const msUntilReset = msUntilUtcPlus3Reset()
    const hours   = Math.floor(msUntilReset / 3600000)
    const minutes = Math.floor((msUntilReset % 3600000) / 60000)
    resetCountdown.value = hours > 0 ? `${hours}h ${minutes}m` : `${minutes}m`
}

const refreshQuota = async () => {
    updateCountdown()
    try {
        const { data } = await axios.get('/api/plan-limits')
        exportLimit.value = data.export_limit
        exportCount.value = data.exports_used_today
    } catch {
        // silently skip — badge falls back to its previous state
    }
}

const reviewDismissed = ref(true)
const reviewSubmitted = ref(false)
const reviewLoading   = ref(false)
const selectedStars   = ref(0)
const hoveredStars    = ref(0)
const reviewMessage   = ref('')

onMounted(async () => {
    try {
        const { data } = await axios.get('/api/review')
        if (!data.reviewed) reviewDismissed.value = false
    } catch {
        // silently skip — keep hidden on error
    }

    await refreshQuota()
    quotaTimer = setInterval(refreshQuota, 30000)
})

onUnmounted(() => {
    if (quotaTimer) clearInterval(quotaTimer)
})

const submitReview = async () => {
    reviewLoading.value = true
    try {
        await axios.post('/api/review', {
            stars:   selectedStars.value,
            message: reviewMessage.value || null,
        })
        reviewSubmitted.value = true
    } catch {
        $toast.error('Failed to submit review')
    } finally {
        reviewLoading.value = false
    }
}

const withExporting = async (key, fn) => {
    isExporting.value  = true
    activeExport.value = key
    try {
        await fn()
        await refreshQuota()
    } catch (e) {
        if (isPlanLimitError(e)) {
            upgradeMessage.value = e.response?.data?.message ?? e.message
        } else {
            $toast.error(e?.message || 'Export failed')
        }
        console.error(e)
    } finally {
        isExporting.value  = false
        activeExport.value = null
    }
}

const generateSql = () => new Promise((resolve, reject) => {
    if (sqlCache.value) { resolve(sqlCache.value); return }
    Diagram.export(props.diagramId).then(result => {
        if (!result) { reject(new Error('Export failed')); return }
        if (result.status === 'done' && result.script) {
            sqlCache.value = result.script
            resolve(sqlCache.value)
            return
        }
        let attempts = 0
        const poll = setInterval(async () => {
            attempts++
            if (attempts > 150) {
                clearInterval(poll)
                reject(new Error('Export timed out'))
                return
            }
            const status = await Diagram.exportStatus(props.diagramId)
            if (!status) return
            if (status.status === 'done') {
                clearInterval(poll)
                sqlCache.value = status.script
                resolve(sqlCache.value)
            } else if (status.status === 'failed') {
                clearInterval(poll)
                reject(new Error(status.error || 'Export failed'))
            }
        }, 2000)
    }).catch(reject)
})

const copyText = () => withExporting('copy', async () => {
    const sql = await generateSql()
    await navigator.clipboard.writeText(sql)
    $toast.success('Copied to clipboard')
})

const downloadSql = (ext) => withExporting('sql', async () => {
    const sql = await generateSql()
    const blob = new Blob([sql], { type: 'text/plain' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `${props.filename}.${ext}`
    a.click()
    URL.revokeObjectURL(url)
})

const downloadJson = () => withExporting('json', async () => {
    const json = await Diagram.exportJson(props.diagramId)
    if (!json) { $toast.error('Failed to export JSON'); return }
    const blob = new Blob([json], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `${props.filename}.json`
    a.click()
    URL.revokeObjectURL(url)
})

const emit = defineEmits(['close', 'capture-png'])

const capturePng = () => withExporting('png', async () => {
    await new Promise((resolve, reject) => {
        emit('capture-png', { resolve, reject })
    })
})

const downloadLaravelMigrations = () => withExporting('laravel', async () => {
    const blob = await Diagram.exportMigration(props.diagramId)
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `${props.filename}_migrations.zip`
    a.click()
    URL.revokeObjectURL(url)
})
</script>

<style scoped>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.55);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.export-modal {
    background: var(--bg-surface);
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    width: 660px;
    max-width: calc(100vw - 2rem);
    overflow: hidden;
}

.export-modal__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    background: var(--color-primary);
}

.export-modal__header-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.export-modal__title {
    font-size: 14px;
    font-weight: 600;
    color: white;
    letter-spacing: 0.8px;
    text-transform: uppercase;
}

.export-modal__count-badge {
    font-family: 'Consolas', 'Monaco', monospace;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.04em;
    color: white;
    background: rgba(255, 255, 255, 0.18);
    border-radius: 999px;
    padding: 2px 10px;
}

.export-modal__quota-row {
    padding: 6px 24px;
    background: var(--bg-surface-alt);
    border-bottom: 1px solid var(--border-color);
    font-size: 11px;
    color: var(--text-secondary);
    text-align: center;
}

.export-modal__quota-tz {
    color: var(--text-muted);
}

.export-modal__close {
    width: 26px;
    height: 26px;
    padding: 4px;
    border: none;
    background: rgba(255, 255, 255, 0.15);
    cursor: pointer;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.15s;
}

.export-modal__close:hover {
    background-color: rgba(255, 255, 255, 0.3);
}

.export-modal__close img {
    width: 14px;
    height: 14px;
    filter: brightness(0) invert(1);
}

.export-modal__status {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 8px 24px;
    background: var(--bg-surface-alt);
    border-bottom: 1px solid var(--border-color);
}

.export-modal__status-text {
    font-size: 11px;
    color: var(--text-secondary);
}

.export-modal__status-spinner {
    display: block;
    width: 12px;
    height: 12px;
    border: 1.5px solid var(--border-color);
    border-top-color: var(--color-primary);
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    flex-shrink: 0;
}

.export-modal__body {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
    padding: 24px;
}

.export-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 20px 8px 16px;
    background: var(--bg-surface-alt);
    border: 1px solid var(--border-color);
    border-radius: 10px;
    cursor: pointer;
    transition: border-color 0.15s, background 0.15s, transform 0.1s;
    text-align: center;
}

.export-card:hover {
    border-color: var(--color-primary);
    background: var(--bg-surface);
    transform: translateY(-2px);
}

.export-card--laravel:hover {
    border-color: #FF2D20;
}

.export-card--png:hover {
    border-color: var(--color-primary);
}

.export-card__icon {
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.export-card__icon svg {
    width: 100%;
    height: 100%;
}

.export-card__label {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-primary);
    font-family: 'Consolas', 'Monaco', monospace;
}

.export-card--laravel .export-card__label {
    color: #FF2D20;
}

.export-card__desc {
    font-size: 10px;
    color: var(--text-secondary);
    line-height: 1.3;
}

.export-card:disabled {
    cursor: not-allowed;
    transform: none;
    opacity: 0.45;
}

.export-card:disabled:hover {
    border-color: var(--border-color);
    background: var(--bg-surface-alt);
    transform: none;
}

.export-card--active,
.export-card--active:disabled {
    opacity: 1;
    border-color: var(--color-primary);
    background: var(--bg-surface);
}

.export-card--laravel.export-card--active,
.export-card--laravel.export-card--active:disabled {
    border-color: #FF2D20;
}

.export-spinner {
    display: block;
    width: 28px;
    height: 28px;
    border: 2.5px solid var(--border-color);
    border-top-color: var(--color-primary);
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

.export-spinner--laravel {
    border-top-color: #FF2D20;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Review section */
.review-section {
    border-top: 1px solid var(--border-color);
    padding: 16px 24px 18px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.review-section__header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 12px;
    width: 100%;
}

.review-section__label {
    font-size: 12px;
    color: var(--text-secondary);
    font-weight: 500;
}

.review-section__dismiss {
    width: 22px;
    height: 22px;
    padding: 3px;
    border: none;
    background: transparent;
    cursor: pointer;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.5;
    transition: opacity 0.15s, background 0.15s;
}

.review-section__dismiss:hover {
    opacity: 1;
    background: var(--bg-surface-alt);
}

.review-section__dismiss img {
    width: 12px;
    height: 12px;
    filter: brightness(0) invert(1);
}

.review-section__stars {
    display: flex;
    gap: 4px;
    margin-bottom: 4px;
}

.star-btn {
    background: none;
    border: none;
    padding: 2px;
    cursor: pointer;
    border-radius: 4px;
    display: flex;
    transition: transform 0.1s;
}

.star-btn:hover {
    transform: scale(1.15);
}

.review-section__message-wrap {
    margin-top: 10px;
    width: 100%;
}

.review-section__textarea {
    width: 100%;
    background: var(--bg-surface-alt);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    color: var(--text-primary);
    font-size: 12px;
    padding: 8px 10px;
    resize: none;
    box-sizing: border-box;
    transition: border-color 0.15s;
    font-family: inherit;
}

.review-section__textarea:focus {
    outline: none;
    border-color: var(--color-primary);
}

.review-section__actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    margin-top: 10px;
}

.review-section__submit {
    padding: 6px 18px;
    background: var(--color-primary);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.15s;
}

.review-section__submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.review-section__submit:not(:disabled):hover {
    opacity: 0.85;
}

.review-section__skip {
    background: none;
    border: none;
    color: var(--text-secondary);
    font-size: 11px;
    cursor: pointer;
    padding: 0;
    text-decoration: underline;
    text-underline-offset: 2px;
}

.review-section__skip:hover {
    color: var(--text-primary);
}

.review-section__thanks {
    font-size: 13px;
    color: var(--color-primary);
    font-weight: 500;
    text-align: center;
    padding: 4px 0;
}
</style>
