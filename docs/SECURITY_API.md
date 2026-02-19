# Phase 6: Security & API - Sécurisation et Finalisation de l'API 🔐

## Vue d'ensemble

Cette phase ajoute des couches de sécurité et finalise l'API REST avec validation, rate limiting, CORS, et API Resources pour une sérialisation optimisée.

## 📋 Composants Créés

### 1. Middlewares de Sécurité

#### ThrottleRequests (Custom)
```php
app/Http/Middleware/ThrottleRequests.php
```

**Fonctionnalités:**
- Rate limiting personnalisé par tenant
- Signature basée sur `tenant_id + user_id + IP`
- Empêche les abus et attaques DDoS

**Clé de throttling:**
```php
// Authentifié: sha1(tenant_id|user_id|ip)
// Non-authentifié: sha1(ip)
```

#### ForceJsonResponse
```php
app/Http/Middleware/ForceJsonResponse.php
```

**Fonctionnalités:**
- Force toutes les réponses API en JSON
- Ajoute `Accept: application/json` automatiquement
- Évite les réponses HTML en cas d'erreur

### 2. API Resources (Serialization)

#### InvoiceResource
```php
app/Http/Resources/InvoiceResource.php
```

**Champs exposés:**
- Données facture (id, number, type, status, montants)
- Dates ISO 8601 (issued_at, due_date, paid_at)
- Relations chargées conditionnellement (client, items, payments)
- Statuts calculés (is_paid, is_overdue, days_until_due)
- URL PDF si disponible

**Exemple de réponse:**
```json
{
  "data": {
    "id": 1,
    "number": "INV-2025-0001",
    "status": "pending",
    "total": 53100,
    "currency": "XOF",
    "is_paid": false,
    "is_overdue": false,
    "days_until_due": 30,
    "pdf_url": "http://api.example.com/api/v1/invoices/1/download",
    "client": {
      "id": 1,
      "name": "Acme Corp",
      "email": "contact@acme.com"
    },
    "items": [...]
  }
}
```

#### ClientResource
- Informations client complètes
- Statistiques conditionnelles (invoices_count, total_revenue)
- Chargement avec `?include_stats=true`

#### PaymentResource
- Métadonnées filtrées (masque card_number, cvv)
- Relation invoice incluse
- Dates completed_at, failed_at

#### InvoiceItemResource
- Description, quantité, prix, total
- Relation product optionnelle

#### ProductResource
- Catalogue produits avec SKU
- Prix et tax_rate par défaut

### 3. Form Requests (Validation)

#### CreateInvoiceRequest
```php
app/Http/Requests/CreateInvoiceRequest.php
```

**Règles de validation:**
```php
'client_id' => 'required|integer|exists:clients,id',
'type' => 'required|string|in:standard,proforma,credit_note',
'currency' => 'required|string|in:XOF,USD,EUR',
'items' => 'required|array|min:1',
'items.*.description' => 'required|string|max:500',
'items.*.quantity' => 'required|numeric|min:0.01',
'items.*.unit_price' => 'required|numeric|min:0',
```

**Messages personnalisés** en français

#### ProcessPaymentRequest
```php
app/Http/Requests/ProcessPaymentRequest.php
```

**Règles:**
```php
'invoice_id' => 'required|integer|exists:invoices,id',
'amount' => 'required|numeric|min:1',
'gateway' => 'required|string|in:stripe,paypal,wave,orange_money,mtn_momo',
'return_url' => 'required|url',
```

### 4. Configuration CORS

```php
config/cors.php
```

**Configuration:**
- **Paths:** `api/*`, `sanctum/*`, `webhooks/*`
- **Methods:** GET, POST, PUT, PATCH, DELETE, OPTIONS
- **Headers:** Authorization, X-Tenant-ID, Content-Type
- **Exposed Headers:** X-RateLimit-Limit, X-RateLimit-Remaining
- **Credentials:** Supportés (cookies, auth)
- **Max Age:** 3600 secondes

**Variables d'environnement:**
```env
CORS_ALLOWED_ORIGINS=https://app.example.com,https://admin.example.com
```

---

## 🔒 Rate Limiting

### Configuration par Route

