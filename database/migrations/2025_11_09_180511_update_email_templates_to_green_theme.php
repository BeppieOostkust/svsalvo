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
        // Update ALL templates to GREEN except privacy (stays red)
        
        // New User Template - GREEN
        EmailTemplate::where('slug', 'new-user-temp-password')->update([
            'html_content' => str_replace(
                ['#667eea', '#764ba2', '102, 126, 234'],
                ['#4CAF50', '#388E3C', '76, 175, 80'],
                EmailTemplate::where('slug', 'new-user-temp-password')->value('html_content')
            )
        ]);
        
        EmailTemplate::where('slug', 'new-user-temp-password')->update([
            'html_content' => str_replace(
                ['#f8f9fa', '#e0e0e0'],
                ['#f1f8e9', '#C8E6C9'],
                EmailTemplate::where('slug', 'new-user-temp-password')->value('html_content')
            )
        ]);

        // Match Template - GREEN  
        EmailTemplate::where('slug', 'new-match')->update([
            'html_content' => str_replace(
                ['#2196F3', '#1976D2', '33, 150, 243', '#e3f2fd', '#1565C0'],
                ['#4CAF50', '#388E3C', '76, 175, 80', '#f1f8e9', '#2E7D32'],
                EmailTemplate::where('slug', 'new-match')->value('html_content')
            )
        ]);

        // Activity Template - GREEN
        EmailTemplate::where('slug', 'new-activity')->update([
            'html_content' => str_replace(
                ['#FF9800', '#F57C00', '255, 152, 0', '#fff3e0', '#E65100'],
                ['#4CAF50', '#388E3C', '76, 175, 80', '#f1f8e9', '#2E7D32'],
                EmailTemplate::where('slug', 'new-activity')->value('html_content')
            )
        ]);

        // Feedback Template - GREEN
        EmailTemplate::where('slug', 'feedback-response')->update([
            'html_content' => str_replace(
                ['#9C27B0', '#7B1FA2', '156, 39, 176', '#f3e5f5', '#6A1B9A'],
                ['#4CAF50', '#388E3C', '76, 175, 80', '#f1f8e9', '#2E7D32'],
                EmailTemplate::where('slug', 'feedback-response')->value('html_content')
            )
        ]);
        
        EmailTemplate::where('slug', 'feedback-response')->update([
            'html_content' => str_replace(
                '#e8f5e9',
                '#C8E6C9',
                EmailTemplate::where('slug', 'feedback-response')->value('html_content')
            )
        ]);

        // Privacy stays RED - no changes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed
    }
};
