<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Services;

use Academy\Enums\PaymentStatus;
use Academy\Models\AcademyEnrolment;
use Academy\Models\AcademyInstalment;
use App\Models\User;
use Database\DB;
use Helpers\DateTimeHelper;
use Pay\DataObjects\PaymentResponse;
use Pay\Pay;
use RuntimeException;

class PaymentManagerService
{
    /**
     * Initialize instalments for an enrolment based on a payment plan.
     */
    public function initializeInstalments(AcademyEnrolment $enrolment): void
    {
        $plan = $enrolment->paymentPlan;

        if (!$plan || $plan->type->value === 'free') {
            return;
        }

        DB::transaction(function () use ($enrolment, $plan) {
            $amountPerInstalment = (int) ($plan->price / $plan->instalment_count);

            $now = DateTimeHelper::now();
            for ($i = 1; $i <= $plan->instalment_count; $i++) {
                AcademyInstalment::create([
                    'enrolment_id' => $enrolment->id,
                    'amount' => $amountPerInstalment,
                    'sequence' => $i,
                    'due_at' => $i === 1 ? $now : (clone $now)->addDays(($i - 1) * $plan->instalment_interval),
                    'status' => PaymentStatus::PENDING,
                ]);
            }
        });
    }

    public function initializePayment(AcademyInstalment $instalment, string $driver = null): PaymentResponse
    {
        $enrolment = $instalment->enrolment;
        $user = $enrolment->user;

        $builder = Pay::amount($instalment->amount)
            ->email($user->email)
            ->reference('acad_' . $instalment->id . '_' . uniqid())
            ->metadata([
                'instalment_id' => $instalment->id,
                'enrolment_id' => $enrolment->id,
                'type' => 'instalment',
            ]);

        if ($driver === 'wallet' && config('academy.integrations.wallet', true)) {
            $builder->driver('wallet')->metadata(['wallet_id' => $user->wallet->id]);
        } elseif ($driver) {
            $builder->driver($driver);
        }

        return $builder->initialize();
    }

    /**
     * Deposit funds to user wallet for future learning.
     */
    public function depositToWallet(User $user, int $amount, string $driver = null): PaymentResponse
    {
        if (!config('academy.integrations.wallet', true)) {
            throw new RuntimeException('Wallet integration is disabled.');
        }

        return Pay::amount($amount)
            ->email($user->email)
            ->metadata([
                'user_id' => $user->id,
                'wallet_id' => $user->wallet->id,
                'intention' => 'fund',
                'source' => 'academy',
            ])
            ->initialize();
    }

    public function processPayment(AcademyEnrolment $enrolment, string $reference, int $amount): bool
    {
        // Find the earliest pending instalment
        $instalment = AcademyInstalment::where('enrolment_id', $enrolment->id)
            ->where('status', PaymentStatus::PENDING)
            ->orderBy('sequence', 'asc')
            ->first();

        if ($instalment) {
            return $instalment->update([
                'status' => PaymentStatus::PAID,
                'paid_at' => DateTimeHelper::now(),
                'payment_reference' => $reference,
            ]);
        }

        return false;
    }

    /**
     * Synchronize Academy payments from external providers.
     */
    public function syncExternalPayments(): int
    {
        $instalments = AcademyInstalment::where('status', PaymentStatus::PENDING)
            ->whereNotNull('payment_reference')
            ->get();

        $count = 0;
        foreach ($instalments as $instalment) {
            $response = Pay::verify($instalment->payment_reference);
            if ($response->isSuccessful()) {
                $instalment->update([
                    'status' => PaymentStatus::PAID,
                    'paid_at' => DateTimeHelper::now(),
                ]);
                $count++;
            }
        }

        return $count;
    }

    public function getDefaulters(?int $programId = null): array
    {
        $query = AcademyInstalment::where('status', PaymentStatus::PENDING)
            ->where('due_at', '<', DateTimeHelper::now())
            ->join('academy_enrolment', 'academy_instalment.enrolment_id', '=', 'academy_enrolment.id')
            ->join('users', 'academy_enrolment.user_id', '=', 'users.id');

        if ($programId) {
            $query->where('academy_enrolment.program_id', $programId);
        }

        return $query->get(['users.id', 'users.name', 'users.email', 'academy_instalment.amount', 'academy_instalment.due_at'])
            ->toArray();
    }

    public function getOutstandingBalance(int $enrolmentId): int
    {
        return (int) AcademyInstalment::where('enrolment_id', $enrolmentId)
            ->where('status', PaymentStatus::PENDING)
            ->sum('amount');
    }

    public function getBalance(int $enrolmentId): int
    {
        return $this->getOutstandingBalance($enrolmentId);
    }

    public function getOverdue(int $enrolmentId): array
    {
        return AcademyInstalment::where('enrolment_id', $enrolmentId)
            ->where('status', PaymentStatus::PENDING)
            ->where('due_at', '<', DateTimeHelper::now())
            ->get()
            ->toArray();
    }
}
