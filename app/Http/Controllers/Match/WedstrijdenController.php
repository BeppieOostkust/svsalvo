<?php

namespace App\Http\Controllers\Match;

use App\Http\Controllers\Controller;
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
        // Fetch matches with related gebruikersScores (only official ones), registrations and their users
        $matches = Matches::with([
                'gebruikersScores' => function($query) {
                    $query->where('is_official', true)->with('gebruiker');
                },
                'registrations.user'
            ])
            ->orderBy('start_datum', 'desc')
            ->get();
        
        // Add registration status for current user if authenticated
        $currentUserId = Auth::id();
        if ($currentUserId) {
            $matches = $matches->map(function ($match) use ($currentUserId) {
                // Check if user is already a participant
                $isParticipant = $match->gebruikersScores->contains('gebruiker_id', $currentUserId);
                
                // Check if user has an active registration
                $hasActiveRegistration = MatchRegistration::where('match_id', $match->id)
                    ->where('user_id', $currentUserId)
                    ->where('status', 'aangemeld')
                    ->exists();
                
                $match->is_user_registered = $isParticipant || $hasActiveRegistration;
                $match->is_participant = $isParticipant;
                $match->has_registration = $hasActiveRegistration;
                
                return $match;
            });
        }
            
        return Inertia::render('wedstrijden', [
            'matches' => $matches
        ]);
    }

    /**
     * Show a specific match with detailed information
     */
    public function show($id)
    {
        $match = Matches::with(['gebruikersScores' => function($query) {
                $query->where('is_official', true)->with(['gebruiker' => function($userQuery) {
                    $userQuery->select('id', 'name', 'show_in_participants');
                }]);
            }])
            ->findOrFail($id);

        // Filter out scores from users who don't want to be shown in participants
        if ($match->gebruikersScores) {
            $match->gebruikersScores = $match->gebruikersScores->filter(function($score) {
                return $score->gebruiker && $score->gebruiker->show_in_participants;
            })->values();
        }

        return Inertia::render('wedstrijd-detail', [
            'match' => $match
        ]);
    }

    /**
     * Register current user for a match
     */
    public function register(Request $request, $matchId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $match = Matches::findOrFail($matchId);
        $userId = Auth::id();

        // Check if match allows registration
        if (!in_array($match->status, ['binnenkort', 'bezig'])) {
            return back()->withErrors(['message' => 'Aanmelden voor deze wedstrijd is niet meer mogelijk.']);
        }

        // Check if user has an active registration (for any caliber)
        $existingRegistration = MatchRegistration::where('match_id', $matchId)
            ->where('user_id', $userId)
            ->where('status', 'aangemeld')
            ->first();

        // We'll allow multiple registrations for different calibers, but show info if they already have some
        if ($existingRegistration) {
            $existingCalibers = MatchRegistration::where('match_id', $matchId)
                ->where('user_id', $userId)
                ->where('status', 'aangemeld')
                ->pluck('caliber')
                ->toArray();
                
            $caliberNames = array_map(function($c) {
                return $c === 'gkp' ? 'GKP' : 'KKP';
            }, $existingCalibers);
            
            $message = count($existingCalibers) === 1 
                ? "Je bent al aangemeld voor deze wedstrijd met " . $caliberNames[0] . "."
                : "Je bent al aangemeld voor deze wedstrijd met " . implode(' en ', $caliberNames) . ".";
            
            // Only block if they're registered for both calibers
            if (count($existingCalibers) >= 2) {
                return back()->withErrors(['message' => $message]);
            }
        }

        // Also check if already a participant
        $existingParticipant = MatchGebruikerScore::where('wedstrijd_id', $matchId)
            ->where('gebruiker_id', $userId)
            ->first();

        if ($existingParticipant) {
            return back()->withErrors(['message' => 'Je bent al deelnemer van deze wedstrijd.']);
        }

        try {
            $calibers = $request->input('calibers', ['gkp']); // Default to GKP if not specified
            
            // Ensure calibers is an array
            if (!is_array($calibers)) {
                $calibers = [$calibers];
            }
            
            // Validate calibers
            $validCalibers = ['gkp', 'kkp'];
            $calibers = array_intersect($calibers, $validCalibers);
            
            if (empty($calibers)) {
                return back()->withErrors(['message' => 'Selecteer tenminste één geldig kaliber (GKP of KKP).']);
            }
            
            // Create registration entries for each caliber
            foreach ($calibers as $caliber) {
                // Check if user already has a registration for this caliber
                $existingCaliberRegistration = MatchRegistration::where('match_id', $matchId)
                    ->where('user_id', $userId)
                    ->where('caliber', $caliber)
                    ->where('status', 'aangemeld')
                    ->first();
                    
                if (!$existingCaliberRegistration) {
                    MatchRegistration::create([
                        'match_id' => $matchId,
                        'user_id' => $userId,
                        'caliber' => $caliber,
                        'status' => 'aangemeld',
                        'registered_at' => now(),
                        'notes' => $request->input('notes'),
                    ]);
                }
            }

            $caliberNames = array_map(function($c) {
                return $c === 'gkp' ? 'GKP' : 'KKP';
            }, $calibers);
            
            $message = count($calibers) === 1 
                ? "Je bent succesvol aangemeld voor de wedstrijd met " . $caliberNames[0] . "!"
                : "Je bent succesvol aangemeld voor de wedstrijd met " . implode(' en ', $caliberNames) . "!";
                
            return back()->with('success', $message . ' De organisatie zal je aanmelding(en) beoordelen.');

        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Er is een fout opgetreden bij het aanmelden.']);
        }
    }

    /**
     * Unregister current user from a match
     */
    public function unregister(Request $request, $matchId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $match = Matches::findOrFail($matchId);
        $userId = Auth::id();

        // Check if match allows unregistration
        if (!in_array($match->status, ['binnenkort', 'bezig'])) {
            return back()->withErrors(['message' => 'Afmelden voor deze wedstrijd is niet meer mogelijk.']);
        }

        try {
            // Find all registrations for this user and match
            $registrations = MatchRegistration::where('match_id', $matchId)
                ->where('user_id', $userId)
                ->where('status', 'aangemeld')
                ->get();

            if ($registrations->count() > 0) {
                $deletedCalibers = [];
                $cannotDeleteCalibers = [];
                
                foreach ($registrations as $registration) {
                    // If registration not yet converted, we can delete it
                    if (!$registration->converted_to_participant) {
                        $deletedCalibers[] = $registration->caliber === 'gkp' ? 'GKP' : 'KKP';
                        $registration->delete();
                    } else {
                        $cannotDeleteCalibers[] = $registration->caliber === 'gkp' ? 'GKP' : 'KKP';
                    }
                }
                
                if (count($deletedCalibers) > 0) {
                    $message = count($deletedCalibers) === 1 
                        ? "Je aanmelding voor " . $deletedCalibers[0] . " is geannuleerd."
                        : "Je aanmeldingen voor " . implode(' en ', $deletedCalibers) . " zijn geannuleerd.";
                    
                    if (count($cannotDeleteCalibers) > 0) {
                        $message .= " De aanmelding(en) voor " . implode(' en ', $cannotDeleteCalibers) . " konden niet geannuleerd worden omdat je al toegevoegd bent als deelnemer.";
                    }
                    
                    return back()->with('success', $message);
                } else {
                    return back()->withErrors(['message' => 'Je kunt deze aanmeldingen niet meer annuleren omdat je al toegevoegd bent als deelnemer voor alle kalibers.']);
                }
            }

            // If no registration found, maybe they're a direct participant (legacy)
            $participant = MatchGebruikerScore::where('wedstrijd_id', $matchId)
                ->where('gebruiker_id', $userId)
                ->first();

            if ($participant) {
                return back()->withErrors(['message' => 'Je bent deelnemer van deze wedstrijd en kunt niet meer afmelden. Neem contact op met de organisatie.']);
            }

            return back()->withErrors(['message' => 'Je was niet aangemeld voor deze wedstrijd.']);

        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Er is een fout opgetreden bij het afmelden.']);
        }
    }

    /**
     * Show participants of a match
     */
    public function participants($matchId)
    {
        $match = Matches::with(['gebruikersScores' => function($query) {
                $query->where('is_official', true)->with('gebruiker');
            }])
            ->findOrFail($matchId);

        return Inertia::render('wedstrijd-deelnemers', [
            'match' => $match,
            'participants' => $match->gebruikersScores
        ]);
    }
}