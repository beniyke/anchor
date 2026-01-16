<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Requests\UploadPhotoRequest;
use Core\Services\ConfigServiceInterface;
use Database\DB;
use Helpers\File\FileSystem;
use Helpers\Http\FileHandler;
use Helpers\String\Str;
use RuntimeException;

class UpdatePhotoAction
{
    private const RANDOM_FILE_NAME_LENGTH = 12;

    private readonly ConfigServiceInterface $config;

    public function __construct(ConfigServiceInterface $config)
    {
        $this->config = $config;
    }

    public function execute(User $user, UploadPhotoRequest $request): bool
    {
        if (! $request->isValid()) {
            return false;
        }

        return DB::transaction(function () use ($user, $request) {
            $old_photo = $user->photo;
            $photo_uploaded = $this->addPhoto($user, $request->photo);

            if (! $photo_uploaded) {
                throw new RuntimeException('Failed to upload new photo file.');
            }

            $userUpdated = $user->update(['photo' => $photo_uploaded]);

            if (! $userUpdated) {
                $this->removePhoto($user, $photo_uploaded);

                throw new RuntimeException("Failed to update user's photo path in database.");
            }

            if ($old_photo) {
                defer(fn () => $this->removePhoto($user, $old_photo));
            }

            defer(function () use ($user) {
                activity('changed profile photo', ['email' => $user->email]);
            });

            return true;
        });
    }

    private function getPhotoPath(User $user): string
    {
        return rtrim($this->config->get('app.assets.photo'), '/') . '/' . md5($user->refid);
    }

    private function addPhoto(User $user, FileHandler $picture): ?string
    {
        $photo_path = $this->getPhotoPath($user);

        $hashed_name = Str::random('alnum', self::RANDOM_FILE_NAME_LENGTH);
        $file = $hashed_name . '.' . $picture->getExtension();

        if ($picture->move($photo_path, $file)) {
            return $file;
        }

        return null;
    }

    private function removePhoto(User $user, string $picture): bool
    {
        $file_to_delete = $this->getPhotoPath($user) . '/' . $picture;

        if (FileSystem::exists($file_to_delete)) {
            return FileSystem::delete($file_to_delete);
        }

        return false;
    }
}
