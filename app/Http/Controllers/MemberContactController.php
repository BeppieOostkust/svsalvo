<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberContactController extends Controller
{
    public function index()
    {
        $members = User::query()
            ->where('id', '!=', auth()->id())
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->show_in_participants ? $user->name : 'Anoniem Lid',
                    'email' => $user->show_contact_info ? $user->email : null,
                    'phone' => $user->show_contact_info ? $user->phone : null,
                    'disciplines' => json_decode($user->disciplines, true) ?? [],
                    'bio' => $user->show_contact_info ? $user->bio : null,
                    'profile_photo' => $user->profile_photo_url,
                ];
            });

        return Inertia::render('Members/Contact', [
            'members' => $members
        ]);
    }

    public function updatePrivacySettings(Request $request)
    {
        $validated = $request->validate([
            'show_contact_info' => 'boolean',
            'show_scores_public' => 'boolean',
            'show_in_participants' => 'boolean',
        ]);

        $user = auth()->user();
        $user->update($validated);

        return back()->with('success', 'Privacy-instellingen bijgewerkt');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'disciplines' => 'nullable|array',
            'bio' => 'nullable|string|max:500',
        ]);

        if (isset($validated['disciplines'])) {
            $validated['disciplines'] = json_encode($validated['disciplines']);
        }

        $user = auth()->user();
        $user->update($validated);

        return back()->with('success', 'Profiel bijgewerkt');
    }
} 