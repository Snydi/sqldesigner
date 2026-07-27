<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use JsonException;
use RuntimeException;

class RobokassaService
{
    /** @return array<string, string> */
    public function checkoutParameters(Payment $payment): array
    {
        $this->assertConfigured();

        if ($payment->provider_invoice_id === null) {
            throw new RuntimeException('The payment must have a Robokassa invoice id.');
        }

        $outSum = $this->formatMinorAmount($payment->provider_amount_minor);
        $receipt = $this->receipt($outSum);
        $customParameters = [
            'Shp_currency' => $payment->currency,
            'Shp_payment_id' => (string) $payment->id,
            'Shp_user_id' => (string) $payment->user_id,
        ];

        $parameters = [
            'MerchantLogin' => (string) config('robokassa.merchant_login'),
            'OutSum' => $outSum,
            'InvId' => $payment->provider_invoice_id,
            'Description' => 'SQL Designer Pro - monthly subscription',
            'Email' => $payment->user->email,
            'Culture' => (string) config('robokassa.culture', 'en'),
            'Encoding' => 'utf-8',
            'IsTest' => config('robokassa.test_mode') ? '1' : '0',
            'ExpirationDate' => ($payment->expires_at
                ?? now()->addMinutes((int) config('robokassa.checkout_expires_minutes', 30)))
                ->toIso8601String(),
            ...$customParameters,
        ];

        // The first payment authorizes future automatic card charges.
        $parameters['Recurring'] = 'true';

        if ($receipt !== null) {
            $parameters['Receipt'] = $receipt;
        }
        if (filled(config('robokassa.payment_method'))) {
            $parameters['IncCurrLabel'] = (string) config('robokassa.payment_method');
        }

        $returnParameters = [
            'ResultUrl2' => (string) config('robokassa.result_url'),
            'SuccessUrl2' => (string) config('robokassa.success_url'),
            'SuccessUrl2Method' => 'GET',
            'FailUrl2' => (string) config('robokassa.fail_url'),
            'FailUrl2Method' => 'GET',
        ];
        $parameters = [...$parameters, ...$returnParameters];

        $parameters['SignatureValue'] = $this->paymentSignature(
            $outSum,
            $payment->provider_invoice_id,
            $receipt,
            $returnParameters,
            $customParameters,
        );

        return $parameters;
    }

    /** @return array<string, string> */
    public function recurringPaymentParameters(Payment $payment, string $parentInvoiceId): array
    {
        $this->assertConfigured();

        if ($payment->provider_invoice_id === null) {
            throw new RuntimeException('The payment must have a Robokassa invoice id.');
        }

        $outSum = $this->formatMinorAmount($payment->provider_amount_minor);
        $receipt = $this->receipt($outSum);
        $customParameters = [
            'Shp_currency' => $payment->currency,
            'Shp_payment_id' => (string) $payment->id,
            'Shp_user_id' => (string) $payment->user_id,
        ];

        $parameters = [
            'MerchantLogin' => (string) config('robokassa.merchant_login'),
            'OutSum' => $outSum,
            'InvId' => $payment->provider_invoice_id,
            'PreviousInvoiceID' => $parentInvoiceId,
            'Description' => 'SQL Designer Pro - monthly subscription renewal',
            ...$customParameters,
        ];
        if ($receipt !== null) {
            $parameters['Receipt'] = $receipt;
        }

        $parameters['SignatureValue'] = $this->paymentSignature(
            $outSum,
            $payment->provider_invoice_id,
            $receipt,
            [],
            $customParameters,
        );

        return $parameters;
    }

    /** @param array<string, mixed> $payload */
    public function verifyResultSignature(array $payload): bool
    {
        $provided = $this->scalarString($payload['SignatureValue'] ?? null);
        if ($provided === '') {
            return false;
        }

        $expected = $this->resultSignature(
            $this->scalarString($payload['OutSum'] ?? null),
            $this->scalarString($payload['InvId'] ?? null),
            $this->customParameters($payload),
        );

        return hash_equals(strtolower($expected), strtolower($provided));
    }

