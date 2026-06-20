<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Support\PublicStorage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
    /**
     * Display a listing of activities
     */
    public function index()
    {
        $upcomingActivities = Activity::with(['organizer'])
            ->whereIn('status', ['gepland', 'bevestigd'])
            ->where('start_date', '>=', now()->subDay()->startOfDay())
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(fn (Activity $activity) => PublicStorage::expose($activity, 'featured_image'));

        $pastActivities = Activity::with(['organizer'])
            ->where('status', 'afgelopen')
            ->orderBy('start_date', 'desc')
            ->limit(6)
            ->get()
            ->map(fn (Activity $activity) => PublicStorage::expose($activity, 'featured_image'));

        return Inertia::render('Activiteiten', [
            'upcomingActivities' => $upcomingActivities,
            'pastActivities' => $pastActivities,
        ]);
    }

    /**
     * Display a single activity
     */
    public function show($slug)
    {
        $activity = Activity::with(['organizer', 'registrations.user'])
            ->where('slug', $slug)
            ->firstOrFail();
        PublicStorage::expose($activity, 'featured_image');

        $userRegistration = null;
        if (auth()->user()) {
            $userRegistration = ActivityRegistration::where('activity_id', $activity->id)
                ->where('user_id', auth()->id())
                ->first();
        }

        return Inertia::render('ActivityDetail', [
            'activity' => $activity,
            'userRegistration' => $userRegistration,
        ]);
    }

    /**
     * Register for an activity
     */
    public function register(Request $request, $slug)
    {
        if (!auth()->user()) {
            return redirect()->route('login')->with('error', 'Je moet ingelogd zijn om je aan te melden.');
        }

        $activity = Activity::where('slug', $slug)->firstOrFail();

        // Check if registration is allowed
        if (!$activity->requires_registration) {
            return back()->with('error', 'Voor deze activiteit is geen aanmelding vereist.');
        }

        if ($activity->registration_deadline && now() > $activity->registration_deadline) {
            return back()->with('error', 'De aanmeldperiode is verlopen.');
        }

        if ($activity->max_participants && $activity->current_participants >= $activity->max_participants) {
            return back()->with('error', 'Deze activiteit is vol.');
        }

        // Check if already registered
        $existingRegistration = ActivityRegistration::where('activity_id', $activity->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingRegistration) {
            return back()->with('error', 'Je bent al aangemeld voor deze activiteit.');
        }

        // Create registration
        ActivityRegistration::create([
            'activity_id' => $activity->id,
            'user_id' => auth()->id(),
            'status' => 'aangemeld',
            'registered_at' => now(),
            'paid_amount' => $activity->entry_fee,
            'payment_confirmed' => $activity->entry_fee == 0, // Free activities are automatically confirmed
        ]);

        // Increment participant count
        $activity->increment('current_participants');

        return back()->with('success', 'Je bent succesvol aangemeld voor deze activiteit!');
    }

    /**
     * Unregister from an activity
     */
    public function unregister($slug)
    {
        if (!auth()->user()) {
            return redirect()->route('login');
        }

        $activity = Activity::where('slug', $slug)->firstOrFail();

        $registration = ActivityRegistration::where('activity_id', $activity->id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$registration) {
            return back()->with('error', 'Je bent niet aangemeld voor deze activiteit.');
        }

        $registration->delete();
        $activity->decrement('current_participants');

        return back()->with('success', 'Je bent afgemeld voor deze activiteit.');
    }
}
