<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Services;

use Academy\Enums\SubmissionStatus;
use Academy\Models\AcademyAnswer;
use Academy\Models\AcademyAssessment;
use Academy\Models\AcademyChoice;
use Academy\Models\AcademyGrade;
use Academy\Models\AcademyQuestion;
use Academy\Models\AcademySubmission;
use Database\DB;
use Helpers\DateTimeHelper;
use RuntimeException;

class AssessmentService
{
    public function startAttempt(int $assessmentId, int $enrolmentId): AcademySubmission
    {
        $assessment = AcademyAssessment::find($assessmentId);

        if (!$assessment) {
            throw new RuntimeException("Assessment not found.");
        }

        $currentAttempts = AcademySubmission::where('assessment_id', $assessmentId)
            ->where('enrolment_id', $enrolmentId)
            ->count();

        if ($assessment->attempts_allowed > 0 && $currentAttempts >= $assessment->attempts_allowed) {
            throw new RuntimeException("Maximum attempts reached for this assessment.");
        }

        return AcademySubmission::create([
            'assessment_id' => $assessmentId,
            'enrolment_id' => $enrolmentId,
            'status' => SubmissionStatus::PENDING,
            'attempt_number' => $currentAttempts + 1,
        ]);
    }

    public function addQuestion(int $assessmentId, array $data): AcademyQuestion
    {
        return AcademyQuestion::create(array_merge($data, [
            'assessment_id' => $assessmentId,
        ]));
    }

    public function addChoice(int $questionId, string $text, bool $isCorrect = false): AcademyChoice
    {
        return AcademyChoice::create([
            'question_id' => $questionId,
            'text' => $text,
            'is_correct' => $isCorrect,
        ]);
    }

    public function bulkAddQuestions(int $assessmentId, array $questions): void
    {
        DB::transaction(function () use ($assessmentId, $questions) {
            foreach ($questions as $q) {
                $choices = $q['choices'] ?? [];
                unset($q['choices']);

                $question = $this->addQuestion($assessmentId, $q);

                foreach ($choices as $choice) {
                    $this->addChoice($question->id, $choice['text'], $choice['is_correct'] ?? false);
                }
            }
        });
    }

    public function submit(AcademySubmission $submission, array $answers): void
    {
        DB::transaction(function () use ($submission, $answers) {
            foreach ($answers as $questionId => $answerData) {
                AcademyAnswer::create([
                    'submission_id' => $submission->id,
                    'question_id' => $questionId,
                    'choice_id' => $answerData['choice_id'] ?? null,
                    'content' => $answerData['content'] ?? null,
                    'file_path' => $answerData['file_path'] ?? null,
                ]);
            }

            $submission->update([
                'status' => SubmissionStatus::GRADED,
                'submitted_at' => DateTimeHelper::now(),
            ]);

            $this->autoGrade($submission);
        });
    }

    public function autoGrade(AcademySubmission $submission): void
    {
        $assessment = $submission->assessment;
        $totalPoints = AcademyQuestion::where('assessment_id', $assessment->id)->sum('points');
        $earnedPoints = 0;

        foreach ($submission->answers as $answer) {
            $choice = AcademyChoice::find($answer->choice_id);
            if ($choice && $choice->is_correct) {
                $question = AcademyQuestion::find($answer->question_id);
                $earnedPoints += $question ? $question->points : 0;
            }
        }

        $percent = $totalPoints > 0 ? (int) (($earnedPoints / $totalPoints) * 100) : 0;

        AcademyGrade::create([
            'submission_id' => $submission->id,
            'raw_score' => $earnedPoints,
            'percent_score' => $percent,
            'is_passing' => $percent >= $assessment->passing_score,
            'graded_at' => DateTimeHelper::now(),
        ]);

        if ($this->isLate($submission)) {
            $this->applyLatePenalty($submission, $submission->grade);
        }
    }

    public function isLate(AcademySubmission $submission): bool
    {
        $deadline = $submission->extended_until ?: $submission->due_at;

        if (!$deadline) {
            return false;
        }

        return $submission->submitted_at > $deadline;
    }

    public function applyLatePenalty(AcademySubmission $submission, AcademyGrade $grade): void
    {
        $policy = $submission->assessment->late_policy;
        if (!$policy || !isset($policy['deduction_percent'])) {
            return;
        }

        $deduction = (int) ($grade->percent_score * ($policy['deduction_percent'] / 100));
        $newPercent = max(0, $grade->percent_score - $deduction);

        $grade->update([
            'percent_score' => $newPercent,
            'is_passing' => $newPercent >= $submission->assessment->passing_score,
            'metadata' => array_merge($grade->metadata ?? [], ['late_penalty' => $deduction]),
        ]);
    }

    public function grantExtension(AcademySubmission $submission, string $until): bool
    {
        return $submission->update([
            'extended_until' => DateTimeHelper::parse($until),
        ]);
    }

    public function manualGrade(AcademySubmission $submission, int $rawScore, ?string $feedback = null): AcademyGrade
    {
        $assessment = $submission->assessment;
        $totalPoints = AcademyQuestion::where('assessment_id', $assessment->id)->sum('points');
        $percent = $totalPoints > 0 ? (int) (($rawScore / $totalPoints) * 100) : 0;

        return DB::transaction(function () use ($submission, $rawScore, $percent, $assessment, $feedback) {
            $submission->update(['status' => SubmissionStatus::GRADED]);

            $grade = AcademyGrade::updateOrCreate(
                ['submission_id' => $submission->id],
                [
                    'raw_score' => $rawScore,
                    'percent_score' => $percent,
                    'is_passing' => $percent >= $assessment->passing_score,
                    'graded_at' => DateTimeHelper::now(),
                    'metadata' => array_merge($submission->grade->metadata ?? [], ['manual_feedback' => $feedback]),
                ]
            );

            if ($this->isLate($submission)) {
                $this->applyLatePenalty($submission, $grade);
            }

            return $grade;
        });
    }

    public function canGrade(int $userId, int $submissionId): bool
    {
        $submission = AcademySubmission::with('assessment')->find($submissionId);
        if (!$submission) {
            return false;
        }

        $programId = DB::table('academy_assessment')
            ->where('id', $submission->assessment_id)
            ->value('program_id');

        if (!$programId) {
            $lessonId = DB::table('academy_assessment')->where('id', $submission->assessment_id)->value('lesson_id');
            if ($lessonId) {
                $moduleId = DB::table('academy_lesson')->where('id', $lessonId)->value('module_id');
                $programId = DB::table('academy_module')->where('id', $moduleId)->value('program_id');
            } else {
                $moduleId = DB::table('academy_assessment')->where('id', $submission->assessment_id)->value('module_id');
                $programId = DB::table('academy_module')->where('id', $moduleId)->value('program_id');
            }
        }

        return resolve(ProgramManagerService::class)->canManage($userId, (int) $programId);
    }

    public function canTake(int $userId, int $assessmentId): bool
    {
        $assessment = AcademyAssessment::find($assessmentId);

        if (!$assessment) {
            return false;
        }

        $lessonId = $assessment->lesson_id;

        if ($lessonId) {
            return resolve(ProgramManagerService::class)->canAccess($userId, (int) $lessonId);
        }

        $moduleId = $assessment->module_id;

        if ($moduleId) {
            $programId = DB::table('academy_module')->where('id', $moduleId)->value('program_id');

            return resolve(ProgramManagerService::class)->canView($userId, (int) $programId);
        }

        return false;
    }
}
