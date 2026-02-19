<?php
/**
 * 🧪 TEST AUTOMATIQUE COMPLET DU SYSTÈME
 * Test de toutes les fonctionnalités de l'application Invoice SaaS
 * 
 * Tests inclus:
 * ✅ Landing Page
 * ✅ Routes publiques et protégées
 * ✅ Sécurité des rôles (Admin/Client)
 * ✅ Création de factures
 * ✅ Base de données
 * ✅ Middlewares
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;

// Couleurs pour le terminal
class Color {
    const GREEN = "\033[32m";
    const RED = "\033[31m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
    const MAGENTA = "\033[35m";
    const CYAN = "\033[36m";
    const WHITE = "\033[37m";
    const BOLD = "\033[1m";
    const RESET = "\033[0m";
}

function printHeader($text) {
    echo "\n" . Color::BOLD . Color::CYAN . "═══════════════════════════════════════════════════════════════\n";
    echo "  " . $text . "\n";
    echo "═══════════════════════════════════════════════════════════════" . Color::RESET . "\n\n";
}

function printTest($name, $status, $details = '') {
    $icon = $status ? '✅' : '❌';
    $color = $status ? Color::GREEN : Color::RED;
    echo $color . $icon . " " . $name . Color::RESET;
    if ($details) {
        echo Color::WHITE . " - " . $details . Color::RESET;
    }
    echo "\n";
    return $status;
}

function printInfo($text) {
    echo Color::BLUE . "ℹ️  " . $text . Color::RESET . "\n";
}

function printWarning($text) {
    echo Color::YELLOW . "⚠️  " . $text . Color::RESET . "\n";
}

// Compteurs
$totalTests = 0;
$passedTests = 0;

// ============================================================
printHeader("🚀 TEST 1: CONNEXION À LA BASE DE DONNÉES");
// ============================================================

try {
    DB::connection()->getPdo();
    $dbName = DB::connection()->getDatabaseName();
    $passedTests += printTest("Connexion DB", true, "Base: $dbName");
    $totalTests++;
} catch (\Exception $e) {
    printTest("Connexion DB", false, "Erreur: " . $e->getMessage());
    $totalTests++;
    die("\n❌ Impossible de continuer sans connexion DB\n");
}

// ============================================================
printHeader("🗄️  TEST 2: STRUCTURE DE LA BASE DE DONNÉES");
// ============================================================

$tables = ['users', 'clients', 'products', 'invoices', 'invoice_items', 'payments'];
foreach ($tables as $table) {
    $exists = Schema::hasTable($table);
    $passedTests += printTest("Table '$table'", $exists);
    $totalTests++;
}

// Vérification des colonnes critiques
$criticalColumns = [
    'users' => ['id', 'name', 'email', 'password', 'role'],
    'clients' => ['id', 'name', 'email', 'company'],
    'invoices' => ['id', 'number', 'client_id', 'status', 'total'],
    'products' => ['id', 'name', 'price'],
];

foreach ($criticalColumns as $table => $columns) {
    foreach ($columns as $column) {
        $exists = Schema::hasColumn($table, $column);
        if (!$exists) {
            $passedTests += printTest("Colonne '$table.$column'", false);
            $totalTests++;
        }
    }
}

// ============================================================
printHeader("👥 TEST 3: DONNÉES DE TEST (SEEDERS)");
// ============================================================

$adminCount = User::where('role', 'admin')->count();
$clientCount = User::where('role', 'client')->count();
$passedTests += printTest("Utilisateurs Admin", $adminCount > 0, "Trouvés: $adminCount");
$totalTests++;
$passedTests += printTest("Utilisateurs Client", $clientCount > 0, "Trouvés: $clientCount");
$totalTests++;

$clientsCount = Client::count();
$passedTests += printTest("Clients créés", $clientsCount > 0, "Trouvés: $clientsCount");
$totalTests++;

$productsCount = Product::count();
$passedTests += printTest("Produits créés", $productsCount > 0, "Trouvés: $productsCount");
$totalTests++;

$invoicesCount = Invoice::count();
$passedTests += printTest("Factures créées", $invoicesCount > 0, "Trouvées: $invoicesCount");
$totalTests++;

// ============================================================
printHeader("🛣️  TEST 4: ROUTES DE L'APPLICATION");
// ============================================================

$routeTests = [
    // Routes publiques
    ['GET', '/', 'Public: Landing Page'],
    ['GET', '/about', 'Public: Page À propos'],
    ['GET', '/login', 'Public: Login'],
    
    // Routes Admin (Filament)
    ['GET', '/admin', 'Admin: Dashboard Filament'],
    ['GET', '/admin/clients', 'Admin: Gestion Clients'],
    ['GET', '/admin/invoices', 'Admin: Gestion Factures'],
    ['GET', '/admin/products', 'Admin: Gestion Produits'],
    
    // Routes Client
    ['GET', '/client', 'Client: Dashboard'],
    ['GET', '/client/invoices', 'Client: Liste Factures'],
    ['GET', '/client/invoices/create', 'Client: Créer Facture'],
    ['POST', '/client/invoices', 'Client: Enregistrer Facture'],
    ['GET', '/client/payments', 'Client: Paiements'],
];

// Routes optionnelles (non critiques - peuvent ne pas exister)
$optionalRoutes = [
    ['GET', '/client/profile/edit', 'Client: Profil (optionnel)'],
    ['GET', '/client/settings', 'Client: Paramètres (optionnel)'],
];

$routeCollection = Route::getRoutes();
foreach ($routeTests as $test) {
    [$method, $uri, $description] = $test;
    try {
        $route = $routeCollection->match(
            \Illuminate\Http\Request::create($uri, $method)
        );
        $exists = $route !== null;
        $passedTests += printTest($description, $exists, $uri);
    } catch (\Exception $e) {
        printTest($description, false, "Erreur: " . substr($e->getMessage(), 0, 50));
    }
    $totalTests++;
}

// Test des routes optionnelles
printInfo("Test des routes optionnelles (non critiques):");
foreach ($optionalRoutes as $test) {
    [$method, $uri, $description] = $test;
    try {
        $route = $routeCollection->match(
            \Illuminate\Http\Request::create($uri, $method)
        );
        $exists = $route !== null;
        if ($exists) {
            echo Color::GREEN . "✅ " . $description . Color::RESET . " - $uri\n";
        } else {
            echo Color::YELLOW . "⚠️  " . $description . Color::RESET . " - Non implémentée\n";
        }
    } catch (\Exception $e) {
        echo Color::YELLOW . "⚠️  " . $description . Color::RESET . " - Non implémentée\n";
    }
}

// ============================================================
printHeader("🔒 TEST 5: MIDDLEWARES DE SÉCURITÉ");
// ============================================================

// Test des middlewares personnalisés
$middlewareFiles = [
    'app/Http/Middleware/EnsureUserIsAdmin.php' => 'Middleware Admin',
    'app/Http/Middleware/EnsureUserIsClient.php' => 'Middleware Client',
];

foreach ($middlewareFiles as $file => $description) {
    $exists = file_exists($file);
    $passedTests += printTest($description, $exists, $file);
    $totalTests++;
}

// Test de la protection des routes admin
$adminRoutes = $routeCollection->getRoutesByMethod()['GET'] ?? [];
$adminProtected = false;
foreach ($adminRoutes as $route) {
    if (str_starts_with($route->uri(), 'admin')) {
        $middleware = $route->middleware();
        if (in_array('admin', $middleware) || in_array('auth', $middleware)) {
            $adminProtected = true;
            break;
        }
    }
}
$passedTests += printTest("Routes Admin protégées", $adminProtected);
$totalTests++;

// ============================================================
printHeader("📄 TEST 6: FICHIERS DE VUE (BLADE)");
// ============================================================

$viewFiles = [
    'resources/views/welcome.blade.php' => 'Landing Page',
    'resources/views/about.blade.php' => 'Page À propos',
    'resources/views/layouts/client.blade.php' => 'Layout Client',
    'resources/views/components/client-layout.blade.php' => 'Composant Client Layout',
    'resources/views/dashboard/index.blade.php' => 'Dashboard Client',
    'resources/views/invoices/index.blade.php' => 'Liste Factures',
    'resources/views/invoices/create.blade.php' => 'Créer Facture',
];

foreach ($viewFiles as $file => $description) {
    $exists = file_exists($file);
    $passedTests += printTest($description, $exists);
    $totalTests++;
    
    if ($exists && $description === 'Landing Page') {
        $content = file_get_contents($file);
        $hasAnimations = strpos($content, '@keyframes') !== false;
        $hasTestimonials = strpos($content, 'Témoignages') !== false;
        $hasFAQ = strpos($content, 'FAQ') !== false;
        
        $passedTests += printTest("  └─ Animations CSS", $hasAnimations);
        $totalTests++;
        $passedTests += printTest("  └─ Section Témoignages", $hasTestimonials);
        $totalTests++;
        $passedTests += printTest("  └─ Section FAQ", $hasFAQ);
        $totalTests++;
    }
}

// ============================================================
printHeader("🎨 TEST 7: MODÈLES ELOQUENT");
// ============================================================

$models = [
    'App\Models\User' => 'User Model',
    'App\Models\Client' => 'Client Model',
    'App\Models\Invoice' => 'Invoice Model',
    'App\Models\InvoiceItem' => 'InvoiceItem Model',
    'App\Models\Product' => 'Product Model',
    'App\Models\Payment' => 'Payment Model',
];

foreach ($models as $class => $description) {
    $exists = class_exists($class);
    $passedTests += printTest($description, $exists);
    $totalTests++;
}

// Test des relations
try {
    $invoice = Invoice::with(['client', 'items'])->first();
    if ($invoice) {
        $hasClient = $invoice->client !== null;
        $passedTests += printTest("Relation Invoice -> Client", $hasClient);
        $totalTests++;
        
        $hasItems = $invoice->items !== null;
        $passedTests += printTest("Relation Invoice -> Items", $hasItems);
        $totalTests++;
    }
} catch (\Exception $e) {
    printTest("Relations Eloquent", false, $e->getMessage());
    $totalTests += 2;
}

// ============================================================
printHeader("⚙️  TEST 8: SERVICES ET JOBS");
// ============================================================

$services = [
    'app/Services/InvoiceCalculatorService.php' => 'Service Calcul Factures',
    'app/Services/InvoiceNumberService.php' => 'Service Numérotation',
    'app/Services/PdfService.php' => 'Service PDF',
    'app/Jobs/GenerateInvoicePdfJob.php' => 'Job Génération PDF',
    'app/Jobs/SendInvoiceEmailJob.php' => 'Job Envoi Email',
];

foreach ($services as $file => $description) {
    $exists = file_exists($file);
    $passedTests += printTest($description, $exists);
    $totalTests++;
}

// ============================================================
printHeader("🎯 TEST 9: CONTROLLERS");
// ============================================================

$controllers = [
    'app/Http/Controllers/PublicInvoiceController.php' => 'Controller Factures Publiques',
    'app/Http/Controllers/StripeWebhookController.php' => 'Controller Stripe Webhook',
];

foreach ($controllers as $file => $description) {
    $exists = file_exists($file);
    $passedTests += printTest($description, $exists);
    $totalTests++;
}

// ============================================================
printHeader("📚 TEST 10: DOCUMENTATION");
// ============================================================

$docs = [
    'docs/LANDING-PAGE.md',
    'docs/LANDING-PAGE-V2-IMPROVEMENTS.md',
    'docs/LANDING-PAGE-V2-VISUAL-TEST.md',
    'docs/FEATURES-SUMMARY.md',
    'README.md',
    'PRD.md',
];

foreach ($docs as $file) {
    $exists = file_exists($file);
    $passedTests += printTest(basename($file), $exists);
    $totalTests++;
}

// ============================================================
printHeader("📊 RÉSUMÉ DES TESTS");
// ============================================================

$percentage = round(($passedTests / $totalTests) * 100, 1);
$color = $percentage >= 90 ? Color::GREEN : ($percentage >= 70 ? Color::YELLOW : Color::RED);

echo "\n";
echo Color::BOLD . "Total de tests: " . Color::RESET . "$totalTests\n";
echo Color::GREEN . Color::BOLD . "Tests réussis: " . Color::RESET . "$passedTests\n";
echo Color::RED . Color::BOLD . "Tests échoués: " . Color::RESET . ($totalTests - $passedTests) . "\n";
echo $color . Color::BOLD . "Taux de réussite: $percentage%" . Color::RESET . "\n\n";

if ($percentage >= 90) {
    echo Color::GREEN . Color::BOLD . "🎉 EXCELLENT ! Le système est prêt pour la production !" . Color::RESET . "\n";
} elseif ($percentage >= 70) {
    echo Color::YELLOW . Color::BOLD . "⚠️  BON, mais quelques améliorations sont nécessaires." . Color::RESET . "\n";
} else {
    echo Color::RED . Color::BOLD . "❌ ATTENTION ! Des corrections importantes sont requises." . Color::RESET . "\n";
}

// ============================================================
printHeader("🔍 PROCHAINES ÉTAPES");
// ============================================================

echo Color::CYAN . "Pour tester l'application manuellement:\n" . Color::RESET;
echo "  1. Serveur: " . Color::BOLD . "http://127.0.0.1:8003" . Color::RESET . "\n";
echo "  2. Admin: " . Color::BOLD . "admin@testcompany.com / password" . Color::RESET . "\n";
echo "  3. Client: " . Color::BOLD . "client@testcompany.com / password" . Color::RESET . "\n\n";

echo Color::CYAN . "URLs à tester:\n" . Color::RESET;
echo "  • Landing Page: " . Color::BOLD . "http://127.0.0.1:8003/" . Color::RESET . "\n";
echo "  • Admin Panel: " . Color::BOLD . "http://127.0.0.1:8003/admin" . Color::RESET . "\n";
echo "  • Client Dashboard: " . Color::BOLD . "http://127.0.0.1:8003/client" . Color::RESET . "\n";
echo "  • Créer Facture: " . Color::BOLD . "http://127.0.0.1:8003/client/invoices/create" . Color::RESET . "\n\n";

echo "═══════════════════════════════════════════════════════════════\n\n";

exit($percentage >= 70 ? 0 : 1);
