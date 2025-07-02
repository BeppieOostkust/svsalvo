# 🔧 Maintenance Mode - SSV De Moes

Deze documentatie legt uit hoe je de maintenance mode voor de SSV De Moes website kunt gebruiken.

## 📋 Overzicht

Er zijn twee maintenance templates beschikbaar:
- **503.blade.php** - Standaard Laravel 503 error pagina (eenvoudig)
- **maintenance.blade.php** - Custom maintenance pagina (uitgebreid met animaties)

## 🚀 Gebruik

### Eenvoudige Commands

```bash
# Maintenance mode aanzetten
php artisan site:maintenance down

# Maintenance mode uitzetten  
php artisan site:maintenance up
```

### Met Secret Bypass Token

```bash
# Maintenance mode aanzetten met bypass token
php artisan site:maintenance down --secret=geheime-code

# Website bezoeken tijdens maintenance (alleen voor admins)
https://jouwsite.nl/?secret=geheime-code
```

### Laravel Standaard Commands

```bash
# Standaard Laravel maintenance mode
php artisan down --render="maintenance"
php artisan up

# Met retry tijd (503 header)
php artisan down --retry=60

# Met secret voor bypass
php artisan down --secret="admin123"
```

## 🎨 Templates

### 1. Standaard Template (503.blade.php)
- Automatische 503 HTTP status
- Eenvoudig en snel
- Auto-refresh elke 30 seconden
- Basis contact informatie

### 2. Custom Template (maintenance.blade.php)  
- Mooie animaties en gradient design
- Progress bar animatie
- Uitgebreide contact informatie
- Auto-refresh elke 60 seconden
- Countdown timer
- Klikbare refresh functie

## 📱 Features

### Beide Templates hebben:
- **Responsive Design** - Werkt op alle apparaten
- **Auto-refresh** - Controleert automatisch wanneer site weer online is
- **Contact Info** - Email en telefoon voor vragen
- **Professional Look** - Past bij SSV De Moes branding

### Extra Features Custom Template:
- **Loading Animaties** - Visual feedback voor gebruikers
- **Feature Preview** - Toont wat er verbeterd wordt
- **Countdown Timer** - Live timer tot volgende refresh
- **Manual Refresh** - Klik op timer voor directe refresh

## 🛠️ Aanpassingen

### Contact Gegevens Wijzigen

Edit de template bestanden:
```html
<!-- Email -->
<a href="mailto:info@ssvdemoes.nl">info@ssvdemoes.nl</a>

<!-- Telefoon -->
<a href="tel:+31162123456">0162 - 123 456</a>

<!-- Adres -->
De Schacht 5, 5107 RD Dongen, Nederland
```

### Auto-refresh Tijd Aanpassen

In de JavaScript sectie onderaan:
```javascript
// 503.blade.php - refresh elke 30 seconden
setTimeout(function() {
    window.location.reload();
}, 30000);

// maintenance.blade.php - refresh elke 60 seconden  
let refreshInterval = setInterval(function() {
    window.location.reload();
}, 60000);
```

### Custom Bericht Toevoegen

Edit de templates en voeg je eigen bericht toe in de HTML.

## 🔒 Security

### Bypass Token Gebruik
- Gebruik altijd een sterke, willekeurige token
- Deel de bypass URL alleen met beheerders
- Token is alleen geldig tijdens maintenance mode

### Best Practices
```bash
# Goede tokens (voorbeelden)
--secret="maint-2024-update-xyz789"
--secret="emergency-fix-abc123"  

# Slechte tokens (vermijd)
--secret="admin"
--secret="123"
--secret="password"
```

## 📚 Gebruiksscenario's

### Geplande Updates
```bash
# Zet maintenance aan
php artisan site:maintenance down --secret="update-jan-2024"

# Deel bypass URL met team
# https://ssvdemoes.nl/?secret=update-jan-2024

# Voer updates uit...

# Zet maintenance uit
php artisan site:maintenance up
```

### Noodonderhoud
```bash
# Snel maintenance aanzetten
php artisan down --render="503"

# Probleem oplossen...

# Site weer online
php artisan up
```

### Grote Database Migratie
```bash
# Maintenance met custom bericht
php artisan down --render="maintenance"

# Migraties uitvoeren
php artisan migrate

# Site weer online  
php artisan site:maintenance up
```

## 🎯 Tips & Tricks

1. **Test Eerst** - Test de maintenance pagina op development
2. **Team Informeren** - Informeer team over geplande maintenance  
3. **Backup Maken** - Maak altijd backup voor updates
4. **Monitor** - Houd server logs in de gaten tijdens maintenance
5. **Snel Uitschakelen** - Houd commando gereed voor snel uitschakelen

## 🚨 Troubleshooting

### Maintenance Mode Werkt Niet
```bash
# Check status
php artisan down

# Force disable
php artisan up

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### Template Wordt Niet Getoond
1. Check of template bestand bestaat
2. Verificeer bestandsrechten  
3. Clear view cache: `php artisan view:clear`

### Secret Token Werkt Niet
1. Check of token correct is in URL: `?secret=jouw-token`
2. Probeer opnieuw: `php artisan down --secret="nieuwe-token"`

## 📞 Support

Bij vragen over maintenance mode:
- **Email**: info@ssvdemoes.nl
- **Telefoon**: 0162 - 123 456
- **Locatie**: De Schacht 5, 5107 RD Dongen
