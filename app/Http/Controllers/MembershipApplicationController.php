<?php

namespace App\Http\Controllers;

use App\Models\MembershipApplication;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class MembershipApplicationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'voornaam' => 'required|string|max:255',
            'achternaam' => 'required|string|max:255',
            'email' => 'required|email|unique:membership_applications,email',
            'telefoonnummer' => 'required|string|max:20',
            'geboortedatum' => 'required|date|before:today',
        ], [
            'voornaam.required' => 'Voornaam is verplicht.',
            'achternaam.required' => 'Achternaam is verplicht.',
            'email.required' => 'E-mailadres is verplicht.',
            'email.email' => 'Vul een geldig e-mailadres in.',
            'email.unique' => 'Dit e-mailadres is al gebruikt voor een aanvraag.',
            'telefoonnummer.required' => 'Telefoonnummer is verplicht.',
            'geboortedatum.required' => 'Geboortedatum is verplicht.',
            'geboortedatum.date' => 'Vul een geldige geboortedatum in.',
            'geboortedatum.before' => 'Geboortedatum moet in het verleden liggen.',
        ]);

        // Calculate age
        $birthDate = Carbon::parse($validated['geboortedatum']);
        $age = $birthDate->age;

        // Create the application
        MembershipApplication::create([
            'voornaam' => $validated['voornaam'],
            'achternaam' => $validated['achternaam'],
            'email' => $validated['email'],
            'telefoonnummer' => $validated['telefoonnummer'],
            'geboortedatum' => $validated['geboortedatum'],
            'leeftijd' => $age,
            'status' => 'nieuw',
            'aangemeld_op' => now(),
        ]);

        return redirect()->route('membership.success');
    }

    public function success()
    {
        return Inertia::render('membership/success');
    }
}
