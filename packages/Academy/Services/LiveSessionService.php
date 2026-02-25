<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Services;

use Academy\Enums\SessionStatus;
use Academy\Models\AcademyAttendance;
use Academy\Models\AcademyLiveSession;
use Helpers\DateTimeHelper;

class LiveSessionService
{
    public function schedule(array $data): AcademyLiveSession
    {
        return AcademyLiveSession::create($data);
    }

    public function updateStatus(AcademyLiveSession $session, SessionStatus $status): bool
    {
        return $session->update(['status' => $status]);
    }

    /**
     * Record attendance.
     */
    public function recordAttendance(int $sessionId, int $enrolmentId): AcademyAttendance
    {
        return AcademyAttendance::updateOrCreate(
            ['live_session_id' => $sessionId, 'enrolment_id' => $enrolmentId],
            ['joined_at' => DateTimeHelper::now()]
        );
    }

    /**
     * Update attendance leave time.
     */
    public function recordLeave(int $sessionId, int $enrolmentId): bool
    {
        $attendance = AcademyAttendance::where('live_session_id', $sessionId)
            ->where('enrolment_id', $enrolmentId)
            ->first();

        if ($attendance) {
            $leftAt = DateTimeHelper::now();
            $duration = (int) $leftAt->diffInMinutes($attendance->joined_at);

            return $attendance->update([
                'left_at' => $leftAt,
                'duration' => $duration,
            ]);
        }

        return false;
    }
}
