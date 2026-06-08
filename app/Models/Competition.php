<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competition extends Model
{
    use HasFactory;

    protected $table = 'competitions';

    protected $fillable = [
        'jaar',
        'naam',
        'beschrijving',
        'status',
    ];

    protected $casts = [
        'jaar' => 'integer',
    ];

    /**
     * Get all rounds for this competition (automatically 5)
     */
    public function rounds(): HasMany
    {
        return $this->hasMany(CompetitionRound::class, 'competition_id')
            ->orderBy('round_number');
    }

    /**
     * Get all registrations for this competition
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(CompetitionRegistration::class, 'competition_id');
    }

    /**
     * Get active registrations
     */
    public function activeRegistrations(): HasMany
    {
        return $this->hasMany(CompetitionRegistration::class, 'competition_id')
            ->where('status', 'actief');
    }

    /**
     * Get all scores across all rounds
     */
    public function allScores()
    {
        return $this->hasManyThrough(
            CompetitionScore::class,
            CompetitionRound::class,
            'competition_id',
            'competition_round_id'
        )->with('user', 'round');
    }

    /**
     * Get participants in this competition
     */
    public function participants()
    {
        return $this->belongsToMany(
            User::class,
            'competition_registrations',
            'competition_id',
            'user_id'
        )->where('competition_registrations.status', 'actief');
    }

    /**
     * Check if a user is registered for this competition
     */
    public function hasUser($userId): bool
    {
        return $this->registrations()
            ->where('user_id', $userId)
            ->where('status', 'actief')
            ->exists();
    }

    /**
     * Get the year as display string
     */
    public function getYearDisplayAttribute(): string
    {
        return "Competitie {$this->jaar}";
    }

    /**
     * Scope to get competitions for a specific year
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('jaar', $year);
    }

    /**
     * Scope to get active competitions
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['gepland', 'bezig']);
    }
}
