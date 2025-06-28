<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrganizationInfo;
use App\Models\BoardMember;
use App\Models\Facility;
use App\Models\ContactInfo;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create organization info
        OrganizationInfo::create([
            'section' => 'mission',
            'title' => 'Onze Missie',
            'content' => 'SSV De Moes streeft ernaar om een sportieve en gezellige vereniging te zijn waar iedereen zich welkom voelt. We bieden een veilige en professionele omgeving voor schietsport.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        OrganizationInfo::create([
            'section' => 'vision',
            'title' => 'Onze Visie',
            'content' => 'Wij willen de beste schietvereniging van de regio zijn, waar zowel beginners als gevorderden hun schiettechniek kunnen verbeteren in een respectvolle omgeving.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        OrganizationInfo::create([
            'section' => 'history',
            'title' => 'Onze Geschiedenis',
            'content' => 'SSV De Moes werd opgericht in 1975 en heeft sinds die tijd duizenden leden begeleid in de prachtige sport van het schieten. We zijn gevestigd in het hart van Nederland.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Create board members
        BoardMember::create([
            'name' => 'Jan van der Berg',
            'position' => 'Voorzitter',
            'email' => 'voorzitter@ssvdemoes.nl',
            'phone' => '+31 6 12345678',
            'description' => 'Jan is al 15 jaar actief bij de vereniging en heeft veel ervaring in het besturen van sportverenigingen.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        BoardMember::create([
            'name' => 'Maria Jansen',
            'position' => 'Secretaris',
            'email' => 'secretaris@ssvdemoes.nl',
            'phone' => '+31 6 87654321',
            'description' => 'Maria zorgt ervoor dat alle administratie perfect op orde is en is het aanspreekpunt voor nieuwe leden.',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        BoardMember::create([
            'name' => 'Piet Bakker',
            'position' => 'Penningmeester',
            'email' => 'penningmeester@ssvdemoes.nl',
            'phone' => '+31 6 11223344',
            'description' => 'Piet houdt alle financiën bij en zorgt ervoor dat de vereniging financieel gezond blijft.',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        // Create facilities
        Facility::create([
            'name' => 'Moderne Schietbanen',
            'description' => 'Onze vereniging beschikt over 8 moderne 25-meter schietbanen met de nieuwste veiligheidssystemen en professionele verlichting.',
            'icon_type' => 'target',
            'icon_color' => 'blue',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Facility::create([
            'name' => 'Gezellige Kantine',
            'description' => 'Na het schieten kunt u gezellig napraten in onze kantine met verse koffie, thee en kleine hapjes.',
            'icon_type' => 'users',
            'icon_color' => 'green',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        Facility::create([
            'name' => 'Materiaalverhuur',
            'description' => 'Voor beginners hebben we een uitgebreid assortiment huurwapens en accessoires beschikbaar.',
            'icon_type' => 'shield',
            'icon_color' => 'purple',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        Facility::create([
            'name' => 'Training & Cursussen',
            'description' => 'We bieden regelmatig trainingen en cursussen aan voor alle niveaus, van beginner tot gevorderd.',
            'icon_type' => 'book',
            'icon_color' => 'yellow',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        // Create contact info
        ContactInfo::create([
            'type' => 'address',
            'title' => 'Bezoekadres',
            'data' => json_encode([
                'street' => 'De Schacht 5',
                'postal_code' => '5107 RD',
                'city' => 'Dongen',
                'country' => 'Nederland',
                'google_maps_url' => 'https://maps.app.goo.gl/SGMjk71W4rH9JT7g7',
                'latitude' => 51.6267,
                'longitude' => 4.9389,
            ]),
            'additional_info' => 'Parkeren is gratis beschikbaar op het terrein',
            'is_active' => true,
        ]);

        ContactInfo::create([
            'type' => 'contact',
            'title' => 'Contact',
            'data' => json_encode([
                'email' => 'info@ssvdemoes.nl',
                'phone' => '+31 33 123 4567',
                'website' => 'https://www.ssvdemoes.nl',
            ]),
            'additional_info' => 'Voor vragen kunt u ons altijd bellen of mailen',
            'is_active' => true,
        ]);

        ContactInfo::create([
            'type' => 'opening_hours',
            'title' => 'Openingstijden',
            'data' => json_encode([
                'hours' => [
                    ['day' => 'Maandag', 'hours' => 'Gesloten'],
                    ['day' => 'Dinsdag', 'hours' => '19:00 - 22:00'],
                    ['day' => 'Woensdag', 'hours' => '19:00 - 22:00'],
                    ['day' => 'Donderdag', 'hours' => 'Gesloten'],
                    ['day' => 'Vrijdag', 'hours' => '19:00 - 22:00'],
                    ['day' => 'Zaterdag', 'hours' => '14:00 - 17:00'],
                    ['day' => 'Zondag', 'hours' => 'Gesloten'],
                ]
            ]),
            'additional_info' => 'Tijdens schoolvakanties kunnen de tijden afwijken',
            'is_active' => true,
        ]);
    }
}
