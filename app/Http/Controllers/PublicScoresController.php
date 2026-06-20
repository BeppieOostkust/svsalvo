<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Competition;
use App\Support\PublicStorage;
use Inertia\Inertia;

class PublicScoresController extends Controller
{
    public function index()
    {
        $users = User::where('show_scores_public', true)
            ->where('is_active_member', true)
            ->select(['id', 'name', 'first_name', 'last_name', 'profile_image', 'preferred_discipline'])
            ->get()
            ->map(fn (User $user) => PublicStorage::expose($user, 'profile_image'));

        return Inertia::render('scores/public', [
            'users' => $users,
        ]);
    }

    public function show($userId)
    {
        $user = User::where('id', $userId)
            ->where('show_scores_public', true)
            ->where('is_active_member', true)
            ->firstOrFail();

        // Get competition scores for this user and adapt shape expected by frontend
        $scores = $user->competitionScores()
            ->with(['round.competition', 'registration'])
            ->orderByDesc('totale_punten')
            ->get()
            ->map(function ($score) {
                // create a `matches`-shaped object for compatibility with the old frontend
                $match = null;
                if ($score->round) {
                    $match = (object) [
                        'id' => $score->round->id,
                        'naam' => $score->round->naam ?? ($score->round->competition?->naam ?? 'Wedstrijd'),
                        'start_datum' => optional($score->round)->datum?->toDateTimeString() ?? null,
                    ];
                }

                return (object) array_merge($score->toArray(), [
                    'matches' => $match,
                ]);
            });

        return Inertia::render('scores/user', [
            'user' => PublicStorage::expose($user, 'profile_image'),
            'scores' => $scores,
        ]);
    }

    public function leaderboard()
    {
        $users = User::where('show_scores_public', true)
            ->where('is_active_member', true)
            ->with('competitionScores')
            ->get()
            ->map(function ($user) {
                // Get all competition scores for this user
                $scoresByDiscipline = $user->competitionScores->groupBy('kaliber');

                $stats = [];
                foreach ($scoresByDiscipline as $discipline => $scores) {
                    $totalScore = $scores->sum('totale_punten');
                    $scoreCount = $scores->count();
                    $averageScore = $scoreCount > 0 ? $totalScore / $scoreCount : 0;

                    $stats[$discipline] = [
                        'total_score' => $totalScore,
                        'average_score' => $averageScore,
                        'score_count' => $scoreCount,
                    ];
                }

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'profile_image' => PublicStorage::modelUrl($user, 'profile_image'),
                    'preferred_discipline' => $user->preferred_discipline,
                    'discipline_stats' => $stats,
                ];
            })
            ->filter(function ($user) {
                // Only include users who have at least one score
                return !empty($user['discipline_stats']);
            });

        // Flatten the data for easier frontend processing
        $leaderboard = collect();
        
        foreach ($users as $user) {
            foreach ($user['discipline_stats'] as $discipline => $stats) {
                $leaderboard->push([
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'profile_image' => $user['profile_image'],
                    'preferred_discipline' => $discipline, // Use the actual discipline from scores
                    'total_score' => $stats['total_score'],
                    'average_score' => $stats['average_score'],
                    'score_count' => $stats['score_count'],
                ]);
            }
        }

        return Inertia::render('scores/leaderboard', [
            'leaderboard' => $leaderboard->values(),
        ]);
    }
}
