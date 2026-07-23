import russianTranslations from '@/locales/ru.json'

const LANGUAGE_KEY = 'site-language'
const TRANSLATABLE_ATTRIBUTES = ['aria-label', 'placeholder', 'title', 'alt']
const SKIPPED_SELECTOR = [
    '[data-no-translate]',
    '[data-legal-language-content]',
    '.notranslate',
    '.vue-flow__node',
    'code',
    'pre',
    'kbd',
    'samp',
    'script',
    'style',
    'svg',
    'textarea',
    '[contenteditable="true"]',
].join(',')

const originalText = new WeakMap()
const originalAttributes = new WeakMap()
let currentLanguage = 'en'
let observer = null

function getSavedLanguage() {
    let savedLanguage = null
    try {
        savedLanguage = localStorage.getItem(LANGUAGE_KEY) || localStorage.getItem('legal-language')
    } catch {}

    if (savedLanguage === 'en' || savedLanguage === 'ru') return savedLanguage
    return navigator.language.toLowerCase().startsWith('ru') ? 'ru' : 'en'
}

function isSkipped(node) {
    const element = node.nodeType === Node.ELEMENT_NODE ? node : node.parentElement
    return !element || Boolean(element.closest(SKIPPED_SELECTOR))
}

function translateValue(value) {
    const leadingWhitespace = value.match(/^\s*/)?.[0] ?? ''
    const trailingWhitespace = value.match(/\s*$/)?.[0] ?? ''
    const normalized = value.trim().replace(/\s+/g, ' ')
    const translated = russianTranslations[normalized]
    return translated ? `${leadingWhitespace}${translated}${trailingWhitespace}` : value
}

function translateTextNode(node) {
    if (isSkipped(node) || !node.nodeValue?.trim()) return
    if (!originalText.has(node)) originalText.set(node, node.nodeValue)

    const source = originalText.get(node)
    const nextValue = currentLanguage === 'ru' ? translateValue(source) : source
    if (node.nodeValue !== nextValue) node.nodeValue = nextValue
}

function translateElementAttributes(element) {
    if (isSkipped(element)) return

    let originals = originalAttributes.get(element)
    if (!originals) {
        originals = new Map()
        originalAttributes.set(element, originals)
    }

    for (const attribute of TRANSLATABLE_ATTRIBUTES) {
        if (!element.hasAttribute(attribute)) continue
        if (!originals.has(attribute)) originals.set(attribute, element.getAttribute(attribute))

        const source = originals.get(attribute)
        const nextValue = currentLanguage === 'ru' ? translateValue(source) : source
        if (element.getAttribute(attribute) !== nextValue) element.setAttribute(attribute, nextValue)
    }
}

function handleTextMutation(node) {
    const source = originalText.get(node)
    const expected = source === undefined
        ? undefined
        : (currentLanguage === 'ru' ? translateValue(source) : source)

    if (source === undefined || node.nodeValue !== expected) {
        originalText.set(node, node.nodeValue)
    }
    translateTextNode(node)
}

function handleAttributeMutation(element, attribute) {
    let originals = originalAttributes.get(element)
    const currentValue = element.getAttribute(attribute)
    if (currentValue === null) {
        originals?.delete(attribute)
        return
    }

    const source = originals?.get(attribute)
    const expected = source === undefined
        ? undefined
        : (currentLanguage === 'ru' ? translateValue(source) : source)

    if (source === undefined || currentValue !== expected) {
        if (!originals) {
            originals = new Map()
            originalAttributes.set(element, originals)
        }
        originals.set(attribute, currentValue)
    }
    translateElementAttributes(element)
}

function translateTree(root) {
    if (root.nodeType === Node.TEXT_NODE) {
        translateTextNode(root)
        return
    }
    if (root.nodeType !== Node.ELEMENT_NODE || isSkipped(root)) return

    translateElementAttributes(root)
    const walker = document.createTreeWalker(root, NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_TEXT)
    let node = walker.nextNode()
    while (node) {
        if (node.nodeType === Node.TEXT_NODE) translateTextNode(node)
        else translateElementAttributes(node)
        node = walker.nextNode()
    }
}

function translateDocumentTitle() {
    if (!originalText.has(document)) originalText.set(document, document.title)
    const source = originalText.get(document)
    const isBlogArticle = window.location.pathname.startsWith('/blog/')
    document.title = currentLanguage === 'ru' && !isBlogArticle
        ? (russianTranslations[source] || source)
        : source
}

function applyLanguage(language) {
    currentLanguage = language === 'ru' ? 'ru' : 'en'
    document.documentElement.lang = currentLanguage
    document.documentElement.dataset.language = currentLanguage
    translateDocumentTitle()
    translateTree(document.body)
}

export function translateForCurrentLanguage(value) {
    return currentLanguage === 'ru' ? translateValue(value).trim() : value
}

export function setLocalizedDocumentTitle(value) {
    originalText.set(document, value)
    translateDocumentTitle()
}

function startObserver() {
    if (observer) return
    observer = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(translateTree)
            } else if (mutation.type === 'characterData') {
                handleTextMutation(mutation.target)
            } else if (mutation.type === 'attributes') {
                handleAttributeMutation(mutation.target, mutation.attributeName)
            }
        }
    })
    observer.observe(document.body, {
        subtree: true,
        childList: true,
        characterData: true,
        attributes: true,
        attributeFilter: TRANSLATABLE_ATTRIBUTES,
    })
}

function initialize() {
    applyLanguage(getSavedLanguage())
    startObserver()
}

window.addEventListener('site-language-change', (event) => {
    applyLanguage(event.detail?.language)
})

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialize, { once: true })
} else {
    initialize()
}
