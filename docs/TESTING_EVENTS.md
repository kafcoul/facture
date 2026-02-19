# 🧪 TESTING GUIDE: Event-Driven Architecture

Ce guide vous permet de tester rapidement l'architecture événementielle implémentée dans la Phase 5.

---

## 🚀 Démarrage Rapide

### 1. Démarrer l'application (3 terminaux)

#### Terminal 1: Serveur Laravel
```bash
cd /Users/teya2023/Downloads/invoice-saas-starter
php artisan serve
```
✅ Serveur: http://127.0.0.1:8000

#### Terminal 2: Queue Worker
```bash
cd /Users/teya2023/Downloads/invoice-saas-starter
php artisan queue:work redis --verbose
```
✅ Worker écoute les événements en continu

#### Terminal 3: Logs en temps réel
```bash
cd /Users/teya2023/Downloads/invoice-saas-starter
tail -f storage/logs/laravel.log
```
✅ Voir les événements dispatched et les listeners exécutés

---

## 📋 Tests Disponibles

### Test 1: Vérifier l'infrastructure

#### Event List
```bash
php artisan event:list | grep "Domain"
```
**Attendu:** 5 événements avec leurs listeners
```
App\Domain\Invoice\Events\InvoiceCreated
  ⇂ SendInvoiceNotification (ShouldQueue)
  ⇂ GenerateInvoicePdf (ShouldQueue)
...
```

#### Routes de test
```bash
php artisan route:list --path=test
```
**Attendu:** 6 routes de test

---

### Test 2: Événement InvoiceCreated

#### Via API
```bash
curl http://127.0.0.1:8000/api/test/events/invoice-created
```

#### Réponse attendue:
```json
{
  "message": "Event InvoiceCreated dispatched successfully",
  "invoice": {
    "id": 1,
    "number": "INV-2025-0001",
    "total": 50000,
    "client": "Demo Client"
  },
  "listeners": [
    "SendInvoiceNotification",
    "GenerateInvoicePdf"
  ]
}
```

#### Vérifier dans les logs (Terminal 3):
```
Testing InvoiceCreated event
Invoice created
```

#### Vérifier le worker (Terminal 2):
```
[2025-01-15 10:00:00] Processing: SendInvoiceNotification
[2025-01-15 10:00:01] Processed: SendInvoiceNotification
[2025-01-15 10:00:02] Processing: GenerateInvoicePdf
[2025-01-15 10:00:05] Processed: GenerateInvoicePdf
```

---

### Test 3: Workflow Complet (Créer Facture + Events)

```bash
curl -X POST http://127.0.0.1:8000/api/test/events/full-workflow
```

#### Réponse attendue:
```json
{
  "message": "Invoice created successfully with events",
  "invoice": {
    "id": 2,
    "number": "INV-2025-0002",
    "total": 53100,
    "status": "pending"
  },
  "events_dispatched": ["InvoiceCreated"],
  "listeners_triggered": [
    "SendInvoiceNotification (queued)",
    "GenerateInvoicePdf (queued)"
  ],
  "next_steps": [
    "1. Check logs: tail storage/logs/laravel.log",
    "2. Process queue: php artisan queue:work redis --once",
    "3. Check queue stats: GET /api/test/events/queue-stats"
  ]
}
```

#### Ce qui se passe en arrière-plan:
1. ✅ **CreateInvoiceUseCase** crée la facture
2. ✅ **event(InvoiceCreated)** dispatché
3. ✅ **2 jobs** ajoutés à la queue Redis
4. ✅ **Worker** exécute les listeners
5. ✅ **SendInvoiceNotification** envoie email (simulé)
6. ✅ **GenerateInvoicePdf** crée le PDF

---

### Test 4: Vérifier les Stats de Queue

```bash
curl http://127.0.0.1:8000/api/test/events/queue-stats
```

#### Réponse attendue:
```json
{
  "queue": "default",
  "pending_jobs": 0,
  "connection": "redis",
  "command": "php artisan queue:work redis --once"
}
```

