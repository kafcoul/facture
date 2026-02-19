# ⚡ Commandes Rapides - Invoice SaaS

Commandes utiles pour le développement quotidien.

## 🚀 Démarrage Rapide

```bash
# Tout en un (dans 3 terminaux séparés)

# Terminal 1 : Serveur
php artisan serve

# Terminal 2 : Queue Worker
php artisan queue:work redis --tries=3 --timeout=90

# Terminal 3 : Stripe Webhooks (dev local)
stripe listen --forward-to localhost:8000/stripe/webhook
```

## 🛠️ Développement

### Artisan

```bash
# Liste toutes les commandes
php artisan list

# Aide sur une commande
php artisan help migrate

# Mode maintenance
php artisan down
php artisan up

# Nettoyer tous les caches
php artisan optimize:clear
```

### Base de Données

```bash
# Migrations
php artisan migrate                  # Exécuter
php artisan migrate:fresh            # Tout supprimer et réexécuter
php artisan migrate:fresh --seed     # Avec seeders
php artisan migrate:rollback         # Annuler la dernière batch
php artisan migrate:reset            # Tout annuler
php artisan migrate:status           # Statut

# Seeders
php artisan db:seed
php artisan db:seed --class=ClientSeeder

# Tinker (console interactive)
php artisan tinker
```

### Queues

```bash
# Lancer le worker
php artisan queue:work
php artisan queue:work redis
php artisan queue:work --queue=high,default
php artisan queue:work --tries=3 --timeout=90
php artisan queue:work --daemon

# Monitoring
php artisan queue:monitor
php artisan queue:failed
php artisan queue:retry all
php artisan queue:retry <job-id>
php artisan queue:flush
php artisan queue:restart

# Queue avec Horizon (si installé)
php artisan horizon
php artisan horizon:terminate
```

### Cache

```bash
# Application
php artisan cache:clear
php artisan cache:forget <key>

# Configuration
php artisan config:cache
php artisan config:clear

# Routes
php artisan route:cache
php artisan route:clear
php artisan route:list

# Vues
php artisan view:cache
php artisan view:clear

# Optimisation (production)
php artisan optimize
php artisan optimize:clear
```

## 🎨 Filament

```bash
# Installation
php artisan filament:install --panels

# Utilisateur
php artisan make:filament-user

# Resources
php artisan make:filament-resource Client
php artisan make:filament-resource Client --generate
php artisan make:filament-resource Client --view

# Pages
php artisan make:filament-page Settings

# Widgets
php artisan make:filament-widget StatsOverview

# Assets
php artisan filament:assets
php artisan filament:upgrade

# Cache
php artisan filament:cache-components
```

## 💳 Stripe (avec Stripe CLI)

```bash
# Installation
brew install stripe/stripe-cli/stripe

# Login
stripe login

# Webhooks
stripe listen --forward-to localhost:8000/stripe/webhook
stripe listen --events payment_intent.succeeded,payment_intent.payment_failed

# Trigger des événements
stripe trigger payment_intent.succeeded
stripe trigger payment_intent.payment_failed
stripe trigger charge.succeeded

# Logs
stripe logs tail

# API Requests
stripe charges list --limit 10
stripe payment_intents list --limit 10
```

## 🔍 Debug

```bash
# Logs en temps réel
tail -f storage/logs/laravel.log

# Avec filtre
tail -f storage/logs/laravel.log | grep ERROR

# Dernières lignes
tail -n 100 storage/logs/laravel.log

# Tests
php artisan test
php artisan test --filter=InvoiceTest
```

## 📦 Composer

```bash
# Installation
composer install
composer install --no-dev  # Production

# Mise à jour
composer update
composer update vendor/package

# Informations
composer show
composer show vendor/package
composer outdated

# Autoload
composer dump-autoload
composer dump-autoload -o  # Optimized

# Audit sécurité
composer audit
```

## 🗄️ Redis

