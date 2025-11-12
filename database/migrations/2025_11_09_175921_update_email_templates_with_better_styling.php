<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\EmailTemplate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update new-user-temp-password template with better styling
        EmailTemplate::where('slug', 'new-user-temp-password')->update([
            'html_content' => '<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
            color: #333333;
        }
        .email-body p {
            margin: 0 0 20px 0;
            font-size: 16px;
            color: #555555;
        }
        .credentials-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .credentials-box h2 {
            margin: 0 0 15px 0;
            font-size: 18px;
            color: #333333;
        }
        .credential-item {
            margin: 12px 0;
            font-size: 15px;
        }
        .credential-label {
            font-weight: 600;
            color: #667eea;
            display: inline-block;
            min-width: 120px;
        }
        .credential-value {
            color: #333333;
            font-family: "Courier New", monospace;
            background-color: #ffffff;
            padding: 6px 12px;
            border-radius: 4px;
            display: inline-block;
            border: 1px solid #e0e0e0;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            padding: 14px 40px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .warning-box p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
        .warning-box strong {
            color: #664d03;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .email-footer p {
            margin: 5px 0;
            font-size: 14px;
            color: #6c757d;
        }
        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🎯 Welkom {{name}}!</h1>
        </div>
        
        <div class="email-body">
            <p>Je account is succesvol aangemaakt bij <strong>{{site_name}}</strong>.</p>
            
            <div class="credentials-box">
                <h2>Jouw inloggegevens:</h2>
                <div class="credential-item">
                    <span class="credential-label">Email:</span>
                    <span class="credential-value">{{email}}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Tijdelijk wachtwoord:</span>
                    <span class="credential-value">{{temporary_password}}</span>
                </div>
            </div>
            
            <div class="button-container">
                <a href="{{login_url}}" class="login-button">Inloggen</a>
            </div>
            
            <div class="warning-box">
                <p><strong>⚠️ Belangrijk:</strong> Wijzig je wachtwoord na je eerste login voor de veiligheid.</p>
            </div>
            
            <div class="divider"></div>
            
            <p style="margin-bottom: 0;">Met vriendelijke groet,</p>
            <p style="margin-top: 5px;"><strong>Het {{site_name}} Team</strong></p>
        </div>
        
        <div class="email-footer">
            <p><strong>{{site_name}}</strong></p>
            <p>Dit is een automatisch gegenereerde email.</p>
        </div>
    </div>
</body>
</html>'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
