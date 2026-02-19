# Guide de Test - Invoice SaaS

## 🚀 Démarrage

Le serveur tourne sur : **http://127.0.0.1:8003**

### Identifiants de test
- **Email** : `admin@testcompany.com`
- **Mot de passe** : `password`

### Données de test disponibles
- ✅ 1 Tenant : Test Company
- ✅ 1 Utilisateur : Admin Test
- ✅ 5 Clients : ABC Corporation, XYZ Solutions, Tech Innovators, Digital Services, Consulting Group
- ✅ 10 Produits : Développement Web (50€), Consulting IT (80€), Formation (70€), etc.

---

## �️ Architecture

### 👨‍💼 `/admin` - Interface Administrateur (Filament)
**Public cible** : Administrateurs de la plateforme
**Technologies** : Filament 3, Livewire, Tailwind CSS
**Fonctionnalités** : 
- Gestion complète des données (CRUD)
- Configuration système
- Rapports et statistiques
- Gestion des utilisateurs et permissions

### 👥 `/client` - Interface Client (Personnalisée)
**Public cible** : Clients/utilisateurs finaux
**Technologies** : Blade, JavaScript Vanilla, Tailwind CSS
**Fonctionnalités** :
- Création optimisée de factures
- Consultation des factures
- Gestion des paiements
- Profil utilisateur

---

## 🎯 Interface 1 : Filament Admin (Back-Office)

### Accès
URL : **http://127.0.0.1:8003/admin**

### Fonctionnalités disponibles
1. **Dashboard** : Vue d'ensemble
2. **Clients** : CRUD complet avec gestion des clients
3. **Products** : CRUD complet avec gestion des produits
4. **Invoices** : CRUD complet avec gestion des factures

### Test de création de facture (Filament)

#### Étape 1 : Se connecter
1. Aller sur http://127.0.0.1:8003/admin/login
2. Email : `admin@testcompany.com`
3. Mot de passe : `password`
4. Cliquer sur "Sign in"

#### Étape 2 : Créer une facture
1. Dans le menu latéral, cliquer sur **"Invoices"**
2. Cliquer sur **"New Invoice"** (bouton en haut à droite)
3. Remplir le formulaire :
   - **Client** : Sélectionner un client (ex: ABC Corporation)
   - **Issue Date** : Date d'émission
   - **Due Date** : Date d'échéance
   - **Items** : Ajouter des lignes de facture
   - **Notes** : Notes internes (optionnel)
   - **Terms** : Conditions de paiement (optionnel)
4. Cliquer sur **"Create"**

#### Étape 3 : Vérifier
- La facture est créée avec un numéro automatique (ex: INV-2025-0001)
- Le statut est "draft"
- Vous pouvez voir les détails, modifier ou supprimer

### Avantages Filament
- ✅ Interface professionnelle et moderne
- ✅ CRUD complet automatique
- ✅ Gestion des relations (client, produits)
- ✅ Filtres et recherche avancés
- ✅ Export/import de données
- ✅ Permissions et rôles
- ✅ Responsive mobile

---

## 🎨 Interface 2 : Dashboard Client (Front-Office)

### Accès
URL : **http://127.0.0.1:8003/client/invoices/create**

### Note importante
⚠️ Il faut d'abord se connecter via Filament (http://127.0.0.1:8003/admin/login) car les deux interfaces partagent la même authentification pour le moment. Dans un environnement de production, les clients auraient leur propre système de connexion sur `/client`.

### Test de création de facture (Dashboard Personnalisé)

#### Étape 1 : Se connecter (via Filament)
1. Aller sur http://127.0.0.1:8003/admin/login
2. Email : `admin@testcompany.com`
3. Mot de passe : `password`

#### Étape 2 : Accéder au Dashboard personnalisé
1. Aller sur http://127.0.0.1:8003/client/invoices
2. Cliquer sur **"Nouvelle facture"** (bouton bleu en haut à droite)

#### Étape 3 : Créer une facture
1. **Section 1 - Informations du client**
   - Sélectionner un client dans la liste déroulante
   - Choisir la date d'émission
   - Choisir la date d'échéance (doit être >= date d'émission)

2. **Section 2 - Lignes de facture**
   - Cliquer sur **"Ajouter une ligne"**
   - Sélectionner un produit (les champs se remplissent automatiquement)
   - Ajuster la quantité
   - Modifier le prix si nécessaire
   - Définir le taux de TVA
   - **Observer** : Le total de la ligne se calcule automatiquement
   - Répéter pour ajouter plusieurs lignes

3. **Section 3 - Totaux**
   - **Observer** : Les totaux se calculent en temps réel
   - Ajouter une remise (optionnel) :
     - En pourcentage (ex: 10%)
     - Ou en montant fixe (ex: 50€)
   - **Observer** : Le total TTC se met à jour

4. **Section 4 - Notes et conditions**
   - Ajouter des notes internes (optionnel)
   - Modifier les conditions de paiement (pré-rempli)

5. **Soumettre**
   - Cliquer sur **"Créer la facture"**
   - Vous serez redirigé vers la page de détails de la facture

#### Étape 4 : Vérifier la facture créée
- ✅ Numéro de facture généré automatiquement
- ✅ Statut : "Brouillon" (draft)
- ✅ Informations client correctes
- ✅ Lignes de facture avec calculs
- ✅ Totaux corrects (HT, TVA, TTC, Remise)
- ✅ Bouton de téléchargement PDF (si implémenté)

