<?php

namespace App\Http\Controllers\Competition;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionRound;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CompetitionController extends Controller
{
    /**
     * Display a listing of all competitions
     */
    public function index()
    {
        $competitions = Competition::with('rounds')
            ->orderBy('jaar', 'desc')
            ->get();

        $currentUserId = Auth::id();
        
        if ($currentUserId) {
            $competitions = $competitions->map(function ($competition) use ($currentUserId) {
                // Check if user is registered
                $registration = CompetitionRegistration::where('competition_id', $competition->id)
                    ->where('user_id', $currentUserId)
                    ->where('status', 'actief')
                    ->first();

                $competition->user_registered = $registration ? true : false;
                $competition->user_caliber = $registration?->kaliber;

                return $competition;
            });
        }

        return Inertia::render('Competitions/index', [
            'competitions' => $competitions
        ]);
    }

    /**
     * Display a specific competition with all rounds and leaderboard
     */
    public function show($id)
    {
        $competition = Competition::with('rounds')
            ->findOrFail($id);

        // Determine viewer context for privacy filtering
        $currentUser = Auth::user();
        $currentUserId = $currentUser?->id;
        $isAdmin = $currentUser instanceof \App\Models\User ? $currentUser->isAdmin() : false;

        // Get all rounds with scores, but respect each user's `show_scores_public` flag
        $rounds = $competition->rounds()->with([
            'scores' => function ($query) use ($currentUserId, $isAdmin) {
                // Admins may see all scores; others only see scores from users who opted-in
                if (! $isAdmin) {
                    $query->whereHas('user', function ($q) use ($currentUserId) {
                        $q->where('show_scores_public', true);
                        if ($currentUserId) {
                            // always allow the current user to see their own scores
                            $q->orWhere('id', $currentUserId);
                        }
                    });
                }

                $query->with('user')
                    ->orderBy('kaliber')
                    ->orderByDesc('totale_punten');
            }
        ])->get();

        // Get active participants, but respect users who opted out of being shown in participants
        $participantsQuery = CompetitionRegistration::where('competition_id', $competition->id)
            ->where('status', 'actief')
            ->with('user')
            ->orderBy('kaliber')
            ->orderBy('user_id');

        if (! $isAdmin) {
            $participantsQuery->whereHas('user', function ($q) use ($currentUserId) {
                $q->where('show_in_participants', true);
                if ($currentUserId) {
                    $q->orWhere('id', $currentUserId);
                }
            });
        }

        $participants = $participantsQuery->get();

        $userRegistration = null;

        if ($currentUserId) {
            $userRegistration = CompetitionRegistration::where('competition_id', $competition->id)
                ->where('user_id', $currentUserId)
                ->first();
        }

        // Compute totals to detect hidden/private entries
        $totalParticipants = CompetitionRegistration::where('competition_id', $competition->id)
            ->where('status', 'actief')
            ->count();

        $visibleParticipants = $participants->count();

        $totalScores = CompetitionScore::whereHas('round', function ($q) use ($competition) {
            $q->where('competition_id', $competition->id);
        })->count();

        $visibleScores = $rounds->reduce(function ($carry, $r) {
            return $carry + ($r->scores->count() ?? 0);
        }, 0);

        $hiddenParticipants = max(0, $totalParticipants - $visibleParticipants);
        $hiddenScores = max(0, $totalScores - $visibleScores);

        return Inertia::render('Competitions/show', [
            'competition' => $competition,
            'rounds' => $rounds,
            'participants' => $participants,
            'userRegistration' => $userRegistration,
        ]);
    }

    /**
     * Register current user for a competition
     */
    public function register(Request $request, $id)
    {
        $request->validate([
            'kaliber' => 'required|in:meesterkaart_zwaar,meesterkaart_licht,kk_geweer_open_50m,kk_geweer_optisch_100m,gk_precision_100m,militair_geweer,militair_geweer_optisch,veteranen_geweer',
        ]);

        $competition = Competition::findOrFail($id);
        $userId = Auth::id();

        // Check if already registered
        $existing = CompetitionRegistration::where('competition_id', $competition->id)
            ->where('user_id', $userId)
            ->where('kaliber', $request->kaliber)
            ->first();

        if ($existing) {
            if ($existing->status === 'afgemeld') {
                // Reactivate
                $existing->update(['status' => 'actief']);
            }
            return back()->with('message', 'Je bent al ingeschreven voor deze competitie.');
        }

        // Create new registration
        $registration = CompetitionRegistration::create([
            'competition_id' => $competition->id,
            'user_id' => $userId,
            'kaliber' => $request->kaliber,
            'status' => 'actief',
        ]);

        return back()->with('message', 'Je hebt je ingeschreven voor de competitie!');
    }

    /**
     * Unregister current user from a competition
     */
    public function unregister($id)
    {
        $competition = Competition::findOrFail($id);
        $userId = Auth::id();

        $registration = CompetitionRegistration::where('competition_id', $competition->id)
            ->where('user_id', $userId)
            ->first();

        if ($registration) {
            $registration->update(['status' => 'afgemeld']);
        }

        return back()->with('message', 'Je hebt je afgemeld voor de competitie.');
    }

    /**
     * Get leaderboard for a specific round and caliber
     */
    public function getRoundLeaderboard($competitionId, $roundId, $caliber = null)
    {
        $competition = Competition::findOrFail($competitionId);
        $round = CompetitionRound::findOrFail($roundId);

        $currentUser = Auth::user();
        $currentUserId = $currentUser?->id;
        $isAdmin = $currentUser instanceof \App\Models\User ? $currentUser->isAdmin() : false;

        $query = $round->scores()->with('user');

        if ($caliber) {
            $query->where('kaliber', $caliber);
        }

        if (! $isAdmin) {
            $query->whereHas('user', function ($q) use ($currentUserId) {
                $q->where('show_scores_public', true);
                if ($currentUserId) {
                    $q->orWhere('id', $currentUserId);
                }
            });
        }

        $scores = $query->orderByDesc('totale_punten')->get();

        return response()->json([
            'competition' => $competition,
            'round' => $round,
            'scores' => $scores,
            'caliber' => $caliber,
        ]);
    }

    /**
     * Get user's personal scores across all rounds
     */
    public function getUserScores($competitionId)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $competition = Competition::findOrFail($competitionId);
        
        // Check if user is registered
        $registration = CompetitionRegistration::where('competition_id', $competitionId)
            ->where('user_id', $userId)
            ->where('status', 'actief')
            ->first();

        if (!$registration) {
            return response()->json(['error' => 'Not registered for this competition'], 403);
        }

        // Get all scores for this user in all rounds
        $scores = CompetitionScore::whereHas('round', function ($query) use ($competitionId) {
            $query->where('competition_id', $competitionId);
        })
        ->where('user_id', $userId)
        ->with('round')
        ->orderBy('kaliber')
        ->get()
        ->groupBy('round.round_number');

        return response()->json([
            'competition' => $competition,
            'scores' => $scores,
        ]);
    }
}
