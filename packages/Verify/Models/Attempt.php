<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Attempt
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Verify\Models;

use Database\BaseModel;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $identifier
 * @property string          $refid
 * @property string          $attempt_type
 * @property int             $count
 * @property string          $window_start
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 */
class Attempt extends BaseModel
{
    protected string $table = 'verify_attempt';

    protected array $fillable = [
        'identifier',
        'refid',
        'attempt_type',
        'count',
        'window_start',
    ];
}
