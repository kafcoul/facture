<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TestTenantSeeder extends Seeder
{
    /**
     * Seed default tenant for testing.
     */
    public function run(): void
    {
        // Create default tenant if it doesn't exist
        Tenant::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Test Company',
                'slug' => 'test',
                'domain' => 'test.local',
                'is_active' => true,
                'settings' => json_encode([
                    'currency' => 'XOF',
                    'language' => 'fr',
                    'timezone' => 'Africa/Dakar',
                ]),
            ]
        );
    }
}
