# Phase 5: Event-Driven Architecture 🎯

## Vue d'ensemble

L'architecture événementielle découple complètement les différentes parties de l'application. Les Use Cases émettent des événements, et les Listeners réagissent de manière asynchrone via les queues.

## 📋 Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                         Use Cases                            │
│  CreateInvoiceUseCase, ProcessPaymentUseCase                │
└──────────────────┬──────────────────────────────────────────┘
                   │ event(new InvoiceCreated($invoice))
                   ▼
┌─────────────────────────────────────────────────────────────┐
│                    Event Dispatcher                          │
│               (EventServiceProvider)                         │
└──────────────────┬──────────────────────────────────────────┘
                   │
         ┌─────────┴──────────┬──────────────┬────────────┐
         ▼                    ▼              ▼            ▼
┌────────────────┐  ┌──────────────┐  ┌──────────┐  ┌────────┐
│  Notification  │  │     PDF      │  │  Status  │  │  Log   │
│    Listener    │  │   Listener   │  │ Listener │  │Listener│
└────────────────┘  └──────────────┘  └──────────┘  └────────┘
         │                    │              │            │
         └──────── Queue ──────────────────────┘─────────┘
                        (Redis)
```

## 🎯 Events Créés

### Invoice Events

#### 1. **InvoiceCreated**
```php
namespace App\Domain\Invoice\Events;

class InvoiceCreated
{
    public function __construct(public Invoice $invoice) {}
    
    public function tags(): array
    {
        return [
            'invoice:created',
            "invoice:{$this->invoice->id}",
            "tenant:{$this->invoice->tenant_id}",
        ];
    }
}
```

**Déclenché par:** `CreateInvoiceUseCase->execute()`  
**Listeners:**
- `SendInvoiceNotification` - Envoyer email au client avec PDF
- `GenerateInvoicePdf` - Générer le PDF automatiquement

#### 2. **InvoicePaid**
```php
class InvoicePaid
{
    public function __construct(public Invoice $invoice) {}
}
```

**Déclenché par:** `ProcessPaymentUseCase->confirmPayment()` (quand montant total payé)  
**Listeners:**
- `UpdateInvoiceStatus` - Mettre à jour status + envoyer remerciement

#### 3. **InvoiceOverdue**
```php
class InvoiceOverdue
{
    public function __construct(
        public Invoice $invoice,
        public int $daysOverdue
    ) {}
}
```

**Déclenché par:** `CheckOverdueInvoicesJob` (schedulé quotidiennement)  
**Listeners:**
- `SendOverdueReminder` - Envoyer rappel au client

### Payment Events

#### 4. **PaymentReceived**
```php
namespace App\Domain\Payment\Events;

class PaymentReceived
{
    public function __construct(public Payment $payment) {}
    
    public function tags(): array
    {
        return [
            'payment:received',
            "payment:{$this->payment->id}",
            "invoice:{$this->payment->invoice_id}",
            "gateway:{$this->payment->gateway}",
            "tenant:{$this->payment->tenant_id}",
        ];
    }
}
```

**Déclenché par:** `ProcessPaymentUseCase->confirmPayment()` (succès)  
**Listeners:**
- `LogPaymentEvent` - Logger pour audit trail
- `NotifyAccountant` - Notifier comptable (si montant > 1M XOF)

#### 5. **PaymentFailed**
```php
class PaymentFailed
{
    public function __construct(
        public Payment $payment,
        public string $reason
    ) {}
}
```

**Déclenché par:** `ProcessPaymentUseCase->confirmPayment()` (échec)  
**Listeners:**
- `HandlePaymentFailure` - Logger + notifier client

## 🎭 Listeners Créés

### Invoice Listeners

| Listener | Event | Queue | Retries | Description |
|----------|-------|-------|---------|-------------|
| `SendInvoiceNotification` | InvoiceCreated | ✅ | 3 | Envoie email au client avec facture |
| `GenerateInvoicePdf` | InvoiceCreated | ✅ | 2 | Génère PDF en arrière-plan |
| `UpdateInvoiceStatus` | InvoicePaid | ✅ | 3 | Met à jour status + envoie remerciement |
| `SendOverdueReminder` | InvoiceOverdue | ✅ | 2 | Envoie rappel pour facture en retard |

### Payment Listeners

| Listener | Event | Queue | Retries | Description |
|----------|-------|-------|---------|-------------|
| `LogPaymentEvent` | PaymentReceived | ✅ | 5 | Log détaillé pour audit |
| `NotifyAccountant` | PaymentReceived | ✅ | 3 | Notifie comptable si > 1M XOF |
| `HandlePaymentFailure` | PaymentFailed | ✅ | 2 | Gère les échecs de paiement |

## 🔧 Configuration

### EventServiceProvider

```php
// app/Providers/EventServiceProvider.php

protected $listen = [
    // Invoice Events
    InvoiceCreated::class => [
        SendInvoiceNotification::class,
        GenerateInvoicePdf::class,
    ],
    
    InvoicePaid::class => [
        UpdateInvoiceStatus::class,
    ],
    
    InvoiceOverdue::class => [
        SendOverdueReminder::class,
    ],
    
    // Payment Events
    PaymentReceived::class => [
        LogPaymentEvent::class,
        NotifyAccountant::class,
    ],
    
    PaymentFailed::class => [
        HandlePaymentFailure::class,
    ],
];
```

### Queue Configuration

```env
# .env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

