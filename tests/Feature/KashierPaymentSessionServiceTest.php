<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Payments\KashierPaymentSessionService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class KashierPaymentSessionServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.url' => 'https://mathcrack.test',
            'nafezly-payments.KASHIER_PAYMENT_SESSIONS_URL' => 'https://api.kashier.io/v3/payment/sessions',
            'nafezly-payments.KASHIER_TOKEN' => 'test-secret-key',
            'nafezly-payments.KASHIER_IFRAME_KEY' => 'test-payment-api-key',
            'nafezly-payments.KASHIER_ACCOUNT_KEY' => 'MID-TEST-123',
            'nafezly-payments.KASHIER_CURRENCY' => 'EGP',
            'nafezly-payments.KASHIER_ALLOWED_METHODS' => 'card,wallet',
            'nafezly-payments.KASHIER_SESSION_TTL_MINUTES' => 30,
            'nafezly-payments.KASHIER_WEBHOOK_URL' => 'https://mathcrack.test/kashier-webhook',
        ]);
    }

    public function test_it_creates_a_wallet_enabled_payment_session(): void
    {
        Http::fake([
            'https://api.kashier.io/v3/payment/sessions' => Http::response([
                '_id' => 'session-123',
                'sessionUrl' => 'https://payments.kashier.io/session/session-123',
            ], 201),
        ]);

        $customer = new User([
            'email' => 'student@example.test',
        ]);
        $customer->id = 42;

        $result = app(KashierPaymentSessionService::class)->create(
            'merchant-order-123',
            125.5,
            $customer,
            'Single test payment'
        );

        $this->assertSame('merchant-order-123', $result['payment_id']);
        $this->assertSame('session-123', $result['session_id']);
        $this->assertSame('https://payments.kashier.io/session/session-123', $result['session_url']);

        Http::assertSent(function (Request $request) {
            $payload = $request->data();

            return $request->url() === 'https://api.kashier.io/v3/payment/sessions'
                && $request->method() === 'POST'
                && $request->hasHeader('Authorization', 'test-secret-key')
                && $request->hasHeader('api-key', 'test-payment-api-key')
                && $payload['paymentType'] === 'credit'
                && $payload['amount'] === '125.50'
                && $payload['currency'] === 'EGP'
                && $payload['order'] === 'merchant-order-123'
                && $payload['allowedMethods'] === 'card,wallet'
                && $payload['interactionSource'] === 'ECOMMERCE'
                && $payload['serverWebhook'] === 'https://mathcrack.test/kashier-webhook'
                && $payload['merchantRedirect'] === route('verify-payment', ['payment' => 'kashier'])
                && $payload['merchantId'] === 'MID-TEST-123'
                && $payload['manualCapture'] === false
                && $payload['customer'] === [
                    'email' => 'student@example.test',
                    'reference' => '42',
                ]
                && !array_key_exists('phone', $payload['customer'])
                && !array_key_exists('mobile', $payload['customer']);
        });
    }

    public function test_it_rejects_an_untrusted_session_url(): void
    {
        Http::fake([
            'https://api.kashier.io/v3/payment/sessions' => Http::response([
                '_id' => 'session-123',
                'sessionUrl' => 'https://example.test/not-kashier',
            ], 201),
        ]);

        $customer = new User(['email' => 'student@example.test']);
        $customer->id = 42;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('invalid session URL');

        app(KashierPaymentSessionService::class)->create(
            'merchant-order-123',
            125.5,
            $customer,
            'Single test payment'
        );
    }

    public function test_it_does_not_expose_a_provider_error_body(): void
    {
        Http::fake([
            'https://api.kashier.io/v3/payment/sessions' => Http::response([
                'message' => 'provider diagnostic that must not be logged',
            ], 422),
        ]);

        $customer = new User(['email' => 'student@example.test']);
        $customer->id = 42;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('HTTP 422');
        $this->expectExceptionMessageMatches('/^(?!.*provider diagnostic).*$/');

        app(KashierPaymentSessionService::class)->create(
            'merchant-order-123',
            125.5,
            $customer,
            'Single test payment'
        );
    }
}
