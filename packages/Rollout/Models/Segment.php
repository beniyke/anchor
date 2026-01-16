<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Segment model for feature targeting.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Rollout\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;
use InvalidArgumentException;
use Rollout\Enums\SegmentType;

/**
 * @property int             $id
 * @property int             $feature_id
 * @property string          $name
 * @property SegmentType     $type
 * @property string          $value
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Feature $feature
 * @property-read ModelCollection $users
 */
class Segment extends BaseModel
{
    protected string $table = 'rollout_segment';

    protected array $fillable = [
        'feature_id',
        'name',
        'type',
        'value',
    ];

    protected array $casts = [
        'feature_id' => 'int',
        'type' => SegmentType::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(SegmentUser::class, 'segment_id');
    }

    public function matches(?User $user = null, array $context = []): bool
    {
        return match ($this->type) {
            SegmentType::USER_ID => $user && $this->containsByUserId($user),
            SegmentType::ROLE => $user && $this->containsByRole($user),
            SegmentType::PLAN => $user && $this->containsByPlan($user),
            SegmentType::EMAIL_DOMAIN => $user && $this->containsByEmailDomain($user),
            SegmentType::LOCATION => $this->matchesLocation($context),
            default => false,
        };
    }

    private function matchesLocation(array $context): bool
    {
        $city = $context['city'] ?? $context['location']['city'] ?? null;
        $country = $context['country'] ?? $context['location']['country_code'] ?? null;

        return strtolower((string)$city) === strtolower($this->value) ||
            strtolower((string)$country) === strtolower($this->value);
    }

    private function containsByUserId(User $user): bool
    {
        return $this->users()
            ->where('user_id', $user->id)
            ->exists();
    }

    private function containsByRole(User $user): bool
    {
        if (!method_exists($user, 'hasRole')) {
            return false;
        }

        return $user->hasRole($this->value);
    }

    private function containsByPlan(User $user): bool
    {
        // Check if user has subscription with matching plan
        if (!method_exists($user, 'subscriptions')) {
            return false;
        }

        $subscriptions = $user->subscriptions()->get();

        foreach ($subscriptions as $subscription) {
            if ($subscription->plan && $subscription->plan->slug === $this->value) {
                return true;
            }
        }

        return false;
    }

    private function containsByEmailDomain(User $user): bool
    {
        if (!isset($user->email)) {
            return false;
        }

        $domain = substr(strrchr($user->email, '@'), 1);

        return strtolower($domain) === strtolower($this->value);
    }

    public function addUser(User $user): void
    {
        if ($this->type !== SegmentType::USER_ID) {
            throw new InvalidArgumentException('Can only add users to USER_ID segments.');
        }

        $exists = $this->users()->where('user_id', $user->id)->exists();

        if (!$exists) {
            SegmentUser::create([
                'segment_id' => $this->id,
                'user_id' => $user->id,
            ]);
        }
    }

    public function removeUser(User $user): void
    {
        $this->users()->where('user_id', $user->id)->delete();
    }
}
