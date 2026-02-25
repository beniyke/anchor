<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Services;

use Academy\Enums\EnrolmentStatus;
use Academy\Enums\ProgramStatus;
use Academy\Models\AcademyAnnouncement;
use Academy\Models\AcademyEnrolment;
use Academy\Models\AcademyLesson;
use Academy\Models\AcademyProgram;
use Academy\Models\AcademyProgramMember;
use Academy\Models\AcademyProgress;
use Academy\Models\AcademyResource;
use Database\DB;
use Helpers\DateTimeHelper;
use RuntimeException;

class ProgramManagerService
{
    public function create(array $data): AcademyProgram
    {
        return DB::transaction(function () use ($data) {
            $program = AcademyProgram::create($data);

            // Assign Instructor(s)
            $instructorIds = $data['instructor_ids'] ?? [];
            if (isset($data['instructor_id'])) {
                $instructorIds[] = $data['instructor_id'];
            }

            // Fallback to authenticated user (creator) if no instructors provided
            if (empty($instructorIds) && function_exists('auth')) {
                $user = auth()->user();
                if ($user) {
                    $instructorIds[] = $user->id;
                }
            }

            foreach (array_unique($instructorIds) as $id) {
                $this->addMember($program, (int) $id, 'instructor');
            }

            return $program;
        });
    }

    public function update(int|AcademyProgram $program, array $data): bool
    {
        $program = $this->resolve($program);

        return $program->update($data);
    }

    public function addMember(AcademyProgram $program, int $userId, string $role = 'instructor'): AcademyProgramMember
    {
        return AcademyProgramMember::create([
            'program_id' => $program->id,
            'user_id' => $userId,
            'role' => $role,
        ]);
    }

    public function publish(AcademyProgram $program): bool
    {
        return $program->update(['status' => ProgramStatus::PUBLISHED]);
    }

    public function getMembers(int $programId, ?string $role = null): array
    {
        $query = AcademyProgramMember::where('program_id', $programId)
            ->join('users', 'academy_program_member.user_id', '=', 'users.id');

        if ($role) {
            $query->where('academy_program_member.role', $role);
        }

        return $query->get(['users.id', 'users.name', 'users.email', 'academy_program_member.role'])
            ->toArray();
    }

    public function getResources(?int $programId = null, ?int $lessonId = null): array
    {
        $query = AcademyResource::query();

        if ($lessonId) {
            $query->where('lesson_id', $lessonId);
        } elseif ($programId) {
            $query->join('academy_lesson', 'academy_resource.lesson_id', '=', 'academy_lesson.id')
                ->join('academy_module', 'academy_lesson.module_id', '=', 'academy_module.id')
                ->where('academy_module.program_id', $programId)
                ->select('academy_resource.*');
        }

        return $query->get()->toArray();
    }

