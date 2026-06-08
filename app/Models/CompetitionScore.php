<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionScore extends Model
{
    use HasFactory;

    protected $table = 'competition_scores';

    protected $fillable = [
        'competition_round_id',
        'registration_id',
        'user_id',
        'kaliber',
        'linker_score',
        'rechter_score',
        'totale_punten',
        'notes',
    ];

    protected $casts = [
        'linker_score' => 'integer',
        'rechter_score' => 'integer',
        'totale_punten' => 'integer',
    ];

    /**
     * Get the competition round this score belongs to
     */
    public function round(): BelongsTo
    {
        return $this->belongsTo(CompetitionRound::class, 'competition_round_id');
    }

    /**
     * Get the user this score belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias for user() - Nederlandse naam
     */
    public function gebruiker(): BelongsTo
    {
        return $this->user();
    }

    /**
     * Get the registration (competition registration) for this score
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(CompetitionRegistration::class, 'registration_id');
    }

    /**
     * Auto-calculate total before saving
     */
    protected static function booted()
    {
        static::saving(function ($score) {
            // Calculate grand total (left + right)
            $score->totale_punten = $score->linker_score + $score->rechter_score;

            // Ensure totale_punten never goes below 0
            if ($score->totale_punten < 0) {
                $score->totale_punten = 0;
            }
        });
    }

    /**
     * Get display name for caliber
     */
    public function getCaliberDisplayAttribute(): string
    {
        return match ($this->kaliber) {
            'meesterkaart_zwaar' => 'Meesterkaart zwaar',
            'meesterkaart_licht' => 'Meesterkaart licht',
            'kk_geweer_open_50m' => 'KK geweer open richtmiddelen 50m',
            'kk_geweer_optisch_100m' => 'KK geweer optisch 100m',
            'gk_precision_100m' => 'Groot kaliber precisiegeweer target 100m',
            'militair_geweer' => 'Militair geweer',
            'militair_geweer_optisch' => 'Militair geweer optisch',
            'veteranen_geweer' => 'Veteranen geweer',
            default => $this->kaliber,
        };
    }

    /**
     * Scope to filter by caliber
     */
    public function scopeByCaliber($query, $caliber)
    {
        return $query->where('kaliber', $caliber);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by round
     */
    public function scopeByRound($query, $roundId)
    {
        return $query->where('competition_round_id', $roundId);
    }

    /**
     * Scope to get scores ordered by points descending
     */
    public function scopeOrderedByScore($query)
    {
        return $query->orderByDesc('totale_punten');
    }

    /**
     * Get total points across both cards
     */
    public function getTotalPointsAttribute(): int
    {
        return $this->totale_punten;
    }

    /**
     * Format score for display
     */
    public function getFormattedScoreAttribute(): string
    {
        return sprintf(
            "%d + %d = %d",
            $this->linker_score,
            $this->rechter_score,
            $this->totale_punten
        );
    }
}
