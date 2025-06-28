<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\User;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = User::where('is_admin', true)->first();
        
        if (!$author) {
            $this->command->error('No admin user found to assign as author');
            return;
        }

        Article::create([
            'title' => 'Welkom bij onze schietvereniging!',
            'slug' => 'welkom-bij-onze-schietvereniging',
            'excerpt' => 'Een hartelijk welkom aan alle nieuwe en bestaande leden van onze dynamische schietvereniging.',
            'content' => '<p>Beste schietliefhebbers,</p><p>Met veel plezier verwelkomen wij je bij onze schietvereniging! Of je nu een ervaren schutter bent of net begint met deze prachtige sport, bij ons vind je de juiste begeleiding en faciliteiten.</p><p>Onze vereniging biedt uitstekende trainingsmogelijkheden voor verschillende disciplines, waaronder groot kaliber pistool (GKP), klein kaliber pistool (KKP) en luchtpistool. We hebben moderne faciliteiten en ervaren instructeurs die je helpen je vaardigheden te ontwikkelen.</p><p>Kom gerust langs voor een kennismaking!</p>',
            'status' => 'published',
            'published_at' => now()->subDays(1),
            'featured' => true,
            'allow_comments' => true,
            'author_id' => $author->id,
        ]);

        Article::create([
            'title' => 'Nieuwe wedstrijdregels 2025',
            'slug' => 'nieuwe-wedstrijdregels-2025',
            'excerpt' => 'Vanaf dit jaar gelden er enkele nieuwe regels voor onze interne wedstrijden. Lees hier wat er verandert.',
            'content' => '<p>Beste leden,</p><p>Per 1 januari 2025 zijn er enkele wijzigingen doorgevoerd in onze wedstrijdregels. Deze wijzigingen zijn bedoeld om de veiligheid te verhogen en de wedstrijden nog eerlijker te maken.</p><h3>Belangrijkste wijzigingen:</h3><ul><li>Nieuwe veiligheidsprocedures bij het hanteren van wapens</li><li>Aangepaste scoring methodes voor bepaalde disciplines</li><li>Verplichte veiligheidsuitrusting voor alle deelnemers</li></ul><p>Voor de volledige regelgeving kunt u terecht bij de wedstrijdcommissie.</p>',
            'status' => 'published',
            'published_at' => now()->subDays(5),
            'featured' => false,
            'allow_comments' => true,
            'author_id' => $author->id,
        ]);

        Article::create([
            'title' => 'Trainingsschema winter 2025',
            'slug' => 'trainingsschema-winter-2025',
            'excerpt' => 'Het nieuwe trainingsschema voor de winterperiode is bekend. Bekijk hier de tijden en locaties.',
            'content' => '<p>Het trainingsschema voor de winterperiode (januari - maart 2025) is vastgesteld:</p><h3>Trainingstijden:</h3><ul><li><strong>Maandag:</strong> 19:00-21:00 - Beginnerstraining</li><li><strong>Woensdag:</strong> 18:00-22:00 - Vrije training alle disciplines</li><li><strong>Vrijdag:</strong> 19:00-21:00 - Gevorderden training</li><li><strong>Zaterdag:</strong> 14:00-17:00 - Jeugdtraining</li></ul><p>Let op: tijdens schoolvakanties kunnen de tijden afwijken. Check altijd de agenda voor actuele informatie.</p>',
            'status' => 'published',
            'published_at' => now()->subDays(10),
            'featured' => false,
            'allow_comments' => true,
            'author_id' => $author->id,
        ]);

        Article::create([
            'title' => 'Aankomende ledenvergadering',
            'slug' => 'aankomende-ledenvergadering',
            'excerpt' => 'Op 15 juli vindt de halfjaarlijkse ledenvergadering plaats. Alle leden zijn van harte welkom.',
            'content' => '<p>Beste leden,</p><p>Hierbij nodigen wij u uit voor de halfjaarlijkse ledenvergadering die plaatsvindt op:</p><p><strong>Datum:</strong> 15 juli 2025<br><strong>Tijd:</strong> 20:00<br><strong>Locatie:</strong> Clubhuis</p><h3>Agenda:</h3><ul><li>Opening en mededelingen</li><li>Financieel verslag</li><li>Wedstrijdresultaten</li><li>Plannen voor het nieuwe seizoen</li><li>Rondvraag</li><li>Sluiting</li></ul><p>Uw aanwezigheid wordt zeer op prijs gesteld!</p>',
            'status' => 'published',
            'published_at' => now()->subHours(6),
            'featured' => true,
            'allow_comments' => true,
            'author_id' => $author->id,
        ]);

        Article::create([
            'title' => 'Concept: Nieuwe faciliteiten',
            'slug' => 'concept-nieuwe-faciliteiten',
            'excerpt' => 'We onderzoeken de mogelijkheden voor het uitbreiden van onze schietfaciliteiten.',
            'content' => '<p>Dit is een concept artikel over mogelijke uitbreidingen van onze faciliteiten. Dit artikel is nog niet gepubliceerd en dient als voorbeeld van een concept artikel.</p>',
            'status' => 'draft',
            'published_at' => null,
            'featured' => false,
            'allow_comments' => true,
            'author_id' => $author->id,
        ]);
    }
}
