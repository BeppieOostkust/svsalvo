<?php

namespace App\Observers;

use App\Models\Activity;
use App\Services\NotificationService;

class ActivityObserver
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Handle the Activity "created" event.
     */
    public function created(Activity $activity): void
    {
        // Only create notifications for published activities
        if ($activity->status === 'published') {
            $this->notificationService->notifyNewActivity($activity);
        }
    }

    /**
     * Handle the Activity "updated" event.
     */
    public function updated(Activity $activity): void
    {
        // If activity was just published (status changed to published)
        if ($activity->isDirty('status') && $activity->status === 'published') {
            $this->notificationService->notifyNewActivity($activity);
        }
    }
}
