<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LegalDocument;

class LegalDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Privacy Policy
        LegalDocument::create([
            'type' => 'privacy_policy',
            'title' => 'Privacy Policy',
            'content' => '<h2>Privacy Policy</h2>
            <p>Deze privacy policy beschrijft hoe SSV De Moes omgaat met uw persoonlijke gegevens.</p>
            
            <h3>Welke gegevens verzamelen wij?</h3>
            <p>Wij verzamelen de volgende persoonlijke gegevens:</p>
            <ul>
                <li>Naam en contactgegevens</li>
                <li>Lidmaatschapsinformatie</li>
                <li>Wedstrijdresultaten</li>
            </ul>
            
            <h3>Hoe gebruiken wij uw gegevens?</h3>
            <p>Uw gegevens worden gebruikt voor:</p>
            <ul>
                <li>Lidmaatschapsbeheer</li>
                <li>Communicatie over activiteiten</li>
                <li>Wedstrijdadministratie</li>
            </ul>
            
            <h3>Contact</h3>
            <p>Voor vragen over deze privacy policy kunt u contact opnemen via info@ssvdemoes.nl</p>',
            'version' => '1.0',
            'is_active' => true,
            'effective_date' => now(),
        ]);

        // Terms & Conditions
        LegalDocument::create([
            'type' => 'terms_conditions',
            'title' => 'Algemene Voorwaarden',
            'content' => '<h2>Algemene Voorwaarden SSV De Moes</h2>
            
            <h3>Artikel 1: Lidmaatschap</h3>
            <p>Het lidmaatschap van SSV De Moes gaat in na goedkeuring door het bestuur.</p>
            
            <h3>Artikel 2: Verplichtingen</h3>
            <p>Leden zijn verplicht zich te houden aan:</p>
            <ul>
                <li>De statuten en reglementen van de vereniging</li>
                <li>De veiligheidsvoorschriften</li>
                <li>De gedragscode</li>
            </ul>
            
            <h3>Artikel 3: Contributie</h3>
            <p>De jaarlijkse contributie dient tijdig te worden voldaan volgens de vastgestelde tarieven.</p>
            
            <h3>Artikel 4: Aansprakelijkheid</h3>
            <p>De vereniging is niet aansprakelijk voor schade ontstaan tijdens de uitoefening van de schietsport.</p>
            
            <h3>Artikel 5: Wijzigingen</h3>
            <p>Deze voorwaarden kunnen worden gewijzigd door het bestuur. Leden worden hiervan op de hoogte gesteld.</p>',
            'version' => '1.0',
            'is_active' => true,
            'effective_date' => now(),
        ]);
    }
}
