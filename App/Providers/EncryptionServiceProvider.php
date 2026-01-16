<?php

declare(strict_types=1);

namespace App\Providers;

use Core\Services\ConfigServiceInterface;
use Core\Services\DeferredServiceProvider;
use Helpers\Encryption\Drivers\FileEncryptor;
use Helpers\Encryption\Drivers\SymmetricEncryptor;
use Helpers\Encryption\Drivers\SymmetricEncryptorInterface;
use Helpers\Encryption\Encrypter;
use RuntimeException;

class EncryptionServiceProvider extends DeferredServiceProvider
{
    public static function provides(): array
    {
        return [
            SymmetricEncryptorInterface::class,
            Encrypter::class,
        ];
    }

    public function register(): void
    {
        $this->container->singleton(SymmetricEncryptorInterface::class, function ($container) {
            $config = $container->get(ConfigServiceInterface::class);
            $key = $config->get('encryption_key');

            if (empty($key)) {
                throw new RuntimeException("Application encryption key ('app.key') is missing in configuration.");
            }

            return new SymmetricEncryptor($key);
        });

        $this->container->singleton(Encrypter::class, function ($container) {
            $stringDriver = $container->get(SymmetricEncryptorInterface::class);
            $fileDriver = $container->get(FileEncryptor::class);

            return new Encrypter($stringDriver, $fileDriver);
        });
    }
}
