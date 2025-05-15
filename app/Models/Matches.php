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

    public function gebruikersScores()
    {
        return $this->hasMany(MatchGebruikerScore::class, 'wedstrijd_id')
            ->orderByRaw("CASE WHEN kaliber = 'gkp' THEN 0 WHEN kaliber = 'kkp' THEN 1 ELSE 2 END")
            ->orderByDesc('totale_punten')
            ->orderByRaw('linker_kaart_6 + linker_kaart_7 + linker_kaart_8 + linker_kaart_9 + linker_kaart_10 DESC');
    }
    public function matchUserScores()
    {
        return $this->hasMany(MatchGebruikerScore::class, 'wedstrijd_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'match_gebruikers_scores', 'wedstrijd_id', 'gebruiker_id')->withPivot('points');
    }
}