<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Candidate.
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
 * @property ?int               $user_id
 * @property string             $name
 * @property string             $email
 * @property ?string            $phone
 * @property ?string            $bio
 * @property string             $status
 * @property bool               $is_talent_pool
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read ?User $user
 * @property-read ModelCollection $applications
 * @property-read ModelCollection $files
 * @property-read ModelCollection $notes
 */
class Candidate extends BaseModel
{
    protected string $table = 'scout_candidate';

    protected array $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'bio',
        'status',
        'is_talent_pool',
    ];

    protected array $casts = [
        'is_talent_pool' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'scout_candidate_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(CandidateFile::class, 'scout_candidate_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'noteable_id')->where('noteable_type', static::class);
    }
}
