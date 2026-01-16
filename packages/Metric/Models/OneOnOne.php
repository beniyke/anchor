<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * One On One.
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
 * @property int                $user_id
 * @property int                $manager_id
 * @property ?DateTimeInterface $scheduled_at
 * @property ?string            $agenda
 * @property ?string            $notes
 * @property ?int               $slot_id
 * @property string             $status
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read User $user
 * @property-read User $manager
 */
class OneOnOne extends BaseModel
{
    protected string $table = 'metric_one_on_one';

    protected array $fillable = [
        'user_id',
        'manager_id',
        'scheduled_at',
        'agenda',
        'notes',
        'slot_id',
        'status',
    ];

    protected array $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
