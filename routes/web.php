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
use App\Http\Controllers\PublicScoresController;
use App\Http\Controllers\MemberContactController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\VereenigingController;
use App\Http\Controllers\FeedbackController;

// Public homepage - accessible to everyone, invites visitors to join
Route::get('/', [PublicController::class, 'index'])->name('home');

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
Route::middleware(['auth', 'verified', 'legal.check', 'password.change'])->group(function () {
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
        $rules = \App\Models\Rule::active()->ordered()->get();
        $prices = \App\Models\Price::active()->ordered()->get();
        
        return Inertia::render('regels', [
            'rules' => $rules,
            'prices' => $prices,
        ]);
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
Route::middleware(['auth', 'verified', 'legal.check', 'password.change'])->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('profile', [\App\Http\Controllers\UserDashboardController::class, 'profile'])->name('profile');
    Route::patch('profile', [\App\Http\Controllers\UserDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('profile', [\App\Http\Controllers\UserDashboardController::class, 'updateProfile'])->name('profile.update.post');
    Route::get('my-scores', [\App\Http\Controllers\UserDashboardController::class, 'matchHistory'])->name('my-scores');
    Route::get('my-match/{matchId}', [\App\Http\Controllers\UserDashboardController::class, 'viewMatch'])->name('my-match');
    
    // My registrations page
    Route::get('mijn-inschrijvingen', [\App\Http\Controllers\MatchRegistrationController::class, 'myRegistrations'])->name('my.registrations');
    
    // Notification routes
    Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
});

// Legacy match registration routes (for backward compatibility) - moved to auth middleware above

// Public scores overview page
Route::get('/scores/openbaar', [PublicScoresController::class, 'index'])
    ->name('scores.public')
    ->middleware(['auth']);

// Public scores leaderboard page
Route::get('/scores/leaderboard', [PublicScoresController::class, 'leaderboard'])
    ->name('scores.leaderboard')
    ->middleware(['auth']);

// Public scores detail page
Route::get('/scores/openbaar/{user}', [PublicScoresController::class, 'show'])
    ->name('scores.public.user')
    ->middleware(['auth']);

// Storage image route for shared hosting compatibility
Route::get('/storage-image/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    
    if (!file_exists($filePath)) {
        abort(404);
    }
    
    $mimeType = mime_content_type($filePath);
    
    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('storage.image');

// Fallback route for 404 errors
Route::fallback(function () {
    return Inertia::render('errors.404');
});

// Auth and settings routes
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Password change routes (must be accessible when password change is required)
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [PasswordChangeController::class, 'showForm'])->name('password.change.form');
    Route::post('/change-password', [PasswordChangeController::class, 'change'])->name('password.change');
});

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

        // Storage debug route (remove in production)
        Route::get('/debug-storage', [App\Http\Controllers\StorageDebugController::class, 'debug'])
            ->middleware('auth')
            ->name('debug.storage');
    });
}

// Verenigingspagina route
Route::get('/vereniging', [VereenigingController::class, 'index'])->name('vereniging')->middleware(['auth']);

// Feedback & Suggesties routes
Route::middleware(['auth'])->prefix('feedback')->name('feedback.')->group(function () {
    Route::get('/', [FeedbackController::class, 'index'])->name('index');
    Route::get('/create', [FeedbackController::class, 'create'])->name('create');
    Route::post('/', [FeedbackController::class, 'store'])->name('store');
    Route::get('/{feedback}', [FeedbackController::class, 'show'])->name('show');
    Route::post('/{feedback}/vote', [FeedbackController::class, 'vote'])->name('vote');
    Route::post('/{feedback}/comment', [FeedbackController::class, 'addComment'])->name('comment');
    Route::delete('/comment/{comment}', [FeedbackController::class, 'deleteComment'])->name('comment.delete');
});

// Legal routes
require __DIR__.'/legal.php';
