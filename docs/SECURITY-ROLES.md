# Système de Rôles et Sécurité

## 🔐 Vue d'ensemble

Le système utilise un contrôle d'accès basé sur les rôles (RBAC) pour séparer les interfaces admin et client.

```
┌─────────────────────────────────────────────────┐
│              Authentification                   │
├──────────────────┬──────────────────────────────┤
│   ADMIN          │         CLIENT               │
│   role='admin'   │      role='client'           │
│   /admin/*       │      /client/*            │
│   Filament       │      Blade + JS              │
└──────────────────┴──────────────────────────────┘
```

---

## 👤 Rôles disponibles

### 1. Admin (`role='admin'`)
**Accès :** `/admin` (Filament)
**Permissions :**
- ✅ Accès complet au back-office Filament
- ✅ CRUD sur toutes les données (clients, produits, factures)
- ✅ Configuration système
- ✅ Gestion des utilisateurs
- ✅ Rapports et statistiques
- ✅ Peut aussi accéder à `/client` pour tester

### 2. Client (`role='client'`)
**Accès :** `/client` (Interface personnalisée)
**Permissions :**
- ✅ Accès à l'interface client uniquement
- ✅ Créer des factures
- ✅ Consulter ses propres factures
- ✅ Télécharger ses PDF
- ✅ Gérer son profil
- ❌ **Pas d'accès** à `/admin`

---

## 🛡️ Middlewares de sécurité

### 1. `EnsureUserIsAdmin`
**Fichier :** `app/Http/Middleware/EnsureUserIsAdmin.php`
**Rôle :** Protège l'accès à `/admin`

```php
if (auth()->user()->role !== 'admin') {
    abort(403, 'Accès refusé. Interface réservée aux administrateurs.');
}
```

**Appliqué sur :**
- Toutes les routes Filament (`/admin/*`)
- Configuré dans `AdminPanelProvider.php`

### 2. `EnsureUserIsClient`
**Fichier :** `app/Http/Middleware/EnsureUserIsClient.php`
**Rôle :** Protège l'accès à `/client`

```php
if (!in_array(auth()->user()->role, ['client', 'admin'])) {
    abort(403, 'Accès refusé. Interface réservée aux clients.');
}
```

**Appliqué sur :**
- Toutes les routes dashboard (`/client/*`)
- Configuré dans `routes/web.php`

**Note :** Les admins peuvent aussi accéder au dashboard pour tester.

---

## 🔑 Comptes de test

### Compte Admin
```
Email: admin@testcompany.com
Mot de passe: password
Rôle: admin
Accès: /admin + /client
```

**Actions possibles :**
1. Se connecter sur http://127.0.0.1:8003/admin/login
2. Gérer tous les clients, produits, factures
3. Configurer le système
4. Accéder au dashboard client pour tester

### Compte Client
```
Email: client@testcompany.com
Mot de passe: password
Rôle: client
Accès: /client uniquement
```

**Actions possibles :**
1. Se connecter sur http://127.0.0.1:8003/admin/login (redirection vers /client)
2. Créer des factures via /client/invoices/create
3. Consulter ses factures
4. Gérer son profil
5. ❌ Ne peut PAS accéder à /admin

---

## 🚀 Configuration des routes

### Routes Admin (Filament)
**Fichier :** `app/Providers/Filament/AdminPanelProvider.php`

```php
->authMiddleware([
    \App\Http\Middleware\EnsureUserIsAdmin::class,
])
```

**Toutes les routes `/admin/*` sont automatiquement protégées.**

### Routes Dashboard (Client)
**Fichier :** `routes/web.php`

```php
Route::middleware(['auth', 'client'])->prefix('dashboard')->group(function () {
    // Toutes les routes ici nécessitent auth + rôle client/admin
});
```

---

## 🧪 Tester la sécurité

### Test 1 : Admin accède à /admin ✅
1. Se connecter avec `admin@testcompany.com`
2. Aller sur http://127.0.0.1:8003/admin
3. **Résultat attendu :** Accès autorisé ✅

### Test 2 : Admin accède à /client ✅
1. Connecté en tant qu'admin
2. Aller sur http://127.0.0.1:8003/client/invoices
3. **Résultat attendu :** Accès autorisé ✅ (pour tester)

