<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Document Upload.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Onboard\Models;

use App\Models\User;
use Database\BaseModel;
use Database\Relations\BelongsTo;
use Helpers\DateTimeHelper;

/**
 * @property int             $id
 * @property int             $user_id
 * @property int             $onboard_document_id
 * @property ?int            $media_id
 * @property string          $status
 * @property ?string         $rejection_reason
 * @property ?DateTimeHelper $verified_at
 * @property ?int            $verified_by
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read User $user
 * @property-read Document $document
 * @property-read ?User $verifier
 */
class DocumentUpload extends BaseModel
{
    protected string $table = 'onboard_document_upload';

    protected array $fillable = [
        'user_id',
        'onboard_document_id',
        'media_id',
        'status',
        'rejection_reason',
        'verified_at',
        'verified_by',
    ];

    protected array $casts = [
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'onboard_document_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
