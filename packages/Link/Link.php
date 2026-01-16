<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Static facade for link operations.
 *
 * @method static LinkBuilder          make()                                         Create a new link builder
 * @method static Link                 validate(string $token)                        Validate token and return link
 * @method static Link|null            validateSafe(string $token)                    Validate without exceptions
 * @method static bool                 isValid(string $token)                         Check if token is valid
 * @method static void                 revoke(string $token)                          Revoke a link by token
 * @method static void                 revokeByRefid(string $refid)                   Revoke a link by refid
 * @method static LinkUsage            recordUsage(Link $link, array $metadata)       Record usage
 * @method static string               generateSignedUrl(Link $link, string $baseUrl) Generate signed URL
 * @method static int                  cleanup()                                      Cleanup expired links
 * @method static LinkAnalyticsService analytics()                                    Get analytics service
 *
 * @see LinkManagerService
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Link;

use Link\Models\Link as LinkModel;
use Link\Services\Builders\LinkBuilder;
use Link\Services\LinkAnalyticsService;
use Link\Services\LinkManagerService;

class Link
{
    /**
     * Create a new link builder for fluent API.
     */
    public static function make(): LinkBuilder
    {
        return new LinkBuilder(resolve(LinkManagerService::class));
    }

    public static function find(string $refid): ?LinkModel
    {
        return LinkModel::findByRefid($refid);
    }

    public static function analytics(): LinkAnalyticsService
    {
        return resolve(LinkAnalyticsService::class);
    }

    /**
     * Forward static calls to LinkManagerService.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return resolve(LinkManagerService::class)->$method(...$arguments);
    }
}
