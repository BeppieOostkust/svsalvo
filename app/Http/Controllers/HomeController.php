<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Activity;
use App\Models\Matches;
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
            ->get();

        // Get featured news
        $featuredNews = Article::with(['author'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->orderBy('published_at', 'desc')
            ->first();

        // Get upcoming activities
        $upcomingActivities = Activity::with(['organizer'])
            ->where('start_date', '>=', now())
            ->where('status', '!=', 'geannuleerd')
            ->orderBy('start_date', 'asc')
            ->limit(4)
            ->get();

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

        // Get partners/sponsors data
        $partners = [
            [
                'name' => 'Wapenhandel van der Zanden',
                'logo' => '/images/partners/vanderzanden.svg',
                'website' => 'https://www.vanderzanden.nl',
                'description' => 'Jacht & Schietsport'
            ],
            [
                'name' => 'KNSA',
                'logo' => '/images/partners/knsa.svg',
                'website' => 'https://www.knsa.nl',
                'description' => 'Koninklijke Nederlandse Schietsport Associatie'
            ],
            [
                'name' => 'Vuurwapens.net',
                'logo' => '/images/partners/vuurwapens.svg',
                'website' => 'https://www.vuurwapens.net',
                'description' => 'Vuurwapen informatie en community'
            ],
            [
                'name' => 'Schietsport Centrum Tichelrij',
                'logo' => '/images/partners/tichelrij.svg',
                'website' => 'https://www.schietsportcentrumtichelrij.nl',
                'description' => 'Schietsport training en faciliteiten'
            ],
            [
                'name' => 'Wapen Advertenties',
                'logo' => '/images/partners/wapenadvertenties.svg',
                'website' => 'https://www.wapenadvertenties.nl',
                'description' => 'Marktplaats voor wapens en accessoires'
            ],
            [
                'name' => 'MH Schietsport',
                'logo' => '/images/partners/mh-schietsport.svg',
                'website' => 'https://www.mhschietsport.nl',
                'description' => 'Schietsport artikelen en onderdelen'
            ]
        ];

        return Inertia::render('Home', [
            'latestNews' => $latestNews,
            'featuredNews' => $featuredNews,
            'upcomingActivities' => $upcomingActivities,
            'upcomingMatches' => $upcomingMatches,
            'partners' => $partners,
        ]);
    }
}
