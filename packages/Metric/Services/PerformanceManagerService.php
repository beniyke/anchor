<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Performance Manager Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric\Services;

use App\Models\User;
use Audit\Audit;
use DateTimeInterface;
use Helpers\DateTimeHelper;
use Hub\Hub;
use Metric\Enums\OneOnOneStatus;
use Metric\Models\Feedback;
use Metric\Models\OneOnOne;
use Metric\Models\Recognition;
use Metric\Models\Review;
use Metric\Models\ReviewCycle;
use Slot\Enums\ScheduleType;
use Slot\Period;
use Slot\Slot;
use Throwable;

class PerformanceManagerService
{
    /**
     * Start a new review cycle.
     */
    public function startCycle(string $name, DateTimeInterface $start, DateTimeInterface $end): ReviewCycle
    {
        return ReviewCycle::create([
            'name' => $name,
            'start_at' => $start,
            'end_at' => $end,
            'status' => 'active',
        ]);
    }

    /**
     * Submit feedback for a user.
     */
    public function giveFeedback(User $author, User $recipient, string $content, ?Review $review = null): Feedback
    {
        $feedback = Feedback::create([
            'metric_review_id' => $review?->id,
            'user_id' => $recipient->id,
            'author_id' => $author->id,
            'content' => $content,
        ]);

        if (class_exists(Hub::class)) {
            Hub::thread()
                ->title("Performance Feedback for {$recipient->name}")
                ->with('feedback', $content)
                ->create();
        }

        return $feedback;
    }

    public function recognize(User $sender, User $receiver, string $awardType, string $message): Recognition
    {
        $recognition = Recognition::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'award_type' => $awardType,
            'message' => $message,
        ]);

        if (class_exists(Audit::class)) {
            Audit::log('metric.recognition.sent', [
                'from' => $sender->email,
                'to' => $receiver->email,
                'type' => $awardType
            ], $recognition);
        }

        return $recognition;
    }

    /**
     * Schedule a 1:1 meeting.
     */
    public function scheduleOneOnOne(User $user, User $manager, DateTimeInterface $scheduledAt, ?string $agenda = null): OneOnOne
    {
        $slotId = null;

        if (class_exists(Slot::class)) {
            try {
                $start = DateTimeHelper::instance($scheduledAt);
                $end = DateTimeHelper::instance((clone $start)->addMinutes(30));
                $period = new Period($start, $end);

                $availabilities = Slot::forModel($manager)->getSchedules(ScheduleType::Availability, $period);

                if (! empty($availabilities)) {
                    $availability = $availabilities[0];
                    $booking = Slot::bookByScheduleId($availability->id, $user, $period);
                    $slotId = $booking->id;
                }
            } catch (Throwable $e) {
                // Silent catch for soft integration
            }
        }

        return OneOnOne::create([
            'user_id' => $user->id,
            'manager_id' => $manager->id,
            'scheduled_at' => $scheduledAt,
            'agenda' => $agenda,
            'slot_id' => $slotId,
            'status' => OneOnOneStatus::SCHEDULED,
        ]);
    }

    /**
     * Complete a 1:1 meeting with notes.
     */
    public function completeOneOnOne(OneOnOne $oneOnOne, string $notes): void
    {
        $oneOnOne->update([
            'notes' => $notes,
            'status' => OneOnOneStatus::COMPLETED,
        ]);

        if (class_exists(Audit::class)) {
            Audit::log('metric.one_on_one.completed', [
                'user' => $oneOnOne->user->email,
                'manager' => $oneOnOne->manager->email,
            ], $oneOnOne);
        }
    }
}
