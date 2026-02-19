# 🎯 Service Layer - Guide d'Utilisation

## Vue d'ensemble

La **Service Layer** implémente la logique métier de l'application en suivant les principes de **Clean Architecture** et **Use Case Driven Development**.

### Architecture

```
Controller → Use Case → Repository → Model
    ↓           ↓
   DTO      Service
```

---

## 📦 Composants

### 1. DTOs (Data Transfer Objects)

Les DTOs sont des objets **immutables** qui transportent les données entre les couches.

#### **CreateInvoiceDTO**
```php
use App\Application\DTOs\CreateInvoiceDTO;

$dto = CreateInvoiceDTO::fromArray([
    'tenant_id' => 1,
    'user_id' => 1,
    'client_id' => 5,
    'type' => 'invoice',
    'items' => [
        [
            'description' => 'Service consulting',
            'quantity' => 10,
            'unit_price' => 50000,
            'tax_rate' => 18,
        ],
    ],
    'due_date' => '2024-12-31',
    'currency' => 'XOF',
]);

// Validation
$errors = $dto->validate();
if (!empty($errors)) {
    // Gérer les erreurs
}
```

#### **ProcessPaymentDTO**
```php
use App\Application\DTOs\ProcessPaymentDTO;

$dto = ProcessPaymentDTO::fromArray([
    'invoice_id' => 123,
    'gateway' => 'wave',
    'amount' => 500000,
    'currency' => 'XOF',
    'return_url' => route('payment.success'),
]);
```

---

### 2. Services

#### **InvoiceCalculatorService**

Service pur pour les calculs métier (sans dépendances externes).

```php
use App\Application\Services\InvoiceCalculatorService;

$calculator = new InvoiceCalculatorService();

// Calculer le total d'un item
$itemTotal = $calculator->calculateItemTotal([
    'quantity' => 5,
    'unit_price' => 10000,
    'tax_rate' => 18,
    'discount' => 10,
]);

// Calculer les totaux d'une facture
$totals = $calculator->calculateInvoiceTotals($items, $globalTaxRate, $globalDiscount);
// Retourne: ['subtotal' => X, 'tax' => Y, 'discount' => Z, 'total' => T]

// Valider un montant de paiement
$isValid = $calculator->validatePaymentAmount(
    $invoiceTotal,
    $paymentAmount,
    $alreadyPaid
);
```

---

### 3. Use Cases

Les Use Cases orchestrent la logique métier et coordonnent les différents services.

#### **CreateInvoiceUseCase**

Crée une nouvelle facture avec toute la logique associée.

```php
use App\Application\UseCases\Invoice\CreateInvoiceUseCase;
use App\Application\DTOs\CreateInvoiceDTO;

// Injection de dépendances automatique via le constructeur
class InvoiceController extends Controller {
    public function __construct(
        private CreateInvoiceUseCase $createInvoice
    ) {}
    
    public function store(Request $request) {
        $dto = CreateInvoiceDTO::fromArray([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'client_id' => $request->client_id,
            'type' => 'invoice',
            'items' => $request->items,
            'due_date' => $request->due_date,
            'currency' => 'XOF',
        ]);
        
        try {
            $invoice = $this->createInvoice->execute($dto);
            
            return response()->json([
                'success' => true,
                'data' => $invoice,
            ], 201);
            
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
```

**Ce que fait le Use Case:**
1. ✅ Valide les données du DTO
2. ✅ Vérifie que le client existe et appartient au tenant
3. ✅ Calcule les totaux (via InvoiceCalculatorService)
4. ✅ Génère le numéro de facture
5. ✅ Crée la facture + items en base (via Repository)
6. ✅ Log l'action
7. ✅ Retourne la facture créée

#### **GeneratePdfUseCase**

Génère le PDF d'une facture.

```php
use App\Application\UseCases\Invoice\GeneratePdfUseCase;

class InvoiceController extends Controller {
    public function __construct(
        private GeneratePdfUseCase $generatePdf
    ) {}
    
    // Générer et sauvegarder
    public function generatePdf(int $id) {
        $pdfPath = $this->generatePdf->execute($id);
        
        return response()->json([
            'pdf_path' => $pdfPath,
            'pdf_url' => url('storage/' . $pdfPath),
        ]);
    }
    
    // Télécharger directement
    public function downloadPdf(int $id) {
        return $this->generatePdf->download($id);
    }
    
    // Afficher dans le navigateur
    public function streamPdf(int $id) {
        return $this->generatePdf->stream($id);
    }
}
```

**Options:**
- `execute($id, $forceRegenerate = false)`: Génère et sauvegarde
- `download($id)`: Télécharge le PDF
- `stream($id)`: Affiche le PDF dans le navigateur

#### **ProcessPaymentUseCase**

Traite un paiement avec une gateway.

```php
use App\Application\UseCases\Payment\ProcessPaymentUseCase;
use App\Application\DTOs\ProcessPaymentDTO;

class PaymentController extends Controller {
    public function __construct(
        private ProcessPaymentUseCase $processPayment
    ) {}
    
    // Initier un paiement
    public function initiate(Request $request) {
        $dto = ProcessPaymentDTO::fromArray([
            'invoice_id' => $request->invoice_id,
            'gateway' => $request->gateway, // 'wave', 'stripe', etc.
            'amount' => $request->amount,
            'currency' => 'XOF',
            'return_url' => route('payment.success'),
        ]);
        
        $result = $this->processPayment->execute($dto);
        
        return response()->json([
            'payment_id' => $result['payment']->id,
            'redirect_url' => $result['redirect_url'],
        ]);
    }
    
    // Confirmer un paiement (callback gateway)
    public function confirm(Request $request, int $paymentId) {
        $payment = $this->processPayment->confirmPayment(
            $paymentId,
            $request->all()
        );
        
        return response()->json([
            'status' => $payment->status,
        ]);
    }
}
```

