<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\EmailTemplate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $html = <<<'HTML'
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welkom bij {{site_name}}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #C8E6C9 0%, #A5D6A7 100%); min-height: 100vh;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background: transparent;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; box-shadow: 0 20px 60px rgba(76, 175, 80, 0.3); overflow: hidden;">
                    <!-- Header met gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%); padding: 50px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: 700; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                Welkom bij {{site_name}}
                            </h1>
                            <p style="margin: 10px 0 0 0; color: rgba(255, 255, 255, 0.95); font-size: 16px;">
                                Uw account is succesvol aangemaakt
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px 0; color: #333333; font-size: 16px; line-height: 1.6;">
                                Beste {{user_name}},
                            </p>

                            <!-- Belangrijk bericht box -->
                            <div style="background: #FFF9C4; border-left: 4px solid #FBC02D; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <p style="margin: 0 0 10px 0; color: #F57F17; font-weight: 600; font-size: 16px;">
                                    ⚠️ Belangrijk:
                                </p>
                                <p style="margin: 0 0 15px 0; color: #555555; font-size: 14px; line-height: 1.6;">
                                    Hopelijk ontvangt u deze e-mail goed.
                                </p>
                                <p style="margin: 0 0 15px 0; color: #555555; font-size: 14px; line-height: 1.6;">
                                    Ik wil graag kort iets vertellen over deze e-mail en over de nieuwe website. Ik ben de afgelopen maanden hard bezig geweest om de laatste finishing touches uit te voeren. Dit is inmiddels succesvol afgerond en op dit moment zie ik zelf geen verdere toevoegingen die nodig zijn.
                                </p>
                                <p style="margin: 0 0 15px 0; color: #555555; font-size: 14px; line-height: 1.6;">
                                    Maar natuurlijk zijn er altijd mensen die denken: "Oh, dit zou leuk zijn om toe te voegen." Dat waardeer ik enorm — niet alleen voor nieuwe ideeën, maar ook voor het melden van problemen, of zoals wij in de programmeerwereld zeggen: bugs. Bugs zijn als het ware 'ongedierte' in een website en kunnen invloed hebben op de snelheid en werking. Deze kunt u allemaal melden via de feedbackpagina, en ik ontvang dit graag zodat de website optimaal blijft functioneren.
                                </p>
                                <p style="margin: 0 0 15px 0; color: #555555; font-size: 14px; line-height: 1.6;">
                                    Daarnaast wil ik u informeren dat wij onze puntentelling hebben gedigitaliseerd. Dit houdt in dat vanaf 01-01-2026 alle punten digitaal worden opgeslagen, tenzij het een andere wedstrijd betreft dan Service Pistol. Ook voor andere wedstrijden zullen wij deze overgang gaan maken, maar dit gebeurt stap voor stap. Het volledige digitaliseringsproces zal ongeveer twee jaar duren.
                                </p>
                                <p style="margin: 0 0 15px 0; color: #555555; font-size: 14px; line-height: 1.6;">
                                    Wanneer u deze e-mail ontvangt, betekent dit dat de oude website per direct is uitgeschakeld en niet meer terug zal komen. Indien u een account had op de oude website, is dit account overgezet naar de nieuwe website. Onderaan deze e-mail vindt u het wachtwoord dat voor u is ingesteld.
                                </p>
                                <p style="margin: 0; color: #555555; font-size: 14px; line-height: 1.6;">
                                    Tot slot verzoek ik u vriendelijk om de privacyverklaring goed door te lezen. Wanneer u akkoord gaat, stemt u in met de voorwaarden zoals deze zijn beschreven in de verklaring. Als u de privacyverklaring weigert, betekent dit helaas dat we u geen toegang mogen geven tot de website — dit is verplicht volgens de Persoonsautoriteit en de AVG-wetgeving.
                                </p>
                            </div>

                            <p style="margin: 20px 0; color: #333333; font-size: 16px; line-height: 1.6;">
                                Dat was het voor nu! Bedankt voor het lezen en tot snel.
                            </p>

                            <!-- Credentials box -->
                            <div style="background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%); border: 2px solid #4CAF50; border-radius: 12px; padding: 25px; margin: 30px 0;">
                                <h2 style="margin: 0 0 20px 0; color: #2E7D32; font-size: 20px; font-weight: 600; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;">
                                    🔐 Uw inloggegevens
                                </h2>
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="padding: 12px 0; color: #555555; font-weight: 600; width: 140px;">E-mailadres:</td>
                                        <td style="padding: 12px 0; color: #333333; font-family: 'Courier New', monospace; font-size: 15px;">{{user_email}}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; color: #555555; font-weight: 600;">Tijdelijk wachtwoord:</td>
                                        <td style="padding: 12px 0; color: #333333; font-family: 'Courier New', monospace; font-size: 15px; background: #fff; padding: 8px 12px; border-radius: 4px; border: 1px solid #ddd;">{{temp_password}}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- CTA Button -->
                            <div style="text-align: center; margin: 35px 0;">
                                <a href="{{login_url}}" style="display: inline-block; background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 50px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4); transition: all 0.3s ease;">
                                    Inloggen op {{site_name}}
                                </a>
                            </div>

                            <!-- Security notice -->
                            <div style="background: #FFF3E0; border-left: 4px solid #FF9800; padding: 15px; margin: 25px 0; border-radius: 4px;">
                                <p style="margin: 0; color: #E65100; font-size: 13px; line-height: 1.5;">
                                    <strong>⚠️ Belangrijk:</strong> Wijzig dit tijdelijke wachtwoord na uw eerste login via Instellingen → Wachtwoord wijzigen.
                                </p>
                            </div>

                            <p style="margin: 25px 0 0 0; color: #333333; font-size: 16px; line-height: 1.6;">
                                Met vriendelijke groet,<br>
                                <strong style="color: #4CAF50;">Dion Geilen</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: #f8f9fa; padding: 30px 40px; border-top: 3px solid #4CAF50;">
                            <p style="margin: 0 0 10px 0; color: #6c757d; font-size: 13px; line-height: 1.6;">
                                Deze e-mail is automatisch gegenereerd. Heeft u vragen? Neem contact op via de website.
                            </p>
                            <p style="margin: 0; color: #adb5bd; font-size: 12px;">
                                © 2025 {{site_name}}. Alle rechten voorbehouden.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

        EmailTemplate::where('slug', 'new-user')->update([
            'html_content' => $html
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed
    }
};
