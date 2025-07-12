<?php

namespace App\Observers;

use App\Models\Matches;
use App\Services\NotificationService;

class MatchesObserver
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Handle the Matches "created" event.
     */
    public function created(Matches $matches): void
    {
        // Create notifications for new matches
        $this->notificationService->notifyNewMatch($matches);
    }
}
