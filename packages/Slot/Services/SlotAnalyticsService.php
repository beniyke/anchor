<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Slot Analytics Service
 *
 * Provides reporting on bookings, occupancy, and calendar data.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Slot\Services;

use Database\DB;
use Slot\Enums\BookingStatus;

class SlotAnalyticsService
{
    private const SCHEDULE_TABLE = 'slot_schedule';
    private const BOOKING_TABLE = 'slot_booking';

    public function getBookingsSummary(?string $from = null, ?string $to = null): array
    {
        $query = DB::table(self::BOOKING_TABLE);

        if ($from) {
            $query->where('starts_at', '>=', $from);
        }
        if ($to) {
            $query->where('starts_at', '<=', $to);
        }

        $stats = $query->select(
            DB::raw('COUNT(*) as total_bookings'),
            DB::raw('SUM(CASE WHEN status = "' . BookingStatus::Confirmed->value . '" THEN 1 ELSE 0 END) as confirmed'),
            DB::raw('SUM(CASE WHEN status = "' . BookingStatus::Cancelled->value . '" THEN 1 ELSE 0 END) as cancelled'),
            DB::raw('SUM(CASE WHEN status = "' . BookingStatus::Completed->value . '" THEN 1 ELSE 0 END) as completed')
        )->first();

        $total = (int) $stats->total_bookings;
        $cancelled = (int) $stats->cancelled;

        return [
            'total_bookings' => $total,
            'confirmed' => (int) $stats->confirmed,
            'cancelled' => $cancelled,
            'completed' => (int) $stats->completed,
            'cancellation_rate' => $total > 0 ? round(($cancelled / $total) * 100, 2) : 0,
        ];
    }

    public function getDailyBookings(string $from, string $to): array
    {
        $trends = DB::table(self::BOOKING_TABLE)
            ->select(
                DB::raw('DATE(starts_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('starts_at', '>=', $from)
            ->where('starts_at', '<=', $to)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return array_map(function ($row) {
            return [
                'date' => $row->date,
                'count' => (int) $row->count,
            ];
        }, $trends);
    }

    /**
     * Get standardized calendar events for a model (e.g., User, Asset) within a range.
     * Compatible with FullCalendar and other generic calendar UIs.
     */
    public function getCalendarEvents(object $model, string $start, string $end): array
    {
        $refid = method_exists($model, 'getRefid') ? $model->getRefid() : $model->id;

        $schedules = DB::table(self::SCHEDULE_TABLE)
            ->where('refid', $refid)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('starts_at', [$start, $end])
                    ->orWhereBetween('ends_at', [$start, $end]);
            })
            ->get();

        $events = [];

        foreach ($schedules as $schedule) {
            $events[] = [
                'id' => 'schedule_' . $schedule->id,
                'title' => 'Available',
                'start' => $schedule->starts_at,
                'end' => $schedule->ends_at,
                'type' => 'availability',
                'color' => '#10b981', // green
                'metadata' => [
                    'schedule_id' => $schedule->id,
                    'type' => $schedule->schedule_type,
                ]
            ];
        }

        $bookings = DB::table(self::BOOKING_TABLE)
            ->where('refid', $refid)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('starts_at', [$start, $end])
                    ->orWhereBetween('ends_at', [$start, $end]);
            })
            ->where('status', '!=', BookingStatus::Cancelled->value)
            ->get();

        foreach ($bookings as $booking) {
            $events[] = [
                'id' => 'booking_' . $booking->id,
                'title' => 'Booked: ' . ($booking->title ?? 'Untitled'),
                'start' => $booking->starts_at,
                'end' => $booking->ends_at,
                'type' => 'booking',
                'color' => '#3b82f6', // blue
                'status' => $booking->status,
                'metadata' => [
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->user_id,
                ]
            ];
        }

        return $events;
    }

    /**
     * Get occupancy rate (booked time / total scheduled time).
     */
    public function getOccupancyRate(object $model, string $start, string $end): float
    {
        $refid = method_exists($model, 'getRefid') ? $model->getRefid() : $model->id;

        // Total scheduled seconds
        $scheduledSeconds = DB::table(self::SCHEDULE_TABLE)
            ->where('refid', $refid)
            ->where('starts_at', '>=', $start)
            ->where('ends_at', '<=', $end)
            ->select(DB::raw('SUM(TIMESTAMPDIFF(SECOND, starts_at, ends_at)) as seconds'))
            ->first()->seconds ?? 0;

        if ($scheduledSeconds <= 0) {
            return 0.0;
        }

        // Total booked seconds
        $bookedSeconds = DB::table(self::BOOKING_TABLE)
            ->where('refid', $refid)
            ->where('starts_at', '>=', $start)
            ->where('ends_at', '<=', $end)
            ->whereIn('status', [BookingStatus::Confirmed->value, BookingStatus::Completed->value])
            ->select(DB::raw('SUM(TIMESTAMPDIFF(SECOND, starts_at, ends_at)) as seconds'))
            ->first()->seconds ?? 0;

        return round(($bookedSeconds / $scheduledSeconds) * 100, 2);
    }
}
