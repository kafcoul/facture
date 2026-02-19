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

        // Mettre à jour l'utilisateur existant avec le tenant
        $user = User::where('email', 'leaudouce0@gmail.com')->first();
        if ($user) {
            $user->update([
                'tenant_id' => $tenant->id,
                'role' => 'admin',
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ Tenant créé: ' . $tenant->name);
        $this->command->info('✅ User mis à jour avec tenant_id: ' . $tenant->id);
    }
}
