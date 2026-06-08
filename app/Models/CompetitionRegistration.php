<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionRegistration extends Model
{
    use HasFactory;

    protected $table = 'competition_registrations';

    protected $fillable = [
        'competition_id',
        'user_id',
        'kaliber',
        'status',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the competition this registration belongs to
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }

    /**
     * Get the user this registration belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user via gebruiker alias (for compatibility)
     */
    public function gebruiker(): BelongsTo
    {
        return $this->user();
    }

    /**
     * Scope to get active registrations only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'actief');
    }

    /**
     * Scope to get registrations for a specific competition
     */
    public function scopeForCompetition($query, $competitionId)
    {
        return $query->where('competition_id', $competitionId);
    }

    /**
     * Scope to get registrations for a specific caliber
     */
    public function scopeForCaliber($query, $caliber)
    {
        return $query->where('kaliber', $caliber);
    }

    /**
     * Check if user is actively registered
     */
    public function isActive(): bool
    {
        return $this->status === 'actief';
    }

    /**
     * Activate registration
     */
    public function activate(): void
    {
        $this->update(['status' => 'actief']);
    }

    /**
     * Deactivate registration
     */
    public function deactivate(): void
    {
        $this->update(['status' => 'inactief']);
    }

    /**
     * Mark as unregistered
     */
    public function unregister(): void
    {
        $this->update(['status' => 'afgemeld']);
    }
}
