<?php

namespace App\Observers;

use App\Models\Article;
use App\Services\NotificationService;

class ArticleObserver
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Handle the Article "created" event.
     */
    public function created(Article $article): void
    {
        // Only create notifications for published articles
        if ($article->status === 'published' && $article->published_at) {
            $this->notificationService->notifyNewArticle($article);
        }
    }

    /**
     * Handle the Article "updated" event.
     */
    public function updated(Article $article): void
    {
        // If article was just published (status changed to published)
        if ($article->isDirty('status') && $article->status === 'published' && $article->published_at) {
            $this->notificationService->notifyNewArticle($article);
        }
    }
}
