<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Rate limiter for Support package to prevent spam.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Support\Services;

use Helpers\DateTimeHelper;
use Support\Models\Ticket;
use Support\Models\TicketReply;

class SupportRateLimiterService
{
    public function canCreateTicket(int $userId, int $maxTicketsPerHour = 5): bool
    {
        $oneHourAgo = DateTimeHelper::now()->subHours(1)->format('Y-m-d H:i:s');

        $recentTickets = Ticket::where('user_id', $userId)
            ->where('created_at', '>=', $oneHourAgo)
            ->count();

        return $recentTickets < $maxTicketsPerHour;
    }

    public function canReply(int $userId, int $ticketId, int $maxRepliesPerMinute = 3): bool
    {
        $oneMinuteAgo = DateTimeHelper::now()->subMinutes(1)->format('Y-m-d H:i:s');

        $recentReplies = TicketReply::where('user_id', $userId)
            ->where('ticket_id', $ticketId)
            ->where('created_at', '>=', $oneMinuteAgo)
            ->count();

        return $recentReplies < $maxRepliesPerMinute;
    }

    public function getRemainingTicketAttempts(int $userId, int $maxTicketsPerHour = 5): int
    {
        $oneHourAgo = DateTimeHelper::now()->subHours(1)->format('Y-m-d H:i:s');

        $recentTickets = Ticket::where('user_id', $userId)
            ->where('created_at', '>=', $oneHourAgo)
            ->count();

        return max(0, $maxTicketsPerHour - $recentTickets);
    }

    public function getTimeUntilNextTicket(int $userId, int $maxTicketsPerHour = 5): ?int
    {
        if ($this->canCreateTicket($userId, $maxTicketsPerHour)) {
            return 0;
        }

        // Find oldest ticket in the window
        $oneHourAgo = DateTimeHelper::now()->subHours(1);

        $oldestTicket = Ticket::where('user_id', $userId)
            ->where('created_at', '>=', $oneHourAgo->format('Y-m-d H:i:s'))
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$oldestTicket) {
            return 0;
        }

        // Calculate when it will expire from the window
        $expiresAt = $oldestTicket->created_at->addHours(1);

        return max(0, DateTimeHelper::now()->diffInSeconds($expiresAt, false));
    }
}
