<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Fluent link builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Link\Services\Builders;

use Carbon\Carbon;
use Database\BaseModel;
use Helpers\DateTimeHelper;
use Link\Enums\LinkScope;
use Link\Models\Link;
use Link\Services\LinkManagerService;

class LinkBuilder
{
    private ?string $linkableType = null;

    private ?int $linkableId = null;

    private ?Carbon $expiresAt = null;

    private ?int $validForHours = null;

    private ?int $maxUses = null;

    private array $scopes = [];

    private ?string $recipientType = null;

    private ?string $recipientValue = null;

    private array $metadata = [];

    private ?int $createdBy = null;

    public function __construct(
        private readonly LinkManagerService $manager
    ) {
    }

    /**
     * Set the resource to link to (polymorphic).
     */
    public function for(BaseModel $model): self
    {
        $this->linkableType = get_class($model);
        $this->linkableId = $model->id;

        return $this;
    }

    /**
     * Set expiration by hours.
     */
    public function validForHours(int $hours): self
    {
        $this->validForHours = $hours;
        $this->expiresAt = null;

        return $this;
    }

    /**
     * Set expiration by days.
     */
    public function validForDays(int $days): self
    {
        return $this->validForHours($days * 24);
    }

    /**
     * Set expiration by minutes.
     */
    public function validForMinutes(int $minutes): self
    {
        $this->validForHours = null;
        $this->expiresAt = DateTimeHelper::now()->addMinutes($minutes);

        return $this;
    }

    /**
     * Set specific expiration datetime.
     */
    public function until(Carbon $datetime): self
    {
        $this->expiresAt = $datetime;
        $this->validForHours = null;

        return $this;
    }

    /**
     * Set no expiration.
     */
    public function forever(): self
    {
        $this->expiresAt = null;
        $this->validForHours = null;

        return $this;
    }

    /**
     * Set maximum uses.
     */
    public function maxUses(int $count): self
    {
        $this->maxUses = $count;

        return $this;
    }

    /**
     * Set as single use.
     */
    public function singleUse(): self
    {
        return $this->maxUses(1);
    }

    public function scopes(array $scopes): self
    {
        $this->scopes = $scopes;

        return $this;
    }

    public function scope(string|LinkScope $scope): self
    {
        if ($scope instanceof LinkScope) {
            $scope = $scope->value;
        }

        if (!in_array($scope, $this->scopes, true)) {
            $this->scopes[] = $scope;
        }

        return $this;
    }

    public function view(): self
    {
        return $this->scope(LinkScope::VIEW);
    }

    public function download(): self
    {
        return $this->scope(LinkScope::DOWNLOAD);
    }

    public function edit(): self
    {
        return $this->scope(LinkScope::EDIT);
    }

    /**
     * Set join scope (for invites).
     */
    public function join(): self
    {
        return $this->scope(LinkScope::JOIN);
    }

    public function share(): self
    {
        return $this->scope(LinkScope::SHARE);
    }

    /**
     * Shorthand for invite-type link.
     */
    public function invite(): self
    {
        return $this->join()->singleUse();
    }

    public function recipient(string $email): self
    {
        $this->recipientType = 'email';
        $this->recipientValue = $email;

        return $this;
    }

    /**
     * Bind to IP address.
     */
    public function recipientIp(string $ip): self
    {
        $this->recipientType = 'ip';
        $this->recipientValue = $ip;

        return $this;
    }

    /**
     * Bind to user ID.
     */
    public function recipientUser(int $userId): self
    {
        $this->recipientType = 'user_id';
        $this->recipientValue = (string) $userId;

        return $this;
    }

    public function metadata(array $data): self
    {
        $this->metadata = $data;

        return $this;
    }

    /**
     * Add metadata key.
     */
    public function with(string $key, mixed $value): self
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    /**
     * Set the creator.
     */
    public function by(int $userId): self
    {
        $this->createdBy = $userId;

        return $this;
    }

    public function create(): Link
    {
        $data = [
            'linkable_type' => $this->linkableType,
            'linkable_id' => $this->linkableId,
            'scopes' => !empty($this->scopes) ? $this->scopes : [LinkScope::VIEW->value],
            'max_uses' => $this->maxUses,
            'recipient_type' => $this->recipientType,
            'recipient_value' => $this->recipientValue,
            'metadata' => $this->metadata,
            'created_by' => $this->createdBy,
        ];

        if ($this->expiresAt !== null) {
            $data['expires_at'] = $this->expiresAt;
        } elseif ($this->validForHours !== null) {
            $data['valid_for_hours'] = $this->validForHours;
        }

        return $this->manager->create($data);
    }
}
