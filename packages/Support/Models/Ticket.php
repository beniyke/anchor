<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Ticket model.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Support\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;
use Support\Enums\TicketPriority;
use Support\Enums\TicketStatus;

/**
 * @property int             $id
 * @property string          $refid
 * @property int             $user_id
 * @property int             $category_id
 * @property ?int            $assigned_to
 * @property string          $subject
 * @property string          $description
 * @property TicketStatus    $status
 * @property TicketPriority  $priority
 * @property ?DateTimeHelper $resolved_at
 * @property ?DateTimeHelper $closed_at
 * @property ?DateTimeHelper $sla_due_at
 * @property ?array          $metadata
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read User $user
 * @property-read ?User $agent
 * @property-read TicketCategory $category
 * @property-read ModelCollection $replies
 *
 * @method static Builder open()
 * @method static Builder forUser(int $userId)
 * @method static Builder assignedTo(int $agentId)
 */
class Ticket extends BaseModel
{
    protected string $table = 'support_ticket';

    protected array $fillable = [
        'refid',
        'user_id',
        'category_id',
        'assigned_to',
        'subject',
        'description',
        'status',
        'priority',
        'resolved_at',
        'closed_at',
        'sla_due_at',
        'metadata',
    ];

    protected array $casts = [
        'user_id' => 'int',
        'category_id' => 'int',
        'assigned_to' => 'int',
        'status' => TicketStatus::class,
        'priority' => TicketPriority::class,
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'sla_due_at' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }

    public function isOpen(): bool
    {
        return in_array($this->status, [TicketStatus::OPEN, TicketStatus::PENDING, TicketStatus::IN_PROGRESS]);
    }

    public function isResolved(): bool
    {
        return $this->status === TicketStatus::RESOLVED;
    }

    public function isClosed(): bool
    {
        return $this->status === TicketStatus::CLOSED;
    }

    public function isLow(): bool
    {
        return $this->priority === TicketPriority::LOW;
    }

    public function isMedium(): bool
    {
        return $this->priority === TicketPriority::MEDIUM;
    }

    public function isHigh(): bool
    {
        return $this->priority === TicketPriority::HIGH;
    }

    public function isUrgent(): bool
    {
        return $this->priority === TicketPriority::URGENT;
    }

    public function isSlaBreached(): bool
    {
        if (!$this->sla_due_at || $this->isResolved() || $this->isClosed()) {
            return false;
        }

        return $this->sla_due_at->isPast();
    }

    /**
     * Assign to agent.
     */
    public function assignTo(int $agentId): void
    {
        $this->update([
            'assigned_to' => $agentId,
            'status' => TicketStatus::IN_PROGRESS,
        ]);
    }

    public function resolve(): void
    {
        $this->update([
            'status' => TicketStatus::RESOLVED,
            'resolved_at' => DateTimeHelper::now(),
        ]);
    }

    public function close(): void
    {
        $this->update([
            'status' => TicketStatus::CLOSED,
            'closed_at' => DateTimeHelper::now(),
        ]);
    }

    public function reopen(): void
    {
        $this->update([
            'status' => TicketStatus::OPEN,
            'resolved_at' => null,
            'closed_at' => null,
        ]);
    }

    public static function findByRefid(string $refid): ?self
    {
        return static::where('refid', $refid)->first();
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [
            TicketStatus::OPEN,
            TicketStatus::PENDING,
            TicketStatus::IN_PROGRESS,
        ]);
    }

    /**
     * Scope for user's tickets.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeAssignedTo(Builder $query, int $agentId): Builder
    {
        return $query->where('assigned_to', $agentId);
    }
}
