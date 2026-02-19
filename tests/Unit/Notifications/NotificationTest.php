<?php

namespace Tests\Unit\Notifications;

use App\Domain\Client\Models\Client;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Payment\Models\Payment;
use App\Models\User;
use App\Notifications\InvoiceCreatedNotification;
use App\Notifications\InvoiceOverdueNotification;
use App\Notifications\InvoiceSentToClientNotification;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\WelcomeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Tests\TestCase;

class NotificationTest extends TestCase
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
            'name' => 'Test User',
            'plan' => 'pro',
        ]);

        $this->client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Client Dakar',
            'email' => 'client@dakar.sn',
        ]);

        $this->invoice = Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'number' => 'INV-2025-001',
            'due_date' => now()->addDays(30),
            'subtotal' => 50000,
            'tax' => 9000,
            'total' => 59000,
            'status' => 'draft',
        ]);
    }

    // ═══════════════════════════════════════════════════
    //  WelcomeNotification
    // ═══════════════════════════════════════════════════

    /** @test */
    public function welcome_notification_uses_mail_and_database_channels()
    {
        $notification = new WelcomeNotification('pro');

        $this->assertEquals(['mail', 'database'], $notification->via($this->user));
    }

    /** @test */
    public function welcome_notification_generates_mail_message()
    {
        $notification = new WelcomeNotification('pro');
        $mail = $notification->toMail($this->user);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertStringContainsString('Bienvenue', $mail->subject);
        $this->assertStringContainsString('Test User', $mail->subject);
    }

    /** @test */
    public function welcome_notification_stores_database_data()
    {
        $notification = new WelcomeNotification('pro');
        $data = $notification->toArray($this->user);

        $this->assertEquals('welcome', $data['type']);
        $this->assertEquals('pro', $data['plan']);
        $this->assertArrayHasKey('message', $data);
    }

    /** @test */
    public function welcome_notification_handles_all_plans()
    {
        foreach (['starter', 'pro', 'enterprise'] as $plan) {
            $notification = new WelcomeNotification($plan);
            $mail = $notification->toMail($this->user);

            $this->assertInstanceOf(MailMessage::class, $mail);
        }
    }

    // ═══════════════════════════════════════════════════
    //  InvoiceCreatedNotification
    // ═══════════════════════════════════════════════════

    /** @test */
    public function invoice_created_notification_uses_mail_and_database()
    {
        $notification = new InvoiceCreatedNotification($this->invoice);

        $this->assertEquals(['mail', 'database'], $notification->via($this->user));
    }

    /** @test */
    public function invoice_created_notification_mail_contains_invoice_info()
    {
        $notification = new InvoiceCreatedNotification($this->invoice);
        $mail = $notification->toMail($this->user);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertStringContainsString('INV-2025-001', $mail->subject);
    }

    /** @test */
    public function invoice_created_notification_stores_correct_data()
    {
        $notification = new InvoiceCreatedNotification($this->invoice);
        $data = $notification->toArray($this->user);

        $this->assertEquals('invoice_created', $data['type']);
        $this->assertEquals($this->invoice->id, $data['invoice_id']);
        $this->assertEquals('INV-2025-001', $data['invoice_number']);
        $this->assertEquals(59000, $data['amount']);
        $this->assertArrayHasKey('currency', $data);
    }

    // ═══════════════════════════════════════════════════
    //  InvoiceOverdueNotification
    // ═══════════════════════════════════════════════════

    /** @test */
    public function overdue_notification_uses_mail_and_database()
    {
        $notification = new InvoiceOverdueNotification($this->invoice, 5);

        $this->assertEquals(['mail', 'database'], $notification->via($this->user));
    }

    /** @test */
    public function overdue_notification_mail_shows_days_overdue()
    {
        $notification = new InvoiceOverdueNotification($this->invoice, 7);
        $mail = $notification->toMail($this->user);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertStringContainsString('7 jours', $mail->subject);
        $this->assertStringContainsString('INV-2025-001', $mail->subject);
    }

    /** @test */
    public function overdue_notification_uses_correct_urgency_emoji()
    {
        // < 14 days: 🟡
        $notification5 = new InvoiceOverdueNotification($this->invoice, 5);
        $mail5 = $notification5->toMail($this->user);
        $this->assertStringContainsString('🟡', $mail5->subject);

        // 15-30 days: 🟠
        $notification20 = new InvoiceOverdueNotification($this->invoice, 20);
        $mail20 = $notification20->toMail($this->user);
        $this->assertStringContainsString('🟠', $mail20->subject);

        // > 30 days: 🔴
        $notification45 = new InvoiceOverdueNotification($this->invoice, 45);
        $mail45 = $notification45->toMail($this->user);
        $this->assertStringContainsString('🔴', $mail45->subject);
    }

    /** @test */
    public function overdue_notification_stores_correct_data()
    {
        $notification = new InvoiceOverdueNotification($this->invoice, 10);
        $data = $notification->toArray($this->user);

        $this->assertEquals('invoice_overdue', $data['type']);
        $this->assertEquals($this->invoice->id, $data['invoice_id']);
        $this->assertEquals(10, $data['days_overdue']);
        $this->assertEquals(59000, $data['amount']);
    }

    // ═══════════════════════════════════════════════════
    //  PaymentReceivedNotification
    // ═══════════════════════════════════════════════════

    /** @test */
    public function payment_received_notification_uses_mail_and_database()
    {
        $payment = Payment::create([
            'tenant_id' => 1,
            'invoice_id' => $this->invoice->id,
            'user_id' => $this->user->id,
            'amount' => 59000,
            'gateway' => 'wave',
            'status' => 'success',
            'transaction_id' => 'tx_001',
        ]);

        $notification = new PaymentReceivedNotification($payment);

        $this->assertEquals(['mail', 'database'], $notification->via($this->user));
    }

    /** @test */
    public function payment_received_notification_stores_correct_data()
    {
        $payment = Payment::create([
            'tenant_id' => 1,
            'invoice_id' => $this->invoice->id,
            'user_id' => $this->user->id,
            'amount' => 59000,
            'gateway' => 'stripe',
            'status' => 'success',
            'transaction_id' => 'tx_002',
        ]);

        $notification = new PaymentReceivedNotification($payment);
        $data = $notification->toArray($this->user);

        $this->assertEquals('payment_received', $data['type']);
        $this->assertEquals($payment->id, $data['payment_id']);
        $this->assertEquals($this->invoice->id, $data['invoice_id']);
        $this->assertEquals(59000, $data['amount']);
    }

    // ═══════════════════════════════════════════════════
    //  InvoiceSentToClientNotification
    // ═══════════════════════════════════════════════════

    /** @test */
    public function sent_to_client_notification_uses_only_mail()
    {
        $notification = new InvoiceSentToClientNotification($this->invoice);

        $this->assertEquals(['mail'], $notification->via($this->user));
    }

    /** @test */
    public function sent_to_client_notification_mail_contains_invoice_info()
    {
        $notification = new InvoiceSentToClientNotification($this->invoice);
        $mail = $notification->toMail($this->user);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertStringContainsString('INV-2025-001', $mail->subject);
    }

    // ═══════════════════════════════════════════════════
    //  Notification is queueable
    // ═══════════════════════════════════════════════════

    /** @test */
    public function all_notifications_implement_should_queue()
    {
        $this->assertInstanceOf(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            new WelcomeNotification('starter')
        );

        $this->assertInstanceOf(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            new InvoiceCreatedNotification($this->invoice)
        );

        $this->assertInstanceOf(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            new InvoiceOverdueNotification($this->invoice, 5)
        );

        $payment = Payment::create([
            'tenant_id' => 1,
            'invoice_id' => $this->invoice->id,
            'user_id' => $this->user->id,
            'amount' => 59000,
            'gateway' => 'stripe',
            'status' => 'success',
            'transaction_id' => 'tx_queue',
        ]);

        $this->assertInstanceOf(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            new PaymentReceivedNotification($payment)
        );

        $this->assertInstanceOf(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            new InvoiceSentToClientNotification($this->invoice)
        );
    }
}
