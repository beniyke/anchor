<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Client Facade provides a static interface for client operations.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Client;

use Client\Models\Client as ClientModel;
use Client\Services\AnalyticsManagerService;
use Client\Services\Builders\ClientBuilder;
use Client\Services\ClientManagerService;

class Client
{
    /**
     * Start a fluent client creation builder.
     */
    public static function make(): ClientBuilder
    {
        return resolve(ClientManagerService::class)->make();
    }

    /**
     * Find a client by ID.
     */
    public static function find(int|string $id): ?ClientModel
    {
        return ClientModel::find($id);
    }

    public static function analytics(): AnalyticsManagerService
    {
        return resolve(AnalyticsManagerService::class);
    }

    public static function findByEmail(string $email): ?ClientModel
    {
        return resolve(ClientManagerService::class)->findByEmail($email);
    }

    public static function findByRefid(string $refid): ?ClientModel
    {
        return resolve(ClientManagerService::class)->findByRefid($refid);
    }

    public static function getByReseller(int|string $resellerId): array
    {
        return resolve(ClientManagerService::class)->getByReseller($resellerId);
    }

    /**
     * Forward static calls to ClientManagerService.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return resolve(ClientManagerService::class)->$method(...$arguments);
    }
}
