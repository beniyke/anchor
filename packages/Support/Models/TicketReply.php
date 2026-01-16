<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Ticket reply model.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Support\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $ticket_id
 * @property int             $user_id
 * @property string          $message
 * @property bool            $is_internal
 * @property ?array          $attachments
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Ticket $ticket
 * @property-read User $user
 */
class TicketReply extends BaseModel
{
    protected string $table = 'support_ticket_reply';

    protected array $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_internal',
        'attachments',
    ];

    protected array $casts = [
        'ticket_id' => 'int',
        'user_id' => 'int',
        'is_internal' => 'bool',
        'attachments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isFromAgent(): bool
    {
        return $this->user_id !== $this->ticket->user_id;
    }
}
