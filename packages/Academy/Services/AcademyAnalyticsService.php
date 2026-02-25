<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Services;

use Academy\Enums\EnrolmentStatus;
use Academy\Models\AcademyAssessment;
use Academy\Models\AcademyEnrolment;
use Academy\Models\AcademyGrade;
use Academy\Models\AcademyInstalment;
use Academy\Models\AcademyLesson;
use Academy\Models\AcademyModule;
use Academy\Models\AcademyProgramMember;
use Academy\Models\AcademyRating;
use Academy\Models\AcademySubmission;
use App\Models\User;
use Database\DB;
use Helpers\Array\Collections;
use Helpers\DateTimeHelper;

class AcademyAnalyticsService
{
    public function getProgramMetrics(int $programId): array
    {
        return [
            'total_enrolments' => AcademyEnrolment::where('program_id', $programId)->count(),
            'completion_rate' => $this->getCompletionRate($programId),
            'average_progress' => (int) AcademyEnrolment::where('program_id', $programId)->avg('progress_percent'),
            'average_rating' => $this->getAverageRating($programId),
            'active_students' => AcademyEnrolment::where('program_id', $programId)->where('status', EnrolmentStatus::ACTIVE)->count(),
        ];
    }

    protected function getAverageRating(int $programId): float
    {
        return (float) DB::table(AcademyRating::TABLE)->where('program_id', $programId)->avg('rating') ?: 0;
    }

    protected function getCompletionRate(int $programId): int
    {
        $total = AcademyEnrolment::where('program_id', $programId)->count();
        if ($total === 0) {
            return 0;
        }

        $completed = AcademyEnrolment::where('program_id', $programId)->where('status', EnrolmentStatus::COMPLETED)->count();

        return (int) (($completed / $total) * 100);
    }

    public function getRevenueInsights(int $programId): array
    {
        // Sum of paid instalments
        $revenue = DB::table(AcademyInstalment::TABLE)
            ->join(AcademyEnrolment::TABLE, AcademyInstalment::TABLE . '.enrolment_id', '=', AcademyEnrolment::TABLE . '.id')
            ->where(AcademyEnrolment::TABLE . '.program_id', $programId)
            ->where(AcademyInstalment::TABLE . '.status', 'paid')
            ->sum('amount');

        return [
            'total_revenue' => (int) $revenue,
            'currency' => 'USD', // Should come from config
        ];
    }

    /**
     * Get historical trend data for charting.
     */
    public function getHistory(string $metric, string|array $range = '30d', array $filters = []): array
    {
        [$start, $end] = $this->parseRange($range);
        $labels = [];
        $values = [];

        // Pre-fill labels and values
        $period = DateTimeHelper::parse($start)->daysUntil($end);
        foreach ($period as $date) {
            $labels[] = $date->format('M d');
            $values[$date->format('Y-m-d')] = 0;
        }

        $startStr = $start->format('Y-m-d H:i:s');
        $endStr = $end->format('Y-m-d H:i:s');

        $queryData = match ($metric) {
            'enrolments' => $this->queryEnrolmentTrends($startStr, $endStr, $filters),
            'revenue' => $this->queryRevenueTrends($startStr, $endStr, $filters),
            'submissions' => $this->querySubmissionTrends($startStr, $endStr, $filters),
            default => [],
        };

        foreach ($queryData as $date => $val) {
            if (isset($values[$date])) {
                $values[$date] = (int) $val;
            }
        }

        return [
            'labels' => $labels,
            'values' => array_values($values),
        ];
    }

    protected function queryEnrolmentTrends(string $start, string $end, array $filters): array
    {
        $query = DB::table(AcademyEnrolment::TABLE)
            ->whereBetween('enrolled_at', [$start, $end]);

        if (isset($filters['program_id'])) {
            $query->where('program_id', $filters['program_id']);
        }

        return Collections::make($query->selectRaw('DATE(enrolled_at) as date, count(*) as count')
            ->groupBy('date')
            ->get())
            ->build('date', 'count')
            ->get();
    }

