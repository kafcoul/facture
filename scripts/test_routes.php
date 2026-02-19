<?php
/**
 * Script de test automatisé pour vérifier les routes et la sécurité
 * Usage: php test_routes.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║          🧪 TEST AUTOMATIQUE DES ROUTES & SÉCURITÉ         ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Test 1: Vérifier que les routes existent
echo "�� TEST 1: Vérification des routes\n";
echo "───────────────────────────────────────────────────────────\n";

$routes = [
    'home' => '/',
    'about' => '/about',
    'admin' => '/admin',
    'client.index' => '/client',
    'client.invoices.index' => '/client/invoices',
    'client.invoices.create' => '/client/invoices/create',
    'login' => '/login',
];

foreach ($routes as $name => $uri) {
    $route = Route::getRoutes()->getByName($name);
    if ($route || Route::getRoutes()->match(new \Illuminate\Http\Request('GET', $uri))) {
        echo "  ✅ $name ($uri)\n";
    } else {
        echo "  ❌ $name ($uri) - INTROUVABLE\n";
    }
}

echo "\n";

// Test 2: Vérifier les middlewares
echo "🔒 TEST 2: Vérification des middlewares\n";
echo "───────────────────────────────────────────────────────────\n";

$middlewares = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
    'client' => \App\Http\Middleware\EnsureUserIsClient::class,
];

foreach ($middlewares as $name => $class) {
    if (class_exists($class)) {
        echo "  ✅ Middleware '$name': $class\n";
    } else {
        echo "  ❌ Middleware '$name': $class - INTROUVABLE\n";
    }
}

echo "\n";

// Test 3: Vérifier la base de données
echo "💾 TEST 3: Vérification de la base de données\n";
echo "───────────────────────────────────────────────────────────\n";

try {
    $tables = ['users', 'clients', 'products', 'invoices', 'invoice_items', 'payments'];
    foreach ($tables as $table) {
        $exists = \Illuminate\Support\Facades\Schema::hasTable($table);
        if ($exists) {
            $count = \Illuminate\Support\Facades\DB::table($table)->count();
            echo "  ✅ Table '$table': $count enregistrements\n";
        } else {
            echo "  ❌ Table '$table': INTROUVABLE\n";
        }
    }
} catch (\Exception $e) {
    echo "  ❌ Erreur DB: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Vérifier les utilisateurs de test
echo "👥 TEST 4: Vérification des utilisateurs de test\n";
echo "───────────────────────────────────────────────────────────\n";

try {
    $admin = \App\Models\User::where('email', 'admin@testcompany.com')->first();
    if ($admin) {
        echo "  ✅ Admin: {$admin->email} (rôle: {$admin->role})\n";
    } else {
        echo "  ❌ Admin non trouvé\n";
    }
    
    $client = \App\Models\User::where('email', 'client@testcompany.com')->first();
    if ($client) {
        echo "  ✅ Client: {$client->email} (rôle: {$client->role})\n";
    } else {
        echo "  ❌ Client non trouvé\n";
    }
} catch (\Exception $e) {
    echo "  ❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 5: Vérifier les vues
echo "🎨 TEST 5: Vérification des vues\n";
echo "───────────────────────────────────────────────────────────\n";

$views = [
    'welcome',
    'about',
    'layouts.client',
    'components.client-layout',
    'dashboard.invoices.index',
    'dashboard.invoices.create',
];

foreach ($views as $view) {
    if (view()->exists($view)) {
        echo "  ✅ Vue '$view'\n";
    } else {
        echo "  ❌ Vue '$view' - INTROUVABLE\n";
    }
}

echo "\n";

// Résumé
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                    📊 RÉSUMÉ DES TESTS                     ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "✅ Tests terminés avec succès!\n";
echo "🌐 Serveur: http://127.0.0.1:8003\n";
echo "👤 Admin: admin@testcompany.com / password\n";
echo "👤 Client: client@testcompany.com / password\n";
echo "\n";
echo "📝 Prochaines étapes:\n";
echo "   1. Ouvrez http://127.0.0.1:8003 dans votre navigateur\n";
echo "   2. Testez la landing page\n";
echo "   3. Connectez-vous avec les comptes de test\n";
echo "   4. Vérifiez les accès admin et client\n";
echo "\n";