```bash
# Connexion
redis-cli

# Dans redis-cli
PING                          # Test
KEYS *                        # Lister toutes les clés
GET key                       # Obtenir une valeur
SET key value                 # Définir une valeur
DEL key                       # Supprimer une clé
FLUSHALL                      # Tout supprimer (⚠️ attention!)
MONITOR                       # Surveiller les commandes
INFO                          # Informations

# Services (macOS)
brew services start redis
brew services stop redis
brew services restart redis
brew services list
```

## 💾 MySQL

```bash
# Connexion
mysql -u root -p

# Dans MySQL
SHOW DATABASES;
USE invoice_saas;
SHOW TABLES;
DESCRIBE clients;
SELECT * FROM invoices LIMIT 10;

# Backup
mysqldump -u root -p invoice_saas > backup.sql

# Restore
mysql -u root -p invoice_saas < backup.sql

# Services (macOS)
brew services start mysql
brew services stop mysql
brew services restart mysql
```

## 📝 Git

```bash
# Status
git status
git log --oneline -10

# Branches
git branch
git checkout -b feature/nouvelle-fonctionnalite
git checkout main

# Commits
git add .
git commit -m "feat: ajout fonctionnalité X"
git push origin feature/nouvelle-fonctionnalite

# Stash
git stash
git stash list
git stash pop
```

## 🔐 Permissions (macOS/Linux)

```bash
# Laravel
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache

# Vérifier
ls -la storage/
```

## 🧪 Testing

```bash
# Tinker - Accès rapide aux modèles
php artisan tinker

# Exemples Tinker
>>> App\Models\Client::count()
>>> App\Models\Invoice::latest()->first()
>>> $invoice = App\Models\Invoice::find(1)
>>> $invoice->client->name
>>> $invoice->items
```

## 📊 Monitoring

```bash
# Espace disque
du -sh storage/
du -sh storage/logs/

# Processus
ps aux | grep php
ps aux | grep queue

# Ports
lsof -i :8000
lsof -i :3306
lsof -i :6379

# Kill un processus
kill -9 <PID>
```

## 🔄 Workflow Complet

### Nouvelle Feature

```bash
# 1. Créer une branche
git checkout -b feature/ma-feature

# 2. Développer
# ... code ...

# 3. Tester
php artisan test

# 4. Commit
git add .
git commit -m "feat: description"

# 5. Push
git push origin feature/ma-feature
```

### Déploiement

```bash
# 1. Pull les changements
git pull origin main

# 2. Dépendances
composer install --no-dev --optimize-autoloader

# 3. Optimisations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Migrations
php artisan migrate --force

# 5. Redémarrer les workers
php artisan queue:restart
```

## 🎯 Raccourcis Personnalisés

Ajoutez à votre `~/.zshrc` ou `~/.bashrc` :

```bash
# Alias Laravel
alias pa='php artisan'
alias pam='php artisan migrate'
alias pas='php artisan serve'
alias pat='php artisan tinker'
alias paqw='php artisan queue:work redis'

# Alias Composer
alias ci='composer install'
alias cu='composer update'
alias cda='composer dump-autoload'

# Alias Git
alias gs='git status'
alias ga='git add .'
alias gc='git commit -m'
alias gp='git push'

# Invoice SaaS
alias invoice-start='php artisan serve & php artisan queue:work redis'
alias invoice-logs='tail -f storage/logs/laravel.log'
```

Rechargez :
```bash
source ~/.zshrc
```

## 📞 Commandes Utiles par Contexte

### "Je viens de cloner le projet"
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan filament:install
php artisan make:filament-user
```

### "Ça ne marche plus"
```bash
php artisan optimize:clear
composer dump-autoload
php artisan queue:restart
brew services restart redis
```

### "Je veux nettoyer"
```bash
php artisan optimize:clear
rm -rf storage/logs/*.log
php artisan queue:flush
```

### "Préparer pour la prod"
```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

**Gardez cette référence sous la main ! 📌**
