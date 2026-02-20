<?php

declare(strict_types=1);

namespace App\Providers;

use App\Channels\Adapters\EmailAdapter;
use App\Channels\Adapters\Interfaces\EmailAdapterInterface;
use App\Channels\Adapters\Interfaces\SmsAdapterInterface;
use App\Channels\Adapters\Interfaces\WhatsAppAdapterInterface;
use App\Channels\Adapters\SmsAdapter;
use App\Channels\Adapters\WhatsAppAdapter;
use App\Channels\EmailChannel;
use App\Channels\SmsChannel;
use App\Channels\WhatsAppChannel;
use Core\Services\ServiceProvider;
use Notify\NotificationManager;

class NotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(EmailAdapterInterface::class, EmailAdapter::class);
        $this->container->singleton(WhatsAppAdapterInterface::class, WhatsAppAdapter::class);
        $this->container->singleton(SmsAdapterInterface::class, SmsAdapter::class);

        $this->container->extend(NotificationManager::class, function (NotificationManager $manager, $container) {
            $manager->registerChannel('email', $container->make(EmailChannel::class));
            $manager->registerChannel('sms', $container->make(SmsChannel::class));
            $manager->registerChannel('whatsapp', $container->make(WhatsAppChannel::class));

            return $manager;
        });
    }
}
