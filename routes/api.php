<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventTestController;
use App\Http\Controllers\Api\HealthCheckController;
use App\Http\Controllers\Api\InvoiceApiController;
use App\Http\Controllers\Api\PaymentApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Version 1)
|--------------------------------------------------------------------------
*/

// Health Check Routes (monitoring & observability)
Route::controller(HealthCheckController::class)->group(function () {
    Route::get('/health', 'index')->name('api.health');
    Route::get('/health/detailed', 'detailed')->name('api.health.detailed');
    Route::get('/health/ready', 'ready')->name('api.health.ready');
    Route::get('/health/alive', 'alive')->name('api.health.alive');
    Route::get('/metrics', 'metrics')->name('api.metrics');
});

// Routes de test pour événements (À SUPPRIMER EN PRODUCTION)
Route::prefix('test/events')->controller(EventTestController::class)->group(function () {
    Route::get('/invoice-created', 'testInvoiceCreated');
    Route::get('/invoice-overdue', 'testInvoiceOverdue');
    Route::get('/payment-received', 'testPaymentReceived');
    Route::get('/payment-failed', 'testPaymentFailed');
    Route::get('/queue-stats', 'queueStats');
    Route::post('/full-workflow', 'testFullWorkflow');
});

// Routes d'authentification (publiques)
Route::prefix('v1/auth')->controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->middleware('throttle:5,1')->name('api.auth.login');
    Route::post('/register', 'register')->middleware('throttle:3,1')->name('api.auth.register');
});

// Routes d'authentification (protégées)
Route::middleware(['auth.sanctum', 'throttle:60,1'])->prefix('v1/auth')->controller(AuthController::class)->group(function () {
    Route::get('/me', 'me')->name('api.auth.me');
    Route::post('/logout', 'logout')->name('api.auth.logout');
    Route::post('/logout-all', 'logoutAll')->name('api.auth.logout-all');
    Route::post('/refresh', 'refresh')->name('api.auth.refresh');
    Route::get('/tokens', 'tokens')->name('api.auth.tokens');
    Route::delete('/tokens/{id}', 'revokeToken')->name('api.auth.revoke-token');
});

// Routes authentifiées avec rate limiting
Route::middleware(['auth.sanctum', 'tenant.resolve', 'throttle:60,1'])->prefix('v1')->group(function () {
    
    // User info
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load('tenant'),
        ]);
    });

    // Invoices (rate limit: 30 requêtes par minute)
    Route::middleware('throttle:30,1')->prefix('invoices')->controller(InvoiceApiController::class)->group(function () {
        Route::post('/', 'store')->name('api.invoices.store');
        Route::post('/{id}/pdf', 'generatePdf')->name('api.invoices.generate-pdf');
        Route::get('/{id}/download', 'downloadPdf')->name('api.invoices.download-pdf');
    });

    // Payments (rate limit: 10 requêtes par minute - plus restrictif)
    Route::middleware('throttle:10,1')->prefix('payments')->controller(PaymentApiController::class)->group(function () {
        Route::post('/', 'initiatePayment')->name('api.payments.initiate');
        Route::post('/{id}/confirm', 'confirmPayment')->name('api.payments.confirm');
    });
});
