<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\ActivityService;
use App\Services\ContentFilter;
use Illuminate\Validation\ValidationException;

class PostObserver
{
    public function __construct(protected ContentFilter $filter) {}

    /**
     * Check content before saving (Create or Update)
     */
    public function saving(Post $post): void
{
    // Check Title
    if ($error = $this->filter->getValidationError($post->title)) {
        throw ValidationException::withMessages(['title' => $error]);
    }

    // Check Body
    if ($error = $this->filter->getValidationError($post->body)) {
        throw ValidationException::withMessages(['body' => $error]);
    }
}

    /**
     * Post created.
     */
    public function created(Post $post): void
    {
        $author = $post->user;
        ActivityService::postCreated($author, $post);
        $author->addReputation('post_created', null, $post);
    }

    /**
     * Post soft deleted.
     */
    public function deleting(Post $post): void
    {
        $author = $post->user;
        $author->removeReputation('post_created', $post);

        if ($post->bestComment) {
            $author->removeReputation('best_answer_awarded', $post->bestComment);
            $post->bestComment->user->removeReputation('best_answer_received', $post->bestComment);
        }

        foreach ($post->votes as $vote) {
            $author->removeReputation(
                $vote->value === 1 ? 'post_upvoted' : 'post_downvoted',
                $post
            );
        }
    }
}