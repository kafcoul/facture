# 🧪 Guide de Test - Invoice SaaS

Ce guide vous aide à tester chaque composant de l'application.

## ✅ Tests Préliminaires

### 1. Vérifier l'Installation

```bash
# Vérifier Composer
composer --version

# Vérifier PHP
php --version  # Doit être >= 8.1

# Vérifier Redis
redis-cli ping  # Doit retourner PONG

# Vérifier MySQL
mysql --version
```

### 2. Vérifier les Packages Installés

```bash
composer show | grep filament
composer show | grep dompdf
composer show | grep stripe
```

## 🔧 Tests de Configuration

### 1. Test de Connexion Base de Données

```bash
php artisan tinker
```

```php
// Dans Tinker
DB::connection()->getPdo();
// Doit retourner l'objet PDO sans erreur
exit
```

### 2. Test de Connexion Redis

```bash
php artisan tinker
```

```php
// Dans Tinker
Redis::connection()->ping();
// Doit retourner +PONG
exit
```

### 3. Test des Migrations

```bash
# Vérifier les migrations pendantes
php artisan migrate:status

# Lancer les migrations
php artisan migrate

# Rollback si nécessaire
php artisan migrate:rollback
```

## 📊 Tests des Modèles

### Créer des Données de Test

```bash
php artisan tinker
```

```php
// 1. Créer un client
$client = \App\Models\Client::create([
    'name' => 'Test Client',
    'email' => 'client@test.com',
    'phone' => '+33612345678',
    'address' => '123 Rue Test, 75001 Paris'
]);

// 2. Créer un produit
$product = \App\Models\Product::create([
    'name' => 'Produit Test',
    'description' => 'Description du produit',
    'sku' => 'TEST-001',
    'price' => 99.99,
    'tax_rate' => 20.0
]);

// 3. Créer une facture
$invoice = \App\Models\Invoice::create([
    'client_id' => $client->id,
    'invoice_number' => 'INV-2024-001',
    'issue_date' => now(),
    'due_date' => now()->addDays(30),
    'status' => 'pending',
    'subtotal' => 0,
    'tax_amount' => 0,
    'discount_amount' => 0,
    'total' => 0,
    'notes' => 'Facture de test',
    'uuid' => \Illuminate\Support\Str::uuid()
]);

// 4. Ajouter une ligne de facture
$item = \App\Models\InvoiceItem::create([
    'invoice_id' => $invoice->id,
    'product_id' => $product->id,
    'description' => 'Produit Test',
    'quantity' => 2,
    'unit_price' => 99.99,
    'tax_rate' => 20.0,
    'subtotal' => 199.98,
    'tax_amount' => 39.996,
    'total' => 239.976
]);

// 5. Recalculer les totaux de la facture
$calculator = app(\App\Services\InvoiceCalculatorService::class);
$calculator->calculate($invoice);

// Vérifier
$invoice->fresh();
echo "Subtotal: {$invoice->subtotal}\n";
echo "Tax: {$invoice->tax_amount}\n";
echo "Total: {$invoice->total}\n";

exit
```

## 📄 Test de Génération PDF

### Méthode 1 : Via Tinker

```bash
php artisan tinker
```

```php
$invoice = \App\Models\Invoice::with(['client', 'items.product'])->first();

if ($invoice) {
    $pdf = \PDF::loadView('pdf.invoice', compact('invoice'));
    $pdf->save(storage_path('app/public/test_invoice.pdf'));
    echo "PDF généré: storage/app/public/test_invoice.pdf\n";
} else {
    echo "Aucune facture trouvée. Créez-en une d'abord.\n";
}

exit
```

### Méthode 2 : Via Service

```bash
php artisan tinker
```

```php
$invoice = \App\Models\Invoice::with(['client', 'items.product'])->first();

if ($invoice) {
    $pdfService = app(\App\Services\PdfService::class);
    $pdf = $pdfService->generate($invoice);
    
    // Sauvegarder
    file_put_contents(storage_path('app/public/test_pdf.pdf'), $pdf);
    echo "PDF généré avec succès!\n";
} else {
    echo "Créez d'abord une facture avec des données.\n";
}

exit
```

### Ouvrir le PDF Généré

```bash
open storage/app/public/test_invoice.pdf
# ou
open storage/app/public/test_pdf.pdf
```

## ⚡ Test des Queues

### 1. Lancer le Worker

```bash
# Terminal 1
php artisan queue:work redis --tries=3 --timeout=90 -vvv
```

### 2. Dispatcher un Job (Terminal 2)

```bash
php artisan tinker
```

```php
$invoice = \App\Models\Invoice::with(['client', 'items'])->first();

// Tester GenerateInvoicePdfJob
\App\Jobs\GenerateInvoicePdfJob::dispatch($invoice);
echo "Job de génération PDF dispatché\n";

// Tester SendInvoiceEmailJob
\App\Jobs\SendInvoiceEmailJob::dispatch($invoice);
echo "Job d'envoi email dispatché\n";

exit
```

### 3. Vérifier l'Exécution

Dans le Terminal 1 (worker), vous devriez voir :
```
[timestamp] Processing: App\Jobs\GenerateInvoicePdfJob
[timestamp] Processed:  App\Jobs\GenerateInvoicePdfJob
```

