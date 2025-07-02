<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class PasswordChangeController extends Controller
{
    /**
     * Show the password change form
     */
    public function showForm()
    {
        $user = auth()->user();
        
        return Inertia::render('auth/ChangePassword', [
            'user' => [
                'first_name' => $user->first_name,
                'name' => $user->name,
                'full_name' => $user->first_name ? $user->first_name : $user->name,
            ]
        ]);
    }

    /**
     * Handle password change
     */
    public function change(Request $request)
    {
        $user = auth()->user();
        
        // For users with password_change_required, we don't need current password
        if ($user->password_change_required) {
            $request->validate([
                'new_password' => ['required', 'confirmed', Password::defaults()],
            ], [
                'new_password.required' => 'Het nieuwe wachtwoord is verplicht.',
                'new_password.confirmed' => 'De wachtwoord bevestiging komt niet overeen.',
                'new_password.min' => 'Het nieuwe wachtwoord moet minimaal 8 karakters bevatten.',
            ]);
        } else {
            // For regular password changes, require current password
            $request->validate([
                'current_password' => ['required'],
                'new_password' => ['required', 'confirmed', Password::defaults()],
            ], [
                'current_password.required' => 'Het huidige wachtwoord is verplicht.',
                'new_password.required' => 'Het nieuwe wachtwoord is verplicht.',
                'new_password.confirmed' => 'De wachtwoord bevestiging komt niet overeen.',
                'new_password.min' => 'Het nieuwe wachtwoord moet minimaal 8 karakters bevatten.',
            ]);

            // Check if current password is correct
            if (!Hash::check($request->current_password, $user->getAuthPassword())) {
                return back()->withErrors([
                    'current_password' => 'Het huidige wachtwoord is onjuist.'
                ]);
            }
        }

        // Update password and remove requirement flag
        $user->update([
            'password' => Hash::make($request->new_password),
            'password_change_required' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Wachtwoord succesvol gewijzigd! Welkom bij SSV de Moes.');
    }
}
