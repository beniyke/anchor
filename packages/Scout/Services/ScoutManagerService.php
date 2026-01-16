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
use Helpers\DateTimeHelper;
use RuntimeException;
use Scout\Models\Application;
use Scout\Models\Candidate;
use Scout\Models\Job;
use Scout\Models\Stage;

class ScoutManagerService
{
    /**
     * Create a new job posting.
     */
    public function createJob(array $data): Job
    {
        $job = Job::create($data);

        if (class_exists('Audit\Audit')) {
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

        if (class_exists('Audit\Audit')) {
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
}
