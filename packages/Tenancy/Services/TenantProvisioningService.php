<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service for provisioning and managing tenant resources.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Tenancy\Services;

use Database\Connection;
use Database\DB;
use Database\Helpers\DatabaseOperationConfig;
use Database\Migration\Migrator;
use Database\Migration\SeedManager;
use RuntimeException;
use Tenancy\Exceptions\TenantException;
use Tenancy\Models\Tenant;
use Throwable;

class TenantProvisioningService
{
    /**
     * Create a new tenant with database provisioning
     */
    public function create(array $data): Tenant
    {
        return DB::transaction(function () use ($data) {
            // Validate required fields
            $this->validateTenantData($data);

            // Generate database credentials
            $dbConfig = $this->generateDatabaseConfig($data['subdomain']);

            $tenant = Tenant::create(array_merge($data, $dbConfig));
            logger('tenancy')->info("Tenant created: {$tenant->subdomain}");

            // Create tenant database
            $this->createTenantDatabase($tenant);
            logger('tenancy.log')->info("Tenant database created: {$tenant->db_name}");

            // Run migrations on tenant database
            $this->runTenantMigrations($tenant);
            logger('tenancy.log')->info("Tenant migrations completed");

            // Seed initial data if needed
            if ($data['seed'] ?? false) {
                $this->seedTenantDatabase($tenant);
                logger('tenancy.log')->info("Tenant database seeded");
            }

            return $tenant;
        });
    }

    /**
     * Delete tenant and cleanup resources
     */
    public function delete(Tenant $tenant, bool $dropDatabase = true): void
    {
        DB::transaction(function () use ($tenant, $dropDatabase) {
            if ($dropDatabase) {
                $this->dropTenantDatabase($tenant);
                logger('tenancy.log')->info("Tenant database dropped: {$tenant->db_name}");
            }

            $tenant->delete();
            logger('tenancy.log')->info("Tenant deleted: {$tenant->subdomain}");

            // Clear cache
            $cacheKey = config('tenancy.cache.key_prefix', 'tenant:') . "subdomain:{$tenant->subdomain}";
            cache('tenants')->delete($cacheKey);
        });
    }

