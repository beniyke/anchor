<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Competency.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Metric\Models;

use Database\BaseModel;
use DateTimeInterface;

/**
 * @property int                $id
 * @property string             $name
 * @property ?string            $description
 * @property ?string            $category
 * @property ?DateTimeInterface $created_at
 * @property ?DateTimeInterface $updated_at
 */
class Competency extends BaseModel
{
    protected string $table = 'metric_competency';

    protected array $fillable = [
        'name',
        'description',
        'category',
    ];
}
