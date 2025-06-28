<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Download;

class DownloadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Download::create([
            'title' => 'Verenigingsreglement 2025',
            'slug' => 'verenigingsreglement-2025',
            'description' => 'Het complete reglement van onze schietvereniging, inclusief veiligheidsvoorschriften en huisregels.',
            'category' => 'reglement',
            'status' => 'active',
            'access_level' => 'members',
            'file_path' => 'downloads/verenigingsreglement-2025.pdf',
            'file_size' => 2048000, // 2MB
            'download_count' => 45,
            'featured' => true,
        ]);

        Download::create([
            'title' => 'Aanmeldformulier nieuwe leden',
            'slug' => 'aanmeldformulier-nieuwe-leden',
            'description' => 'Formulier voor nieuwe leden om zich aan te melden bij de vereniging.',
            'category' => 'formulier',
            'status' => 'active',
            'access_level' => 'public',
            'file_path' => 'downloads/aanmeldformulier.pdf',
            'file_size' => 512000, // 512KB
            'download_count' => 23,
            'featured' => true,
        ]);

        Download::create([
            'title' => 'Veiligheidshandleiding schietbaan',
            'slug' => 'veiligheidshandleiding-schietbaan',
            'description' => 'Uitgebreide handleiding over veilig schieten en het correct gebruik van de schietbaan.',
            'category' => 'handleiding',
            'status' => 'active',
            'access_level' => 'members',
            'file_path' => 'downloads/veiligheidshandleiding.pdf',
            'file_size' => 3072000, // 3MB
            'download_count' => 67,
            'featured' => false,
        ]);

        Download::create([
            'title' => 'Wedstrijdreglement GKP',
            'slug' => 'wedstrijdreglement-gkp',
            'description' => 'Specifieke regels en voorschriften voor wedstrijden Groot Kaliber Pistool.',
            'category' => 'wedstrijd',
            'status' => 'active',
            'access_level' => 'members',
            'file_path' => 'downloads/wedstrijdreglement-gkp.pdf',
            'file_size' => 1024000, // 1MB
            'download_count' => 34,
            'featured' => false,
        ]);

        Download::create([
            'title' => 'Trainingsschema 2025',
            'slug' => 'trainingsschema-2025',
            'description' => 'Overzicht van alle trainingen en tijden voor het jaar 2025.',
            'category' => 'training',
            'status' => 'active',
            'access_level' => 'members',
            'file_path' => 'downloads/trainingsschema-2025.pdf',
            'file_size' => 256000, // 256KB
            'download_count' => 89,
            'featured' => false,
        ]);

        Download::create([
            'title' => 'Historisch document 2020',
            'slug' => 'historisch-document-2020',
            'description' => 'Oud document dat niet meer actueel is, alleen voor archiefdoeleinden.',
            'category' => 'algemeen',
            'status' => 'archived',
            'access_level' => 'admin',
            'file_path' => 'downloads/historisch-2020.pdf',
            'file_size' => 1536000, // 1.5MB
            'download_count' => 5,
            'featured' => false,
        ]);
    }
}
