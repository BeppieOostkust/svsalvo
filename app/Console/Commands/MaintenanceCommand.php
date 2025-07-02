<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MaintenanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:maintenance 
                            {action : up/down - Enable or disable maintenance mode}
                            {--secret= : Secret token to bypass maintenance mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable or disable maintenance mode for SSV De Moes website';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        switch (strtolower($action)) {
            case 'down':
                $this->enableMaintenance();
                break;
                
            case 'up':
                $this->disableMaintenance();
                break;
                
            default:
                $this->error('Invalid action. Use "up" or "down".');
                return 1;
        }
        
        return 0;
    }
    
    /**
     * Enable maintenance mode
     */
    private function enableMaintenance()
    {
        $options = [];
        
        // Secret bypass token
        if ($secret = $this->option('secret')) {
            $options['--secret'] = $secret;
        }

        // Use our custom maintenance view
        $options['--render'] = 'maintenance';
        
        $exitCode = Artisan::call('down', $options);
        
        if ($exitCode === 0) {
            $this->info('🔧 Maintenance mode enabled successfully!');
            $this->line('');
            $this->line('📋 Configuration:');
            
            if (isset($options['--secret'])) {
                $this->line("   Secret bypass: {$options['--secret']}");
                $this->line("   Bypass URL: " . url("/?secret={$options['--secret']}"));
            }
            
            $this->line("   Template: maintenance.blade.php");
            $this->line('');
            $this->comment('💡 Tip: Use "php artisan site:maintenance up" to disable maintenance mode');
        } else {
            $this->error('Failed to enable maintenance mode.');
        }
    }
    
    /**
     * Disable maintenance mode
     */
    private function disableMaintenance()
    {
        $exitCode = Artisan::call('up');
        
        if ($exitCode === 0) {
            $this->info('✅ Maintenance mode disabled successfully!');
            $this->line('');
            $this->comment('🌟 Your website is now live and accessible to all visitors.');
        } else {
            $this->error('Failed to disable maintenance mode.');
        }
    }
}
