<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Services;

use Academy\Enums\EnrolmentStatus;
use Academy\Events\EnrolmentCreatedEvent;
use Academy\Exceptions\AcademyException;
use Academy\Exceptions\AccessDeniedException;
use Academy\Models\AcademyEnrolment;
use Academy\Models\AcademyProgram;
use Academy\Models\AcademyWaitlist;
use App\Models\User;
use Blish\Blish;
use Core\Event;
use Database\DB;
use Helpers\DateTimeHelper;
use Refer\Refer;
use Wave\Wave;

class EnrolmentManagerService
{
    public function enrol(int $userId, int|AcademyProgram $program, ?int $paymentPlanId = null, ?string $referralCode = null): AcademyEnrolment
    {
        $program = is_object($program) ? $program : AcademyProgram::find($program);
        if (!$program) {
            throw new AcademyException("Program not found.");
        }

        $user = User::find($userId);
        if (!$user) {
            throw new AccessDeniedException("User not found.");
        }

        // Wave Integration (Subscription Access Gating)
        if (config('academy.integrations.wave', true) && class_exists(Wave::class)) {
            $requiredPlanIds = $program->metadata['required_plan_ids'] ?? null;
            if ($requiredPlanIds) {
                $requiredPlanIds = (array) $requiredPlanIds;

                if (!Wave::subscriptions()->hasActivePlan($user->id, 'user', $requiredPlanIds)) {
                    throw new AccessDeniedException('Active subscription plan required to enrol in this program.');
                }
            }
        }

        return DB::transaction(function () use ($user, $program, $paymentPlanId, $referralCode) {
            $enrolment = AcademyEnrolment::create([
                'user_id' => $user->id,
                'program_id' => $program->id,
                'payment_plan_id' => $paymentPlanId,
                'status' => EnrolmentStatus::PENDING,
                'enrolled_at' => DateTimeHelper::now(),
            ]);

            // Refer Integration (Affiliate Tracking)
            if ($referralCode && config('academy.integrations.refer', true) && class_exists(Refer::class)) {
                Refer::track($referralCode, $user->id);
            }

            // Blish Integration (Newsletter Subscription)
            if (config('academy.integrations.blish', true) && class_exists(Blish::class)) {
                Blish::subscribe($user->email, [
                    'name' => $user->name,
                    'source' => 'academy_enrolment',
                    'program' => $program->title,
                ]);
            }

            $this->generateAdmissionNumber($enrolment->id);

            $enrolment = $enrolment->fresh();

            Event::dispatch(new EnrolmentCreatedEvent($enrolment));

            return $enrolment;
        });
    }

    public function bulkEnrol(array $userIds, int|AcademyProgram $program, ?int $paymentPlanId = null): void
    {
        DB::transaction(function () use ($userIds, $program, $paymentPlanId) {
            foreach ($userIds as $userId) {
                $this->enrol($userId, $program, $paymentPlanId);
            }
        });
    }

    public function activate(AcademyEnrolment $enrolment): bool
    {
        return $enrolment->update(['status' => EnrolmentStatus::ACTIVE]);
    }

    public function isEnrolled(int $userId, int $programId): bool
    {
        return AcademyEnrolment::where('user_id', $userId)
            ->where('program_id', $programId)
            ->whereIn('status', [EnrolmentStatus::ACTIVE, EnrolmentStatus::COMPLETED])
            ->exists();
    }

    public function bulkIssueCredentials(?int $programId = null): int
    {
        $query = AcademyEnrolment::where('status', EnrolmentStatus::COMPLETED)
            ->whereDoesntHave('certificate');

        if ($programId) {
            $query->where('program_id', $programId);
        }

        $enrolments = $query->get();
        $count = 0;

        foreach ($enrolments as $enrolment) {
            resolve(CertificateService::class)->issue($enrolment);
            $count++;
        }

        return $count;
    }

    public function pruneExpiredEnrolments(): int
    {
        return (int) AcademyEnrolment::where('expires_at', '<', DateTimeHelper::now())
            ->where('status', '!=', EnrolmentStatus::COMPLETED)
            ->delete();
    }

    public function pruneExpiredWaitlists(): int
    {
        return (int) AcademyWaitlist::where('expires_at', '<', DateTimeHelper::now())
            ->delete();
    }

    public function extend(AcademyEnrolment $enrolment, int $days): bool
    {
        $currentExpiry = $enrolment->expires_at ?: DateTimeHelper::now();
        $newExpiry = $currentExpiry->addDays($days);

        return $enrolment->update([
            'expires_at' => $newExpiry,
            'status' => EnrolmentStatus::ACTIVE,
        ]);
    }

    public function addToWishlist(int $userId, int $programId): AcademyWaitlist
    {
        return AcademyWaitlist::updateOrCreate(
            ['user_id' => $userId, 'program_id' => $programId],
            ['status' => 'wishlisted']
        );
    }

    public function getWishlist(int $userId): array
    {
        return AcademyWaitlist::where('user_id', $userId)
            ->where('status', 'wishlisted')
            ->join('academy_program', 'academy_waitlist.program_id', '=', 'academy_program.id')
            ->get(['academy_program.*'])
            ->toArray();
    }

    public function generateAdmissionNumber(int $enrolmentId): string
    {
        $enrolment = AcademyEnrolment::find($enrolmentId);
        if (!$enrolment) {
            throw new AcademyException("Enrolment not found.");
        }

        $prefix = config('academy.admissions.prefix', 'ADM-');
        $year = date('Y');
        $sequence = str_pad((string) $enrolment->id, 4, '0', STR_PAD_LEFT);
        $admissionId = "{$prefix}{$year}-{$sequence}";

        $enrolment->update(['admission_id' => $admissionId]);

        return $admissionId;
    }

    public function searchLearners(string $query): array
    {
        return AcademyEnrolment::join('users', 'academy_enrolment.user_id', '=', 'users.id')
            ->where(function ($q) use ($query) {
                $q->where('users.name', 'like', "%{$query}%")
                    ->orWhere('users.email', 'like', "%{$query}%")
                    ->orWhere('academy_enrolment.admission_id', 'like', "%{$query}%");
            })
            ->select(['users.id', 'users.name', 'users.email', 'academy_enrolment.admission_id', 'academy_enrolment.status'])
            ->get()
            ->toArray();
    }

    public function getEnrolmentDetails(int|string $id): array
    {
        $enrolment = $this->resolve($id);

        return [
            'enrolment' => $enrolment,
            'program' => $enrolment->program,
            'user' => User::find($enrolment->user_id),
            'progress' => $enrolment->lessonsProgress()->get(),
            'notes' => $enrolment->notes()->get(),
            'certificate' => $enrolment->certificate,
            'instalments' => $enrolment->instalments()->get(),
            'submissions' => $enrolment->submissions()->get(),
        ];
    }

    protected function resolve(int|string|AcademyEnrolment $enrolment): AcademyEnrolment
    {
        if ($enrolment instanceof AcademyEnrolment) {
            return $enrolment;
        }

        $query = AcademyEnrolment::query();

        if (is_int($enrolment) || is_numeric($enrolment)) {
            $query->where('id', (int) $enrolment);
        } else {
            $query->where('refid', $enrolment);
        }

        $foundEnrolment = $query->first();

        if (!$foundEnrolment) {
            throw new AcademyException("Enrolment not found.");
        }

        return $foundEnrolment;
    }
}