    protected function queryRevenueTrends(string $start, string $end, array $filters): array
    {
        $query = DB::table(AcademyInstalment::TABLE)
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$start, $end]);

        if (isset($filters['program_id'])) {
            $query->join(AcademyEnrolment::TABLE, AcademyInstalment::TABLE . '.enrolment_id', '=', AcademyEnrolment::TABLE . '.id')
                ->where(AcademyEnrolment::TABLE . '.program_id', $filters['program_id']);
        }

        return Collections::make($query->selectRaw('DATE(paid_at) as date, sum(amount) as total')
            ->groupBy('date')
            ->get())
            ->build('date', 'total')
            ->get();
    }

    protected function querySubmissionTrends(string $start, string $end, array $filters): array
    {
        $query = DB::table(AcademySubmission::TABLE)
            ->whereBetween('submitted_at', [$start, $end]);

        if (isset($filters['program_id'])) {
            $query->join(AcademyAssessment::TABLE, AcademySubmission::TABLE . '.assessment_id', '=', AcademyAssessment::TABLE . '.id')
                ->join(AcademyLesson::TABLE, AcademyAssessment::TABLE . '.lesson_id', '=', AcademyLesson::TABLE . '.id')
                ->join(AcademyModule::TABLE, AcademyLesson::TABLE . '.module_id', '=', AcademyModule::TABLE . '.id')
                ->where(AcademyModule::TABLE . '.program_id', $filters['program_id']);
        }

        return Collections::make($query->selectRaw('DATE(submitted_at) as date, count(*) as count')
            ->groupBy('date')
            ->get())
            ->build('date', 'count')
            ->get();
    }

    public function getLearnerPerformance(int $enrolmentId): array
    {
        $data = DB::table(AcademyGrade::TABLE)
            ->join(AcademySubmission::TABLE, AcademyGrade::TABLE . '.submission_id', '=', AcademySubmission::TABLE . '.id')
            ->where(AcademySubmission::TABLE . '.enrolment_id', $enrolmentId)
            ->orderBy('graded_at', 'asc')
            ->get(['percent_score', 'graded_at']);

        return [
            'labels' => Collections::make($data)->pluck('graded_at')->map(fn ($d) => substr((string) $d, 5, 5))->get(),
            'values' => Collections::make($data)->pluck('percent_score')->get(),
        ];
    }

    public function getInstructorDashboard(int $instructorId): array
    {
        $programIds = DB::table(AcademyProgramMember::TABLE)
            ->where('user_id', $instructorId)
            ->where('role', 'instructor')
            ->pluck('program_id');

        if (empty($programIds)) {
            return [];
        }

        return [
            'total_students' => AcademyEnrolment::whereIn('program_id', $programIds)->count(),
            'average_completion' => $this->getAverageCompletionForPrograms($programIds),
            'revenue_trends' => $this->getHistory('revenue', '30d', ['program_ids' => $programIds]),
            'student_trends' => $this->getHistory('enrolments', '30d', ['program_ids' => $programIds]),
        ];
    }

    protected function getAverageCompletionForPrograms(array $ids): int
    {
        $rates = array_map(fn ($id) => $this->getCompletionRate($id), $ids);

        return count($rates) > 0 ? (int) (array_sum($rates) / count($rates)) : 0;
    }

    public function getLeaderboard(int $programId, int $limit = 10): array
    {
        return DB::table(AcademyEnrolment::TABLE)
            ->join(User::TABLE, AcademyEnrolment::TABLE . '.user_id', '=', User::TABLE . '.id')
            ->where(AcademyEnrolment::TABLE . '.program_id', $programId)
            ->orderBy(AcademyEnrolment::TABLE . '.progress_percent', 'desc')
            ->orderBy(AcademyEnrolment::TABLE . '.completed_at', 'asc')
            ->limit($limit)
            ->get([User::TABLE . '.id', User::TABLE . '.name', AcademyEnrolment::TABLE . '.progress_percent', AcademyEnrolment::TABLE . '.completed_at']);
    }

    public function getRatingSummary(int $programId): array
    {
        $ratings = Collections::make(DB::table(AcademyRating::TABLE)
            ->where('program_id', $programId)
            ->selectRaw('rating, count(*) as count')
            ->groupBy('rating')
            ->get())
            ->build('rating', 'count')
            ->get();

        $summary = [];
        for ($i = 5; $i >= 1; $i--) {
            $summary[$i] = $ratings[$i] ?? 0;
        }

        return [
            'average' => $this->getAverageRating($programId),
            'total' => array_sum($summary),
            'breakdown' => $summary,
        ];
    }

    public function getAssessmentLeaderboard(int $assessmentId, int $limit = 10): array
    {
        return DB::table(AcademySubmission::TABLE)
            ->join(AcademyEnrolment::TABLE, AcademySubmission::TABLE . '.enrolment_id', '=', AcademyEnrolment::TABLE . '.id')
            ->join(User::TABLE, AcademyEnrolment::TABLE . '.user_id', '=', User::TABLE . '.id')
            ->join(AcademyGrade::TABLE, AcademySubmission::TABLE . '.id', '=', AcademyGrade::TABLE . '.submission_id')
            ->where(AcademySubmission::TABLE . '.assessment_id', $assessmentId)
            ->where(AcademySubmission::TABLE . '.status', 'graded')
            ->orderBy(AcademyGrade::TABLE . '.percent_score', 'desc')
            ->limit($limit)
            ->get([User::TABLE . '.id', User::TABLE . '.name', AcademyGrade::TABLE . '.percent_score', AcademyGrade::TABLE . '.graded_at']);
    }

    private function parseRange(string|array $range): array
    {
        if (is_array($range)) {
            return [DateTimeHelper::parse($range[0]), DateTimeHelper::parse($range[1])];
        }

        $end = DateTimeHelper::now()->endOfDay();
        $start = match ($range) {
            '7d' => DateTimeHelper::now()->subDays(7)->startOfDay(),
            '30d' => DateTimeHelper::now()->subDays(30)->startOfDay(),
            '90d' => DateTimeHelper::now()->subDays(90)->startOfDay(),
            default => DateTimeHelper::now()->subDays(30)->startOfDay(),
        };

        return [$start, $end];
    }
}
