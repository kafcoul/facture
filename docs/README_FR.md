# 🧾 Invoice SaaS - Système de Facturation Complet

Application SaaS de facturation complète construite avec Laravel 10, Filament 3, et Stripe.

## ✨ Fonctionnalités

- 🎨 **Interface Admin Moderne** avec Filament 3
- 📄 **Génération de PDF** automatique avec DomPDF
- 💳 **Paiements Stripe** intégrés
- 🔔 **Webhooks Stripe** pour le suivi des paiements
- 📧 **Envoi d'emails** automatique des factures
- 🔢 **Numérotation automatique** des factures
- 👥 **Gestion des clients** complète
- 📦 **Gestion des produits** avec SKU
- 💰 **Calcul automatique** des taxes et remises
- 📊 **Suivi des paiements** en temps réel
- 🔐 **Accès public sécurisé** aux factures via UUID
- ⚡ **Queue Jobs** avec Redis pour les performances

## 🛠️ Stack Technique

- **Framework**: Laravel 10.x
- **Admin Panel**: Filament 3.x
- **PDF Generator**: Laravel DomPDF
- **Paiements**: Stripe PHP SDK
- **Queue**: Redis
- **Base de données**: MySQL 8.x
- **PHP**: 8.1+

## 📦 Installation Rapide

### Option 1 : Script Automatique (Recommandé)

```bash
cd /Users/teya2023/Downloads/invoice-saas-starter
./setup.sh
```

Le script configure automatiquement :
- ✅ Redis
- ✅ Structure Laravel
- ✅ Clés d'application
- ✅ Permissions
- ✅ Migrations
- ✅ Filament
- ✅ Utilisateur admin

### Option 2 : Installation Manuelle

Suivez le guide détaillé dans [SETUP_GUIDE.md](SETUP_GUIDE.md)

## ⚙️ Configuration

### 1. Variables d'Environnement

Configurez votre fichier `.env` :

```env
# Application
APP_NAME=InvoiceSaaS
APP_URL=http://localhost:8000

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invoice_saas
DB_USERNAME=root
DB_PASSWORD=votre_password

# Redis & Queues
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Stripe
STRIPE_KEY=pk_test_votre_cle
STRIPE_SECRET=sk_test_votre_secret
STRIPE_WEBHOOK_SECRET=whsec_votre_webhook_secret

# Informations Entreprise
COMPANY_ADDRESS="123 Rue Example"
COMPANY_CITY="75001 Paris"
COMPANY_COUNTRY="France"
COMPANY_EMAIL="contact@example.com"
```

### 2. Base de Données

```bash
# Créer la base de données
mysql -u root -p
CREATE DATABASE invoice_saas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Lancer les migrations
php artisan migrate
```

### 3. Filament Admin

```bash
# Installer Filament
php artisan filament:install --panels

# Créer un utilisateur admin
php artisan make:filament-user
```

## 🚀 Lancement

### Serveur de Développement

```bash
# Terminal 1 : Serveur Web
php artisan serve

# Terminal 2 : Queue Worker
php artisan queue:work redis --tries=3 --timeout=90

# Terminal 3 : Stripe Webhooks (optionnel, pour dev local)
stripe listen --forward-to localhost:8000/stripe/webhook
```

### Accès

- **Admin Panel**: http://localhost:8000/admin
- **API Webhook**: http://localhost:8000/stripe/webhook

## 📚 Architecture

### Modèles

```
app/Models/
├── Client.php          # Clients (nom, email, adresse)
├── Invoice.php         # Factures (numéro, dates, montants)
├── InvoiceItem.php     # Lignes de facture
├── Payment.php         # Paiements Stripe
└── Product.php         # Produits (SKU, prix, taxe)
```

### Services

```
app/Services/
├── InvoiceCalculatorService.php    # Calculs (subtotal, taxes, total)
├── InvoiceNumberService.php        # Génération de numéros
└── PdfService.php                  # Génération de PDF
```

### Jobs (Queue)

```
app/Jobs/
├── GenerateInvoicePdfJob.php       # Génération asynchrone de PDF
└── SendInvoiceEmailJob.php         # Envoi d'email asynchrone
```

### Controllers

```
app/Http/Controllers/
├── PublicInvoiceController.php     # Affichage public + paiement
└── StripeWebhookController.php     # Réception webhooks Stripe
```

### Vues

```
resources/views/
├── pdf/
│   └── invoice.blade.php           # Template PDF
└── invoices/
    └── public.blade.php            # Vue publique avec Stripe
```

