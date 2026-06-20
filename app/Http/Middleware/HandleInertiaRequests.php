<?php

namespace App\Http\Middleware;

use App\Models\Article;
use App\Support\PublicStorage;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        // Get urgent articles with error handling
        try {
            $urgentArticles = Article::with(['author'])
                ->published()
                ->urgent()
                ->orderBy('published_at', 'desc')
                ->limit(3)
                ->get(['id', 'title', 'excerpt', 'slug', 'published_at', 'author_id', 'featured_image'])
                ->map(fn (Article $article) => PublicStorage::expose($article, 'featured_image'));
        } catch (\Exception $e) {
            // If there's any database error, return empty array and log it
            \Log::warning('Failed to fetch urgent articles: ' . $e->getMessage());
            $urgentArticles = [];
        }

        // Get user notifications if authenticated
        $notifications = [];
        if ($request->user()) {
            try {
                $notifications = $request->user()
                    ->notifications()
                    ->limit(10)
                    ->get(['id', 'type', 'title', 'message', 'data', 'read_at', 'created_at']);
            } catch (\Exception $e) {
                \Log::warning('Failed to fetch notifications: ' . $e->getMessage());
                $notifications = [];
            }
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'urgentArticles' => $urgentArticles,
            'notifications' => $notifications,
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => fn (): array => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ]
        ];
    }
}