Si `pending_jobs > 0`, il y a des jobs en attente.

---

### Test 5: Événement PaymentReceived

```bash
curl http://127.0.0.1:8000/api/test/events/payment-received
```

#### Réponse attendue:
```json
{
  "message": "Event PaymentReceived dispatched",
  "payment": {
    "id": 1,
    "amount": 50000,
    "gateway": "stripe",
    "invoice_number": "INV-2025-0001"
  },
  "listeners": [
    "LogPaymentEvent",
    "NotifyAccountant (if amount > 1M XOF)"
  ]
}
```

#### Vérifier logs (Terminal 3):
```
Payment received - detailed log
{
  "event": "payment.received",
  "payment_id": 1,
  "amount": 50000,
  "gateway": "stripe"
}
```

---

### Test 6: Événement InvoiceOverdue

```bash
curl http://127.0.0.1:8000/api/test/events/invoice-overdue
```

#### Réponse attendue:
```json
{
  "message": "Event InvoiceOverdue dispatched",
  "invoice": {
    "id": 1,
    "number": "INV-2025-0001",
    "due_date": "2025-01-01",
    "days_overdue": 14
  },
  "listener": "SendOverdueReminder"
}
```

---

## 🧪 Tests Manuels via Artisan

### Test 1: Dispatcher manuellement un événement

```bash
php artisan tinker
```

```php
$invoice = App\Models\Invoice::first();
event(new App\Domain\Invoice\Events\InvoiceCreated($invoice));
exit
```

### Test 2: Exécuter un job manuellement

```bash
# Traiter 1 job
php artisan queue:work redis --once

# Traiter tous les jobs
php artisan queue:work redis --stop-when-empty
```

### Test 3: Vérifier les failed jobs

```bash
# Lister les jobs échoués
php artisan queue:failed

# Retry un job
php artisan queue:retry {job-id}

# Retry tous les jobs échoués
php artisan queue:retry all

# Supprimer les failed jobs
php artisan queue:flush
```

---

## 🔍 Debug & Monitoring

### Vérifier Redis

```bash
# Se connecter à Redis
redis-cli

# Voir les queues
KEYS queues:*

# Voir la longueur d'une queue
LLEN queues:default

# Voir les jobs
LRANGE queues:default 0 -1

# Quitter
exit
```

### Vérifier les Logs

```bash
# Logs Laravel
tail -f storage/logs/laravel.log | grep "Invoice\|Payment"

# Filtrer les événements
tail -f storage/logs/laravel.log | grep "event"

# Filtrer les erreurs
tail -f storage/logs/laravel.log | grep "ERROR"
```

### Monitoring avec Artisan

```bash
# Surveiller les queues en temps réel
php artisan queue:monitor redis

# Stats
php artisan queue:work redis --verbose

# Liste des workers actifs
ps aux | grep "queue:work"
```

---

## 📊 Scénarios de Test Complets

### Scénario A: Création de Facture → PDF → Email

1. **Créer la facture:**
```bash
curl -X POST http://127.0.0.1:8000/api/test/events/full-workflow
```

2. **Vérifier la queue:**
```bash
curl http://127.0.0.1:8000/api/test/events/queue-stats
# Attendu: pending_jobs = 2
```

3. **Traiter les jobs:**
```bash
php artisan queue:work redis --once  # SendInvoiceNotification
php artisan queue:work redis --once  # GenerateInvoicePdf
```

4. **Vérifier les logs:**
```bash
tail -20 storage/logs/laravel.log | grep "Invoice notification sent\|Invoice PDF generated"
```

5. **Vérifier le PDF généré:**
```bash
ls -la storage/app/tenants/1/invoices/
```

---

### Scénario B: Paiement Reçu → Notifications Multiples

1. **Simuler paiement:**
```bash
curl http://127.0.0.1:8000/api/test/events/payment-received
```

2. **Vérifier workers (Terminal 2):**
```
Processing: LogPaymentEvent
Processed: LogPaymentEvent
Processing: NotifyAccountant
Processed: NotifyAccountant
```

