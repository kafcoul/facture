#!/bin/bash

# 🚀 Script de Configuration Automatique - Invoice SaaS
# Ce script configure automatiquement votre environnement

set -e  # Arrêter en cas d'erreur

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}"
echo "╔════════════════════════════════════════════════════════╗"
echo "║     Configuration Automatique - Invoice SaaS          ║"
echo "╚════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# Fonction pour afficher un message de succès
success() {
    echo -e "${GREEN}✅ $1${NC}"
}

# Fonction pour afficher un message d'erreur
error() {
    echo -e "${RED}❌ $1${NC}"
}

# Fonction pour afficher un message d'avertissement
warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

# Fonction pour afficher une info
info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Vérifier si nous sommes dans le bon répertoire
if [ ! -f "composer.json" ]; then
    error "composer.json introuvable. Êtes-vous dans le bon répertoire ?"
    exit 1
fi

info "Démarrage de la configuration..."

# 1. Vérifier Redis
echo ""
info "Étape 1/8 : Vérification de Redis..."
if redis-cli ping > /dev/null 2>&1; then
    success "Redis est actif"
else
    warning "Redis n'est pas actif. Tentative de démarrage..."
    if command -v brew &> /dev/null; then
        brew services start redis
        sleep 2
        if redis-cli ping > /dev/null 2>&1; then
            success "Redis démarré avec succès"
        else
            error "Impossible de démarrer Redis. Installez-le avec: brew install redis"
            exit 1
        fi
    else
        error "Redis n'est pas installé. Installez-le d'abord."
        exit 1
    fi
fi

# 2. Vérifier MySQL
echo ""
info "Étape 2/8 : Vérification de MySQL..."
if command -v mysql &> /dev/null; then
    success "MySQL est installé"
    warning "Assurez-vous que MySQL est démarré et que la base de données 'invoice_saas' existe"
    info "Si elle n'existe pas, créez-la avec:"
    echo "    mysql -u root -p -e \"CREATE DATABASE invoice_saas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\""
else
    warning "MySQL n'est pas détecté. Assurez-vous qu'il est installé."
fi

# 3. Installation complète de Laravel (structure manquante)
echo ""
info "Étape 3/8 : Vérification de la structure Laravel..."
if [ ! -d "public" ] || [ ! -f "public/index.php" ]; then
    warning "Structure Laravel incomplète détectée"
    info "Installation d'une structure Laravel complète..."
    
    # Créer un projet Laravel temporaire
    cd ..
    TEMP_DIR="temp_laravel_$(date +%s)"
    composer create-project laravel/laravel "$TEMP_DIR" "10.*" --quiet
    
    # Copier les fichiers manquants
    cp -rn "$TEMP_DIR/public" invoice-saas-starter/ 2>/dev/null || true
    cp -rn "$TEMP_DIR/bootstrap" invoice-saas-starter/ 2>/dev/null || true
    cp -rn "$TEMP_DIR/app/Providers" invoice-saas-starter/app/ 2>/dev/null || true
    cp -rn "$TEMP_DIR/app/Http/Middleware" invoice-saas-starter/app/Http/ 2>/dev/null || true
    
    # Nettoyer
    rm -rf "$TEMP_DIR"
    cd invoice-saas-starter
    
    success "Structure Laravel installée"
else
    success "Structure Laravel complète"
fi

# 4. Générer la clé d'application
echo ""
info "Étape 4/8 : Génération de la clé d'application..."
if grep -q "APP_KEY=$" .env 2>/dev/null || [ ! -f .env ]; then
    php artisan key:generate --force
    success "Clé d'application générée"
else
    success "Clé d'application déjà présente"
fi

# 5. Créer les dossiers de storage
echo ""
info "Étape 5/8 : Configuration des permissions..."
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache
success "Dossiers et permissions configurés"

# 6. Lancer les migrations
echo ""
info "Étape 6/8 : Exécution des migrations..."
read -p "Voulez-vous exécuter les migrations maintenant ? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    success "Migrations exécutées"
else
    warning "Migrations ignorées. Exécutez-les manuellement avec: php artisan migrate"
fi

# 7. Installation de Filament
echo ""
info "Étape 7/8 : Installation de Filament..."
if php artisan list | grep -q "filament:install"; then
    php artisan filament:install --panels --force
    php artisan filament:assets
    success "Filament installé"
    
    # Créer un utilisateur admin
    read -p "Voulez-vous créer un utilisateur admin Filament ? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        php artisan make:filament-user
        success "Utilisateur admin créé"
    fi
else
    warning "Commandes Filament non disponibles. Installez manuellement avec: php artisan filament:install"
fi

# 8. Créer le lien symbolique storage
echo ""
info "Étape 8/8 : Création du lien symbolique storage..."
php artisan storage:link
success "Lien symbolique créé"

# Résumé final
echo ""
echo -e "${GREEN}"
echo "╔════════════════════════════════════════════════════════╗"
echo "║              ✅ Configuration Terminée !               ║"
echo "╚════════════════════════════════════════════════════════╝"
echo -e "${NC}"

echo ""
info "Prochaines étapes :"
echo ""
echo "1. Configurez vos clés Stripe dans .env :"
echo "   STRIPE_KEY=pk_test_..."
echo "   STRIPE_SECRET=sk_test_..."
echo "   STRIPE_WEBHOOK_SECRET=whsec_..."
echo ""
echo "2. Lancez le serveur de développement :"
echo "   php artisan serve"
echo ""
echo "3. Dans un autre terminal, lancez le worker de queue :"
echo "   php artisan queue:work redis --tries=3"
echo ""
echo "4. Pour tester les webhooks Stripe en local :"
echo "   stripe listen --forward-to localhost:8000/stripe/webhook"
echo ""
echo "5. Accédez à l'admin panel :"
echo "   http://localhost:8000/admin"
echo ""

success "Installation terminée avec succès ! 🎉"
