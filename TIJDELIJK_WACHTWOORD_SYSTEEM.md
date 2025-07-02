# Tijdelijk Wachtwoord Systeem

## Overzicht

Dit systeem zorgt ervoor dat nieuwe gebruikers bij eerste inlog hun tijdelijke wachtwoord moeten wijzigen naar een eigen wachtwoord. Dit verhoogt de beveiliging omdat administrators nooit de definitieve wachtwoorden van gebruikers hoeven te kennen.

## Hoe het werkt

### 1. Database Aanpassingen
- Nieuw veld `password_change_required` toegevoegd aan users tabel
- Dit veld bepaalt of een gebruiker gedwongen wordt het wachtwoord te wijzigen

### 2. Middleware
- `RequirePasswordChange` middleware controleert bij elke request of wachtwoordwijziging vereist is
- Gebruikers worden automatisch doorgeleid naar de wachtwoord wijzig pagina
- Alleen wachtwoord wijzig routes en logout zijn toegestaan wanneer wijziging vereist is

### 3. Wachtwoord Wijzig Pagina
- Mooie interface met typewriter animatie
- Persoonlijke begroeting met voornaam van gebruiker
- Geleidelijke introductie van het systeem
- Eenvoudig formulier voor wachtwoordwijziging

## Gebruikers aanmaken

### Via Artisan Commando
```bash
php artisan user:create-temp email@example.com "Gebruiker Naam" --first-name="Voornaam" --last-name="Achternaam"
```

Voorbeeld:
```bash
php artisan user:create-temp dion@ssv.nl "Dion van der Berg" --first-name="Dion" --last-name="van der Berg" --avg-name="D. van der Berg"
```

### Via Filament Admin Panel
1. Ga naar "Leden" in het admin panel
2. Klik op "Aanmaken met tijdelijk wachtwoord" (gele knop)
3. Vul de gebruiker gegevens in
4. Het systeem genereert automatisch een tijdelijk wachtwoord
5. Het tijdelijke wachtwoord wordt getoond in een persistente notificatie

## Gebruikerservaring

### Voor nieuwe gebruikers:
1. **Inloggen**: Gebruiker logt in met tijdelijk wachtwoord
2. **Typewriter animatie**: 
   - "Hoi, [Voornaam]..."
   - "Welkom bij de vernieuwde site van SSV de Moes!"
   - "Wij hebben u een tijdelijk wachtwoord gegeven om in te loggen."
   - "Hier kunt u dat nu aanpassen voor uw veiligheid."
3. **Formulier**: Eenvoudig formulier om wachtwoord te wijzigen
4. **Dashboard**: Na wijziging doorgeleid naar dashboard

### Voor administrators:
- Duidelijk overzicht in admin panel welke gebruikers nog wachtwoord moeten wijzigen
- Eenvoudig systeem om nieuwe gebruikers aan te maken
- Tijdelijke wachtwoorden worden veilig getoond maar niet opgeslagen

## Beveiliging

### Voordelen:
- Administrators kennen nooit de definitieve wachtwoorden van gebruikers
- Gebruikers worden gedwongen om sterke wachtwoorden te kiezen
- Tijdelijke wachtwoorden zijn uniek en complex
- Alle toegang is geblokkeerd totdat wachtwoord is gewijzigd

### Wachtwoord vereisten:
- Minimaal 8 karakters
- Moet bevestigd worden
- Laravel's standaard Password validatie regels

## Technische Details

### Files aangepast/toegevoegd:
- `database/migrations/*_add_password_change_required_to_users_table.php`
- `app/Models/User.php` - Model aanpassingen
- `app/Http/Middleware/RequirePasswordChange.php` - Middleware
- `app/Http/Controllers/PasswordChangeController.php` - Controller
- `app/Console/Commands/CreateUserWithTempPassword.php` - Artisan commando
- `resources/js/pages/Auth/ChangePassword.tsx` - React component
- `app/Filament/Resources/UserResource.php` - Admin interface
- `routes/web.php` - Routes
- `bootstrap/app.php` - Middleware registratie

### Routes:
- `GET /change-password` - Toon wachtwoord wijzig formulier
- `POST /change-password` - Verwerk wachtwoordwijziging

### Middleware registratie:
- Geregistreerd in `bootstrap/app.php`
- Toegepast op alle web routes
- Uitzondering voor wachtwoord wijzig routes en logout

## Test Account

Voor testing is er een test account aangemaakt:
- **Email**: dion@example.com
- **Tijdelijk wachtwoord**: TempPass123!

Log in met deze gegevens om het systeem te testen.

## Commando's voor beheer

### Test gebruiker aanmaken:
```bash
php artisan db:seed --class=TestUserSeeder
```

### Cache legen na wijzigingen:
```bash
php artisan config:clear && php artisan route:clear && php artisan cache:clear
```

### Routes controleren:
```bash
php artisan route:list | Select-String "password"
```

## Styling

De wachtwoord wijzig pagina gebruikt:
- Wit background voor eenvoudige, professionele uitstraling
- Zwarte tekst voor optimale leesbaarheid
- Typewriter animatie voor persoonlijke touch
- Responsief design
- Fade-in animaties voor vloeiende overgang van intro naar formulier

## Integratie met bestaand systeem

Het systeem integreert naadloos met:
- Bestaande legal document acceptance middleware
- Filament admin panel
- Inertia.js routing
- Laravel authenticatie systeem
- Bestaande Layout componenten

Het respecteert alle bestaande middleware en interfereert niet met andere functionaliteiten.