3. **Vérifier logs détaillés:**
```bash
tail -30 storage/logs/laravel.log | grep "Payment received"
```

---

### Scénario C: Facture en Retard → Rappel Client

1. **Dispatcher l'événement:**
```bash
curl http://127.0.0.1:8000/api/test/events/invoice-overdue
```

2. **Worker traite:**
```
Processing: SendOverdueReminder
Overdue reminder sent
Processed: SendOverdueReminder
```

3. **Vérifier metadata mise à jour:**
```bash
php artisan tinker
```
```php
$invoice = Invoice::first();
dd($invoice->metadata);
// Attendu: ['reminder_count' => 1, 'last_overdue_milestone' => 14]
```

---

## ⚡ Tests de Performance

### Mesurer le temps de réponse API

```bash
# Sans événements (ancien code)
time curl -X POST http://127.0.0.1:8000/api/v1/invoices \
  -H "Authorization: Bearer TOKEN" \
  -d '{ ... }'
# Attendu: ~5-7 secondes

# Avec événements (nouveau code)
time curl -X POST http://127.0.0.1:8000/api/test/events/full-workflow
# Attendu: < 200ms
```

### Stress Test avec Apache Bench

```bash
# 100 requêtes, 10 concurrentes
ab -n 100 -c 10 http://127.0.0.1:8000/api/test/events/invoice-created

# Vérifier la queue après
curl http://127.0.0.1:8000/api/test/events/queue-stats
# Attendu: pending_jobs = 200 (100 x 2 listeners)
```

---

## 🐛 Troubleshooting

### Problème: Worker ne traite pas les jobs

**Causes possibles:**
1. Redis non démarré
2. Queue connection mal configurée
3. Jobs failed sans retry

**Solutions:**
```bash
# Vérifier Redis
redis-cli ping  # Attendu: PONG

# Vérifier configuration
php artisan config:cache
cat .env | grep QUEUE_CONNECTION  # Attendu: redis

# Relancer worker
php artisan queue:restart
php artisan queue:work redis --verbose
```

### Problème: Events non dispatched

**Vérifier EventServiceProvider:**
```bash
php artisan event:list | grep "InvoiceCreated"
```

**Vérifier les imports:**
```php
// Dans CreateInvoiceUseCase.php
use App\Domain\Invoice\Events\InvoiceCreated;
event(new InvoiceCreated($invoice));
```

**Clear cache:**
```bash
php artisan event:clear
php artisan cache:clear
php artisan config:clear
```

### Problème: Listeners échouent

**Vérifier failed jobs:**
```bash
php artisan queue:failed
```

**Voir les détails:**
```bash
php artisan queue:failed --id={job-id}
```

**Retry:**
```bash
php artisan queue:retry {job-id}
```

---

## ✅ Checklist de Validation

Après avoir exécuté tous les tests:

- [ ] `event:list` affiche 5 événements + 7 listeners
- [ ] `route:list --path=test` affiche 6 routes
- [ ] Serveur Laravel démarré (port 8000)
- [ ] Worker queue actif (Terminal 2)
- [ ] Test InvoiceCreated réussi (200 OK)
- [ ] Test full-workflow réussi (facture créée)
- [ ] Logs affichent "event dispatched"
- [ ] Worker traite les jobs (Terminal 2)
- [ ] PDF généré dans storage/app/tenants/1/invoices/
- [ ] Metadata mis à jour (notification_sent_at)
- [ ] Queue stats = 0 pending_jobs
- [ ] Aucun failed job

---

## 📚 Prochaines Étapes

Une fois tous les tests validés:

1. ✅ **Implémenter les Mailables** (InvoiceCreatedMail, etc.)
2. ✅ **Configurer Redis pour production**
3. ✅ **Déployer Supervisor pour workers**
4. ✅ **Installer Laravel Horizon** pour monitoring avancé
5. ✅ **Supprimer routes de test** avant production

---

**Bon testing! 🚀**