    public function getAnnouncements(int $programId): array
    {
        return AcademyAnnouncement::where('program_id', $programId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function search(string $query, array $filters = []): array
    {
        $builder = AcademyProgram::where('status', ProgramStatus::PUBLISHED)
            ->where(function ($q) use ($query) {
                // Search Programs
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");

                // Search Modules & Lessons (via exists)
                $q->whereExists(function ($sub) use ($query) {
                    $sub->from('academy_module')
                        ->whereColumn('academy_module.program_id', '=', 'academy_program.id')
                        ->where(function ($m) use ($query) {
                            $m->where('title', 'like', "%{$query}%")
                                ->orWhere('description', 'like', "%{$query}%");

                            $m->whereExists(function ($l) use ($query) {
                                $l->from('academy_lesson')
                                    ->whereColumn('academy_lesson.module_id', '=', 'academy_module.id')
                                    ->where(function ($ls) use ($query) {
                                        $ls->where('title', 'like', "%{$query}%")
                                            ->orWhere('content', 'like', "%{$query}%");
                                    });
                            }, 'OR');
                        });
                }, 'OR');

                // Search Announcements (via exists)
                $q->whereExists(function ($sub) use ($query) {
                    $sub->from('academy_announcement')
                        ->whereColumn('academy_announcement.program_id', '=', 'academy_program.id')
                        ->where(function ($a) use ($query) {
                            $a->where('title', 'like', "%{$query}%")
                                ->orWhere('content', 'like', "%{$query}%");
                        });
                }, 'OR');
            });

        if (isset($filters['is_featured'])) {
            $builder->where('is_featured', $filters['is_featured']);
        }

        return $builder->get()->toArray();
    }

    public function searchInstructors(string $query): array
    {
        return AcademyProgramMember::where('role', 'instructor')
            ->join('users', 'academy_program_member.user_id', '=', 'users.id')
            ->where(function ($q) use ($query) {
                $q->where('users.name', 'like', "%{$query}%")
                    ->orWhere('users.email', 'like', "%{$query}%");
            })
            ->select(['users.id', 'users.name', 'users.email'])
            ->distinct()
            ->get()
            ->toArray();
    }

    public function getDripContent(int $programId, int $enrolmentId): array
    {
        // In a real system, this would check 'drip_delay' in module/lesson metadata
        return AcademyProgram::find($programId)
            ->modules()
            ->with(['lessons'])
            ->get()
            ->toArray();
    }

    public function canAccess(int $userId, int $lessonId): bool
    {
        $lesson = AcademyLesson::find($lessonId);
        if (!$lesson) {
            return false;
        }

        $module = $lesson->module;
        if (!$module) {
            return false;
        }

        if ($this->canManage($userId, $module->program_id)) {
            return true;
        }

        $allowedUserIds = $lesson->metadata['allowed_user_ids'] ?? null;
        if ($allowedUserIds && is_array($allowedUserIds)) {
            if (!in_array($userId, $allowedUserIds)) {
                return false;
            }
        }

        $enrolment = AcademyEnrolment::where('user_id', $userId)
            ->where('program_id', $module->program_id)
            ->whereIn('status', [EnrolmentStatus::ACTIVE, EnrolmentStatus::COMPLETED])
            ->first();

        if (!$enrolment) {
            return false;
        }

        $dripDelay = $module->metadata['drip_delay'] ?? 0;
        if ($dripDelay > 0) {
            $enrolledAt = DateTimeHelper::parse($enrolment->enrolled_at);
            if ($enrolledAt->addDays((int) $dripDelay)->isFuture()) {
                return false;
            }
        }

        $prerequisiteId = $lesson->metadata['prerequisite_lesson_id'] ?? null;
        if ($prerequisiteId) {
            $isCompleted = AcademyProgress::where('enrolment_id', $enrolment->id)
                ->where('lesson_id', $prerequisiteId)
                ->where('status', 'completed')
                ->exists();

            if (!$isCompleted) {
                return false;
            }
        }

        return true;
    }

    public function canManage(int $userId, int $programId): bool
    {
        return AcademyProgramMember::where('program_id', $programId)
            ->where('user_id', $userId)
            ->where('role', 'instructor')
            ->exists();
    }

    public function canView(int $userId, int $programId): bool
    {
        if ($this->canManage($userId, $programId)) {
            return true;
        }

        return AcademyEnrolment::where('user_id', $userId)
            ->where('program_id', $programId)
            ->whereIn('status', [EnrolmentStatus::ACTIVE, EnrolmentStatus::COMPLETED])
            ->exists();
    }

    public function getProgramDetails(int|string $id): array
    {
        $program = $this->resolve($id);

        return [
            'program' => $program,
            'modules' => $program->modules()->with('lessons.resources')->get(),
            'instructors' => $program->staff()->where('role', 'instructor')->get(),
            'announcements' => $program->announcements()->latest()->get(),
            'learner_count' => $program->enrolments()->count(),
            'payment_plans' => $program->paymentPlans()->get(),
        ];
    }

    public function getLessonDetails(int|string $id): array
    {
        $query = AcademyLesson::with(['module.program', 'resources', 'assessment', 'liveSession']);

        if (is_int($id) || is_numeric($id)) {
            $query->where('id', (int) $id);
        } else {
            $query->where('refid', $id);
        }

        $lesson = $query->first();

        if (!$lesson) {
            throw new RuntimeException("Lesson not found.");
        }

        return [
            'lesson' => $lesson,
            'resources' => $lesson->resources,
            'assessment' => $lesson->assessment,
            'live_session' => $lesson->liveSession,
            'module' => $lesson->module,
            'program' => $lesson->module->program ?? null,
        ];
    }

    protected function resolve(int|string|AcademyProgram $program): AcademyProgram
    {
        if ($program instanceof AcademyProgram) {
            return $program;
        }

        $query = AcademyProgram::query();

        if (is_int($program) || is_numeric($program)) {
            $query->where('id', (int) $program);
        } else {
            $query->where('refid', $program);
        }

        $foundProgram = $query->first();

        if (!$foundProgram) {
            throw new RuntimeException("Program not found.");
        }

        return $foundProgram;
    }
}
