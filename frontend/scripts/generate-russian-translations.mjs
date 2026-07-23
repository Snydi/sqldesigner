import fs from 'node:fs/promises'
import path from 'node:path'
import { fileURLToPath } from 'node:url'

const scriptDirectory = path.dirname(fileURLToPath(import.meta.url))
const projectRoot = path.resolve(scriptDirectory, '..', '..')
const frontendRoot = path.join(projectRoot, 'frontend')
const outputPath = path.join(frontendRoot, 'src', 'locales', 'ru.json')
const appUrl = process.env.APP_URL || 'http://nginx'

const publicRoutes = [
    '/',
    '/about',
    '/features',
    '/library',
    '/pricing',
    '/sitemap',
    '/blog',
]

const manualTranslations = {
    '$0 USD': '0 ₽',
    '$10 USD': '780 ₽',
    '$10 USD / month': '780 ₽ / месяц',
    '$10 USD/month': '780 ₽/месяц',
    '/ forever': '/ навсегда',
    '/ month': '/ месяц',
    'Free covers 1 diagram and 3 exports a day. Pro removes both limits for $10 USD/month.':
        'Бесплатный план включает 1 диаграмму и 3 экспорта в день. Pro снимает оба ограничения за 780 ₽/месяц.',
    '$10 USD/month, billed automatically. Cancel anytime; Pro stays active until the end of the current billing period.':
        '780 ₽/месяц, автоматическое продление. Отменить можно в любое время; Pro останется активным до конца текущего расчётного периода.',
    'Get Pro — $10 USD/month': 'Получить Pro — 780 ₽/месяц',
    'Pro is $10 USD per month and renews automatically until you cancel.':
        'Pro стоит 780 ₽ в месяц и автоматически продлевается, пока вы не отмените подписку.',
    'SQL Designer Pro costs $10 USD per month and removes the diagram and export limits entirely.':
        'SQL Designer Pro стоит 780 ₽ в месяц и полностью снимает ограничения на диаграммы и экспорт.',
    'Start Pro — $10 USD/month': 'Подключить Pro — 780 ₽/месяц',
}

