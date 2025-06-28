<?php

namespace App\Observers;

use App\Models\ActivityRegistration;

class ActivityRegistrationObserver
{
    /**
     * Handle the ActivityRegistration "created" event.
     */
    public function created(ActivityRegistration $activityRegistration): void
    {
        $this->updateParticipantCount($activityRegistration);
    }

    /**
     * Handle the ActivityRegistration "updated" event.
     */
    public function updated(ActivityRegistration $activityRegistration): void
    {
        $this->updateParticipantCount($activityRegistration);
    }

    /**
     * Handle the ActivityRegistration "deleted" event.
     */
    public function deleted(ActivityRegistration $activityRegistration): void
    {
        $this->updateParticipantCount($activityRegistration);
    }

    /**
     * Handle the ActivityRegistration "restored" event.
     */
    public function restored(ActivityRegistration $activityRegistration): void
    {
        $this->updateParticipantCount($activityRegistration);
    }

    /**
     * Handle the ActivityRegistration "force deleted" event.
     */
    public function forceDeleted(ActivityRegistration $activityRegistration): void
    {
        $this->updateParticipantCount($activityRegistration);
    }

    /**
     * Update the current participants count for the activity
     */
    private function updateParticipantCount(ActivityRegistration $activityRegistration): void
    {
        $activity = $activityRegistration->activity;
        if ($activity) {
            $currentParticipants = $activity->confirmedRegistrations()->count();
            $activity->update(['current_participants' => $currentParticipants]);
        }
    }
}
