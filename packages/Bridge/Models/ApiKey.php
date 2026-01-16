<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * ApiKey Model represents an API key for external access.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Bridge\Models;

use Database\BaseModel;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $name
 * @property string          $key
 * @property ?DateTimeHelper $last_used_at
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 */
class ApiKey extends BaseModel
{
    protected string $table = 'api_key';

    protected array $fillable = [
        'name',
        'key',
        'last_used_at',
    ];

    public static function generate(string $name): array
    {
        $rawKey = bin2hex(random_bytes(32));
        $hashedKey = hash('sha256', $rawKey);

        $apiKey = static::create([
            'name' => $name,
            'key' => $hashedKey,
        ]);

        return [
            'key' => $rawKey,
            'model' => $apiKey,
        ];
    }

    public function revoke(): bool
    {
        return $this->delete();
    }
}
