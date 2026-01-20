<?php

declare(strict_types=1);

use Database\Migration\BaseSeeder;
use Database\DB;

/**
 * Tenant Database Seeder
 * 
 * This seeder runs when a new tenant database is created.
 * Add your tenant-specific initial data here.
 */
class TenantSeeder extends BaseSeeder
{
    public function run(): void
    {
        // Example: Create default admin user for tenant
        // ]);

        // Example: Create default settings
        // ]);

        // Call other seeders if needed
        // $this->call([
        //     'RolesSeeder',
        //     'PermissionsSeeder',
        // ]);
    }
}
