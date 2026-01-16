<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserStatus;
use Bridge\Contracts\TokenableInterface;
use Bridge\Traits\HasApiTokens;
use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Query\Builder;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Database\Traits\HasRefid;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $name
 * @property ?string         $gender
 * @property ?string         $phone
 * @property ?string         $photo
 * @property string          $email
 * @property string          $refid
 * @property string          $password
 * @property UserStatus      $status
 * @property ?string         $reset_token
 * @property ?DateTimeHelper $reset_token_created_at
 * @property ?string         $activation_token
 * @property ?DateTimeHelper $activation_token_created_at
 * @property ?DateTimeHelper $password_updated_at
 * @property ?int            $role_id
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ?Role $role
 * @property-read ModelCollection $activities
 * @property-read ModelCollection $notifications
 */
class User extends BaseModel implements TokenableInterface
{
    use HasApiTokens;
    use HasRefid;

    protected string $table = 'user';

    protected array $fillable = [
        'name',
        'gender',
        'phone',
        'photo',
        'email',
        'refid',
        'password',
        'status',
        'reset_token',
        'reset_token_created_at',
        'activation_token',
        'activation_token_created_at',
        'password_updated_at',
        'role_id',
    ];

    protected array $hidden = ['password', 'reset_token', 'activation_token'];

