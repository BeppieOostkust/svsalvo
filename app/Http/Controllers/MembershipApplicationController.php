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
            'email' => [
                'required',
                'email',
                'unique:membership_applications,email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'telefoonnummer' => [
                'required',
                'string',
                'max:20',
                'regex:/^(\+31|0)([1-9]{1}[0-9]{8}|6{1}[1-9]{1}[0-9]{7})$/'
            ],
            'geboortedatum' => [
                'required',
                'date',
                'before:' . now()->subYears(12)->format('Y-m-d'),
                'after:' . now()->subYears(100)->format('Y-m-d')
            ],
        ], [
            'voornaam.required' => 'Voornaam is verplicht.',
            'achternaam.required' => 'Achternaam is verplicht.',
            'email.required' => 'E-mailadres is verplicht.',
            'email.email' => 'Vul een geldig e-mailadres in.',
            'email.unique' => 'Dit e-mailadres is al gebruikt voor een aanvraag.',
            'email.regex' => 'Vul een geldig e-mailadres in.',
            'telefoonnummer.required' => 'Telefoonnummer is verplicht.',
            'telefoonnummer.regex' => 'Vul een geldig Nederlands telefoonnummer in (bijv. 0612345678 of +31612345678).',
            'geboortedatum.required' => 'Geboortedatum is verplicht.',
            'geboortedatum.date' => 'Vul een geldige geboortedatum in.',
            'geboortedatum.before' => 'Je moet minimaal 12 jaar oud zijn om lid te worden.',
            'geboortedatum.after' => 'Vul een geldige geboortedatum in.',
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
