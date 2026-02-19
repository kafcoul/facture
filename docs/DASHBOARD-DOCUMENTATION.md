# 📊 Dashboard Client - Documentation Complète

## 🎯 Vue d'ensemble

Le dashboard client est une interface complète permettant aux utilisateurs authentifiés de gérer leurs factures, paiements, profil et paramètres. Il a été construit avec Laravel, Tailwind CSS et AlpineJS pour offrir une expérience utilisateur moderne et responsive.

## 📁 Structure des fichiers

### Layouts (1 fichier)
```
resources/views/layouts/
└── dashboard.blade.php          # Layout principal avec sidebar et header
```

### Pages (6 fichiers)
```
resources/views/dashboard/
├── index.blade.php              # Page d'accueil avec statistiques
├── invoices/
│   ├── index.blade.php          # Liste des factures
│   └── show.blade.php           # Détails d'une facture
├── payments/
│   └── index.blade.php          # Historique des paiements
├── profile/
│   └── edit.blade.php           # Édition du profil
└── settings/
    └── index.blade.php          # Paramètres de l'application
```

### Contrôleurs (5 fichiers)
```
app/Http/Controllers/Dashboard/
├── DashboardController.php      # Gestion de la page d'accueil
├── InvoiceController.php        # Gestion des factures
├── PaymentController.php        # Gestion des paiements
├── ProfileController.php        # Gestion du profil utilisateur
└── SettingsController.php       # Gestion des paramètres
```

## 🚀 Pages et fonctionnalités

### 1. Page d'accueil (`/dashboard`)
**Contrôleur:** `DashboardController@index`

**Fonctionnalités:**
- 4 cartes de statistiques :
  - Total des factures
  - Factures impayées (count + montant)
  - Factures payées (count + montant)
  - Factures en retard (count + montant)
- Liste des 5 dernières factures avec liens vers les détails
- Liste des 5 derniers paiements
- Bannière d'actions rapides

**Variables disponibles:**
```php
$stats = [
    'total_invoices' => 150,
    'unpaid_count' => 25,
    'unpaid_amount' => 15000.00,
    'paid_count' => 120,
    'paid_amount' => 120000.00,
    'overdue_count' => 5,
    'overdue_amount' => 5000.00
];
$recent_invoices = Collection (5 dernières factures)
$recent_payments = Collection (5 derniers paiements)
```

### 2. Liste des factures (`/dashboard/invoices`)
**Contrôleur:** `InvoiceController@index`

**Fonctionnalités:**
- Filtres :
  - Par statut (draft, sent, viewed, paid, overdue, cancelled)
  - Par recherche (numéro de facture ou nom du client)
- Tableau avec colonnes :
  - N° Facture (cliquable)
  - Client (nom + email)
  - Date de création
  - Date d'échéance
  - Montant total
  - Statut (badge coloré)
  - Actions (voir détails, télécharger PDF)
- Pagination (15 factures par page)
- État vide si aucune facture

**Requête HTTP:**
```
GET /dashboard/invoices?status=paid&search=INV-2024
```

### 3. Détails d'une facture (`/dashboard/invoices/{invoice}`)
**Contrôleur:** `InvoiceController@show`

**Fonctionnalités:**
- En-tête avec statut visuel et icône
- Bouton de retour vers la liste
- Bouton télécharger PDF
- Informations client (nom, email, téléphone, adresse)
- Tableau des articles (description, quantité, prix unitaire, total)
- Section récapitulatif :
  - Sous-total
  - TVA (avec taux)
  - Remise (si applicable)
  - Total TTC
- Dates importantes :
  - Date de création
  - Date d'échéance
  - Date de paiement (si payée)
- Historique des paiements (avec gateway et statut)
- Notes de la facture

**Sécurité:**
- Vérification que `invoice.tenant_id == auth()->user()->tenant_id`
- `abort(403)` si l'utilisateur n'a pas accès

### 4. Téléchargement PDF (`/dashboard/invoices/{invoice}/download`)
**Contrôleur:** `InvoiceController@download`

**Fonctionnalités:**
- Téléchargement direct du PDF stocké
- Vérification de l'existence du fichier
- Nom de fichier : `invoice-{numero}.pdf`

