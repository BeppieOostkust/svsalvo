<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Activity;
use App\Models\Matches;
use App\Models\Setting;
use App\Support\PublicStorage;
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
            ->get()
            ->map(fn (Article $article) => PublicStorage::expose($article, 'featured_image'));

        // Get upcoming public activities (non-member activities or general info)
        $upcomingActivities = Activity::with(['organizer'])
            ->where('start_date', '>=', now())
            ->where('status', '!=', 'geannuleerd')
            ->whereIn('type', ['evenement', 'toernooi', 'cursus']) // Show public-friendly activities
            ->orderBy('start_date', 'asc')
            ->limit(3)
            ->get()
            ->map(fn (Activity $activity) => PublicStorage::expose($activity, 'featured_image'));

        // Get some basic organization stats
        $stats = [
            'established_year' => '1967',
            'member_count' => '45+',
            'disciplines' => [
                'Meesterkaart zwaar',
                'Meesterkaart Licht', 
                'KK Geweer Open richtm. 50 meter',
                'KK Geweer Optisch 100 meter',
                'Gr.Kal.Precisie Geweer Target 100 m',
                'Militair Geweer Optisch 100 meter',
                'Militair Geweer',
                'Veteranen Geweer',
            ]
        ];

        return Inertia::render('PublicHome', [
            'featuredNews' => $featuredNews,
            'upcomingActivities' => $upcomingActivities,
            'stats' => $stats,
        ]);
    }
}
