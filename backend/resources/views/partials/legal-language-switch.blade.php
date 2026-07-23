<style>
    [data-legal-language-content][hidden] { display: none; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var sections = document.querySelectorAll('[data-legal-language-content]');

        function setLanguage(language) {
            sections.forEach(function (section) {
                section.hidden = section.dataset.legalLanguageContent !== language;
            });
        }

        var savedLanguage = null;
        try {
            savedLanguage = localStorage.getItem('site-language') || localStorage.getItem('legal-language');
        } catch (error) {}
        var initialLanguage = savedLanguage === 'ru' || savedLanguage === 'en'
            ? savedLanguage
            : (navigator.language.toLowerCase().startsWith('ru') ? 'ru' : 'en');

        window.addEventListener('site-language-change', function (event) {
            setLanguage(event.detail.language);
        });
        setLanguage(initialLanguage);
    });
</script>
