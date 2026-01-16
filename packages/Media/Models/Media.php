<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Media model for storing file metadata.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Media\Models;

use Database\BaseModel;
use Database\Query\Builder;
use Database\Relations\MorphTo;
use Helpers\DateTimeHelper;
use Helpers\String\Str;

/**
 * @property int             $id
 * @property string          $uuid
 * @property string          $disk
 * @property string          $path
 * @property string          $filename
 * @property string          $original_filename
 * @property string          $mime_type
 * @property int             $size
 * @property string          $mediable_type
 * @property int             $mediable_id
 * @property string          $collection
 * @property ?array          $conversions
 * @property ?array          $metadata
 * @property ?DateTimeHelper $created_at
 * @property ?DateTimeHelper $updated_at
 * @property-read BaseModel $mediable
 *
 * @method static Builder collection(string $collection)
 * @method static Builder images()
 * @method static Builder videos()
 */
class Media extends BaseModel
{
    protected string $table = 'media';

    protected array $fillable = [
        'uuid',
        'disk',
        'path',
        'filename',
        'original_filename',
        'mime_type',
        'size',
        'mediable_type',
        'mediable_id',
        'collection',
        'conversions',
        'metadata',
    ];

    protected array $casts = [
        'size' => 'int',
        'conversions' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getFullPath(): string
    {
        return $this->path . DIRECTORY_SEPARATOR . $this->filename;
    }

    public function isImage(): bool
    {
        return Str::startsWith($this->mime_type, 'image/');
    }

    public function isVideo(): bool
    {
        return Str::startsWith($this->mime_type, 'video/');
    }

    public function isAudio(): bool
    {
        return Str::startsWith($this->mime_type, 'audio/');
    }

    public function isDocument(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }

    public function getHumanSize(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor((strlen((string) $bytes) - 1) / 3);

        if ($factor == 0) {
            return $bytes . ' B';
        }

        return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[(int) $factor]);
    }

    public function humanSize(): string
    {
        return $this->getHumanSize();
    }

    public function hasConversion(string $name): bool
    {
        return isset($this->conversions[$name]);
    }

    public function getConversionPath(string $name): ?string
    {
        return $this->conversions[$name]['path'] ?? null;
    }

    public static function findByUuid(string $uuid): ?self
    {
        return static::where('uuid', $uuid)->first();
    }

    public function scopeCollection(Builder $query, string $collection): Builder
    {
        return $query->where('collection', $collection);
    }

    public function scopeImages(Builder $query): Builder
    {
        return $query->where('mime_type', 'LIKE', 'image/%');
    }

    public function scopeVideos(Builder $query): Builder
    {
        return $query->where('mime_type', 'LIKE', 'video/%');
    }
}
