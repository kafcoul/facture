# Phase 8 - Tests Automatisés 🧪

## Vue d'ensemble

Phase en cours d'implémentation! Suite de tests complète pour l'Invoice SaaS API.

**Date de démarrage:** 30 novembre 2025  
**Status:** 🔄 EN COURS

---

## 🎯 Objectifs

### 1. Unit Tests ✅ (Partiellement)
- **Services:** InvoiceCalculatorService (10 tests) ✅
- **Models:** Invoice (en cours)
- **Repositories:** À venir

### 2. Feature Tests 🔄 (En cours)
- **Authentication API:** 12 tests créés
- **Invoice API:** 10 tests créés
- **Payment API:** À venir

### 3. Integration Tests 📋 (À venir)
- **Workflow complet:** Création invoice → PDF → Paiement
- **Multi-tenancy:** Isolation des données
- **Events:** Dispatch et listeners

### 4. Code Coverage 📊 (À venir)
- **Objectif:** >80%
- **Actuel:** À mesurer

---

## 📦 Composants Créés

### Configuration (3 fichiers)

```
✅ phpunit.xml                    Configuration PHPUnit complète
✅ tests/TestCase.php              Base class pour tous les tests
✅ tests/CreatesApplication.php    Trait bootstrap Laravel
```

### Unit Tests (2 fichiers)

```
✅ tests/Unit/Services/InvoiceCalculatorServiceTest.php    10 tests ✅
🔄 tests/Unit/Models/InvoiceTest.php                        9 tests (à tester)
```

### Feature Tests (2 fichiers)

```
🔄 tests/Feature/Api/AuthenticationTest.php    12 tests (à tester)
🔄 tests/Feature/Api/InvoiceApiTest.php        10 tests (à tester)
```

### Factories (3 fichiers)

```
✅ database/factories/UserFactory.php         Factory User avec tenant
✅ database/factories/ClientFactory.php       Factory Client multi-tenant
✅ database/factories/InvoiceFactory.php      Factory Invoice avec états
```

### Packages Installés

```bash
phpunit/phpunit: ^10.5          Framework de tests
mockery/mockery: ^1.6           Mocking pour tests
```

---

## ✅ Tests Unitaires - InvoiceCalculatorService

### Résultats: 10/10 PASS ✅

```bash
./vendor/bin/phpunit --testdox tests/Unit/Services

✔ It calculates subtotal correctly
✔ It calculates tax amount
✔ It applies discount percentage
✔ It applies fixed discount
✔ It calculates total amount
✔ It handles zero values
✔ It handles decimal quantities
✔ It rounds tax to two decimals
✔ It handles high precision calculations
✔ It validates negative discount does not exceed subtotal

Tests: 10, Assertions: 11
```

### Méthodes testées

1. **calculateSubtotal(array $items):** Calcul sous-total depuis items
2. **calculateTax(float $subtotal, float $taxRate):** Calcul taxes
3. **applyDiscountPercentage(float $subtotal, float $discount):** Remise %
4. **applyFixedDiscount(float $subtotal, float $discount):** Remise fixe
5. **calculateTotal(float $subtotal, float $tax, float $discount):** Total

---

## 🧪 Tests Feature - Authentication API

### 12 Tests Créés

```php
✓ user_can_register                                  // POST /api/v1/auth/register
✓ registration_validates_required_fields             // Validation
✓ registration_requires_unique_email                 // Unicité email
✓ user_can_login_with_valid_credentials              // POST /api/v1/auth/login
✓ login_fails_with_invalid_credentials               // Login échoue
✓ authenticated_user_can_get_their_info              // GET /api/v1/auth/me
✓ unauthenticated_user_cannot_access_protected       // 401 si non auth
✓ authenticated_user_can_logout                      // POST /api/v1/auth/logout
✓ user_can_list_their_tokens                         // GET /api/v1/auth/tokens
✓ user_can_revoke_specific_token                     // DELETE /api/v1/auth/tokens/{id}
✓ rate_limiting_works_for_login                      // 429 après 5 tentatives
```

---

## 🧪 Tests Feature - Invoice API

### 10 Tests Créés

```php
✓ authenticated_user_can_create_invoice              // POST /api/v1/invoices
✓ unauthenticated_user_cannot_create_invoice         // 401 si non auth
✓ invoice_creation_validates_required_fields         // Validation
✓ invoice_creation_validates_items_array             // Items doit être array
✓ invoice_calculations_are_correct                   // Calculs corrects
✓ user_can_generate_invoice_pdf                      // POST /api/v1/invoices/{id}/pdf
✓ user_can_download_invoice_pdf                      // GET /api/v1/invoices/{id}/download
✓ user_cannot_access_invoices_from_different_tenant  // Isolation multi-tenant
✓ rate_limiting_works_for_invoice_creation           // 429 après 30 req/min
```

---

## 🏭 Factories

### UserFactory

```php
User::factory()->create([
    'tenant_id' => 1,
    'email' => 'test@example.com',
    'password' => Hash::make('password'),
]);
```

### ClientFactory

