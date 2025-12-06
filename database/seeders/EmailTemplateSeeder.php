<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use App\Models\EmailSetting;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Template 1: New User with Temporary Password
        EmailTemplate::create([
            'name' => 'Nieuwe Gebruiker - Tijdelijk Wachtwoord',
            'slug' => 'new-user-temp-password',
            'subject' => 'Welkom bij {{site_name}} - Jouw Account Gegevens',
            'html_content' => '<h1>Goeiedag {{name}}!</h1>
<p>Je account is succesvol aangemaakt bij {{site_name}}.</p>
<h2>Jouw inloggegevens:</h2>
<p><strong>Email:</strong> {{email}}<br>
<strong>Tijdelijk wachtwoord:</strong> {{temporary_password}}</p>
<p><a href="{{login_url}}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Inloggen</a></p>
<p><strong>Belangrijk:</strong> Wijzig je wachtwoord na je eerste login voor de veiligheid.</p>
<p>Met vriendelijke groet,<br>Het {{site_name}} Team</p>',
            'text_content' => 'Welkom {{name}}!

Je account is succesvol aangemaakt bij {{site_name}}.

Jouw inloggegevens:
Email: {{email}}
Tijdelijk wachtwoord: {{temporary_password}}

Login op: {{login_url}}

Belangrijk: Wijzig je wachtwoord na je eerste login voor de veiligheid.

Met vriendelijke groet,
Besttuur SSV de Moes',
            'available_variables' => ['name', 'email', 'temporary_password', 'login_url', 'site_name'],
            'is_active' => true,
            'category' => 'user',
            'description' => 'Verzonden wanneer een nieuwe gebruiker wordt aangemaakt met een tijdelijk wachtwoord.',
        ]);

        EmailSetting::create([
            'key' => 'new-user-temp-password',
            'name' => 'Nieuwe Gebruiker - Tijdelijk Wachtwoord',
            'enabled' => true,
            'description' => 'Verzend email bij het aanmaken van een nieuwe gebruiker met tijdelijk wachtwoord',
            'category' => 'user',
        ]);

        // Template 2: New Match
        EmailTemplate::create([
            'name' => 'Nieuwe Wedstrijd Aangemaakt',
            'slug' => 'new-match',
            'subject' => 'Nieuwe Wedstrijd: {{match_name}}',
            'html_content' => '<h1>Nieuwe Wedstrijd Aangemaakt</h1>
<p>Hallo {{name}},</p>
<p>Er is een nieuwe wedstrijd gepland!</p>
<h2>Wedstrijd Details:</h2>
<p><strong>Naam:</strong> {{match_name}}<br>
<strong>Datum:</strong> {{match_date}}<br>
<strong>Tijd:</strong> {{match_time}}<br>
<strong>Locatie:</strong> {{match_location}}</p>
<p><a href="{{match_url}}" style="background-color: #2196F3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Bekijk Wedstrijd</a></p>
<p>Vergeet niet om je in te schrijven!</p>
<p>Met sportieve groet,<br>Het {{site_name}} Team</p>',
            'text_content' => 'Nieuwe Wedstrijd Aangemaakt

Hallo {{name}},

Er is een nieuwe wedstrijd gepland!

Wedstrijd Details:
Naam: {{match_name}}
Datum: {{match_date}}
Tijd: {{match_time}}
Locatie: {{match_location}}

Bekijk op: {{match_url}}

Vergeet niet om je in te schrijven!

Met sportieve groet,
Het {{site_name}} Team',
            'available_variables' => ['name', 'match_name', 'match_date', 'match_time', 'match_location', 'match_url', 'site_name'],
            'is_active' => true,
            'category' => 'match',
            'description' => 'Verzonden wanneer een nieuwe wedstrijd wordt aangemaakt.',
        ]);

        EmailSetting::create([
            'key' => 'new-match',
            'name' => 'Nieuwe Wedstrijd',
            'enabled' => true,
            'description' => 'Verzend email bij het aanmaken van een nieuwe wedstrijd',
            'category' => 'match',
        ]);

        // Template 3: New Activity
        EmailTemplate::create([
            'name' => 'Nieuwe Activiteit Aangemaakt',
            'slug' => 'new-activity',
            'subject' => 'Nieuwe Activiteit: {{activity_name}}',
            'html_content' => '<h1>Nieuwe Activiteit!</h1>
<p>Hallo {{name}},</p>
<p>Er is een nieuwe activiteit gepland die je niet wilt missen!</p>
<h2>Activiteit Details:</h2>
<p><strong>Naam:</strong> {{activity_name}}<br>
<strong>Datum:</strong> {{activity_date}}<br>
<strong>Tijd:</strong> {{activity_time}}<br>
<strong>Locatie:</strong> {{activity_location}}</p>
<p><strong>Beschrijving:</strong><br>{{activity_description}}</p>
<p><a href="{{activity_url}}" style="background-color: #FF9800; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Meer Informatie</a></p>
<p>We hopen je te zien!</p>
<p>Met vriendelijke groet,<br>Het {{site_name}} Team</p>',
            'text_content' => 'Nieuwe Activiteit!

Hallo {{name}},

Er is een nieuwe activiteit gepland die je niet wilt missen!

Activiteit Details:
Naam: {{activity_name}}
Datum: {{activity_date}}
Tijd: {{activity_time}}
Locatie: {{activity_location}}

Beschrijving:
{{activity_description}}

Meer info: {{activity_url}}

We hopen je te zien!

Met vriendelijke groet,
Het {{site_name}} Team',
            'available_variables' => ['name', 'activity_name', 'activity_date', 'activity_time', 'activity_location', 'activity_description', 'activity_url', 'site_name'],
            'is_active' => true,
            'category' => 'activity',
            'description' => 'Verzonden wanneer een nieuwe activiteit wordt aangemaakt.',
        ]);

        EmailSetting::create([
            'key' => 'new-activity',
            'name' => 'Nieuwe Activiteit',
            'enabled' => true,
            'description' => 'Verzend email bij het aanmaken van een nieuwe activiteit',
            'category' => 'activity',
        ]);

        // Template 4: Feedback Response
        EmailTemplate::create([
            'name' => 'Reactie op Jouw Feedback',
            'slug' => 'feedback-response',
            'subject' => 'Reactie op je feedback: {{feedback_title}}',
            'html_content' => '<h1>Reactie op Jouw Feedback</h1>
<p>Hallo {{name}},</p>
<p>We hebben gereageerd op jouw feedback!</p>
<h2>Jouw Feedback:</h2>
<p><strong>Titel:</strong> {{feedback_title}}</p>
<p>{{feedback_content}}</p>
<h2>Onze Reactie:</h2>
<p>{{response_content}}</p>
<p><a href="{{feedback_url}}" style="background-color: #9C27B0; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Bekijk Feedback</a></p>
<p>Bedankt voor je bijdrage aan onze vereniging!</p>
<p>Met vriendelijke groet,<br>Het {{site_name}} Team</p>',
            'text_content' => 'Reactie op Jouw Feedback

Hallo {{name}},

We hebben gereageerd op jouw feedback!

Jouw Feedback:
Titel: {{feedback_title}}
{{feedback_content}}

Onze Reactie:
{{response_content}}

Bekijk op: {{feedback_url}}

Bedankt voor je bijdrage aan onze vereniging!

Met vriendelijke groet,
Het {{site_name}} Team',
            'available_variables' => ['name', 'feedback_title', 'feedback_content', 'response_content', 'feedback_url', 'site_name'],
            'is_active' => true,
            'category' => 'feedback',
            'description' => 'Verzonden wanneer er een reactie is gegeven op feedback van een gebruiker.',
        ]);

        EmailSetting::create([
            'key' => 'feedback-response',
            'name' => 'Reactie op Feedback',
            'enabled' => true,
            'description' => 'Verzend email bij een reactie op gebruiker feedback',
            'category' => 'feedback',
        ]);

        // Template 5: Privacy Policy Update
        EmailTemplate::create([
            'name' => 'Privacy Verklaring Bijgewerkt',
            'slug' => 'privacy-policy-update',
            'subject' => 'Belangrijke Update: {{document_title}}',
            'html_content' => '<h1>Privacy Verklaring Bijgewerkt</h1>
<p>Hallo {{name}},</p>
<p>We informeren je graag dat onze Privacy Verklaring is bijgewerkt.</p>
<h2>Document Details:</h2>
<p><strong>Document:</strong> {{document_title}}<br>
<strong>Versie:</strong> {{document_version}}<br>
<strong>Datum:</strong> {{document_date}}</p>
<h2>Samenvatting van Wijzigingen:</h2>
<p>{{changes_summary}}</p>
<p><a href="{{document_url}}" style="background-color: #F44336; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Lees Volledige Privacy Verklaring</a></p>
<p>Het is belangrijk dat je deze wijzigingen bekijkt. Door onze diensten te blijven gebruiken, ga je akkoord met de bijgewerkte voorwaarden.</p>
<p>Met vriendelijke groet,<br>Het {{site_name}} Team</p>',
            'text_content' => 'Privacy Verklaring Bijgewerkt

Hallo {{name}},

We informeren je graag dat onze Privacy Verklaring is bijgewerkt.

Document Details:
Document: {{document_title}}
Versie: {{document_version}}
Datum: {{document_date}}

Samenvatting van Wijzigingen:
{{changes_summary}}

Lees op: {{document_url}}

Het is belangrijk dat je deze wijzigingen bekijkt. Door onze diensten te blijven gebruiken, ga je akkoord met de bijgewerkte voorwaarden.

Met vriendelijke groet,
Het {{site_name}} Team',
            'available_variables' => ['name', 'document_title', 'document_version', 'document_date', 'changes_summary', 'document_url', 'site_name'],
            'is_active' => true,
            'category' => 'legal',
            'description' => 'Verzonden wanneer de privacy verklaring of andere juridische documenten worden bijgewerkt.',
        ]);

        EmailSetting::create([
            'key' => 'privacy-policy-update',
            'name' => 'Privacy Verklaring Update',
            'enabled' => true,
            'description' => 'Verzend email bij update van privacy verklaring',
            'category' => 'legal',
        ]);

        $this->command->info('Email templates en settings zijn succesvol aangemaakt!');
    }
}

