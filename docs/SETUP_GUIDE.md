# 🚀 Guide de Configuration - Invoice SaaS

Ce guide vous accompagne dans la configuration complète de l'application Invoice SaaS.

## ✅ Étapes Complétées

- ✅ Packages installés (Filament, DomPDF, Stripe)
- ✅ Fichier .env créé avec configuration Redis
- ✅ Vues Blade créées (PDF + Affichage public)
- ✅ Fichiers de configuration créés

## 📋 Configuration Requise

### 1. Configuration de la Base de Données

Modifiez les valeurs dans votre fichier `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invoice_saas
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe
```

**Créer la base de données :**
```bash
mysql -u root -p
CREATE DATABASE invoice_saas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 2. Configuration Redis

Si Redis n'est pas installé :
```bash
# macOS avec Homebrew
brew install redis
brew services start redis

# Vérifier que Redis fonctionne
redis-cli ping
# Devrait répondre: PONG
```

Configuration dans `.env` (déjà fait) :
```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 3. Configuration Stripe

1. Créez un compte sur [Stripe](https://dashboard.stripe.com/register)
2. Obtenez vos clés API (mode test) depuis le Dashboard
3. Configurez le webhook dans Stripe Dashboard :
   - URL: `https://votre-domaine.com/stripe/webhook`
   - Événements à écouter :
     - `payment_intent.succeeded`
     - `payment_intent.payment_failed`
     - `charge.succeeded`

4. Mettez à jour `.env` avec vos vraies clés :
```env
STRIPE_KEY=pk_test_votre_cle_publique
STRIPE_SECRET=sk_test_votre_cle_secrete
STRIPE_WEBHOOK_SECRET=whsec_votre_secret_webhook
```

### 4. Informations de l'Entreprise

Ajoutez à la fin de votre `.env` :
```env
COMPANY_ADDRESS="123 Rue Example"
COMPANY_CITY="75001 Paris"
COMPANY_COUNTRY="France"
COMPANY_EMAIL="contact@votreentreprise.com"
```

## 🔧 Commandes d'Installation

### Étape 1 : Générer la clé d'application

⚠️ **IMPORTANT** : Il manque plusieurs fichiers Laravel de base. Vous devez initialiser un projet Laravel complet ou copier les fichiers manquants.

**Option A - Projet Laravel existant :**
Si vous avez déjà un projet Laravel, copiez ces fichiers manquants :
- `public/index.php`
- `bootstrap/cache/.gitignore`
- Routes complètes
- Middleware
- Providers

**Option B - Nouveau projet :**
```bash
# Dans un dossier temporaire
composer create-project laravel/laravel temp-laravel "10.*"

# Copier les fichiers nécessaires vers invoice-saas-starter
cp -r temp-laravel/public invoice-saas-starter/
cp -r temp-laravel/bootstrap invoice-saas-starter/
cp -r temp-laravel/config invoice-saas-starter/
cp -r temp-laravel/resources invoice-saas-starter/
cp -r temp-laravel/app/Providers invoice-saas-starter/app/
cp -r temp-laravel/app/Http/Middleware invoice-saas-starter/app/Http/

# Supprimer le dossier temporaire
rm -rf temp-laravel
```

### Étape 2 : Générer la clé

```bash
php artisan key:generate
```

### Étape 3 : Installer Filament

```bash
php artisan filament:install --panels
```

### Étape 4 : Migrations

```bash
php artisan migrate
```

### Étape 5 : Créer un utilisateur admin Filament

```bash
php artisan make:filament-user
```

### Étape 6 : Créer les liens symboliques

```bash
php artisan storage:link
```

### Étape 7 : Publier les assets Filament

```bash
php artisan filament:assets
```

## 🎯 Lancer l'Application

### Terminal 1 : Serveur Web
```bash
php artisan serve
```
Accédez à : http://localhost:8000

### Terminal 2 : Queue Worker (Redis)
```bash
php artisan queue:work redis --tries=3 --timeout=90
```

### Terminal 3 (Optionnel) : Queue Monitoring
```bash
php artisan queue:monitor
```

### Terminal 4 (Dev) : Horizon (Alternative à queue:work)
Si vous préférez utiliser Laravel Horizon pour gérer les queues :
```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon
```

## 🧪 Tester les Webhooks Stripe

### En local avec Stripe CLI

1. Installer Stripe CLI :
```bash
# macOS
brew install stripe/stripe-cli/stripe

# Autres OS : https://stripe.com/docs/stripe-cli#install
```

2. Se connecter à Stripe :
```bash
stripe login
```

3. Écouter les webhooks localement :
```bash
stripe listen --forward-to localhost:8000/stripe/webhook
```

4. Obtenir le webhook secret affiché et le mettre dans `.env`

5. Déclencher un événement de test :
```bash
stripe trigger payment_intent.succeeded
```

### En production

- Configurez l'URL webhook dans le Dashboard Stripe
- Assurez-vous que l'URL est accessible publiquement
- Vérifiez les logs des webhooks dans Stripe Dashboard

## 🔍 Vérifications

### Vérifier Redis
```bash
redis-cli ping
```

### Vérifier les Jobs en Queue
```bash
php artisan queue:failed  # Jobs échoués
php artisan queue:retry all  # Réessayer les jobs échoués
```

### Vérifier la génération de PDF
```bash
php artisan tinker
>>> $invoice = App\Models\Invoice::first();
>>> $pdf = PDF::loadView('pdf.invoice', compact('invoice'));
>>> $pdf->save(storage_path('test.pdf'));
```

## 📂 Structure des Fichiers Créés

```
resources/views/
├── pdf/
│   └── invoice.blade.php          # Template PDF de la facture
└── invoices/
    └── public.blade.php            # Vue publique avec paiement Stripe

config/
├── stripe.php                      # Configuration Stripe
├── dompdf.php                      # Configuration DomPDF
└── queue.php                       # Configuration des Queues
```

## 🚨 Dépannage

### Erreur "Class not found"
```bash
composer dump-autoload
php artisan clear-compiled
php artisan config:clear
```

### Erreur Redis Connection
```bash
# Vérifier que Redis tourne
brew services list
# Redémarrer Redis
brew services restart redis
```

### Erreur de Permission
```bash
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

### Les Jobs ne se lancent pas
```bash
# Vérifier la configuration
php artisan config:cache
php artisan queue:restart
```

## 📚 Prochaines Étapes

1. ✅ Configurer .env avec vos vraies données
2. ✅ Créer la base de données
3. ✅ Lancer les migrations
4. ✅ Créer un utilisateur admin
5. ✅ Lancer le serveur et les workers
6. ✅ Tester la création d'une facture
7. ✅ Tester le paiement Stripe
8. ✅ Vérifier la génération de PDF
9. ✅ Tester les webhooks

## 🔗 URLs Importantes

- **Admin Panel**: http://localhost:8000/admin
- **API Webhooks**: http://localhost:8000/stripe/webhook
- **Facture Publique**: http://localhost:8000/invoices/{uuid}
- **Téléchargement PDF**: http://localhost:8000/invoices/{uuid}/download

## 💡 Conseils

- Utilisez toujours les clés de **test** Stripe en développement
- Surveillez les logs : `tail -f storage/logs/laravel.log`
- Testez les webhooks avec Stripe CLI avant la production
- Configurez un système de monitoring pour les queues (Horizon recommandé)
- Sauvegardez régulièrement votre base de données

---

**Besoin d'aide ?** Consultez la documentation :
- [Laravel Queues](https://laravel.com/docs/10.x/queues)
- [Filament](https://filamentphp.com/docs)
- [Stripe](https://stripe.com/docs)
- [DomPDF](https://github.com/barryvdh/laravel-dompdf)
