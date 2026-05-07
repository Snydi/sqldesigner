@extends('layouts.main')

@section('title', 'Terms of Service — SQL Designer')

@section('head')
<meta name="description" content="Terms of Service for SQL Designer — the rules and conditions for using our free online database schema designer.">
<link rel="canonical" href="https://sql-designer.com/terms">
<script type="application/ld+json">
@verbatim
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Terms of Service",
  "url": "https://sql-designer.com/terms",
  "description": "Terms of Service for SQL Designer"
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
        font-size: 0.82rem;
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
        font-size: 0.92rem;
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

<div class="legal-wrap">
    <h1>Terms of Service</h1>
    <p class="legal-meta">Last updated: May 7, 2026</p>

    <p>These Terms of Service ("Terms") govern your use of <strong>sql-designer.com</strong> ("Service"), operated by SQL Designer. By accessing or using the Service you agree to these Terms.</p>

    <h2>1. Use of the Service</h2>
    <p>SQL Designer provides a free online tool for designing relational database schemas. You may use the Service for personal, educational, or commercial projects.</p>

    <h2>2. Accounts</h2>
    <ul>
        <li>You must provide accurate information when creating an account.</li>
        <li>You are responsible for maintaining the confidentiality of your account and all activity under it.</li>
        <li>You must be at least 13 years old to create an account.</li>
    </ul>

    <h2>3. Your Content</h2>
    <p>You retain ownership of the diagrams and schemas you create. By using the Service you grant us a limited licence to store and display your content solely for the purpose of providing the Service to you.</p>

    <h2>4. Shared &amp; Public Diagrams</h2>
    <p>If you share a diagram via a public link, that diagram becomes accessible to anyone with the link. You are solely responsible for the content of diagrams you choose to share. You can revoke access at any time.</p>

    <h2>5. Prohibited Uses</h2>
    <p>You agree not to:</p>
    <ul>
        <li>Use the Service for any unlawful purpose.</li>
        <li>Attempt to gain unauthorised access to our systems or another user's data.</li>
        <li>Upload or transmit malicious code or content.</li>
        <li>Scrape or bulk-download Service content without permission.</li>
        <li>Resell or sublicence the Service without our written consent.</li>
    </ul>

    <h2>6. Availability</h2>
    <p>We aim to keep the Service running reliably but do not guarantee uninterrupted availability. We may suspend or discontinue the Service at any time with reasonable notice where possible.</p>

    <h2>7. Disclaimer of Warranties</h2>
    <p>The Service is provided "as is" without warranties of any kind, express or implied. We do not warrant that the Service will be error-free or that data will never be lost.</p>

    <h2>8. Limitation of Liability</h2>
    <p>To the fullest extent permitted by law, SQL Designer shall not be liable for any indirect, incidental, or consequential damages arising from your use of the Service.</p>

    <h2>9. Termination</h2>
    <p>We may suspend or terminate your account if you violate these Terms. You may delete your account at any time by contacting us.</p>

    <h2>10. Changes to These Terms</h2>
    <p>We may update these Terms from time to time. The "Last updated" date at the top reflects any changes. Continued use after changes constitutes acceptance of the new Terms.</p>

    <h2>11. Governing Law</h2>
    <p>These Terms are governed by applicable law. Any disputes will be resolved in accordance with that law.</p>

    <h2>12. Contact</h2>
    <p>Questions? Email us at <a href="mailto:dmitriy@sql-designer.com">dmitriy@sql-designer.com</a>.</p>

    <hr class="legal">
    <p><a href="/privacy">View our Privacy Policy</a></p>
</div>
@endsection
