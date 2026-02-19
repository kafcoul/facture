# 🏗️ Architecture Production-Ready SaaS

## 📋 Table des Matières
1. [Vue d'ensemble](#vue-densemble)
2. [Architecture en Couches (DDD)](#architecture-en-couches-ddd)
3. [Multi-Tenancy](#multi-tenancy)
4. [Repository Pattern](#repository-pattern)
5. [Principes SOLID](#principes-solid)
6. [Structure des Dossiers](#structure-des-dossiers)
7. [Modèles de Données](#modèles-de-données)
8. [Tests et Qualité](#tests-et-qualité)

---

## Vue d'ensemble

Cette application SaaS de facturation suit les **principes de Clean Architecture** et **Domain-Driven Design (DDD)** pour garantir:

- ✅ **Maintenabilité**: Code organisé, séparation des responsabilités
- ✅ **Scalabilité**: Architecture modulaire, multi-tenancy intégré
- ✅ **Testabilité**: Isolation des couches, dependency injection
- ✅ **Sécurité**: Isolation des données par tenant, RBAC (à venir)
- ✅ **Performance**: Repository pattern, eager loading, caching (à venir)

---

## Architecture en Couches (DDD)

```
┌─────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                    │
│  (Controllers, Resources, Views, API, Filament Admin)    │
├─────────────────────────────────────────────────────────┤
│                    APPLICATION LAYER                     │
│     (Use Cases, DTOs, Services, Command Handlers)        │
├─────────────────────────────────────────────────────────┤
│                      DOMAIN LAYER                        │
│   (Entities, Value Objects, Repository Interfaces)       │
├─────────────────────────────────────────────────────────┤
│                  INFRASTRUCTURE LAYER                    │
│  (Repository Implementations, External Services, DB)     │
└─────────────────────────────────────────────────────────┘
```

### 1. **Domain Layer** (`app/Domain/`)
Contient la **logique métier pure** sans dépendances externes.

**Responsabilités:**
- Modèles de domaine (Invoice, Client, Payment, Tenant)
- Interfaces de repositories (contrats)
- Value Objects (immutables)
- Events du domaine

**Exemple:**
```php
// app/Domain/Invoice/Models/Invoice.php
class Invoice extends Model {
    public function markAsPaid(): void {
        $this->status = 'paid';
        $this->paid_at = now();
        $this->save();
    }
}
```

### 2. **Application Layer** (`app/Application/`)
Contient les **cas d'utilisation** (orchestration de la logique métier).

**Responsabilités:**
- Use Cases (CreateInvoiceUseCase, ProcessPaymentUseCase)
- DTOs (Data Transfer Objects)
- Services applicatifs

**Exemple à venir:**
```php
// app/Application/UseCases/Invoice/CreateInvoiceUseCase.php
class CreateInvoiceUseCase {
    public function execute(CreateInvoiceDTO $data): Invoice {
        // Orchestration: validate, calculate, persist, trigger events
    }
}
```

### 3. **Infrastructure Layer** (`app/Infrastructure/`)
Contient les **implémentations techniques** (base de données, API externes).

**Responsabilités:**
- Repository Eloquent implementations
- Payment gateway integrations
- Logging, caching, queues
- Traits techniques (BelongsToTenant)

**Exemple:**
```php
// app/Infrastructure/Persistence/Repositories/InvoiceRepository.php
class InvoiceRepository implements InvoiceRepositoryInterface {
    public function findByUuid(string $uuid): ?Invoice {
        return $this->model->where('uuid', $uuid)->first();
    }
}
```

### 4. **Presentation Layer** (`app/Http/`, `app/Filament/`)
Contient l'**interface utilisateur** et les contrôleurs.

**Responsabilités:**
- Controllers HTTP
- API Resources
- Filament Admin Resources
- Views Blade

---

## Multi-Tenancy

### Stratégie d'Isolation

**Single Database + tenant_id** (choix actuel):
- ✅ Simplicité de déploiement
- ✅ Backups centralisés
- ✅ Performances optimales pour <10k tenants
- ✅ Coût infrastructure réduit

Chaque table inclut une colonne `tenant_id` avec:
- **Foreign key** vers `tenants.id`
- **Index** pour performances
- **Cascade delete** pour intégrité

### Trait BelongsToTenant

Appliqué automatiquement sur tous les modèles:

```php
// app/Infrastructure/Traits/BelongsToTenant.php
trait BelongsToTenant {
    protected static function bootBelongsToTenant() {
        // Global Scope: Filtre automatique par tenant_id
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });
        
        // Auto-assign tenant_id lors de la création
        static::creating(function ($model) {
            if (auth()->check() && empty($model->tenant_id)) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}
```

**Avantages:**
- 🔒 Isolation automatique des données
- 🚫 Impossible d'accéder aux données d'un autre tenant
- 🎯 Aucun code supplémentaire dans les controllers
- 🧪 Facilite les tests unitaires

### Middlewares

1. **ResolveTenant** (appliqué sur web group):
   - Vérifie que l'utilisateur a un tenant
   - Vérifie que le tenant est actif
   - Stocke `tenant_id` en session

2. **IdentifyTenantByDomain** (optionnel):
   - Identifie le tenant par sous-domaine
   - Ex: `client1.app.com` → tenant "client1"

---

## Repository Pattern

### Pourquoi ?

- ✅ **Abstraction**: Découple la logique métier de la persistence
- ✅ **Testabilité**: Facilite les mocks dans les tests
- ✅ **Flexibilité**: Changer de DB (Eloquent → Doctrine) sans toucher au domaine
- ✅ **SOLID**: Respect du principe de séparation des responsabilités

### Architecture

```
Interface (Domain)  →  Implementation (Infrastructure)  →  Model (Domain)
     ↓                          ↓                              ↓
InvoiceRepositoryInterface → InvoiceRepository → Invoice Model
```

### Exemple Complet

**1. Interface dans Domain:**
```php
// app/Domain/Invoice/Repositories/InvoiceRepositoryInterface.php
interface InvoiceRepositoryInterface {
    public function findByUuid(string $uuid): ?Invoice;
    public function getAllForTenant(int $tenantId): Collection;
    public function create(array $data): Invoice;
}
```

**2. Implémentation dans Infrastructure:**
```php
// app/Infrastructure/Persistence/Repositories/InvoiceRepository.php
class InvoiceRepository implements InvoiceRepositoryInterface {
    protected Invoice $model;
    
    public function __construct(Invoice $model) {
        $this->model = $model;
    }
    
    public function findByUuid(string $uuid): ?Invoice {
        return $this->model->where('uuid', $uuid)->first();
    }
}
```

**3. Binding dans AppServiceProvider:**
```php
// app/Providers/AppServiceProvider.php
public function register(): void {
    $this->app->bind(
        InvoiceRepositoryInterface::class,
        InvoiceRepository::class
    );
}
```

**4. Utilisation dans Controller:**
```php
class InvoiceController extends Controller {
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository
    ) {}
    
    public function show(string $uuid) {
        $invoice = $this->invoiceRepository->findByUuid($uuid);
    }
}
```

---

## Principes SOLID

### ✅ Single Responsibility
Chaque classe a **une seule raison de changer**.

**Exemple:**
- `Invoice` (Model): Gestion des données de facture
- `InvoiceCalculatorService`: Calcul des totaux
- `PdfService`: Génération de PDF

### ✅ Open/Closed
**Ouvert à l'extension, fermé à la modification.**

**Exemple: Payment Gateways**
```php
interface PaymentGatewayInterface {
    public function charge(float $amount): PaymentResult;
}

class StripeGateway implements PaymentGatewayInterface { }
class WaveGateway implements PaymentGatewayInterface { }
// Ajouter un nouveau gateway sans modifier le code existant
```

### ✅ Liskov Substitution
Les sous-types doivent être **substituables** à leurs types de base.

**Exemple:**
```php
function processPayment(PaymentGatewayInterface $gateway) {
    $gateway->charge(100); // Fonctionne pour tous les gateways
}
```

### ✅ Interface Segregation
**Interfaces spécifiques** plutôt qu'une seule interface générale.

**Exemple:**
- `InvoiceRepositoryInterface` (CRUD factures)
- `PaymentRepositoryInterface` (CRUD paiements)
- Pas de `GenericRepositoryInterface`

### ✅ Dependency Inversion
Dépendre des **abstractions** (interfaces), pas des implémentations concrètes.

**Exemple:**
```php
// ✅ BON: Dépend de l'interface
public function __construct(InvoiceRepositoryInterface $repo) {}

// ❌ MAUVAIS: Dépend de l'implémentation
public function __construct(InvoiceRepository $repo) {}
```

---

## Structure des Dossiers

```
app/
├── Domain/                         # Logique métier pure
│   ├── Client/
│   │   ├── Models/
│   │   │   └── Client.php
│   │   └── Repositories/
│   │       └── ClientRepositoryInterface.php
│   ├── Invoice/
│   │   ├── Models/
│   │   │   ├── Invoice.php
│   │   │   └── InvoiceItem.php
│   │   ├── Repositories/
│   │   │   └── InvoiceRepositoryInterface.php
│   │   ├── Events/
│   │   │   ├── InvoiceCreated.php
│   │   │   └── InvoicePaid.php
│   │   └── ValueObjects/
│   │       └── InvoiceNumber.php
│   ├── Payment/
│   │   ├── Models/
│   │   │   └── Payment.php
│   │   ├── Repositories/
│   │   │   └── PaymentRepositoryInterface.php
│   │   └── Gateways/
│   │       └── PaymentGatewayInterface.php
│   └── Tenant/
│       └── Models/
│           └── Tenant.php
│
├── Application/                    # Use Cases & Services
│   ├── UseCases/
│   │   ├── Invoice/
│   │   │   ├── CreateInvoiceUseCase.php
│   │   │   └── GeneratePdfUseCase.php
│   │   └── Payment/
│   │       ├── ProcessPaymentUseCase.php
│   │       └── HandleWebhookUseCase.php
│   └── DTOs/
│       ├── CreateInvoiceDTO.php
│       └── ProcessPaymentDTO.php
│
├── Infrastructure/                 # Implémentations techniques
│   ├── Persistence/
│   │   └── Repositories/
│   │       ├── InvoiceRepository.php
│   │       ├── ClientRepository.php
│   │       └── PaymentRepository.php
│   ├── Payment/
│   │   ├── StripeGateway.php
│   │   ├── WaveGateway.php
│   │   └── PaystackGateway.php
│   ├── Traits/
│   │   └── BelongsToTenant.php
│   └── Logging/
│       └── CustomLogger.php
│
├── Http/                           # Presentation Layer
│   ├── Controllers/
│   │   ├── PublicInvoiceController.php
│   │   └── StripeWebhookController.php
│   ├── Middleware/
│   │   └── Tenant/
│   │       ├── ResolveTenant.php
│   │       └── IdentifyTenantByDomain.php
│   └── Resources/
│       └── InvoiceResource.php
│
├── Filament/                       # Admin Panel
│   └── Resources/
│       ├── ClientResource.php
│       ├── InvoiceResource.php
│       └── ProductResource.php
│
├── Services/                       # Legacy services (à migrer)
│   ├── InvoiceCalculatorService.php
│   ├── InvoiceNumberService.php
│   └── PdfService.php
│
└── Models/                         # Legacy models (User, Product)
    ├── User.php
    └── Product.php
```

---

## Modèles de Données

### Tenant (Multi-tenancy)
```php
tenants:
  - id: bigint
  - name: string (nom de l'entreprise)
  - slug: string (unique, pour URL)
  - domain: string (unique, nullable, sous-domaine)
  - database: string (nullable, pour multi-DB)
  - settings: json (config tenant-specific)
  - is_active: boolean
  - expires_at: timestamp (nullable)
  - timestamps, soft_deletes
```

### Invoice (Facture)
```php
invoices:
  - id: bigint
  - tenant_id: bigint (FK → tenants)
  - user_id: bigint (FK → users)
  - client_id: bigint (FK → clients)
  - number: string (INV-2024-001)
  - uuid: string (unique, pour public URL)
  - type: enum (invoice, quote, credit_note)
  - status: enum (draft, pending, paid, cancelled)
  - subtotal: decimal(10,2)
  - tax: decimal(10,2)
  - discount: decimal(10,2)
  - total: decimal(10,2)
  - currency: string(3) (XOF, EUR, USD)
  - issued_at: timestamp
  - due_date: date
  - paid_at: timestamp (nullable)
  - pdf_path: string (nullable)
  - public_hash: string (unique, 32 chars)
  - notes: text (nullable)
  - terms: text (nullable)
  - metadata: json (nullable)
  - timestamps, soft_deletes
```

### Client
```php
clients:
  - id: bigint
  - tenant_id: bigint (FK → tenants)
  - user_id: bigint (FK → users)
  - company: string (nullable)
  - name: string
  - email: string
  - phone: string (nullable)
  - address: text (nullable)
  - city: string (nullable)
  - state: string (nullable)
  - country: string (nullable)
  - postal_code: string (nullable)
  - tax_id: string (nullable)
  - currency: string(3) (default: XOF)
  - language: string(2) (default: fr)
  - notes: text (nullable)
  - is_active: boolean (default: true)
  - timestamps, soft_deletes
```

### Payment
```php
payments:
  - id: bigint
  - tenant_id: bigint (FK → tenants)
  - invoice_id: bigint (FK → invoices)
  - user_id: bigint (FK → users)
  - amount: decimal(10,2)
  - gateway: string (stripe, wave, paystack, etc.)
  - transaction_id: string (unique)
  - status: enum (pending, completed, failed, refunded)
  - currency: string(3) (XOF, EUR, USD)
  - payment_method: string (card, mobile_money, bank_transfer)
  - metadata: json (gateway-specific data)
  - completed_at: timestamp (nullable)
  - failed_at: timestamp (nullable)
  - failure_reason: text (nullable)
  - timestamps, soft_deletes
```

### Relations

```
Tenant
  └── has many → Users, Clients, Invoices, Payments

User
  ├── belongs to → Tenant
  └── has many → Clients, Invoices

Client
  ├── belongs to → Tenant, User
  └── has many → Invoices

Invoice
  ├── belongs to → Tenant, User, Client
  ├── has many → InvoiceItems
  └── has many → Payments

Payment
  ├── belongs to → Tenant, Invoice, User
```

---

## Tests et Qualité

### Objectifs (à implémenter)
- ✅ **Unit Tests**: Repositories, Services, Models
- ✅ **Feature Tests**: Controllers, API endpoints
- ✅ **Integration Tests**: Payment flows, PDF generation
- ✅ **Code Coverage**: >80%

### Outils
- PHPUnit (tests)
- PHPStan (static analysis)
- Laravel Pint (code style)
- Laravel Telescope (debugging dev)
- Sentry (error tracking production)

---

## Prochaines Étapes

### Phase 1: Service Layer ✅ En cours
- [ ] CreateInvoiceUseCase
- [ ] ProcessPaymentUseCase
- [ ] GeneratePdfUseCase

### Phase 2: Event-Driven Architecture
- [ ] InvoiceCreated event
- [ ] InvoicePaid event
- [ ] SendInvoiceNotification listener
- [ ] LogPaymentEvent listener

### Phase 3: Sécurité
- [ ] Installer spatie/laravel-permission (RBAC)
- [ ] 2FA avec pragmarx/google2fa
- [ ] Audit logs (spatie/laravel-activitylog)
- [ ] Rate limiting API

### Phase 4: API + Documentation
- [ ] API v1 REST
- [ ] OpenAPI/Swagger docs
- [ ] Sanctum authentication

### Phase 5: Testing
- [ ] PHPUnit tests suite
- [ ] Factories pour tous les models
- [ ] Test coverage >80%

### Phase 6: DevOps
- [ ] Docker + docker-compose
- [ ] CI/CD GitHub Actions
- [ ] Production deployment checklist

---

## 📚 Ressources

- [Laravel Clean Architecture](https://github.com/alexeymezenin/laravel-best-practices)
- [Domain-Driven Design](https://martinfowler.com/tags/domain%20driven%20design.html)
- [SOLID Principles](https://www.digitalocean.com/community/conceptual_articles/s-o-l-i-d-the-first-five-principles-of-object-oriented-design)
- [Repository Pattern](https://dev.to/bdelespierre/php-refactoring-a-legacy-codebase-with-repositories-1f6m)
- [Laravel Multi-Tenancy](https://tenancyforlaravel.com/)

---

**Dernière mise à jour**: 29 Novembre 2024  
**Version Architecture**: 1.0  
**Statut**: ✅ Phase 1 complétée (DDD + Multi-tenancy + Repository Pattern)
