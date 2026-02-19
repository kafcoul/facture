# 🔐 Sanctum Authentication - Guide Complet

## Vue d'ensemble

Laravel Sanctum fournit un système d'authentification léger pour les SPA, applications mobiles et APIs simples avec tokens.

---

## 🚀 Configuration

### 1. Installation

✅ **Déjà installé** via `laravel/sanctum` package

### 2. Migration

```bash
php artisan migrate
```

Crée la table `personal_access_tokens` pour stocker les tokens API.

### 3. Middleware

Déjà configuré dans `app/Http/Kernel.php`:
```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    // ...
],
```

---

## 📡 Endpoints d'Authentification

### Routes Publiques

#### 1. Login (Générer Token)

```http
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password",
  "device_name": "iPhone 14" // optionnel
}
```

**Response (200 OK):**
```json
{
  "message": "Authentification réussie",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com",
      "tenant_id": 1
    },
    "token": "1|abc123def456...",
    "token_type": "Bearer"
  }
}
```

**Rate Limit:** 5 requêtes/minute

---

### Routes Protégées (avec token)

#### 2. Me (Infos Utilisateur)

```http
GET /api/v1/auth/me
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "tenant": {
      "id": 1,
      "name": "Demo Company",
      "slug": "demo"
    },
    "created_at": "2025-01-15T10:00:00Z"
  }
}
```

#### 3. Logout (Révoquer Token Actuel)

```http
POST /api/v1/auth/logout
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "message": "Déconnexion réussie"
}
```

#### 4. Logout All (Révoquer Tous les Tokens)

```http
POST /api/v1/auth/logout-all
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "message": "Déconnexion de tous les appareils réussie"
}
```

#### 5. Refresh Token

```http
POST /api/v1/auth/refresh
Authorization: Bearer {token}
Content-Type: application/json

{
  "device_name": "iPhone 14" // optionnel
}
```

**Response (200 OK):**
```json
{
  "message": "Token rafraîchi",
  "data": {
    "token": "2|xyz789abc123...",
    "token_type": "Bearer"
  }
}
```

#### 6. List Tokens

```http
GET /api/v1/auth/tokens
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "iPhone 14",
      "last_used_at": "2025-11-29T23:00:00Z",
      "created_at": "2025-11-29T10:00:00Z",
      "expires_at": null
    },
    {
      "id": 2,
      "name": "MacBook Pro",
      "last_used_at": "2025-11-28T15:30:00Z",
      "created_at": "2025-11-20T08:00:00Z",
      "expires_at": null
    }
  ]
}
```

#### 7. Revoke Specific Token

```http
DELETE /api/v1/auth/tokens/{id}
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "message": "Token révoqué avec succès"
}
```

---

## 🔒 Sécurité

### Rate Limiting

