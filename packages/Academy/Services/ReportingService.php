<?php

declare(strict_types=1);

namespace Academy\Services;

use Academy\Models\AcademyEnrolment;
use Academy\Models\AcademyProgress;
use Academy\Models\AcademySubmission;
use Database\DB;

class ReportingService
{
    public function getTranscript(int $enrolmentId): array
    {
        $enrolment = AcademyEnrolment::with(['user', 'program', 'submissions.grade', 'submissions.assessment'])->find($enrolmentId);

        if (!$enrolment) {
            return [];
        }

        return [
            'learner_id' => $enrolment->user_id,
            'learner_refid' => $enrolment->user->refid ?? null,
            'learner_name' => $enrolment->user->name ?? 'Student',
            'program_title' => $enrolment->program->title,
            'status' => $enrolment->status->value,
            'enrolled_at' => $enrolment->enrolled_at->format('Y-m-d'),
            'completed_at' => $enrolment->completed_at?->format('Y-m-d'),
            'progress' => $enrolment->progress_percent,
            'assessments' => $enrolment->submissions->map(fn ($sub) => [
                'title' => $sub->assessment->metadata['title'] ?? 'Assessment',
                'score' => $sub->grade->percent_score ?? 0,
                'is_passing' => $sub->grade->is_passing ?? false,
                'submitted_at' => $sub->submitted_at?->format('Y-m-d'),
                'is_late' => $sub->submitted_at > ($sub->extended_until ?: $sub->due_at),
            ]),
        ];
    }

    public function getProgressReport(int $enrolmentId): array
    {
        $enrolment = AcademyEnrolment::find($enrolmentId);
        if (!$enrolment) {
            return [];
        }

        $totalLessons = DB::table('academy_lesson')
            ->join('academy_module', 'academy_lesson.module_id', '=', 'academy_module.id')
            ->where('academy_module.program_id', $enrolment->program_id)
            ->count();

        $completedLessons = AcademyProgress::where('enrolment_id', $enrolmentId)
            ->with('lesson')
            ->get();

        return [
            'total_lessons' => $totalLessons,
            'completed_count' => $completedLessons->count(),
            'percentage' => $enrolment->progress_percent,
            'lessons' => $completedLessons->map(fn ($p) => [
                'title' => $p->lesson->title,
                'completed_at' => $p->completed_at->format('Y-m-d H:i'),
                'time_spent' => $p->time_spent,
            ]),
        ];
    }

    public function getLifecycleHistory(int $enrolmentId): array
    {
        $lessons = AcademyProgress::where('enrolment_id', $enrolmentId)
            ->get()
            ->map(fn ($p) => [
                'type' => 'lesson',
                'event' => 'Completed lesson: ' . ($p->lesson->title ?? 'Unknown'),
                'date' => $p->completed_at,
            ]);

        $submissions = AcademySubmission::where('enrolment_id', $enrolmentId)
            ->get()
            ->map(fn ($s) => [
                'type' => 'assessment',
                'event' => 'Submitted assessment for: ' . ($s->assessment->lesson->title ?? 'Unknown'),
                'date' => $s->submitted_at,
                'score' => $s->grade->percent_score ?? null,
            ]);

        return $lessons->merge($submissions)
            ->sortByDesc('date')
            ->values()
            ->all();
    }
}