## 📊 Flux de Données Complet

### Scénario: Création de Facture

```
1. API Request → InvoiceApiController->store()
2. Controller → CreateInvoiceUseCase->execute()
3. Use Case:
   - Valide données (DTO)
   - Génère numéro
   - Calcule totaux
   - Persiste en DB
   - Dispatch: event(new InvoiceCreated($invoice))
4. Event Dispatcher:
   - Queue Job: SendInvoiceNotification
   - Queue Job: GenerateInvoicePdf
5. Listeners (async):
   - SendInvoiceNotification:
     * Charge invoice + client
     * Envoie email avec PDF
     * Met à jour metadata notification_sent_at
   - GenerateInvoicePdf:
     * Appelle GeneratePdfUseCase
     * Stocke PDF dans storage/tenants/{id}/invoices/
     * Log succès
```

### Scénario: Paiement Reçu

```
1. Webhook → StripeWebhookController->handle()
2. Controller → ProcessPaymentUseCase->confirmPayment()
3. Use Case:
   - Vérifie signature gateway
   - Marque payment comme 'completed'
   - Calcule total payé
   - Si total >= invoice.total:
     * invoice->markAsPaid()
     * Dispatch: event(new InvoicePaid($invoice))
   - Dispatch: event(new PaymentReceived($payment))
4. Event Dispatcher:
   - Queue Job: UpdateInvoiceStatus
   - Queue Job: LogPaymentEvent
   - Queue Job: NotifyAccountant (si montant > 1M)
5. Listeners (async):
   - UpdateInvoiceStatus:
     * Met à jour metadata
     * Envoie email de remerciement
   - LogPaymentEvent:
     * Log détaillé avec analytics
   - NotifyAccountant:
     * Vérifie threshold
     * Envoie notification comptable
```

### Scénario: Facture en Retard

```
1. Scheduler (daily) → CheckOverdueInvoicesJob
2. Job:
   - Récupère factures pending avec due_date < now()
   - Pour chaque facture:
     * Calcule daysOverdue
     * Si milestone (1, 7, 14, 30 jours) ET pas déjà notifié:
       → Dispatch: event(new InvoiceOverdue($invoice, $daysOverdue))
3. Event Dispatcher:
   - Queue Job: SendOverdueReminder
4. Listener:
   - SendOverdueReminder:
     * Envoie email de rappel
     * Incrémente reminder_count
     * Enregistre last_overdue_milestone
```

## ⚙️ Démarrage des Queues

### Développement

```bash
# Démarrer un worker
php artisan queue:work redis --sleep=3 --tries=3 --timeout=90

# Avec verbosity
php artisan queue:work redis --verbose

# Relancer automatiquement sur changements de code
php artisan queue:listen redis
```

### Production (Supervisor)

```ini
# /etc/supervisor/conf.d/invoice-saas-worker.conf

[program:invoice-saas-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/invoice-saas/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/invoice-saas/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Redémarrer Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start invoice-saas-worker:*
```

## 🧪 Testing Events

### Dispatch Manuellement

```php
use App\Domain\Invoice\Events\InvoiceCreated;
use App\Domain\Invoice\Models\Invoice;

// Dans Tinker ou un test
$invoice = Invoice::first();
event(new InvoiceCreated($invoice));
```

### Vérifier les Jobs en Queue

```bash
# Stats des queues
php artisan queue:monitor redis

# Failed jobs
php artisan queue:failed

# Retry un job échoué
php artisan queue:retry {job-id}

# Retry tous les jobs échoués
php artisan queue:retry all
```

### Unit Tests

```php
use Illuminate\Support\Facades\Event;

public function test_invoice_created_dispatches_event()
{
    Event::fake();
    
    $useCase = app(CreateInvoiceUseCase::class);
    $invoice = $useCase->execute($dto);
    
    Event::assertDispatched(InvoiceCreated::class, function ($event) use ($invoice) {
        return $event->invoice->id === $invoice->id;
    });
}
```

## 📈 Monitoring

### Tags pour Filtering

Tous les événements ont une méthode `tags()` pour filtrer les logs:

```php
// Dans Horizon ou logs
'invoice:created'
'invoice:123'
'tenant:1'
'payment:received'
'gateway:stripe'
```

### Laravel Horizon (Recommandé)

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon
```

Dashboard: `http://localhost:8000/horizon`

## ✅ Avantages

1. **Découplage Total:** Use Cases ne connaissent pas les Listeners
2. **Scalabilité:** Listeners exécutés en parallèle via workers
3. **Résilience:** Retry automatique en cas d'échec
4. **Extensibilité:** Ajouter listeners sans toucher Use Cases
5. **Monitoring:** Tags + Horizon pour traçabilité
6. **Testing:** Event::fake() pour tests unitaires

## 🚀 Prochaines Étapes

- [ ] Implémenter les Mailable (InvoiceCreatedMail, etc.)
- [ ] Configurer Redis pour production
- [ ] Déployer Supervisor workers
- [ ] Installer Laravel Horizon
- [ ] Ajouter Webhook endpoints publics
- [ ] Implémenter notifications in-app

## 📚 Références

- [Laravel Events](https://laravel.com/docs/10.x/events)
- [Laravel Queues](https://laravel.com/docs/10.x/queues)
- [Laravel Horizon](https://laravel.com/docs/10.x/horizon)