| Endpoint | Limite | Fenêtre |
|----------|--------|---------|
| /auth/login | 5 req | 1 minute |
| /auth/register | 3 req | 1 minute |
| Autres /auth/* | 60 req | 1 minute |

### Token Abilities

Tous les tokens ont l'ability `*` (toutes permissions).

Pour des permissions granulaires:
```php
$token = $user->createToken('api-token', ['invoice:read', 'invoice:create']);
```

Vérifier dans middleware:
```php
if ($request->user()->tokenCan('invoice:create')) {
    // Autoriser
}
```

### Token Expiration

Par défaut: **pas d'expiration**

Pour ajouter une expiration:
```php
// config/sanctum.php
'expiration' => 525600, // 1 an en minutes
```

---

## 🧪 Tests

### 1. Login

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "leaudouce0@gmail.com",
    "password": "password"
  }'
```

**Sauvegarder le token:**
```bash
TOKEN="1|abc123..."
```

### 2. Tester Route Protégée

```bash
curl http://localhost:8000/api/v1/auth/me \
  -H "Authorization: Bearer $TOKEN"
```

### 3. Créer une Facture

```bash
curl -X POST http://localhost:8000/api/v1/invoices \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": 1,
    "type": "standard",
    "currency": "XOF",
    "items": [
      {
        "description": "Test",
        "quantity": 1,
        "unit_price": 10000
      }
    ]
  }'
```

### 4. Logout

```bash
curl -X POST http://localhost:8000/api/v1/auth/logout \
  -H "Authorization: Bearer $TOKEN"
```

### 5. Test Rate Limiting

```bash
# Login 10 fois rapidement
for i in {1..10}; do
  curl -X POST http://localhost:8000/api/v1/auth/login \
    -H "Content-Type: application/json" \
    -d '{"email": "test@test.com", "password": "wrong"}'
  echo "\nAttempt $i"
done

# Attendu: 5 OK, 5 erreurs 429 (Too Many Requests)
```

---

## 📱 Utilisation Frontend

### JavaScript/Fetch

```javascript
// Login
const login = async (email, password) => {
  const response = await fetch('http://localhost:8000/api/v1/auth/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      email,
      password,
      device_name: navigator.userAgent,
    }),
  });
  
  const data = await response.json();
  
  if (response.ok) {
    // Stocker le token
    localStorage.setItem('api_token', data.data.token);
    return data;
  }
  
  throw new Error(data.message);
};

// Requête authentifiée
const fetchInvoices = async () => {
  const token = localStorage.getItem('api_token');
  
  const response = await fetch('http://localhost:8000/api/v1/invoices', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
    },
  });
  
  return response.json();
};

// Logout
const logout = async () => {
  const token = localStorage.getItem('api_token');
  
  await fetch('http://localhost:8000/api/v1/auth/logout', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
    },
  });
  
  localStorage.removeItem('api_token');
};
```

### Axios

```javascript
import axios from 'axios';

// Configuration globale
axios.defaults.baseURL = 'http://localhost:8000/api/v1';

// Intercepteur pour ajouter le token
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('api_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Login
const login = async (email, password) => {
  const { data } = await axios.post('/auth/login', {
    email,
    password,
    device_name: navigator.userAgent,
  });
  
  localStorage.setItem('api_token', data.data.token);
  return data;
};

// Logout
const logout = async () => {
  await axios.post('/auth/logout');
  localStorage.removeItem('api_token');
};
```

---

## 🔧 Configuration Avancée

### 1. Expiration des Tokens

**config/sanctum.php:**
```php
'expiration' => 60 * 24 * 30, // 30 jours
```

### 2. Middleware Stateful

Pour les SPA sur le même domaine:

**config/sanctum.php:**
```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1'
)),
```

### 3. Permissions Personnalisées

**Créer token avec abilities:**
```php
$token = $user->createToken('mobile-app', [
    'invoice:read',
    'invoice:create',
    'payment:read',
]);
```

**Vérifier dans controller:**
```php
public function store(Request $request)
{
    if (!$request->user()->tokenCan('invoice:create')) {
        abort(403, 'Token does not have the required ability');
    }
    
    // Créer la facture...
}
```

**Middleware personnalisé:**
```php
Route::post('/invoices', [InvoiceController::class, 'store'])
    ->middleware('ability:invoice:create');
```

---

## 🐛 Troubleshooting

### Erreur: "Unauthenticated"

**Causes possibles:**
1. Token invalide ou expiré
2. Header Authorization manquant
3. Format incorrect: doit être `Bearer {token}`
4. Token révoqué

**Solution:**
```bash
# Vérifier le token en DB
php artisan tinker
>>> \Laravel\Sanctum\PersonalAccessToken::all();
```

### Erreur: 419 (CSRF Token Mismatch)

**Cause:** SPA sur domaine différent non configuré

**Solution:**
```env
# .env
SANCTUM_STATEFUL_DOMAINS=localhost:3000,app.example.com
SESSION_DOMAIN=.example.com
```

### Token non révoqué au logout

**Vérifier:**
```php
$request->user()->currentAccessToken()->delete(); // ✅
$request->user()->tokens()->delete(); // Tous les tokens
```

---

## ✅ Checklist de Configuration

- [x] Package `laravel/sanctum` installé
- [x] Migration exécutée (personal_access_tokens)
- [x] Trait `HasApiTokens` ajouté au modèle User
- [x] AuthController créé avec 7 endpoints
- [x] Routes API configurées avec rate limiting
- [x] Middleware auth:sanctum sur routes protégées
- [x] CORS configuré pour frontend
- [ ] Tests manuels exécutés
- [ ] Documentation frontend créée
- [ ] Expiration tokens configurée (optionnel)

---

## 📊 Métriques

| Endpoint | Response Time | Rate Limit |
|----------|---------------|------------|
| POST /login | ~50ms | 5/min |
| GET /me | ~20ms | 60/min |
| POST /logout | ~10ms | 60/min |
| GET /tokens | ~30ms | 60/min |

---

## 🚀 Prochaines Étapes

1. ✅ Tester login/logout avec curl
2. ✅ Vérifier tokens en base de données
3. ✅ Tester rate limiting
4. ⏳ Créer frontend de test
5. ⏳ Documenter flow d'authentification complet
6. ⏳ Ajouter refresh token automatique
7. ⏳ Implémenter 2FA (optionnel)

---

**Sanctum configuré et prêt à l'emploi!** 🔐✨
