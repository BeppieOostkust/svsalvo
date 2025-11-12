# Email Systeem Documentatie

## Overzicht

Het email systeem biedt een complete oplossing voor het verzenden van geautomatiseerde emails met aanpasbare HTML templates.

## Componenten

### 1. Email Templates (`EmailTemplate` model)
- **Locatie**: App\Models\EmailTemplate
- **Doel**: Opslaan van herbruikbare email templates met HTML inhoud
- **Velden**:
  - `name`: Naam van de template
  - `slug`: Unieke identifier (gebruikt in code)
  - `subject`: Email onderwerp (ondersteunt variabelen)
  - `html_content`: HTML email inhoud (ondersteunt variabelen)
  - `text_content`: Platte tekst versie (optioneel)
  - `available_variables`: Array van beschikbare variabelen
  - `is_active`: Of de template actief is
  - `category`: Categorie (user, match, activity, feedback, legal, system)
  - `description`: Beschrijving van wanneer de template wordt gebruikt

### 2. Email Logs (`EmailLog` model)
- **Locatie**: App\Models\EmailLog
- **Doel**: Logging van alle verzonden emails
- **Velden**:
  - `email_template_id`: Gekoppelde template
  - `user_id`: Gekoppelde gebruiker (optioneel)
  - `to_email`: Ontvanger email
  - `to_name`: Ontvanger naam
  - `subject`: Verzonden onderwerp
  - `html_content`: Verzonden HTML inhoud
  - `text_content`: Verzonden tekst inhoud
  - `status`: Status (pending, sent, failed)
  - `error_message`: Foutmelding bij failure
  - `sent_at`: Tijdstip van verzending
  - `variables`: Gebruikte variabelen (JSON)

### 3. Email Settings (`EmailSetting` model)
- **Locatie**: App\Models\EmailSetting
- **Doel**: In-/uitschakelen van email notificaties
- **Velden**:
  - `key`: Unieke sleutel (moet overeenkomen met template slug)
  - `name`: Weergave naam
  - `enabled`: Of de notificatie ingeschakeld is
  - `description`: Beschrijving
  - `category`: Categorie

### 4. Email Service (`EmailService` class)
- **Locatie**: App\Services\EmailService
- **Doel**: Centrale service voor het verzenden van emails

## Gebruik

### Basis gebruik: Email verzenden

```php
use App\Services\EmailService;

$emailService = new EmailService();

// Verzend email met template
$success = $emailService->sendFromTemplate(
    'new-user-temp-password',  // Template slug
    'user@example.com',         // Ontvanger email
    [                           // Variabelen
        'name' => 'Jan Jansen',
        'email' => 'user@example.com',
        'temporary_password' => 'Temp123!',
        'login_url' => route('login'),
        'site_name' => 'KNSA Vereniging',
    ],
    $user,                      // User model (optioneel)
    'Jan Jansen'               // Ontvanger naam (optioneel)
);
```

### Specifieke email types

#### 1. Nieuwe gebruiker met tijdelijk wachtwoord

```php
$emailService->sendTemporaryPasswordEmail($user, 'TempPass123!');
```

#### 2. Nieuwe wedstrijd

```php
$emailService->sendNewMatchEmail($user, $match);
```

#### 3. Nieuwe activiteit

```php
$emailService->sendNewActivityEmail($user, $activity);
```

#### 4. Feedback reactie

```php
$emailService->sendFeedbackResponseEmail($user, $feedback, $responseContent);
```

#### 5. Privacy verklaring update

```php
$emailService->sendPrivacyPolicyUpdateEmail($user, $legalDocument);
```

### Bulk emails verzenden

```php
$recipients = User::where('receive_notifications', true)->get();

$results = $emailService->sendBulk(
    'new-match',
    $recipients,
    [
        'match_name' => 'Wintercompetitie 2025',
        'match_date' => '15-01-2025',
        'site_name' => 'KNSA Vereniging',
    ]
);

// $results = ['sent' => 45, 'failed' => 2, 'skipped' => 0]
```

## Filament Resources

### 1. Email Templates Resource
- **Locatie**: Filament Admin Panel → Email Beheer → Email Templates
- **Toegang**: Alleen sitebeheerder en secretaris
- **Functies**:
  - Templates aanmaken/bewerken/verwijderen
  - HTML editor voor email inhoud
  - Variabelen beheren
  - Templates activeren/deactiveren
  - Categorieën en filters

### 2. Email Logs Resource
- **Locatie**: Filament Admin Panel → Email Beheer → Email Logs
- **Toegang**: Alleen sitebeheerder en secretaris
- **Functies**:
  - Bekijk alle verzonden emails
  - Status tracking (verzonden, mislukt, in afwachting)
  - Filter op template, status, datum
  - Bekijk foutmeldingen bij mislukte emails
  - **Read-only**: Logs kunnen niet handmatig aangemaakt worden

### 3. Email Settings Resource
- **Locatie**: Filament Admin Panel → Email Beheer → Email Settings
- **Toegang**: Alleen sitebeheerder en secretaris
- **Functies**:
  - Email notificaties in-/uitschakelen
  - Quick toggle in tabel
  - Categorie filters
  - Beschrijvingen per notificatie type

