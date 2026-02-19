# 🧪 Guide de Test Manuel - Invoice SaaS

**Objectif** : Vérifier manuellement les fonctionnalités critiques après les tests automatiques (91.1% réussis).

---

## 📋 Préparation

### Serveur
✅ Serveur démarré : http://127.0.0.1:8003

### Comptes de test
- **Admin** : `admin@testcompany.com` / `password`
- **Client** : `client@testcompany.com` / `password`

### Navigateurs recommandés
- Chrome/Edge (recommandé)
- Firefox
- Safari

---

## 🧪 Test 1 : Landing Page Marketing

**URL** : http://127.0.0.1:8003/

### ✅ Points à vérifier

| # | Élément | Attendu | ✓ |
|---|---------|---------|---|
| 1 | Navigation avec backdrop-blur | Barre transparente avec flou | ☐ |
| 2 | Logo "InvoicePro" avec animation | Logo flotte légèrement | ☐ |
| 3 | Boutons "Admin" et "Client" | Liens fonctionnels | ☐ |
| 4 | Hero avec animations | Texte apparaît progressivement | ☐ |
| 5 | Badge "5 000+ entreprises" | Visible et stylisé | ☐ |
| 6 | Gradient sur "en toute simplicité" | Couleurs indigo→purple | ☐ |
| 7 | 2 boutons CTA | "Commencer" + "En savoir plus" | ☐ |
| 8 | 3 Trust badges | Avec icônes check vertes | ☐ |
| 9 | Séparateur vague | SVG entre hero et features | ☐ |
| 10 | 6 Feature cards | Avec gradient icons | ☐ |
| 11 | Hover sur cards | Élévation au survol | ☐ |
| 12 | Section Pricing | 3 plans visibles | ☐ |
| 13 | Plan "Pro" mis en avant | Badge + gradient | ☐ |
| 14 | Section FAQ | 5 questions avec emojis | ☐ |
| 15 | Footer complet | 5 colonnes avec liens | ☐ |
| 16 | Badges RGPD/SSL | En bas du footer | ☐ |
| 17 | "🇫🇷 Fait avec ❤️ en France" | Message visible | ☐ |

### 📱 Test Responsive

| Device | Breakpoint | Attendu | ✓ |
|--------|-----------|---------|---|
| Mobile | < 640px | Menu hamburger visible | ☐ |
| Tablet | 640-1024px | Layout adapté | ☐ |
| Desktop | > 1024px | Layout complet | ☐ |

### Actions
1. Ouvrir http://127.0.0.1:8003/ dans un navigateur
2. Vérifier chaque point de la liste
3. Cocher ✓ si OK
4. Tester sur mobile (inspecteur → mode responsive)

---

## 🔐 Test 2 : Authentification Admin

**URL** : http://127.0.0.1:8003/admin

### Étapes

1. **Accéder à l'admin**
   ```
   URL : http://127.0.0.1:8003/admin
   ```
   - ☐ Redirection vers page de login
   - ☐ Formulaire d'authentification visible

2. **Se connecter**
   ```
   Email : admin@testcompany.com
   Password : password
   ```
   - ☐ Connexion réussie
   - ☐ Redirection vers dashboard Filament

3. **Vérifier dashboard admin**
   - ☐ Sidebar Filament visible (gauche)
   - ☐ Menu "Clients" visible
   - ☐ Menu "Products" visible
   - ☐ Menu "Invoices" visible
   - ☐ Header avec user menu
   - ☐ Logo InvoicePro

### Explorer les ressources

#### Clients
1. Cliquer sur "Clients" dans la sidebar
   - ☐ Liste des clients s'affiche
   - ☐ 5 clients de test visibles
   - ☐ Colonnes : Name, Email, Company, etc.
   - ☐ Bouton "New Client" visible
   - ☐ Filtres disponibles
   - ☐ Recherche fonctionnelle

2. Créer un nouveau client
   - ☐ Cliquer "New Client"
   - ☐ Formulaire s'affiche
   - ☐ Champs : Name, Email, Company, Address, etc.
   - ☐ Remplir et sauvegarder
   - ☐ Client créé avec succès
   - ☐ Message de confirmation

#### Products
1. Cliquer sur "Products"
   - ☐ Liste des produits s'affiche
   - ☐ 10 produits de test visibles
   - ☐ Colonnes : Name, Price, Description
   - ☐ Actions disponibles (Edit, Delete)

#### Invoices
1. Cliquer sur "Invoices"
   - ☐ Liste des factures s'affiche
   - ☐ Peut être vide (normal)
   - ☐ Bouton "New Invoice" visible
   - ☐ Filtres par status

2. **Créer une facture de test**
   - ☐ Cliquer "New Invoice"
   - ☐ Sélectionner un client
   - ☐ Ajouter des items
   - ☐ Calculs automatiques (subtotal, tax, total)
   - ☐ Sauvegarder
   - ☐ Facture créée avec numéro unique

---

## 👤 Test 3 : Interface Client

**URL** : http://127.0.0.1:8003/client

### Préparation
1. Se déconnecter de l'admin
   - ☐ Cliquer sur user menu
   - ☐ Logout

2. Se connecter en tant que client
   ```
   Email : client@testcompany.com
   Password : password
   ```
   - ☐ Connexion réussie

### Dashboard Client

