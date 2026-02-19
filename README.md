# Invoice SaaS - Starter (Laravel + Filament)
Starter scaffold prêt à coder pour un SaaS de facturation.

## 🏗️ Architecture à trois interfaces

- **`/`** (Landing page) → Page marketing pour visiteurs non-authentifiés
- **`/admin`** (Filament) → Interface administrateur (back-office) - CRUD complet, rapports, configuration
- **`/client`** (Personnalisée) → Interface client (front-office) - Création factures optimisée, workflow guidé

Voir `docs/ARCHITECTURE.md` et `docs/LANDING-PAGE.md` pour plus de détails.

## Contenu
- PRD.md
- database/migrations/*.php
- app/Models/*.php
- app/Filament/Resources/*
- app/Services/*
- app/Jobs/*
- app/Http/Controllers/*
- routes/web.php
- .env.example
- composer.json (placeholder)

## Installation (rapide)
1. Copier le contenu dans un projet Laravel `laravel new invoice-saas`
2. Copier/ajouter les fichiers `database/migrations`, `app/*`
3. `composer require filament/filament barryvdh/laravel-dompdf stripe/stripe-php`
4. Configurer `.env` (DB, QUEUE_CONNECTION=redis, STRIPE_*)
5. `php artisan migrate`
6. `php artisan filament:install`
7. `php artisan queue:work` (ou Horizon)

Bonne construction 🚀