| Route Group | Limite | Fenêtre | Description |
|-------------|--------|---------|-------------|
| Global API | 60 req | 1 minute | Toutes routes /api/v1/* |
| Invoices | 30 req | 1 minute | Création/génération PDF |
| Payments | 10 req | 1 minute | Initiation/confirmation paiement |
| Test Events | Illimité | - | À supprimer en production |

### Implémentation

```php
// routes/api.php
Route::middleware(['auth:sanctum', 'tenant.resolve', 'throttle:60,1'])
    ->prefix('v1')
    ->group(function () {
        // Limite globale: 60 req/min
        
        Route::middleware('throttle:30,1')
            ->prefix('invoices')
            ->group(function () {
                // Limite spécifique: 30 req/min
            });
        
        Route::middleware('throttle:10,1')
            ->prefix('payments')
            ->group(function () {
                // Limite plus restrictive: 10 req/min
            });
    });
```

### Headers de Réponse

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
Retry-After: 60
```

---

## 🛡️ Sécurité Implémentée

### 1. Validation des Entrées

✅ **Form Requests** avec règles strictes  
✅ **Messages d'erreur personnalisés** en français  
✅ **Validation des relations** (exists:clients,id)  
✅ **Sanitization automatique** (TrimStrings, ConvertEmptyStrings)

### 2. Rate Limiting

✅ **Par tenant** (évite qu'un tenant abuse)  
✅ **Par endpoint** (limites différenciées)  
✅ **Par IP** (utilisateurs non-authentifiés)  
✅ **Headers exposés** (transparence pour clients)

### 3. CORS

✅ **Origins restrictives** (via CORS_ALLOWED_ORIGINS)  
✅ **Methods spécifiques** (pas de méthodes non-standard)  
✅ **Headers contrôlés** (seulement ceux nécessaires)  
✅ **Credentials supportés** (pour Sanctum)

### 4. Serialization Sécurisée

✅ **API Resources** (masque données sensibles)  
✅ **Chargement conditionnel** (évite over-fetching)  
✅ **Filtrage metadata** (card_number, cvv exclus)  
✅ **Timestamps ISO 8601** (format standardisé)

### 5. Middleware Stack

```
┌─────────────────────────────────────┐
│     Request                          │
└──────────┬──────────────────────────┘
           │
           ▼
┌─────────────────────────────────────┐
│  ForceJsonResponse                   │
│  (Accept: application/json)          │
└──────────┬──────────────────────────┘
           │
           ▼
┌─────────────────────────────────────┐
│  ThrottleRequests                    │
│  (Rate limiting par tenant)          │
└──────────┬──────────────────────────┘
           │
           ▼
┌─────────────────────────────────────┐
│  auth:sanctum                        │
│  (Authentication)                    │
└──────────┬──────────────────────────┘
           │
           ▼
┌─────────────────────────────────────┐
│  tenant.resolve                      │
│  (Multi-tenancy isolation)           │
└──────────┬──────────────────────────┘
           │
           ▼
┌─────────────────────────────────────┐
│  Controller                          │
│  (Business logic via Use Cases)      │
└─────────────────────────────────────┘
```

---

## 📡 Exemples d'Utilisation API

### 1. Créer une Facture

**Request:**
```http
POST /api/v1/invoices
Authorization: Bearer {token}
Content-Type: application/json

{
  "client_id": 1,
  "type": "standard",
  "currency": "XOF",
  "tax_rate": 18,
  "discount": 0,
  "notes": "Merci pour votre confiance",
  "terms": "Paiement sous 30 jours",
  "items": [
    {
      "description": "Consultation",
      "quantity": 1,
      "unit_price": 50000,
      "tax_rate": 18
    },
    {
      "description": "Développement",
      "quantity": 10,
      "unit_price": 10000,
      "tax_rate": 18
    }
  ]
}
```

**Response (201 Created):**
```json
{
  "data": {
    "id": 5,
    "number": "INV-2025-0005",
    "type": "standard",
    "status": "pending",
    "subtotal": 150000,
    "tax": 27000,
    "discount": 0,
    "total": 177000,
    "currency": "XOF",
    "issued_at": "2025-11-29T23:00:00Z",
    "due_date": "2025-12-29T23:00:00Z",
    "client": {
      "id": 1,
      "name": "Acme Corp",
      "email": "contact@acme.com"
    },
    "items": [
      {
        "id": 1,
        "description": "Consultation",
        "quantity": 1,
        "unit_price": 50000,
        "total": 59000
      },
      {
        "id": 2,
        "description": "Développement",
        "quantity": 10,
        "unit_price": 10000,
        "total": 118000
      }
    ],
    "is_paid": false,
    "is_overdue": false,
    "days_until_due": 30,
    "pdf_url": null,
    "created_at": "2025-11-29T23:00:00Z"
  },
  "message": "Invoice created successfully"
}
```

### 2. Initier un Paiement

**Request:**
```http
POST /api/v1/payments
Authorization: Bearer {token}
Content-Type: application/json

{
  "invoice_id": 5,
  "amount": 177000,
  "gateway": "stripe",
  "currency": "XOF",
  "payment_method": "card",
  "return_url": "https://app.example.com/payments/callback",
  "metadata": {
    "customer_note": "Paiement par carte"
  }
}
```

**Response (201 Created):**
```json
{
  "data": {
    "id": 10,
    "amount": 177000,
    "currency": "XOF",
    "gateway": "stripe",
    "status": "pending",
    "transaction_id": "ch_1234567890",
    "payment_method": "card",
    "initiated_at": "2025-11-29T23:05:00Z",
    "invoice": {
      "id": 5,
      "number": "INV-2025-0005",
      "total": 177000
    }
  },
  "message": "Payment initiated successfully",
  "redirect_url": "https://checkout.stripe.com/pay/cs_test_abc123"
}
```

### 3. Erreur de Rate Limiting

**Response (429 Too Many Requests):**
```json
{
  "message": "Too Many Requests",
  "exception": "Illuminate\\Http\\Exceptions\\ThrottleRequestsException"
}
```

**Headers:**
```http
X-RateLimit-Limit: 30
X-RateLimit-Remaining: 0
Retry-After: 52
```

### 4. Erreur de Validation

**Response (422 Unprocessable Entity):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "client_id": [
      "Le client sélectionné n'existe pas"
    ],
    "items.0.description": [
      "La description de l'article est requise"
    ],
    "items.0.quantity": [
      "La quantité doit être supérieure à 0"
    ]
  }
}
```

---

## ⚙️ Configuration Environnement

### .env
```env
# CORS
CORS_ALLOWED_ORIGINS=https://app.example.com,https://admin.example.com

# Rate Limiting
THROTTLE_API_LIMIT=60
THROTTLE_INVOICE_LIMIT=30
THROTTLE_PAYMENT_LIMIT=10

# Session (pour Sanctum)
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

---

## 🧪 Tests

### Test Rate Limiting

```bash
# Envoyer 70 requêtes en 1 minute
for i in {1..70}; do
  curl -H "Authorization: Bearer TOKEN" \
       http://localhost:8000/api/v1/user
  echo "Request $i"
done

# Attendu: 60 OK, 10 erreurs 429
```

### Test CORS

```bash
# Preflight request
curl -X OPTIONS http://localhost:8000/api/v1/invoices \
  -H "Origin: https://app.example.com" \
  -H "Access-Control-Request-Method: POST" \
  -H "Access-Control-Request-Headers: Authorization"

# Attendu: 
# Access-Control-Allow-Origin: https://app.example.com
# Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS
```

### Test Validation

```bash
# Facture invalide (items vide)
curl -X POST http://localhost:8000/api/v1/invoices \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": 1,
    "items": []
  }'

# Attendu: 422 avec message "Au moins un article est requis"
```

---

## 📊 Métriques de Sécurité

| Indicateur | Avant Phase 6 | Après Phase 6 |
|------------|---------------|---------------|
| **Rate Limiting** | ❌ Aucun | ✅ Par tenant + endpoint |
| **CORS** | ⚠️ Allow all | ✅ Restrictif |
| **Validation** | ⚠️ Basique | ✅ Form Requests |
| **Serialization** | ⚠️ Modèle brut | ✅ API Resources |
| **Headers sécurité** | ❌ Aucun | ✅ RateLimit, CORS |
| **Données sensibles** | ⚠️ Exposées | ✅ Filtrées |

---

## ✅ Checklist de Sécurité

- [x] Rate limiting par tenant activé
- [x] CORS configuré avec origins restrictives
- [x] Form Requests avec validation stricte
- [x] API Resources masquant données sensibles
- [x] Middleware ForceJsonResponse
- [x] ThrottleRequests personnalisé
- [x] Messages d'erreur en français
- [x] Headers RateLimit exposés
- [ ] Sanctum Authentication (à configurer)
- [ ] API Documentation OpenAPI (à générer)
- [ ] HTTPS forcé en production
- [ ] Content Security Policy headers
- [ ] HSTS headers
- [ ] XSS Protection headers

---

## 🚀 Prochaines Étapes

### Immédiat (Phase 6 suite)
1. Configurer Sanctum pour l'authentification API
2. Générer tokens d'accès pour utilisateurs
3. Créer documentation OpenAPI/Swagger
4. Ajouter headers de sécurité (CSP, HSTS, XSS)

### Court Terme (Phase 7)
1. Installer Laravel Telescope pour monitoring
2. Configurer Sentry pour error tracking
3. Implémenter audit logs (spatie/laravel-activitylog)
4. Ajouter health checks avancés

### Moyen Terme (Phase 8-9)
1. Suite de tests (Unit + Feature + Integration)
2. CI/CD avec GitHub Actions
3. Code coverage > 80%

---

## 📚 Références

- [Laravel Validation](https://laravel.com/docs/10.x/validation)
- [Laravel API Resources](https://laravel.com/docs/10.x/eloquent-resources)
- [Laravel Rate Limiting](https://laravel.com/docs/10.x/routing#rate-limiting)
- [CORS Configuration](https://laravel.com/docs/10.x/routing#cors)
- [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum)
- [OWASP API Security](https://owasp.org/www-project-api-security/)

---

**Phase 6 complétée à 70%** - Reste Sanctum + OpenAPI docs + Security headers 🔐