```php
Client::factory()->create([
    'tenant_id' => 1,
    'name' => 'Acme Corp',
    'email' => 'contact@acme.com',
]);
```

### InvoiceFactory

```php
// États disponibles
Invoice::factory()->draft()->create();
Invoice::factory()->sent()->create();
Invoice::factory()->paid()->create();
Invoice::factory()->overdue()->create();
Invoice::factory()->cancelled()->create();
```

---

## ⚙️ Configuration PHPUnit

### phpunit.xml

```xml
<phpunit bootstrap="vendor/autoload.php"
         colors="true"
         executionOrder="random"
         cacheDirectory=".phpunit.cache">
    
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
        <env name="SENTRY_LARAVEL_DSN" value=""/>
    </php>
    
    <coverage>
        <report>
            <html outputDirectory=".coverage/html"/>
            <clover outputFile=".coverage/clover.xml"/>
            <text outputFile=".coverage/coverage.txt"/>
        </report>
    </coverage>
</phpunit>
```

**Optimisations:**
- ✅ SQLite en mémoire (rapide)
- ✅ Cache/Session en array
- ✅ Queue synchrone
- ✅ Telescope désactivé
- ✅ Sentry désactivé
- ✅ Ordre aléatoire (détecte dépendances)

---

## 🚀 Commandes de Test

### Tous les tests
```bash
./vendor/bin/phpunit
```

### Tests avec format lisible
```bash
./vendor/bin/phpunit --testdox
```

### Tests spécifiques
```bash
# Par suite
./vendor/bin/phpunit tests/Unit
./vendor/bin/phpunit tests/Feature

# Par fichier
./vendor/bin/phpunit tests/Unit/Services/InvoiceCalculatorServiceTest.php

# Par test
./vendor/bin/phpunit --filter=it_calculates_subtotal_correctly
```

### Avec code coverage (nécessite Xdebug/PCOV)
```bash
./vendor/bin/phpunit --coverage-html .coverage/html
./vendor/bin/phpunit --coverage-text
```

### Tests en parallèle (plus rapide)
```bash
composer require --dev brianium/paratest
./vendor/bin/paratest
```

---

## 📊 Prochaines Étapes

### Immédiat
- [ ] Exécuter tests Feature Authentication
- [ ] Exécuter tests Feature Invoice
- [ ] Corriger erreurs éventuelles
- [ ] Ajouter tests Models (Invoice, Client, Payment)

### Court terme
- [ ] Tests Repositories (InvoiceRepository, ClientRepository)
- [ ] Tests Use Cases (CreateInvoice, ProcessPayment)
- [ ] Tests Integration (workflows complets)
- [ ] Mesurer code coverage

### Moyen terme
- [ ] Tests Events/Listeners
- [ ] Tests Jobs (queue)
- [ ] Tests Middleware
- [ ] Tests API Resources
- [ ] Tests Form Requests

---

## 🎯 Objectif Coverage

| Composant | Coverage Cible |
|-----------|---------------|
| **Models** | >90% |
| **Services** | >85% |
| **Repositories** | >85% |
| **Controllers** | >75% |
| **Use Cases** | >90% |
| **TOTAL** | >80% |

---

## 📚 Best Practices Appliquées

### Tests Unitaires
✅ Un test = une assertion principale
✅ Noms descriptifs (it_does_something)
✅ Arrange-Act-Assert pattern
✅ Isolation complète (pas de DB)
✅ Tests rapides (<100ms)

### Tests Feature
✅ Tests end-to-end réalistes
✅ Utilisation de factories
✅ Sanctum pour auth
✅ JSON responses
✅ Validation des status codes
✅ Vérification DB avec assertDatabaseHas

### Tests Integration
✅ Workflows complets
✅ Multi-tenancy validation
✅ Events dispatch verification
✅ Queue jobs execution
✅ État final du système

---

## 🔧 Debugging Tests

### Afficher output détaillé
```bash
./vendor/bin/phpunit --testdox --verbose
```

### Voir les queries SQL
```php
DB::enableQueryLog();
// ... test code ...
dd(DB::getQueryLog());
```

### Dump response
```php
$response->dump();
$response->dumpHeaders();
$response->dumpSession();
```

### PHPUnit Debugging
```bash
./vendor/bin/phpunit --stop-on-failure
./vendor/bin/phpunit --stop-on-error
```

---

## ✅ Checklist Phase 8 (50% Complété)

- [x] Installation PHPUnit + Mockery
- [x] Configuration phpunit.xml
- [x] TestCase + CreatesApplication
- [x] Factories (User, Client, Invoice)
- [x] Tests Unit Services (10 tests ✅)
- [x] Tests Feature Authentication (12 tests créés)
- [x] Tests Feature Invoice (10 tests créés)
- [ ] Exécuter tests Feature
- [ ] Tests Unit Models
- [ ] Tests Unit Repositories
- [ ] Tests Integration workflows
- [ ] Mesurer code coverage
- [ ] Documentation complète

---

**Prochaine action:** Exécuter tous les tests Feature et corriger les erreurs

**Temps estimé restant:** 1-2 heures

**Date:** 30 novembre 2025  
**Status:** 🔄 EN COURS (50%)
