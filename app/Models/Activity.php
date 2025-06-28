<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'location',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'type',
        'status',
        'max_participants',
        'current_participants',
        'entry_fee',
        'requires_registration',
        'registration_deadline',
        'requirements',
        'contact_info',
        'featured_image',
        'additional_info',
        'organizer_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_deadline' => 'datetime',
        'entry_fee' => 'decimal:2',
        'requires_registration' => 'boolean',
        'max_participants' => 'integer',
        'current_participants' => 'integer',
        'additional_info' => 'array',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(ActivityRegistration::class);
    }

    public function confirmedRegistrations(): HasMany
    {
        return $this->hasMany(ActivityRegistration::class)->where('status', 'bevestigd');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Helper methods
    public function hasAvailableSpots()
    {
        if (!$this->max_participants) {
            return true;
        }
        
        return $this->current_participants < $this->max_participants;
    }

    public function isRegistrationOpen()
    {
        if (!$this->requires_registration) {
            return false;
        }

        if ($this->registration_deadline && $this->registration_deadline < now()) {
            return false;
        }

        return $this->hasAvailableSpots();
    }
}
