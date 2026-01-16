<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Pulse.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Pulse;

use App\Models\User;
use BadMethodCallException;
use Pulse\Models\Channel;
use Pulse\Models\Post;
use Pulse\Models\Thread;
use Pulse\Services\Builders\ChannelBuilder;
use Pulse\Services\Builders\PostBuilder;
use Pulse\Services\Builders\ThreadBuilder;
use Pulse\Services\EngagementManagerService;
use Pulse\Services\ModerationManagerService;
use Pulse\Services\PulseAnalyticsService;
use Pulse\Services\PulseManagerService;

/**
 * Pulse Facade
 *
 * @method static Channel               createChannel(array $data)
 * @method static Thread                createThread(User $user, Channel $channel, string $title, string $content)
 * @method static Post                  createPost(User $user, Thread $thread, string $content, ?int $parentId = null)
 * @method static void                  pin(Thread $thread)
 * @method static void                  lock(Thread $thread)
 * @method static void                  awardPoints(User $user, int $points)
 * @method static PulseAnalyticsService analytics()
 */
class Pulse
{
    /**
     * Get the PulseManagerService instance.
     */
    protected static function manager(): PulseManagerService
    {
        return resolve(PulseManagerService::class);
    }

    /**
     * Start building a new channel.
     */
    public static function channel(): ChannelBuilder
    {
        return new ChannelBuilder(static::manager());
    }

    /**
     * Start building a new thread.
     */
    public static function thread(): ThreadBuilder
    {
        return new ThreadBuilder(static::manager());
    }

    /**
     * Start building a new post.
     */
    public static function post(): PostBuilder
    {
        return new PostBuilder(static::manager());
    }

    public static function analytics(): PulseAnalyticsService
    {
        return resolve(PulseAnalyticsService::class);
    }

    /**
     * Delegate static calls to the PulseManagerService or ModerationManagerService.
     */
    public static function __callStatic(string $method, array $arguments): mixed
    {
        $manager = static::manager();
        if (method_exists($manager, $method)) {
            return $manager->$method(...$arguments);
        }

        $moderation = resolve(ModerationManagerService::class);
        if (method_exists($moderation, $method)) {
            return $moderation->$method(...$arguments);
        }

        $engagement = resolve(EngagementManagerService::class);
        if (method_exists($engagement, $method)) {
            return $engagement->$method(...$arguments);
        }

        throw new BadMethodCallException("Method {$method} does not exist on Pulse facades.");
    }
}
