<?php

namespace App\Models;

use Carbon\CarbonImmutable;
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

    public function getScheduleDateAttribute(): CarbonImmutable
    {
        $quarterStart = CarbonImmutable::create($this->year, (($this->quarter - 1) * 3) + 1, 1);
        $targetDay = $this->dayOfWeekNumber();
        $daysUntilTarget = ($targetDay - $quarterStart->dayOfWeek + 7) % 7;

        return $quarterStart
            ->addDays($daysUntilTarget)
            ->addWeeks($this->week_number - 1);
    }

    public function getScheduleDateLabelAttribute(): string
    {
        return sprintf(
            '%s %s',
            $this->day_of_week_label,
            $this->schedule_date->locale('nl')->translatedFormat('j F Y')
        );
    }

    private function dayOfWeekNumber(): int
    {
        return match ($this->day_of_week) {
            'maandag' => CarbonImmutable::MONDAY,
            'dinsdag' => CarbonImmutable::TUESDAY,
            'woensdag' => CarbonImmutable::WEDNESDAY,
            'donderdag' => CarbonImmutable::THURSDAY,
            'vrijdag' => CarbonImmutable::FRIDAY,
            'zaterdag' => CarbonImmutable::SATURDAY,
            'zondag' => CarbonImmutable::SUNDAY,
            default => CarbonImmutable::MONDAY,
        };
    }
}
