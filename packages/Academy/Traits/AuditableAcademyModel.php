<?php

declare(strict_types=1);

namespace Academy\Traits;

use Audit\Audit;
use ReflectionClass;

trait AuditableAcademyModel
{
    /**
     * Boot the auditable trait for the model.
     */
    public static function bootAuditableAcademyModel(): void
    {
        if (!config('academy.integrations.audit', true) || !class_exists(Audit::class)) {
            return;
        }

        static::created(function ($model) {
            $name = strtolower((new ReflectionClass($model))->getShortName());
            Audit::make()
                ->event('academy.' . $name . '.created')
                ->on($model)
                ->metadata($model->toArray())
                ->log();
        });

        static::updated(function ($model) {
            $name = strtolower((new ReflectionClass($model))->getShortName());
            Audit::make()
                ->event('academy.' . $name . '.updated')
                ->on($model)
                ->metadata([
                    'changes' => (method_exists($model, 'getChanges') ? $model->getChanges() : []),
                    'original' => (method_exists($model, 'getOriginal') ? $model->getOriginal() : []),
                ])
                ->log();
        });

        static::deleted(function ($model) {
            $name = strtolower((new ReflectionClass($model))->getShortName());
            Audit::make()
                ->event('academy.' . $name . '.deleted')
                ->on($model)
                ->log();
        });
    }
}
