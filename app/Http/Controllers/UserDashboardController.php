<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\MatchGebruikerScore;
use App\Models\ActivityRegistration;

class UserDashboardController extends Controller
{
    /**
     * Show user dashboard with their scores and personal info
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get user's match scores with match information
        $matchScores = MatchGebruikerScore::with(['matches'])
            ->where('gebruiker_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get user's activity registrations
        $activityRegistrations = ActivityRegistration::with(['activity'])
            ->where('user_id', $user->id)
            ->orderBy('registered_at', 'desc')
            ->get();

        // Calculate some statistics
        $stats = [
            'total_matches' => $matchScores->count(),
            'best_score' => $matchScores->max('totale_punten'),
            'average_score' => $matchScores->count() > 0 ? round($matchScores->avg('totale_punten'), 1) : null,
            'recent_activities' => $activityRegistrations->take(5),
        ];

        return Inertia::render('Dashboard/UserDashboard', [
            'user' => $user,
            'matchScores' => $matchScores,
            'activityRegistrations' => $activityRegistrations,
            'stats' => $stats,
        ]);
    }

    /**
     * Show user profile page for editing personal information
     */
    public function profile()
    {
        return Inertia::render('Dashboard/UserProfile', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Update user profile information
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'preferred_discipline' => 'nullable|string|max:255',
            'show_contact_info' => 'boolean',
            'show_scores_public' => 'boolean',
        ]);

        // Validate profile image separately to handle both file uploads and remove action
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            
            // Check if file is valid
            if (!$file->isValid()) {
                return redirect()->back()->withErrors(['profile_image' => 'Het geüploade bestand is beschadigd.']);
            }
            
            // Detailed validation with better error messages
            $request->validate([
                'profile_image' => [
                    'required',
                    'file',
                    'image',
                    'mimes:jpeg,jpg,png,gif,webp',
                    'max:5120', // 5MB max (5120 KB)
                    'dimensions:max_width=4000,max_height=4000' // Reasonable size limits
                ]
            ], [
                'profile_image.image' => 'Het bestand moet een afbeelding zijn.',
                'profile_image.mimes' => 'Alleen JPEG, PNG, GIF en WebP bestanden zijn toegestaan.',
                'profile_image.max' => 'De afbeelding mag maximaal 5MB groot zijn.',
                'profile_image.dimensions' => 'De afbeelding mag maximaal 4000x4000 pixels zijn.',
            ]);
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old profile image if it exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            
            // Store new profile image
            $path = $request->file('profile_image')->store('profile-images', 'public');
            $validated['profile_image'] = $path;
        } elseif ($request->input('profile_image') === 'remove') {
            // Handle profile image removal
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $validated['profile_image'] = null;
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'Profiel succesvol bijgewerkt!');
    }

    /**
     * Show user's match history with detailed scores
     */
    public function matchHistory()
    {
        $user = auth()->user();
        
        $matchScores = MatchGebruikerScore::with(['matches'])
            ->where('gebruiker_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Dashboard/MatchHistory', [
            'matchScores' => $matchScores,
        ]);
    }

    /**
     * Show user's scores for a specific match
     */
    public function viewMatch($matchId)
    {
        $user = auth()->user();
        
        // Get user's score for this specific match
        $matchScore = MatchGebruikerScore::with(['matches'])
            ->where('gebruiker_id', $user->id)
            ->where('wedstrijd_id', $matchId)
            ->firstOrFail();

        return Inertia::render('Dashboard/MatchDetail', [
            'matchScore' => $matchScore,
        ]);
    }
}
