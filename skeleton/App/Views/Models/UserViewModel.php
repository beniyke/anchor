<?php

declare(strict_types=1);

namespace App\Views\Models;

use App\Models\User;
use Helpers\DateTimeHelper;
use Helpers\String\Str;

readonly class UserViewModel
{
    private int $id;

    private string $name;

    private string $email;

    private string $gender;

    private string $refid;

    private ?string $phone;

    private bool $hasPhoto;

    private ?string $photo;

    private bool $hasIncompleteProfile;

    private string $status;

    private string $statusColor;

    private bool $shouldUpdatePassword;

    private array $missingProfileFields;

    private ?string $roleName;

    private ?array $notifications;

    private int $notificationCount;

    private bool $isActive;

    private bool $isSuspended;

    private bool $isPending;

    private DateTimeHelper $createdAt;

    private DateTimeHelper $updatedAt;

    public function __construct(User $user, array $with = [])
    {
        $this->id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->gender = $user->gender;
        $this->refid = $user->refid;
        $this->phone = $user->phone;
        $this->hasPhoto = $user->hasPhoto();
        $this->photo = $user->photo;
        $this->hasIncompleteProfile = $user->hasIncompleteProfile();
        $this->status = $user->status->label();
        $this->statusColor = $user->status->color();
        $this->shouldUpdatePassword = $user->passwordNeedsUpdate(config('auth.password_max_age_days'));
        $this->missingProfileFields = $this->getMissingProfileFieldsList($user);
        $this->isActive = $user->isActive();
        $this->isSuspended = $user->isSuspended();
        $this->isPending = $user->hasActivationToken();
        $this->createdAt = $user->created_at;
        $this->updatedAt = $user->updated_at;

        if (in_array('role', $with, true)) {
            $this->roleName = $user->role ? $user->role->title : null;
        } else {
            $this->roleName = null;
        }

        if (in_array('notifications', $with, true)) {
            $data = $user->unreadNotificationsData(3);
            $this->notifications = NotificationViewModel::collection($data['notifications']);
            $this->notificationCount = $data['count'];
        } else {
            $this->notifications = null;
            $this->notificationCount = 0;
        }
    }

    public static function basic(User $user): self
    {
        return new self($user);
    }

    public static function withRole(User $user): self
    {
        return new self($user, ['role']);
    }

    public static function withNotifications(User $user): self
    {
        return new self($user, ['notifications']);
    }

    public static function full(User $user): self
    {
        return new self($user, ['role', 'notifications']);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortName(): string
    {
        return Str::shortenWithInitials($this->name);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getRefid(): string
    {
        return $this->refid;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getRoleName(): ?string
    {
        return $this->roleName;
    }

    public function getAvatar(): string
    {
        $photo_path = rtrim(config('app.assets.photo'), '/') . '/' . md5($this->getRefid());

        return $this->hasPhoto() ? url($photo_path . '/' . $this->photo) : '';
    }

    public function hasPhoto(): bool
    {
        return $this->hasPhoto;
    }

    public function hasIncompleteProfile(): bool
    {
        return $this->hasIncompleteProfile;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStatusColor(): string
    {
        return $this->statusColor;
    }

    public function shouldUpdatePassword(): bool
    {
        return $this->shouldUpdatePassword;
    }

    public function getNotifications(): ?array
    {
        return $this->notifications;
    }

    public function getNotificationCount(): int
    {
        return $this->notificationCount;
    }

    public function hasNotification(): bool
    {
        return $this->notificationCount > 0;
    }

    public function getMissingProfileFields(): array
    {
        return $this->missingProfileFields;
    }

    public function hasMissingProfileFields(): bool
    {
        return ! empty($this->getMissingProfileFields());
    }

    private function getMissingProfileFieldsList(User $user): array
    {
        $fields = [];
        if (! $user->hasPhoto()) {
            $fields[] = 'Profile Picture';
        }

        if (empty($user->phone)) {
            $fields[] = 'Phone Number';
        }

        return $fields;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt->format('Y-m-d');
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt->format('Y-m-d');
    }

    public function getCreatedTimeAgo(): string
    {
        return DateTimeHelper::timeAgo($this->createdAt->format('Y-m-d H:i:s'));
    }

    public function getUpdatedTimeAgo(): string
    {
        return DateTimeHelper::timeAgo($this->updatedAt->format('Y-m-d H:i:s'));
    }

    public function getFormattedCreatedAt(): string
    {
        return $this->createdAt->format('M j, Y');
    }

    public function getFormattedUpdatedAt(): string
    {
        return $this->updatedAt->format('M j, Y');
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function isSuspended(): bool
    {
        return $this->isSuspended;
    }

    public function isPending(): bool
    {
        return $this->isPending;
    }

    public function isInactive(): bool
    {
        return ! $this->isActive && ! $this->isSuspended && ! $this->isPending;
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'short_name' => $this->getShortName(),
            'email' => $this->getEmail(),
            'gender' => $this->getGender(),
            'refid' => $this->getRefid(),
            'phone' => $this->getPhone(),
            'avatar' => $this->getAvatar(),
            'has_photo' => $this->hasPhoto(),
            'has_incomplete_profile' => $this->hasIncompleteProfile(),
            'status' => $this->getStatus(),
            'status_color' => $this->getStatusColor(),
            'should_update_password' => $this->shouldUpdatePassword(),
            'missing_profile_fields' => $this->getMissingProfileFields(),
            'notification_count' => $this->getNotificationCount(),
            'created_at' => $this->getFormattedCreatedAt(),
            'updated_at' => $this->getFormattedUpdatedAt(),
            'updated_time_ago' => $this->getUpdatedTimeAgo(),
        ];

        if ($this->roleName !== null) {
            $data['role_name'] = $this->getRoleName();
        }

        if ($this->notifications !== null) {
            $data['notifications'] = array_map(fn (NotificationViewModel $notifications) => $notifications->toArray(), $this->notifications);
        }

        return $data;
    }

    public static function collection(array $users, array $with = []): array
    {
        return array_map(fn (User $user) => new self($user, $with), $users);
    }
}
