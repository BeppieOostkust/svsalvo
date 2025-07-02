<?php

namespace Database\Seeders;

use App\Models\Rule;
use App\Models\Price;
use Illuminate\Database\Seeder;

class RulesAndPricesSeeder extends Seeder
{
    public function run()
    {
        // Sample Rules
        Rule::create([
            'title' => 'Veiligheidsregels',
            'content' => "1. Wapens mogen alleen op de schietserie worden gericht\n2. Altijd gehoorsbescherming dragen\n3. Wapens moeten te allen tijde ongeladen zijn buiten de schietlijn\n4. Nooit een wapen op een persoon richten",
            'category' => 'Veiligheid',
            'order' => 1,
            'is_active' => true,
        ]);

        Rule::create([
            'title' => 'Gedragsregels',
            'content' => "1. Respectvol gedrag jegens alle leden\n2. Alcohol en drugs zijn ten strengste verboden\n3. Roken is alleen toegestaan in aangewezen gebieden\n4. Houd de accommodatie schoon",
            'category' => 'Gedrag',
            'order' => 2,
            'is_active' => true,
        ]);

        Rule::create([
            'title' => 'Tijden en toegang',
            'content' => "1. De baan is geopend volgens de vastgestelde tijden\n2. Toegang alleen voor leden en begeleide gasten\n3. Reservering verplicht voor wedstrijden\n4. Sluit af na gebruik",
            'category' => 'Algemeen',
            'order' => 3,
            'is_active' => true,
        ]);

        // Sample Prices
        Price::create([
            'title' => 'Lidmaatschap Senioren',
            'description' => 'Volledig lidmaatschap voor leden van 18 jaar en ouder',
            'amount' => 120.00,
            'currency' => 'EUR',
            'category' => 'Lidmaatschap',
            'period' => 'per jaar',
            'order' => 1,
            'is_active' => true,
        ]);

        Price::create([
            'title' => 'Lidmaatschap Junioren',
            'description' => 'Lidmaatschap voor leden tot 18 jaar',
            'amount' => 60.00,
            'currency' => 'EUR',
            'category' => 'Lidmaatschap',
            'period' => 'per jaar',
            'order' => 2,
            'is_active' => true,
        ]);

        Price::create([
            'title' => 'Inschrijfgeld',
            'description' => 'Eenmalige kosten bij aanmelding',
            'amount' => 25.00,
            'currency' => 'EUR',
            'category' => 'Lidmaatschap',
            'period' => 'eenmalig',
            'order' => 3,
            'is_active' => true,
        ]);

        Price::create([
            'title' => 'Beginnerscursus',
            'description' => 'Complete cursus voor beginners (8 lessen)',
            'amount' => 75.00,
            'currency' => 'EUR',
            'category' => 'Cursus',
            'period' => 'eenmalig',
            'order' => 4,
            'is_active' => true,
        ]);

        Price::create([
            'title' => 'Munitie (.22 LR)',
            'description' => 'Per doos van 50 stuks',
            'amount' => 8.50,
            'currency' => 'EUR',
            'category' => 'Materiaal',
            'period' => 'per doos',
            'order' => 5,
            'is_active' => true,
        ]);

        Price::create([
            'title' => 'Wapenkluis huur',
            'description' => 'Huur van wapenkluis in het clubhuis',
            'amount' => 15.00,
            'currency' => 'EUR',
            'category' => 'Materiaal',
            'period' => 'per maand',
            'order' => 6,
            'is_active' => true,
        ]);
    }
}
