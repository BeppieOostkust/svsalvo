<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'user_id',
        'status',
        'caliber',
        'notes',
        'registered_at',
        'paid_amount',
        'payment_confirmed',
        'converted_to_participant',
        'additional_data',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'paid_amount' => 'decimal:2',
        'payment_confirmed' => 'boolean',
        'converted_to_participant' => 'boolean',
        'additional_data' => 'array',
    ];

    /**
     * Get the match that this registration belongs to
     */
    public function match(): BelongsTo
    {
        return $this->belongsTo(Matches::class, 'match_id');
    }

    /**
     * Get the user that made this registration
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for confirmed registrations
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'bevestigd');
    }

    /**
     * Scope for pending registrations
     */
    public function scopePending($query)
    {
        return $query->where('status', 'aangemeld');
    }

    /**
     * Scope for non-converted registrations
     */
    public function scopeNotConverted($query)
    {
        return $query->where('converted_to_participant', false);
    }

    /**
     * Convert this registration to a MatchGebruikerScore participant
     */
    public function convertToParticipant(): MatchGebruikerScore
    {
        // Create a new MatchGebruikerScore entry
        $participant = MatchGebruikerScore::create([
            'wedstrijd_id' => $this->match_id,
            'gebruiker_id' => $this->user_id,
            'kaliber' => $this->caliber,
            'linker_kaart_6' => 0,
            'linker_kaart_7' => 0,
            'linker_kaart_8' => 0,
            'linker_kaart_9' => 0,
            'linker_kaart_10' => 0,
            'rechter_kaart_6' => 0,
            'rechter_kaart_7' => 0,
            'rechter_kaart_8' => 0,
            'rechter_kaart_9' => 0,
            'rechter_kaart_10' => 0,
            'aantal_schoten_buiten_tijd' => 0,
            'afwaarderingen' => 0,
            'totale_punten' => 0,
        ]);

        // Mark this registration as converted
        $this->update(['converted_to_participant' => true]);

        return $participant;
    }
}
