<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MembershipApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'voornaam',
        'achternaam',
        'email',
        'telefoonnummer',
        'geboortedatum',
        'leeftijd',
        'status',
        'opmerkingen',
        'aangemeld_op',
    ];

    protected $casts = [
        'geboortedatum' => 'date',
        'aangemeld_op' => 'datetime',
    ];

    // Automatically calculate age when setting birth date
    public function setGeboortedatumAttribute($value)
    {
        $this->attributes['geboortedatum'] = $value;
        
        if ($value) {
            $birthDate = Carbon::parse($value);
            $this->attributes['leeftijd'] = $birthDate->age;
        }
    }

    // Accessor for full name
    public function getVolledigeNaamAttribute()
    {
        return $this->voornaam . ' ' . $this->achternaam;
    }

    // Status labels in Dutch
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'nieuw' => 'Nieuw',
            'in_behandeling' => 'In behandeling',
            'goedgekeurd' => 'Goedgekeurd',
            'afgewezen' => 'Afgewezen',
            default => $this->status,
        };
    }

    // Scope for filtering by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope for new applications
    public function scopeNieuw($query)
    {
        return $query->where('status', 'nieuw');
    }
}
