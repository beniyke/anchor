<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Strategy for handling multi-tenancy via separate databases.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Tenancy\Strategies;

use Database\Connection;
use Database\DB;
use Tenancy\Exceptions\TenantException;
use Tenancy\Models\Tenant;
use Throwable;

class SeparateDatabaseStrategy
{
    private const MAX_RETRY_ATTEMPTS = 3;
    private const RETRY_DELAY_MS = 100;

    private ?Connection $originalConnection = null;

    private ?Connection $tenantConnection = null;

    /**
     * Set tenant context by switching database connection
     */
    public function setTenantContext(Tenant $tenant): void
    {
        try {
            // Store original connection
            $this->originalConnection = DB::connection();

            // Get tenant database configuration
            $config = $tenant->getDatabaseConfig();

            // Validate configuration
            $this->validateDatabaseConfig($config);

            $this->tenantConnection = $this->createConnection($config);

            // Attempt connection with retry logic
            $this->connectWithRetry($tenant);

            DB::setDefaultConnection($this->tenantConnection);
        } catch (Throwable $e) {
            throw TenantException::databaseConnectionFailed($tenant->subdomain, $e);
        }
    }

    /**
     * Reset to central database
     */
    public function resetContext(): void
    {
        if ($this->originalConnection) {
            DB::setDefaultConnection($this->originalConnection);
        }

        // Disconnect tenant connection
        if ($this->tenantConnection) {
            $this->tenantConnection->disconnect();
            $this->tenantConnection = null;
        }
    }

    /**
     * Test tenant database connection
     */
    public function testConnection(Tenant $tenant): bool
    {
        try {
            $config = $tenant->getDatabaseConfig();
            $testConnection = $this->createConnection($config);

            // Verify we can actually query
            $pdo = $testConnection->getPdo();
            $pdo->query('SELECT 1');

            $testConnection->disconnect();

            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Create database connection from config
     */
    private function createConnection(array $config): Connection
    {
        if ($config['driver'] === 'sqlite') {
            $dsn = "sqlite:{$config['database']}";
        } else {
            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=%s',
                $config['driver'],
                $config['host'] ?? '127.0.0.1',
                $config['port'] ?? '3306',
                $config['database'],
                $config['charset'] ?? 'utf8mb4'
            );
        }

        return Connection::configure($dsn, $config['username'] ?? null, $config['password'] ?? null)
            ->connect();
    }

    private function validateDatabaseConfig(array $config): void
    {
        $required = ['host', 'database', 'username'];

        foreach ($required as $key) {
            if (empty($config[$key])) {
                throw new TenantException("Missing required database config: {$key}");
            }
        }
    }

    /**
     * Connect with retry logic
     */
    private function connectWithRetry(Tenant $tenant): void
    {
        $attempts = 0;
        $lastException = null;

        while ($attempts < self::MAX_RETRY_ATTEMPTS) {
            try {
                $this->tenantConnection->getPdo();

                return;
            } catch (Throwable $e) {
                $lastException = $e;
                $attempts++;

                if ($attempts < self::MAX_RETRY_ATTEMPTS) {
                    usleep(self::RETRY_DELAY_MS * 1000 * $attempts);
                }
            }
        }

        throw TenantException::databaseConnectionFailed($tenant->subdomain, $lastException);
    }
}