const decodeEntities = (value) => value
    .replaceAll('&amp;', '&')
    .replaceAll('&quot;', '"')
    .replaceAll('&#039;', "'")
    .replaceAll('&#39;', "'")
    .replaceAll('&apos;', "'")
    .replaceAll('&lt;', '<')
    .replaceAll('&gt;', '>')
    .replaceAll('&nbsp;', ' ')
    .replace(/&#(\d+);/g, (_, code) => String.fromCodePoint(Number(code)))

const normalize = (value) => decodeEntities(value).replace(/\s+/g, ' ').trim()

function isTranslatable(value) {
    if (value.length < 2 || value.length > 1200 || !/[A-Za-z]/.test(value)) return false
    if (/^(https?:|mailto:|\/|#|[a-z]+:)/i.test(value)) return false
    if (/^[\w.-]+\.(com|io|js|vue|css|json|php|sql)$/i.test(value)) return false
    if (/[{}[\]<>]|=>|===|!==|&&|\|\||\b(const|let|var|function|return|import|from)\b/.test(value)) return false
    if (/^[A-Z0-9_(),.*\s+-]+$/.test(value) && value.length > 25) return false
    return true
}

function collectMarkupStrings(markup, strings) {
    const clean = markup
        .replace(/<!--[\s\S]*?-->/g, ' ')
        .replace(/<(script|style|svg|noscript|template)\b[\s\S]*?<\/\1>/gi, ' ')

    for (const match of clean.matchAll(/>([^<]+)</g)) {
        const value = normalize(match[1])
        if (isTranslatable(value) && !value.includes('{{') && !value.includes('@')) strings.add(value)
    }

    for (const match of clean.matchAll(/\b(?:aria-label|placeholder|title|alt)="([^"]+)"/gi)) {
        const value = normalize(match[1])
        if (isTranslatable(value) && !value.includes('{{')) strings.add(value)
    }
}

function collectQuotedStrings(source, strings) {
    for (const match of source.matchAll(/'([^'\r\n]{2,500})'|"([^"\r\n]{2,500})"|`([^`\r\n]{2,500})`/g)) {
        const value = normalize(match[1] || match[2] || match[3])
        if (isTranslatable(value) && /\s/.test(value) && !value.includes('${')) strings.add(value)
    }
}

async function walk(directory, extension) {
    const entries = await fs.readdir(directory, { withFileTypes: true })
    const files = []
    for (const entry of entries) {
        const entryPath = path.join(directory, entry.name)
        if (entry.isDirectory()) files.push(...await walk(entryPath, extension))
        else if (entryPath.endsWith(extension)) files.push(entryPath)
    }
    return files
}

async function pathExists(targetPath) {
    try {
        await fs.access(targetPath)
        return true
    } catch {
        return false
    }
}

async function collectStrings() {
    const strings = new Set()

    for (const route of publicRoutes) {
        const response = await fetch(`${appUrl}${route}`)
        if (!response.ok) throw new Error(`Unable to read ${route}: HTTP ${response.status}`)
        collectMarkupStrings(await response.text(), strings)
    }

    const vueFiles = await walk(path.join(frontendRoot, 'src', 'components'), '.vue')
    for (const file of vueFiles) {
        const source = await fs.readFile(file, 'utf8')
        const template = source.match(/<template>([\s\S]*?)<\/template>/)?.[1] ?? ''
        collectMarkupStrings(template, strings)
        collectQuotedStrings(template, strings)

        const script = source.match(/<script[^>]*>([\s\S]*?)<\/script>/)?.[1] ?? ''
        collectQuotedStrings(script, strings)
    }

    const javascriptFiles = await walk(path.join(frontendRoot, 'src'), '.js')
    for (const file of javascriptFiles) {
        if (file.endsWith(path.join('js', 'site-language.js'))) continue
        const source = await fs.readFile(file, 'utf8')
        collectQuotedStrings(source, strings)
    }

    const backendAppPath = path.join(projectRoot, 'backend', 'app')
    if (await pathExists(backendAppPath)) {
        const backendFiles = await walk(backendAppPath, '.php')
        for (const file of backendFiles) {
            collectQuotedStrings(await fs.readFile(file, 'utf8'), strings)
        }
    }

    return [...strings].sort((left, right) => left.localeCompare(right))
}

async function translateBatch(values) {
    const params = new URLSearchParams({
        client: 'gtx',
        sl: 'en',
        tl: 'ru',
        dt: 't',
        q: values.join('\n'),
    })
    const response = await fetch(`https://translate.googleapis.com/translate_a/single?${params}`)
    if (!response.ok) throw new Error(`Translation request failed: HTTP ${response.status}`)
    const payload = await response.json()
    const translated = payload[0].map((part) => part[0]).join('').trimEnd().split('\n')

    if (translated.length !== values.length) {
        throw new Error(`Translation count mismatch: expected ${values.length}, received ${translated.length}`)
    }
    return translated
}

async function translateAll(values) {
    const result = {}
    let batch = []
    let batchLength = 0

    async function flush() {
        if (!batch.length) return
        const translated = await translateBatch(batch)
        batch.forEach((source, index) => {
            result[source] = translated[index]
        })
        batch = []
        batchLength = 0
    }

    for (const value of values) {
        if (batch.length >= 35 || batchLength + value.length > 4200) await flush()
        batch.push(value)
        batchLength += value.length + 1
    }
    await flush()
    return result
}

const strings = await collectStrings()
let existingTranslations = {}
try {
    existingTranslations = JSON.parse(await fs.readFile(outputPath, 'utf8'))
} catch {}
const translations = {
    ...existingTranslations,
    ...await translateAll(strings),
}
Object.assign(translations, manualTranslations)
await fs.mkdir(path.dirname(outputPath), { recursive: true })
await fs.writeFile(outputPath, `${JSON.stringify(translations, null, 2)}\n`)
console.log(`Generated ${strings.length} Russian translations in ${outputPath}`)
