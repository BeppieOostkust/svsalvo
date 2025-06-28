<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Matches;
use App\Models\MatchRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MatchRegistrationController extends Controller
{


    /**
     * Show user's match registrations
     */
    public function myRegistrations()
    {
        $user = Auth::user();
        
        $registrations = MatchRegistration::with('match')
            ->where('user_id', $user->id)
            ->orderBy('registered_at', 'desc')
            ->get();

        return Inertia::render('Dashboard/MyMatchRegistrations', [
            'registrations' => $registrations,
        ]);
    }
}
