<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Model representing a tenant.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Tenancy\Models;

use Database\BaseModel;
use Helpers\DateTimeHelper;
use Tenancy\Exceptions\TenantException;
use Throwable;

/**
 * @property int             $id
 * @property string          $name
 * @property string          $subdomain
 * @property string          $status
 * @property ?array          $config
 * @property ?string         $db_host
 * @property ?string         $db_port
 * @property string          $db_name
 * @property ?string         $db_user
 * @property ?string         $db_password
 * @property string          $plan
 * @property int             $max_users
 * @property int             $max_storage_mb
 * @property ?DateTimeHelper $trial_ends_at
 * @property ?DateTimeHelper $expires_at
 * @property bool            $is_default
 * @property ?DateTimeHelper $last_activity_at
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 */
class Tenant extends BaseModel
{
    protected string $table = 'tenant';

    protected array $fillable = [
        'name',
        'subdomain',
        'status',
        'config',
        'db_host',
        'db_port',
        'db_name',
        'db_user',
        'db_password',
        'plan',
        'max_users',
        'max_storage_mb',
        'trial_ends_at',
        'expires_at',
        'is_default',
        'last_activity_at',
    ];

    protected array $casts = [
        'config' => 'array',
        'is_default' => 'boolean',
        'max_users' => 'integer',
        'max_storage_mb' => 'integer',
        'trial_ends_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    protected array $hidden = [
        'db_password',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($tenant) {
            if (!self::isValidSubdomain($tenant->subdomain)) {
                throw new TenantException("Invalid subdomain: {$tenant->subdomain}");
            }

            if (!$tenant->db_name) {
                $tenant->db_name = self::generateDatabaseName($tenant->subdomain);
            }
        });

        static::updating(function ($tenant) {
            if ($tenant->isDirty('subdomain') && $tenant->exists) {
                throw new TenantException('Subdomain cannot be changed after creation');
            }
        });
    }

    /**
     * Get tenant configuration value with dot notation support
     */
    public function getConfig(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config ?? [];

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }

            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Set tenant configuration value with dot notation support
     */
    public function setConfig(string $key, $value): void
    {
        $config = $this->config ?? [];
        $keys = explode('.', $key);
        $current = &$config;

        foreach ($keys as $i => $k) {
            if ($i === count($keys) - 1) {
                $current[$k] = $value;
            } else {
                if (!isset($current[$k]) || !is_array($current[$k])) {
                    $current[$k] = [];
                }
                $current = &$current[$k];
            }
        }

        $this->config = $config;
        $this->save();
    }

    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function getDbPasswordAttribute($value = null): string
    {
        $value = $value ?? $this->attributes['db_password'] ?? null;

        if (!$value) {
            return '';
        }

        try {
            return decrypt($value);
        } catch (Throwable $e) {
            throw new TenantException('Failed to decrypt database password');
        }
    }

    /**
     * Set encrypted database password
     */
    public function setDbPasswordAttribute($value): void
    {
        if (!$value) {
            $this->attributes['db_password'] = null;

            return;
        }

        $this->attributes['db_password'] = encrypt($value);
    }

    public function getDatabaseConfig(): array
    {
        return [
            'driver' => config('tenancy.database.driver', 'mysql'),
            'host' => $this->db_host,
            'port' => $this->db_port,
            'database' => $this->db_name,
            'username' => $this->db_user,
            'password' => $this->db_password,
            'charset' => config('tenancy.database.charset', 'utf8mb4'),
            'collation' => config('tenancy.database.collation', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'strict' => true,
            'engine' => 'InnoDB',
        ];
    }

    /**
     * Update last activity timestamp
     */
    public function touchActivity(): void
    {
        $this->last_activity_at = date('Y-m-d H:i:s');
        $this->save();
    }

    public static function isValidSubdomain(string $subdomain): bool
    {
        // Must be lowercase alphanumeric with hyphens
        if (!preg_match('/^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?$/', $subdomain)) {
            return false;
        }

        // Check against excluded subdomains
        $excluded = config('tenancy.excluded_subdomains', []);
        if (in_array($subdomain, $excluded)) {
            return false;
        }

        // Check against reserved keywords
        $reserved = ['admin', 'api', 'www', 'mail', 'ftp', 'localhost', 'test'];
        if (in_array($subdomain, $reserved)) {
            return false;
        }

        return true;
    }

    private static function generateDatabaseName(string $subdomain): string
    {
        $prefix = config('tenancy.database.prefix_pattern', 'tenant_');
        $sanitized = preg_replace('/[^a-z0-9_]/', '_', strtolower($subdomain));

        return $prefix . $sanitized;
    }
}
