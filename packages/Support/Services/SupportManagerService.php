<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Core support manager service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Support\Services;

use Core\Services\ConfigServiceInterface;
use Helpers\DateTimeHelper;
use Helpers\String\Str;
use Support\Enums\TicketPriority;
use Support\Enums\TicketStatus;
use Support\Models\Ticket;
use Support\Models\TicketCategory;
use Support\Models\TicketReply;

class SupportManagerService
{
    public function __construct(
        private readonly ConfigServiceInterface $config
    ) {
    }

    public function createTicket(array $data): Ticket
    {
        $priority = $data['priority'] ?? $this->config->get('support.default_priority', 'medium');

        if (is_string($priority)) {
            $priority = TicketPriority::from($priority);
        }

        $slaHours = $this->config->get("support.priorities.{$priority->value}.sla_hours", 24);

        $ticket = Ticket::create([
            'refid' => Str::random('secure'),
            'user_id' => $data['user_id'],
            'category_id' => $data['category_id'] ?? null,
            'subject' => $data['subject'],
            'description' => $data['description'],
            'status' => TicketStatus::OPEN,
            'priority' => $priority,
            'sla_due_at' => DateTimeHelper::now()->addHours($slaHours),
            'metadata' => $data['metadata'] ?? [],
        ]);

        return $ticket;
    }

    public function reply(Ticket $ticket, int $userId, string $message, bool $isInternal = false): TicketReply
    {
        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $userId,
            'message' => $message,
            'is_internal' => $isInternal,
        ]);

        // Update ticket status if reply is from customer
        if ($userId === $ticket->user_id && $ticket->status !== TicketStatus::OPEN) {
            $ticket->update(['status' => TicketStatus::PENDING]);
        }

        return $reply;
    }

    public function assign(Ticket $ticket, int $agentId): void
    {
        $ticket->assignTo($agentId);
    }

    public function resolve(Ticket $ticket): void
    {
        $ticket->resolve();
    }

    public function close(Ticket $ticket): void
    {
        $ticket->close();
    }

    public function getCategories(): array
    {
        return TicketCategory::active()
            ->ordered()
            ->get()
            ->all();
    }

    public function getUserTickets(int $userId): array
    {
        return Ticket::forUser($userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();
    }

    public function getAgentTickets(int $agentId): array
    {
        return Ticket::assignedTo($agentId)
            ->open()
            ->orderBy('sla_due_at', 'asc')
            ->get()
            ->all();
    }

    public function getUnassignedTickets(): array
    {
        return Ticket::whereNull('assigned_to')
            ->open()
            ->orderBy('created_at', 'asc')
            ->get()
            ->all();
    }

    public function getSlaBreachedTickets(): array
    {
        return Ticket::open()
            ->where('sla_due_at', '<', DateTimeHelper::now())
            ->orderBy('sla_due_at', 'asc')
            ->get()
            ->all();
    }

    public function autoCloseResolvedTickets(): int
    {
        $days = $this->config->get('support.auto_close_days', 7);

        if ($days <= 0) {
            return 0;
        }

        $cutoff = DateTimeHelper::now()->subDays($days);

        $tickets = Ticket::where('status', TicketStatus::RESOLVED)
            ->where('resolved_at', '<', $cutoff)
            ->get();

        $count = 0;

        foreach ($tickets as $ticket) {
            $ticket->close();
            $count++;
        }

        return $count;
    }
}
