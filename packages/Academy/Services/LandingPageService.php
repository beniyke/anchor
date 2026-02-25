<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Services;

use Academy\Exceptions\AcademyException;
use Academy\Models\AcademyProgram;
use Rank\Rank;

class LandingPageService
{
    public function getPageData(string $slug): array
    {
        $program = AcademyProgram::where('slug', $slug)
            ->where('status', 'published')
            ->with(['modules.lessons', 'paymentPlans'])
            ->first();

        if (!$program) {
            throw new AcademyException("Program not found.");
        }

        // SEO Integration (Rank)
        if (config('academy.integrations.rank', true) && class_exists(Rank::class)) {
            Rank::setTitle($program->title);
            Rank::setDescription($program->description ?? '');
            if ($program->thumbnail) {
                Rank::setImage($program->thumbnail);
            }
        }

        return [
            'program' => $program,
            'instructors' => $program->staff()->where('role', 'instructor')->get(),
            'modules_count' => $program->modules->count(),
            'lessons_count' => $program->modules->sum(function ($m) {
                return $m->lessons->count();
            }),
        ];
    }
}
