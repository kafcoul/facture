# 🔐 SYSTÈME DE RÔLES ET REDIRECTIONS

## 📋 Architecture Actuelle

### Trois Interfaces Distinctes

| Interface | URL | Accès | Description |
|-----------|-----|-------|-------------|
| **Landing** | `/` | Public | Page marketing pour visiteurs |
| **Client** | `/client` | Authentifié | Interface de facturation (front-office) |
| **Admin** | `/admin` | Admin only | Back-office Filament (gestion avancée) |

---

## 👥 Rôles Utilisateurs

### 1. **Rôle "admin"**
- ✅ Peut accéder à `/admin` (Filament)
- ✅ Peut accéder à `/client` (Interface client)
- ✅ Premier utilisateur qui crée l'entreprise
- ✅ Peut gérer : clients, produits, factures, utilisateurs

### 2. **Rôle "client"**
- ❌ NE peut PAS accéder à `/admin`
- ✅ Peut accéder à `/client` (Interface client)
- ✅ Utilisateurs ajoutés par l'admin
- ✅ Peut gérer : ses propres factures

---

## 🚀 Flux d'Inscription Actuel

### Étape 1 : Utilisateur s'inscrit sur `/register`
1. Choisit un plan (Starter/Pro/Enterprise)
2. Remplit le formulaire (entreprise, nom, email, mot de passe)
3. Accepte les conditions

### Étape 2 : Création automatique
```php
// 1. Création du TENANT (entreprise)
$tenant = Tenant::create([
    'name' => 'Ma Super Entreprise',
    'plan' => 'pro',
    'trial_ends_at' => now()->addDays(30),
]);

// 2. Création de l'UTILISATEUR (premier = admin)
$user = User::create([
    'tenant_id' => $tenant->id,
    'name' => 'Jean Dupont',
    'email' => 'jean@masuperentreprise.com',
    'role' => 'admin', // ← Premier utilisateur = admin
]);

// 3. Connexion automatique
Auth::login($user);

// 4. Redirection vers /client (interface principale)
return redirect()->route('client.index');
```

### Étape 3 : Redirection
- ✅ **AVANT** : Redirection vers `/admin` (Filament)
- ✅ **MAINTENANT** : Redirection vers `/client` (Interface client)

**Pourquoi ce changement ?**
- `/client` est l'interface **principale** de facturation
- Plus intuitif pour un nouvel utilisateur
- L'admin peut toujours accéder à `/admin` quand il en a besoin

---

## 🎯 Scénarios d'Usage

### Scénario 1 : Fondateur d'entreprise (Solo)
```
1. S'inscrit sur /register
2. Devient "admin" automatiquement
3. Redirigé vers /client
4. Utilise /client pour créer ses factures
5. Peut accéder à /admin pour gestion avancée si besoin
```

### Scénario 2 : Fondateur + Équipe
```
1. Fondateur s'inscrit → rôle "admin"
2. Fondateur invite des collaborateurs via /admin
3. Collaborateurs créés avec rôle "client"
4. Collaborateurs peuvent seulement accéder à /client
5. Admin peut tout gérer via /admin et /client
```

### Scénario 3 : Grande Entreprise (Multi-utilisateurs)
```
1. Admin principal (fondateur)
2. Admins secondaires (comptables, managers)
3. Clients (commerciaux, équipe)
4. Tous utilisent /client pour la facturation
5. Admins utilisent /admin pour la gestion
```

---

## 🔒 Sécurité et Middlewares

### Middleware `auth`
```php
Route::middleware(['auth'])->group(function () {
    // Routes accessibles à tous les utilisateurs connectés
});
```

### Middleware `client`
```php
Route::middleware(['auth', 'client'])->prefix('client')->group(function () {
    // Routes /client accessibles aux roles 'admin' ET 'client'
});
```

### Middleware `admin` (Filament)
```php
// /admin protégé par Filament
// Seulement accessible aux users avec role = 'admin'
```

---

## 📊 Matrice des Permissions

