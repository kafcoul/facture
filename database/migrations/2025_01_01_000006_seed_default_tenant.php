<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Skip seeding in test environment
        if (app()->environment('testing')) {
            return;
        }

        // Créer un tenant par défaut seulement s'il n'existe pas
        $exists = DB::table('tenants')->where('slug', 'demo')->exists();
        
        if (!$exists) {
            DB::table('tenants')->insert([
                'name' => 'Demo Company',
                'slug' => 'demo',
                'domain' => 'demo',
                'is_active' => true,
                'settings' => json_encode([
                    'currency' => 'XOF',
                    'language' => 'fr',
                    'timezone' => 'Africa/Dakar',
                    'tax_rate' => 18,
                    'invoice_prefix' => 'INV-',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Récupérer l'ID du tenant
        $tenantId = DB::table('tenants')->where('slug', 'demo')->value('id');

        // Mettre à jour tous les utilisateurs existants
        DB::table('users')->update([
            'tenant_id' => $tenantId,
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Mettre à jour toutes les données existantes avec le tenant_id
        if ($tenantId) {
            DB::table('clients')->update(['tenant_id' => $tenantId]);
            DB::table('products')->update(['tenant_id' => $tenantId]);
            
            // Générer UUID pour les factures existantes
            $invoices = DB::table('invoices')->whereNull('uuid')->get();
            foreach ($invoices as $invoice) {
                DB::table('invoices')
                    ->where('id', $invoice->id)
                    ->update([
                        'tenant_id' => $tenantId,
                        'uuid' => \Illuminate\Support\Str::uuid(),
                        'currency' => 'XOF',
                        'issued_at' => $invoice->created_at ?? now(),
                    ]);
            }
            
            DB::table('invoice_items')->update(['tenant_id' => $tenantId]);
            DB::table('payments')->update([
                'tenant_id' => $tenantId,
                'currency' => 'XOF',
            ]);
        }
    }

    public function down(): void
    {
        // Remettre à null
        DB::table('users')->update(['tenant_id' => null]);
        DB::table('clients')->update(['tenant_id' => null]);
        DB::table('products')->update(['tenant_id' => null]);
        DB::table('invoices')->update(['tenant_id' => null]);
        DB::table('invoice_items')->update(['tenant_id' => null]);
        DB::table('payments')->update(['tenant_id' => null]);
        
        DB::table('tenants')->where('slug', 'demo')->delete();
    }
};
