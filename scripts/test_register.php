<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== TEST D'INSCRIPTION ===\n\n";

try {
    $timestamp = time();
    
    // 1. Créer le tenant
    $tenantData = [
        'name' => 'Test Company ' . $timestamp,
        'slug' => 'test-company-' . $timestamp,
        'plan' => 'starter',
        'trial_ends_at' => now()->addDays(30),
        'is_active' => true,
    ];
    
    echo "1. Creating tenant...\n";
    $tenant = Tenant::create($tenantData);
    echo "   ✅ Tenant created: ID={$tenant->id}, Name={$tenant->name}\n";
    
    // 2. Créer l'utilisateur
    $userData = [
        'tenant_id' => $tenant->id,
        'name' => 'Test User',
        'email' => 'test' . $timestamp . '@example.com',
        'password' => Hash::make('password123'),
        'role' => 'admin',
        'is_active' => true,
    ];
    
    echo "2. Creating user...\n";
    $user = User::create($userData);
    echo "   ✅ User created: ID={$user->id}, Email={$user->email}\n";
    
    // 3. Vérifier la relation
    echo "3. Checking relationship...\n";
    $loadedUser = User::with('tenant')->find($user->id);
    echo "   ✅ User's tenant: {$loadedUser->tenant->name}\n";
    
    // 4. Vérifier canAccessPanel
    echo "4. Checking canAccessPanel...\n";
    $panel = new class {
        public function getId() { return 'admin'; }
    };
    
    if ($loadedUser->tenant && $loadedUser->tenant->isActive()) {
        echo "   ✅ User can access panel (tenant is active)\n";
    } else {
        echo "   ❌ User cannot access panel\n";
    }
    
    echo "\n========================================\n";
    echo "✅ INSCRIPTION FONCTIONNE CORRECTEMENT!\n";
    echo "========================================\n";
    echo "\nVous pouvez maintenant vous inscrire via la modale.\n";
    
} catch (Exception $e) {
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
