<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Document.
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
 * @property ?string         $description
 * @property bool            $is_required
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read Template $template
 * @property-read ModelCollection $uploads
 */
class Document extends BaseModel
{
    protected string $table = 'onboard_document';

    protected array $fillable = [
        'onboard_template_id',
        'name',
        'description',
        'is_required',
    ];

    protected array $casts = [
        'is_required' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'onboard_template_id');
    }

    public function uploads(): HasMany
    {
        return $this->hasMany(DocumentUpload::class, 'onboard_document_id');
    }
}
