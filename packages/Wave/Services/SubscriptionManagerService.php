<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Subscription Manager for handling subscription lifecycle.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wave\Services;

use Database\DB;
use DateTimeInterface;
use Helpers\Data;
use Helpers\DateTimeHelper;
use Helpers\String\Str;
use Mail\Mail;
use Wave\Enums\PlanInterval;
use Wave\Enums\SubscriptionStatus;
use Wave\Exceptions\PlanNotFoundException;
use Wave\Exceptions\SubscriptionActiveException;
use Wave\Exceptions\SubscriptionNotFoundException;
use Wave\Models\Plan;
use Wave\Models\Subscription;
use Wave\Notifications\RenewalReminderNotification;

class SubscriptionManagerService
{
    public function __construct(
        private readonly PlanManagerService $planManager,
        private readonly InvoiceManagerService $invoiceManager
    ) {
    }

    public function subscribe(string|int $ownerId, string $ownerType, string|int $planId, array $options = []): Subscription
    {
        $plan = $this->planManager->find($planId);
        if (!$plan) {
            throw PlanNotFoundException::forId($planId);
        }

        if ($this->hasActiveSubscription($ownerId, $ownerType)) {
            throw new SubscriptionActiveException();
        }

        return DB::transaction(function () use ($ownerId, $ownerType, $plan, $options) {
            $trialDays = $options['trial_days'] ?? $plan->trial_days;
            $status = $trialDays > 0 ? SubscriptionStatus::TRIALING : SubscriptionStatus::ACTIVE;
            $trialEndsAt = $trialDays > 0 ? DateTimeHelper::now()->addDays($trialDays) : null;

            $subscription = Subscription::create([
                'refid' => Str::random('alnum', 16),
                'owner_id' => $ownerId,
                'owner_type' => $ownerType,
                'plan_id' => $plan->id,
                'status' => $status->value,
                'quantity' => $options['quantity'] ?? 1,
                'trial_ends_at' => $trialEndsAt,
                'current_period_start' => DateTimeHelper::now(),
                'current_period_end' => $this->calculatePeriodEnd(DateTimeHelper::now(), $plan),
                'metadata' => $options['metadata'] ?? null,
            ]);

            if ($status === SubscriptionStatus::ACTIVE) {
                $this->invoiceManager->createFromSubscription($subscription);
            }

            return $subscription;
        });
    }

    public function swap(string|int $subscriptionId, string|int $newPlanId): Subscription
    {
        $subscription = $this->find($subscriptionId);
        if (!$subscription) {
            throw SubscriptionNotFoundException::forId($subscriptionId);
        }

        $newPlan = $this->planManager->find($newPlanId);
        if (!$newPlan) {
            throw PlanNotFoundException::forId($newPlanId);
        }

        return DB::transaction(function () use ($subscription, $newPlan) {
            $unusedValue = $this->calculateProration($subscription, $newPlan);
            $oldPlanId = $subscription->plan_id;

            $subscription->update([
                'plan_id' => $newPlan->id,
                'current_period_start' => DateTimeHelper::now(),
                'current_period_end' => $this->calculatePeriodEnd(DateTimeHelper::now(), $newPlan),
            ]);

            $subscription->refresh();

            $this->invoiceManager->createFromSubscription($subscription, "Plan Swap from {$subscription->plan->name}", [
                'proration_credit' => $unusedValue,
                'swap_from_plan_id' => $oldPlanId,
            ]);

            return $subscription;
        });
    }

    private function calculateProration(Subscription $subscription, Plan $newPlan): int
    {
        $currentPlan = $subscription->plan;

        if ($subscription->status === SubscriptionStatus::TRIALING) {
            return 0;
        }

        $now = DateTimeHelper::now();
        $start = $subscription->current_period_start;
        $end = $subscription->current_period_end;

        if ($now >= $end) {
            return 0;
        }

        $totalSeconds = $end->getTimestamp() - $start->getTimestamp();
        $usedSeconds = $now->getTimestamp() - $start->getTimestamp();
        $remainingSeconds = $totalSeconds - $usedSeconds;

        if ($totalSeconds <= 0) {
            return 0;
        }

        $currentValue = $currentPlan->price * $subscription->quantity;
        $proratedAmount = (int) ($currentValue * ($remainingSeconds / $totalSeconds));

        return max(0, $proratedAmount);
    }

    public function cancel(string|int $subscriptionId, bool $atPeriodEnd = true): bool
    {
        $subscription = $this->find($subscriptionId);
        if (!$subscription) {
            return false;
        }

        if ($atPeriodEnd) {
            return $subscription->update([
                'ends_at' => $subscription->current_period_end,
                'canceled_at' => DateTimeHelper::now(),
            ]);
        }

        return $subscription->update([
            'status' => SubscriptionStatus::CANCELED->value,
            'ends_at' => DateTimeHelper::now(),
            'canceled_at' => DateTimeHelper::now(),
        ]);
    }

    public function find(string|int $id): ?Subscription
    {
        if (is_numeric($id)) {
            return Subscription::find($id);
        }

        return Subscription::query()->where('refid', $id)->first();
    }

    public function hasActiveSubscription(string|int $ownerId, string $ownerType): bool
    {
        return Subscription::query()
            ->where('owner_id', $ownerId)
            ->where('owner_type', $ownerType)
            ->whereIn('status', [SubscriptionStatus::ACTIVE->value, SubscriptionStatus::TRIALING->value])
            ->exists();
    }

    public function updateQuantity(string|int $subscriptionId, int $quantity): bool
    {
        $subscription = $this->find($subscriptionId);
        if (!$subscription) {
            return false;
        }

        return $subscription->update(['quantity' => $quantity]);
    }

    public function sendRenewalReminders(int $daysAhead = 3): int
    {
        $threshold = DateTimeHelper::now()->addDays($daysAhead);

        $subscriptions = Subscription::query()
            ->where('status', SubscriptionStatus::ACTIVE->value)
            ->where('current_period_end', '<=', $threshold)
            ->whereNull('ends_at') // Not canceled
            ->get();

        $count = 0;
        foreach ($subscriptions as $subscription) {
            $email = $subscription->metadata['email'] ?? null;
            if ($email) {
                $payload = Data::make([
                    'email' => $email,
                    'name' => $subscription->metadata['name'] ?? '',
                    'plan_name' => $subscription->plan->name,
                    'renewal_date' => $subscription->current_period_end->format('Y-m-d'),
                    'manage_url' => config('wave.invoice.subscription_url', '/billing/subscription') . '/' . $subscription->refid,
                ]);

                Mail::send(new RenewalReminderNotification($payload));

                $count++;
            }
        }

        return $count;
    }

    public function calculatePeriodEnd(DateTimeInterface $start, Plan $plan): DateTimeInterface
    {
        $date = DateTimeHelper::parse($start->format('Y-m-d H:i:s'));
        $count = $plan->interval_count;

        return match ($plan->interval) {
            PlanInterval::DAY => $date->addDays($count),
            PlanInterval::WEEK => $date->addWeeks($count),
            PlanInterval::MONTH => $date->addMonths($count),
            PlanInterval::YEAR => $date->addYears($count),
        };
    }
}
