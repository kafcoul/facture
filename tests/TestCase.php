<?php

namespace Tests;

use App\Domain\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create default tenant for tests if table exists and tenant doesn't exist
        if (Schema::hasTable('tenants') && !Tenant::find(1)) {
            Tenant::create([
                'id' => 1,
                'name' => 'Test Company',
                'slug' => 'test',
                'domain' => 'test.local',
                'is_active' => true,
                'settings' => [
                    'currency' => 'XOF',
                    'language' => 'fr',
                    'timezone' => 'Africa/Dakar',
                ],
            ]);
        }
    }
}
