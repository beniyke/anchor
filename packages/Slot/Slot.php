<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Facade for the Slot system.
 */

namespace Slot;

use Slot\Enums\ScheduleType;
use Slot\Models\SlotBooking;
use Slot\Models\SlotSchedule;
use Slot\Services\SlotAnalyticsService;

/**
 * @method static SlotManager          forModel(object $model)
 * @method static SlotSchedule         schedule(Period $period, array $options = [])
 * @method static SlotSchedule         availability(Period $period, array $options = [])
 * @method static SlotSchedule         appointment(Period $period, array $options = [])
 * @method static SlotSchedule         blocked(Period $period, array $options = [])
 * @method static SlotSchedule         custom(Period $period, array $options = [])
 * @method static array                getAvailableSlots(Period $range, ?int $duration = null, array $constraints = [])
 * @method static array                checkConflicts(Period $period, ScheduleType|string|null $type = null)
 * @method static SlotBooking          book(SlotSchedule $schedule, object $bookable, Period $period, array $options = [])
 * @method static bool                 updateSchedule(SlotSchedule $schedule, Period|array $data)
 * @method static bool                 deleteSchedule(SlotSchedule $schedule)
 * @method static SlotAnalyticsService analytics()
 */
class Slot
{
    private static ?SlotManager $instance = null;

    public static function instance(): SlotManager
    {
        if (self::$instance === null) {
            self::$instance = resolve(SlotManager::class);
        }

        return self::$instance;
    }

    public static function __callStatic(string $method, array $arguments): mixed
    {
        return self::instance()->$method(...$arguments);
    }
}