**Ce que fait le Use Case:**
1. ✅ Valide le montant et la gateway
2. ✅ Vérifie que la facture existe et n'est pas déjà payée
3. ✅ Calcule le montant restant à payer
4. ✅ Crée l'enregistrement Payment avec status 'pending'
5. ✅ Initie le paiement avec la gateway
6. ✅ Retourne l'URL de redirection
7. ✅ Lors de la confirmation: vérifie, marque comme complété, met à jour la facture

---

## 🚀 Utilisation dans les Controllers

### Injection de Dépendances

Laravel résout automatiquement les dépendances via le constructeur:

```php
use App\Application\UseCases\Invoice\CreateInvoiceUseCase;
use App\Application\UseCases\Invoice\GeneratePdfUseCase;
use App\Application\UseCases\Payment\ProcessPaymentUseCase;

class InvoiceController extends Controller {
    public function __construct(
        private CreateInvoiceUseCase $createInvoice,
        private GeneratePdfUseCase $generatePdf,
        private ProcessPaymentUseCase $processPayment,
    ) {}
    
    public function store(Request $request) {
        // Utiliser $this->createInvoice->execute(...)
    }
}
```

### Gestion des Erreurs

Les Use Cases lancent des exceptions typées:

```php
try {
    $invoice = $this->createInvoice->execute($dto);
    
} catch (\InvalidArgumentException $e) {
    // Erreurs de validation
    return response()->json(['error' => $e->getMessage()], 422);
    
} catch (\RuntimeException $e) {
    // Erreurs système (DB, gateway, etc.)
    return response()->json(['error' => $e->getMessage()], 500);
}
```

---

## 📡 API REST

Routes disponibles dans `routes/api.php`:

### Invoices

```bash
# Créer une facture
POST /api/v1/invoices
Content-Type: application/json
Authorization: Bearer {token}

{
  "client_id": 5,
  "type": "invoice",
  "items": [
    {
      "description": "Service consulting",
      "quantity": 10,
      "unit_price": 50000,
      "tax_rate": 18
    }
  ],
  "due_date": "2024-12-31",
  "currency": "XOF"
}

# Générer PDF
POST /api/v1/invoices/{id}/pdf

# Télécharger PDF
GET /api/v1/invoices/{id}/download
```

### Payments

```bash
# Initier un paiement
POST /api/v1/payments
Content-Type: application/json

{
  "invoice_id": 123,
  "gateway": "wave",
  "amount": 500000,
  "currency": "XOF",
  "return_url": "https://app.com/payment/success"
}

# Confirmer un paiement
POST /api/v1/payments/{id}/confirm
```

### Health Check

```bash
GET /api/health

Response:
{
  "status": "ok",
  "timestamp": "2024-11-29T22:00:00Z",
  "version": "1.0.0"
}
```

---

## ✅ Avantages de cette Architecture

### 1. **Testabilité**
Chaque Use Case peut être testé indépendamment:

```php
class CreateInvoiceUseCaseTest extends TestCase {
    public function test_create_invoice_success() {
        $mockRepo = Mockery::mock(InvoiceRepositoryInterface::class);
        $useCase = new CreateInvoiceUseCase($mockRepo, ...);
        
        $invoice = $useCase->execute($dto);
        
        $this->assertNotNull($invoice->id);
    }
}
```

### 2. **Réutilisabilité**
Les Use Cases peuvent être appelés depuis:
- Controllers HTTP
- Controllers API
- Jobs (queues)
- Commandes Artisan
- Tests

### 3. **Maintenabilité**
La logique métier est centralisée dans les Use Cases:
- ✅ Un seul endroit à modifier
- ✅ Facile à comprendre
- ✅ Respect du principe Single Responsibility

### 4. **Sécurité**
- ✅ Validation stricte via DTOs
- ✅ Isolation tenant automatique via repositories
- ✅ Logging de toutes les actions
- ✅ Transactions DB automatiques

### 5. **Performance**
- ✅ Eager loading des relations dans les repositories
- ✅ Cache du PDF généré
- ✅ Queries optimisées

---

## 🔍 Logging

Tous les Use Cases loggent leurs actions:

```php
// Dans CreateInvoiceUseCase
Log::info('Invoice created', [
    'invoice_id' => $invoice->id,
    'invoice_number' => $invoice->number,
    'tenant_id' => $invoice->tenant_id,
    'total' => $invoice->total,
]);

// Dans ProcessPaymentUseCase
Log::info('Payment initiated', [
    'payment_id' => $payment->id,
    'gateway' => $dto->gateway,
    'amount' => $dto->amount,
]);
```

Consultez les logs:
```bash
tail -f storage/logs/laravel.log
```

---

## 🎓 Prochaines Étapes

1. **Events & Listeners**: Déclencher des événements après les actions
   - `InvoiceCreated` → `SendInvoiceNotification`
   - `PaymentReceived` → `NotifyAccountant`

2. **Queues**: Déplacer les tâches lourdes en arrière-plan
   - Génération PDF asynchrone
   - Envoi d'emails

3. **Caching**: Mettre en cache les factures fréquemment consultées

4. **Tests**: Créer une suite complète de tests unitaires et d'intégration

---

**Dernière mise à jour**: 29 Novembre 2024  
**Version**: 1.0  
**Status**: ✅ Service Layer implémentée
