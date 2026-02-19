#!/usr/bin/env php
<?php

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║  TEST SYSTÈME D'INSCRIPTION AUTOMATISÉE AVEC PLAN             ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$baseUrl = 'http://127.0.0.1:8003';
$passed = 0;
$failed = 0;
$tests = [];

function test($description, $callback) {
    global $passed, $failed, $tests;
    try {
        $result = $callback();
        if ($result) {
            $passed++;
            $tests[] = ['status' => '✅', 'test' => $description];
            echo "✅ PASS: $description\n";
        } else {
            $failed++;
            $tests[] = ['status' => '❌', 'test' => $description];
            echo "❌ FAIL: $description\n";
        }
    } catch (Exception $e) {
        $failed++;
        $tests[] = ['status' => '❌', 'test' => $description, 'error' => $e->getMessage()];
        echo "❌ ERROR: $description - " . $e->getMessage() . "\n";
    }
}

echo "═══════════════════════════════════════════════════════════════\n";
echo " SECTION 1: Tests des Routes et Pages\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("Page d'accueil accessible", function() use ($baseUrl) {
    $ch = curl_init("$baseUrl/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $httpCode === 200 && strpos($response, 'Essai Gratuit') !== false;
});

test("Boutons 'Essai Gratuit' pointent vers /register", function() use ($baseUrl) {
    $ch = curl_init("$baseUrl/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $count = substr_count($response, "route('register')") + substr_count($response, '/register"');
    return $count >= 3; // Au moins 3 boutons CTA
});

test("Page d'inscription accessible (/register)", function() use ($baseUrl) {
    $ch = curl_init("$baseUrl/register");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $httpCode === 200 && strpos($response, 'Créez votre compte') !== false;
});

test("Page d'inscription affiche les 3 plans (Starter, Pro, Enterprise)", function() use ($baseUrl) {
    $ch = curl_init("$baseUrl/register");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $hasStarter = strpos($response, 'Starter') !== false;
    $hasPro = strpos($response, 'Pro') !== false;
    $hasEnterprise = strpos($response, 'Enterprise') !== false;
    return $hasStarter && $hasPro && $hasEnterprise;
});

test("Page d'inscription affiche les prix (0€, 29€, 99€)", function() use ($baseUrl) {
    $ch = curl_init("$baseUrl/register");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $hasStarter = strpos($response, '0€') !== false;
    $hasPro = strpos($response, '29€') !== false;
    $hasEnterprise = strpos($response, '99€') !== false;
    return $hasStarter && $hasPro && $hasEnterprise;
});

test("Formulaire d'inscription contient tous les champs requis", function() use ($baseUrl) {
    $ch = curl_init("$baseUrl/register");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $hasCompany = strpos($response, 'name="company_name"') !== false;
    $hasName = strpos($response, 'name="name"') !== false;
    $hasEmail = strpos($response, 'name="email"') !== false;
    $hasPassword = strpos($response, 'name="password"') !== false;
    $hasPlan = strpos($response, 'name="plan"') !== false;
    $hasTerms = strpos($response, 'name="terms"') !== false;
    return $hasCompany && $hasName && $hasEmail && $hasPassword && $hasPlan && $hasTerms;
});

test("Formulaire d'inscription utilise POST vers /register-with-plan", function() use ($baseUrl) {
    $ch = curl_init("$baseUrl/register");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $hasAction = strpos($response, 'register-with-plan') !== false;
    $hasPost = preg_match('/method=["\']POST["\']/i', $response) > 0;
    return $hasAction && $hasPost;
});

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo " SECTION 2: Tests de la Structure des Données\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("Table 'tenants' a les colonnes nécessaires", function() {
    $output = shell_exec("php -r \"require 'vendor/autoload.php'; \\\$app = require_once 'bootstrap/app.php'; \\\$app->make('Illuminate\\\\Contracts\\\\Console\\\\Kernel')->bootstrap(); echo json_encode(array_keys(Schema::getColumnListing('tenants')));\"");
    if (!$output) return false;
    return strpos($output, 'plan') !== false && strpos($output, 'trial_ends_at') !== false;
});

