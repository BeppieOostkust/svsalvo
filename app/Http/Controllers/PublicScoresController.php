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
}
