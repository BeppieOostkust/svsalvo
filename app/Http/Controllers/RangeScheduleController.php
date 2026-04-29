<?php

namespace App\Http\Controllers;

use App\Models\RangeSchedule;
use Inertia\Inertia;

class RangeScheduleController extends Controller
{
    public function index()
    {
        $items = RangeSchedule::query()
            ->ordered()
            ->get();

        $quarters = $items
            ->groupBy(fn (RangeSchedule $item) => $item->quarter_label)
            ->map(function ($group, $quarterLabel) {
                return [
                    'quarter_label' => $quarterLabel,
                    'items' => $group->values()->map(fn (RangeSchedule $item) => [
                        'id' => $item->id,
                        'week_number' => $item->week_number,
                        'discipline' => $item->discipline,
                        'discipline_label' => $item->discipline_label,
                        'day_of_week' => $item->day_of_week,
                        'day_of_week_label' => $item->day_of_week_label,
                        'is_open' => $item->is_open,
                        'start_time' => $item->start_time,
                        'end_time' => $item->end_time,
                        'notes' => $item->notes,
                    ]),
                ];
            })
            ->values();

        return Inertia::render('Baanplanning/Index', [
            'quarters' => $quarters,
        ]);
    }
}
