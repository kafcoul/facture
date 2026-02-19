# 🔒 Configuration Accès Super Admin

## ⚠️ IMPORTANT - À FAIRE IMMÉDIATEMENT

Avant de vous inscrire sur la plateforme, configurez votre email de propriétaire !

## 📝 Étape 1 : Configurer votre email

Ouvrez le fichier `.env` et modifiez cette ligne :

```env
SUPER_ADMIN_EMAIL=votre-vrai-email@domaine.com
```

**Exemple** :
```env
SUPER_ADMIN_EMAIL=jean.dupont@masuperentreprise.com
```

## 📝 Étape 2 : S'inscrire avec CET email

1. Allez sur http://127.0.0.1:8003
2. Cliquez sur "🚀 Essai Gratuit"
3. **UTILISEZ EXACTEMENT LE MÊME EMAIL** que dans `.env`
4. Remplissez le formulaire d'inscription
5. Créez votre compte

## ✅ Résultat

- ✅ Vous (et vous seul) aurez accès à `/admin`
- ✅ Tous les autres utilisateurs seront bloqués de `/admin`
- ✅ Tous les utilisateurs pourront utiliser `/client`

## 🔐 Niveaux d'Accès

| Utilisateur | Email | Accès /admin | Accès /client |
|-------------|-------|--------------|---------------|
| **Vous** (Propriétaire) | Configuré dans `.env` | ✅ OUI | ✅ OUI |
| **Autres utilisateurs** | Tous les autres emails | ❌ NON | ✅ OUI |

## 🧪 Tester la Restriction

1. Connectez-vous avec votre email (configuré dans `.env`)
2. Allez sur http://127.0.0.1:8003/admin
3. ✅ Vous devriez avoir accès

4. Créez un autre utilisateur avec un email différent
5. Connectez-vous avec cet utilisateur
6. Tentez d'accéder à http://127.0.0.1:8003/admin
7. ❌ Vous devriez voir : "Accès refusé. Seul le propriétaire..."

## 🔄 Autoriser Plusieurs Propriétaires (Optionnel)

Si vous voulez autoriser plusieurs personnes à accéder à `/admin` :

### Option 1 : Modifier le middleware directement

Ouvrez `app/Http/Middleware/EnsureUserIsAdmin.php` et modifiez :

```php
$authorizedEmails = [
    'proprietaire1@exemple.com',
    'proprietaire2@exemple.com',
    'comptable@exemple.com',
];
```

### Option 2 : Utiliser une variable .env avec liste d'emails

Dans `.env` :
```env
SUPER_ADMIN_EMAILS=email1@exemple.com,email2@exemple.com,email3@exemple.com
```

Puis modifiez le middleware :
```php
$authorizedEmails = explode(',', env('SUPER_ADMIN_EMAILS', 'admin@example.com'));
```

## ⚠️ Notes Importantes

1. **Changez l'email AVANT de vous inscrire** - Sinon vous devrez changer l'email dans la base de données
2. **Email sensible à la casse** - `Jean@exemple.com` ≠ `jean@exemple.com`
3. **Utilisez un email que vous contrôlez** - Pour la récupération de compte
4. **Ne partagez pas cet email** - C'est votre clé d'accès unique

## 🛡️ Sécurité

Cette configuration ajoute deux couches de sécurité :

1. ✅ **Vérification de l'email** : L'email doit correspondre à `SUPER_ADMIN_EMAIL`
2. ✅ **Vérification du rôle** : L'utilisateur doit avoir `role = 'admin'`

Les deux conditions doivent être vraies pour accéder à `/admin`.

## 📧 Configuration Actuelle

Votre configuration actuelle dans `.env` :

```env
SUPER_ADMIN_EMAIL=votre-email@exemple.com
```

⚠️ **CHANGEZ CECI MAINTENANT !**

---

**Date de configuration** : 30 Novembre 2025  
**Statut** : ✅ Restriction active  
**Protection** : Double vérification (email + rôle)