## 🎯 Utilisation

### 1. Créer un Client

Via l'admin panel Filament :
- Accédez à `/admin/clients`
- Cliquez sur "Nouveau Client"
- Remplissez le formulaire

### 2. Créer une Facture

Via l'admin panel Filament :
- Accédez à `/admin/invoices`
- Cliquez sur "Nouvelle Facture"
- Sélectionnez un client
- Ajoutez des lignes de facture
- Sauvegardez

### 3. Partager la Facture

Chaque facture génère automatiquement :
- Un UUID unique
- Une URL publique : `/invoices/{uuid}`
- Un PDF téléchargeable

### 4. Paiement en Ligne

Sur la page publique, le client peut :
- Voir les détails de la facture
- Payer directement avec Stripe
- Télécharger le PDF

## 🔌 Webhooks Stripe

### Configuration

1. Dans le Dashboard Stripe, créez un webhook endpoint :
   - URL: `https://votre-domaine.com/stripe/webhook`
   - Événements : `payment_intent.succeeded`, `payment_intent.payment_failed`

2. Copiez le secret du webhook dans `.env`

### Événements Gérés

- `payment_intent.succeeded` : Marque la facture comme payée
- `payment_intent.payment_failed` : Enregistre l'échec
- `charge.succeeded` : Enregistre la transaction

## 🧪 Tests

### Tester la Génération de PDF

```bash
php artisan tinker
>>> $invoice = App\Models\Invoice::first();
>>> $pdf = app(App\Services\PdfService::class)->generate($invoice);
>>> file_put_contents('test.pdf', $pdf);
```

### Tester les Webhooks Localement

```bash
# Installer Stripe CLI
brew install stripe/stripe-cli/stripe

# Se connecter
stripe login

# Écouter les webhooks
stripe listen --forward-to localhost:8000/stripe/webhook

# Déclencher un test
stripe trigger payment_intent.succeeded
```

### Tester les Queues

```bash
# Voir les jobs en attente
php artisan queue:monitor

# Voir les jobs échoués
php artisan queue:failed

# Réessayer les jobs échoués
php artisan queue:retry all
```

## 📊 Filament Resources

L'application inclut 3 resources Filament :

### ClientResource
- Liste et CRUD des clients
- Recherche et filtres
- Relations avec factures

### InvoiceResource
- Liste et CRUD des factures
- Gestion des lignes de facture (Repeater)
- Calcul automatique des totaux
- Génération de PDF
- Actions personnalisées

### ProductResource
- Liste et CRUD des produits
- Gestion des SKU
- Prix et taux de taxe

## 🔒 Sécurité

- ✅ **CSRF Protection** sur tous les formulaires
- ✅ **UUID** pour l'accès public aux factures
- ✅ **Signature des Webhooks** Stripe vérifiée
- ✅ **Validation** des données stricte
- ✅ **Sanitization** des entrées utilisateur

## 🚀 Déploiement en Production

### Checklist

- [ ] Configurer les vraies clés Stripe (production)
- [ ] Mettre `APP_DEBUG=false`
- [ ] Configurer `APP_URL` avec votre domaine
- [ ] Mettre en place SSL/HTTPS
- [ ] Configurer les webhooks Stripe en production
- [ ] Optimiser avec `php artisan optimize`
- [ ] Configurer un superviseur pour les queues
- [ ] Mettre en place un système de backup
- [ ] Configurer les logs et monitoring

### Commandes de Production

```bash
# Optimisation
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Queues (avec Supervisor)
php artisan queue:work redis --tries=3 --timeout=90 --sleep=3 --daemon
```

## 📝 Logs

Les logs sont disponibles dans :
- `storage/logs/laravel.log` - Logs généraux
- Stripe Dashboard - Logs webhooks
- Queue monitoring - Jobs échoués

## 🤝 Support

Pour toute question ou problème :

1. Consultez [SETUP_GUIDE.md](SETUP_GUIDE.md)
2. Vérifiez les logs
3. Testez avec Stripe CLI en mode test

## 📄 Licence

Ce projet est sous licence MIT.

## 🎨 Captures d'Écran

### Admin Panel
Interface Filament pour gérer clients, produits et factures.

### Vue Publique
Page responsive avec intégration Stripe pour le paiement.

### PDF Généré
Facture professionnelle générée automatiquement.

---

**Made with ❤️ using Laravel, Filament, and Stripe**
