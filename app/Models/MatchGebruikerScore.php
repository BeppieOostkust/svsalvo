<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchGebruikerScore extends Model
{
    use HasFactory;

    protected $table = 'match_gebruikers_scores';

    protected $fillable = [
        'wedstrijd_id',
        'gebruiker_id',
        'linker_kaart_6',
        'linker_kaart_7',
        'linker_kaart_8',
        'linker_kaart_9',
        'linker_kaart_10',
        'rechter_kaart_6',
        'rechter_kaart_7',
        'rechter_kaart_8',
        'rechter_kaart_9',
        'rechter_kaart_10',
        'aantal_schoten_buiten_tijd',
        'afwaarderingen',
        'totale_punten',
    ];

    /**
     * Get the match that owns this score
     */
    public function matches(): BelongsTo
    {
        return $this->belongsTo(Matches::class, 'wedstrijd_id');
    }

    /**
     * Get the user that owns this score
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gebruiker_id');
    }
    
    /**
     * Auto-update total points before saving
     */
    protected static function booted()
    {
        static::saving(function ($matchUserScore) {
            $matchUserScore->totale_punten = 
                (int)$matchUserScore->linker_kaart_6 +
                (int)$matchUserScore->linker_kaart_7 +
                (int)$matchUserScore->linker_kaart_8 +
                (int)$matchUserScore->linker_kaart_9 +
                (int)$matchUserScore->linker_kaart_10 +
                (int)$matchUserScore->rechter_kaart_6 +
                (int)$matchUserScore->rechter_kaart_7 +
                (int)$matchUserScore->rechter_kaart_8 +
                (int)$matchUserScore->rechter_kaart_9 +
                (int)$matchUserScore->rechter_kaart_10 -
                ((int)$matchUserScore->aantal_schoten_buiten_tijd * 2) -
                (int)$matchUserScore->afwaarderingen;
        });
    }
}