### 4. Voir les Jobs Échoués

```bash
# Liste des jobs échoués
php artisan queue:failed

# Réessayer un job spécifique
php artisan queue:retry <job-id>

# Réessayer tous les jobs échoués
php artisan queue:retry all

# Supprimer les jobs échoués
php artisan queue:flush
```

## 💳 Test Stripe (Mode Test)

### 1. Configuration

Assurez-vous d'avoir dans `.env` :
```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
```

### 2. Test de Connexion Stripe

```bash
php artisan tinker
```

```php
\Stripe\Stripe::setApiKey(config('services.stripe.secret'));

try {
    $balance = \Stripe\Balance::retrieve();
    echo "Connexion Stripe OK!\n";
    print_r($balance);
} catch (\Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}

exit
```

### 3. Créer un Payment Intent de Test

```bash
php artisan tinker
```

```php
\Stripe\Stripe::setApiKey(config('services.stripe.secret'));

$invoice = \App\Models\Invoice::first();

$paymentIntent = \Stripe\PaymentIntent::create([
    'amount' => $invoice->total * 100, // En centimes
    'currency' => 'eur',
    'metadata' => [
        'invoice_id' => $invoice->id,
        'invoice_number' => $invoice->invoice_number,
    ],
]);

echo "Payment Intent créé: {$paymentIntent->id}\n";
echo "Client Secret: {$paymentIntent->client_secret}\n";

exit
```

### 4. Cartes de Test Stripe

Pour tester les paiements sur la page publique :

- **Succès** : `4242 4242 4242 4242`
- **Échec** : `4000 0000 0000 0002`
- **3D Secure** : `4000 0027 6000 3184`

Date d'expiration : N'importe quelle date future
CVC : N'importe quel 3 chiffres

## 🔗 Test des Routes

### 1. Routes Publiques

```bash
# Démarrer le serveur
php artisan serve
```

Testez dans le navigateur :

- Admin Panel : http://localhost:8000/admin
- Facture publique : http://localhost:8000/invoices/{uuid}
- Download PDF : http://localhost:8000/invoices/{uuid}/download

### 2. Test du Webhook Stripe (Local)

```bash
# Terminal 1 : Serveur
php artisan serve

# Terminal 2 : Stripe CLI
stripe listen --forward-to localhost:8000/stripe/webhook

# Terminal 3 : Déclencher un événement
stripe trigger payment_intent.succeeded
```

Vérifiez les logs dans Terminal 1 et Terminal 2.

## 🎨 Test Filament

### 1. Accéder à l'Admin

```bash
# Créer un utilisateur admin si pas encore fait
php artisan make:filament-user

# Démarrer le serveur
php artisan serve
```

Accédez à : http://localhost:8000/admin

### 2. Tester les Resources

- **Clients** : Créer, éditer, supprimer un client
- **Produits** : Créer, éditer, supprimer un produit
- **Factures** : Créer une facture avec plusieurs lignes

### 3. Actions Personnalisées

Dans une facture :
- Cliquez sur "Générer PDF"
- Cliquez sur "Envoyer par Email"
- Vérifiez que les jobs sont dispatched

## 📧 Test des Emails (Optionnel)

### Configuration Mailtrap (Dev)

Dans `.env` :
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@invoicesaas.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Test d'Envoi

```bash
php artisan tinker
```

```php
$invoice = \App\Models\Invoice::with(['client', 'items'])->first();

\App\Jobs\SendInvoiceEmailJob::dispatch($invoice);

echo "Email dispatché! Vérifiez Mailtrap.\n";

exit
```

## 🐛 Debug

### Activer le Mode Debug

Dans `.env` :
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

### Voir les Logs en Temps Réel

```bash
tail -f storage/logs/laravel.log
```

### Nettoyer le Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

## ✅ Checklist de Test Complet

- [ ] Installation des packages
- [ ] Configuration .env
- [ ] Connexion base de données
- [ ] Connexion Redis
- [ ] Migrations executées
- [ ] Utilisateur admin Filament créé
- [ ] Client de test créé
- [ ] Produit de test créé
- [ ] Facture de test créée
- [ ] PDF généré avec succès
- [ ] Queue worker fonctionne
- [ ] Jobs dispatched et traités
- [ ] Stripe connecté (mode test)
- [ ] Payment Intent créé
- [ ] Webhooks reçus et traités
- [ ] Page publique affichée
- [ ] Paiement de test effectué
- [ ] Email de test envoyé (optionnel)

## 🎯 Résultats Attendus

Si tous les tests passent :

✅ Base de données configurée  
✅ Redis opérationnel  
✅ PDF générés correctement  
✅ Queues fonctionnelles  
✅ Stripe intégré  
✅ Webhooks traités  
✅ Interface admin accessible  
✅ Page publique fonctionnelle  

**Votre application est prête ! 🚀**

## 🆘 En Cas de Problème

1. Vérifiez les logs : `storage/logs/laravel.log`
2. Vérifiez Redis : `redis-cli ping`
3. Vérifiez MySQL : `mysql -u root -p`
4. Nettoyez le cache : `php artisan optimize:clear`
5. Relancez les workers : `php artisan queue:restart`
6. Consultez [SETUP_GUIDE.md](SETUP_GUIDE.md)

---

**Bon testing ! 🧪**
