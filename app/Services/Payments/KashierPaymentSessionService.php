<?php

namespace App\Services\Payments;

use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class KashierPaymentSessionService
{
    private const HEADER_NAMES = ['Authorization', 'api-key', 'Content-Type', 'Accept'];

    public function create(
        string $orderId,
        float $amount,
        User $customer,
        string $description
    ): array {
        $endpoint = trim((string) config('nafezly-payments.KASHIER_PAYMENT_SESSIONS_URL'));
        $merchantId = trim((string) config('nafezly-payments.KASHIER_ACCOUNT_KEY'));
        [$secretKey, $secretKeySource] = $this->credential(
            'KASHIER_PAYMENT_SESSIONS_SECRET_KEY',
            'KASHIER_TOKEN'
        );
        [$paymentApiKey, $apiKeySource] = $this->credential(
            'KASHIER_PAYMENT_SESSIONS_API_KEY',
            'KASHIER_IFRAME_KEY'
        );

        $diagnostics = [
            'credential_presence' => [
                'KASHIER_ACCOUNT_KEY' => $merchantId !== '',
                'KASHIER_PAYMENT_SESSIONS_SECRET_KEY' => $this->isConfigured('KASHIER_PAYMENT_SESSIONS_SECRET_KEY'),
                'KASHIER_PAYMENT_SESSIONS_API_KEY' => $this->isConfigured('KASHIER_PAYMENT_SESSIONS_API_KEY'),
                'KASHIER_TOKEN' => $this->isConfigured('KASHIER_TOKEN'),
                'KASHIER_IFRAME_KEY' => $this->isConfigured('KASHIER_IFRAME_KEY'),
            ],
            'credential_sources' => [
                'Authorization' => $secretKeySource,
                'api-key' => $apiKeySource,
            ],
            'header_names' => self::HEADER_NAMES,
            'authorization_format' => 'raw',
            'endpoint_host' => parse_url($endpoint, PHP_URL_HOST),
        ];

        Log::info('Kashier Payment Sessions request prepared', $diagnostics);

        if ($endpoint === '' || $merchantId === '' || $secretKey === '' || $paymentApiKey === '') {
            Log::warning('Kashier Payment Sessions configuration incomplete', $diagnostics);
            throw new RuntimeException('The selected payment method is temporarily unavailable.');
        }

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withHeaders([
                    'Authorization' => $secretKey,
                    'api-key' => $paymentApiKey,
                ])
                ->connectTimeout(10)
                ->timeout(20)
                ->post($endpoint, $this->payload($orderId, $amount, $customer, $description, $merchantId));
        } catch (ConnectionException) {
            Log::warning('Kashier Payment Sessions connection failed', [
                'endpoint_host' => $diagnostics['endpoint_host'],
                'header_names' => self::HEADER_NAMES,
            ]);
            throw new RuntimeException('The selected payment method is temporarily unavailable.');
        }

        if (!$response->successful()) {
            Log::warning('Kashier Payment Sessions request rejected', [
                'endpoint_host' => $diagnostics['endpoint_host'],
                'header_names' => self::HEADER_NAMES,
                'http_status' => $response->status(),
                'response_message' => $this->sanitizedResponseMessage($response),
            ]);
            throw new RuntimeException('The selected payment method is temporarily unavailable.');
        }

        $responseData = $response->json();
        $sessionUrl = data_get($responseData, 'sessionUrl')
            ?? data_get($responseData, 'data.sessionUrl');
        $sessionId = data_get($responseData, '_id')
            ?? data_get($responseData, 'data._id')
            ?? data_get($responseData, 'data.sessionId');

        if (!is_string($sessionUrl) || !$this->isTrustedSessionUrl($sessionUrl)) {
            Log::warning('Kashier Payment Sessions returned an invalid session URL', [
                'endpoint_host' => $diagnostics['endpoint_host'],
                'http_status' => $response->status(),
            ]);
            throw new RuntimeException('The selected payment method is temporarily unavailable.');
        }

        return [
            'payment_id' => $orderId,
            'session_id' => is_scalar($sessionId) ? (string) $sessionId : null,
            'session_url' => $sessionUrl,
        ];
    }

    private function payload(
        string $orderId,
        float $amount,
        User $customer,
        string $description,
        string $merchantId
    ): array {
        return [
            'expireAt' => now()
                ->addMinutes((int) config('nafezly-payments.KASHIER_SESSION_TTL_MINUTES', 30))
                ->utc()
                ->toIso8601String(),
            'maxFailureAttempts' => 3,
            'paymentType' => 'credit',
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => (string) config('nafezly-payments.KASHIER_CURRENCY', 'EGP'),
            'order' => $orderId,
            'merchantRedirect' => route(
                config('nafezly-payments.VERIFY_ROUTE_NAME'),
                ['payment' => 'kashier']
            ),
            'display' => 'en',
            'type' => 'one-time',
            'allowedMethods' => (string) config('nafezly-payments.KASHIER_ALLOWED_METHODS', 'card,wallet'),
            'redirectMethod' => null,
            'merchantId' => $merchantId,
            'failureRedirect' => false,
            'defaultMethod' => 'card',
            'description' => mb_substr(trim($description), 0, 150),
            'manualCapture' => false,
            'customer' => [
                'email' => (string) $customer->email,
                'reference' => (string) $customer->id,
            ],
            'interactionSource' => 'ECOMMERCE',
            'enable3DS' => true,
            'serverWebhook' => (string) config('nafezly-payments.KASHIER_WEBHOOK_URL'),
        ];
    }

    private function credential(string $preferredName, string $fallbackName): array
    {
        $preferred = trim((string) config('nafezly-payments.' . $preferredName));

        if ($preferred !== '') {
            return [$preferred, $preferredName];
        }

        return [trim((string) config('nafezly-payments.' . $fallbackName)), $fallbackName];
    }

    private function isConfigured(string $name): bool
    {
        return trim((string) config('nafezly-payments.' . $name)) !== '';
    }

    private function sanitizedResponseMessage(Response $response): string
    {
        return match ($response->status()) {
            400, 422 => 'Request validation rejected by Kashier.',
            401 => 'Authentication rejected by Kashier.',
            403 => 'Authorization rejected by Kashier.',
            429 => 'Request rate limited by Kashier.',
            default => $response->serverError()
                ? 'Kashier service returned an error.'
                : 'Kashier returned an error response.',
        };
    }

    private function isTrustedSessionUrl(string $url): bool
    {
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return $scheme === 'https'
            && ($host === 'kashier.io' || str_ends_with($host, '.kashier.io'));
    }
}
