<?php

namespace Tests\Feature;

use App\Domain\Invoice\Models\Invoice;
use App\Domain\Payment\Models\Payment;
use App\Domain\Client\Models\Client;
use App\Models\User;
use App\Models\WebhookLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Client $client;
    protected Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'tenant_id' => 1,
            'role' => 'client',
        ]);

        $this->client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Webhook Client',
            'email' => 'webhook@test.com',
        ]);

        $this->invoice = Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'number' => 'INV-WH-001',
            'subtotal' => 10000,
            'tax' => 1800,
            'total' => 11800,
            'status' => 'sent',
            'due_date' => now()->addDays(30),
        ]);
    }

    // ─── Stripe Webhook: checkout.session.completed ────

    /** @test */
    public function stripe_webhook_handles_checkout_completed()
    {
        $response = $this->postJson(route('stripe.webhook'), [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'metadata' => [
                        'invoice_id' => $this->invoice->id,
                    ],
                    'payment_intent' => 'pi_test_123',
                ],
            ],
        ]);

        $response->assertOk();
        $this->invoice->refresh();
        $this->assertEquals('paid', $this->invoice->status);
        $this->assertNotNull($this->invoice->paid_at);
    }

    // ─── Stripe Webhook: payment_intent.succeeded ──────

    /** @test */
    public function stripe_webhook_handles_payment_intent_succeeded()
    {
        $payment = Payment::create([
            'tenant_id' => 1,
            'invoice_id' => $this->invoice->id,
            'user_id' => $this->user->id,
            'amount' => 11800,
            'gateway' => 'stripe',
            'transaction_id' => 'pi_succeed_test',
            'status' => 'pending',
            'currency' => 'XOF',
        ]);

        $response = $this->postJson(route('stripe.webhook'), [
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_succeed_test',
                    'metadata' => [
                        'invoice_id' => $this->invoice->id,
                    ],
                ],
            ],
        ]);

        $response->assertOk();
        $payment->refresh();
        $this->assertEquals('completed', $payment->status);
        $this->assertNotNull($payment->completed_at);
    }

    // ─── Stripe Webhook: payment_intent.payment_failed ─

    /** @test */
    public function stripe_webhook_handles_payment_failed()
    {
        $payment = Payment::create([
            'tenant_id' => 1,
            'invoice_id' => $this->invoice->id,
            'user_id' => $this->user->id,
            'amount' => 11800,
            'gateway' => 'stripe',
            'transaction_id' => 'pi_fail_test',
            'status' => 'pending',
            'currency' => 'XOF',
        ]);

        $response = $this->postJson(route('stripe.webhook'), [
            'type' => 'payment_intent.payment_failed',
            'data' => [
                'object' => [
                    'id' => 'pi_fail_test',
                    'last_payment_error' => [
                        'message' => 'Card declined',
                    ],
                ],
            ],
        ]);

        $response->assertOk();
        $payment->refresh();
        $this->assertEquals('failed', $payment->status);
        $this->assertEquals('Card declined', $payment->failure_reason);
    }

    // ─── Stripe Webhook: charge.refunded ───────────────

    /** @test */
    public function stripe_webhook_handles_charge_refunded()
    {
        $payment = Payment::create([
            'tenant_id' => 1,
            'invoice_id' => $this->invoice->id,
            'user_id' => $this->user->id,
            'amount' => 11800,
            'gateway' => 'stripe',
            'transaction_id' => 'pi_refund_test',
            'status' => 'completed',
            'currency' => 'XOF',
            'completed_at' => now(),
        ]);

        $response = $this->postJson(route('stripe.webhook'), [
            'type' => 'charge.refunded',
            'data' => [
                'object' => [
                    'payment_intent' => 'pi_refund_test',
                    'amount' => 1180000,
                    'amount_refunded' => 1180000,
                ],
            ],
        ]);

        $response->assertOk();
        $payment->refresh();
        $this->assertEquals('refunded', $payment->status);
    }

    // ─── Stripe Webhook: unknown event ─────────────────

    /** @test */
    public function stripe_webhook_ignores_unknown_events()
    {
        $response = $this->postJson(route('stripe.webhook'), [
            'type' => 'some.unknown.event',
            'data' => ['object' => []],
        ]);

        $response->assertOk();
        $response->assertJson(['status' => 'ignored']);
    }

    // ─── Webhook Logging ───────────────────────────────

    /** @test */
    public function webhook_events_are_logged()
    {
        $this->postJson(route('stripe.webhook'), [
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'metadata' => [
                        'invoice_id' => $this->invoice->id,
                    ],
                ],
            ],
        ]);

        $this->assertDatabaseHas('webhook_logs', [
            'gateway' => 'stripe',
            'event_type' => 'checkout.session.completed',
            'processed' => true,
        ]);
    }

    /** @test */
    public function webhook_log_model_filters_by_gateway()
    {
        WebhookLog::log('stripe', 'charge.succeeded', ['test' => true]);
        WebhookLog::log('paystack', 'charge.success', ['test' => true]);
        WebhookLog::log('stripe', 'payment_intent.succeeded', ['test' => true]);

        $stripeLogs = WebhookLog::forGateway('stripe')->get();
        $this->assertCount(2, $stripeLogs);

        $paystackLogs = WebhookLog::forGateway('paystack')->get();
        $this->assertCount(1, $paystackLogs);
    }

    /** @test */
    public function webhook_log_model_filters_recent()
    {
        // Create an "old" log by setting the timestamp manually
        $oldLog = WebhookLog::create([
            'gateway' => 'stripe',
            'event_type' => 'old.event',
            'payload' => [],
            'processed' => true,
        ]);
        // Manually update created_at to 30 days ago
        WebhookLog::where('id', $oldLog->id)->update(['created_at' => now()->subDays(30)]);

        WebhookLog::log('stripe', 'recent.event', []);

        $recentLogs = WebhookLog::recent(7)->get();
        $this->assertCount(1, $recentLogs);
        $this->assertEquals('recent.event', $recentLogs->first()->event_type);
    }
}
