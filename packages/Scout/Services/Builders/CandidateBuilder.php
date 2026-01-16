<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Candidate Builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Services\Builders;

use RuntimeException;
use Scout\Models\Candidate;
use Scout\Services\ScoutManagerService;

class CandidateBuilder
{
    protected ScoutManagerService $manager;

    protected array $data = [
        'status' => 'active',
        'is_talent_pool' => false,
    ];

    public function __construct(ScoutManagerService $manager)
    {
        $this->manager = $manager;
    }

    public function name(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function email(string $email): self
    {
        $this->data['email'] = $email;

        return $this;
    }

    public function phone(string $phone): self
    {
        $this->data['phone'] = $phone;

        return $this;
    }

    public function bio(string $bio): self
    {
        $this->data['bio'] = $bio;

        return $this;
    }

    public function talentPool(bool $active = true): self
    {
        $this->data['is_talent_pool'] = $active;

        return $this;
    }

    public function associateUser(int $userId): self
    {
        $this->data['user_id'] = $userId;

        return $this;
    }

    public function create(): Candidate
    {
        if (empty($this->data['name']) || empty($this->data['email'])) {
            throw new RuntimeException("Candidate requires a name and email.");
        }

        return $this->manager->findOrCreateCandidate($this->data);
    }
}
