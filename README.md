# Invoice SaaS — Application complète de facturation

Application SaaS de facturation construite avec **Laravel 10**, **Filament v3**, **Tailwind CSS** et **Alpine.js**.

> 🇸🇳 Conçue pour l'Afrique de l'Ouest — Devise XOF (Franc CFA), fuseau Africa/Dakar, langue française.

## 🏗️ Architecture à trois interfaces

| Interface | URL | Description |
|---|---|---|
| **Landing page** | `/` | Page marketing pour visiteurs non-authentifiés |
| **Admin (Filament)** | `/admin` | Back-office complet — CRUD, rapports, configuration |
| **Client** | `/client` | Front-office — Création factures, workflow guidé |

## ✨ Fonctionnalités

- **Facturation** — Factures, devis, avoirs, factures récurrentes
- **Clients & Produits** — Gestion complète avec tenant isolation
- **Paiements** — Stripe, Paystack, Flutterwave, Wave, MPesa, FedaPay, KKiaPay, CinetPay
- **Plans & Abonnements** — Starter (gratuit), Pro, Enterprise avec gestion des essais
- **Dashboard Filament** — 7 widgets, 13 resources, 3 pages, 6 groupes de navigation
- **Emails** — Bienvenue, facture envoyée, paiement reçu, rappels/relances
- **Export CSV** — Factures, clients, paiements, produits
- **Clés API** — Permissions granulaires, rate limiting, révocation
- **Webhooks** — Réception et logs de tous les gateways de paiement
- **Sécurité** — 2FA (Google Authenticator), Sanctum, politiques d'accès
- **CI/CD** — GitHub Actions (tests, couverture, lint, sécurité)

## 🧪 Tests

```bash
./vendor/bin/phpunit --no-coverage
# 279 tests, 763 assertions — TOUT VERT ✅
```

## 🚀 Installation

```bash
# 1. Cloner le projet
git clone https://github.com/kafcoul/facture.git
cd facture

# 2. Installer les dépendances
composer install

# 3. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 4. Configurer la base de données dans .env puis :
php artisan migrate --seed

# 5. Lancer le serveur
php artisan serve
```

## 📁 Structure principale

```
app/
├── Domain/          # Modèles domaine (Invoice, Client, Product, Tenant)
├── Filament/        # Resources, Pages, Widgets (admin panel)
├── Http/            # Controllers, Middleware
├── Mail/            # Mailables (4 templates)
├── Services/        # Services métier (Plan, Payment, Export, Invoice)
├── Jobs/            # Jobs asynchrones
└── Providers/       # Service providers

database/migrations/ # 15+ migrations
tests/               # 279 tests (Feature + Unit)
resources/views/     # Blade views (landing, auth, client, emails)
routes/              # web.php, api.php
```

## 📄 Licence

Projet privé.