    private function validateTenantData(array $data): void
    {
        $required = ['name', 'subdomain'];

        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new TenantException("Missing required field: {$field}");
            }
        }

        // Validate subdomain
        if (!Tenant::isValidSubdomain($data['subdomain'])) {
            throw TenantException::invalidSubdomain($data['subdomain']);
        }

        // Check if subdomain already exists
        if (Tenant::where('subdomain', $data['subdomain'])->exists()) {
            throw TenantException::alreadyExists($data['subdomain']);
        }
    }

    private function generateDatabaseConfig(string $subdomain): array
    {
        $prefix = config('tenancy.database.prefix_pattern', 'tenant_');
        $sanitized = preg_replace('/[^a-z0-9_]/', '_', strtolower($subdomain));
        $dbName = $prefix . $sanitized;

        return [
            'db_host' => config('tenancy.database.default_host', '127.0.0.1'),
            'db_port' => config('tenancy.database.default_port', '3306'),
            'db_name' => $dbName,
            'db_user' => $this->generateDatabaseUser($dbName),
            'db_password' => $this->generateSecurePassword(),
        ];
    }

    private function generateDatabaseUser(string $dbName): string
    {
        // Truncate to MySQL username limit (32 chars)
        return substr($dbName, 0, 32);
    }

    private function generateSecurePassword(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    private function createTenantDatabase(Tenant $tenant): void
    {
        $config = $tenant->getDatabaseConfig();

        if ($config['driver'] === 'sqlite') {
            $database = $config['database'];

            if ($database === ':memory:') {
                return;
            }

            $directory = dirname($database);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            if (file_exists($database)) {
                $this->unlinkDatabase($database);
            }

            touch($database);

            return;
        }

        $dbName = $tenant->db_name;
        $charset = config('tenancy.database.default_charset', 'utf8mb4');
        $collation = config('tenancy.database.default_collation', 'utf8mb4_unicode_ci');

        // Create database
        DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET {$charset} COLLATE {$collation}");

        // Create database user
        $user = $tenant->db_user;
        $password = $tenant->db_password;
        $host = $tenant->db_host;

        DB::statement("CREATE USER IF NOT EXISTS '{$user}'@'{$host}' IDENTIFIED BY '{$password}'");
        DB::statement("GRANT ALL PRIVILEGES ON `{$dbName}`.* TO '{$user}'@'{$host}'");
        DB::statement('FLUSH PRIVILEGES');
    }

    private function dropTenantDatabase(Tenant $tenant): void
    {
        $config = $tenant->getDatabaseConfig();

        if ($config['driver'] === 'sqlite') {
            $database = $config['database'];
            if ($database !== ':memory:' && file_exists($database)) {
                $this->unlinkDatabase($database);
            }

            return;
        }

        $dbName = $tenant->db_name;
        $user = $tenant->db_user;
        $host = $tenant->db_host;

        // Drop database
        DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");

        // Drop user
        DB::statement("DROP USER IF EXISTS '{$user}'@'{$host}'");
        DB::statement('FLUSH PRIVILEGES');
    }

    /**
     * Run migrations on tenant database
     */
    private function runTenantMigrations(Tenant $tenant): void
    {
        // Store original connection
        $originalConnection = DB::connection();

        try {
            // Create tenant connection
            $tenantConnection = $this->createTenantConnection($tenant);

            // Set as default
            DB::setDefaultConnection($tenantConnection);

            // Verify connection works
            $tenantConnection->getPdo();

            // Get migration path
            $dbConfig = config('database');
            $operationConfig = new DatabaseOperationConfig($dbConfig);

            $migrationPath = config('tenancy.database.migrations_path', $operationConfig->getMigrationsPath());

            // Run migrations
            $migrator = new Migrator($tenantConnection, $migrationPath);
            $migrator->run();
        } finally {
            // Restore original connection
            DB::setDefaultConnection($originalConnection);
            if (isset($tenantConnection)) {
                $tenantConnection->disconnect();
            }
        }
    }

    /**
     * Seed tenant database with initial data
     */
    private function seedTenantDatabase(Tenant $tenant): void
    {
        // Store original connection
        $originalConnection = DB::connection();

        try {
            // Create tenant connection
            $tenantConnection = $this->createTenantConnection($tenant);

            // Set as default
            DB::setDefaultConnection($tenantConnection);

            // Get seeder path from DatabaseOperationConfig
            $dbConfig = config('database');
            $operationConfig = new DatabaseOperationConfig($dbConfig);
            $seederPath = $operationConfig->getSeedsPath();

            $seedManager = new SeedManager($tenantConnection, $seederPath);

            // Run the default seeder (or tenant-specific seeder)
            $seederClass = config('tenancy.default_seeder', 'TenantSeeder');

            try {
                $seedManager->run($seederClass);
            } catch (RuntimeException $e) {
                // If TenantSeeder doesn't exist, try DatabaseSeeder
                if (str_contains($e->getMessage(), 'not found')) {
                    $seedManager->run('DatabaseSeeder');
                } else {
                    throw $e;
                }
            }
        } finally {
            // Restore original connection
            DB::setDefaultConnection($originalConnection);
            if (isset($tenantConnection)) {
                $tenantConnection->disconnect();
            }
        }
    }

    private function createTenantConnection(Tenant $tenant): Connection
    {
        $config = $tenant->getDatabaseConfig();

        if ($config['driver'] === 'sqlite') {
            $dsn = "sqlite:{$config['database']}";
        } else {
            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=%s',
                $config['driver'],
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset'] ?? 'utf8mb4'
            );
        }

        return Connection::configure($dsn, $config['username'], $config['password'])
            ->connect();
    }

    /**
     * Unlink database file with retry logic for Windows
     */
    private function unlinkDatabase(string $path): void
    {
        // Force garbage collection to release any PDO handles
        gc_collect_cycles();

        $attempts = 0;
        $maxAttempts = 5;

        while ($attempts < $maxAttempts) {
            try {
                if (!file_exists($path)) {
                    return;
                }

                if (@unlink($path)) {
                    return;
                }

                $attempts++;
                if ($attempts < $maxAttempts) {
                    usleep(100000); // 100ms
                }
            } catch (Throwable $e) {
                $attempts++;
                if ($attempts >= $maxAttempts) {
                    throw $e;
                }
                usleep(100000);
            }
        }
    }
}
