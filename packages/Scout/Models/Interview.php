<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Interview.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use DateTimeInterface;

/**
 * @property int                $id
 * @property int                $scout_application_id
 * @property int                $user_id
 * @property ?int               $slot_id
 * @property ?DateTimeInterface $scheduled_at
 * @property int                $duration
 * @property ?string            $location
 * @property ?string            $internal_notes
 * @property string             $status
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read Application $application
 * @property-read User $interviewer
 * @property-read ModelCollection $scorecards
 */
class Interview extends BaseModel
{
    public const TABLE = 'scout_interview';

    protected string $table = self::TABLE;

    protected array $fillable = [
        'scout_application_id',
        'user_id',
        'slot_id',
        'scheduled_at',
        'duration',
        'location',
        'internal_notes',
        'status',
        'reminded_at',
    ];

    protected array $casts = [
        'scheduled_at' => 'datetime',
        'reminded_at' => 'datetime',
        'duration' => 'integer',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'scout_application_id');
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scorecards(): HasMany
    {
        return $this->hasMany(Scorecard::class, 'scout_interview_id');
    }

    public function scopePendingReminders($query, $start, $end)
    {
        return $query->where('status', 'scheduled')
            ->whereNull('reminded_at')
            ->whereBetween('scheduled_at', [$start, $end]);
    }
}
