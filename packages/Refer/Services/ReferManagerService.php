<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Core referral manager service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Refer\Services;

use Core\Event;
use Core\Services\ConfigServiceInterface;
use Exception;
use Helpers\DateTimeHelper;
use Helpers\String\Str;
use Refer\Enums\ReferralStatus;
use Refer\Events\ReferralCompletedEvent;
use Refer\Models\Referral;
use Refer\Models\ReferralCode;
use Wallet\Wallet;

class ReferManagerService
{
    public function __construct(
        private readonly ConfigServiceInterface $config
    ) {
    }

    public function generateCode(int $userId, ?string $customCode = null): ReferralCode
    {
        $existing = ReferralCode::where('user_id', $userId)->first();

        if ($existing) {
            return $existing;
        }

        $code = $customCode ?? $this->generateUniqueCode();

        $expiryDays = $this->config->get('refer.limits.code_expiry_days', 0);
        $maxUses = $this->config->get('refer.limits.max_referrals', 0);

        return ReferralCode::create([
            'user_id' => $userId,
            'code' => strtoupper($code),
            'is_active' => true,
            'uses_count' => 0,
            'max_uses' => $maxUses,
            'expires_at' => $expiryDays > 0 ? DateTimeHelper::now()->addDays($expiryDays) : null,
        ]);
    }

    public function getCode(int $userId): ?ReferralCode
    {
        return ReferralCode::where('user_id', $userId)->first();
    }

    public function complete(string $code, int $refereeId): ?Referral
    {
        return $this->track($code, $refereeId);
    }

    public function track(string $code, int $refereeId): ?Referral
    {
        $referralCode = ReferralCode::findByCode($code);

        if (!$referralCode || !$referralCode->isValid()) {
            return null;
        }

        if ($referralCode->user_id === $refereeId) {
            return null;
        }

        $existingReferral = Referral::where('referee_id', $refereeId)->first();

        if ($existingReferral) {
            return null;
        }

        $referrerReward = $this->config->get('refer.rewards.referrer.amount', 0);
        $refereeReward = $this->config->get('refer.rewards.referee.amount', 0);

        $referral = Referral::create([
            'code_id' => $referralCode->id,
            'referrer_id' => $referralCode->user_id,
            'referee_id' => $refereeId,
            'status' => ReferralStatus::PENDING,
            'referrer_reward' => $referrerReward,
            'referee_reward' => $refereeReward,
        ]);

        $referralCode->incrementUsage();

        if (!$this->config->get('refer.conditions.require_purchase', false)) {
            $this->reward($referral);
        }

        return $referral;
    }

    public function reward(Referral $referral): bool
    {
        if ($referral->isRewarded()) {
            return false;
        }

        if ($referral->referrer_reward > 0) {
            $this->creditWallet(
                $referral->referrer_id,
                $referral->referrer_reward,
                'Referral reward for user #' . $referral->referee_id,
                ['referral_id' => $referral->id, 'type' => 'referrer_reward']
            );
        }

        if ($referral->referee_reward > 0) {
            $this->creditWallet(
                $referral->referee_id,
                $referral->referee_reward,
                'Welcome bonus for joining via referral',
                ['referral_id' => $referral->id, 'type' => 'referee_reward']
            );
        }

        $referral->markAsRewarded();

        Event::dispatch(new ReferralCompletedEvent($referral));

        return true;
    }

    private function creditWallet(int $userId, int $amount, string $description, array $metadata): void
    {
        if (!class_exists(Wallet::class)) {
            return;
        }

        try {
            Wallet::for($userId)
                ->credit($amount)
                ->description($description)
                ->metadata($metadata)
                ->execute();
        } catch (Exception $e) {
        }
    }

    public function getReferrals(int $userId): array
    {
        return Referral::where('referrer_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();
    }

    public function getStats(int $userId): array
    {
        $referrals = Referral::where('referrer_id', $userId);

        return [
            'total_referrals' => (clone $referrals)->count(),
            'pending_referrals' => (clone $referrals)->where('status', ReferralStatus::PENDING)->count(),
            'rewarded_referrals' => (clone $referrals)->where('status', ReferralStatus::REWARDED)->count(),
            'total_earnings' => (clone $referrals)->where('status', ReferralStatus::REWARDED)->sum('referrer_reward'),
        ];
    }

    private function generateUniqueCode(): string
    {
        $length = $this->config->get('refer.code.length', 8);
        $prefix = $this->config->get('refer.code.prefix', '');
        $uppercase = $this->config->get('refer.code.uppercase', true);

        do {
            $code = $prefix . Str::random('alphanumeric', $length);

            if ($uppercase) {
                $code = strtoupper($code);
            }
        } while (ReferralCode::findByCode($code));

        return $code;
    }
}