    /** @return array{header: array<string, mixed>, data: array<string, mixed>} */
    public function decodeResultUrl2Notification(string $jws): array
    {
        $parts = explode('.', $jws);
        if (count($parts) !== 3) {
            throw new InvalidArgumentException('Invalid Robokassa JWS format.');
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;

        try {
            $header = json_decode($this->base64UrlDecode($encodedHeader), true, flags: JSON_THROW_ON_ERROR);
            $payload = json_decode($this->base64UrlDecode($encodedPayload), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException('Invalid Robokassa JWS payload.', previous: $exception);
        }

        if (! is_array($header) || ($header['alg'] ?? null) !== 'RS256') {
            throw new InvalidArgumentException('Unsupported Robokassa JWS algorithm.');
        }

        $publicKey = $this->jwsPublicKey();
        $verified = openssl_verify(
            $encodedHeader.'.'.$encodedPayload,
            $this->base64UrlDecode($encodedSignature),
            $publicKey,
            OPENSSL_ALGO_SHA256,
        );
        if ($verified !== 1) {
            throw new InvalidArgumentException('Invalid Robokassa JWS signature.');
        }

        $data = is_array($payload) ? ($payload['data'] ?? null) : null;
        if (($header['typ'] ?? null) !== 'JWT'
            || ! is_array($data)
            || ($payload['header']['type'] ?? null) !== 'PaymentStateNotification'
            || ($data['shop'] ?? null) !== (string) config('robokassa.merchant_login')
            || ($data['state'] ?? null) !== 'OK') {
            throw new InvalidArgumentException('Invalid Robokassa payment notification.');
        }

        foreach (['opKey', 'invId', 'incSum', 'paymentMethod'] as $field) {
            if (! is_scalar($data[$field] ?? null) || (string) $data[$field] === '') {
                throw new InvalidArgumentException("Missing Robokassa JWS field: {$field}");
            }
        }

        return [
            'header' => is_array($payload['header'] ?? null) ? $payload['header'] : [],
            'data' => $data,
        ];
    }

    /** @param array<string, string> $customParameters */
    public function resultSignature(string $outSum, string $invoiceId, array $customParameters = []): string
    {
        return $this->hash($this->signatureBase([
            $outSum,
            $invoiceId,
            (string) config('robokassa.password2'),
        ], $customParameters));
    }

    public function paymentUrl(Payment $payment): string
    {
        return $this->paymentUrlFromParameters($this->checkoutParameters($payment));
    }

    /**
     * Returns the provider's operation state, or null when it cannot be read.
     *
     * @see https://docs.robokassa.ru/ru/xml-interfaces
     */
    public function operationState(Payment $payment): ?int
    {
        if ($payment->provider_invoice_id === null) {
            return null;
        }

        $this->assertConfigured();
        $merchantLogin = (string) config('robokassa.merchant_login');
        $response = Http::timeout(15)->get((string) config('robokassa.operation_state_url'), [
            'MerchantLogin' => $merchantLogin,
            'InvoiceID' => $payment->provider_invoice_id,
            'Signature' => $this->hash($merchantLogin.':'.$payment->provider_invoice_id.':'.(string) config('robokassa.password2')),
        ]);
        if (! $response->successful()) {
            return null;
        }

        $xml = @simplexml_load_string($response->body());
        if ($xml === false) {
            return null;
        }

        $resultCode = $xml->xpath('//*[local-name()="Result"]/*[local-name()="Code"]');
        $stateCode = $xml->xpath('//*[local-name()="State"]/*[local-name()="Code"]');
        if ($resultCode === false || $stateCode === false || ! isset($resultCode[0], $stateCode[0]) || (int) $resultCode[0] !== 0) {
            return null;
        }

        return (int) $stateCode[0];
    }

    /** @param array<string, string> $parameters */
    public function paymentUrlFromParameters(array $parameters): string
    {
        return (string) config('robokassa.payment_url').'?'.http_build_query(
            $parameters,
            '',
            '&',
            PHP_QUERY_RFC3986,
        );
    }

    public function providerAmountMinor(): int
    {
        $amount = config('robokassa.provider_amount');
        if (! is_string($amount) && ! is_int($amount) && ! is_float($amount)) {
            throw new RuntimeException('ROBOKASSA_PROVIDER_AMOUNT must be configured.');
        }

        return $this->parseMinorAmount((string) $amount);
    }

    public function parseMinorAmount(string $amount, bool $allowZero = false): int
    {
        if (! preg_match('/^(\d+)(?:\.(\d{1,6}))?$/', $amount, $matches)) {
            throw new RuntimeException('Robokassa amount has an invalid format.');
        }

        $fraction = str_pad($matches[2] ?? '', 6, '0');
        if (substr($fraction, 2) !== '0000') {
            throw new RuntimeException('Robokassa amount has precision below one kopeck.');
        }

        $minor = ((int) $matches[1] * 100) + (int) substr($fraction, 0, 2);
        if ($minor < 0 || (! $allowZero && $minor === 0)) {
            throw new RuntimeException('Robokassa amount must be greater than zero.');
        }

        return $minor;
    }

    public function formatMinorAmount(int $minor): string
    {
        return sprintf('%d.%02d', intdiv($minor, 100), $minor % 100);
    }

    public function assertConfigured(): void
    {
        foreach (['merchant_login', 'password1', 'password2'] as $key) {
            if (blank(config('robokassa.'.$key))) {
                throw new RuntimeException('Robokassa credentials are not configured.');
            }
        }

        $this->providerAmountMinor();
        $algorithm = strtolower((string) config('robokassa.hash_algorithm', 'md5'));
        if (! in_array($algorithm, hash_algos(), true)) {
            throw new RuntimeException('ROBOKASSA_HASH_ALGORITHM is not supported by PHP.');
        }
        if ((string) config('robokassa.provider_currency') !== 'RUB') {
            throw new RuntimeException('Robokassa OutSum must be configured in RUB.');
        }
    }

    /**
     * @param  array<string, string>  $customParameters
     * @param  array<string, string>  $returnParameters
     */
    private function paymentSignature(
        string $outSum,
        string $invoiceId,
        ?string $receipt,
        array $returnParameters,
        array $customParameters,
    ): string {
        $parts = [
            (string) config('robokassa.merchant_login'),
            $outSum,
            $invoiceId,
        ];
        if ($receipt !== null) {
            $parts[] = $receipt;
        }
        foreach (['ResultUrl2', 'SuccessUrl2', 'SuccessUrl2Method', 'FailUrl2', 'FailUrl2Method'] as $key) {
            if (isset($returnParameters[$key])) {
                $parts[] = $returnParameters[$key];
            }
        }
        $parts[] = (string) config('robokassa.password1');

        return $this->hash($this->signatureBase($parts, $customParameters));
    }

    /** @param array<string, mixed> $payload
     * @return array<string, string>
     */
    private function customParameters(array $payload): array
    {
        $parameters = [];
        foreach ($payload as $key => $value) {
            if (str_starts_with($key, 'Shp_') && is_scalar($value)) {
                $parameters[$key] = (string) $value;
            }
        }

        return $parameters;
    }

    /**
     * @param  list<string>  $parts
     * @param  array<string, string>  $customParameters
     */
    private function signatureBase(array $parts, array $customParameters): string
    {
        ksort($customParameters, SORT_STRING);
        foreach ($customParameters as $key => $value) {
            $parts[] = $key.'='.$value;
        }

        return implode(':', $parts);
    }

    private function hash(string $value): string
    {
        return hash(strtolower((string) config('robokassa.hash_algorithm', 'md5')), $value);
    }

    private function base64UrlDecode(string $value): string
    {
        $padding = (4 - strlen($value) % 4) % 4;
        $decoded = base64_decode(strtr($value, '-_', '+/').str_repeat('=', $padding), true);
        if ($decoded === false) {
            throw new InvalidArgumentException('Invalid Robokassa JWS encoding.');
        }

        return $decoded;
    }

    /** @return \OpenSSLAsymmetricKey */
    private function jwsPublicKey(): \OpenSSLAsymmetricKey
    {
        $keyMaterial = config('robokassa.jws_public_key');
        if (! is_string($keyMaterial) || trim($keyMaterial) === '') {
            $path = config('robokassa.jws_public_key_path');
            if (! is_string($path) || ! is_file($path)) {
                throw new RuntimeException('Robokassa JWS public key is not configured.');
            }
            $keyMaterial = file_get_contents($path);
        }

        if (! is_string($keyMaterial) || ($publicKey = openssl_pkey_get_public($keyMaterial)) === false) {
            throw new RuntimeException('Robokassa JWS public key is invalid.');
        }

        return $publicKey;
    }

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    /** @throws JsonException */
    private function receipt(string $outSum): ?string
    {
        if (! config('robokassa.receipt.enabled')) {
            return null;
        }

        $receipt = [
            'items' => [[
                'name' => 'SQL Designer Pro - monthly subscription',
                'quantity' => 1,
                'sum' => (float) $outSum,
                'payment_method' => 'full_payment',
                'payment_object' => 'service',
                'tax' => (string) config('robokassa.receipt.tax', 'none'),
            ]],
        ];
        if (filled(config('robokassa.receipt.sno'))) {
            $receipt['sno'] = (string) config('robokassa.receipt.sno');
        }

        return urlencode(json_encode($receipt, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
    }
}
