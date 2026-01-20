<?php

declare(strict_types=1);

use Database\Migration\BaseSeeder;
use Permit\Permit;

/**
 * RolesSeeder
 *
 * This seeder initializes the default roles and permissions for the application.
 * It serves as the "Source of Truth" for the authorization structure.
 */
class RolesSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define Permissions
        // Group: Writing
        Permit::permission()->slug('posts.create')->group('Writing')->create();
        Permit::permission()->slug('posts.edit')->group('Writing')->create();

        // Group: Admin
        Permit::permission()->slug('posts.delete')->group('Admin')->create();
        Permit::permission()->slug('settings.manage')->group('Admin')->create();

        // 2. Define Roles & Inheritance

        // Editor Role: Can create and edit posts
        Permit::role()
            ->slug('editor')
            ->name('Content Editor')
            ->permissions(['posts.create', 'posts.edit'])
            ->create();

        // Admin Role: Inherits Editor powers + adds deletion and settings
        Permit::role()
            ->slug('admin')
            ->name('Administrator')
            ->inherits('editor')
            ->permissions(['posts.delete', 'settings.manage'])
            ->create();

        // Super Admin: Bypasses all checks (Defined in config, but we can seed it)
        Permit::role()
            ->slug('super-admin')
            ->name('Super Administrator')
            ->create();
    }
}
