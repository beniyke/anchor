<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Scout Manager Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Services;

use App\Models\User;
use Audit\Audit;
use Helpers\Data\Data;
use Helpers\DateTimeHelper;
use Mail\Mail;
use RuntimeException;
use Scout\Models\Application;
use Scout\Models\Candidate;
use Scout\Models\Interview;
use Scout\Models\Job;
use Scout\Models\Stage;
use Scout\Notifications\InterviewReminderNotification;

class ScoutManagerService
{
    /**
     * Create a new job posting.
     */
    public function createJob(array $data): Job
    {
        $job = Job::create($data);

        if (class_exists(Audit::class)) {
            Audit::log('scout.job.created', ['id' => $job->id, 'title' => $job->title], $job);
        }

        return $job;
    }

    public function findOrCreateCandidate(array $data): Candidate
    {
        $candidate = Candidate::where('email', $data['email'])->first();

        if (!$candidate) {
            $candidate = Candidate::create($data);
        }

        return $candidate;
    }

    public function apply(Candidate $candidate, Job $job): Application
    {
        if ($job->status !== 'published') {
            throw new RuntimeException("Cannot apply for a job that is not published.");
        }

        if ($job->expires_at && DateTimeHelper::now()->greaterThan($job->expires_at)) {
            throw new RuntimeException("This job posting has expired.");
        }

        $defaultStage = Stage::where('is_default', true)->first()
            ?? Stage::orderBy('order', 'asc')->first();

        if (!$defaultStage) {
            throw new RuntimeException("No recruitment stages defined.");
        }

        $application = Application::create([
            'scout_job_id' => $job->id,
            'scout_candidate_id' => $candidate->id,
            'scout_stage_id' => $defaultStage->id,
            'status' => 'active',
        ]);

        if (class_exists(Audit::class)) {
            Audit::log('scout.application.submitted', ['id' => $application->id, 'job' => $job->title], $application, $candidate->user_id ? User::find($candidate->user_id) : null);
        }

        return $application;
    }

    /**
     * Generate a unique slug for a model.
     */
    public function generateSlug(string $title, string $model): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $count = $model::where('slug', 'like', "{$slug}%")->count();

        return $count > 0 ? "{$slug}-" . ($count + 1) : $slug;
    }

    public function sendReminders(): int
    {
        $now = DateTimeHelper::now();
        $limit = (clone $now)->addMinutes(30);

        // Find confirmed interviews starting soon that haven't been reminded
        $interviews = Interview::pendingReminders($now, $limit)->get();

        $count = 0;

        foreach ($interviews as $interview) {
            if ($interview->interviewer && $interview->interviewer->email) {
                $payload = Data::make([
                    'interview_id' => $interview->id,
                    'candidate_name' => $interview->application->candidate->name,
                    'interviewer_name' => $interview->interviewer->name,
                    'job_title' => $interview->application->job->title,
                    'date' => $interview->scheduled_at->format('F j, Y'),
                    'time' => $interview->scheduled_at->format('H:i'),
                    'location' => $interview->location,
                    'to_email' => $interview->interviewer->email,
                    'to_name' => $interview->interviewer->name,
                ]);

                Mail::send(new InterviewReminderNotification($payload));

                $interview->update([
                    'reminded_at' => DateTimeHelper::now(),
                ]);

                $count++;
            }
        }

        return $count;
    }

    public function stages(): mixed
    {
        return Stage::orderBy('order', 'asc')->get();
    }

    public function findStage(int|string $idOrSlug): ?Stage
    {
        if (is_numeric($idOrSlug)) {
            return Stage::find((int) $idOrSlug);
        }

        return Stage::findBySlug($idOrSlug);
    }

    /**
     * Create a new recruitment stage.
     */
    public function createStage(array $data): Stage
    {
        if (!isset($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name'], Stage::class);
        }

        return Stage::create($data);
    }

    public function updateStage(Stage $stage, array $data): bool
    {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name'], Stage::class);
        }

        return $stage->update($data);
    }

    public function deleteStage(Stage $stage): bool
    {
        if ($stage->applications()->count() > 0) {
            throw new RuntimeException("Cannot delete stage that has active applications.");
        }

        return $stage->delete();
    }
}
