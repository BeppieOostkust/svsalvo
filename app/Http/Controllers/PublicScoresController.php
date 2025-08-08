<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;

class PublicScoresController extends Controller
{
    public function index()
    {
        $users = User::where('show_scores_public', true)
            ->where('is_active_member', true)
            ->select(['id', 'name', 'first_name', 'last_name', 'profile_image', 'preferred_discipline'])
            ->get();
        // Optionally, you can eager load scores if you have a relation
        // ->with('scores')

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

        // Assuming a relation 'scores' exists on User model
        $scores = $user->scores()->with('matches')->orderByDesc('totale_punten')->get();

        return Inertia::render('scores/user', [
            'user' => $user,
            'scores' => $scores,
        ]);
    }

    public function leaderboard()
    {
        $users = User::where('show_scores_public', true)
            ->where('is_active_member', true)
            ->with(['matchScores.matches'])
            ->get()
            ->map(function ($user) {
                // Calculate total and average scores for each discipline (kaliber)
                $scoresByDiscipline = $user->matchScores->groupBy('kaliber');

                $stats = [];
                foreach ($scoresByDiscipline as $discipline => $scores) {
                    $totalScore = $scores->sum('totale_punten');
                    $matchCount = $scores->count();
                    $averageScore = $matchCount > 0 ? $totalScore / $matchCount : 0;

                    $stats[$discipline] = [
                        'total_score' => $totalScore,
                        'average_score' => $averageScore,
                        'match_count' => $matchCount,
                    ];
                }

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'profile_image' => $user->profile_image,
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
                    'match_count' => $stats['match_count'],
                ]);
            }
        }

        return Inertia::render('scores/leaderboard', [
            'leaderboard' => $leaderboard->values(),
        ]);
    }
}
