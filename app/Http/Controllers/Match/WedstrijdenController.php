<?php

namespace App\Http\Controllers\Match;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionRound;
use App\Models\CompetitionScore;
use App\Models\Matches;
use App\Models\MatchGebruikerScore;
use App\Models\MatchRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WedstrijdenController extends Controller
{
    public function index()
    {
        // Fetch competitions (new system) with related rounds
        $competitions = Competition::with('rounds')
            ->orderBy('jaar', 'desc')
            ->get();
        
        // Add registration status for current user if authenticated
        $currentUserId = Auth::id();
        if ($currentUserId) {
            $competitions = $competitions->map(function ($competition) use ($currentUserId) {
                // Check if user is registered for this competition
                $registration = CompetitionRegistration::where('competition_id', $competition->id)
                    ->where('user_id', $currentUserId)
                    ->where('status', 'actief')
                    ->first();
                
                $competition->is_user_registered = $registration ? true : false;
                $competition->user_caliber = $registration?->kaliber;
                $competition->user_registration_id = $registration?->id;
                
                return $competition;
            });
        }

        // Get the most recent competition with scores for leaderboard
        $latestCompetition = Competition::with('rounds.scores.user')
            ->orderBy('jaar', 'desc')
            ->first();

        $leaderboard = collect();
        
        if ($latestCompetition) {
            // Get all scores for this competition grouped by caliber
            $scoresByCaliberAndUser = collect();
            
            foreach ($latestCompetition->rounds as $round) {
                foreach ($round->scores as $score) {
                    if ($score->user && $score->user->show_scores_public) {
                        $key = $score->user->id . '_' . $score->kaliber;
                        if (!$scoresByCaliberAndUser->has($key)) {
                            $scoresByCaliberAndUser->put($key, [
                                'user_id' => $score->user->id,
                                'user_name' => $score->user->name,
                                'user_first_name' => $score->user->first_name,
                                'user_last_name' => $score->user->last_name,
                                'user_profile_image' => $score->user->profile_image,
                                'kaliber' => $score->kaliber,
                                'total_points' => 0,
                                'scores_count' => 0,
                            ]);
                        }
                        
                        $entry = $scoresByCaliberAndUser->get($key);
                        $entry['total_points'] += $score->totale_punten;
                        $entry['scores_count'] += 1;
                        $scoresByCaliberAndUser->put($key, $entry);
                    }
                }
            }
            
            // Convert to array and sort by total points descending
            $leaderboard = $scoresByCaliberAndUser->values()
                ->sortByDesc('total_points')
                ->take(10) // Top 10
                ->values();
        }
            
        return Inertia::render('wedstrijden', [
            'matches' => $competitions,
            'latestCompetition' => $latestCompetition,
            'leaderboard' => $leaderboard->values(),
        ]);
    }

    /**
     * Show a specific match with detailed information
     */
    public function show($id)
    {
        $competition = Competition::with('rounds')->find($id);

        if ($competition) {
            $currentUser = Auth::user();
            $currentUserId = $currentUser?->id;
            $isAdmin = $currentUser instanceof \App\Models\User ? $currentUser->isAdmin() : false;

            $rounds = $competition->rounds()->with([
                'scores' => function ($query) use ($currentUserId, $isAdmin) {
                    if (! $isAdmin) {
                        $query->whereHas('user', function ($userQuery) use ($currentUserId) {
                            $userQuery->where('show_scores_public', true);
                            if ($currentUserId) {
                                $userQuery->orWhere('id', $currentUserId);
                            }
                        });
                    }

                    $query->with('user')
                        ->orderByDesc('totale_punten');
                },
            ])->get();

            $participantsQuery = CompetitionRegistration::where('competition_id', $competition->id)
                ->where('status', 'actief')
                ->with('user');

            if (! $isAdmin) {
                $participantsQuery->whereHas('user', function ($userQuery) use ($currentUserId) {
                    $userQuery->where('show_in_participants', true);
                    if ($currentUserId) {
                        $userQuery->orWhere('id', $currentUserId);
                    }
                });
            }

            $participants = $participantsQuery->orderBy('kaliber')->orderBy('user_id')->get();

            return Inertia::render('Competitions/show', [
                'competition' => $competition,
                'rounds' => $rounds,
                'participants' => $participants,
                'userRegistration' => $currentUserId
                    ? CompetitionRegistration::where('competition_id', $competition->id)
                        ->where('user_id', $currentUserId)
                        ->first()
                    : null,
                'totalParticipants' => CompetitionRegistration::where('competition_id', $competition->id)
                    ->where('status', 'actief')
                    ->count(),
                'visibleParticipants' => $participants->count(),
                'hiddenParticipants' => max(0, CompetitionRegistration::where('competition_id', $competition->id)
                    ->where('status', 'actief')
                    ->count() - $participants->count()),
                'totalScores' => CompetitionScore::whereHas('round', function ($q) use ($competition) {
                    $q->where('competition_id', $competition->id);
                })->count(),
                'visibleScores' => $rounds->reduce(fn ($carry, $round) => $carry + $round->scores->count(), 0),
                'hiddenScores' => max(0, CompetitionScore::whereHas('round', function ($q) use ($competition) {
                    $q->where('competition_id', $competition->id);
                })->count() - $rounds->reduce(fn ($carry, $round) => $carry + $round->scores->count(), 0)),
            ]);
        }

        $match = Matches::with(['gebruikersScores' => function($query) {
                $query->where('is_official', true)->with(['gebruiker' => function($userQuery) {
                    $userQuery->select('id', 'name', 'avg_name', 'first_name', 'last_name', 'show_scores_public', 'show_full_name');
                }]);
            }])
            ->findOrFail($id);

        // Filter out scores from users who don't want to be shown in participants
        if ($match->gebruikersScores) {
            $match->gebruikersScores = $match->gebruikersScores->filter(function($score) {
                return $score->gebruiker && $score->gebruiker->show_scores_public;
            })->values();
        }

        return Inertia::render('wedstrijd-detail', [
            'match' => $match
        ]);
    }

    /**
     * Register current user for a competition
     */
    public function register(Request $request, $competitionId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $competition = Competition::findOrFail($competitionId);
        $userId = Auth::id();
        $caliber = $request->input('calibers')[0] ?? 'gkp'; // Get first caliber

        // Check if user is already registered
        $existingRegistration = CompetitionRegistration::where('competition_id', $competitionId)
            ->where('user_id', $userId)
            ->first();

        if ($existingRegistration) {
            return back()->withErrors(['message' => 'Je bent al aangemeld voor deze competitie.']);
        }

        try {
            CompetitionRegistration::create([
                'competition_id' => $competitionId,
                'user_id' => $userId,
                'kaliber' => $caliber,
                'status' => 'actief',
                'registered_at' => now(),
            ]);

            return back()->with('success', 'Je bent succesvol aangemeld voor de competitie!');

        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Er is een fout opgetreden bij het aanmelden.']);
        }
    }

    /**
     * Unregister current user from a competition
     */
    public function unregister(Request $request, $competitionId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $competition = Competition::findOrFail($competitionId);
        $userId = Auth::id();

        try {
            $registration = CompetitionRegistration::where('competition_id', $competitionId)
                ->where('user_id', $userId)
                ->first();

            if (!$registration) {
                return back()->withErrors(['message' => 'Je was niet aangemeld voor deze competitie.']);
            }

            // Check if there are already scores - if so, don't allow deletion
            $hasScores = CompetitionScore::where('registration_id', $registration->id)->exists();
            
            if ($hasScores) {
                return back()->withErrors(['message' => 'Je kunt niet afmelden nu je al deelgenomen hebt. Neem contact op met de organisatie.']);
            }

            $registration->delete();

            return back()->with('success', 'Je aanmelding is geannuleerd.');

        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Er is een fout opgetreden bij het afmelden.']);
        }
    }

    /**
     * Show participants of a competition
     */
    public function participants($competitionId)
    {
        $competition = Competition::with('rounds')->findOrFail($competitionId);

        $participants = CompetitionRegistration::where('competition_id', $competitionId)
            ->where('status', 'actief')
            ->with('user')
            ->orderBy('kaliber')
            ->orderBy('user_id')
            ->get();

        return Inertia::render('Competitions/participants', [
            'competition' => $competition,
            'participants' => $participants
        ]);
    }
}