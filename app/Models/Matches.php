<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'naam',
        'beschrijving',
        'status',
        'start_datum',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'start_datum' => 'datetime',
    ];

    public function gebruikersScores()
    {
        return $this->hasMany(MatchGebruikerScore::class, 'wedstrijd_id')
            ->orderBy('round_number')
            ->orderByRaw("CASE WHEN kaliber = 'kkp' THEN 1 WHEN kaliber = 'gkp' THEN 2 ELSE 3 END")
            ->orderByDesc('totale_punten');
    }
    
    public function matchUserScores()
    {
        return $this->hasMany(MatchGebruikerScore::class, 'wedstrijd_id');
    }

    /**
     * Get all registrations for this match
     */
    public function registrations()
    {
        return $this->hasMany(MatchRegistration::class, 'match_id');
    }

    /**
     * Get pending registrations that haven't been converted to participants
     */
    public function pendingRegistrations()
    {
        return $this->hasMany(MatchRegistration::class, 'match_id')
            ->where('converted_to_participant', false)
            ->whereIn('status', ['aangemeld', 'bevestigd']);
    }

    /**
     * Get confirmed registrations
     */
    public function confirmedRegistrations()
    {
        return $this->hasMany(MatchRegistration::class, 'match_id')
            ->where('status', 'bevestigd');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'match_gebruikers_scores', 'wedstrijd_id', 'gebruiker_id')->withPivot('points');
    }
}