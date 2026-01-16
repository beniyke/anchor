<?php

declare(strict_types=1);
/**
 * Anchor Framework
 *
 * Pulse Manager Service.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Pulse\Services;

use App\Models\User;
use Audit\Audit;
use Helpers\DateTimeHelper;
use Pulse\Models\Channel;
use Pulse\Models\Post;
use Pulse\Models\Reaction;
use Pulse\Models\Subscription;
use Pulse\Models\Thread;
use RuntimeException;

class PulseManagerService
{
    public function __construct(
        private readonly \Core\Services\ConfigServiceInterface $config
    ) {
    }

    public function createChannel(array $data): Channel
    {
        $channel = Channel::create($data);

        if (class_exists(Audit::class)) {
            Audit::log('pulse.channel.created', ['id' => $channel->id, 'name' => $channel->name], $channel);
        }

        return $channel;
    }

    public function createThread(User $user, Channel $channel, string $title, string $content): Thread
    {
        $thread = Thread::create([
            'pulse_channel_id' => $channel->id,
            'user_id' => $user->id,
            'title' => $title,
            'slug' => $this->generateSlug($title, Thread::class),
            'last_activity_at' => DateTimeHelper::now(),
        ]);

        $this->createPost($user, $thread, $content);

        if (class_exists(Audit::class)) {
            Audit::log('pulse.thread.created', ['id' => $thread->id, 'title' => $thread->title], $thread, $user);
        }

        return $thread;
    }

    public function createPost(User $user, Thread $thread, string $content, ?int $parentId = null): Post
    {
        if ($thread->is_locked) {
            throw new RuntimeException("Cannot post in a locked thread.");
        }

        $post = Post::create([
            'pulse_thread_id' => $thread->id,
            'user_id' => $user->id,
            'parent_id' => $parentId,
            'content' => $content,
        ]);

        $thread->update(['last_activity_at' => DateTimeHelper::now()]);

        $this->notifyInteractions($post);

        return $post;
    }

    /**
     * Add a reaction to a post.
     */
    public function react(User $user, Post $post, string $type): Reaction
    {
        return Reaction::updateOrCreate([
            'pulse_post_id' => $post->id,
            'user_id' => $user->id,
        ], [
            'type' => $type,
        ]);
    }

    /**
     * Subscribe a user to a thread.
     */
    public function subscribe(User $user, Thread $thread): void
    {
        Subscription::updateOrCreate([
            'pulse_thread_id' => $thread->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Unsubscribe a user from a thread.
     */
    public function unsubscribe(User $user, Thread $thread): void
    {
        Subscription::where('pulse_thread_id', $thread->id)
            ->where('user_id', $user->id)
            ->delete();
    }

    /**
     * Notify users of replies or mentions.
     */
    protected function notifyInteractions(Post $post): void
    {
        $mentionedUsers = $this->resolveMentions($post->content);
        $thread = $post->thread;
        $author = $post->author;

        foreach ($mentionedUsers as $user) {
            if ($user->id === $author->id) {
                continue;
            }

            \Mail\Mail::send(new \Pulse\Notifications\MentionNotification(\Helpers\Data::make([
                'email' => $user->email,
                'name' => $user->name ?? 'there',
                'mentioner_name' => $author->name ?? 'Someone',
                'thread_title' => $thread->title ?? 'a conversation',
                'message_preview' => \Helpers\String\Str::limit($post->content, 100),
                'thread_url' => $this->getPulseThreadUrl($thread),
            ])));
        }

        $subscriberIds = Subscription::where('pulse_thread_id', $thread->id)
            ->pluck('user_id');

        $excludeIds = array_merge($mentionedUsers->pluck('id'), [$author->id]);
        $recipientIds = array_diff($subscriberIds, $excludeIds);

        if (empty($recipientIds)) {
            return;
        }

        $recipients = [];
        $users = User::whereIn('id', $recipientIds)->get();
        foreach ($users as $user) {
            if ($user->email) {
                $recipients[$user->email] = $user->name ?? '';
            }
        }

        if (!empty($recipients)) {
            \Mail\Mail::send(new \Pulse\Notifications\PostReplyNotification(\Helpers\Data::make([
                'recipients' => $recipients,
                'sender_name' => $author->name ?? 'Someone',
                'thread_title' => $thread->title ?? 'a conversation',
                'message_preview' => \Helpers\String\Str::limit($post->content, 100),
                'thread_url' => $this->getPulseThreadUrl($thread),
            ])));
        }
    }

    private function resolveMentions(string $content): \Database\Collections\ModelCollection
    {
        $pattern = '/@(\w+)/';
        preg_match_all($pattern, $content, $matches);
        $usernames = $matches[1] ?? [];

        if (empty($usernames)) {
            return new \Database\Collections\ModelCollection([]);
        }

        // Assuming users have a 'username' or 'name' column.
        // Using 'name' for now based on typical schema, or 'username' if it exists.
        // Best guess: 'name' is often used as display name, but 'username' is handle.
        // Let's check User model if possible, but for now I'll try 'username' or fallback to 'name'.
        // Actually User model usually has 'email', 'name'.
        // I will assume 'username' exists or I should query 'name'.
        // Safer to just query 'name' for now or 'username' if I knew.
        // I'll query 'username' as it's standard for mentions.
        return User::whereIn('username', $usernames)->get();
    }

    private function getPulseThreadUrl(Thread $thread): string
    {
        $pattern = $this->config->get('pulse.urls.thread', '/pulse/thread/{slug}');

        return url(str_replace('{slug}', $thread->slug, $pattern));
    }

    /**
     * Generate a unique slug for a model.
     */
    public function generateSlug(string $title, string $model): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $count = $model::where('slug', 'like', "{$slug}%")->count();

        return $count > 0 ? "{$slug}-" . ($count + 1) : $slug;
    }
}
