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
                // Determine which name to show based on show_full_name setting
                $displayName = 'Anonieme Lid'; // Default fallback
                
                if ($user->show_full_name && $user->first_name && $user->last_name) {
                    // Show full name
                    $displayName = "{$user->first_name} {$user->last_name}";
                } elseif ($user->avg_name) {
                    // Show AVG name (default)
                    $displayName = $user->avg_name;
                } elseif ($user->name) {
                    // Fallback to regular name
                    $displayName = $user->name;
                }
                
                // Base user data that's always shown
                $memberData = [
                    'id' => $user->id,
                    'name' => $displayName,
                    'profile_image_url' => $user->profile_image_url,
                    'member_since' => $user->member_since,
                    'position' => $user->position,
                    'preferred_discipline' => $user->preferred_discipline,
                    'show_contact_info' => $user->show_contact_info,
                ];

                // Check privacy setting for contact info
                if ($user->show_contact_info) {
                    // Add contact information only if user wants to share it
                    $memberData = array_merge($memberData, [
                        'first_name' => $user->show_full_name ? $user->first_name : null,
                        'last_name' => $user->show_full_name ? $user->last_name : null,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'city' => $user->city,
                        'bio' => $user->bio,
                        'is_anonymous' => false,
                    ]);
                } else {
                    // Don't show contact information
                    $memberData = array_merge($memberData, [
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

        // Separate board members based on "Toon in organisatie overzicht" checkbox
        $boardMembers = $members->filter(function ($member) {
            // Find the user to check show_in_organization setting
            $user = User::find($member['id']);
            return $user && $user->show_in_organization;
        });

        $regularMembers = $members->filter(function ($member) {
            // Find the user to check show_in_organization setting  
            $user = User::find($member['id']);
            return !$user || !$user->show_in_organization;
        });

        return Inertia::render('Vereniging/Index', [
            'boardMembers' => $boardMembers->values(),
            'regularMembers' => $regularMembers->values(),
            'totalMembers' => $members->count(),
            'totalActiveMembers' => User::where('is_active_member', true)->count(),
        ]);
    }
}
