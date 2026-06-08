<?php

namespace App\Http\Controllers\Competition;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\CompetitionRound;
use App\Models\CompetitionScore;
use App\Models\CompetitionRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompetitionScoreController extends Controller
{
    /**
     * Create or update a score for a user in a round
     */
    public function store(Request $request, $competitionId, $roundId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'kaliber' => 'required|in:meesterkaart_zwaar,meesterkaart_licht,kk_geweer_open_50m,kk_geweer_optisch_100m,gk_precision_100m,militair_geweer,militair_geweer_optisch,veteranen_geweer',
            'linker_score' => 'required|integer|min:0',
            'rechter_score' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $competition = Competition::findOrFail($competitionId);
        $round = CompetitionRound::findOrFail($roundId);

        // Verify round belongs to competition
        if ($round->competition_id !== $competition->id) {
            return response()->json(['error' => 'Round does not belong to this competition'], 422);
        }

        // Check authorization (only admins can create scores for now)
        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if user is registered for this competition with this caliber
        $registration = CompetitionRegistration::where('competition_id', $competitionId)
            ->where('user_id', $request->user_id)
            ->where('kaliber', $request->kaliber)
            ->where('status', 'actief')
            ->first();

        if (!$registration) {
            return response()->json(['error' => 'User is not registered for this caliber'], 422);
        }

        // Find or create score
        $score = CompetitionScore::firstOrCreate(
            [
                'competition_round_id' => $roundId,
                'user_id' => $request->user_id,
                'kaliber' => $request->kaliber,
            ],
            [
                'linker_score' => 0,
                'rechter_score' => 0,
            ]
        );

        // Update score with request data
        $score->update([
            'linker_score' => $request->linker_score,
            'rechter_score' => $request->rechter_score,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Score saved successfully',
            'score' => $score,
        ]);
    }

    /**
     * Update an existing score
     */
    public function update(Request $request, $scoreId)
    {
        $request->validate([
            'linker_score' => 'required|integer|min:0',
            'rechter_score' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $score = CompetitionScore::findOrFail($scoreId);

        // Check authorization
        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $score->update([
            'linker_score' => $request->linker_score,
            'rechter_score' => $request->rechter_score,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Score updated successfully',
            'score' => $score,
        ]);
    }

    /**
     * Delete a score
     */
    public function destroy($scoreId)
    {
        $score = CompetitionScore::findOrFail($scoreId);

        // Check authorization
        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $score->delete();

        return response()->json(['message' => 'Score deleted successfully']);
    }

    /**
     * Get all scores for a round
     */
    public function byRound($competitionId, $roundId)
    {
        $competition = Competition::findOrFail($competitionId);
        $round = CompetitionRound::where('competition_id', $competitionId)
            ->findOrFail($roundId);

        $currentUser = Auth::user();
        $currentUserId = $currentUser?->id;
        $isAdmin = $currentUser instanceof \App\Models\User ? $currentUser->isAdmin() : false;

        $query = $round->scores()
            ->with('user')
            ->orderBy('kaliber')
            ->orderByDesc('totale_punten');

        if (! $isAdmin) {
            $query->whereHas('user', function ($q) use ($currentUserId) {
                $q->where('show_scores_public', true);
                if ($currentUserId) {
                    $q->orWhere('id', $currentUserId);
                }
            });
        }

        $scores = $query->get();

        return response()->json([
            'competition' => $competition,
            'round' => $round,
            'scores' => $scores,
        ]);
    }

    /**
     * Get scores for a specific user in a competition
     */
    public function byUser($competitionId, $userId)
    {
        $competition = Competition::findOrFail($competitionId);

        $currentUser = Auth::user();
        $currentUserId = $currentUser?->id;
        $isAdmin = $currentUser instanceof \App\Models\User ? $currentUser->isAdmin() : false;

        // If the requesting user is not admin and not the owner, ensure target user's scores are public
        if (! $isAdmin && $currentUserId !== (int) $userId) {
            $target = \App\Models\User::find($userId);
            if (! $target || ! $target->show_scores_public) {
                return response()->json(['error' => 'Forbidden'], 403);
            }
        }

        $scores = CompetitionScore::whereHas('round', function ($query) use ($competitionId) {
            $query->where('competition_id', $competitionId);
        })
        ->where('user_id', $userId)
        ->with(['round', 'user'])
        ->orderBy('kaliber')
        ->get()
        ->groupBy(function ($score) {
            return $score->round->round_number;
        });

        return response()->json([
            'competition' => $competition,
            'user_id' => $userId,
            'scores' => $scores,
        ]);
    }
}