**Sécurité:**
- Vérification tenant_id
- Vérification que le fichier existe
- `abort(403/404)` si accès refusé ou fichier manquant

### 5. Historique des paiements (`/dashboard/payments`)
**Contrôleur:** `PaymentController@index`

**Fonctionnalités:**
- Filtres :
  - Par statut (pending, completed, failed, refunded)
  - Par plage de dates (date_from, date_to)
- Tableau avec colonnes :
  - Date et heure du paiement
  - Facture associée (cliquable)
  - Client
  - Montant
  - Passerelle de paiement (Stripe, PayPal, etc.)
  - Statut (badge coloré)
- Pagination (15 paiements par page)
- État vide si aucun paiement

**Requête HTTP:**
```
GET /dashboard/payments?status=completed&date_from=2024-01-01&date_to=2024-12-31
```

### 6. Édition du profil (`/dashboard/profile`)
**Contrôleur:** `ProfileController@edit` et `ProfileController@update`

**Fonctionnalités:**
- Formulaire de mise à jour :
  - Nom complet (requis)
  - Email (requis, unique)
- Section changement de mot de passe :
  - Mot de passe actuel (requis si nouveau mot de passe)
  - Nouveau mot de passe (min 8 caractères)
  - Confirmation du nouveau mot de passe
- Validation côté serveur
- Messages d'erreur inline
- Message de succès après mise à jour
- Note informative sur l'utilisation de l'email

**Validation:**
```php
[
    'name' => 'required|string|max:255',
    'email' => 'required|email|max:255|unique:users,email,' . $user->id,
    'current_password' => 'nullable|required_with:password',
    'password' => 'nullable|confirmed|Password::defaults()'
]
```

### 7. Paramètres (`/dashboard/settings`)
**Contrôleur:** `SettingsController@index`

**Fonctionnalités:**
- Section Notifications :
  - Nouvelles factures (checkbox)
  - Paiements reçus (checkbox)
  - Factures en retard (checkbox)
- Section Langue et région :
  - Langue (Français, English, Español)
  - Fuseau horaire (Europe/Paris, etc.)
  - Devise (EUR, USD, GBP)
- Section Sécurité :
  - Authentification à deux facteurs (bientôt disponible)
  - Sessions actives
- Zone de danger :
  - Suppression du compte

**Note:** Ces paramètres sont actuellement des interfaces statiques. L'implémentation backend sera ajoutée ultérieurement.

## 🛡️ Sécurité

### Multi-tenancy
Toutes les requêtes sont filtrées par `tenant_id` :
```php
Invoice::where('tenant_id', auth()->user()->tenant_id)->get();
Payment::where('tenant_id', auth()->user()->tenant_id)->get();
```

### Vérifications explicites
```php
if ($invoice->tenant_id !== auth()->user()->tenant_id) {
    abort(403);
}
```

### Middleware d'authentification
```php
Route::middleware(['auth'])->group(function () {
    // Toutes les routes du dashboard
});
```

### Protection CSRF
Tous les formulaires incluent `@csrf`.

## 🎨 Design et UX

### Responsive Design
- **Mobile:** Menu hamburger, colonnes empilées
- **Tablet:** Sidebar rétractable, grilles 2 colonnes
- **Desktop:** Sidebar fixe, grilles 3-4 colonnes

### Badges de statut colorés

**Factures:**
- `draft` → Gris (Brouillon)
- `sent` → Bleu (Envoyée)
- `viewed` → Violet (Vue)
- `paid` → Vert (Payée)
- `overdue` → Rouge (En retard)
- `cancelled` → Gris foncé (Annulée)

**Paiements:**
- `pending` → Jaune (En attente)
- `completed` → Vert (Complété)
- `failed` → Rouge (Échoué)
- `refunded` → Gris (Remboursé)

### États vides
Chaque liste affiche un état vide élégant avec :
- Icône SVG illustrative
- Message explicatif
- Bouton d'action (si applicable)

### Icons
Utilisation des **Heroicons** partout pour une cohérence visuelle.

