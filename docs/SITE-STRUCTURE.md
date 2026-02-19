# 🗺️ Structure du site - Vue d'ensemble

```
┌─────────────────────────────────────────────────────────────────┐
│                      INVOICE SAAS - STRUCTURE                    │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                    PUBLIC (Non-authentifié)                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  🏠 / (Landing Page)                                            │
│     ├── Hero Section                                            │
│     ├── Features (6)                                            │
│     ├── Pricing (3 plans)                                       │
│     ├── CTA + Form                                              │
│     └── Footer                                                  │
│                                                                  │
│  📖 /about                                                       │
│     ├── Histoire                                                │
│     ├── Valeurs                                                 │
│     └── Équipe                                                  │
│                                                                  │
│  🔗 /admin/login ──────────────┐                               │
│                                 │                               │
└─────────────────────────────────┼───────────────────────────────┘
                                  │
                    ┌─────────────┴──────────────┐
                    │                             │
                    ▼                             ▼
┌─────────────────────────────┐  ┌─────────────────────────────┐
│   ADMIN (Role: admin)       │  │   CLIENT (Role: client)     │
├─────────────────────────────┤  ├─────────────────────────────┤
│                             │  │                             │
│  🔧 /admin                  │  │  👤 /client                 │
│     ├── Dashboard           │  │     ├── Dashboard           │
│     ├── Clients (CRUD)      │  │     ├── Mes factures        │
│     ├── Products (CRUD)     │  │     │   ├── Liste           │
│     ├── Invoices (CRUD)     │  │     │   ├── Créer ⭐        │
│     └── Settings            │  │     │   └── Détails         │
│                             │  │     ├── Paiements           │
│  Framework: Filament 3      │  │     ├── Profil             │
│  Access: Admin only         │  │     └── Paramètres         │
│                             │  │                             │
│                             │  │  Framework: Blade + JS      │
│  ┌───────────────────────┐ │  │  Access: Client + Admin     │
│  │ Admin peut aussi      │ │  │                             │
│  │ accéder à /client     │ │  │  ┌───────────────────────┐ │
│  │ (pour tester)         │ │  │  │ Client NE PEUT PAS    │ │
│  └───────────────────────┘ │  │  │ accéder à /admin      │ │
│                             │  │  │ (redirect + message)  │ │
└─────────────────────────────┘  │  └───────────────────────┘ │
                                 │                             │
                                 └─────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                         API ENDPOINTS                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  🔍 /client/api/clients/search                                  │
│     → Autocomplete clients (pour création facture)              │
│                                                                  │
│  🔍 /client/api/products/search                                 │
│     → Autocomplete products avec tax_rate                       │
│                                                                  │
│  💳 /stripe/webhook                                              │
│     → Webhooks Stripe (paiements)                               │
│                                                                  │
│  📄 /invoices/{uuid}                                             │
│     → Vue publique facture (sans auth)                          │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

## 🔀 Flux de navigation

### Visiteur non-authentifié
```
Landing (/) 
    ├── Clic "Connexion" → /admin/login
    ├── Clic "À propos" → /about
    ├── Clic "Essai gratuit" → #demo (anchor)
    └── Clic "Fonctionnalités" → #fonctionnalites (anchor)
```

### Utilisateur Admin
```
/admin/login (authentification)
    │
    ├─→ / (home) ──auto redirect──> /admin
    │
    └─→ /admin (dashboard)
            ├── Clients (CRUD)
            ├── Products (CRUD)
            ├── Invoices (CRUD)
            └── /client (accès autorisé pour test)
                    └── Toutes les pages client
```

### Utilisateur Client
```
/admin/login (authentification)
    │
    ├─→ / (home) ──auto redirect──> /client
    │
    ├─→ /admin (accès INTERDIT) ──redirect──> /client + message
    │
    └─→ /client (dashboard)
            ├── Mes factures
            │   ├── Liste
            │   ├── Créer (formulaire complet)
            │   └── {id} (détails)
            ├── Paiements
            ├── Profil
            └── Paramètres