| Action | Admin | Client | Public |
|--------|-------|--------|--------|
| Voir landing page | ✅ | ✅ | ✅ |
| S'inscrire | ✅ | ✅ | ✅ |
| Accéder `/client` | ✅ | ✅ | ❌ |
| Créer factures | ✅ | ✅ | ❌ |
| Voir ses factures | ✅ | ✅ | ❌ |
| Accéder `/admin` | ✅ | ❌ | ❌ |
| Gérer clients | ✅ | ❌ | ❌ |
| Gérer produits | ✅ | ❌ | ❌ |
| Gérer utilisateurs | ✅ | ❌ | ❌ |
| Gérer paramètres | ✅ | ❌ | ❌ |

---

## 🛠️ Options de Configuration

### Option 1 : Tous les inscrits sont "clients" simples ❌
```php
// Dans RegisterWithPlanController.php
'role' => 'client', // ← Utilisateurs sans accès admin
```

**Problème** : Qui gère l'entreprise ? Qui accède à `/admin` ?

### Option 2 : Premier utilisateur = "admin" ✅ (ACTUEL)
```php
// Dans RegisterWithPlanController.php
'role' => 'admin', // ← Premier utilisateur = admin de l'entreprise
```

**Avantages** :
- Fondateur a accès complet
- Peut inviter d'autres utilisateurs
- Peut gérer toute l'entreprise

### Option 3 : Redirection intelligente selon rôle
```php
// 4. Redirection selon le rôle
if ($user->role === 'admin') {
    return redirect()->route('client.index'); // Interface principale
} else {
    return redirect()->route('client.index'); // Même chose pour les clients
}
```

---

## 🎨 Interface `/client` vs `/admin`

### Interface `/client` (Front-office)
- 🎯 **Public cible** : Tous les utilisateurs (admin + clients)
- 🎨 **Design** : Interface moderne et épurée
- 📱 **Features** :
  - Dashboard avec statistiques
  - Liste des factures
  - Création de factures
  - Gestion des paiements
  - Profil utilisateur
  - Paramètres basiques

### Interface `/admin` (Back-office)
- 🎯 **Public cible** : Admins uniquement
- 🎨 **Design** : Filament (admin panel)
- 📱 **Features** :
  - Gestion complète des clients
  - Gestion des produits
  - Gestion des factures (toutes)
  - Gestion des utilisateurs
  - Paramètres avancés
  - Rapports et analytics

---

## ✅ Recommandation Finale

**Configuration actuelle (après correction)** :
```php
// Inscription publique
$user = User::create([
    'role' => 'admin', // Premier utilisateur = admin
]);

// Redirection
return redirect()->route('client.index'); // Interface principale /client
```

**Pourquoi cette config ?**
1. ✅ Fondateur a les pleins pouvoirs (`admin`)
2. ✅ Il commence par l'interface principale (`/client`)
3. ✅ Il peut accéder à `/admin` quand nécessaire
4. ✅ Plus intuitif pour découvrir le produit
5. ✅ Évite de perdre l'utilisateur dans Filament

**Flux utilisateur optimal** :
```
Inscription → /client (découverte + premières factures)
            ↓
          Besoin avancé → /admin (gestion complète)
```

---

## 🔄 Comment Ajouter des Utilisateurs

### Via `/admin` (Filament)
1. Admin se connecte
2. Va sur `/admin`
3. Clique sur "Users"
4. Crée un nouvel utilisateur
5. Choisit le rôle : `admin` ou `client`

### Via Code (Seeder)
```php
// Admin secondaire
User::create([
    'tenant_id' => $tenant->id,
    'name' => 'Marie Comptable',
    'email' => 'marie@entreprise.com',
    'password' => Hash::make('password'),
    'role' => 'admin', // Peut gérer l'entreprise
]);

// Client simple
User::create([
    'tenant_id' => $tenant->id,
    'name' => 'Paul Commercial',
    'email' => 'paul@entreprise.com',
    'password' => Hash::make('password'),
    'role' => 'client', // Seulement /client
]);
```

---

## 📝 Résumé

| Point | Valeur |
|-------|--------|
| **Premier utilisateur** | `admin` (fondateur) |
| **Redirection après inscription** | `/client` (interface principale) |
| **Accès `/admin`** | Réservé aux admins |
| **Accès `/client`** | Ouvert à admin + clients |
| **Interface recommandée** | `/client` (plus intuitive) |

**Statut** : ✅ Configuration optimale pour un SaaS de facturation

---

**Date** : 30 Novembre 2025  
**Changement** : Redirection `/admin` → `/client` après inscription
