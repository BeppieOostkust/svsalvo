<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserLegalAcceptance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LegalAcceptanceController extends Controller
{
    public function show(Request $request)
    {
        $requiredAcceptance = session('required_legal_acceptance');
        
        if (!$requiredAcceptance) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Legal/AcceptanceRequired', [
            'document' => $requiredAcceptance
        ]);
    }

    public function accept(Request $request)
    {
        $requiredAcceptance = session('required_legal_acceptance');
        
        if (!$requiredAcceptance) {
            return redirect()->route('dashboard');
        }

        $user = Auth::user();

        // Save or update acceptance
        UserLegalAcceptance::updateOrCreate(
            [
                'user_id' => $user->id,
                'legal_document_id' => $requiredAcceptance['document_id']
            ],
            [
                'version_accepted' => $requiredAcceptance['document_version'],
                'accepted_at' => now(),
                'ip_address' => $request->ip(),
            ]
        );

        // Clear session
        session()->forget('required_legal_acceptance');

        return redirect()->route('dashboard')->with('success', 
            'Bedankt voor het accepteren van de ' . $requiredAcceptance['document_title']
        );
    }

    public function decline(Request $request)
    {
        $requiredAcceptance = session('required_legal_acceptance');
        
        if (!$requiredAcceptance) {
            return redirect()->route('dashboard');
        }

        $user = Auth::user();

        // Block the user account
        User::where('id', $user->id)->update([
            'is_blocked' => true,
            'blocked_reason' => 'Algemene voorwaarden niet geaccepteerd',
            'blocked_at' => now(),
        ]);

        // Log out the user
        Auth::logout();

        // Clear session
        session()->forget('required_legal_acceptance');

        return redirect()->route('login')->with('error', 
            'Uw account is geblokkeerd omdat u de ' . $requiredAcceptance['document_title'] . 
            ' niet heeft geaccepteerd. Neem contact op met de beheerder om uw account te deblokkeren.'
        );
    }
}
