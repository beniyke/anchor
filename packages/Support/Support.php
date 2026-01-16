<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Static facade for support operations.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Support;

use Support\Models\Ticket;
use Support\Models\TicketCategory;
use Support\Services\Builders\CategoryBuilder;
use Support\Services\Builders\TicketBuilder;
use Support\Services\SupportAnalyticsService;
use Support\Services\SupportManagerService;
use Support\Services\SupportRateLimiterService;

class Support
{
    /**
     * Create a new ticket builder for fluent API.
     */
    public static function make(): TicketBuilder
    {
        return new TicketBuilder(resolve(SupportManagerService::class));
    }

    /**
     * Create a new category builder.
     */
    public static function configCategory(): CategoryBuilder
    {
        return new CategoryBuilder();
    }

    public static function createTicket(array $data): Ticket
    {
        return resolve(SupportManagerService::class)->createTicket($data);
    }

    public static function reply(Ticket $ticket, int $userId, string $message, bool $isInternal = false): void
    {
        resolve(SupportManagerService::class)->reply($ticket, $userId, $message, $isInternal);
    }

    public static function assign(Ticket $ticket, int $agentId): void
    {
        resolve(SupportManagerService::class)->assign($ticket, $agentId);
    }

    public static function resolve(Ticket $ticket): void
    {
        resolve(SupportManagerService::class)->resolve($ticket);
    }

    public static function close(Ticket $ticket): void
    {
        resolve(SupportManagerService::class)->close($ticket);
    }

    public static function categories(?bool $active = true): array
    {
        $query = TicketCategory::query();

        if ($active !== null) {
            $query->active($active);
        }

        return $query->ordered()->get()->all();
    }

    public static function find(string $refid): ?Ticket
    {
        return Ticket::findByRefid($refid);
    }

    public static function analytics(): SupportAnalyticsService
    {
        return resolve(SupportAnalyticsService::class);
    }

    public static function rateLimiter(): SupportRateLimiterService
    {
        return resolve(SupportRateLimiterService::class);
    }

    /**
     * Forward static calls to SupportManagerService.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        return resolve(SupportManagerService::class)->$method(...$arguments);
    }
}
