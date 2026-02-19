<?php

namespace Tests\Feature;

use App\Domain\Client\Models\Client;
use App\Domain\Invoice\Models\Invoice;
use App\Mail\InvoiceReminderMail;
use App\Mail\InvoiceSentMail;
use App\Mail\PaymentReceivedMail;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MailableTest extends TestCase
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
            'plan' => 'pro',
        ]);

        $this->client = Client::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'name' => 'Mail Test Client',
            'email' => 'mail-client@test.com',
        ]);

        $this->invoice = Invoice::create([
            'tenant_id' => 1,
            'user_id' => $this->user->id,
            'client_id' => $this->client->id,
            'number' => 'INV-MAIL-001',
            'subtotal' => 10000,
            'tax' => 1800,
            'total' => 11800,
            'status' => 'sent',
            'currency' => 'XOF',
            'due_date' => now()->addDays(30),
        ]);
    }

    /** @test */
    public function welcome_mail_can_be_rendered()
    {
        $mailable = new WelcomeMail($this->user);
        $mailable->assertSeeInHtml($this->user->name);
        $mailable->assertSeeInHtml('Bienvenue');
    }

    /** @test */
    public function welcome_mail_has_correct_subject()
    {
        $mailable = new WelcomeMail($this->user);
        $mailable->assertHasSubject('Bienvenue sur ' . config('app.name') . ' ! 🎉');
    }

    /** @test */
    public function payment_received_mail_can_be_rendered()
    {
        $mailable = new PaymentReceivedMail($this->invoice, 11800, 'Stripe');
        $mailable->assertSeeInHtml('Paiement reçu');
        $mailable->assertSeeInHtml('INV-MAIL-001');
        $mailable->assertSeeInHtml('Stripe');
    }

    /** @test */
    public function payment_received_mail_has_correct_subject()
    {
        $mailable = new PaymentReceivedMail($this->invoice, 11800);
        $mailable->assertHasSubject('Paiement reçu — Facture INV-MAIL-001');
    }

    /** @test */
    public function invoice_sent_mail_can_be_rendered()
    {
        $mailable = new InvoiceSentMail($this->invoice);
        $mailable->assertSeeInHtml('Nouvelle facture');
        $mailable->assertSeeInHtml('INV-MAIL-001');
    }

    /** @test */
    public function invoice_sent_mail_has_correct_subject()
    {
        $mailable = new InvoiceSentMail($this->invoice);
        $mailable->assertHasSubject('Nouvelle facture INV-MAIL-001');
    }

    /** @test */
    public function invoice_reminder_mail_before_due_date()
    {
        $this->invoice->update(['due_date' => now()->addDays(5)]);
        $mailable = new InvoiceReminderMail($this->invoice->fresh());

        $mailable->assertSeeInHtml('Rappel');
        $mailable->assertSeeInHtml('INV-MAIL-001');
    }

    /** @test */
    public function invoice_reminder_mail_overdue()
    {
        $this->invoice->update(['due_date' => now()->subDays(10)]);
        $mailable = new InvoiceReminderMail($this->invoice->fresh());

        $mailable->assertSeeInHtml('INV-MAIL-001');
        $mailable->assertSeeInHtml('en retard');
    }
}
