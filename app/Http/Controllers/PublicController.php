<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Activity;
use App\Models\Matches;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicController extends Controller
{
    public function index()
    {
        // Check if user is already authenticated
        if (auth()->check()) {
            // Redirect authenticated users to their authenticated homepage
            return redirect()->route('dashboard.home');
        }

        // Get some public preview content to showcase the organization
        $featuredNews = Article::with(['author'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        // Get upcoming public activities (non-member activities or general info)
        $upcomingActivities = Activity::with(['organizer'])
            ->where('start_date', '>=', now())
            ->where('status', '!=', 'geannuleerd')
            ->whereIn('type', ['evenement', 'toernooi', 'cursus']) // Show public-friendly activities
            ->orderBy('start_date', 'asc')
            ->limit(3)
            ->get();

        // Get some basic organization stats
        $stats = [
            'established_year' => '1967',
            'member_count' => '45+',
            'disciplines' => [
                'Luchtdruk pistool',
                'Klein kaliber pistool', 
                'Groot kaliber pistool',
                'Luchtbuks',
                'Klein kaliber geweer'
            ]
        ];

        return Inertia::render('PublicHome', [
            'featuredNews' => $featuredNews,
            'upcomingActivities' => $upcomingActivities,
            'stats' => $stats,
        ]);
    }
}
