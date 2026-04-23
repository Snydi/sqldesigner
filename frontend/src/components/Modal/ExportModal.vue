<template>
    <div class="modal flex-centered" @click.self="$emit('close')">
        <div class="export-modal">
            <div class="export-modal__header">
                <span class="export-modal__title">Export</span>
                <button class="export-modal__close" @click="$emit('close')">
                    <img src="../../icons/close.svg" alt="Close">
                </button>
            </div>
            <div class="export-modal__body">
                <button class="export-card" @click="copyText">
                    <div class="export-card__icon">
                        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="6" y="10" width="26" height="32" rx="3" fill="var(--bg-surface-alt)" stroke="var(--border-color)" stroke-width="2"/>
                            <rect x="16" y="6" width="26" height="32" rx="3" fill="var(--bg-surface)" stroke="var(--color-primary)" stroke-width="2"/>
                            <line x1="22" y1="16" x2="36" y2="16" stroke="var(--color-primary)" stroke-width="2" stroke-linecap="round"/>
                            <line x1="22" y1="22" x2="36" y2="22" stroke="var(--color-primary)" stroke-width="2" stroke-linecap="round"/>
                            <line x1="22" y1="28" x2="30" y2="28" stroke="var(--color-primary)" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span class="export-card__label">Copy SQL</span>
                    <span class="export-card__desc">Copy to clipboard</span>
                </button>

                <button class="export-card" @click="downloadSql('sql')">
                    <div class="export-card__icon">
                        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6h20l10 10v26a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z" fill="var(--bg-surface-alt)" stroke="var(--color-primary)" stroke-width="2"/>
                            <path d="M30 6v10h10" stroke="var(--color-primary)" stroke-width="2" stroke-linejoin="round"/>
                            <text x="24" y="35" text-anchor="middle" font-size="10" font-weight="700" font-family="monospace" fill="var(--color-primary)">.sql</text>
                        </svg>
                    </div>
                    <span class="export-card__label">.sql</span>
                    <span class="export-card__desc">SQL script file</span>
                </button>

                <button class="export-card" @click="downloadJson">
                    <div class="export-card__icon">
                        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 6h20l10 10v26a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z" fill="var(--bg-surface-alt)" stroke="var(--border-color)" stroke-width="2"/>
                            <path d="M30 6v10h10" stroke="var(--border-color)" stroke-width="2" stroke-linejoin="round"/>
                            <text x="24" y="35" text-anchor="middle" font-size="9" font-weight="700" font-family="monospace" fill="var(--text-secondary)">.json</text>
                        </svg>
                    </div>
                    <span class="export-card__label">.json</span>
                    <span class="export-card__desc">Diagram JSON backup</span>
                </button>

                <button class="export-card export-card--laravel" @click="downloadLaravelMigrations">
                    <div class="export-card__icon">
                        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    </div>
                    <span class="export-card__label">Laravel</span>
                    <span class="export-card__desc">Migration files (.zip)</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useToast } from 'vue-toast-notification'
import { Diagram } from '@/services/Diagram.js'

const $toast = useToast()

const props = defineProps({
    sqlContent:  { type: String, default: '' },
    jsonContent: { type: String, default: null },
    filename:    { type: String, default: 'schema' },
    diagramId:   { type: Number, default: null },
})

defineEmits(['close'])

const copyText = async () => {
    await navigator.clipboard.writeText(props.sqlContent)
    $toast.success('Copied to clipboard')
}

const downloadSql = (ext) => {
    const blob = new Blob([props.sqlContent], { type: 'text/plain' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `${props.filename}.${ext}`
    a.click()
    URL.revokeObjectURL(url)
}

const downloadJson = () => {
    const blob = new Blob([props.jsonContent], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `${props.filename}.json`
    a.click()
    URL.revokeObjectURL(url)
}

const downloadLaravelMigrations = async () => {
    try {
        const blob = await Diagram.exportMigration(props.diagramId)
        const url = URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = url
        a.download = `${props.filename}_migrations.zip`
        a.click()
        URL.revokeObjectURL(url)
    } catch (e) {
        $toast.error('Failed to generate migrations')
        console.error(e)
    }
}
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
    width: 540px;
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

.export-modal__title {
    font-size: 14px;
    font-weight: 600;
    color: white;
    letter-spacing: 0.8px;
    text-transform: uppercase;
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

.export-modal__body {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
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
</style>
