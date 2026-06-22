<?php

namespace App\Services\Payments;

use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class KashierPaymentSessionService
{
    public function create(
        string $orderId,
        float $amount,
        User $customer,
        string $description
    ): array {
        $endpoint = trim((string) config('nafezly-payments.KASHIER_PAYMENT_SESSIONS_URL'));
        $secretKey = trim((string) config('nafezly-payments.KASHIER_TOKEN'));
        $paymentApiKey = trim((string) config('nafezly-payments.KASHIER_IFRAME_KEY'));
        $merchantId = trim((string) config('nafezly-payments.KASHIER_ACCOUNT_KEY'));

        if ($endpoint === '' || $secretKey === '' || $paymentApiKey === '' || $merchantId === '') {
            throw new RuntimeException('Kashier Payment Sessions is not configured.');
        }

        $payload = $this->payload($orderId, $amount, $customer, $description, $merchantId);

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withHeaders([
                    'Authorization' => $secretKey,
                    'api-key' => $paymentApiKey,
                ])
                ->connectTimeout(10)
                ->timeout(20)
                ->post($endpoint, $payload);
        } catch (ConnectionException) {
            throw new RuntimeException('Kashier Payment Sessions could not be reached.');
        }

        if (!$response->successful()) {
            throw new RuntimeException(
                'Kashier Payment Sessions rejected the request with HTTP ' . $response->status() . '.'
            );
        }

        $responseData = $response->json();
        $sessionUrl = data_get($responseData, 'sessionUrl')
            ?? data_get($responseData, 'data.sessionUrl');
        $sessionId = data_get($responseData, '_id')
            ?? data_get($responseData, 'data._id')
            ?? data_get($responseData, 'data.sessionId');

        if (!is_string($sessionUrl) || !$this->isTrustedSessionUrl($sessionUrl)) {
            throw new RuntimeException('Kashier Payment Sessions returned an invalid session URL.');
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
        $redirectUrl = route(
            config('nafezly-payments.VERIFY_ROUTE_NAME'),
            ['payment' => 'kashier']
        );
        $webhookUrl = trim((string) config('nafezly-payments.KASHIER_WEBHOOK_URL'));

        if ($webhookUrl === '') {
            throw new RuntimeException('Kashier webhook URL is not configured.');
        }

        $payload = [
            'expireAt' => now()
                ->addMinutes((int) config('nafezly-payments.KASHIER_SESSION_TTL_MINUTES', 30))
                ->utc()
                ->toIso8601String(),
            'maxFailureAttempts' => 3,
            'paymentType' => 'credit',
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => (string) config('nafezly-payments.KASHIER_CURRENCY', 'EGP'),
            'order' => $orderId,
            'merchantRedirect' => $redirectUrl,
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
            'serverWebhook' => $webhookUrl,
        ];

        return $payload;
    }

    private function isTrustedSessionUrl(string $url): bool
    {
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        return $scheme === 'https'
            && ($host === 'kashier.io' || str_ends_with($host, '.kashier.io'));
    }
}
