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
        'baan_nummer',
        'round_number',
        'is_official',
        'wedstrijd_id',
        'gebruiker_id',
        'linker_kaart_5',
        'linker_kaart_6',
        'linker_kaart_7',
        'linker_kaart_8',
        'linker_kaart_9',
        'linker_kaart_10',
        'rechter_kaart_5',
        'rechter_kaart_6',
        'rechter_kaart_7',
        'rechter_kaart_8',
        'rechter_kaart_9',
        'rechter_kaart_10',
        'aantal_schoten_buiten_tijd',
        'afwaarderingen',
        'totale_punten',
        'notes',
    ];

    protected $casts = [
        'baan_nummer' => 'integer',
        'round_number' => 'integer',
        'is_official' => 'boolean',
        'linker_kaart_5' => 'integer',
        'linker_kaart_6' => 'integer',
        'linker_kaart_7' => 'integer',
        'linker_kaart_8' => 'integer',
        'linker_kaart_9' => 'integer',
        'linker_kaart_10' => 'integer',
        'rechter_kaart_5' => 'integer',
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
     * Alias for matches() - Nederlandse naam
     */
    public function wedstrijd(): BelongsTo
    {
        return $this->matches();
    }

    /**
     * Get the user that owns this score
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gebruiker_id');
    }

    public function gebruiker(): BelongsTo
    {
        return $this->user();
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

    /**
     * Scope to get only official scores (the ones that count for rankings)
     */
    public function scopeOfficial($query)
    {
        return $query->where('is_official', true);
    }
    
    /**
     * Scope to get scores for a specific round
     */
    public function scopeRound($query, $roundNumber)
    {
        return $query->where('round_number', $roundNumber);
    }
    
    /**
     * Get the round name for display
     */
    public function getRoundNameAttribute()
    {
        $roundNames = [
            1 => '1e Serie',
            2 => '2e Serie', 
            3 => '3e Serie',
            4 => '4e Serie'
        ];
        
        return $roundNames[$this->round_number] ?? "Serie {$this->round_number}";
    }
    
    /**
     * Check if this is the official score for this player/caliber combination
     */
    public function getIsOfficialScoreAttribute()
    {
        return $this->is_official;
    }
}