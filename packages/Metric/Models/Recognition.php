<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Recognition.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use DateTimeInterface;

/**
 * @property int                $id
 * @property int                $sender_id
 * @property int                $receiver_id
 * @property string             $award_type
 * @property string             $message
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read User $sender
 * @property-read User $receiver
 */
class Recognition extends BaseModel
{
    protected string $table = 'metric_recognition';

    protected array $fillable = [
        'sender_id',
        'receiver_id',
        'award_type',
        'message',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
