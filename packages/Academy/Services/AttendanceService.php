<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Services;

use Academy\Models\AcademyAttendance;
use Database\BaseModel;
use Helpers\DateTimeHelper;

class AttendanceService
{
    /**
     * Record attendance for an attendable entity.
     */
    public function record(BaseModel $attendable, int $enrolmentId): AcademyAttendance
    {
        return AcademyAttendance::updateOrCreate(
            [
                'attendable_type' => get_class($attendable),
                'attendable_id' => $attendable->id,
                'enrolment_id' => $enrolmentId,
            ],
            ['joined_at' => DateTimeHelper::now()]
        );
    }

    /**
     * Record leave for an attendable entity.
     */
    public function recordLeave(BaseModel $attendable, int $enrolmentId): bool
    {
        $attendance = AcademyAttendance::where('attendable_type', get_class($attendable))
            ->where('attendable_id', $attendable->id)
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

    public function getAttendance(BaseModel $attendable): array
    {
        return AcademyAttendance::where('attendable_type', get_class($attendable))
            ->where('attendable_id', $attendable->id)
            ->get()
            ->toArray();
    }
}
