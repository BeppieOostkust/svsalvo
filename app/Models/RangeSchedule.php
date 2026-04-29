<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RangeSchedule extends Model
{
    use HasFactory;

    protected $table = 'range_schedule_weeks';

    protected $fillable = [
        'year',
        'quarter',
        'week_number',
        'discipline',
        'day_of_week',
        'is_open',
        'start_time',
        'end_time',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
        'quarter' => 'integer',
        'week_number' => 'integer',
        'is_open' => 'boolean',
    ];

    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }

    public function scopeOrdered($query)
    {
        return $query
            ->orderByDesc('year')
            ->orderByDesc('quarter')
            ->orderBy('week_number');
    }

    public function getQuarterLabelAttribute(): string
    {
        return sprintf('Q%d %d', $this->quarter, $this->year);
    }

    public function getDisciplineLabelAttribute(): string
    {
        return match ($this->discipline) {
            'geweer' => 'Geweer',
            default => 'Pistool',
        };
    }

    public function getDayOfWeekLabelAttribute(): string
    {
        return match ($this->day_of_week) {
            'dinsdag' => 'Dinsdag',
            'woensdag' => 'Woensdag',
            'donderdag' => 'Donderdag',
            'vrijdag' => 'Vrijdag',
            'zaterdag' => 'Zaterdag',
            'zondag' => 'Zondag',
            default => 'Maandag',
        };
    }
}
