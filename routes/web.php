<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Match\WedstrijdenController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\MembershipApplicationController;
use App\Http\Controllers\PublicScoresController;
use App\Http\Controllers\MemberContactController;

// Public homepage - accessible to everyone, invites visitors to join
Route::get('/', [PublicController::class, 'index'])->name('home');

// Membership application routes
Route::post('/lidmaatschap-aanvragen', [MembershipApplicationController::class, 'store'])->name('membership.apply');
Route::get('/lidmaatschap-succes', [MembershipApplicationController::class, 'success'])->name('membership.success');
Route::get('/lidmaatschap-gesloten', function () {
    return Inertia::render('membership/closed');
})->name('membership.closed');

// Test route for urgent banner functionality
Route::get('/test-urgent', function () {
    return Inertia::render('test-urgent');
})->name('test-urgent')->middleware('auth');

// Debug route for file upload testing
Route::post('/debug-file', function (Request $request) {
    if ($request->hasFile('test_file')) {
        $file = $request->file('test_file');
        return response()->json([
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
            'is_valid' => $file->isValid(),
            'real_path' => $file->getRealPath(),
        ]);
    }
    return response()->json(['error' => 'No file uploaded']);
})->middleware('auth');

// Debug route to check urgent articles
Route::get('/debug-urgent', function () {
    $articles = \App\Models\Article::all(['id', 'title', 'is_urgent', 'status']);
    $urgentArticles = \App\Models\Article::with(['author'])
        ->published()
        ->urgent()
        ->get(['id', 'title', 'excerpt', 'slug', 'published_at', 'author_id']);
    
    return response()->json([
        'all_articles' => $articles,
        'urgent_articles' => $urgentArticles,
        'article_count' => $articles->count(),
        'urgent_count' => $urgentArticles->count()
    ]);
})->middleware('auth');

// Protected routes - only for authenticated users
Route::middleware(['auth', 'verified'])->group(function () {
    // Authenticated member homepage
    Route::get('/home', [HomeController::class, 'index'])->name('dashboard.home');
    
    // News routes
    Route::get('/nieuws', [ArticleController::class, 'index'])->name('nieuws');
    Route::get('/nieuws/{slug}', [ArticleController::class, 'show'])->name('artikel.show');

    // Downloads routes
    Route::get('/downloads', [DownloadController::class, 'index'])->name('downloads');
    Route::get('/download/{id}', [DownloadController::class, 'download'])->name('download');

    // Activities routes
    Route::get('/activiteiten', [ActivityController::class, 'index'])->name('activiteiten');
    Route::get('/activiteiten/{slug}', [ActivityController::class, 'show'])->name('activiteit.show');
    Route::post('/activiteiten/{slug}/aanmelden', [ActivityController::class, 'register'])->name('activiteit.register');
    Route::delete('/activiteiten/{slug}/afmelden', [ActivityController::class, 'unregister'])->name('activiteit.unregister');

    // Organization and contact pages
    Route::get('/organisatie', [OrganizationController::class, 'index'])->name('organisatie');

    Route::get('/contact', function () {
        return Inertia::render('contact');
    })->name('contact');

    Route::get('/instellingen', function () {
        return Inertia::render('instellingen');
    })->name('instellingen');

    Route::get('/regels', function () {
        return Inertia::render('regels');
    })->name('regels');

    // Match routes
    Route::get('/wedstrijden', [WedstrijdenController::class, 'index'])->name('wedstrijden.index');
    Route::get('/wedstrijd/{id}', [WedstrijdenController::class, 'show'])->name('wedstrijd.show');
    
    // Match registration routes
    Route::post('/wedstrijd/{id}/aanmelden', [WedstrijdenController::class, 'register'])->name('wedstrijd.register');
    Route::delete('/wedstrijd/{id}/afmelden', [WedstrijdenController::class, 'unregister'])->name('wedstrijd.unregister');
    Route::get('/wedstrijd/{id}/deelnemers', [WedstrijdenController::class, 'participants'])->name('wedstrijd.participants');

    // Member contact routes
    Route::get('/leden', [MemberContactController::class, 'index'])->name('leden.contact');
    Route::post('/leden/privacy-settings', [MemberContactController::class, 'updatePrivacySettings'])->name('leden.privacy');
    Route::post('/leden/update-profile', [MemberContactController::class, 'updateProfile'])->name('leden.profile');
});

// User Dashboard routes (protected by auth middleware)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('profile', [\App\Http\Controllers\UserDashboardController::class, 'profile'])->name('profile');
    Route::patch('profile', [\App\Http\Controllers\UserDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('profile', [\App\Http\Controllers\UserDashboardController::class, 'updateProfile'])->name('profile.update.post');
    Route::get('my-scores', [\App\Http\Controllers\UserDashboardController::class, 'matchHistory'])->name('my-scores');
    Route::get('my-match/{matchId}', [\App\Http\Controllers\UserDashboardController::class, 'viewMatch'])->name('my-match');
    
    // My registrations page
    Route::get('mijn-inschrijvingen', [\App\Http\Controllers\MatchRegistrationController::class, 'myRegistrations'])->name('my.registrations');
});

// Legacy match registration routes (for backward compatibility) - moved to auth middleware above

// Public scores overview page
Route::get('/scores/openbaar', [PublicScoresController::class, 'index'])
    ->name('scores.public')
    ->middleware(['auth']);

// Public scores detail page
Route::get('/scores/openbaar/{user}', [PublicScoresController::class, 'show'])
    ->name('scores.public.user')
    ->middleware(['auth']);

// Fallback route for 404 errors
Route::fallback(function () {
    return Inertia::render('errors.404');
});

// Auth and settings routes
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Debug routes - Only available in local environment
if (config('app.env') === 'local') {
    Route::prefix('debug')->name('debug.')->group(function () {
        Route::get('/login/{userId?}', function ($userId = null) {
            $user = $userId ? \App\Models\User::find($userId) : \App\Models\User::first();
            if ($user) {
                auth()->login($user);
                return redirect()->back()->with('success', "🎯 Debug: Logged in as {$user->name}");
            }
            return redirect()->back()->with('error', '❌ Debug: User not found');
        })->name('login');

        Route::get('/logout', function () {
            $username = auth()->user()?->name ?? 'Unknown';
            auth()->logout();
            return redirect()->back()->with('success', "👋 Debug: Logged out {$username}");
        })->name('logout');

        Route::get('/users', function () {
            $users = \App\Models\User::select('id', 'name', 'email', 'is_admin')->get();
            return response()->json($users);
        })->name('users');
    });
}
