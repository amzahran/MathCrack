<?php

namespace Tests\Feature;

use App\Http\Controllers\Web\Back\Users\Payment\PaymentController;
use App\Models\User;
use App\Services\Payments\KashierPaymentSessionService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery;
use ReflectionMethod;
use RuntimeException;
use Tests\TestCase;

class KashierPaymentSessionsAuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.url' => 'https://mathcrack.test',
            'nafezly-payments.KASHIER_PAYMENT_SESSIONS_URL' => 'https://api.kashier.io/v3/payment/sessions',
            'nafezly-payments.KASHIER_PAYMENT_SESSIONS_SECRET_KEY' => 'test-session-secret',
            'nafezly-payments.KASHIER_PAYMENT_SESSIONS_API_KEY' => 'test-session-api-key',
            'nafezly-payments.KASHIER_ACCOUNT_KEY' => 'MID-TEST-123',
            'nafezly-payments.KASHIER_TOKEN' => 'test-legacy-token',
            'nafezly-payments.KASHIER_IFRAME_KEY' => 'test-legacy-iframe-key',
            'nafezly-payments.KASHIER_CURRENCY' => 'EGP',
            'nafezly-payments.KASHIER_ALLOWED_METHODS' => 'card,wallet',
            'nafezly-payments.KASHIER_SESSION_TTL_MINUTES' => 30,
            'nafezly-payments.KASHIER_WEBHOOK_URL' => 'https://mathcrack.test/kashier-webhook',
        ]);
    }

    public function test_hosted_checkout_is_the_default_flow(): void
    {
        $this->assertFalse((bool) config('nafezly-payments.KASHIER_USE_PAYMENT_SESSIONS'));

        $method = new ReflectionMethod(PaymentController::class, 'usesKashierPaymentSessions');
        $this->assertFalse($method->invoke(app(PaymentController::class)));

        config(['nafezly-payments.KASHIER_USE_PAYMENT_SESSIONS' => true]);

        $this->assertTrue($method->invoke(app(PaymentController::class)));
    }

    public function test_payment_sessions_uses_documented_raw_auth_headers(): void
    {
        Log::spy();
        Http::fake([
            'https://api.kashier.io/v3/payment/sessions' => Http::response([
                '_id' => 'session-123',
                'sessionUrl' => 'https://payments.kashier.io/session/session-123',
            ], 201),
        ]);

        $result = app(KashierPaymentSessionService::class)->create(
            'merchant-order-123',
            125.5,
            $this->customer(),
            'Single test payment'
        );

        $this->assertSame('merchant-order-123', $result['payment_id']);

        Http::assertSent(function (Request $request) {
            $payload = $request->data();

            return $request->hasHeader('Authorization', 'test-session-secret')
                && $request->hasHeader('api-key', 'test-session-api-key')
                && !$request->hasHeader('x-api-key')
                && !str_starts_with((string) $request->header('Authorization')[0], 'Bearer ')
                && !array_key_exists('hash', $payload)
                && !array_key_exists('signature', $payload)
                && $payload['merchantId'] === 'MID-TEST-123'
                && $payload['allowedMethods'] === 'card,wallet';
        });

        Log::shouldHaveReceived('info')
            ->with('Kashier Payment Sessions request prepared', Mockery::on(function (array $context) {
                $encoded = json_encode($context);

                return $context['credential_sources'] === [
                    'Authorization' => 'KASHIER_PAYMENT_SESSIONS_SECRET_KEY',
                    'api-key' => 'KASHIER_PAYMENT_SESSIONS_API_KEY',
                ]
                    && $context['header_names'] === ['Authorization', 'api-key', 'Content-Type', 'Accept']
                    && $context['authorization_format'] === 'raw'
                    && $context['endpoint_host'] === 'api.kashier.io'
                    && !str_contains($encoded, 'test-session-secret')
                    && !str_contains($encoded, 'test-session-api-key')
                    && !str_contains($encoded, 'test-legacy-token')
                    && !str_contains($encoded, 'test-legacy-iframe-key');
            }));
    }

    public function test_legacy_credential_names_are_identified_as_fallback_sources(): void
    {
        config([
            'nafezly-payments.KASHIER_PAYMENT_SESSIONS_SECRET_KEY' => null,
            'nafezly-payments.KASHIER_PAYMENT_SESSIONS_API_KEY' => null,
        ]);

        Log::spy();
        Http::fake([
            'https://api.kashier.io/v3/payment/sessions' => Http::response([
                '_id' => 'session-123',
                'sessionUrl' => 'https://payments.kashier.io/session/session-123',
            ], 201),
        ]);

        app(KashierPaymentSessionService::class)->create(
            'merchant-order-123',
            125.5,
            $this->customer(),
            'Single test payment'
        );

        Log::shouldHaveReceived('info')
            ->with('Kashier Payment Sessions request prepared', Mockery::on(function (array $context) {
                return $context['credential_sources'] === [
                    'Authorization' => 'KASHIER_TOKEN',
                    'api-key' => 'KASHIER_IFRAME_KEY',
                ];
            }));
    }

    public function test_401_has_safe_logs_and_safe_user_facing_exception(): void
    {
        Log::spy();
        Http::fake([
            'https://api.kashier.io/v3/payment/sessions' => Http::response([
                'message' => 'Secret key invalid: test-session-secret',
            ], 401),
        ]);

        try {
            app(KashierPaymentSessionService::class)->create(
                'merchant-order-123',
                125.5,
                $this->customer(),
                'Single test payment'
            );
            $this->fail('Expected Payment Sessions to reject the request.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'The selected payment method is temporarily unavailable.',
                $exception->getMessage()
            );
            $this->assertStringNotContainsString('test-session-secret', $exception->getMessage());
        }

        Log::shouldHaveReceived('warning')
            ->with('Kashier Payment Sessions request rejected', Mockery::on(function (array $context) {
                $encoded = json_encode($context);

                return $context['http_status'] === 401
                    && $context['endpoint_host'] === 'api.kashier.io'
                    && $context['response_message'] === 'Authentication rejected by Kashier.'
                    && !str_contains($encoded, 'test-session-secret')
                    && !str_contains($encoded, 'Secret key invalid');
            }));
    }

    private function customer(): User
    {
        $customer = new User(['email' => 'student@example.test']);
        $customer->id = 42;

        return $customer;
    }
}