## Standaard Templates

Na het runnen van de seeder zijn de volgende templates beschikbaar:

1. **new-user-temp-password**: Nieuwe gebruiker met tijdelijk wachtwoord
2. **new-match**: Nieuwe wedstrijd aangemaakt
3. **new-activity**: Nieuwe activiteit aangemaakt
4. **feedback-response**: Reactie op feedback
5. **privacy-policy-update**: Privacy verklaring bijgewerkt

## Template Variabelen

Gebruik `{{variabele_naam}}` in je templates om dynamische waarden in te voegen.

### Algemene variabelen:
- `{{name}}`: Naam van ontvanger
- `{{site_name}}`: Naam van de website/vereniging

### User templates:
- `{{email}}`: Email adres
- `{{temporary_password}}`: Tijdelijk wachtwoord
- `{{login_url}}`: Login URL

### Match templates:
- `{{match_name}}`: Wedstrijd naam
- `{{match_date}}`: Wedstrijd datum
- `{{match_time}}`: Wedstrijd tijd
- `{{match_location}}`: Wedstrijd locatie
- `{{match_url}}`: Link naar wedstrijd

### Activity templates:
- `{{activity_name}}`: Activiteit naam
- `{{activity_date}}`: Activiteit datum
- `{{activity_time}}`: Activiteit tijd
- `{{activity_location}}`: Activiteit locatie
- `{{activity_description}}`: Activiteit beschrijving
- `{{activity_url}}`: Link naar activiteit

### Feedback templates:
- `{{feedback_title}}`: Feedback titel
- `{{feedback_content}}`: Feedback inhoud
- `{{response_content}}`: Reactie inhoud
- `{{feedback_url}}`: Link naar feedback

### Legal templates:
- `{{document_title}}`: Document titel
- `{{document_version}}`: Document versie
- `{{document_date}}`: Document datum
- `{{changes_summary}}`: Samenvatting van wijzigingen
- `{{document_url}}`: Link naar document

## Email Settings Checken

Voordat een email wordt verzonden, controleert het systeem of de notificatie ingeschakeld is:

```php
if (EmailSetting::isEnabled('new-match')) {
    // Verzend email
}
```

Dit gebeurt automatisch in de `EmailService::sendFromTemplate()` methode.

## Logging

Alle emails worden automatisch gelogd in de `email_logs` tabel met:
- Verzonden inhoud
- Status (pending → sent/failed)
- Eventuele foutmeldingen
- Timestamp van verzending
- Gebruikte variabelen

## Error Handling

Bij een mislukte email:
1. Status wordt gezet op 'failed'
2. Foutmelding wordt opgeslagen
3. Log entry wordt aangemaakt
4. `false` wordt returned vanuit `sendFromTemplate()`

## Best Practices

1. **Test templates**: Test nieuwe templates altijd eerst met test-data
2. **Variabelen documenteren**: Houd `available_variables` up-to-date
3. **HTML + Tekst**: Zorg voor een tekst versie voor email clients zonder HTML
4. **Controleer settings**: Laat gebruikers zelf bepalen welke emails ze willen ontvangen
5. **Monitor logs**: Bekijk regelmatig de logs voor mislukte emails

## Integratie met Bestaande Systemen

### User Creation (TODO)
Integreer met UserResource om automatisch email te verzenden bij nieuwe gebruikers:

```php
// In UserResource na create
$temporaryPassword = Str::random(12);
$user->password = Hash::make($temporaryPassword);
$user->save();

$emailService = new EmailService();
$emailService->sendTemporaryPasswordEmail($user, $temporaryPassword);
```

### Match Creation (TODO)
```php
// In MatchResource na create
$users = User::where('receive_match_notifications', true)->get();
foreach ($users as $user) {
    $emailService->sendNewMatchEmail($user, $match);
}
```

### Activity Creation (TODO)
```php
// In ActivityResource na create
$users = User::where('receive_activity_notifications', true)->get();
foreach ($users as $user) {
    $emailService->sendNewActivityEmail($user, $activity);
}
```

### Feedback Response (TODO)
```php
// In FeedbackResource bij toevoegen van reactie
$emailService->sendFeedbackResponseEmail(
    $feedback->user,
    $feedback,
    $response
);
```

### Legal Document Update (TODO)
```php
// In LegalDocumentResource bij update van privacy verklaring
$users = User::all();
foreach ($users as $user) {
    $emailService->sendPrivacyPolicyUpdateEmail($user, $document);
}
```

## Technische Details

- **Database migraties**: `2025_11_09_170900_create_email_templates_table.php`, `2025_11_09_170909_create_email_logs_table.php`, `2025_11_09_170944_create_email_settings_table.php`
- **Seeder**: `EmailTemplateSeeder`
- **Service Provider**: Automatisch via Laravel's service container
- **Queue Support**: Emails kunnen later naar queue worden verplaatst voor betere performance
