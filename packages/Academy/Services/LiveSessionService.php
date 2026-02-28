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

    public function recordAttendance(int $sessionId, int $enrolmentId): AcademyAttendance
    {
        $session = AcademyLiveSession::find($sessionId);

        return (new AttendanceService())->record($session, $enrolmentId);
    }

    public function recordLeave(int $sessionId, int $enrolmentId): bool
    {
        $session = AcademyLiveSession::find($sessionId);

        return (new AttendanceService())->recordLeave($session, $enrolmentId);
    }
}
