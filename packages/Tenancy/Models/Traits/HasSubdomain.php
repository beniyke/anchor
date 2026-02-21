<?php

declare(strict_types=1);

namespace Tenancy\Models\Traits;

use Tenancy\Exceptions\TenantException;

trait HasSubdomain
{
    /**
     * Boot the trait
     */
    protected static function bootHasSubdomain(): void
    {
        static::creating(function ($model) {
            $excluded = config('tenancy.excluded_subdomains', []);

            if (!method_exists($model, 'isValidSubdomain')) {
                return;
            }

            /** @var object $model */
            if (!$model->isValidSubdomain($model->subdomain, $excluded)) {
                throw new TenantException("Invalid subdomain: {$model->subdomain}");
            }

            if (property_exists($model, 'db_name') && !$model->db_name && method_exists($model, 'generateDatabaseName')) {
                $prefix = config('tenancy.database.prefix_pattern', 'tenant_');
                $model->db_name = $model->generateDatabaseName($model->subdomain, $prefix);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('subdomain') && $model->exists) {
                throw new TenantException('Subdomain cannot be changed after creation');
            }
        });
    }
}
