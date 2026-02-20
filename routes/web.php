<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\PublicInvoiceController;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Tenant;

use Illuminate\Auth\Events\Registered;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home Page - Landing page marketing or redirect if authenticated
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect('/admin');
        }
        return redirect('/client');
    }
    
    // Statistiques réelles pour la landing page
    $stats = [
        'users' => User::count(),
        'tenants' => class_exists(Tenant::class) ? Tenant::count() : User::where('role', 'admin')->count(),
        'invoices' => Invoice::count(),
        'total_invoiced' => Invoice::where('status', 'paid')->sum('total') ?? 0,
    ];
    
    // Show marketing landing page for non-authenticated users
    return view('welcome', compact('stats'));
})->name('home');

// About Page
Route::get('/about', function () {
    return view('about');
})->name('about');

// Legal Pages
Route::get('/conditions-generales', function () {
    return view('legal.terms');
})->name('legal.terms');

Route::get('/politique-confidentialite', function () {
    return view('legal.privacy');
})->name('legal.privacy');

Route::get('/mentions-legales', function () {
    return view('legal.mentions');
})->name('legal.mentions');

// Login route - Page de connexion dédiée pour les clients
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.submit');

// Registration with Plan Selection
Route::get('/register', [App\Http\Controllers\Auth\RegisterWithPlanController::class, 'showRegistrationForm'])->name('register');
Route::post('/register-with-plan', [App\Http\Controllers\Auth\RegisterWithPlanController::class, 'register'])->name('register.with-plan');

