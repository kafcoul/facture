<?php

namespace App\Providers;

use App\Domain\Client\Models\Client;
use App\Domain\Invoice\Models\CreditNote;
use App\Domain\Invoice\Models\Invoice;
use App\Domain\Invoice\Models\RecurringInvoice;
use App\Domain\Payment\Models\Payment;
use App\Models\Product;
use App\Policies\ClientPolicy;
use App\Policies\CreditNotePolicy;
use App\Policies\InvoicePolicy;
use App\Policies\ProductPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\RecurringInvoicePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Client::class => ClientPolicy::class,
        CreditNote::class => CreditNotePolicy::class,
        Invoice::class => InvoicePolicy::class,
        Product::class => ProductPolicy::class,
        Payment::class => PaymentPolicy::class,
        RecurringInvoice::class => RecurringInvoicePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
