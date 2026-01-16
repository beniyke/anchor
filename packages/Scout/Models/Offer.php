<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Offer.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Scout\Models;

use Database\BaseModel;
use Database\Relations\BelongsTo;
use DateTimeInterface;

/**
 * @property int                $id
 * @property int                $scout_application_id
 * @property float              $amount
 * @property string             $currency
 * @property string             $terms
 * @property ?DateTimeInterface $expires_at
 * @property string             $status
 * @property ?int               $link_id
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 * @property-read Application $application
 */
class Offer extends BaseModel
{
    protected string $table = 'scout_offer';

    protected array $fillable = [
        'scout_application_id',
        'amount',
        'currency',
        'terms',
        'expires_at',
        'status',
        'link_id',
    ];

    protected array $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'scout_application_id');
    }
}
