<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Invoice Events
        \App\Domain\Invoice\Events\InvoiceCreated::class => [
            \App\Application\Listeners\Invoice\SendInvoiceNotification::class,
            \App\Application\Listeners\Invoice\GenerateInvoicePdf::class,
        ],

        \App\Domain\Invoice\Events\InvoicePaid::class => [
            \App\Application\Listeners\Invoice\UpdateInvoiceStatus::class,
        ],

        \App\Domain\Invoice\Events\InvoiceOverdue::class => [
            \App\Application\Listeners\Invoice\SendOverdueReminder::class,
        ],

        // Payment Events
        \App\Domain\Payment\Events\PaymentReceived::class => [
            \App\Application\Listeners\Payment\LogPaymentEvent::class,
            \App\Application\Listeners\Payment\NotifyAccountant::class,
        ],

        \App\Domain\Payment\Events\PaymentFailed::class => [
            \App\Application\Listeners\Payment\HandlePaymentFailure::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
