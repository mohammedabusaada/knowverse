<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\User;
use App\Enums\NotificationType;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Asynchronous job to handle mass notification distribution.
 * Offloads heavy database queries and insertions to a background worker,
 * preventing HTTP timeout errors when an author with thousands of followers publishes a post.
 */
class NotifyFollowersOnNewPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $post;
    public $tagIds;

    public function __construct(Post $post, array $tagIds = [])
    {
        $this->post = $post;
        $this->tagIds = $tagIds;
    }

    /**
     * Executes the background job to dispatch personalized notifications.
     * Injects the NotificationService dependency automatically via Laravel's Service Container.
     */
    public function handle(NotificationService $notificationService): void
    {
        $post = $this->post; 
        $author = $post->user;

        // ------------------------------------------------------------------
        // Phase 1: Notify the Author's Direct Followers
        // ------------------------------------------------------------------
        $author->followers()->chunk(100, function ($followers) use ($author, $notificationService, $post) {
            foreach ($followers as $follower) {
                $notificationService->notify(
                    recipient: $follower,
                    type: NotificationType::NEW_POST_FOLLOWING,
                    actor: $author,
                    target: $post,
                    message: "A scholar you follow published: {$post->title}"
                );
            }
        });

        // ------------------------------------------------------------------
        // Phase 2: Notify Topic (Tag) Subscribers
        // ------------------------------------------------------------------
        if (!empty($this->tagIds)) {
            $usersToNotify = User::whereHas('followedTags', function ($query) {
                    $query->whereIn('tags.id', $this->tagIds);
                })
                ->where('id', '!=', $author->id)
                ->whereDoesntHave('following', function ($query) use ($author) {
                    $query->where('users.id', $author->id);
                })
                ->get();

            foreach ($usersToNotify as $userToNotify) {
                $notificationService->notify(
                    recipient: $userToNotify,
                    type: NotificationType::NEW_POST_TAG,
                    actor: $author,
                    target: $post,
                    message: "A new discussion was published under a topic you follow: {$post->title}"
                );
            }
        }
    }
}