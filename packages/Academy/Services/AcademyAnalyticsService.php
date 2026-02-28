<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Services;

use Academy\Enums\EnrolmentStatus;
use Academy\Models\AcademyAnswer;
use Academy\Models\AcademyAssessment;
use Academy\Models\AcademyAttendance;
use Academy\Models\AcademyChoice;
use Academy\Models\AcademyEnrolment;
use Academy\Models\AcademyGrade;
use Academy\Models\AcademyInstalment;
use Academy\Models\AcademyLesson;
use Academy\Models\AcademyLiveSession;
use Academy\Models\AcademyModule;
use Academy\Models\AcademyProgramMember;
use Academy\Models\AcademyProgress;
use Academy\Models\AcademyQuestion;
use Academy\Models\AcademyRating;
use Academy\Models\AcademySubmission;
use App\Models\User;
use Database\BaseModel;
use Database\DB;
use Helpers\Array\Collections;
use Helpers\DateTimeHelper;

class AcademyAnalyticsService
{
    public function getProgramMetrics(int $programId): array
    {
        $enrolments = AcademyEnrolment::with('user')
            ->where('program_id', $programId)
            ->whereIn('status', [EnrolmentStatus::ACTIVE, EnrolmentStatus::SUSPENDED])
            ->get();

        return [
            'total_enrolments' => AcademyEnrolment::where('program_id', $programId)->count(),
            'completion_rate' => $this->getCompletionRate($programId),
            'average_progress' => (int) AcademyEnrolment::where('program_id', $programId)->avg('progress_percent'),
            'average_rating' => $this->getAverageRating($programId),
            'active_students' => $enrolments->count(),
            'active_enrolments' => $enrolments
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
            ->get([User::TABLE . '.id', User::TABLE . '.refid', User::TABLE . '.name', AcademyEnrolment::TABLE . '.progress_percent', AcademyEnrolment::TABLE . '.completed_at']);
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
            ->get([User::TABLE . '.id', User::TABLE . '.refid', User::TABLE . '.name', AcademyGrade::TABLE . '.percent_score', AcademyGrade::TABLE . '.graded_at']);
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

    /**
     * Retrieves a list of users currently active in a session (left_at is null).
     */
    public function getActiveAttendees(BaseModel $attendable): array
    {
        $results = DB::table(AcademyAttendance::TABLE)
            ->join(AcademyEnrolment::TABLE, AcademyAttendance::TABLE . '.enrolment_id', '=', AcademyEnrolment::TABLE . '.id')
            ->join(User::TABLE, AcademyEnrolment::TABLE . '.user_id', '=', User::TABLE . '.id')
            ->where(AcademyAttendance::TABLE . '.attendable_type', get_class($attendable))
            ->where(AcademyAttendance::TABLE . '.attendable_id', $attendable->id)
            ->whereNull(AcademyAttendance::TABLE . '.left_at')
            ->orderBy(AcademyAttendance::TABLE . '.joined_at', 'asc')
            ->get([
                User::TABLE . '.id as user_id',
                User::TABLE . '.name',
                User::TABLE . '.refid',
                AcademyAttendance::TABLE . '.joined_at'
            ]);

        $attendees = [];
        foreach ($results as $row) {
            $attendees[] = (object) $row;
        }

        return $attendees;
    }

    /**
     * Determines which learners met a specific "seat time" requirement (in minutes).
     */
    public function getAttendanceCompliance(BaseModel $attendable, int $requiredMinutes): array
    {
        $requiredSeconds = $requiredMinutes * 60;

        $attendances = DB::table(AcademyAttendance::TABLE)
            ->join(AcademyEnrolment::TABLE, AcademyAttendance::TABLE . '.enrolment_id', '=', AcademyEnrolment::TABLE . '.id')
            ->join(User::TABLE, AcademyEnrolment::TABLE . '.user_id', '=', User::TABLE . '.id')
            ->where(AcademyAttendance::TABLE . '.attendable_type', get_class($attendable))
            ->where(AcademyAttendance::TABLE . '.attendable_id', $attendable->id)
            ->get([
                User::TABLE . '.id as user_id',
                User::TABLE . '.name',
                User::TABLE . '.refid',
                AcademyAttendance::TABLE . '.duration'
            ]);

        $compliant = [];
        $nonCompliant = [];

        foreach ($attendances as $record) {
            $duration = (int) $record['duration'];
            $data = [
                'user_id' => $record['user_id'],
                'refid' => $record['refid'],
                'name' => $record['name'],
                'duration_seconds' => $duration,
                'duration_minutes' => round($duration / 60, 2),
            ];

            if ($duration >= $requiredSeconds) {
                $compliant[] = $data;
            } else {
                $nonCompliant[] = $data;
            }
        }

        return [
            'compliant' => $compliant,
            'non_compliant' => $nonCompliant,
            'summary' => [
                'total_compliant' => count($compliant),
                'total_non_compliant' => count($nonCompliant),
                'required_minutes' => $requiredMinutes,
            ]
        ];
    }

    /**
     * Generates chart-ready data showing learner drop-off over the lifespan of a session.
     */
    public function getDropOffTrend(BaseModel $attendable, int $intervalMinutes = 5): array
    {
        $attendances = DB::table(AcademyAttendance::TABLE)
            ->where('attendable_type', get_class($attendable))
            ->where('attendable_id', $attendable->id)
            ->whereNotNull('joined_at')
            ->orderBy('joined_at', 'asc')
            ->get(['joined_at', 'left_at']);

        if (empty($attendances)) {
            return ['labels' => [], 'values' => []];
        }

        $sessionStart = DateTimeHelper::parse($attendances[0]['joined_at']);

        $latestLeftAt = null;
        foreach ($attendances as $attendance) {
            if ($attendance['left_at'] && (!$latestLeftAt || $attendance['left_at'] > $latestLeftAt)) {
                $latestLeftAt = $attendance['left_at'];
            }
        }
        $sessionEnd = $latestLeftAt ? DateTimeHelper::parse($latestLeftAt) : DateTimeHelper::now();

        // Total session duration in minutes
        $totalSessionMinutes = $sessionStart->diffInMinutes($sessionEnd);

        // Handle edge case where session just started
        if ($totalSessionMinutes < $intervalMinutes) {
            $totalSessionMinutes = $intervalMinutes;
        }

        $buckets = [];
        $labels = [];
        $numBuckets = (int) ceil($totalSessionMinutes / $intervalMinutes);

        for ($i = 0; $i < $numBuckets; $i++) {
            $startMin = $i * $intervalMinutes;
            $endMin = ($i + 1) * $intervalMinutes;
            $label = "{$startMin}-{$endMin} min";

            $labels[] = $label;
            $buckets[$i] = 0; // Initialize count to 0
        }

        foreach ($attendances as $record) {
            if (!$record['left_at']) {
                continue; // User hasn't dropped off yet
            }

            $leftAtTime = DateTimeHelper::parse($record['left_at']);

            // Difference in minutes from session start to when they left
            $minutesToDropOff = $sessionStart->diffInMinutes($leftAtTime);

            // Determine which bucket index this falls into
            $bucketIndex = (int) floor($minutesToDropOff / $intervalMinutes);

            // Cap the index to the last bucket just in case of slight time overflows
            if ($bucketIndex >= $numBuckets) {
                $bucketIndex = $numBuckets - 1;
            }

            // Increment the drop-off count for this interval
            if (isset($buckets[$bucketIndex])) {
                $buckets[$bucketIndex]++;
            }
        }

        $activeUsers = 0;
        foreach ($attendances as $record) {
            if (!$record['left_at']) {
                $activeUsers++;
            }
        }

        return [
            'labels' => $labels,
            'values' => array_values($buckets),
            'summary' => [
                'total_dropoffs' => array_sum($buckets),
                'active_users' => $activeUsers
            ]
        ];
    }

    /**
     * Retrieves a specific learner's attendance records, optionally filtered by type.
     * If AcademyLiveSession is requested, it includes compliance metrics.
     */
    public function getLearnerAttendanceReport(int $enrolmentId, ?string $attendableType = AcademyLiveSession::class, int $requiredMinutes = 45): array
    {
        $enrolment = AcademyEnrolment::where('id', $enrolmentId)->first();
        if (!$enrolment) {
            return [];
        }

        // Complex compliance logic for Live Sessions
        if ($attendableType === AcademyLiveSession::class) {
            $sessions = DB::table(AcademyLiveSession::TABLE)
                ->select(AcademyLiveSession::TABLE . '.*')
                ->join(AcademyLesson::TABLE, AcademyLiveSession::TABLE . '.lesson_id', '=', AcademyLesson::TABLE . '.id')
                ->join(AcademyModule::TABLE, AcademyLesson::TABLE . '.module_id', '=', AcademyModule::TABLE . '.id')
                ->where(AcademyModule::TABLE . '.program_id', (int) $enrolment->program_id)
                ->get();

            $report = [];
            $totalSessions = count($sessions);
            $attendedCount = 0;
            $compliantCount = 0;
            $requiredSeconds = $requiredMinutes * 60;

            foreach ($sessions as $session) {
                $sessionId = is_object($session) ? $session->id : $session['id'];
                $sessionProvider = is_object($session) ? $session->provider : $session['provider'];
                $sessionStartsAt = is_object($session) ? $session->starts_at : $session['starts_at'];

                $attendance = DB::table(AcademyAttendance::TABLE)
                    ->where('enrolment_id', $enrolmentId)
                    ->where('attendable_type', AcademyLiveSession::class)
                    ->where('attendable_id', $sessionId)
                    ->first();

                $duration = 0;
                if ($attendance) {
                    $duration = is_object($attendance) ? (int) $attendance->duration : (int) $attendance['duration'];
                }

                $isCompliant = $duration >= $requiredSeconds;

                if ($attendance) {
                    $attendedCount++;
                }
                if ($isCompliant) {
                    $compliantCount++;
                }

                $report[] = [
                    'session_id' => $sessionId,
                    'provider' => $sessionProvider,
                    'starts_at' => $sessionStartsAt,
                    'attended' => $attendance !== null,
                    'joined_at' => $attendance ? (is_object($attendance) ? $attendance->joined_at : $attendance['joined_at']) : null,
                    'duration_minutes' => round($duration / 60, 2),
                    'is_compliant' => $isCompliant,
                ];
            }

            $attendanceRate = $totalSessions > 0 ? (int) round(($attendedCount / $totalSessions) * 100) : 0;
            $complianceRate = $totalSessions > 0 ? (int) round(($compliantCount / $totalSessions) * 100) : 0;

            return [
                'sessions' => $report,
                'summary' => [
                    'total_sessions' => $totalSessions,
                    'attended_sessions' => $attendedCount,
                    'compliant_sessions' => $compliantCount,
                    'attendance_rate' => $attendanceRate,
                    'compliance_rate' => $complianceRate,
                ]
            ];
        }

        // Generic logic for all other types or "All"
        $query = DB::table(AcademyAttendance::TABLE)
            ->where('enrolment_id', $enrolmentId);

        if ($attendableType) {
            $query->where('attendable_type', $attendableType);
        }

        $records = $query->get();
        $report = [];

        foreach ($records as $record) {
            $report[] = [
                'id' => is_object($record) ? $record->id : $record['id'],
                'type' => is_object($record) ? $record->attendable_type : $record['attendable_type'],
                'target_id' => is_object($record) ? $record->attendable_id : $record['attendable_id'],
                'joined_at' => is_object($record) ? $record->joined_at : $record['joined_at'],
                'left_at' => is_object($record) ? $record->left_at : $record['left_at'],
                'duration_minutes' => round(((is_object($record) ? (int) $record->duration : (int) $record['duration']) / 60), 2),
            ];
        }

        return [
            'records' => $report,
            'summary' => [
                'total_count' => count($report),
                'total_duration_minutes' => array_sum(array_column($report, 'duration_minutes')),
                'filtered_by' => $attendableType ?? 'all'
            ]
        ];
    }

    /**
     * A master function to fetch all possible metrics about a single learner inside a program.
     */
    public function getLearnerMetrics(int $enrolmentId): array
    {
        return [
            'transcript' => \Academy\Academy::reports()->getTranscript($enrolmentId),
            'progress' => \Academy\Academy::reports()->getProgressReport($enrolmentId),
            'performance_trend' => $this->getLearnerPerformance($enrolmentId),
            'attendance_report' => $this->getLearnerAttendanceReport($enrolmentId),
        ];
    }

    /**
     * A robust "At a Glance" grid of all learners enrolled in a particular program.
     */
    public function getProgramLearnersReport(int $programId): array
    {
        $enrolments = AcademyEnrolment::with('user')->where('program_id', $programId)->get();
        if ($enrolments->isEmpty()) {
            return [];
        }

        $report = [];
        foreach ($enrolments as $enrolment) {
            $attendance = $this->getLearnerAttendanceReport((int) $enrolment->id);

            $avgScore = DB::table(AcademyGrade::TABLE)
                ->join(AcademySubmission::TABLE, AcademyGrade::TABLE . '.submission_id', '=', AcademySubmission::TABLE . '.id')
                ->where(AcademySubmission::TABLE . '.enrolment_id', $enrolment->id)
                ->avg('percent_score');

            $name = $enrolment->user->name ?? User::where('id', $enrolment->user_id)->value('name');

            $report[] = [
                'user_id' => $enrolment->user_id,
                'refid' => $enrolment->user->refid ?? User::where('id', $enrolment->user_id)->value('refid'),
                'name' => $name ?? 'Student (' . $enrolment->user_id . ')',
                'status' => $enrolment->status->value,
                'progress_percent' => $enrolment->progress_percent,
                'average_score' => $avgScore ? (int) $avgScore : 0,
                'attendance_compliance_rate' => $attendance['summary']['compliance_rate'] ?? 0,
            ];
        }

        return $report;
    }

    /**
     * Identifies exactly where learners "give up" or stall in a program.
     */
    public function getProgramBottlenecks(int $programId): array
    {
        $enrolments = DB::table(AcademyEnrolment::TABLE)
            ->where('program_id', $programId)
            ->whereIn('status', ['active', 'suspended'])
            ->get(['id']);

        $enrolmentIds = array_column((array) $enrolments, 'id');

        if (empty($enrolmentIds)) {
            return [];
        }

        $progresses = DB::table(AcademyProgress::TABLE)
            ->whereIn('enrolment_id', $enrolmentIds)
            ->orderBy('completed_at', 'desc')
            ->get(['enrolment_id', 'lesson_id']);

        $latestLessons = [];
        foreach ($progresses as $p) {
            $eId = (int) (is_object($p) ? $p->enrolment_id : $p['enrolment_id']);
            if (!isset($latestLessons[$eId])) {
                $latestLessons[$eId] = (int) (is_object($p) ? $p->lesson_id : $p['lesson_id']);
            }
        }

        $bottleneckCounts = array_count_values($latestLessons);
        $lessonIds = array_keys($bottleneckCounts);

        if (empty($lessonIds)) {
            return [];
        }

        $lessons = DB::table(AcademyLesson::TABLE)
            ->whereIn('id', $lessonIds)
            ->get(['id', 'title']);

        $lessonMap = [];
        foreach ($lessons as $l) {
            $lessonMap[(int) (is_object($l) ? $l->id : $l['id'])] = is_object($l) ? $l->title : $l['title'];
        }

        $report = [];
        foreach ($bottleneckCounts as $lessonId => $count) {
            $report[] = [
                'lesson_id' => $lessonId,
                'lesson_title' => $lessonMap[$lessonId] ?? 'Unknown Lesson',
                'stalled_count' => $count,
            ];
        }

        usort($report, fn ($a, $b) => $b['stalled_count'] <=> $a['stalled_count']);

        return $report;
    }

    /**
     * Alerts instructors to students who are likely to fail or churn.
     */
    public function getAtRiskLearners(int $programId, int $daysInactive = 14, int $maxProgress = 20): array
    {
        $cutoffDate = DateTimeHelper::now()->subDays($daysInactive)->format('Y-m-d H:i:s');

        $enrolments = AcademyEnrolment::with('user')
            ->where('program_id', $programId)
            ->where('status', 'active')
            ->where('progress_percent', '<=', $maxProgress)
            ->get();

        $atRisk = [];

        foreach ($enrolments as $e) {
            if ($e->enrolled_at > DateTimeHelper::now()->subDays(7)) {
                continue;
            }

            $latestProgress = DB::table(AcademyProgress::TABLE)
                ->where('enrolment_id', $e->id)
                ->orderBy('completed_at', 'desc')
                ->first();

            $lastActivity = $latestProgress ? (is_object($latestProgress) ? $latestProgress->completed_at : $latestProgress['completed_at']) : $e->enrolled_at->format('Y-m-d H:i:s');

            $latestAttendance = DB::table(AcademyAttendance::TABLE)
                ->where('enrolment_id', $e->id)
                ->orderBy('joined_at', 'desc')
                ->first();

            if ($latestAttendance) {
                $attendanceDate = is_object($latestAttendance) ? $latestAttendance->joined_at : $latestAttendance['joined_at'];
                if ($attendanceDate > $lastActivity) {
                    $lastActivity = $attendanceDate;
                }
            }

            if ($lastActivity < $cutoffDate) {
                // Ensure correct day calculation inversion
                $diff = DateTimeHelper::parse($lastActivity)->daysUntil(DateTimeHelper::now());
                $atRisk[] = [
                    'user_id' => $e->user_id,
                    'refid' => $e->user->refid ?? 'N/A',
                    'name' => $e->user->name ?? 'Unknown',
                    'progress_percent' => $e->progress_percent,
                    'last_activity' => $lastActivity,
                    'days_inactive' => count($diff),
                ];
            }
        }

        return $atRisk;
    }

    /**
     * Evaluates the fairness and difficulty of quiz questions by analyzing failure rates.
     */
    public function getAssessmentQuestionMetrics(int $assessmentId): array
    {
        $submissions = DB::table(AcademySubmission::TABLE)
            ->where('assessment_id', $assessmentId)
            ->get(['id']);

        $submissionIds = array_column((array) $submissions, 'id');
        if (empty($submissionIds)) {
            return [];
        }

        $answers = DB::table(AcademyAnswer::TABLE)
            ->whereIn('submission_id', $submissionIds)
            ->get(['question_id', 'choice_id']);

        $choiceIds = array_column((array) $answers, 'choice_id');
        $choices = [];
        if (!empty($choiceIds)) {
            $choices = DB::table(AcademyChoice::TABLE)
                ->whereIn('id', array_filter($choiceIds))
                ->get(['id', 'is_correct']);
        }

        $isCorrectMap = [];
        foreach ($choices as $c) {
            $isCorrectMap[$c['id']] = (bool) $c['is_correct'];
        }

        $stats = [];
        foreach ($answers as $a) {
            $qId = $a['question_id'];
            if (!isset($stats[$qId])) {
                $stats[$qId] = ['total' => 0, 'correct' => 0];
            }
            $stats[$qId]['total']++;

            if (isset($a['choice_id']) && !empty($isCorrectMap[$a['choice_id']])) {
                $stats[$qId]['correct']++;
            }
        }

        $questionIds = array_keys($stats);
        if (empty($questionIds)) {
            return [];
        }

        $questions = DB::table(AcademyQuestion::TABLE)
            ->whereIn('id', $questionIds)
            ->get(['id', 'text', 'type']);

        $report = [];
        foreach ($questions as $q) {
            $qStat = $stats[$q['id']];
            $failureRate = $qStat['total'] > 0 ? (($qStat['total'] - $qStat['correct']) / $qStat['total']) * 100 : 0;

            $report[] = [
                'question_id' => $q['id'],
                'content' => substr(strip_tags((string) ($q['text'] ?? '')), 0, 100) . '...',
                'type' => $q['type'] ?? 'unknown',
                'total_attempts' => $qStat['total'],
                'failure_rate_percent' => round($failureRate, 2),
            ];
        }

        usort($report, fn ($a, $b) => $b['failure_rate_percent'] <=> $a['failure_rate_percent']);

        return $report;
    }

    /**
     * A single 0-100 metric determining how active a learner is based on various engagement parameters.
     */
    public function getLearnerEngagementScore(int $enrolmentId): int
    {
        $enrolment = AcademyEnrolment::find($enrolmentId);
        if (!$enrolment) {
            return 0;
        }

        $progressScore = min(100, $enrolment->progress_percent);

        $avgScore = DB::table(AcademyGrade::TABLE)
            ->join(AcademySubmission::TABLE, AcademyGrade::TABLE . '.submission_id', '=', AcademySubmission::TABLE . '.id')
            ->where(AcademySubmission::TABLE . '.enrolment_id', $enrolmentId)
            ->avg('percent_score');

        $assessmentScore = $avgScore !== null ? min(100, (float) $avgScore) : 50;

        $attendanceData = $this->getLearnerAttendanceReport($enrolmentId);
        $attendanceScore = $attendanceData['summary']['total_sessions'] > 0
            ? $attendanceData['summary']['attendance_rate']
            : 50;

        $engagement = ($progressScore * 0.4) + ($assessmentScore * 0.4) + ($attendanceScore * 0.2);

        return (int) round($engagement);
    }
}
