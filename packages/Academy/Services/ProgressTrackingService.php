<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Services;

use Academy\Events\ProgramCompletedEvent;
use Academy\Models\AcademyEnrolment;
use Academy\Models\AcademyNote;
use Academy\Models\AcademyProgress;
use Core\Event;
use Database\DB;
use Helpers\DateTimeHelper;

class ProgressTrackingService
{
    public function completeLesson(int $enrolmentId, int $lessonId, int $timeSpent = 0): AcademyProgress
    {
        return DB::transaction(function () use ($enrolmentId, $lessonId, $timeSpent) {
            $progress = AcademyProgress::updateOrCreate(
                ['enrolment_id' => $enrolmentId, 'lesson_id' => $lessonId],
                ['completed_at' => DateTimeHelper::now(), 'time_spent' => $timeSpent]
            );

            $this->updateOverallProgress($enrolmentId);

            return $progress;
        });
    }

    public function updateOverallProgress(int $enrolmentId): void
    {
        $enrolment = AcademyEnrolment::find($enrolmentId);
        if (!$enrolment || !$enrolment->program_id) {
            return;
        }

        // Get total lessons across all modules of the program
        $totalLessons = DB::table('academy_lesson')
            ->join('academy_module', 'academy_lesson.module_id', '=', 'academy_module.id')
            ->where('academy_module.program_id', $enrolment->program_id)
            ->count();

        $completedCount = AcademyProgress::where('enrolment_id', $enrolmentId)->count();

        $percentage = $totalLessons > 0 ? (int) (($completedCount / $totalLessons) * 100) : 0;

        $enrolment->update(['progress_percent' => $percentage]);

        if ($percentage === 100) {
            Event::dispatch(new ProgramCompletedEvent($enrolment));
        }
    }

    public function saveNote(int $enrolmentId, int $lessonId, string $content): AcademyNote
    {
        return AcademyNote::create([
            'enrolment_id' => $enrolmentId,
            'lesson_id' => $lessonId,
            'content' => $content,
        ]);
    }

    public function getNotes(int $enrolmentId, int $lessonId = null): array
    {
        $query = AcademyNote::where('enrolment_id', $enrolmentId);

        if ($lessonId) {
            $query->where('lesson_id', $lessonId);
        }

        return $query->get()->toArray();
    }
}
