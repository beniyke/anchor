<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Core service for managing and logging application activities.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Activity\Services;

use Activity\Models\Activity;
use App\Models\User;
use Core\Services\ConfigServiceInterface;
use Database\BaseModel;
use Database\DB;
use Database\Pagination\Paginator;
use Defer\Defer;
use Helpers\DateTimeHelper;
use Throwable;

class ActivityManagerService
{
    public const DEFAULT_PER_PAGE = 20;

    private string $description = '';

    private array $data = [];

    private ?int $user_id = null;

    private ?BaseModel $subject = null;

    private array $metadata = [];

    private string $tag;

    private string $level;

    private ?string $session_id = null;

    private string $channel = 'web';

    private bool $deferred = true;

    private ConfigServiceInterface $config;

    public function __construct(ConfigServiceInterface $config)
    {
        $this->config = $config;
        $this->tag = $this->config->get('activity.default_tag', 'general');
        $this->level = $this->config->get('activity.default_level', 'info');
    }

    public function description(string $description): self
    {
        $this->reset();
        $this->description = $description;

        return $this;
    }

    private function reset(): void
    {
        $this->description = '';
        $this->data = [];
        $this->user_id = null;
        $this->subject = null;
        $this->metadata = [];
        $this->tag = $this->config->get('activity.default_tag', 'general');
        $this->level = $this->config->get('activity.default_level', 'info');
        $this->session_id = null;
        $this->channel = 'web';
        $this->deferred = true;
    }

    public function data(?array $data): self
    {
        if ($data !== null) {
            $this->data = $data;
        }

        return $this;
    }

    public function user(?int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function immediate(bool $immediate = true): self
    {
        $this->deferred = ! $immediate;

        return $this;
    }

    public function subject(?BaseModel $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function metadata(array $metadata): self
    {
        $this->metadata = array_merge($this->metadata, $metadata);

        return $this;
    }

    public function tag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function level(string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function session(?string $sessionId): self
    {
        $this->session_id = $sessionId;

        return $this;
    }

    public function channel(string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function log(): bool
    {
        static $hasTable = null;

        if ($hasTable === null) {
            try {
                $hasTable = DB::connection()->tableExists(Activity::TABLE);
            } catch (Throwable $e) {
                $hasTable = false;
            }
        }

        if (! $hasTable) {
            return false;
        }

        $description = $this->interpolate($this->description, $this->data);

        // Auto-capture some metadata if available
        $this->captureContext();

        if ($this->user_id === null) {
            return false;
        }

        $logAction = fn () => Activity::log(
            $this->user_id,
            $description,
            $this->metadata,
            $this->tag,
            $this->level,
            $this->subject,
            $this->session_id,
            $this->channel
        );

        if ($this->deferred && class_exists(Defer::class)) {
            Defer::push($logAction);

            return true;
        }

        $logAction();

        return true;
    }

    protected function captureContext(): void
    {
        if ($this->config->get('activity.capture.ip', true) && isset($_SERVER['REMOTE_ADDR'])) {
            $this->metadata['ip'] = $_SERVER['REMOTE_ADDR'];
        }

        if ($this->config->get('activity.capture.user_agent', true) && isset($_SERVER['HTTP_USER_AGENT'])) {
            $this->metadata['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        }

        // Auto-capture session if not explicitly set
        if ($this->config->get('activity.capture.session', true) && $this->session_id === null && session_id()) {
            $this->session_id = session_id();
        }

        // Auto-determine channel
        if ($this->config->get('activity.capture.channel', true)) {
            if (PHP_SAPI === 'cli') {
                $this->channel = 'cli';
            } elseif (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
                $this->channel = 'api';
            }
        }

        // Auto-capture user if not explicitly set
        if ($this->user_id === null && function_exists('auth')) {
            $user = auth()->user();
            if ($user) {
                $this->user_id = (int) $user->id;
            }
        }
    }

    /**
     * Prune activity logs older than a specific number of days.
     */
    public function prune(int $days): int
    {
        return Activity::prune($days);
    }

    public function listUserActivities(User $user, int $page = 1, int $perPage = self::DEFAULT_PER_PAGE): Paginator
    {
        return Activity::latestForUser($user->id)
            ->paginate($perPage, $page);
    }

    public function listRecentActivities(int $days = 7, int $page = 1, int $perPage = self::DEFAULT_PER_PAGE): Paginator
    {
        $query = Activity::query();

        if ($days === 0) {
            $query->today();
        } elseif ($days > 0) {
            $query->recent($days);
        }

        $query->with('user');

        return $query
            ->latest()
            ->paginate($perPage, $page);
    }

    public function getSummary(Activity $activity): string
    {
        $timeAgo = DateTimeHelper::timeAgo((string) $activity->created_at);

        return $activity->formatSummary($timeAgo);
    }

    protected function interpolate(string $description, array $data): string
    {
        if (empty($data)) {
            return $description;
        }

        return preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($data) {
            $key = $matches[1];

            return $data[$key] ?? $matches[0];
        }, $description);
    }
}
