@extends('layouts.main')

@section('title', 'Privacy Policy for the SQL Designer Online Platform')

@section('head')
<meta name="description" content="Read the SQL Designer Privacy Policy to learn what account, diagram, and usage data we collect, how we use it, and how to request deletion.">
<link rel="canonical" href="https://sql-designer.com/privacy">
<script type="application/ld+json">
@verbatim
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Privacy Policy",
  "url": "https://sql-designer.com/privacy",
  "description": "Privacy Policy for SQL Designer"
}
@endverbatim
</script>
@endsection

@section('content')
<style>
    .legal-wrap {
        max-width: 760px;
        margin: 0 auto;
        padding: 3rem var(--gutter) 4rem;
    }
    .legal-wrap h1 {
        font-size: clamp(1.6rem, 4vw, 2.2rem);
        font-weight: 700;
        margin: 0 0 0.4rem;
        color: var(--text-primary);
    }
    .legal-meta {
        font-size: 16px;
        color: var(--text-muted);
        margin-bottom: 2.5rem;
    }
    .legal-wrap h2 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 2rem 0 0.5rem;
    }
    .legal-wrap p, .legal-wrap li {
        font-size: 16px;
        color: var(--text-secondary);
        line-height: 1.75;
    }
    .legal-wrap ul {
        padding-left: 1.25rem;
        margin: 0.4rem 0 0.8rem;
    }
    .legal-wrap a {
        color: var(--color-primary-text);
        text-decoration: underline;
        text-underline-offset: 3px;
    }
    .legal-wrap a:hover { color: #6dc290; }
    hr.legal { border: none; border-top: 1px solid var(--border-light); margin: 2rem 0; }
</style>

<div class="legal-wrap" data-legal-page>
    @include('partials.legal-language-switch')

    <section data-legal-language-content="en" lang="en">
    <h1>Privacy Policy</h1>
    <p class="legal-meta">Last updated: July 27, 2026</p>

    <p>SQL Designer ("we", "us", or "our") operates <strong>sql-designer.com</strong>. This Privacy Policy explains what information we collect, how we use it, and the choices you have.</p>

    <h2>1. Information We Collect</h2>
    <ul>
        <li><strong>Account data:</strong> When you register, we collect your email address and, if you sign in via OAuth (Google, GitHub, GitLab), your name and profile picture from that provider.</li>
        <li><strong>Diagram data:</strong> The database schemas you create and save are stored on our servers and associated with your account.</li>
        <li><strong>Usage data:</strong> We collect anonymised analytics (page views, feature usage) via Google Analytics to understand how the product is used.</li>
        <li><strong>Local storage:</strong> Your authentication token is stored in your browser's <code>localStorage</code> so you stay logged in across sessions.</li>
        <li><strong>Billing and consent data:</strong> We store subscription and payment identifiers, amounts, currencies, statuses, consent text and version, acceptance time, IP address, and user-agent. Card details are processed by Robokassa and are not stored on sql-designer.com.</li>
    </ul>

    <h2>2. How We Use Your Information</h2>
    <ul>
        <li>To provide and improve the SQL Designer service.</li>
        <li>To send transactional emails (email verification, account-related notices).</li>
        <li>To analyse product usage and fix bugs.</li>
        <li>We do <strong>not</strong> sell your personal data to third parties.</li>
    </ul>

    <h2>3. Shared Diagrams</h2>
    <p>If you choose to share a diagram via a public share link, anyone with that link can view it. You can revoke access at any time from within the diagram editor.</p>

    <h2>4. Data Retention</h2>
    <p>We retain your account and diagram data for as long as your account is active. You may request deletion of your account and all associated data by emailing us at <a href="mailto:dmitriy@sql-designer.com">dmitriy@sql-designer.com</a>.</p>

    <h2>5. Third-Party Services</h2>
    <ul>
        <li><strong>Google Analytics:</strong> Anonymised usage tracking. See <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">Google's Privacy Policy</a>.</li>
        <li><strong>OAuth providers:</strong> Google, GitHub, and GitLab if you choose to sign in with them.</li>
        <li><strong>Robokassa:</strong> Payment processing, recurring charges, and refunds for SQL Designer Pro.</li>
    </ul>

    <h2>6. Cookies</h2>
    <p>We do not set tracking cookies ourselves. Google Analytics may set its own cookies subject to Google's policies. We use <code>localStorage</code> (not cookies) for authentication.</p>

    <h2>7. Security</h2>
    <p>We take reasonable technical measures to protect your data. However, no method of transmission over the internet is 100% secure.</p>

    <h2>8. Children</h2>
    <p>SQL Designer is not directed at children under 13. We do not knowingly collect personal data from children.</p>

    <h2>9. Changes to This Policy</h2>
    <p>We may update this policy from time to time. The "Last updated" date at the top will reflect any changes. Continued use of the service after changes constitutes acceptance.</p>

    <h2>10. Contact</h2>
    <p>Questions about this policy? Email us at <a href="mailto:dmitriy@sql-designer.com">dmitriy@sql-designer.com</a>.</p>

    <hr class="legal">
    <p><a href="/terms">View our Terms of Service</a></p>
    </section>

    <section data-legal-language-content="ru" lang="ru" hidden>
    <h1>Политика конфиденциальности</h1>
    <p class="legal-meta">Последнее обновление: 27 июля 2026 г.</p>

    <p>SQL Designer («мы», «нас» или «наш») управляет сайтом <strong>sql-designer.com</strong>. Настоящая Политика конфиденциальности объясняет, какую информацию мы собираем, как используем ее и какие возможности выбора у вас есть.</p>

    <h2>1. Информация, которую мы собираем</h2>
    <ul>
        <li><strong>Данные учетной записи:</strong> при регистрации мы собираем ваш адрес электронной почты, а при входе через OAuth (Google, GitHub или GitLab) — имя и изображение профиля, полученные от соответствующего сервиса.</li>
        <li><strong>Данные диаграмм:</strong> созданные и сохраненные вами схемы баз данных хранятся на наших серверах и связаны с вашей учетной записью.</li>
        <li><strong>Данные об использовании:</strong> с помощью Google Analytics мы собираем обезличенную аналитику (просмотры страниц и использование функций), чтобы понимать, как используется продукт.</li>
        <li><strong>Локальное хранилище:</strong> токен аутентификации хранится в <code>localStorage</code> вашего браузера, чтобы вы оставались авторизованы между сеансами.</li>
        <li><strong>Данные оплаты и согласий:</strong> мы храним идентификаторы подписки и платежей, суммы, валюты, статусы, текст и версию согласия, дату и время принятия, IP-адрес и user-agent. Реквизиты карты обрабатывает Robokassa; на sql-designer.com они не сохраняются.</li>
    </ul>

    <h2>2. Как мы используем вашу информацию</h2>
    <ul>
        <li>Для предоставления и улучшения Сервиса SQL Designer.</li>
        <li>Для отправки служебных писем, включая подтверждение электронной почты и уведомления об учетной записи.</li>
        <li>Для анализа использования продукта и исправления ошибок.</li>
        <li>Мы <strong>не продаем</strong> ваши персональные данные третьим лицам.</li>
    </ul>

    <h2>3. Общие диаграммы</h2>
    <p>Если вы предоставляете доступ к диаграмме по публичной ссылке, ее сможет просмотреть любой, у кого есть эта ссылка. Вы можете отозвать доступ в любое время в редакторе диаграмм.</p>

    <h2>4. Срок хранения данных</h2>
    <p>Мы храним данные вашей учетной записи и диаграмм, пока учетная запись активна. Вы можете запросить удаление учетной записи и всех связанных с ней данных по адресу <a href="mailto:dmitriy@sql-designer.com">dmitriy@sql-designer.com</a>.</p>

    <h2>5. Сторонние сервисы</h2>
    <ul>
        <li><strong>Google Analytics:</strong> обезличенная аналитика использования. См. <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">Политику конфиденциальности Google</a>.</li>
        <li><strong>Провайдеры OAuth:</strong> Google, GitHub и GitLab, если вы выбираете вход через один из этих сервисов.</li>
        <li><strong>Robokassa:</strong> обработка платежей, автоматических списаний и возвратов по подписке SQL Designer Pro.</li>
    </ul>

    <h2>6. Файлы cookie</h2>
    <p>Мы самостоятельно не устанавливаем отслеживающие файлы cookie. Google Analytics может устанавливать собственные файлы cookie в соответствии с политиками Google. Для аутентификации мы используем <code>localStorage</code>, а не файлы cookie.</p>

    <h2>7. Безопасность</h2>
    <p>Мы принимаем разумные технические меры для защиты ваших данных. Однако ни один способ передачи данных через интернет не обеспечивает стопроцентную безопасность.</p>

    <h2>8. Дети</h2>
    <p>SQL Designer не предназначен для детей младше 13 лет. Мы сознательно не собираем персональные данные детей.</p>

    <h2>9. Изменения Политики</h2>
    <p>Мы можем периодически обновлять настоящую Политику. Дата в строке «Последнее обновление» будет отражать внесенные изменения. Продолжение использования Сервиса после изменений означает принятие обновленной Политики.</p>

    <h2>10. Контакты</h2>
    <p>По вопросам о настоящей Политике напишите нам: <a href="mailto:dmitriy@sql-designer.com">dmitriy@sql-designer.com</a>.</p>

    <hr class="legal">
    <p><a href="/personal-data-consent">Ознакомиться с Согласием на обработку персональных данных</a></p>
    </section>
</div>
@endsection
