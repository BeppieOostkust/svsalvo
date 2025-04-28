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
        'kaliber',
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

    protected $casts = [
        'linker_kaart_6' => 'integer',
        'linker_kaart_7' => 'integer',
        'linker_kaart_8' => 'integer',
        'linker_kaart_9' => 'integer',
        'linker_kaart_10' => 'integer',
        'rechter_kaart_6' => 'integer',
        'rechter_kaart_7' => 'integer',
        'rechter_kaart_8' => 'integer',
        'rechter_kaart_9' => 'integer',
        'rechter_kaart_10' => 'integer',
        'aantal_schoten_buiten_tijd' => 'integer',
        'afwaarderingen' => 'integer',
        'totale_punten' => 'integer',
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
        static::saving(function ($score) {
            // Recalculate the total points before saving
            $score->totale_punten = 
                ($score->linker_kaart_6 * 6) +
                ($score->linker_kaart_7 * 7) +
                ($score->linker_kaart_8 * 8) +
                ($score->linker_kaart_9 * 9) +
                ($score->linker_kaart_10 * 10) +
                ($score->rechter_kaart_6 * 6) +
                ($score->rechter_kaart_7 * 7) +
                ($score->rechter_kaart_8 * 8) +
                ($score->rechter_kaart_9 * 9) +
                ($score->rechter_kaart_10 * 10) -
                ($score->aantal_schoten_buiten_tijd * 2) -
                $score->afwaarderingen;
        });
    }
}