<?php

namespace Database\Seeders;

use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un tenant de démonstration
        $tenant = Tenant::create([
            'name' => 'Demo Company',
            'slug' => 'demo',
            'domain' => 'demo',
            'is_active' => true,
            'settings' => [
                'currency' => 'XOF',
                'language' => 'fr',
                'timezone' => 'Africa/Dakar',
                'tax_rate' => 18,
                'invoice_prefix' => 'INV-',
            ],
        ]);

        // Créer ou mettre à jour l'utilisateur admin
        $user = User::firstOrCreate(
            ['email' => 'leaudouce0@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'plan' => 'enterprise',
            ]
        );

        $user->update([
            'tenant_id' => $tenant->id,
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->command->info('✅ Tenant créé: ' . $tenant->name);
        $this->command->info('✅ Compte Admin configuré:');
        $this->command->info('   📧 Email: leaudouce0@gmail.com');
        $this->command->info('   🔑 Mot de passe: password123');
        $this->command->info('   🌐 Accès: /admin');
    }
}
