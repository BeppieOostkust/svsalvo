<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Activity;
use App\Models\Matches;
use App\Support\PublicStorage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        // Get latest news (published articles)
        $latestNews = Article::with(['author'])
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get()
            ->map(fn (Article $article) => PublicStorage::expose($article, 'featured_image'));

        // Get featured news
        $featuredNews = Article::with(['author'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->orderBy('published_at', 'desc')
            ->first();
        if ($featuredNews) {
            PublicStorage::expose($featuredNews, 'featured_image');
        }

        // Get upcoming activities
        $upcomingActivities = Activity::with(['organizer'])
            ->where('start_date', '>=', now())
            ->where('status', '!=', 'geannuleerd')
            ->orderBy('start_date', 'asc')
            ->limit(4)
            ->get()
            ->map(fn (Activity $activity) => PublicStorage::expose($activity, 'featured_image'));

        // Get upcoming matches - handle potential database issues gracefully
        try {
            $upcomingMatches = Matches::where('start_datum', '>=', now())
                ->where('status', '!=', 'geannuleerd')
                ->orderBy('start_datum', 'asc')
                ->limit(4)
                ->get();
        } catch (\Exception $e) {
            // If matches table has issues, provide empty collection
            $upcomingMatches = collect([]);
        }

        return Inertia::render('Home', [
            'latestNews' => $latestNews,
            'featuredNews' => $featuredNews,
            'upcomingActivities' => $upcomingActivities,
            'upcomingMatches' => $upcomingMatches,
        ]);
    }
}