1. **Vérifier layout**
   - ☐ URL : http://127.0.0.1:8003/client
   - ☐ Sidebar visible (gauche)
   - ☐ Logo "InvoicePro"
   - ☐ Menu "Tableau de bord"
   - ☐ Menu "Mes factures"
   - ☐ Menu "Paiements"
   - ☐ Menu "Paramètres"
   - ☐ Profil utilisateur en bas
   - ☐ Bouton logout

2. **Contenu dashboard**
   - ☐ Titre "Tableau de bord"
   - ☐ Statistiques visibles (peut être vide)
   - ☐ Graphiques/widgets (si disponibles)

### Liste des factures

1. Cliquer sur "Mes factures"
   - ☐ URL : http://127.0.0.1:8003/client/invoices
   - ☐ Liste s'affiche
   - ☐ Peut être vide (normal)
   - ☐ Bouton "Créer une facture" visible

### Créer une facture

1. Cliquer sur "Créer une facture"
   - ☐ URL : http://127.0.0.1:8003/client/invoices/create
   - ☐ Formulaire s'affiche
   - ☐ Champ "Client" (dropdown)
   - ☐ Champ "Date d'échéance"
   - ☐ Section "Articles"
   - ☐ Bouton "Ajouter un article"

2. **Remplir le formulaire**
   ```
   Client : Sélectionner "Test Company"
   Date : J+30
   
   Article 1:
   - Produit : "Consultation"
   - Quantité : 2
   - Prix unitaire : 100.00
   
   Article 2:
   - Produit : "Développement"
   - Quantité : 5
   - Prix unitaire : 150.00
   ```
   
   - ☐ Tous les champs remplis
   - ☐ Calculs automatiques visibles
   - ☐ Sous-total = 950.00
   - ☐ Taxes calculées (20%)
   - ☐ Total = 1140.00

3. **Sauvegarder**
   - ☐ Cliquer "Enregistrer"
   - ☐ Redirection vers liste
   - ☐ Facture visible dans la liste
   - ☐ Message de succès

### Paiements

1. Cliquer sur "Paiements"
   - ☐ URL : http://127.0.0.1:8003/client/payments
   - ☐ Liste des paiements
   - ☐ Peut être vide (normal)

---

## 🔒 Test 4 : Sécurité des Rôles

### Test A : Admin peut tout

**Compte** : `admin@testcompany.com`

1. Accès Admin
   - ☐ http://127.0.0.1:8003/admin → ✅ Autorisé

2. Accès Client
   - ☐ http://127.0.0.1:8003/client → ✅ Autorisé (test)

### Test B : Client restreint

**Compte** : `client@testcompany.com`

1. Accès Client
   - ☐ http://127.0.0.1:8003/client → ✅ Autorisé

2. **Accès Admin (DOIT ÊTRE REFUSÉ)**
   - ☐ http://127.0.0.1:8003/admin → ❌ **Erreur 403 attendue**
   - ☐ Message : "Accès non autorisé" ou "Forbidden"
   - ☐ Pas d'accès aux ressources admin

### Test C : Non-authentifié

1. Se déconnecter
   - ☐ Logout

2. Tenter accès protégés
   - ☐ http://127.0.0.1:8003/admin → Redirige vers /login
   - ☐ http://127.0.0.1:8003/client → Redirige vers /login
   - ☐ http://127.0.0.1:8003/ → ✅ Landing page visible

---

## 📊 Résumé des Tests

### Critères de validation

| Catégorie | Tests | Réussis | % | Statut |
|-----------|-------|---------|---|--------|
| Landing Page | 20 | ___ | __% | ☐ |
| Admin | 15 | ___ | __% | ☐ |
| Client | 15 | ___ | __% | ☐ |
| Sécurité | 6 | ___ | __% | ☐ |
| **TOTAL** | **56** | ___ | __% | ☐ |

### Verdict

- [ ] ✅ **Excellent** : 100% réussis → Prêt production
- [ ] ⚠️  **Bon** : 80-99% → Corrections mineures
- [ ] ❌ **Insuffisant** : < 80% → Corrections majeures

---

## 🐛 Signalement de Bugs

Si vous trouvez un problème :

```markdown
### Bug #X : [Titre court]

**Catégorie** : Landing / Admin / Client / Sécurité
**Sévérité** : Critique / Majeur / Mineur
**URL** : http://...
**Compte** : admin@... / client@...

**Description** :
[Description détaillée du problème]

**Étapes pour reproduire** :
1. ...
2. ...
3. ...

**Résultat attendu** :
[Ce qui devrait se passer]

**Résultat observé** :
[Ce qui se passe réellement]

**Capture d'écran** :
[Si possible]
```

---

## ✅ Checklist finale

Avant de marquer les tests comme terminés :

- [ ] Tous les tests landing page complétés
- [ ] Tous les tests admin complétés
- [ ] Tous les tests client complétés
- [ ] Tous les tests sécurité complétés
- [ ] Au moins 1 facture créée via Filament
- [ ] Au moins 1 facture créée via interface client
- [ ] Sécurité rôles vérifiée (403 pour client → admin)
- [ ] Responsive testé (mobile + desktop)
- [ ] Bugs signalés (si trouvés)
- [ ] Rapport de test rempli

---

## 📝 Notes

**Date du test** : _______________  
**Testeur** : _______________  
**Navigateur** : _______________  
**Résolution** : _______________  

**Observations générales** :
```
[Notes libres]
```

**Recommandations** :
```
[Améliorations suggérées]
```

---

**Guide créé le** : 30 novembre 2025  
**Version** : 1.0  
**Basé sur** : Test automatique (91.1% réussis)
