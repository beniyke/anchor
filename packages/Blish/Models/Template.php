<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Template.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Blish\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property string          $refid
 * @property string          $name
 * @property string          $content
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read ModelCollection $campaigns
 */
class Template extends BaseModel
{
    protected string $table = 'blish_template';

    protected array $fillable = [
        'refid',
        'name',
        'content',
    ];

    protected array $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'template_id');
    }
}
