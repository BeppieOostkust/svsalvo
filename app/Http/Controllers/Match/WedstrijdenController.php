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
        // Fetch matches with related gebruikersScores, registrations and their users
        $matches = Matches::with([
                'gebruikersScores.gebruiker',
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
        $match = Matches::with(['gebruikersScores.gebruiker'])
            ->findOrFail($id);

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

        // Check if user has an active registration
        $existingRegistration = MatchRegistration::where('match_id', $matchId)
            ->where('user_id', $userId)
            ->where('status', 'aangemeld')
            ->first();

        if ($existingRegistration) {
            return back()->withErrors(['message' => 'Je bent al aangemeld voor deze wedstrijd.']);
        }

        // Also check if already a participant
        $existingParticipant = MatchGebruikerScore::where('wedstrijd_id', $matchId)
            ->where('gebruiker_id', $userId)
            ->first();

        if ($existingParticipant) {
            return back()->withErrors(['message' => 'Je bent al deelnemer van deze wedstrijd.']);
        }

        try {
            // Create registration entry using new system
            MatchRegistration::create([
                'match_id' => $matchId,
                'user_id' => $userId,
                'caliber' => $request->input('caliber', 'gkp'), // Default to GKP if not specified
                'status' => 'aangemeld',
                'registered_at' => now(),
                'notes' => $request->input('notes'),
            ]);

            return back()->with('success', 'Je bent succesvol aangemeld voor de wedstrijd! De organisatie zal je aanmelding beoordelen.');

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
            // First try to find and cancel registration
            $registration = MatchRegistration::where('match_id', $matchId)
                ->where('user_id', $userId)
                ->where('status', 'aangemeld')
                ->first();

            if ($registration) {
                // If registration not yet converted, we can delete it
                if (!$registration->converted_to_participant) {
                    $registration->delete(); // Delete instead of updating status
                    return back()->with('success', 'Je aanmelding is geannuleerd.');
                } else {
                    // If already converted, cannot cancel registration anymore
                    return back()->withErrors(['message' => 'Je kunt deze aanmelding niet meer annuleren omdat je al toegevoegd bent als deelnemer.']);
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
        $match = Matches::with(['gebruikersScores.gebruiker'])
            ->findOrFail($matchId);

        return Inertia::render('wedstrijd-deelnemers', [
            'match' => $match,
            'participants' => $match->gebruikersScores
        ]);
    }
}