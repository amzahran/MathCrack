<?php

namespace Tests\Feature;

use App\Http\Controllers\Web\Back\Users\Payment\PaymentController;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Tests\TestCase;

// Ensure isolated worktrees exercise the controller from their own checkout.
require_once dirname(__DIR__, 2) . '/app/Http/Controllers/Web/Back/Users/Payment/PaymentController.php';

class KashierPaidReturnFallbackTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('category')->default('lecture');
            $table->string('type')->default('single');
            $table->string('type_value')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->string('pid')->nullable();
            $table->timestamps();
        });
    }

    public function test_paid_pending_invoice_redirects_to_success_and_clears_session_reference(): void
    {
        [$user, $invoice] = $this->createUserAndInvoice('paid');
        $request = $this->failedReturnRequest($user, $invoice);

        $response = app(PaymentController::class)->paymentFailed($request);

        $this->assertSame(
            route('dashboard.users.payment-success', ['invoice_id' => $invoice->id]),
            $response->getTargetUrl()
        );
        $this->assertNull(session('pending_kashier_invoice_id'));
        $this->assertNull(session('pending_kashier_invoice_pid'));
    }

    public function test_pending_invoice_does_not_redirect_to_success(): void
    {
        [$user, $invoice] = $this->createUserAndInvoice('pending');
        $request = $this->failedReturnRequest($user, $invoice);

        $response = app(PaymentController::class)->paymentFailed($request);

        $this->assertInstanceOf(View::class, $response);
        $this->assertSame($invoice->id, $response->getData()['invoice']->id);
        $this->assertSame($invoice->id, session('pending_kashier_invoice_id'));
    }

    public function test_stale_paid_invoice_does_not_redirect_to_success(): void
    {
        [$user, $invoice] = $this->createUserAndInvoice('paid');
        $invoice->forceFill([
            'created_at' => now()->subMinutes(31),
            'updated_at' => now()->subMinutes(31),
        ])->save();
        $request = $this->failedReturnRequest($user, $invoice);

        $response = app(PaymentController::class)->paymentFailed($request);

        $this->assertInstanceOf(View::class, $response);
        $this->assertNull($response->getData()['invoice']);
    }

    public function test_paid_invoice_for_another_user_does_not_redirect_to_success(): void
    {
        [$owner, $invoice] = $this->createUserAndInvoice('paid');
        $currentUser = User::create(['email' => 'current@example.test']);
        $request = $this->failedReturnRequest($currentUser, $invoice);

        $response = app(PaymentController::class)->paymentFailed($request);

        $this->assertInstanceOf(View::class, $response);
        $this->assertNull($response->getData()['invoice']);
        $this->assertNotSame($owner->id, $currentUser->id);
    }

    private function createUserAndInvoice(string $status): array
    {
        $user = User::create(['email' => uniqid('user-', true) . '@example.test']);
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'status' => $status,
            'pid' => uniqid('kashier-', true),
        ]);

        return [$user, $invoice];
    }

    private function failedReturnRequest(User $user, Invoice $invoice): Request
    {
        $this->actingAs($user);
        $session = app('session')->driver();
        $session->put([
            'pending_kashier_invoice_id' => $invoice->id,
            'pending_kashier_invoice_pid' => $invoice->pid,
        ]);

        $request = Request::create('/users/payment/failed', 'GET');
        $request->setLaravelSession($session);

        return $request;
    }
}
