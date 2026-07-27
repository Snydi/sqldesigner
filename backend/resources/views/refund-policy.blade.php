@extends('layouts.main')

@section('title', 'Refund Policy for Pro Subscriptions — SQL Designer')

@section('head')
<meta name="description" content="Read the SQL Designer refund policy for Pro subscriptions, including cancellation steps, processing times, refund eligibility, and repayment methods.">
<link rel="canonical" href="https://sql-designer.com/refund-policy">
<script type="application/ld+json">
@verbatim
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Refund Policy",
  "url": "https://sql-designer.com/refund-policy",
  "description": "Refund and cancellation terms for SQL Designer Pro subscriptions"
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
    <h1>Refund Policy</h1>
    <p class="legal-meta">Last updated: July 21, 2026</p>

    <p>This Refund Policy applies to paid SQL Designer Pro subscriptions purchased through <strong>sql-designer.com</strong>. Pro access normally begins immediately after a successful payment.</p>

    <h2>1. Cancelling Before or During the Service</h2>
    <p>You may refuse the purchase before Pro access begins or cancel while the subscription service is being provided. If you cancel before access begins, we will refund the full amount paid. If the service has already begun, the refundable amount is calculated for the unused subscription period from the date we receive your request. We may consider only documented expenses directly incurred in providing the service before cancellation, where permitted by applicable law.</p>

    <h2>2. How to Request a Cancellation and Refund</h2>
    <p>To cancel and request a refund:</p>
    <ul>
        <li>Email <a href="mailto:dmitriy@sql-designer.com">dmitriy@sql-designer.com</a> from the address associated with your SQL Designer account.</li>
        <li>Use the subject line "Subscription cancellation and refund".</li>
        <li>Include your account email, payment date and amount, and the payment or order identifier shown on your receipt. You may include a reason, but it is not required.</li>
    </ul>

    <h2>3. How We Process Your Request</h2>
    <ul>
        <li>We will confirm receipt of your request by email.</li>
        <li>We will verify the payment, cancel future renewals, and calculate the refundable amount.</li>
        <li>We will email you the result and the refund amount.</li>
        <li>Approved funds will be returned to the original payment method within 10 calendar days after we receive your request.</li>
    </ul>

    <h2>4. Refund Amount and Fees</h2>
    <p>We do not deduct payment-service commissions, bank commissions, or refund-processing fees from the refundable amount. If a deduction for documented expenses is permitted by law, we will identify and substantiate those expenses in our response.</p>

    <h2>5. Billing Errors</h2>
    <p>If you believe you were charged incorrectly or more than once, follow the same request process above. We will investigate the payment and return any confirmed overpayment to the original payment method within 10 calendar days after receiving your request.</p>

    <h2>6. Rights Under Applicable Law</h2>
    <p>Nothing in this policy limits any refund or cancellation rights that cannot legally be excluded in your jurisdiction. Where applicable law gives you more favourable rights or deadlines, those rules apply.</p>

    <h2>7. Contact</h2>
    <p>For billing or refund questions, email <a href="mailto:dmitriy@sql-designer.com">dmitriy@sql-designer.com</a>.</p>

    <hr class="legal">
    <p><a href="/terms">View our Terms of Service</a></p>
    </section>

    <section data-legal-language-content="ru" lang="ru" hidden>
    <h1>Политика возврата денежных средств</h1>
    <p class="legal-meta">Последнее обновление: 21 июля 2026 г.</p>

    <p>Настоящая Политика распространяется на платные подписки SQL Designer Pro, приобретенные на сайте <strong>sql-designer.com</strong>. Обычно доступ к функциям Pro предоставляется сразу после успешной оплаты.</p>

    <h2>1. Отказ до начала или во время оказания услуги</h2>
    <p>Вы можете отказаться от покупки до предоставления доступа Pro или отменить подписку в процессе оказания услуги. Если вы отказываетесь до предоставления доступа, мы вернем полную уплаченную сумму. Если оказание услуги уже началось, сумма возврата рассчитывается за неиспользованный период подписки начиная с даты получения нами обращения. В случаях, предусмотренных применимым законодательством, мы можем учесть только документально подтвержденные расходы, непосредственно понесенные при оказании услуги до момента отказа.</p>

    <h2>2. Как отменить подписку и запросить возврат</h2>
    <p>Чтобы отменить подписку и запросить возврат денежных средств:</p>
    <ul>
        <li>Отправьте письмо с адреса, связанного с вашей учетной записью SQL Designer, на <a href="mailto:dmitriy@sql-designer.com">dmitriy@sql-designer.com</a>.</li>
        <li>Укажите тему письма «Отмена подписки и возврат денежных средств».</li>
        <li>Укажите адрес электронной почты учетной записи, дату и сумму платежа, а также идентификатор платежа или заказа из чека. Причину отказа можно указать по желанию, но это не обязательно.</li>
    </ul>

    <h2>3. Как мы обрабатываем обращение</h2>
    <ul>
        <li>Мы подтвердим получение обращения по электронной почте.</li>
        <li>Мы проверим платеж, отключим будущие продления и рассчитаем сумму возврата.</li>
        <li>Мы сообщим результат рассмотрения и сумму возврата по электронной почте.</li>
        <li>Денежные средства будут возвращены тем же способом, которым была произведена оплата, в течение 10 календарных дней с даты получения нами обращения.</li>
    </ul>

    <h2>4. Сумма возврата и комиссии</h2>
    <p>Мы не удерживаем из суммы возврата комиссии платежного сервиса, банка или комиссии за обработку возврата. Если закон допускает учет документально подтвержденных расходов, мы укажем такие расходы и предоставим их обоснование в ответе на обращение.</p>

    <h2>5. Ошибки при оплате</h2>
    <p>Если вы считаете, что с вас списали неверную сумму или платеж был списан повторно, направьте обращение в порядке, указанном выше. Мы проверим платеж и вернем подтвержденную сумму переплаты тем же способом, которым была произведена оплата, в течение 10 календарных дней с даты получения обращения.</p>

    <h2>6. Права, предусмотренные законодательством</h2>
    <p>Настоящая Политика не ограничивает права на отказ от услуги или возврат денежных средств, которые не могут быть исключены применимым законодательством. Если применимое законодательство устанавливает более благоприятные права или сроки, применяются такие правила.</p>

    <h2>7. Контакты</h2>
    <p>По вопросам оплаты или возврата денежных средств напишите нам: <a href="mailto:dmitriy@sql-designer.com">dmitriy@sql-designer.com</a>.</p>

    <hr class="legal">
    <p><a href="/terms">Ознакомиться с Условиями использования</a></p>
    </section>
</div>
@endsection
