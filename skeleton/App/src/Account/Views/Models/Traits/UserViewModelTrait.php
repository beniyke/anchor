<?php

declare(strict_types=1);

namespace App\Account\Views\Models\Traits;

use App\Enums\UserStatus;
use Helpers\Array\Collections;

trait UserViewModelTrait
{
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getRolesForDropdown(): array
    {
        $dropdown = [];
        foreach ($this->getRoles() as $role) {
            $dropdown[$role->getSlug()] = $role->getName();
        }

        return $this->prependSelectToOptions($dropdown);
    }

    public function getGendersForDropdown(): array
    {
        $genders = [
            'male' => 'Male',
            'female' => 'Female'
        ];

        return $this->prependSelectToOptions($genders);
    }

    public function getStatusForDropdown(): array
    {
        $options = [];

        $status = UserStatus::all();

        foreach ($status as $option) {
            $options[$option] = UserStatus::from($option)->label();
        }

        return $this->prependSelectToOptions($options);
    }

    private function prependSelectToOptions(array $options): array
    {
        return Collections::make($options)->prepend(['' => 'SELECT'])->get();
    }
}