// Client Routes (Authenticated + Verified + Client Role)
Route::middleware(['auth', 'verified', 'client'])->prefix('client')->name('client.')->group(function () {
    // Client Home
    Route::get('/', [App\Http\Controllers\Dashboard\DashboardController::class, 'index'])->name('index');
    
    // Billing / Plans
    Route::get('/billing', [App\Http\Controllers\Dashboard\BillingController::class, 'index'])->name('billing');
    Route::post('/billing/upgrade', [App\Http\Controllers\Dashboard\BillingController::class, 'upgrade'])->name('billing.upgrade');
    Route::post('/billing/downgrade', [App\Http\Controllers\Dashboard\BillingController::class, 'downgrade'])->name('billing.downgrade');
    Route::post('/billing/cancel', [App\Http\Controllers\Dashboard\BillingController::class, 'cancel'])->name('billing.cancel');
    
    // Invoices
    Route::get('/invoices', [App\Http\Controllers\Dashboard\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [App\Http\Controllers\Dashboard\InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [App\Http\Controllers\Dashboard\InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{invoice}', [App\Http\Controllers\Dashboard\InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/edit', [App\Http\Controllers\Dashboard\InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('/invoices/{invoice}', [App\Http\Controllers\Dashboard\InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{invoice}', [App\Http\Controllers\Dashboard\InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::post('/invoices/{invoice}/send', [App\Http\Controllers\Dashboard\InvoiceController::class, 'send'])->name('invoices.send');
    Route::post('/invoices/{invoice}/duplicate', [App\Http\Controllers\Dashboard\InvoiceController::class, 'duplicate'])->name('invoices.duplicate');
    Route::patch('/invoices/{invoice}/status', [App\Http\Controllers\Dashboard\InvoiceController::class, 'changeStatus'])->name('invoices.status');
    Route::get('/invoices/{invoice}/download', [App\Http\Controllers\Dashboard\InvoiceController::class, 'download'])->name('invoices.download');
    
    // Invoice API endpoints for autocomplete
    Route::get('/api/clients/search', [App\Http\Controllers\Dashboard\InvoiceController::class, 'searchClients'])->name('api.clients.search');
    Route::get('/api/products/search', [App\Http\Controllers\Dashboard\InvoiceController::class, 'searchProducts'])->name('api.products.search');
    
    // Payments
    Route::get('/payments', [App\Http\Controllers\Dashboard\PaymentController::class, 'index'])->name('payments.index');
    
    // Clients (Pro+)
    Route::middleware(['plan:pro,enterprise'])->group(function () {
        Route::get('/clients', [App\Http\Controllers\Dashboard\ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/create', [App\Http\Controllers\Dashboard\ClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [App\Http\Controllers\Dashboard\ClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}', [App\Http\Controllers\Dashboard\ClientController::class, 'show'])->name('clients.show');
        Route::get('/clients/{client}/edit', [App\Http\Controllers\Dashboard\ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [App\Http\Controllers\Dashboard\ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [App\Http\Controllers\Dashboard\ClientController::class, 'destroy'])->name('clients.destroy');
    });
    
    // Products (tous les plans)
    Route::get('/products', [App\Http\Controllers\Dashboard\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [App\Http\Controllers\Dashboard\ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\Dashboard\ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [App\Http\Controllers\Dashboard\ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [App\Http\Controllers\Dashboard\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [App\Http\Controllers\Dashboard\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [App\Http\Controllers\Dashboard\ProductController::class, 'destroy'])->name('products.destroy');
    
    // Analytics (Pro+)
    Route::middleware(['plan:pro,enterprise'])->group(function () {
        Route::get('/analytics', [App\Http\Controllers\Dashboard\AnalyticsController::class, 'index'])->name('analytics.index');
    });

    // Credit Notes / Avoirs (Pro+)
    Route::middleware(['plan:pro,enterprise'])->group(function () {
        Route::get('/credit-notes', [App\Http\Controllers\Dashboard\CreditNoteController::class, 'index'])->name('credit-notes.index');
        Route::get('/credit-notes/create', [App\Http\Controllers\Dashboard\CreditNoteController::class, 'create'])->name('credit-notes.create');
        Route::post('/credit-notes', [App\Http\Controllers\Dashboard\CreditNoteController::class, 'store'])->name('credit-notes.store');
        Route::get('/credit-notes/{creditNote}', [App\Http\Controllers\Dashboard\CreditNoteController::class, 'show'])->name('credit-notes.show');
        Route::get('/credit-notes/{creditNote}/edit', [App\Http\Controllers\Dashboard\CreditNoteController::class, 'edit'])->name('credit-notes.edit');
        Route::put('/credit-notes/{creditNote}', [App\Http\Controllers\Dashboard\CreditNoteController::class, 'update'])->name('credit-notes.update');
        Route::patch('/credit-notes/{creditNote}/status', [App\Http\Controllers\Dashboard\CreditNoteController::class, 'changeStatus'])->name('credit-notes.status');
        Route::delete('/credit-notes/{creditNote}', [App\Http\Controllers\Dashboard\CreditNoteController::class, 'destroy'])->name('credit-notes.destroy');
    });

    // Recurring Invoices / Factures Récurrentes (Pro+)
    Route::middleware(['plan:pro,enterprise'])->group(function () {
        Route::get('/recurring-invoices', [App\Http\Controllers\Dashboard\RecurringInvoiceController::class, 'index'])->name('recurring-invoices.index');
        Route::get('/recurring-invoices/create', [App\Http\Controllers\Dashboard\RecurringInvoiceController::class, 'create'])->name('recurring-invoices.create');
        Route::post('/recurring-invoices', [App\Http\Controllers\Dashboard\RecurringInvoiceController::class, 'store'])->name('recurring-invoices.store');
        Route::get('/recurring-invoices/{recurringInvoice}', [App\Http\Controllers\Dashboard\RecurringInvoiceController::class, 'show'])->name('recurring-invoices.show');
        Route::get('/recurring-invoices/{recurringInvoice}/edit', [App\Http\Controllers\Dashboard\RecurringInvoiceController::class, 'edit'])->name('recurring-invoices.edit');
        Route::put('/recurring-invoices/{recurringInvoice}', [App\Http\Controllers\Dashboard\RecurringInvoiceController::class, 'update'])->name('recurring-invoices.update');
        Route::post('/recurring-invoices/{recurringInvoice}/toggle', [App\Http\Controllers\Dashboard\RecurringInvoiceController::class, 'toggleActive'])->name('recurring-invoices.toggle');
        Route::delete('/recurring-invoices/{recurringInvoice}', [App\Http\Controllers\Dashboard\RecurringInvoiceController::class, 'destroy'])->name('recurring-invoices.destroy');
    });
    
    // Profile
    Route::get('/profile', [App\Http\Controllers\Dashboard\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Dashboard\ProfileController::class, 'update'])->name('profile.update');
    
    // Settings
    Route::get('/settings', [App\Http\Controllers\Dashboard\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\Dashboard\SettingsController::class, 'update'])->name('settings.update');
    
    // Templates de factures
    Route::get('/templates', [App\Http\Controllers\Dashboard\TemplateController::class, 'index'])->name('templates.index');
    Route::post('/templates/{templateId}/select', [App\Http\Controllers\Dashboard\TemplateController::class, 'select'])->name('templates.select');
    Route::get('/templates/{templateId}/preview', [App\Http\Controllers\Dashboard\TemplateController::class, 'preview'])->name('templates.preview');
    
    // Exports CSV (Pro+)
    Route::middleware(['plan:pro,enterprise'])->prefix('exports')->name('exports.')->group(function () {
        Route::get('/clients', [App\Http\Controllers\ExportController::class, 'clients'])->name('clients');
        Route::get('/invoices', [App\Http\Controllers\ExportController::class, 'invoices'])->name('invoices');
        Route::get('/payments', [App\Http\Controllers\ExportController::class, 'payments'])->name('payments');
        Route::get('/products', [App\Http\Controllers\ExportController::class, 'products'])->name('products');
    });

    // Two-Factor Authentication (Pro+)
    Route::middleware(['plan:pro,enterprise'])->group(function () {
        Route::get('/two-factor/enable', [App\Http\Controllers\Dashboard\TwoFactorController::class, 'enable'])->name('two-factor.enable');
        Route::post('/two-factor/confirm', [App\Http\Controllers\Dashboard\TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
        Route::delete('/two-factor/disable', [App\Http\Controllers\Dashboard\TwoFactorController::class, 'disable'])->name('two-factor.disable');
        Route::get('/two-factor/recovery-codes', [App\Http\Controllers\Dashboard\TwoFactorController::class, 'showRecoveryCodes'])->name('two-factor.recovery-codes');
        Route::post('/two-factor/recovery-codes/regenerate', [App\Http\Controllers\Dashboard\TwoFactorController::class, 'regenerateRecoveryCodes'])->name('two-factor.recovery-codes.regenerate');
    });
    
    // Team Management (Enterprise only)
    Route::middleware(['plan:enterprise'])->prefix('team')->name('team.')->group(function () {
        Route::get('/', [App\Http\Controllers\Dashboard\TeamController::class, 'index'])->name('index');
        Route::post('/invite', [App\Http\Controllers\Dashboard\TeamController::class, 'invite'])->name('invite');
        Route::delete('/invitation/{invitation}', [App\Http\Controllers\Dashboard\TeamController::class, 'cancelInvitation'])->name('invitation.cancel');
        Route::post('/invitation/{invitation}/resend', [App\Http\Controllers\Dashboard\TeamController::class, 'resendInvitation'])->name('invitation.resend');
        Route::patch('/member/{member}/role', [App\Http\Controllers\Dashboard\TeamController::class, 'updateRole'])->name('member.role');
        Route::delete('/member/{member}', [App\Http\Controllers\Dashboard\TeamController::class, 'removeMember'])->name('member.remove');
    });
    
    // API Keys (Enterprise only)
    Route::middleware(['plan:enterprise'])->prefix('api-keys')->name('api-keys.')->group(function () {
        Route::get('/', [App\Http\Controllers\Dashboard\ApiKeyController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Dashboard\ApiKeyController::class, 'store'])->name('store');
        Route::patch('/{apiKey}', [App\Http\Controllers\Dashboard\ApiKeyController::class, 'update'])->name('update');
        Route::patch('/{apiKey}/revoke', [App\Http\Controllers\Dashboard\ApiKeyController::class, 'revoke'])->name('revoke');
        Route::delete('/{apiKey}', [App\Http\Controllers\Dashboard\ApiKeyController::class, 'destroy'])->name('destroy');
        Route::get('/documentation', [App\Http\Controllers\Dashboard\ApiKeyController::class, 'documentation'])->name('documentation');
    });
});

// Authentication Routes
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// Team Invitations (public routes)
Route::prefix('invitation')->name('invitation.')->group(function () {
    Route::get('/{token}', [App\Http\Controllers\InvitationController::class, 'show'])->name('show');
    Route::post('/{token}/accept', [App\Http\Controllers\InvitationController::class, 'accept'])->name('accept');
    Route::post('/{token}/decline', [App\Http\Controllers\InvitationController::class, 'decline'])->name('decline');
});

// Stripe Webhook
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// Public Invoice Routes
Route::prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/{uuid}', [PublicInvoiceController::class, 'show'])->name('public');
    Route::get('/{uuid}/download', [PublicInvoiceController::class, 'download'])->name('download');
    Route::get('/{uuid}/pdf', [PublicInvoiceController::class, 'pdf'])->name('pdf');
    Route::post('/{uuid}/create-payment-intent', [PublicInvoiceController::class, 'createPaymentIntent'])->name('create-payment-intent');
    Route::post('/{uuid}/payment/initialize', [PublicInvoiceController::class, 'initializePayment'])->name('payment.initialize');
    Route::get('/{uuid}/payment/callback', [PublicInvoiceController::class, 'paymentCallback'])->name('payment.callback');
    Route::get('/{uuid}/payment-success', [PublicInvoiceController::class, 'paymentSuccess'])->name('payment-success');
    Route::get('/{uuid}/payment/success', [PublicInvoiceController::class, 'paymentSuccessAlt'])->name('payment.success');
    Route::get('/{uuid}/payment/error', [PublicInvoiceController::class, 'paymentError'])->name('payment.error');
});

// Payment Gateway Webhooks (exclude from CSRF verification)
Route::post('/webhooks/paystack', [PublicInvoiceController::class, 'paystackWebhook'])->name('webhooks.paystack');
Route::post('/webhooks/flutterwave', [PublicInvoiceController::class, 'flutterwaveWebhook'])->name('webhooks.flutterwave');
Route::post('/webhooks/wave', [PublicInvoiceController::class, 'waveWebhook'])->name('webhooks.wave');
Route::post('/webhooks/mpesa', [PublicInvoiceController::class, 'mpesaWebhook'])->name('webhooks.mpesa');
Route::post('/webhooks/fedapay', [PublicInvoiceController::class, 'fedapayWebhook'])->name('webhooks.fedapay');
Route::post('/webhooks/kkiapay', [PublicInvoiceController::class, 'kkiapayWebhook'])->name('webhooks.kkiapay');
Route::post('/webhooks/cinetpay', [PublicInvoiceController::class, 'cinetpayWebhook'])->name('webhooks.cinetpay');

// Legacy route (backward compatibility)
Route::get('/i/{public_hash}', [PublicInvoiceController::class, 'showByHash'])->name('invoice.public');
