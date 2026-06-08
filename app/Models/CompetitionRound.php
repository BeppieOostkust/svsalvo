<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionRound extends Model
{
    use HasFactory;

    protected $table = 'competition_rounds';

    protected $fillable = [
        'competition_id',
        'round_number',
        'naam',
        'datum',
        'beschrijving',
    ];

    protected $casts = [
        'round_number' => 'integer',
        'datum' => 'date',
    ];

    /**
     * Get the competition this round belongs to
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }

    /**
     * Get all scores for this round
     */
    public function scores(): HasMany
    {
        return $this->hasMany(CompetitionScore::class, 'competition_round_id')
            ->orderBy('kaliber')
            ->orderByDesc('totale_punten');
    }

    /**
     * Get the round display name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->naam ?? "Beurt {$this->round_number}";
    }

    /**
     * Get scores for a specific caliber
     */
    public function scoresForCaliber($caliber): HasMany
    {
        return $this->hasMany(CompetitionScore::class, 'competition_round_id')
            ->where('kaliber', $caliber)
            ->orderByDesc('totale_punten');
    }

    /**
     * Check if this round has scores entered
     */
    public function hasScores(): bool
    {
        return $this->scores()->exists();
    }

    /**
     * Get score count
     */
    public function getScoreCountAttribute(): int
    {
        return $this->scores()->count();
    }
}
