<?php

namespace App\Http\Controllers;

use App\Models\ClubWeapon;
use Inertia\Inertia;

class ClubWeaponController extends Controller
{
    public function index()
    {
        $clubWeapons = ClubWeapon::query()
            ->active()
            ->ordered()
            ->get()
            ->map(fn (ClubWeapon $weapon) => [
                'id' => $weapon->id,
                'name' => $weapon->name,
                'weapon_type' => $weapon->weapon_type,
                'image' => $weapon->image,
                'image_url' => $weapon->image_url,
            ]);

        return Inertia::render('Clubwapens/Index', [
            'clubWeapons' => $clubWeapons,
        ]);
    }
}
