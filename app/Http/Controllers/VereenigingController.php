<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VereenigingController extends Controller
{
    /**
     * Display the organization page with members based on privacy settings
     */
    public function index()
    {
        // Get all active members, sorted so that those who share contact info come first
        $members = User::where('is_active_member', true)
            ->orderByRaw('show_contact_info DESC') // Those who share contact info first (1 before 0)
            ->orderBy('position', 'asc')
            ->orderBy('last_name', 'asc')
            ->orderBy('first_name', 'asc')
            ->get()
            ->map(function ($user) {
                // Base user data that's always shown
                $memberData = [
                    'id' => $user->id,
                    'profile_image_url' => $user->profile_image_url,
                    'member_since' => $user->member_since,
                    'position' => $user->position,
                    'preferred_discipline' => $user->preferred_discipline,
                    'show_contact_info' => $user->show_contact_info,
                ];

                // Check privacy setting for contact info
                if ($user->show_contact_info) {
                    // Show full contact information
                    $memberData = array_merge($memberData, [
                        'name' => $user->first_name && $user->last_name 
                            ? "{$user->first_name} {$user->last_name}" 
                            : $user->name,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'city' => $user->city,
                        'bio' => $user->bio,
                        'is_anonymous' => false,
                    ]);
                } else {
                    // Show as anonymous member
                    $memberData = array_merge($memberData, [
                        'name' => 'Anonieme Lid',
                        'first_name' => null,
                        'last_name' => null,
                        'email' => null,
                        'phone' => null,
                        'city' => null,
                        'bio' => null,
                        'is_anonymous' => true,
                    ]);
                }

                return $memberData;
            });

        // Separate board members (with positions) from regular members
        // Both groups will maintain the same sorting (contact sharers first)
        $boardMembers = $members->filter(function ($member) {
            return !empty($member['position']);
        });

        $regularMembers = $members->filter(function ($member) {
            return empty($member['position']);
        });

        return Inertia::render('Vereniging/Index', [
            'boardMembers' => $boardMembers->values(),
            'regularMembers' => $regularMembers->values(),
            'totalMembers' => $members->count(),
            'totalActiveMembers' => User::where('is_active_member', true)->count(),
        ]);
    }
}
