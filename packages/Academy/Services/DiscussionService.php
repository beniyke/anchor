<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Academy\Services;

use Academy\Enums\DiscussionType;
use Academy\Exceptions\AcademyException;
use Academy\Models\AcademyDiscussion;
use Academy\Models\AcademyProgram;
use Database\DB;
use Helpers\String\Str;
use Hub\Hub;

class DiscussionService
{
    /**
     * Post a new discussion topic or reply.
     */
    public function post(array $data): AcademyDiscussion
    {
        $programId = $data['program_id'] ?? null;
        if ($programId) {
            $program = AcademyProgram::find($programId);
            if (!$program) {
                throw new AcademyException("Program not found.");
            }
        }

        return DB::transaction(function () use ($data) {
            $discussion = AcademyDiscussion::create($data);

            // Hub Integration (Community Threads)
            if (config('academy.integrations.hub', true) && class_exists(Hub::class)) {
                if (!isset($data['parent_id'])) {
                    $thread = Hub::thread()
                        ->on($discussion)
                        ->title($data['content'] ? Str::limit($data['content'], 50) : 'Discussion Topic')
                        ->by($data['user_id'])
                        ->create();

                    $discussion->update(['metadata' => array_merge($discussion->metadata ?? [], ['hub_thread_id' => $thread->id])]);
                } else {
                    $parent = AcademyDiscussion::find($data['parent_id']);
                    $threadId = $parent->metadata['hub_thread_id'] ?? null;

                    if ($threadId) {
                        Hub::message()
                            ->in($threadId)
                            ->from($data['user_id'])
                            ->body($data['content'])
                            ->send();
                    }
                }
            }

            return $discussion;
        });
    }

    public function pin(AcademyDiscussion $discussion): bool
    {
        return $discussion->update(['is_pinned' => true]);
    }

    public function resolve(AcademyDiscussion $discussion): bool
    {
        return $discussion->update(['is_resolved' => true]);
    }

    public function getForProgram(int $programId, ?DiscussionType $type = null)
    {
        $query = AcademyDiscussion::where('program_id', $programId)->whereNull('parent_id');

        if ($type) {
            $query->where('type', $type);
        }

        return $query->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc')->get();
    }
}