```

## 📁 Structure des fichiers

```
invoice-saas-starter/
│
├── resources/views/
│   ├── welcome.blade.php           ← Landing page ⭐ NEW
│   ├── about.blade.php              ← About page ⭐ NEW
│   │
│   ├── layouts/
│   │   ├── dashboard.blade.php     ← Layout original
│   │   └── client.blade.php        ← Layout client ⭐ NEW
│   │
│   └── dashboard/                  ← Vues client (/client)
│       ├── index.blade.php
│       ├── invoices/
│       │   ├── index.blade.php
│       │   ├── create.blade.php    ← Création facture ⭐
│       │   └── show.blade.php
│       ├── payments/
│       ├── profile/
│       └── settings/
│
├── app/
│   ├── Filament/Resources/         ← Admin Filament
│   │   ├── ClientResource.php
│   │   ├── ProductResource.php
│   │   └── InvoiceResource.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Dashboard/
│   │   │       └── InvoiceController.php  ← CRUD client
│   │   │
│   │   ├── Middleware/
│   │   │   ├── EnsureUserIsAdmin.php     ← Protect /admin
│   │   │   └── EnsureUserIsClient.php    ← Protect /client
│   │   │
│   │   └── Requests/
│   │       └── StoreInvoiceRequest.php   ← Validation
│   │
│   ├── Models/
│   │   ├── Client.php
│   │   ├── Product.php
│   │   ├── Invoice.php
│   │   ├── InvoiceItem.php
│   │   └── Payment.php
│   │
│   ├── Services/
│   │   ├── InvoiceCalculatorService.php  ← Calculs
│   │   ├── InvoiceNumberService.php      ← Numérotation
│   │   └── PdfService.php                ← PDF
│   │
│   └── Jobs/
│       ├── GenerateInvoicePdfJob.php
│       └── SendInvoiceEmailJob.php
│
├── routes/
│   └── web.php                     ← Routes modifiées ⭐
│
├── docs/                            ← Documentation
│   ├── ARCHITECTURE.md
│   ├── SECURITY-ROLES.md
│   ├── LANDING-PAGE.md             ⭐ NEW
│   ├── LANDING-PAGE-TESTING.md     ⭐ NEW
│   └── FEATURES-SUMMARY.md         ⭐ NEW
│
└── database/
    ├── migrations/
    │   ├── create_clients_table.php
    │   ├── create_products_table.php
    │   ├── add_tax_rate_to_products_table.php
    │   └── ...
    │
    └── seeders/
        └── TestDataSeeder.php      ← Test data
```

## 🎨 Design System

### Couleurs
```
Landing Page:
├── Primary: Indigo 600 (#667eea)
├── Secondary: Purple 600 (#764ba2)
├── Success: Green 500
└── Text: Gray 900 (titres), Gray 600 (corps)

Admin (/admin):
└── Filament default theme

Client (/client):
├── Primary: Indigo 600
└── Background: Gray 50
```

### Typographie
```
Landing:
├── Titles: 5xl (hero), 4xl (sections), 2xl (cards)
├── Body: xl (lead), base (paragraphs)
└── Font: System font stack (Tailwind default)

App:
├── Titles: 2xl, xl
├── Body: base, sm
└── Font: Inter (via Filament)
```

### Responsive Breakpoints
```
sm:  640px  (Mobile landscape)
md:  768px  (Tablet)
lg:  1024px (Desktop)
xl:  1280px (Large desktop)
2xl: 1536px (Extra large)
```

## 🔐 Sécurité - Matrice d'accès

| Route/Interface | Non-auth | Client | Admin |
|----------------|----------|--------|-------|
| `/` (Landing)  | ✅       | → /client | → /admin |
| `/about`       | ✅       | ✅     | ✅    |
| `/admin`       | ❌       | ❌ → /client | ✅ |
| `/client`      | ❌       | ✅     | ✅    |
| `/admin/login` | ✅       | ✅     | ✅    |

**Légende** :
- ✅ Accès autorisé
- ❌ Accès refusé
- → Redirection automatique

## 📊 Données de test

```
Tenant: Test Company
├── Users (2)
│   ├── admin@testcompany.com / password (role: admin)
│   └── client@testcompany.com / password (role: client)
│
├── Clients (5)
│   ├── ABC Corporation
│   ├── XYZ Solutions
│   ├── Tech Innovators
│   ├── Digital Services
│   └── Consulting Group
│
└── Products (10)
    ├── Développement Web (50€/h, TVA 20%)
    ├── Consulting IT (80€/h, TVA 20%)
    ├── Formation (70€/h, TVA 20%)
    └── ... (7 autres)
```

---

**Dernière mise à jour** : 30 novembre 2025  
**Version** : 1.0.0