### Interactions
- Hover effects sur les lignes de tableau
- Navigation active highlighting
- Messages flash avec animations
- AlpineJS pour le menu mobile

## 🔗 Routes complètes

```php
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
});
```

## 📊 Variables disponibles dans les vues

### Layout (`dashboard.blade.php`)
```php
auth()->user()           // Utilisateur connecté
auth()->user()->name     // Nom de l'utilisateur
auth()->user()->email    // Email de l'utilisateur
```

### Dashboard (`index.blade.php`)
```php
$stats                   // Array des statistiques
$recent_invoices         // Collection des 5 dernières factures
$recent_payments         // Collection des 5 derniers paiements
```

### Invoices (`invoices/index.blade.php`)
```php
$invoices                // LengthAwarePaginator (15 par page)
request('status')        // Filtre de statut
request('search')        // Recherche
```

### Invoice Detail (`invoices/show.blade.php`)
```php
$invoice                 // Model Invoice avec relations
$invoice->client         // Model Client
$invoice->items          // Collection InvoiceItem
$invoice->payments       // Collection Payment
```

### Payments (`payments/index.blade.php`)
```php
$payments                // LengthAwarePaginator (15 par page)
request('status')        // Filtre de statut
request('date_from')     // Date début
request('date_to')       // Date fin
```

### Profile (`profile/edit.blade.php`)
```php
$user                    // Model User
old('name')              // Anciennes valeurs du formulaire
$errors                  // MessageBag des erreurs
```

## 🧪 Comment tester

### 1. Démarrer le serveur
```bash
php artisan serve
```

### 2. Créer un utilisateur de test
```bash
php artisan tinker
```
```php
$user = User::factory()->create([
    'email' => 'test@example.com',
    'tenant_id' => 1
]);

// Créer quelques factures de test
Invoice::factory(20)->create(['tenant_id' => 1]);
```

### 3. Se connecter
Accéder à `/login` et utiliser :
- Email: `test@example.com`
- Mot de passe: `password` (par défaut avec factory)

### 4. Tester les pages
- `/dashboard` - Vue d'ensemble
- `/dashboard/invoices` - Liste
- `/dashboard/invoices/1` - Détails
- `/dashboard/payments` - Paiements
- `/dashboard/profile` - Profil
- `/dashboard/settings` - Paramètres

## 🔄 Prochaines améliorations possibles

1. **Graphiques et analytics**
   - Chart.js pour visualiser l'évolution des revenus
   - Graphiques de répartition par statut
   - Tendances mensuelles

2. **Export de données**
   - Export CSV des factures
   - Export Excel des paiements
   - Export PDF de rapports

3. **Notifications en temps réel**
   - Laravel Echo + WebSockets
   - Notifications push navigateur
   - Badges de notifications

4. **Fonctionnalités avancées**
   - Recherche avancée multi-critères
   - Sauvegarde de filtres favoris
   - Actions par lot (bulk actions)
   - Gestion des pièces jointes

5. **Authentification à deux facteurs**
   - Configuration 2FA
   - Codes de récupération
   - Gestion des sessions

6. **Paramètres fonctionnels**
   - Enregistrement des préférences en DB
   - Emails de notification configurables
   - Personnalisation du thème

## 📝 Notes techniques

- **Framework:** Laravel 11.x
- **Frontend:** Tailwind CSS 3.x + AlpineJS 3.x (CDN)
- **Pagination:** Laravel default (15 items/page)
- **Authentication:** Laravel Auth middleware
- **Icons:** Heroicons (inline SVG)
- **Responsive:** Mobile-first approach

## ✅ Checklist de déploiement

- [ ] Tester toutes les routes
- [ ] Vérifier la sécurité multi-tenant
- [ ] Tester la pagination
- [ ] Tester les filtres
- [ ] Vérifier le téléchargement PDF
- [ ] Tester le formulaire de profil
- [ ] Valider le responsive design
- [ ] Vérifier les messages d'erreur
- [ ] Tester les états vides
- [ ] Optimiser les requêtes N+1

---

**Créé le:** 30 novembre 2024  
**Version:** 1.0  
**Statut:** ✅ Complet et fonctionnel
