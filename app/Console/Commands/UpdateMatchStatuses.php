<?php

namespace App\Console\Commands;

use App\Models\Matches;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateMatchStatuses extends Command
{
    protected $signature = 'matches:update-statuses';
    protected $description = 'Update de status van wedstrijden op basis van hun startdatum';

    public function handle()
    {
        $now = Carbon::now();
        
        // Vind alle wedstrijden die nog niet zijn afgelopen of geannuleerd
        $matches = Matches::whereNotIn('status', ['afgelopen', 'geannuleerd'])
            ->where('start_datum', '<=', $now)
            ->where('status', '!=', 'bezig')
            ->get();

        $updatedCount = 0;
        
        foreach ($matches as $match) {
            $match->status = 'bezig';
            $match->save();
            $updatedCount++;
        }

        if ($updatedCount > 0) {
            $this->info("{$updatedCount} wedstrijd(en) bijgewerkt naar status 'bezig'");
        } else {
            $this->info("Geen wedstrijden gevonden die bijgewerkt moeten worden");
        }
    }
} 