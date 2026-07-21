<div class="legal-language-switch" role="group" aria-label="Language / Язык">
    <button type="button" data-legal-language="en" aria-pressed="true">English</button>
    <button type="button" data-legal-language="ru" aria-pressed="false">Русский</button>
</div>

<style>
    .legal-language-switch {
        display: inline-flex;
        gap: 0.25rem;
        padding: 0.25rem;
        margin-bottom: 1.75rem;
        border: 1px solid var(--border-light);
        border-radius: 0.5rem;
        background: var(--bg-surface);
    }
    .legal-language-switch button {
        border: 0;
        border-radius: 0.35rem;
        padding: 0.45rem 0.75rem;
        color: var(--text-secondary);
        background: transparent;
        font: inherit;
        font-size: 0.82rem;
        cursor: pointer;
    }
    .legal-language-switch button[aria-pressed="true"] {
        color: var(--text-primary);
        background: var(--bg-surface-alt);
    }
    [data-legal-language-content][hidden] { display: none; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var root = document.querySelector('[data-legal-page]');
        if (!root) return;

        var buttons = root.querySelectorAll('[data-legal-language]');
        var sections = root.querySelectorAll('[data-legal-language-content]');

        function setLanguage(language) {
            sections.forEach(function (section) {
                section.hidden = section.dataset.legalLanguageContent !== language;
            });
            buttons.forEach(function (button) {
                button.setAttribute('aria-pressed', String(button.dataset.legalLanguage === language));
            });
            try { localStorage.setItem('legal-language', language); } catch (error) {}
        }

        var savedLanguage = null;
        try { savedLanguage = localStorage.getItem('legal-language'); } catch (error) {}
        var initialLanguage = savedLanguage === 'ru' || savedLanguage === 'en'
            ? savedLanguage
            : (navigator.language.toLowerCase().startsWith('ru') ? 'ru' : 'en');

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                setLanguage(button.dataset.legalLanguage);
            });
        });
        setLanguage(initialLanguage);
    });
</script>
