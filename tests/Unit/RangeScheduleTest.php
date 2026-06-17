<?php

use App\Models\RangeSchedule;

test('it calculates the first saturday in the second quarter of 2026', function () {
    $schedule = new RangeSchedule([
        'year' => 2026,
        'quarter' => 2,
        'week_number' => 1,
        'day_of_week' => 'zaterdag',
    ]);

    expect($schedule->schedule_date->toDateString())->toBe('2026-04-04')
        ->and($schedule->schedule_date_label)->toBe('Zaterdag 4 april 2026');
});

test('it calculates subsequent quarter weeks from the first matching weekday', function () {
    $schedule = new RangeSchedule([
        'year' => 2026,
        'quarter' => 2,
        'week_number' => 2,
        'day_of_week' => 'zaterdag',
    ]);

    expect($schedule->schedule_date->toDateString())->toBe('2026-04-11')
        ->and($schedule->schedule_date_label)->toBe('Zaterdag 11 april 2026');
});