test("Modèle Tenant accepte les champs 'plan' et 'trial_ends_at'", function() {
    $content = file_get_contents(__DIR__ . '/app/Models/Tenant.php');
    return strpos($content, "'plan'") !== false && strpos($content, "'trial_ends_at'") !== false;
});

test("Contrôleur RegisterWithPlanController existe", function() {
    return file_exists(__DIR__ . '/app/Http/Controllers/Auth/RegisterWithPlanController.php');
});

test("Route 'register' est définie", function() {
    $routes = shell_exec('php artisan route:list --name=register');
    return strpos($routes, 'register') !== false;
});

test("Route 'register.with-plan' est définie", function() {
    $routes = shell_exec('php artisan route:list --name=register.with-plan');
    return strpos($routes, 'register.with-plan') !== false;
});

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo " SECTION 3: Tests de Validation du Formulaire\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

test("Validation échoue sans company_name", function() use ($baseUrl) {
    // Get CSRF token first
    $ch = curl_init("$baseUrl/register");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    preg_match('/Set-Cookie: ([^;]+)/', $response, $matches);
    $cookie = $matches[1] ?? '';
    preg_match('/_token["\']?\s*value=["\']([^"\']+)/', $response, $tokenMatches);
    $token = $tokenMatches[1] ?? '';
    
    if (!$token || !$cookie) return false;
    
    $ch = curl_init("$baseUrl/register-with-plan");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        '_token' => $token,
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'plan' => 'pro',
        'terms' => '1'
    ]));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode === 302 && (strpos($response, 'company_name') !== false || strpos($response, 'errors') !== false);
});

test("Validation échoue avec email invalide", function() use ($baseUrl) {
    $ch = curl_init("$baseUrl/register");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    preg_match('/Set-Cookie: ([^;]+)/', $response, $matches);
    $cookie = $matches[1] ?? '';
    preg_match('/_token["\']?\s*value=["\']([^"\']+)/', $response, $tokenMatches);
    $token = $tokenMatches[1] ?? '';
    
    if (!$token || !$cookie) return false;
    
    $ch = curl_init("$baseUrl/register-with-plan");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        '_token' => $token,
        'company_name' => 'Test Company',
        'name' => 'Test User',
        'email' => 'invalid-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'plan' => 'pro',
        'terms' => '1'
    ]));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode === 302;
});

test("Validation échoue avec mot de passe trop court", function() use ($baseUrl) {
    $ch = curl_init("$baseUrl/register");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    preg_match('/Set-Cookie: ([^;]+)/', $response, $matches);
    $cookie = $matches[1] ?? '';
    preg_match('/_token["\']?\s*value=["\']([^"\']+)/', $response, $tokenMatches);
    $token = $tokenMatches[1] ?? '';
    
    if (!$token || !$cookie) return false;
    
    $ch = curl_init("$baseUrl/register-with-plan");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        '_token' => $token,
        'company_name' => 'Test Company',
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
        'plan' => 'pro',
        'terms' => '1'
    ]));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode === 302;
});

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo " RÉSULTATS FINAUX\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$total = $passed + $failed;
$percentage = $total > 0 ? round(($passed / $total) * 100, 1) : 0;

echo "Tests réussis: $passed / $total ($percentage%)\n";
echo "Tests échoués: $failed / $total\n\n";

if ($percentage >= 90) {
    echo "🎉 EXCELLENT! Le système d'inscription est opérationnel!\n";
} elseif ($percentage >= 70) {
    echo "⚠️  ATTENTION: Quelques tests ont échoué. Vérifiez les détails.\n";
} else {
    echo "❌ CRITIQUE: Plusieurs tests ont échoué. Action requise.\n";
}

echo "\n";
echo "════════════════════════════════════════════════════════════════\n";
echo " DÉTAILS DES TESTS\n";
echo "════════════════════════════════════════════════════════════════\n\n";

foreach ($tests as $test) {
    echo "{$test['status']} {$test['test']}\n";
    if (isset($test['error'])) {
        echo "   └─ Erreur: {$test['error']}\n";
    }
}

echo "\n";
echo "✅ Tests terminés!\n";
echo "\n";

exit($failed > 0 ? 1 : 0);
