<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Search Log.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Guide\Models;

use Database\BaseModel;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $query
 * @property int             $results_count
 * @property ?int            $user_id
 * @property ?string         $ip_address
 * @property ?array          $metadata
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 */
class SearchLog extends BaseModel
{
    protected string $table = 'guide_search_log';

    protected array $fillable = [
        'query',
        'results_count',
        'user_id',
        'ip_address',
        'metadata',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'metadata' => 'json',
    ];
}
