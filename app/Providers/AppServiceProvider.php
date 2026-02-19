<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

// Repository Interfaces
use App\Domain\Invoice\Repositories\InvoiceRepositoryInterface;
use App\Domain\Client\Repositories\ClientRepositoryInterface;
use App\Domain\Payment\Repositories\PaymentRepositoryInterface;

// Repository Implementations
use App\Infrastructure\Persistence\Repositories\InvoiceRepository;
use App\Infrastructure\Persistence\Repositories\ClientRepository;
use App\Infrastructure\Persistence\Repositories\PaymentRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Empêcher Sanctum de charger ses propres migrations (déjà publiées)
        Sanctum::ignoreMigrations();

        // Lier les interfaces aux implémentations (Dependency Injection)
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
