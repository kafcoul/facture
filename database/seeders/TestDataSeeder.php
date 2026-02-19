<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use App\Domain\Client\Models\Client;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un tenant de test
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'test-company'],
            [
                'name' => 'Test Company',
                'domain' => 'testcompany.local',
                'is_active' => true,
            ]
        );

        // Créer un utilisateur admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@testcompany.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Admin Test',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Créer un utilisateur client
        $client = User::firstOrCreate(
            ['email' => 'client@testcompany.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Client Test',
                'password' => Hash::make('password'),
                'role' => 'client',
                'is_active' => true,
            ]
        );

        // Créer 5 clients de test
        $clients = [
            [
                'name' => 'ABC Corporation',
                'email' => 'contact@abc-corp.com',
                'company' => 'ABC Corporation',
                'phone' => '+33 1 23 45 67 89',
                'address' => '123 Avenue des Champs',
                'city' => 'Paris',
                'country' => 'France',
            ],
            [
                'name' => 'XYZ Solutions',
                'email' => 'info@xyz-solutions.com',
                'company' => 'XYZ Solutions SARL',
                'phone' => '+33 4 56 78 90 12',
                'address' => '456 Rue de la République',
                'city' => 'Lyon',
                'country' => 'France',
            ],
            [
                'name' => 'Tech Innovators',
                'email' => 'contact@tech-innovators.com',
                'company' => 'Tech Innovators SAS',
                'phone' => '+33 5 67 89 01 23',
                'address' => '789 Boulevard Victor Hugo',
                'city' => 'Toulouse',
                'country' => 'France',
            ],
            [
                'name' => 'Digital Services',
                'email' => 'hello@digital-services.fr',
                'company' => 'Digital Services',
                'phone' => '+33 2 34 56 78 90',
                'address' => '321 Place de la Gare',
                'city' => 'Nantes',
                'country' => 'France',
            ],
            [
                'name' => 'Consulting Group',
                'email' => 'contact@consulting-group.fr',
                'company' => 'Consulting Group SA',
                'phone' => '+33 3 45 67 89 01',
                'address' => '654 Avenue Jean Jaurès',
                'city' => 'Marseille',
                'country' => 'France',
            ],
        ];

        foreach ($clients as $clientData) {
            Client::firstOrCreate(
                [
                    'email' => $clientData['email'],
                    'tenant_id' => $tenant->id,
                ],
                array_merge($clientData, [
                    'tenant_id' => $tenant->id,
                    'user_id' => $admin->id,
                ])
            );
        }

        // Créer 10 produits de test
        $products = [
            [
                'name' => 'Développement Web',
                'description' => 'Prestation de développement web (par heure)',
                'price' => 50.00,
                'unit_price' => 50.00,
                'unit' => 'heure',
                'tax_rate' => 20.00,
            ],
            [
                'name' => 'Consulting IT',
                'description' => 'Conseil en systèmes d\'information (par heure)',
                'price' => 80.00,
                'unit_price' => 80.00,
                'unit' => 'heure',
                'tax_rate' => 20.00,
            ],
            [
                'name' => 'Formation',
                'description' => 'Formation technique (par heure)',
                'price' => 70.00,
                'unit_price' => 70.00,
                'unit' => 'heure',
                'tax_rate' => 20.00,
            ],
            [
                'name' => 'Support Technique',
                'description' => 'Support technique et maintenance (par heure)',
                'price' => 45.00,
                'unit_price' => 45.00,
                'unit' => 'heure',
                'tax_rate' => 20.00,
            ],
            [
                'name' => 'Design Graphique',
                'description' => 'Création graphique et design (par heure)',
                'price' => 55.00,
                'unit_price' => 55.00,
                'unit' => 'heure',
                'tax_rate' => 20.00,
            ],
            [
                'name' => 'Hébergement Web',
                'description' => 'Hébergement web professionnel (mensuel)',
                'price' => 25.00,
                'unit_price' => 25.00,
                'unit' => 'mois',
                'tax_rate' => 20.00,
            ],
            [
                'name' => 'Nom de Domaine',
                'description' => 'Enregistrement nom de domaine (annuel)',
                'price' => 15.00,
                'unit_price' => 15.00,
                'unit' => 'an',
                'tax_rate' => 20.00,
            ],
            [
                'name' => 'Certificat SSL',
                'description' => 'Certificat SSL sécurisé (annuel)',
                'price' => 50.00,
                'unit_price' => 50.00,
                'unit' => 'an',
                'tax_rate' => 20.00,
            ],
            [
                'name' => 'Audit SEO',
                'description' => 'Audit complet de référencement',
                'price' => 300.00,
                'unit_price' => 300.00,
                'unit' => 'forfait',
                'tax_rate' => 20.00,
            ],
            [
                'name' => 'Rédaction de Contenu',
                'description' => 'Rédaction de contenu web (par article)',
                'price' => 100.00,
                'unit_price' => 100.00,
                'unit' => 'article',
                'tax_rate' => 20.00,
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                [
                    'name' => $productData['name'],
                    'tenant_id' => $tenant->id,
                ],
                array_merge($productData, [
                    'tenant_id' => $tenant->id,
                    'user_id' => $admin->id,
                ])
            );
        }

        $this->command->info('✅ Données de test créées avec succès !');
        $this->command->info('');
        $this->command->info('�‍💼 Compte Admin (accès /admin):');
        $this->command->info('   �📧 Email: admin@testcompany.com');
        $this->command->info('   🔑 Password: password');
        $this->command->info('');
        $this->command->info('👥 Compte Client (accès /dashboard):');
        $this->command->info('   📧 Email: client@testcompany.com');
        $this->command->info('   🔑 Password: password');
        $this->command->info('');
        $this->command->info('📊 Données:');
        $this->command->info('   👥 Clients: ' . Client::where('tenant_id', $tenant->id)->count());
        $this->command->info('   📦 Produits: ' . Product::where('tenant_id', $tenant->id)->count());
    }
}
