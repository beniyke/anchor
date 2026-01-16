<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Trait for models that have media attachments.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Media\Traits;

use Database\Relations\MorphMany;
use Media\Models\Media;
use Media\Services\MediaManagerService;

trait HasMedia
{
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function getMedia(string $collection = 'default'): array
    {
        return $this->media()
            ->where('collection', $collection)
            ->orderBy('created_at', 'asc')
            ->get()
            ->all();
    }

    public function getFirstMedia(string $collection = 'default'): ?Media
    {
        return $this->media()
            ->where('collection', $collection)
            ->orderBy('created_at', 'asc')
            ->first();
    }

    /**
     * Attach media to this model.
     */
    public function attachMedia(Media $media, string $collection = 'default'): Media
    {
        $media->update([
            'mediable_type' => static::class,
            'mediable_id' => $this->id,
            'collection' => $collection,
        ]);

        return $media;
    }

    public function addMedia(mixed $file, string $collection = 'default', array $options = []): Media
    {
        $media = resolve(MediaManagerService::class)->upload($file, $options);

        return $this->attachMedia($media, $collection);
    }

    public function addMediaFromUrl(string $url, string $collection = 'default', array $options = []): Media
    {
        $media = resolve(MediaManagerService::class)->uploadFromUrl($url, $options);

        return $this->attachMedia($media, $collection);
    }

    /**
     * Clear media from a collection.
     */
    public function clearMediaCollection(string $collection = 'default'): int
    {
        $manager = resolve(MediaManagerService::class);
        $count = 0;

        foreach ($this->getMedia($collection) as $media) {
            $manager->delete($media);
            $count++;
        }

        return $count;
    }

    public function hasMedia(string $collection = 'default'): bool
    {
        return $this->media()
            ->where('collection', $collection)
            ->exists();
    }
}