### Fonctionnalités de l'interface personnalisée
- ✅ Calculs automatiques en temps réel
- ✅ Sélection de produits avec auto-remplissage
- ✅ Ajout/Suppression dynamique de lignes
- ✅ Remise en % ou montant fixe
- ✅ Validation des données côté serveur
- ✅ Messages d'erreur en français
- ✅ Interface moderne avec Tailwind CSS
- ✅ Animations et transitions fluides

### Avantages Dashboard Client
- ✅ Interface simplifiée pour les clients
- ✅ Calculs en temps réel (JavaScript)
- ✅ Expérience utilisateur optimisée
- ✅ Workflow guidé et intuitif
- ✅ Branding personnalisable
- ✅ Peut intégrer des API tierces facilement
- ✅ Pas de surcharge d'informations admin

---

## 🧪 Tests à effectuer

### Tests de validation
1. **Essayer de soumettre sans client** → Erreur
2. **Date d'échéance < date d'émission** → Erreur
3. **Soumettre sans lignes de facture** → Erreur
4. **Quantité = 0** → Erreur
5. **Prix négatif** → Erreur
6. **Taux TVA > 100%** → Erreur

### Tests de calculs
1. **1 produit à 50€, qté 2, TVA 20%** → Total ligne : 120€
2. **2 lignes : 50€ (qté 2) + 80€ (qté 1)** → Sous-total : 130€
3. **Avec TVA 20%** → TVA : 26€, Total : 156€
4. **Avec remise 10%** → Remise : 15.60€, Total : 140.40€
5. **Avec remise fixe 20€** → Total : 136€

### Tests fonctionnels
1. ✅ Ajouter plusieurs lignes
2. ✅ Supprimer une ligne
3. ✅ Modifier la quantité
4. ✅ Sélectionner différents produits
5. ✅ Changer le type de remise (% ↔ fixe)
6. ✅ Visualiser la facture créée
7. ✅ Retourner à la liste des factures
8. ⏳ Télécharger le PDF (si implémenté)
9. ⏳ Envoyer par email (si implémenté)

---

## 📊 Comparaison des interfaces

| Critère | Admin (Filament) | Client (Dashboard) |
|---------|------------------|-------------------|
| **Public cible** | Administrateurs | Clients finaux |
| **Rapidité de développement** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ |
| **Personnalisation** | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Expérience utilisateur** | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Maintenance** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ |
| **Features built-in** | ⭐⭐⭐⭐⭐ | ⭐⭐ |
| **Complexité interface** | Complète | Simplifiée |
| **Performance** | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |

---

## 🎯 Architecture recommandée (Actuelle) 🏆

### `/admin` - Back-Office (Filament)
**Pour les administrateurs uniquement**
- ✅ Gestion complète des données (clients, produits, factures)
- ✅ CRUD automatique avec filtres, recherche, export
- ✅ Configuration système et paramètres
- ✅ Gestion des utilisateurs et permissions
- ✅ Rapports et statistiques
- ✅ Logs et monitoring
- ✅ Maintenance et support

### `/client` - Front-Office (Personnalisé)
**Pour les clients finaux**
- ✅ Création de factures (workflow optimisé)
- ✅ Consultation de leurs factures uniquement
- ✅ Téléchargement de PDF
- ✅ Suivi des paiements
- ✅ Gestion de leur profil
- ✅ Interface simple et guidée
- ✅ Branding personnalisé

### Séparation des responsabilités
- **Admins** → `/admin` → Gestion globale de la plateforme
- **Clients** → `/client` → Actions limitées à leurs données
- **Authentification** → Partagée mais avec rôles différents
- **Base de données** → Commune avec tenant_id pour isolation

---

## 🐛 Problèmes connus et solutions

### Problème : Route [login] not defined
**Solution** : Ajouté une route `/login` qui redirige vers `/admin/login`

### Problème : Colonnes manquantes dans la base de données
**Solution** : Ajout de migrations pour `deleted_at`, `city`, `country`, `unit_price`

### Problème : user_id required pour products/clients
**Solution** : Ajouté `user_id` dans le seeder

---

## 📝 Prochaines étapes

### Priorité haute
- [ ] Implémenter la génération PDF (déjà prévu dans GenerateInvoicePdfJob)
- [ ] Implémenter l'envoi d'email (déjà prévu dans SendInvoiceEmailJob)
- [ ] Ajouter l'édition de factures (mode brouillon uniquement)

### Priorité moyenne
- [ ] Ajouter la recherche dans la liste des factures
- [ ] Ajouter des filtres (statut, date, client)
- [ ] Implémenter les paiements
- [ ] Historique des modifications

### Priorité basse
- [ ] Export Excel/CSV
- [ ] Statistiques et graphiques
- [ ] Multi-devises
- [ ] Templates de facture personnalisables

---

## 💡 Conseils

1. **Testez d'abord Filament** pour comprendre la structure de base
2. **Ensuite testez le Dashboard** pour voir l'interface personnalisée
3. **Comparez les deux** approches pour vos besoins
4. **Utilisez les deux** en fonction du contexte (admin vs utilisateur final)

---

## 📞 Support

En cas de problème :
1. Vérifier les logs : `storage/logs/laravel.log`
2. Vérifier la console navigateur (F12)
3. Vérifier la connexion à la base de données
4. Relancer les migrations si nécessaire

Bon test ! 🚀
