<?php

namespace App\Http\Controllers;

use App\Models\OrganizationInfo;
use App\Models\BoardMember;
use App\Models\Facility;
use App\Models\ContactInfo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrganizationController extends Controller
{
    public function index()
    {
        // Get organization information
        $organizationInfo = OrganizationInfo::active()
            ->ordered()
            ->get()
            ->groupBy('section');

        // Get active board members
        $boardMembers = BoardMember::active()
            ->ordered()
            ->get();

        // Get active facilities
        $facilities = Facility::active()
            ->ordered()
            ->get();

        // Get contact information
        $contactInfo = ContactInfo::active()->get()->keyBy('type');

        return Inertia::render('organisatie', [
            'organizationInfo' => $organizationInfo,
            'boardMembers' => $boardMembers,
            'facilities' => $facilities,
            'contactInfo' => $contactInfo,
        ]);
    }
}
