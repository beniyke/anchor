<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Onboarding Builder.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Onboard\Services\Builders;

use App\Models\User;
use DateTimeInterface;
use Onboard\Models\Onboarding;
use Onboard\Models\Template;
use Onboard\Services\OnboardManagerService;
use RuntimeException;

class OnboardingBuilder
{
    protected OnboardManagerService $manager;

    protected ?User $user = null;

    protected ?Template $template = null;

    protected ?DateTimeInterface $dueAt = null;

    public function __construct(OnboardManagerService $manager)
    {
        $this->manager = $manager;
    }

    public function for(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function using(Template $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function dueAt(DateTimeInterface $date): self
    {
        $this->dueAt = $date;

        return $this;
    }

    public function start(): Onboarding
    {
        if (!$this->user || !$this->template) {
            throw new RuntimeException("Onboarding requires a user and a template.");
        }

        return $this->manager->startOnboarding($this->user, $this->template, $this->dueAt);
    }
}
