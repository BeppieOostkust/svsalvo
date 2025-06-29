<?php

namespace App\Observers;

use App\Models\Matches;
use Carbon\Carbon;

class MatchObserver
{
    /**
     * Handle the Matches "saving" event.
     */
    public function saving(Matches $match): void
    {
        // Als de wedstrijd nog niet is afgelopen of geannuleerd
        if (!in_array($match->status, ['afgelopen', 'geannuleerd'])) {
            $now = Carbon::now();
            $startDate = Carbon::parse($match->start_datum);

            // Als de startdatum is verstreken, zet status op 'bezig'
            if ($now->greaterThan($startDate)) {
                $match->status = 'bezig';
            }
        }
    }
} 