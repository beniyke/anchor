<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Otp Code
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
 * @property string          $code
 * @property string          $channel
 * @property ?DateTimeHelper $expires_at
 * @property ?DateTimeHelper $verified_at
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 */
class OtpCode extends BaseModel
{
    protected string $table = 'verify_otp_code';

    protected array $fillable = [
        'identifier',
        'refid',
        'code',
        'channel',
        'expires_at',
        'verified_at',
    ];
}
