<?php

namespace App\Presenters;

use App\Models\{Notification, Post, Comment, Report, User, Tag};
use App\Enums\NotificationType;
use Illuminate\Support\Str;

class NotificationPresenter
{
    public function __construct(protected Notification $notification) {}

    /**
     * Formats the notification message for the UI.
     */
    public function message(): string
    {
        $actor = $this->actorName();

        return match ($this->notification->type) {
            NotificationType::REPORT_RESOLVED => 
                "A report you filed has been resolved. Thank you for maintaining the integrity of the community.",
            NotificationType::CONTENT_REMOVED => 
                "Notice: Your content was restricted due to guideline violations.",
            NotificationType::POST_COMMENTED => "{$actor} contributed a response to your discussion",
            NotificationType::COMMENT_REPLIED => "{$actor} replied to your response",
            NotificationType::POST_UPVOTED => "{$actor} upvoted your discussion",
            NotificationType::POST_DOWNVOTED => "{$actor} downvoted your discussion",
            NotificationType::COMMENT_UPVOTED => "{$actor} upvoted your response",
            NotificationType::COMMENT_DOWNVOTED => "{$actor} downvoted your response",
            NotificationType::AUTHORS_PICK_RECEIVED => "Your response was highlighted as the Author's Pick.",
            NotificationType::USER_FOLLOWED => "{$actor} is now following your academic updates",
            NotificationType::TAG_FOLLOWED => "{$actor} subscribed to a topic you originated",
            NotificationType::NEW_POST_FOLLOWING => "A scholar you follow published a new discussion",
            NotificationType::NEW_POST_TAG => "A new discussion was published under a topic you follow",
            default => $this->notification->message ?? Str::headline($this->notification->type->value),
        };
    }

    /**
     * Resolves the appropriate destination URL based on the polymorphic target.
     */
    public function url(): ?string
    {
        $type = $this->notification->type;
        $target = $this->notification->target;
        $actor = $this->notification->actor;

        if ($type === NotificationType::USER_FOLLOWED && $actor) {
            return route('profile.show', $actor->username);
        }

        if ($type === NotificationType::TAG_FOLLOWED && $target instanceof Tag) {
            return route('tags.show', $target->slug);
        }

        if ($target) {
            return match (true) {
                $target instanceof Post => route('posts.show', $target),
                $target instanceof Comment => $target->post 
                    ? route('posts.show', $target->post) . "#comment-{$target->id}"
                    : route('notifications.index'),
                $target instanceof User => route('profile.show', $target->username),
                $target instanceof Tag => route('tags.show', $target->slug),
                $target instanceof Report => route('notifications.index'),
                default => route('notifications.index'),
            };
        }

        return route('notifications.index');
    }

    protected function actorName(): string
    {
        return $this->notification->actor ? $this->notification->actor->username : 'System';
    }

    /**
     * Returns an SVG icon string based on the notification type.
     */
    public function icon(): string
    {
        $baseClasses = "w-5 h-5";

        return match ($this->notification->type) {
            NotificationType::REPORT_RESOLVED => 
                '<svg class="'.$baseClasses.' text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            
            NotificationType::CONTENT_REMOVED => 
                '<svg class="'.$baseClasses.' text-accent-warm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
            
            NotificationType::POST_COMMENTED, NotificationType::COMMENT_REPLIED => 
                '<svg class="'.$baseClasses.' text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>',
            
            NotificationType::POST_UPVOTED, NotificationType::COMMENT_UPVOTED => 
                '<svg class="'.$baseClasses.' text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>',
            
            NotificationType::POST_DOWNVOTED, NotificationType::COMMENT_DOWNVOTED => 
                '<svg class="'.$baseClasses.' text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>',
            
            NotificationType::AUTHORS_PICK_RECEIVED => 
                '<svg class="'.$baseClasses.' text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>',
            
            NotificationType::USER_FOLLOWED => 
                '<svg class="'.$baseClasses.' text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>',
            
            NotificationType::TAG_FOLLOWED => 
                '<svg class="'.$baseClasses.' text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>',
            
            NotificationType::NEW_POST_FOLLOWING, NotificationType::NEW_POST_TAG => 
                '<svg class="'.$baseClasses.' text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>',
            
            default => 
                '<svg class="'.$baseClasses.' text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>',
        };
    }
}