<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Training.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Onboard\Models;

use Database\BaseModel;
use Database\Collections\ModelCollection;
use Database\Relations\BelongsTo;
use Database\Relations\HasMany;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $onboard_template_id
 * @property string          $name
 * @property string          $url
 * @property ?string         $description
 * @property int             $order
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Template $template
 * @property-read ModelCollection $progress
 */
class Training extends BaseModel
{
    protected string $table = 'onboard_training';

    protected array $fillable = [
        'onboard_template_id',
        'name',
        'url',
        'description',
        'order',
    ];

    protected array $casts = [
        'order' => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'onboard_template_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(TrainingProgress::class, 'onboard_training_id');
    }
}
