# 🎯 Guide de test - Landing Page Marketing

## URLs à tester

### 1. Page d'accueil (non-authentifié)
**URL** : http://127.0.0.1:8003/

**Ce que vous devriez voir** :
- ✅ Navigation avec logo "Invoice SaaS"
- ✅ Hero section avec gradient violet/indigo
- ✅ Titre : "Gérez vos factures en toute simplicité"
- ✅ 2 boutons : "Démarrer gratuitement" + "En savoir plus"
- ✅ Section "Tout ce dont vous avez besoin" avec 6 features
- ✅ Section "Tarifs simples et transparents" avec 3 plans
- ✅ Formulaire d'inscription email
- ✅ Footer avec liens

**Actions à tester** :
1. Scroller pour voir toutes les sections
2. Cliquer sur "Fonctionnalités" (doit scroller vers #fonctionnalites)
3. Cliquer sur "Tarifs" (doit scroller vers #tarifs)
4. Cliquer sur "Connexion" (doit aller vers /admin/login)
5. Cliquer sur "À propos" dans le footer (doit aller vers /about)
6. Tester sur mobile (responsive)

### 2. Page À propos
**URL** : http://127.0.0.1:8003/about

**Ce que vous devriez voir** :
- ✅ Navigation identique à la page d'accueil
- ✅ Hero section "À propos d'Invoice SaaS"
- ✅ Section "Notre histoire"
- ✅ Section "Nos valeurs" avec 3 cartes (Simplicité, Sécurité, Innovation)
- ✅ Section "L'équipe"
- ✅ CTA "Rejoignez-nous !"
- ✅ Footer

**Actions à tester** :
1. Retour à l'accueil (clic sur logo)
2. Clic sur "Essayer gratuitement" (doit aller vers /#demo)
3. Vérifier la navigation

### 3. Test avec utilisateur authentifié (Admin)
**Étapes** :
1. Se connecter : http://127.0.0.1:8003/admin/login
   - Email : admin@testcompany.com
   - Password : password
2. Aller sur : http://127.0.0.1:8003/
3. **Résultat attendu** : Redirection automatique vers `/admin`

### 4. Test avec utilisateur authentifié (Client)
**Étapes** :
1. Se déconnecter
2. Se connecter : http://127.0.0.1:8003/admin/login
   - Email : client@testcompany.com
   - Password : password
3. Aller sur : http://127.0.0.1:8003/
4. **Résultat attendu** : Redirection automatique vers `/client`

## ✅ Checklist de validation

### Design
- [ ] Gradient violet/indigo visible dans le hero
- [ ] Icônes SVG s'affichent correctement
- [ ] Cards avec ombre portée (hover effect)
- [ ] Boutons avec couleurs correctes (indigo pour primaire)
- [ ] Footer avec fond noir (#1f2937)
- [ ] Textes lisibles (contraste suffisant)

### Responsive
- [ ] Navigation mobile (hamburger si implémenté)
- [ ] Grid features : 1 col mobile, 3 cols desktop
- [ ] Grid pricing : 1 col mobile, 3 cols desktop
- [ ] Boutons empilés verticalement sur mobile
- [ ] Padding et marges adaptés mobile/desktop

### Fonctionnalités
- [ ] Anchor links fonctionnent (#fonctionnalites, #tarifs, #demo)
- [ ] Lien "Connexion" → /admin/login
- [ ] Lien "À propos" → /about
- [ ] Logo cliquable (retour accueil)
- [ ] Redirection intelligente selon authentification/rôle

### SEO & Performance
- [ ] Title tag présent : "Invoice SaaS - Gestion de facturation simplifiée"
- [ ] Meta viewport pour mobile
- [ ] Tailwind CSS charge (CDN)
- [ ] Pas d'erreurs dans la console navigateur
- [ ] Page charge en < 2 secondes

## 🐛 Problèmes potentiels

### Tailwind ne charge pas
**Symptôme** : Page blanche ou texte non stylisé  
**Solution** : Vérifier connexion internet (CDN Tailwind)

### Erreur 404 sur /about
**Symptôme** : Page not found  
**Solution** : 
```bash
php artisan route:clear
php artisan route:cache
```

### Redirection ne fonctionne pas
**Symptôme** : Landing page s'affiche même quand connecté  
**Solution** : Vérifier `auth()->check()` dans routes/web.php

### SVG icons manquants
**Symptôme** : Carrés vides à la place des icônes  
**Solution** : Vérifier le code SVG dans welcome.blade.php

## 📸 Screenshots recommandés

Pour documenter :
1. Hero section (desktop)
2. Section features (desktop)
3. Section pricing avec plan "Pro" mis en avant
4. Version mobile (toute la page)
5. Page /about

## 🚀 Prochains tests

Après validation de la landing page :
1. Test de l'inscription (formulaire fonctionnel à implémenter)
2. Test SEO avec Google Lighthouse
3. Test performance avec PageSpeed Insights
4. Test accessibilité (WCAG)
5. Test cross-browser (Chrome, Firefox, Safari)

---

**Date de création** : 30 novembre 2025  
**Testé par** : [Votre nom]  
**Statut** : ⏳ En attente de test
