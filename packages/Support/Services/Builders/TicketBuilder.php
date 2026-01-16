<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fluent ticket builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Support\Services\Builders;

use Support\Enums\TicketPriority;
use Support\Models\Ticket;
use Support\Services\SupportManagerService;

class TicketBuilder
{
    private int $userId;

    private ?int $categoryId = null;

    private string $subject = '';

    private string $description = '';

    private TicketPriority $priority = TicketPriority::MEDIUM;

    private array $metadata = [];

    public function __construct(
        private readonly SupportManagerService $manager
    ) {
    }

    /**
     * Set the user creating the ticket.
     */
    public function for(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function category(int $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function low(): self
    {
        $this->priority = TicketPriority::LOW;

        return $this;
    }

    public function medium(): self
    {
        $this->priority = TicketPriority::MEDIUM;

        return $this;
    }

    public function high(): self
    {
        $this->priority = TicketPriority::HIGH;

        return $this;
    }

    public function urgent(): self
    {
        $this->priority = TicketPriority::URGENT;

        return $this;
    }

    public function priority(string|TicketPriority $priority): self
    {
        if (is_string($priority)) {
            $priority = TicketPriority::from($priority);
        }

        $this->priority = $priority;

        return $this;
    }

    public function metadata(array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function create(): Ticket
    {
        return $this->manager->createTicket([
            'user_id' => $this->userId,
            'category_id' => $this->categoryId,
            'subject' => $this->subject,
            'description' => $this->description,
            'priority' => $this->priority,
            'metadata' => $this->metadata,
        ]);
    }
}
