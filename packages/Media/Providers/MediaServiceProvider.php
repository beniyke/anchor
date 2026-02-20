<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for the Media package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Media\Providers;

use App\Models\User;
use Core\Services\ServiceProvider;
use Database\Relations\MorphMany;
use Media\Models\Media;
use Media\Services\MediaManagerService;

class MediaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(MediaManagerService::class);
    }

    public function boot(): void
    {
        $this->registerUserMacros();
    }

    protected function registerUserMacros(): void
    {
        $container = $this->container;

        User::macro('media', function (): MorphMany {
            return $this->morphMany(Media::class, 'mediable');
        });

        User::macro('getMedia', function (string $collection = 'default'): array {
            return $this->media()
                ->where('collection', $collection)
                ->orderBy('created_at', 'asc')
                ->get()
                ->all();
        });

        User::macro('getFirstMedia', function (string $collection = 'default'): ?Media {
            return $this->media()
                ->where('collection', $collection)
                ->orderBy('created_at', 'asc')
                ->first();
        });

        User::macro('attachMedia', function (Media $media, string $collection = 'default'): Media {
            $media->update([
                'mediable_type' => static::class,
                'mediable_id' => $this->id,
                'collection' => $collection,
            ]);

            return $media;
        });

        User::macro('addMedia', function (mixed $file, string $collection = 'default', array $options = []) use ($container): Media {
            $media = $container->get(MediaManagerService::class)->upload($file, $options);

            return $this->attachMedia($media, $collection);
        });

        User::macro('addMediaFromUrl', function (string $url, string $collection = 'default', array $options = []) use ($container): Media {
            $media = $container->get(MediaManagerService::class)->uploadFromUrl($url, $options);

            return $this->attachMedia($media, $collection);
        });

        User::macro('clearMediaCollection', function (string $collection = 'default') use ($container): int {
            $manager = $container->get(MediaManagerService::class);
            $count = 0;

            foreach ($this->getMedia($collection) as $media) {
                $manager->delete($media);
                $count++;
            }

            return $count;
        });

        User::macro('hasMedia', function (string $collection = 'default'): bool {
            return $this->media()
                ->where('collection', $collection)
                ->exists();
        });
    }
}
