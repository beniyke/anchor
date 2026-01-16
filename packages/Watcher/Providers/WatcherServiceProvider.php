<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Service provider for registering Watcher components.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Watcher\Providers;

use Core\Error\ErrorHandler;
use Core\Event;
use Core\Ioc\ContainerInterface;
use Core\Services\ConfigServiceInterface;
use Core\Services\ServiceProvider;
use Database\Connection;
use Database\ConnectionInterface;
use Ghost\Events\ImpersonationStartedEvent;
use Ghost\Events\ImpersonationStoppedEvent;
use Helpers\File\Contracts\CacheInterface;
use Helpers\File\FileLogger;
use Queue\Worker;
use Throwable;
use Watcher\Alerts\AlertManager;
use Watcher\Alerts\Channels\EmailChannel;
use Watcher\Alerts\Channels\SlackChannel;
use Watcher\Alerts\Channels\WebhookChannel;
use Watcher\Analytics\WatcherAnalytics;
use Watcher\Config\WatcherConfig;
use Watcher\Filters\WatcherFilter;
use Watcher\Retention\RetentionPolicy;
use Watcher\Sampling\Sampler;
use Watcher\Storage\WatcherRepository;
use Watcher\WatcherManager;

class WatcherServiceProvider extends ServiceProvider
{
    private const WATCHER_TABLE = 'watcher_entries';

    public function register(): void
    {
        $container = $this->container;
        $config = $this->container->get(ConfigServiceInterface::class);

        $container->singleton(WatcherConfig::class, function () use ($config) {
            return new WatcherConfig($config->get('watcher') ?? []);
        });

        $container->singleton(WatcherRepository::class, function (ContainerInterface $container) {
            return new WatcherRepository($container->get(ConnectionInterface::class));
        });

        $container->singleton(Sampler::class, function (ContainerInterface $container) {
            return new Sampler($container->get(WatcherConfig::class));
        });

        $container->singleton(WatcherFilter::class, function (ContainerInterface $container) {
            return new WatcherFilter($container->get(WatcherConfig::class));
        });

        $container->singleton(WatcherManager::class, function (ContainerInterface $container) {
            return new WatcherManager($container->get(WatcherConfig::class), $container->get(WatcherRepository::class), $container->get(Sampler::class), $container->get(WatcherFilter::class));
        });

        $container->singleton(WatcherAnalytics::class, function (ContainerInterface $container) {
            return new WatcherAnalytics($container->get(WatcherRepository::class));
        });

        $container->singleton(RetentionPolicy::class, function (ContainerInterface $container) {
            return new RetentionPolicy($container->get(WatcherConfig::class), $container->get(WatcherRepository::class));
        });

        $container->singleton(AlertManager::class, function (ContainerInterface $container) {
            $config = $container->get(WatcherConfig::class);
            $manager = new AlertManager($config, $container->get(WatcherAnalytics::class), $container->get(CacheInterface::class));

            $channels = $config->getAlertChannels();

            $slackChannel = $container->make(SlackChannel::class, ['webhookUrl' => $channels['slack']]);
            $emailChannel = $container->make(EmailChannel::class, ['recipients' => $channels['email']]);
            $webhookChannel = $container->make(WebhookChannel::class, ['webhookUrl' => $channels['webhook']]);

            $manager->registerChannel('email', $emailChannel);
            $manager->registerChannel('slack', $slackChannel);
            $manager->registerChannel('webhook', $webhookChannel);

            return $manager;
        });
    }

    public function boot(): void
    {
        $container = $this->container;
        $config = $container->get(WatcherConfig::class);

        if (! $config->isEnabled()) {
            return;
        }

        try {
            $connection = $container->get(ConnectionInterface::class);
            if (! $connection->tableExists(self::WATCHER_TABLE)) {
                return;
            }
        } catch (Throwable $e) {
            return;
        }

        $watcher = $container->get(WatcherManager::class);

        if ($config->isTypeEnabled('query')) {
            Connection::listen(function (array $queryData) use ($watcher) {
                $watcher->record('query', $queryData);
            });
        }

        if ($config->isTypeEnabled('exception')) {
            ErrorHandler::listen(function (array $exceptionData) use ($watcher) {
                $watcher->record('exception', $exceptionData);
            });
        }

        if ($config->isTypeEnabled('job')) {
            Worker::listen(function (string $event, array $jobData) use ($watcher) {
                $watcher->record('job', array_merge($jobData, ['event' => $event]));
            });
        }

        if ($config->isTypeEnabled('log')) {
            FileLogger::listen(function (array $logData) use ($watcher) {
                $watcher->record('log', $logData);
            });
        }

        if ($config->isTypeEnabled('ghost')) {
            if (class_exists(ImpersonationStartedEvent::class)) {
                Event::listen(ImpersonationStartedEvent::class, function ($event) use ($watcher) {
                    $watcher->record('ghost', [
                        'action' => 'started',
                        'impersonator_id' => $event->impersonator->id,
                        'impersonator_name' => $event->impersonator->name,
                        'impersonated_id' => $event->impersonated->id,
                        'impersonated_name' => $event->impersonated->name,
                    ]);
                });
            }

            if (class_exists(ImpersonationStoppedEvent::class)) {
                Event::listen(ImpersonationStoppedEvent::class, function ($event) use ($watcher) {
                    $watcher->record('ghost', [
                        'action' => 'stopped',
                        'impersonator_id' => $event->impersonator->id,
                        'impersonator_name' => $event->impersonator->name,
                        'impersonated_id' => $event->impersonated->id,
                        'impersonated_name' => $event->impersonated->name,
                    ]);
                });
            }
        }

        register_shutdown_function(function () use ($watcher) {
            $watcher->flush();
        });
    }
}