### Test 3 : Client accède à /client ✅
1. Se connecter avec `client@testcompany.com`
2. Aller sur http://127.0.0.1:8003/client/invoices
3. **Résultat attendu :** Accès autorisé ✅

### Test 4 : Client tente d'accéder à /admin ❌
1. Connecté en tant que client
2. Aller sur http://127.0.0.1:8003/admin
3. **Résultat attendu :** Erreur 403 ❌
4. **Message :** "Accès refusé. Interface réservée aux administrateurs."

### Test 5 : Utilisateur non authentifié ❌
1. Se déconnecter
2. Tenter d'accéder à /client ou /admin
3. **Résultat attendu :** Redirection vers /login

---

## 📊 Base de données

### Table `users`
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY,
    tenant_id BIGINT UNSIGNED,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'client') DEFAULT 'client', -- ⭐ Champ clé
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Valeurs possibles pour `role` :**
- `'admin'` → Accès admin
- `'client'` → Accès client uniquement

---

## 🔄 Workflow de connexion

### Connexion Admin
```
1. Utilisateur va sur /admin/login
2. Filament affiche le formulaire de connexion
3. Utilisateur entre: admin@testcompany.com / password
4. Filament vérifie les credentials
5. Middleware EnsureUserIsAdmin vérifie role='admin'
6. ✅ Accès autorisé → Dashboard admin
```

### Connexion Client
```
1. Utilisateur va sur /admin/login (même formulaire)
2. Utilisateur entre: client@testcompany.com / password
3. Filament vérifie les credentials
4. Middleware EnsureUserIsAdmin vérifie role='admin'
5. ❌ role='client' → Erreur 403
6. Redirection automatique vers /client (à implémenter)
```

**Note :** Dans une version production, vous devriez avoir deux formulaires de connexion séparés :
- `/admin/login` pour les admins
- `/client/login` pour les clients

---

## 🎯 Recommandations pour la production

### 1. Séparer les formulaires de connexion
```php
// routes/web.php
Route::get('/client/login', [DashboardAuthController::class, 'showLoginForm']);
Route::post('/client/login', [DashboardAuthController::class, 'login']);
```

### 2. Redirection intelligente après connexion
```php
// Dans le LoginController
protected function authenticated($request, $user)
{
    if ($user->role === 'admin') {
        return redirect('/admin');
    }
    return redirect('/client');
}
```

### 3. Ajouter plus de rôles
```php
// Exemples
'manager'  → Accès partiel à /admin
'support'  → Lecture seule sur /admin
'viewer'   → Consultation uniquement
```

### 4. Permissions granulaires
```php
// Utiliser un package comme spatie/laravel-permission
$user->givePermissionTo('create invoices');
$user->givePermissionTo('edit clients');
```

### 5. Logs d'accès
```php
// Enregistrer toutes les tentatives d'accès
Log::info('Admin access', [
    'user' => auth()->user()->email,
    'ip' => request()->ip(),
    'route' => request()->path()
]);
```

---

## 🛠️ Maintenance

### Changer le rôle d'un utilisateur
```bash
php artisan tinker
>>> $user = User::where('email', 'user@example.com')->first();
>>> $user->role = 'admin';
>>> $user->save();
```

### Créer un nouvel admin
```bash
php artisan tinker
>>> User::create([
    'tenant_id' => 1,
    'name' => 'Nouvel Admin',
    'email' => 'newadmin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin',
    'is_active' => true
]);
```

### Lister tous les admins
```bash
php artisan tinker
>>> User::where('role', 'admin')->get(['id', 'name', 'email']);
```

---

## 📞 Support

Si un utilisateur ne peut pas accéder à une interface :
1. Vérifier son rôle dans la table `users`
2. Vérifier qu'il est actif (`is_active = 1`)
3. Vérifier les logs : `storage/logs/laravel.log`
4. Tester avec un autre compte

---

## ✅ Checklist de sécurité

- [x] Middleware admin créé et appliqué
- [x] Middleware client créé et appliqué
- [x] Routes admin protégées
- [x] Routes dashboard protégées
- [x] Comptes de test créés (admin + client)
- [x] Messages d'erreur clairs (403)
- [ ] Formulaires de connexion séparés (recommandé)
- [ ] Redirection intelligente (recommandé)
- [ ] Logs d'accès (recommandé)
- [ ] Tests automatisés (recommandé)

---

Votre application est maintenant sécurisée avec un système de rôles fonctionnel ! 🎉