    protected array $casts = [
        'refid' => 'string',
        'password_updated_at' => 'datetime',
        'reset_token_created_at' => 'datetime',
        'activation_token_created_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'role_id' => 'integer',
        'status' => UserStatus::class,
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'user_id', 'id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id', 'id')
            ->latest();
    }

    public static function findByEmail(string $email): ?static
    {
        return static::query()
            ->where('email', $email)
            ->cache(3600) // 1 hour
            ->cacheTags(['users', "user:email:{$email}"])
            ->first();
    }

    public static function findByEmailForAuth(string $email): ?static
    {
        return static::query()->where('email', $email)->first();
    }

    public static function findByRefid(string $refid): ?static
    {
        return static::query()
            ->where('refid', $refid)
            ->cache(3600) // 1 hour
            ->cacheTags(['users', "user:refid:{$refid}"])
            ->first();
    }

    public static function superAdmin(): Builder
    {
        return static::query()
            ->whereNull('role_id')
            ->cache(3600)
            ->cacheTags(['users', 'super-admin']);
    }

    public function scopeWhereActivationToken(Builder $query, string $token): Builder
    {
        return $query->where('activation_token', $token);
    }

    public function scopeWhereResetToken(Builder $query, string $token): Builder
    {
        return $query->where('reset_token', $token);
    }

    public function scopeHasActivationToken(Builder $query): Builder
    {
        return $query->whereNotNull('activation_token');
    }

    public function scopeHasResetToken(Builder $query): Builder
    {
        return $query->whereNotNull('reset_token');
    }

    public function scopeResetTokenValid(Builder $query, int $minutes): Builder
    {
        $cutoffTime = DateTimeHelper::now()->subMinutes($minutes);

        return $query->newerThan('reset_token_created_at', $cutoffTime);
    }

    public function scopeActivationTokenValid(Builder $query, int $minutes): Builder
    {
        $cutoffTime = DateTimeHelper::now()->subMinutes($minutes);

        return $query->newerThan('activation_token_created_at', $cutoffTime);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Active->value);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Inactive->value);
    }

    public function scopeSearch(Builder $query, string $searchQuery): Builder
    {
        $terms = array_filter(preg_split('/[+\s]+/', strtolower(trim($searchQuery))), 'strlen');

        if (empty($terms)) {
            return $query;
        }

        $query->where(function (Builder $query) use ($terms) {
            $firstTerm = true;

            foreach ($terms as $term) {
                $searchTerm = '%' . $term . '%';
                $method = $firstTerm ? 'whereAnyLike' : 'orWhereAnyLike';
                $query->$method(['name', 'email'], $searchTerm);
                $firstTerm = false;
            }
        });

        return $query;
    }

    public function scopeNonSuperAdmin(Builder $query): Builder
    {
        return $query->whereNotNull('role_id');
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active;
    }

    public function isSuspended(): bool
    {
        return $this->status === UserStatus::Suspended;
    }

    private function setStatusAndSave(UserStatus $newStatus): bool
    {
        $this->status = $newStatus;

        return $this->save();
    }

    public function activate(): bool
    {
        $this->status = UserStatus::Active;
        $this->clearActivationToken();

        return $this->save();
    }

    public function deactivate(): bool
    {
        return $this->setStatusAndSave(UserStatus::Inactive);
    }

    public function suspend(): bool
    {
        return $this->setStatusAndSave(UserStatus::Suspended);
    }

    public function unsuspend(): bool
    {
        $this->status = UserStatus::Active;

        return $this->save();
    }

    public function updatePassword(string $newPassword): bool
    {
        $updates = [
            'password' => $newPassword,
            'password_updated_at' => DateTimeHelper::now(),
            'reset_token' => null,
            'reset_token_created_at' => null,
        ];

        return $this->update($updates);
    }

    public function clearResetToken(): bool
    {
        $this->reset_token = null;
        $this->reset_token_created_at = null;

        return $this->save();
    }

    public function clearActivationToken(): bool
    {
        $this->activation_token = null;
        $this->activation_token_created_at = null;

        return $this->save();
    }

    public function hasActivationToken(): bool
    {
        return ! is_null($this->activation_token) && trim($this->activation_token) !== '';
    }

    public function hasResetToken(): bool
    {
        return ! is_null($this->reset_token) && trim($this->reset_token) !== '';
    }

    public function hasPendingTokens(): bool
    {
        return $this->hasActivationToken() || $this->hasResetToken();
    }

    public function canLogin(): bool
    {
        return ! $this->hasPendingTokens() && $this->isActive();
    }

    public function isSuperAdmin(): bool
    {
        return is_null($this->role_id);
    }

    public function hasRole(string $roleTitle): bool
    {
        return $this->role && $this->role->title === $roleTitle;
    }

    public function hasNotifications(): bool
    {
        return $this->notificationsCount() > 0;
    }

    public function hasUnreadNotifications(): bool
    {
        return $this->unreadNotificationsCount() > 0;
    }

    public function hasActivities(): bool
    {
        return $this->activities()->exists();
    }

    public function can(string $permissionKey): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->role ? $this->role->hasPermission($permissionKey) : false;
    }

    public function emailHasChanged(string $newEmail): bool
    {
        return strtolower(trim($this->email)) !== strtolower(trim($newEmail));
    }

    public function passwordNeedsUpdate(int $maxAgeInDays): bool
    {
        $daysSinceUpdate = DateTimeHelper::now()->diffInDays($this->password_updated_at);

        return $daysSinceUpdate > $maxAgeInDays;
    }

    public function hasPhoto(): bool
    {
        return ! is_null($this->photo);
    }

    public function hasIncompleteProfile(): bool
    {
        return empty($this->phone) && ! $this->hasPhoto();
    }

    public static function findWithRole(int $id): ?static
    {
        return static::query()
            ->with('role')
            ->where('id', $id)
            ->cache(3600) // 1 hour
            ->cacheTags(['users', "user:{$id}", 'with-role'])
            ->first();
    }

    public static function activeUsersCount(): int
    {
        return static::query()
            ->active()
            ->cache(900) // 15 minutes
            ->cacheTags(['users', 'stats', 'active-users'])
            ->count();
    }

    public function unreadNotificationsCount(): int
    {
        return Notification::unreadCountForUser($this->id);
    }

    public function unreadNotifications(int $limit = 10): array
    {
        return Notification::unreadForUser($this->id, $limit);
    }

    public function unreadNotificationsData(int $limit = 10): array
    {
        return [
            'notifications' => $this->unreadNotifications($limit),
            'count' => $this->unreadNotificationsCount(),
        ];
    }

    public function notificationsCount(): int
    {
        return $this->notifications()
            ->cache(300) // 5 minutes
            ->cacheTags(['notifications', "user:{$this->id}", 'count'])
            ->count();
    }
}
