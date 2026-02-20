<?php

declare(strict_types=1);

namespace Onboard\Services;

use App\Models\User;
use Audit\Audit;
use Onboard\Enums\EquipmentStatus;
use Onboard\Models\Equipment;

class EquipmentManagerService
{
    /**
     * Request equipment for a user.
     */
    public function request(User $user, string $type, ?string $notes = null): Equipment
    {
        $equipment = Equipment::create([
            'user_id' => $user->id,
            'request_type' => $type,
            'status' => EquipmentStatus::PENDING,
            'notes' => $notes,
        ]);

        if (class_exists(Audit::class)) {
            Audit::log('onboard.equipment.requested', [
                'user' => $user->email,
                'type' => $type,
            ], $equipment);
        }

        return $equipment;
    }

    /**
     * Assign an asset tag to equipment and update its status.
     */
    public function assign(Equipment $equipment, string $assetTag, string $status = 'assigned'): void
    {
        $equipment->update([
            'asset_tag' => $assetTag,
            'status' => $status,
        ]);

        if (class_exists(Audit::class)) {
            Audit::log('onboard.equipment.assigned', [
                'type' => $equipment->request_type,
                'tag' => $assetTag,
            ], $equipment);
        }
    }
}
